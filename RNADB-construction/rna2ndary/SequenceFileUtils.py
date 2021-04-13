# SequenceFileUtils.py : Sequence file type parsers;
# Author: Maxie D. Schmidt (maxieds@gmail.com)
# Created: 2019.06.18

from collections import namedtuple
from ScriptConfig import ScriptConfig
from Types import NopCTMetaData
from Utils import Utils

class SequenceFileUtils(object):

     @staticmethod
     def ParseFasta(filename):
          """
          Will return the concatenation of all lines from filename that do
          not start with '>' (the comment character in fast files).

          TODO:
             - Check if the returned str is actually a valid sequence.
          """
          sequence = ''
          with open(filename, 'rb') as infile:
               for l in infile:
                    if l[0] != '>':
                         sequence += l[:-1].upper()
          return sequence
     ##

     @staticmethod
     def ParseCTFromFile(ctfile):
          """
          Takes a file object as input to extract the stored ct data.

          Typically the first row has the following structure.

          122     dG = -50.72   [initially -55.70] d.5.b.E.coli.ct

          The first column is the length of the sequence (122), next is the
          energy (-50.72) and the filename at the very end (note that nopct
          files from utexas do not contain the filename and that the energy is
          set to 0.0, also note that gtfold output only contains length and
          energy).

          After this a sequence of columns follow, such as

          1    U    0    2    119    1
          2    G    1    3    118    2
          3    C    2    4    117    3
          4    C    3    5    116    4
          5    U    4    6    115    5
          6    G    5    7    114    6
          7    G    6    8    113    7

          Second column of row i contains the nucleotide at position i
          (current nucleotide) in the sequence. Column 5 contains the index of
          the nucleotide that the current nucleotide is paired to in the
          secondary structure. If column 5 has value 0 then that means the
          current nucleotide is not paired to any other in the secondary
          structure. Columns 1 and 6 both contain index i while column 3 and 4
          contain i-1 and i+1 respectively, or 0 when i is the sequence length.

          WARNING:
          - When parsing utexas nopct files recall that the associated energy is useless.
          - gtfold output only contains length and energy.
          """
          line = ''
          while True:
              line = ctfile.readline().decode('utf-8')
              if len(line) > 0 and (line[0].isalpha() or line[0] == '>'):
                  continue
              elif line == '\n':
                  continue
              break
          ##
          length = int(line.split()[0])
          energy = float(line.split()[3])
          sequence = ''
          # pair_array is a list that indicates i is paired to pair_array[i].  We
          # allow indices in pair_array to range from 0 to n+2.  Nucleotide 0 and n+1
          # are just a dummy nucleotides used only to have indices in pair_array
          # coincide con nucleotide numbers in the sequence
          pair_array = []
          for line in ctfile:
               if line == b'\n':
                    break
               columns = line.split()
               sequence += columns[1].decode('utf-8')
               other_nucleotide = int(columns[4])
               if other_nucleotide != 0:
                    pair_array.append(other_nucleotide)
               else:
                    pair_array.append(None)
          ScriptConfig.PrintDebug("Sequence: " + str(sequence))
          ScriptConfig.PrintDebug("Pair Array: " + str(pair_array))
          if length != len(sequence) or len(sequence) != len(pair_array):
               msg = "len = {0}; seq_len = {1}; pair_array-1 = {2}"
               ScriptConfig.PrintError(msg.format(length, len(sequence), len(pair_array)));
               raise RuntimeWarning("Length mismatch.")
          data = { 
                    'length': length, 
                    'energy': energy, 
                    'sequence': str(sequence.upper()),
                    'pair_array': pair_array 
                 }
          return data
     ##

     @staticmethod
     def ParseCT(filename):
         with open(filename, 'rb') as ctfile:
              return SequenceFileUtils.ParseCTFromFile(ctfile)
     ## 
     
     @staticmethod
     def ParseNopCT(filename):
          """Parses nopct files as retrieved from the Gutell database.

          First four lines in a nopct file contain misc info, eg.

          Filename: d.5.b.E.coli.nopct
          Organism: Escherichia coli
          Accession Number: V00336
          Citation and related information available at http://www.rna.ccbb.utexas.edu
    
          Starting from line 5 the ct portion begins. Thus we pass control of
          the parsing procedure to parse_ct_from_file.
          """
          with open(filename, 'rb') as nopctfile:
               nopctMetaData = SequenceFileUtils.ParseNopCTMetadata(filename)
               data = SequenceFileUtils.ParseCTFromFile(nopctfile)
               for (kidx, key) in enumerate(["filename", "organism_name", "accession_number", "url"]):
                   data[key] = str(nopctMetaData[kidx])
               return data
     ## 
     
     @staticmethod
     def ParseNopCTMetadata( filename ):
          """
          Parses the first three lines of nopct files as retrieved from the Gutell database.

          Filename: d.5.b.E.coli.nopct
          Organism: Escherichia coli
          Accession Number: V00336
          """
          if filename == None:
              return NopCTMetaData("", "", "", "")
          with open(filename, 'rb') as nopctfile:
               splitLine = nopctfile.readline().split()
               fname, organism_name, accession_numbers = "", "", ""
               fname = splitLine[1] if len(splitLine) >= 2 else b''
               fname = Utils.Python3BytesToStringConvert(fname) 
               splitLine = nopctfile.readline().split()
               organism_name = b' '.join(splitLine[1:]) if len(splitLine) >= 2 else b''
               if organism_name[0] == b' ':
                    organism_name = organism_name[1:]
               organism_name = Utils.Python3BytesToStringConvert(organism_name) 
               splitLine = nopctfile.readline().split()
               accession_numbers = b' '.join(splitLine[2:]) if len(splitLine) >= 3 else b''
               if accession_numbers[0] == b' ':
                    accession_numbers = accession_numbers[1:]
               accession_numbers = Utils.Python3BytesToStringConvert(accession_numbers) 
               line = nopctfile.readline().decode('utf-8')
               related_info_url = ""
               if len(line) > 0 and (line[0].isalpha() or line[0] == '>'):
                    splitLine = line.split()
                    related_info_url = splitLine[-1] if len(splitLine) >= 1 else "" 
               nopCTParserArgs = [fname, organism_name, accession_numbers, related_info_url]
               return NopCTMetaData(*nopCTParserArgs)
     ##
##
