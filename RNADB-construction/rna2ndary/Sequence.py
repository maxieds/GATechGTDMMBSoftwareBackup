# Sequence.py : Define and compute associated constants on a RNA sequence with the GTFoldPython package easily;
# Author: Maxie D. Schmidt (maxieds@gmail.com)
# Created: 2019.06.18

from SecondaryStructure import SecondaryStructure as ss
from collections import namedtuple
from ScriptConfig import ScriptConfig
from SequenceFileUtils import SequenceFileUtils
from Utils import Utils

class Sequence:

     """ Constructor: """

     def __init__(self, dataSpec, format="str", kind=None, origin=None, name=None):
          if format == "str":
               self.sequence = dataSpec.upper();
               self.length = len(self.sequence);
          elif format == "fasta":
               self.sequence = SequenceFileUtils.ParseFasta(dataSpec);
               self.length = len(self.sequence);
          elif format == "ct" or format == "nopct":
               dataSpec = SequenceFileUtils.ParseCT(dataSpec) if format == "ct"\
                          else SequenceFileUtils.ParseNopCT(dataSpec);
               self.sequence = dataSpec["sequence"];
               self.length = dataSpec["length"];
               self.names = {};
               if format == "nopct":
                    dictComps = ["filename", "organism_name", "accession_number", "url"];
                    self.metaData = dict((s, str(dataSpec[s])) for s in dictComps);
          else:
               raise ValueError("Unknown sequence format {0}.".format(format));
          # The following should be one of:
          # 'tRNA', '5S', 'RNaseP', 'GI-Intron', '16S', '23S', etc.
          self.kind = kind;
          self.GenSequenceInfo();
     ##

     def LatinName(self):
          try:
               return self.metaData['organism_name'];
          except:
               self.metaData['organism_name'] = ''
               ScriptConfig.PrintWarning("No latin name found in meta data.")
               #raise KeyError("No latin name found in meta data.");
               return self.metaData['organism_name']
     ##

     def AccessionNumber(self):
          try:
               return self.metaData['accession_number']
          except:
               self.metaData['accession_number'] = ''
               ScriptConfig.PrintWarning("No accession info found in meta data.")
               #raise KeyError("No accession info found in meta data.")
               return metaData['accession_number']
     ##

     def Filename(self):
          try:
               return self.metaData['filename']
          except:
               raise KeyError("No filename found in meta data.")
     ##

     def GenSequenceInfo(self):
          seq = self.sequence;
          self.a_ct = seq.count('A');
          self.c_ct = seq.count('C');
          self.g_ct = seq.count('G');
          self.u_ct = seq.count('U');
          self.ambiguous = 'N' in self.sequence;
          self.gc_content = (self.g_ct + self.c_ct) / float(len(seq));
     ##

     def gc(self):
          return self.gc_content;
     ##

     def GetLength(self):
          return self.length;
     ##

     def __len__(self):
          return len(self.sequence);
     ##

     def __str__(self):
          return str(self.sequence);
     ##

     def __getitem__(self, itemIdx):
          return self.sequence[itemIdx];
     ##

     def Save(self, filename, name=None, structure=None, header=None):
          fileExt = Utils.GetFileExt(filename);
          if fileExt == "fasta":
               with open(filename, 'w', encoding='utf-8') as fastaHandle:
                    if header is not None:
                         fastaHandle.write(header + "\n");
                    else:
                         if name is not None:
                              fastaHandle.write('> name: {0}\n'.format(name));
                         comment = '> length: {0}\n';
                         fastaHandle.write(comment.format(self.length));
                    fastaHandle.write('{0}\n'.format(self.sequence));
               ##
          elif fileExt == "ct":
               if structure is None:
                    raise ValueError('Structure must be saved in CT format.');
               structure.Save(filename, name, self);
          ##
     ##           
##
