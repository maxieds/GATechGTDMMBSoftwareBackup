# RNADB construction script test cases

## Generating the output files to test

Once you have *GTFoldPython* configured by running its ``make bash-configure``, run the following:

```
$ mkdir -p TestOutputs
$ rm -rf TestOutputs/*
$ ../UtilityScripts/RunRNADBConstruction.sh Testing/TestCases/Inputs Testing/TestCases/Outputs --NoUpdate
$ ../UtilityScripts/DiffSequences.sh
```

## Test cases

### 1. 
* *Latin Name:* Lactobacillus delbrueckii subsp. bulgaricus ATCC BAA-365
* *Family:* tRNA
* *Reason:* Short intro sequence for testing
* *Properties:* No pseudoknots, no isolated pairs

### 2. 
* *Latin Name:* Homo sapiens
* *Family:* tRNA
* *Reason:* Longer intro sequence for testing
* *Properties:* No pseudoknots, no isolated pairs

### 3.
* *Latin Name:* E. nidulans
* *Family:* 16S
* *Reason:* Longer intro sequence for testing
* *Properties:* Has pseudoknots, has isolated pairs
