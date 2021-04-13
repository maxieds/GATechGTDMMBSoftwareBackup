# Example usage of the RNADB construction script

## Download the active sequence data set (unprocessed raw source data)

```bash
$ cd $GTDMMB_HOME
$ git clone https://github.gatech.edu/gtDMMB/RNADB-sequence-data.git
```

## First time generation of post-processed files on the full data set

```bash
$ cd $GTDMMB_HOME/RNADB-construction/rna2ndary
$ ./UtilityScripts/RunRNADBConstruction.sh
```

## Updating sequence data and re-running the script

After updating and running ``git pull`` in the sequence data repository directory as
```bash
$ cd $GTDMMB_HOME/RNADB-sequence-data
$ git pull
$ cd ..
```
the user can re-run the script as above to generate the data for **only the new and/or updated** 
sequences. If you wish to re-generate the post-processed sequence data for RNADB for **ALL** 
sequences instead, then run the following command instead:
```bash
$ cd $GTDMMB_HOME/RNADB-construction/rna2ndary
$ ./UtilityScripts/RunRNADBConstruction.sh --NoUpdate
```

## Another example (from the week of June 24, 2020 meeting notes)

First, run the construction script on just the *Rogers14* paper sequences:
```bash
$ cd $GTDMMB_HOME/RNADB-construction/rna2ndary
$ ./UtilityScripts/RunRNADBConstruction.sh $GTDMMB_HOME/RNADB-sequence-data/TestCasesByPaper/Rogers14/ $GTDMMB_HOME/TestCasesOutput
```
Next, to verify the update-only feature is working consider the following 
command output:
```bash
$ cd $GTDMMB_HOME/RNADB-construction/rna2ndary
$ cp $GTDMMB_HOME/RNADB-sequence-data/TestCasesByPaper/Rogers14/5S/* $GTDMMB_HOME/RNADB-sequence-data/TestCasesByPaper/CombinedR14R17/5S/
$ cp $GTDMMB_HOME/RNADB-sequence-data/TestCasesByPaper/Rogers14/tRNA/* $GTDMMB_HOME/RNADB-sequence-data/TestCasesByPaper/CombinedR14R17/tRNA/
$ ./UtilityScripts/RunRNADBConstruction.sh $GTDMMB_HOME/RNADB-sequence-data/TestCasesByPaper/CombinedR14R17/ $GTDMMB_HOME/TestCasesOutput
```
What you should have observed is that only the papers for the *Rogers17* paper 
were re-processed and packaged. That is, the *Rogers14* paper sequences and data 
has been marked as unchanged by the first run of the script above, so we notice 
this the second time the script is run with new sequences included in the input 
and decide to only run the expensive GTFold computations on the new/updated 
sequences. 
