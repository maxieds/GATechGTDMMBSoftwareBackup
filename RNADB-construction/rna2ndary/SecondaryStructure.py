# SecondaryStructure.py :
# Author: Maxie D. Schmidt (maxieds@gmail.com)
# Created: 2019.06.18

from ScriptConfig import ScriptConfig
import Exceptions
from SequenceFileUtils import SequenceFileUtils
from os import remove
from Utils import Utils
from GTFoldPython import GTFoldPython
import math
from GTFoldPython import GTFoldPython
import os
from Types import NopCTMetaData

class SecondaryStructure:

     def __init__(self, dat, format='dot_bra', constrained=False, fullSequenceLength = None):
          """Construct a secondary structure from multiple possible inputs.

             INPUT::

             - dat - The input data. The type of dat will depend on the other
             variables. See below for details.
 
             - format: a string. One of 'dot_bra', 'ct', 'nopct', '2s', 'ss',
             'fasta', 'gtfold_run', 'cons_gtfold_run', 'mfold_run', or
             'RNAfold_run'. See below for details

             - constrained: a boolean. Whether to constrain gtfold to run
             using a constraints file. See below for details.

             We list the possible types of input that are expected, depending
             on the value of `format`.

             +-----------------+--------------+-------------------------------------+
             | format val      | dat type     |dat value                            |
             +=================+==============+=====================================+
             | dot_bra         | str          | dot bracket secondary structure     |
             +-----------------+--------------+-------------------------------------+
             | ct              | str          | filename (path to ct file)          |
             +-----------------+--------------+-------------------------------------+
             | nopct           | str          | filename (path to nopct file)       |
             +-----------------+--------------+-------------------------------------+
             | 2s, ss, fassta  | str          | filename (path to file)             |
             +-----------------+--------------+-------------------------------------+
             | gtfold_run      | str          | filename (path to fasta)            |
             +-----------------+--------------+-------------------------------------+
             | cons_gtfold_run | 2 elem tuple | 2 filenames (fasta and constraints) |
             +-----------------+--------------+-------------------------------------+
             | mfold_run       | str          | filename (path to fasta)            |
             +-----------------+--------------+-------------------------------------+
             | RNAfold_run     | str          | filename (path to fasta)            |
             +-----------------+--------------+-------------------------------------+

             The 2s, ss, fassta format is assumed to be a "fasta like"
             formatted file that contains the dot bracket representation of a
             secondary structure (as opposed to the actual sequence in a
             fasta file).
          """
          if format == 'dot_bra':
               self.sequence = ""
               self.struct = dat
               self.length = len(self.struct) if fullSequenceLength == None else fullSequenceLength
               self.construct_pair_array()
               self.energy = None
          elif format in ['ct', 'nopct', 'ct_data', 'nopct_data']:
               data = SequenceFileUtils.ParseCT(dat) if format == 'ct' or format == 'ct_data' else SequenceFileUtils.ParseNopCT(dat)
               self.sequence = data['sequence']
               self.struct_pair_array = data['pair_array']
               self.length = data['length']
               self.construct_struct()
               self.energy = data['energy']
          elif format in ['2s', 'ss', 'fasta']:
               self.struct = SequenceFileUtils.ParseFasta(dat)
               self.sequence = self.struct
               self.length = len(self.struct)
               self.construct_pair_array()
               self.energy = None
          else:
              raise ValueError("Unknown format {0}.".format(format))
          self.origin = format
          self.gen_struct_info()
          self.accuracy = None
          self.native = None
          upperStructStrLen = 25 if len(self.struct) >= 25 else len(self.struct) - 1
          ScriptConfig.PrintInfo('New secondary struct with format {0}: {1} [DOT: {2} ] ...'.format(format, 
                                 str(self.sequence)[0:upperStructStrLen], 
                                 str(self.struct)[0:upperStructStrLen]))
     ##

     def gen_struct_info(self):
          """Compute structure info. Base pairs, number of pairs and density."""
          self.compute_pairs()
          self.num_pairs = len(self.pairs)
          self.density = (2.0 * self.num_pairs) / self.length
     ##

     def construct_pair_array(self):
          n = len(self.struct)
          pair_array = [None for i in range(0, n + 1)]
          stack = []
          for (i, c) in enumerate(self.struct):
               if c == '.':
                    continue
               elif c == '(':
                    stack.append(i)
               elif c == ')':
                    j = stack.pop()
                    pair_array[i] = j + 1
                    pair_array[j] = i + 1
          self.struct_pair_array = pair_array
     ##

     def construct_struct(self):
          n = self.length
          sec_struct = list('.' * n)
          for (i, j) in enumerate(self.struct_pair_array):
               if j:
                    if i < j:
                         sec_struct[i] = '('
                    elif i > j:
                         sec_struct[i] = ')'
          self.struct = ''.join(sec_struct)
     ##

     def is_Watson_Crick(self, x, y):
          """Return true only if xy is a Watson-Crick pair, that is AU or CG."""
          WatsonCrick = ['AU', 'CG', 'GC', 'UA']
          return x.upper() + y.upper() in WatsonCrick
     ##

     def is_wobble(self, x, y):
          """Return true only if xy is a wobble pair, namely GU."""
          return x.upper() + y.upper() in ['GU', 'UG']
     ##

     def is_canonical(self, x, y):
          """Return true only if xy is a canonical pair.
             By definition a canonical pair is either a Watson-Crick pair or
             a wobble pair.
          """
          return self.is_Watson_Crick(x, y) or self.is_wobble(x, y)
     ##

     def get_pairs(self):
          """Return a copy of the list of pairs"""
          return self.pairs[:]
     ##

     def get_watson_crick_pairs(self, seq):
          """Return list of Watson-Crick pairs in structure given a sequence."""
          if self.length != seq.GetLength():
               raise ValueError(
                   "Secondary struct and sequence length mismatch.")
          wc_filter = lambda ij: self.is_Watson_Crick(
              str(seq[ij[0] - 1]), str(seq[ij[1] - 1]))
          return filter(wc_filter, self.pairs)
     ##

     def get_wobble_pairs(self, seq):
          """Return list of wobble pairs in structure given a sequence."""
          if self.length != seq.GetLength():
               raise ValueError(
                   "Secondary struct and sequence length mismatch.")
          wobble_filter = lambda ij: self.is_Watson_Crick(
              str(seq[ij[0] - 1]), str(seq[ij[1] - 1]))
          return filter(wobble_filter, self.pairs)
     ##

     def get_non_canonical_pairs(self, seq):
          """Return list of non-canonical pairs in structure given a sequence."""
          if self.length != seq.GetLength():
               raise ValueError(
                   "Secondary struct and sequence length mismatch.")
          canonical = lambda ij: not self.is_canonical(
              str(seq[ij[0] - 1]), str(seq[ij[1] - 1]))
          return filter(canonical, self.pairs)
     ##

     def get_canonical_pairs(self, seq):
          """Return list of canonical pairs in structure given a sequence."""
          if self.length != seq.GetLength():
               raise ValueError(
                   "Secondary struct and sequence length mismatch.")
          canonical = lambda ij: self.is_canonical(
              str(seq[ij[0] - 1]), str(seq[ij[1] - 1]))
          return filter(canonical, self.pairs)
     ##

     def get_isolated_pairs(self):
          """Return list of isolated pairs in structure."""
          n = len(self.struct)
          # We store pairs in an array structure for finding pairs efficiently.
          # We allow indices from 0 to n+1. Bases 0 and n+1 will be dummy bases,
          # used only to avoid boundary checking when iterating over all pairs.
          pair_array = [None for i in range(0, n + 2)]
          for (i, j) in self.pairs:
               pair_array[i] = j
               pair_array[j] = i
          isolated_pairs = []
          for (i, j) in self.pairs:
               # Will check if each pair is stacked "above or below".  If neither
               # occurs, then we classify it as isolated.
               if pair_array[i - 1] == j + 1 or pair_array[i + 1] == j - 1:
                    continue
               isolated_pairs.append((i, j))
          return isolated_pairs
     ##

     def get_non_isolated_pairs(self):
          """Return list of non-isolated pairs in structure."""
          n = len(self.struct)
          # We store pairs in an array structure for finding pairs efficiently.
          # We allow indices from 0 to n+1. Bases 0 and n+1 will be dummy bases,
          # used only to avoid boundary checking when iterating over all pairs.
          pair_array = [None for i in range(0, n + 2)]
          for (i, j) in self.pairs:
               pair_array[i] = j
               pair_array[j] = i
          non_isolated_pairs = []
          for (i, j) in self.pairs:
               # Will check if each pair is stacked "above or below".  If at least
               # one occurs, then we classify it as non isolated.
               if pair_array[i - 1] == j + 1 or pair_array[i + 1] == j - 1:
                    non_isolated_pairs.append((i, j))
          return non_isolated_pairs
     ##

     def canonical_struct(self, seq):
          """Return the underlying canonical secondary structure wrt sequence."""
          n = len(seq)
          canonical_struct = list('.' * n)
          for (i, j) in self.get_canonical_pairs(seq):
               canonical_struct[i - 1] = '('
               canonical_struct[j - 1] = ')'
          return SecondaryStructure(''.join(canonical_struct))
     ##

     def non_isolated_struct(self):
          """Return the underlying structure consisting of non isolated pairs. """
          n = len(self.struct)
          canonical_struct = list('.' * n)
          for (i, j) in self.get_non_isolated_pairs():
               canonical_struct[i - 1] = '('
               canonical_struct[j - 1] = ')'
          return SecondaryStructure(''.join(canonical_struct))
     ##

     def pairs_not_in(self, other):
          """Return list of pairs in self that are not in other."""
          n = len(self.struct)
          # We store pairs from other in an array structure for finding pairs
          # efficiently.  We allow indices from 0 to n+1. Bases 0 and n+1 will be
          # dummy bases, used only to avoid boundary checking when iterating over
          # all pairs.
          other_pair_array = [None for i in range(0, n + 2)]
          for (i, j) in other.get_pairs():
               other_pair_array[i] = j
               other_pair_array[j] = i
          missing_from_other = []
          for (i, j) in self.pairs:
               if other_pair_array[i] != j:
                    missing_from_other.append((i, j))
          return missing_from_other
     ##

     def get_num_pairs(self):
          """Return number of pairs in current structure. Output is cached."""
          return self.num_pairs
     ##

     def get_dot_bracket(self):
          """Get secondary structure in dot-bracket notation."""
          return self.struct
     ##

     def get_energy(self):
          """Return energy."""
          return self.energy
     ##

     def get_length(self):
         """Return length of structure."""
         return self.length
     ##

     def __len__(self):
         """Return length of structure."""
         return self.get_length()
     ##

     def get_density(self):
         """Return density of current structure."""
         return self.density
     ##

     def compute_pairs(self):
         """Computes the list of pairs from the dot-bracket representation."""
         sec_struct = self.struct
         stack = []
         pairs = []
         for i, c in enumerate(sec_struct):
              if c == '(':
                   stack.append(i)
              elif c == ')':
                   j = stack.pop()
                   pairs.append((j + 1, i + 1))
         self.pairs = sorted(pairs)
     ##

     def compute_missing_pairs(self, ctfile, format='nopct'):
          """Returns list of missing pairs given the ct filename."""
          if ctfile == None or not os.path.exists(ctfile):
               return []
          if format == 'ct':
               data = SequenceFileUtils.ParseCT(ctfile)
          elif format == 'nopct':
               data = SequenceFileUtils.ParseNopCT(ctfile)
          pn_pair_array = data['pair_array']
          missing_pairs = set([])
          for i, (x, y) in enumerate(zip(self.struct_pair_array, pn_pair_array)):
               if x is None and x != y:
                    missing_pairs.add(tuple(sorted([i, y])))
          return sorted(missing_pairs)
     ##

     def relative_accuracy(self, reference):
          """Return the accuracy of self relative to the reference structure."""
          if self.accuracy is None or reference != self.native:
               self._compute_accuracy(reference)
          return self.accuracy
     ##

     def precision_recall_data(self, reference):
          """Return precision recall params of self given a reference structure."""
          if self.precision is None:
               self._compute_precision_recall(reference)
          return (self.precision, self.recall, self.tp, self.fp, self.fn)
     ##

     def _compute_accuracy(self, reference):
          """Compute accuracy of self relative to reference structure"""
          # We treat self as the predicted structure and it is going to be
          # compared against native.
          if self.accuracy is None:
               self._compute_precision_recall(reference)
          denom = 2.0 * self.tp + self.fn + self.fp
          self.accuracy = 2 * self.tp / denom if denom != 0.0 else 0
     ##

     def _compute_precision_recall(self, reference):
          """Compute precision recall params of self given reference structure """
          predicted_pairs = set(self.get_pairs())
          reference_pairs = set(reference.get_pairs())
          self.num_canon_pairs = len(predicted_pairs)
          self.tp = len(reference_pairs.intersection(predicted_pairs))
          self.fn = len(reference_pairs.difference(predicted_pairs))
          self.fp = len(predicted_pairs.difference(reference_pairs))
          denom = 2.0 * self.tp + self.fn + self.fp
          self.accuracy = 2 * self.tp / denom if denom != 0.0 else 0
          self.precision = self.tp / float(self.tp + self.fp) if self.tp + self.fp != 0 else 0
          self.recall = self.tp / float(self.tp + self.fn) if self.tp + self.fn != 0 else 0
     ##

     def __eq__(self, other):
          """Compares two secondary structures based on their dot-bracket repr."""
          if isinstance(other, SecondaryStructure):
               return self.struct == other.struct
          return NotImplemented
     ##

     def __ne__(self, other):
          """Compares two secondary structures based on their dot-bracket repr."""
          result = self.__eq__(other)
          if result is NotImplemented:
               return result
          return not result
     ##

     def __getitem__(self, i):
          """Get the i-th element from the dot-bracket representation."""
          return self.struct[i]
     ##

     def __str__(self):
          """Return the dot-bracket representation of self."""
          return self.struct
     ##

     def save(self, filename, seq, metadata, struct_type):
          """
             Save secondary structure in text file in one of various formats.

             - metadata should contain filename, organism, and accession number, used for creating the first
               three lines of the header.
             - struct_type will be one of 'canonical', 'clean', 'forced', or 'mfe', and is used
               in the fourth line of the header.
          """
          ext = Utils.GetFileExt(filename)
          energy = self.energy if self.energy else 0.0
          if ext in ['fasta', '2s', 'c2s']:
               with open(filename, 'w', encoding='utf-8') as fastaHandle:
                    if header:
                         if name is not None:
                              fastaHandle.write('> name: {0}\n'.format(name))
                         if seq is not None:
                              fastaHandle.write(
                                  '> sequence: {0}\n'.format(seq))
                         comment = '> length: {0} energy: {1}\n'
                         fastaHandle.write(comment.format(self.length, energy))
                    ##
                    fastaHandle.write('{0}\n'.format(self.struct))
          elif ext == 'ct':
               if seq is None:
                    raise ValueError('Sequence must be provided to save in CT format.')
               #if isinstance(seq, Sequence):
               seq = str(seq)
               if len(seq) != len(self):
                    ScriptConfig.PrintError(str(self.pairs))
                    ScriptConfig.PrintError("len(seq) = {0}, len(self) = {1}\n".format(len(seq), len(self)));
                    raise ValueError('Sequence must have same length as structure.')
               base_pair = dict( (i,j) for i,j in self.pairs )
               base_pair.update( dict( (j,i) for i,j in self.pairs ) )
               seq = seq.upper()
               header_lines = [
                    'Filename: {0}'.format(metadata.filename),
                    'Organism: {0}'.format(metadata.organism_name),
                    'Accession Numbers: {0}'.format(metadata.accession_number),
                    'Created using the process described in README.md for the \'{0}\' structure'.format(struct_type),
                    '%d   dG =     %1.2f  [initially     %1.1f]' % (len(self), energy, energy),
               ]
               header = '\n'.join(header_lines) + '\n'
               numIdxDigits = int(math.ceil(math.log(len(seq), 10)))
               line = '%{0}d  %s  %{0}d  %{0}s  %{0}d  %{0}d\n'.format(numIdxDigits)
               with open(filename, 'w', encoding='utf-8') as ctHandle:
                    ctHandle.write(header)
                    for (i, base) in enumerate(seq):
                         ip1 = i + 2 if i < len(self) else 0
                         j = self.struct_pair_array[i] if self.struct_pair_array[i] else 0
                         curLine = line % (i + 1, base, i, ip1, j, i + 1) 
                         ctHandle.write(curLine)
               ##
          else:
               raise ValueError('Unknown sequence output extension \'{0}\'.'.format(ext))
          ##
     ##
     
     def GenerateConstraints(self, force_entire_structure = False):
          """Generate (gtfold) constraint file from current structure."""
          pairs = self.pairs[:]
          initial_constraints = [[i, j, 1] for i, j in pairs]
          condensed_constraints = self.CondenseForcedConstraints(initial_constraints)
          expanded_constraints = self.ExpandForcedConstraints(condensed_constraints)
          assert expanded_constraints == initial_constraints
          final_constraints = self.RemoveTightHairpinsFromConstraints(condensed_constraints)
          final_constraints = [[i, j, k] for i, j, k in final_constraints if k > 1] # exclude isolated pairs
          final_constraints = self.ExpandForcedConstraints(final_constraints)
          ScriptConfig.PrintInfo('The following constraints were removed from the original list of constraints:')
          for constraint in initial_constraints:
               if constraint not in final_constraints:
                    ScriptConfig.PrintInfo('\t{0}'.format(constraint))
          ScriptConfig.PrintInfo('The following constraints are new in the final_constraints list:')
          for constraint in final_constraints:
               if constraint not in initial_constraints:
                    ScriptConfig.PrintInfo('\t{0}'.format(constraint))
          constraints=""
          for i, j, k in final_constraints:
               constraints += "F {0} {1} {2}\n".format(i, j, k)
          if force_entire_structure:
               filterFunc = lambda lp: lp[1] == "."
               for idx,param in filter(filterFunc, enumerate(self.struct, 1)):
                    constraints += "P {0} 0 1\n".format(idx)
          return constraints
     ##

     @staticmethod
     def CondenseForcedConstraints(forced_constraints):
     # e.g. combines [83, 88, 1] and [84, 87, 1] into [83, 88, 2]
     # forced_constraints: list of constraints, where the last element of each constraint must be ONE
          forced_constraints = sorted(forced_constraints, key = lambda ijk: ijk[0])
          condensed_constraints = []
          previous_i = -1
          previous_j = -1
          for i, j, k in forced_constraints:
               assert k == 1
               if (i == previous_i + 1) and (j == previous_j - 1): # then this pair is stacked inside the previous pair
                    condensed_constraints[-1][-1] += 1 # increment k for the previous pair
               else:
                    condensed_constraints.append([i, j, k])
               previous_i, previous_j = i, j
          return condensed_constraints
     ##

     @staticmethod
     def ExpandForcedConstraints(condensed_constraints):
          expanded_constraints = []
          for i, j, k in condensed_constraints:
               for nesting in range(k):
                    expanded_constraints.append([i + nesting, j - nesting, 1])
          return expanded_constraints
     ##

     @staticmethod
     def RemoveTightHairpinsFromConstraints(constraints):
     # Removes any base pair for which i and j are too close together
     # The constraints list may be condensed or expanded
     # The resulting list of constraints may contain isolated base pairs
     # e.g. [83, 88, 2] would become [83, 88, 1]; [84, 87, 1] would be removed entirely
          new_constraints = []
          for i, j, k in constraints:
               empty_space_in_hairpin = (j - k + 1) - (i + k - 1)
               while empty_space_in_hairpin <= 3:
                    k -= 1
                    empty_space_in_hairpin = (j - k + 1) - (i + k - 1)
                    if k > 0:
                         new_constraints.append([i, j, k])
          return new_constraints
     ##

     def GetGTFoldPythonConstraints(self, force_entire_structure = False):
          """Generate (gtfold) constraint file from current structure."""
          pairs = self.pairs[:]
          initial_constraints = [[i, j, 1] for i, j in pairs]
          condensed_constraints = self.CondenseForcedConstraints(initial_constraints)
          expanded_constraints = self.ExpandForcedConstraints(condensed_constraints)
          assert expanded_constraints == initial_constraints
          final_constraints = self.RemoveTightHairpinsFromConstraints(condensed_constraints)
          final_constraints = [[i, j, k] for i, j, k in final_constraints if k > 1] # exclude isolated pairs
          final_constraints = self.ExpandForcedConstraints(final_constraints)
          gtfoldConsList = []
          for i, j, k in final_constraints:
              gtfoldConsList.append([GTFoldPython.F, i, j, k]) # assert k == 1 (above)
          if force_entire_structure:
               filterFunc = lambda lp: lp[1] == "."
               for idx,param in filter(filterFunc, enumerate(self.struct, 1)):
                    gtfoldConsList.append([GTFoldPython.P, idx, 0, 1])
                    #constraints += "P {0} 0 1\n".format(idx)
          return gtfoldConsList
     ##

     def GenerateConstrainedStruct(self, sequence, force_entire_structure = False):
          GTFoldPython.Init()
          consList = self.GetGTFoldPythonConstraints(force_entire_structure)
          baseSeq = str(sequence)
          (mfe, constrSS) = GTFoldPython.GetMFEStructure(baseSeq, consList)
          return constrSS, mfe
     ##

##
