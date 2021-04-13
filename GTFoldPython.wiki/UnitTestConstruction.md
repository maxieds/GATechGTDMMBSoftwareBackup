# GTFoldPython library unit tests

## Construction of the unit tests

The purpose of putting several unit tests in place is to guarantee 
consistency and accuracy of the implementation of the Python 
bindings we have written over time. 
We have to rely on the output of the original ``GTFold`` shell utility 
to generate output since auxilliary programs like ``ViennaRNA`` and 
``RNAStructure`` use significantly different methods to generate 
their folding data. The command line specification for ``GTFold`` is 
found in 
[this online documentation](http://gtfold.sourceforge.net/guide.html). 
We use the ``cmake``-enabled build of [GTFold forked here](https://github.com/gtDMMB/gtfold).

### Commands used to generate the testing data

#### Unit test 1 (no constraints)

```bash
$ ./bin/gtmfe -v -d2 -m -dS --exactintloop -m --prefilter 2 ../GTFoldPython/Python/Testing/TestData/5S/E.coli.fa.seq 
GTfold: A Scalable Multicore Code for RNA Secondary Structure Prediction
(c) 2007-2011  D.A. Bader, C.E. Heitsch, S.C. Harvey
Georgia Institute of Technology

Checking for environ variable 'GTFOLDDATADIR', found 
Run Configuration:
+ enabled terminal mismatch calculations
+ running with prefilter value = 2
- thermodynamic parameters: /Users/mschmidt34/GTDMMBSoftware/GTFoldPython/Python/Testing/ExtraGTFoldThermoData/GTFoldTurner99/
- input file: ../GTFoldPython/Python/Testing/TestData/5S/E.coli.fa.seq
- sequence length: 120
- output file: E.coli.fa.ct

Computing minimum free energy structure...
Done.

Results:
- Minimum Free Energy:     -55.0000 kcal/mol
- MFE runtime:  0.006120 seconds


UGCCUGGCGGCCGUAGCGCGGUGGUCCCACCUGACCCCAUGCCGAACUCAGAAGUGAAACGCCGUAGCGCCGAUGGUAGUGUGGGGUCUCCCCAUGCGAGAGUAGGGAACUGCCAGGCAU
((((((((((.........((((....)))).((((((((((.....(((....)))...(((((.......))))).))))))))))..(((.(((....))))))..)))))))))).

MFE structure saved in .ct format to E.coli.fa.ct
```
**NOTE:** Specifying the historical ``--rnafold`` option to the GTFold ``gtmfe`` utility changes the MFE results slightly. This is not set by default within the Python bindings code, so it can generate failed unit tests without setting up options in the test runners first.

#### Unit test 2 (with constraints)

```bash
$ ./bin/gtmfe -v -d2 -m -dS --exactintloop -m --prefilter 2 ../GTFoldPython/Python/Testing/TestData/5S/E.coli.fa.seq -c ../GTFoldPython/Python/Testing/TestData/5S/E.coli.fa.cons



```

## Running the unit tests

From the main ``GTFoldPython`` directory issue the following 
commands:
```bash
cd Python 
make test
```
Successful unit test output should resemble the following:
```bash
|| GTFOLD-PYTHON UNIT TEST INFO: (test1_16S_K00421)
    ## 1
    >> Test Purpose:    Basic MFE calculation without constraints
    >> Organism:        d.16.a.H.volcanii.bpseq
    >> Base Sequence:   [#1474] AUUCCGGU ... CUGGAUCACCUCCUG
    >> Constraints:     None enabled
////
 ... TEST PASSED! [OK]
.


|| GTFOLD-PYTHON UNIT TEST INFO: (test2_5S_EColiFa)
    ## 2
    >> Test Purpose:    Basic MFE calculation without constraints
    >> Organism:         E. coli.fs (Native structure) -- 5S, rRNA
    >> Base Sequence:   [#120] UGCCUGGC ... GGAACUGCCAGGCAU
    >> Constraints:     None enabled
////
 ... TEST PASSED! [OK]
.

|| GTFOLD-PYTHON UNIT TEST INFO: (test2_5S_EColiFa_withcons_RNAfold)
    ## 3
    >> Test Purpose:    Basic MFE calculation *with* constraints
    >> Organism:         E. coli.fs (Native structure) -- 5S, rRNA
    >> Base Sequence:   [#120] UGCCUGGC ... GGAACUGCCAGGCAU
    >> Constraints:     8 Total, 0 Forced, 8 Prohibited
////

 ... TEST PASSED! [OK]
.


|| GTFOLD-PYTHON UNIT TEST INFO: (test3_tRNA_yeastFa)
    ## 4
    >> Test Purpose:    Basic MFE calculation without constraints
    >> Organism:        tRNA(asp), yeast.fa (Native structure) ENERGY = -34.3
    >> Base Sequence:   [#75] GCCGUGAU ... CCCGUCGCGGCGCCA
    >> Constraints:     None enabled
////
 ... TEST PASSED! [OK]
.

|| GTFOLD-PYTHON UNIT TEST INFO: (test3_tRNA_yeastFa_withcons_RNAfold)
    ## 5
    >> Test Purpose:    Basic MFE calculation *with* constraints
    >> Organism:        tRNA(asp), yeast.fa (Native structure) ENERGY = -34.3
    >> Base Sequence:   [#75] GCCGUGAU ... CCCGUCGCGGCGCCA
    >> Constraints:     325 Total, 7 Forced, 318 Prohibited
////

 ... TEST PASSED! [OK]
.


|| GTFOLD-PYTHON UNIT TEST INFO: (test4_other_humanFa)
    ## 6
    >> Test Purpose:    Basic MFE calculation without constraints
    >> Organism:        Telomerase pseudoknot, human.fa (Native structure)
    >> Base Sequence:   [#47] GGGCUGUU ... ACAAAAAAAGUCAGC
    >> Constraints:     None enabled
////
 ... TEST PASSED! [OK]
.

|| GTFOLD-PYTHON UNIT TEST INFO: (test4_other_humanFa_withcons_RNAfold)
    ## 7
    >> Test Purpose:    Basic MFE calculation *with* constraints
    >> Organism:        Telomerase pseudoknot, human.fa (Native structure)
    >> Base Sequence:   [#47] GGGCUGUU ... ACAAAAAAAGUCAGC
    >> Constraints:     5 Total, 3 Forced, 2 Prohibited
////

 ... TEST PASSED! [OK]
.


|| GTFOLD-PYTHON UNIT TEST INFO: (test5_other_PSyringae)
    ## 8
    >> Test Purpose:    Basic MFE calculation without constraints
    >> Organism:        ENERGY = -45.2  F Sensing RS (Native structure)
    >> Base Sequence:   [#66] GCAUUGGA ... GAUGAUGCCUACAGA
    >> Constraints:     None enabled
////
 ... TEST PASSED! [OK]
.

|| GTFOLD-PYTHON UNIT TEST INFO: (test5_other_PSyringae_withcons_RNAfold)
    ## 9
    >> Test Purpose:    Basic MFE calculation *with* constraints
    >> Organism:        ENERGY = -45.2  F Sensing RS (Native structure)
    >> Base Sequence:   [#66] GCAUUGGA ... GAUGAUGCCUACAGA
    >> Constraints:     6 Total, 6 Forced, 0 Prohibited
////

 ... TEST PASSED! [OK]
.
----------------------------------------------------------------------
Ran 9 tests in 20.284s

OK
```