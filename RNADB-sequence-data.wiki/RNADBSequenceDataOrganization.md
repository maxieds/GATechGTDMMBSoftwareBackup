# RNADB Sequence Data Organization Scheme Into Subdirectories

There are a couple of active directories with variable numbers of sequences for both testing purposes (on a small sample set), to generate the full contents of the RNADB, and also some preserving historical data used by gtDMMB projects. The following lists of the contents of the relevant subdirectories found in the repository:

* [**DatabaseSequenceData:**](https://github.gatech.edu/gtDMMB/RNADB-sequence-data/tree/master/DatabaseSequenceData) The primary active compendia of sequences we have available for RNADB. Note that processing this entire directory at a single session with the construction script is time consuming.
* [**EmilyOldSequenceSources:**](https://github.gatech.edu/gtDMMB/RNADB-sequence-data/tree/master/EmilyOldSequenceSources) This directory contains the historical contents of the first internal instantiation of the RNADB project. It has been archived here for posterity. The data was retrieved (if memory serves) from the online databases on the RNADB Plesk webservers. Credit for these sources and the original compilation of this data is due to Emily Rogers.
* [**HelperScripts:**](https://github.gatech.edu/gtDMMB/RNADB-sequence-data/tree/master/HelperScripts) Contains helper ``bash`` shell scripts written to assist with processing batches of the sequence files.
* [**SampleSequenceData:**](https://github.gatech.edu/gtDMMB/RNADB-sequence-data/tree/master/SampleSequenceData) Historical archive of past RNADB sequences work. See the [README file](https://github.gatech.edu/gtDMMB/RNADB-sequence-data/blob/master/SampleSequenceData/SAMPLE_DATA_README.md) for this subdirectory.
* [**TestCasesByPaper:**](https://github.gatech.edu/gtDMMB/RNADB-sequence-data/tree/master/TestCasesByPaper) A subset of the paper-by-paper sequences associated to a few publications. This small sample set is designed for testing. A supplementary example of usage running the RNADB construction script on this data set is [archived here](https://github.gatech.edu/gtDMMB/RNADB-construction/wiki/ExampleUsage#another-example-from-the-week-of-june-24-2020-meeting-notes).