# Register.py :
# Author: Maxie D. Schmidt (maxieds@gmail.com)
# Created: 2019.06.18

from ScriptConfig import ScriptConfig
from Utils import Utils
import Exceptions
from Sequence import Sequence
from SequenceFileUtils import SequenceFileUtils
from SecondaryStructure import SecondaryStructure as ss
from GTFoldPython import GTFoldPython

import hashlib
from os import getcwd, chdir, unlink, fsync
from os.path import abspath, basename, join, splitext, exists
from tempfile import mkstemp
from glob import glob
from shutil import copy
import sys

import requests
import time
import xml.etree.ElementTree as ET

class Register:

    def __init__(self, ct_filename, out_dir, family, rid):
        name, ext = splitext(ct_filename)
        self.native_ct_path = name + ".nopct"
        if not exists(self.native_ct_path):
            self.native_ct_path = None
        ##
        self.orig_ct_path = ct_filename
        if not exists(self.orig_ct_path):
             self.orig_ct_path = None
        ##
        self.name = basename(name)
        self.out_dir = out_dir
        self.family = family
        self.rid = rid
        self.nopct_metadata = SequenceFileUtils.ParseNopCTMetadata(self.native_ct_path)
        self.sequence = Sequence(self.native_ct_path, format='nopct') if self.native_ct_path != None else \
                        Sequence(self.orig_ct_path, format='nopct')

        # The second argument below is 'nopct' instead of 'ct' because our 'ct' files have the Gutell header.
        # This header is expected for 'nopct' files but not for 'ct' files.
        self.orig_struct = ss(self.orig_ct_path, format='ct') if self.orig_ct_path != None else None
        self.native_struct = ss(self.native_ct_path, format='nopct') if self.native_ct_path != None else None
        ## TODO: 
        viennaDataFileSrc = None
        if self.native_struct != None:
            self.pseudoknot_base_pairs = self.native_struct.compute_missing_pairs(self.native_ct_path)
            self.non_canonical_base_pairs = self.native_struct.get_non_canonical_pairs(self.sequence)  # diff between native and canonical
            self.canonical_struct = self.native_struct.canonical_struct(self.sequence)
            viennaDataFileSrc = self.native_ct_path
        else:
            self.pseudoknot_base_pairs = None
            self.non_canonical_base_pairs = self.orig_struct.get_non_canonical_pairs(self.sequence)  # diff between native and canonical
            self.canonical_struct = self.orig_struct.canonical_struct(self.sequence)
            viennaDataFileSrc = self.orig_ct_path
        ##
        
        if self.canonical_struct != None:
            self.isolated_base_pairs = self.canonical_struct.get_isolated_pairs()
            self.clean_struct = self.canonical_struct.non_isolated_struct()
        else:
            self.isolated_base_pairs = None
            self.clean_struct = None
        ##

        if self.sequence.ambiguous:
            ScriptConfig.PrintInfo('Found ambiguous sequence ... ')
            ScriptConfig.PrintInfo('Ambiguous sequence source = \'{0}\''.format(ct_filename))
        else:
            ScriptConfig.PrintInfo('Generating constrained struct ... ')
            ScriptConfig.PrintInfo('Constrained struct source = \'{0}\''.format(viennaDataFileSrc))
            (constSS, cssMFE) = self.clean_struct.GenerateConstrainedStruct(
                self.sequence, force_entire_structure=False)
            self.constrained_struct = ss(constSS, format='dot_bra', constrained=True)
            self.constrained_struct_energy = cssMFE
            ScriptConfig.PrintInfo('Generating mfe struct from source = \'{0}\''.format(viennaDataFileSrc))
            (mfe, mfeStruct) = GTFoldPython.GetMFEStructure(str(self.sequence), [])
            self.mfe_struct = ss(mfeStruct, format='dot_bra', constrained=False)
            self.mfe_struct_energy = mfe
            ScriptConfig.PrintInfo('Using GTFoldPython to find energy for clean_struct = \'{0}\''.format(viennaDataFileSrc))
            (cleanConstSS, cleanCSSMFE) = self.clean_struct.GenerateConstrainedStruct(self.sequence, force_entire_structure=True)
            #self.clean_struct = ss(
            #    cleanConstSS, format='dot_bra', constrained=True)
            self.clean_struct_energy = cleanCSSMFE
            ScriptConfig.PrintInfo('Using GTFoldPython to find energy for canonical struct = \'{0}\''.format(viennaDataFileSrc))
            # Add the calculation of the energy of the canonical struct,
            # so that the energy will be included in the saved ct file
            (canonicalConstSS, canonicalCSSMFE) = self.canonical_struct.GenerateConstrainedStruct(self.sequence, force_entire_structure=True)
            #self.canonical_struct = ss(
            #    canonicalConstSS, format='dot_bra', constrained=True)
            self.canonical_struct_energy = canonicalCSSMFE
        ##
        self.generate_register_data()
        self.generate_files()
    ## 

    def generate_register_data(self):
         self.latin_name = self.sequence.LatinName()
         self.accession = self.sequence.AccessionNumber()
         self.filename = self.sequence.Filename()    
         self.length = len(self.sequence)
         self.gc_content = self.sequence.gc()
         self.initial_fragment = self.sequence.sequence[:30].upper().replace('T', 'U')
         self.seq_checksum = hashlib.md5(self.sequence.sequence.upper().encode('utf-8')).hexdigest()
         self.canonical_num_bp = self.canonical_struct.get_num_pairs() if self.canonical_struct != None else -1
         self.native_num_bp = self.native_struct.get_num_pairs() if self.native_struct != None else -1
         self.clean_num_bp = self.clean_struct.get_num_pairs() if self.clean_struct != None else -1
         self.orig_num_bp = self.orig_struct.get_num_pairs() if self.orig_struct != None else -1
         if not self.sequence.ambiguous:
              self.accuracy = self.mfe_struct.relative_accuracy(self.clean_struct)
              pr_data = self.mfe_struct.precision_recall_data(self.clean_struct)
              self.precision, self.recall, self.tp, self.fp, self.fn = pr_data
              self.mfe_num_bp = self.mfe_struct.get_num_pairs()
              self.constrained_num_bp = self.constrained_struct.get_num_pairs()
              self.completeness = self.clean_num_bp / float(self.constrained_num_bp) if float(self.constrained_num_bp) != 0 else 0
         ##
         accession_sequence_info = self.get_accession_sequence_info()
         self.acc_length = accession_sequence_info.get('acc_length', 'NULL')
         self.seq_start = accession_sequence_info.get('seq_start', 'NULL')
         self.seq_stop = accession_sequence_info.get('seq_stop', 'NULL')
    ##    
     
    def generate_files(self):
        filename_prefix = join(self.out_dir, self.name)
        fasta_txt = filename_prefix + ".fasta"
        fasta_header = "> name: {0}; accession: {1}; primary key: {2}".format(self.latin_name, self.accession, self.rid)
        self.sequence.Save(fasta_txt, header = fasta_header)
        self.fasta_txt = basename(fasta_txt)
        pseudoknots_txt = filename_prefix + "_knots.txt"
        non_canonical_txt = filename_prefix + "_noncanonical.txt"
        isolated_txt = filename_prefix + "_isolated.txt"
        
        Utils.write_pair_list_to_file(pseudoknots_txt, self.pseudoknot_base_pairs)
        Utils.write_pair_list_to_file(non_canonical_txt, self.non_canonical_base_pairs)
        Utils.write_pair_list_to_file(isolated_txt, self.isolated_base_pairs)

        self.pseudoknots_txt = basename(pseudoknots_txt) if self.pseudoknot_base_pairs != None else ""
        self.non_canonical_txt = basename(non_canonical_txt) if self.non_canonical_base_pairs != None else ""
        self.isolated_txt = basename(isolated_txt) if self.isolated_base_pairs != None else ""

        original_ct_path = filename_prefix + "_orig.ct"
        native_ct_path = filename_prefix + "_nop.ct"
        canonical_ct_path = filename_prefix + "_canon.ct"
        clean_ct_path = filename_prefix + "_clean.ct"
        forced_ct_path = filename_prefix + "_forced.ct"
        mfe_ct_path = filename_prefix + "_mfe.ct"

        if self.orig_ct_path != None:
            copy(self.orig_ct_path, original_ct_path)
        else:
            ScriptConfig.PrintInfo("Original CT file \"{0}\" does not exist ... skipping".format(filename_prefix + ".ct"))
        if self.native_ct_path != None:
            copy(self.native_ct_path, native_ct_path)
        self.canonical_struct.save(canonical_ct_path, seq = self.sequence, metadata = self.nopct_metadata, struct_type = 'canonical')
        self.clean_struct.save(clean_ct_path, seq = self.sequence, metadata = self.nopct_metadata, struct_type = 'clean')

        if not self.sequence.ambiguous:
            self.forced_ct = basename(forced_ct_path)
            self.constrained_struct.save(forced_ct_path, seq = self.sequence, metadata = self.nopct_metadata, struct_type = 'forced')
            self.mfe_struct.save(mfe_ct_path, seq = self.sequence, metadata = self.nopct_metadata, struct_type = 'mfe')
            self.mfe_ct = basename(mfe_ct_path)
        ##

        self.orig_ct = basename(original_ct_path)
        self.native_ct = basename(native_ct_path) if self.native_ct_path != None else ""
        self.canonical_ct = basename(canonical_ct_path)
        self.clean_ct = basename(clean_ct_path)
    ##
       
    def get_register_data(self):
        base_data = {
            'latin_name' : self.latin_name,
            'family' : self.family,
            'accession' : self.accession,
            'length' : self.length,
            'acc_length' : self.acc_length,
            'seq_start' : self.seq_start,
            'seq_stop' : self.seq_stop,
            'gc_content' : self.gc_content,
            'fasta_txt' : self.fasta_txt,
            'initial_fragment' : self.initial_fragment,
            'seq_checksum' : self.seq_checksum,
            'orig_bp' : self.orig_num_bp,
            'nop_bp' : self.native_num_bp,
            'canon_bp' : self.canonical_num_bp,
            'clean_bp' : self.clean_num_bp,
            'orig_ct' : self.orig_ct,
            'nop_ct' : self.native_ct,
            'canon_ct' : self.canonical_ct,
            'clean_ct' : self.clean_ct,
            'knots_txt' : self.pseudoknots_txt,
            'pseudoknots' : '<placeholder>', 
            'noncanonical_txt' : self.non_canonical_txt,
            'isolated_txt' : self.isolated_txt,
        }
        if not self.sequence.ambiguous:
            other_data = {
                'completeness': self.completeness,
                'tp' : self.tp, 
                'fp' : self.fp, 
                'fn' : self.fn,
                'precision_val' : self.precision,
                'recall' : self.recall,
                'f_measure' : self.accuracy,
                'mfe_bp' : self.mfe_num_bp,
                'forced_bp' : self.constrained_num_bp,
                'clean_energy' : self.clean_struct_energy,
                'mfe_energy' : self.mfe_struct_energy,
                'forced_energy' : self.constrained_struct_energy,
                'mfe_ct' : self.mfe_ct,
                'forced_ct' : self.forced_ct,
            }
            base_data.update(other_data)
        ##
        base_data['ambiguous'] = int(self.sequence.ambiguous)
        return base_data
    ##
     
    @staticmethod
    def get_reverse_complement(sequence):
        complements = {
            'a' : 'u',
            'u' : 'a',
            'c' : 'g',
            'g' : 'c',
            'n' : 'n',
        }
        return "".join(complements.get(base, base) for base in reversed(sequence))
    ##
     
    def get_accession_sequence_info(self):
        params = {
            "db"     : "nuccore",
            "format" : "xml",
            "tool"   : "GeorgiaTechRNAdb",
            "email"  : "heitsch@math.gatech.edu",
        }
        params = '&'.join('{0}={1}'.format(key, value) for (key, value) in params.items())
        base_url = 'https://eutils.ncbi.nlm.nih.gov/entrez/eutils/efetch.fcgi?' + params + '&id={0}'

        full_sequences = []
        accNoStr = self.accession.encode('utf-8')
        accNumbers = [accNoStr]
        if accNoStr.find(b',') > -1:
            accNumbers = accNoStr.strip().split(b',')
        elif accNoStr.find(b' ') > -1:
            accNumbers = [accno.strip() for accno in accNoStr.split(b' ')]
        ##
        for acc in accNumbers: # split on comma or whitespace
            acc = Utils.Python3BytesToStringConvert(acc) 
            requestSuccess = False
            while True:
                try:
                    r = requests.get(base_url.format(acc))
                    requestSuccess = True
                except (KeyboardInterrupt, SystemExit):
                    sys.exit(ScriptConfig.EXIT_KBDINT)
                except:
                    ScriptConfig.PrintError("Unable to process request (Do you have a working Internet connection?)... ")
                    time.sleep(2) 
                    continue
                break
            ##
            tree = ET.fromstring(r.text)
            element_list = tree.findall('.//GBSeq_sequence')
            if len(element_list) == 1:
                full_sequences.append(element_list[0].text)
                # NCBI requires use of an API key by any IP address posting more than three requests per second. 
                # So let's stay safe ... 
                time.sleep(0.5) 
            ##
        if len(full_sequences) == 0:
            # couldn't find any sequences matching this accession number: 
            ScriptConfig.PrintWarning("Couldn't find any sequences for accession number(s): {0}".format(self.accession))
            return dict()
        ##
        full_sequences = [s.lower().replace('t', 'u') for s in full_sequences]
        partial_sequence = self.sequence.sequence.lower()
        for full_sequence in full_sequences:
            i = full_sequence.find(partial_sequence)
            if i != -1:
                return {
                    'acc_length' : len(full_sequence),
                    'seq_start' : i + 1,
                    'seq_stop' : i + len(partial_sequence)
                }
            ##
            i = full_sequence.find(self.get_reverse_complement(partial_sequence))
            if i != -1:
                return {
                    'acc_length' : len(full_sequence),
                    'seq_start' : i + len(partial_sequence),
                    'seq_stop' : i + 1
                }
            ##
        ##
        # Last Resort: Can't find this sequence within any full sequence corresponding to an accession number
        return {'acc_length' : len(full_sequences[0])}
##
