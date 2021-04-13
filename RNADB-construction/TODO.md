# TODO: Features and bugs which we should add/fix at some point

* fix bug where clean_struct_energy is 0.
* ncurses-based interface for ease of use?
* Add (sub)-timing data printing as we generate the CSV files.
* In ``Register.py``:
```
    def compute_sden(self):
        # Needs reimplementation
        pass
        comparass = self.native_struct
        # secondary_struct does no longer have a get_num_canonical_pairs method!
        ncan_comp = comparass.get_num_canonical_pairs(self.sequence)
        
        conpredss = self.cons_gtfold_struct
        
        predss = self.mfe_struct

        self.sden = float(comparass.num_pairs)/conpredss.num_pairs
        self.oldsden = float(conpredss.num_pairs)/comparass.num_pairs
        self.mist = (2.0*conpredss.num_pairs-1)/(2*predss.num_pairs-1)
        self.mist2 = conpredss.num_pairs/float(predss.num_pairs)
        self.mist3 = (2.0*ncan_comp-1)/(2*predss.num_pairs-1)
        self.win = ncan_comp/float(predss.num_pairs)
        print "-"*20
        print "Settin win to: {0}/{1}={2}".format(ncan_comp, predss.num_pairs,
                                                      self.win)
        print "-"*20
```
* In ``sec_struct.py``:
```
    def sden(self, other, seq):
        pass
        # Useless, needs reimplementation.
        num_native_canonical_pairs =  len(self.get_canonical_pairs(seq))
        num_predicted_canonical_pairs = len(other.get_canonical_pairs(seq))

        sden = num_native_canonical_pairs/float(num_predicted_canonical_pairs)

        return sden
    def compare_structures(self,other,comparison='f'):
        # Needs implementation
        pass
        # Interface to RNAdistance
        command = ['RNAdistance','-D',comparison]
        stdin_input = '{0}\n{1}'.format(self,other)
        out,err,retcode = run_command(command,stdin_input)
        if retcode:
            raise RuntimeWarning("RNAdistance returned nonzero code.")
        distance = int(out.split()[1])
        return distance
```
