# RNADB construction tutorial

## About RNADB

RNADB is a gtDMMB-sponsored selection of RNA structures used to supplement papers and projects coming our of the group. 
The database can be searched, and selected sequence data downloaded, at [this link](https://rnadb.gatech.edu/FrontEnd/Search.php). 
Authorized administrative users can add new papers to which sequences can be associated, and add / delete / manage / update 
existing sequences via [this link](https://rnadb.gatech.edu/Admin/Login.php) 
(protected by a select list of GTIDs via a ``.htaccess`` file in Apache). 
Typically new sequences will be updated in batches using the CSV-formatted and zipped CT data files created by running the 
RNADB construction script (see the [CSV output format](https://github.gatech.edu/gtDMMB/RNADB-construction/blob/master/CSV-OUTFILE-INFO.md)). 
The source for the RNADB construction script is written in Python (with ViennaRNA Python2 bindings) and 
updates to it are managed in [this repository](https://github.gatech.edu/gtDMMB/rnadb-construction). 

The source for the web front and backends for RNADB are housed in 
[this repository](https://github.gatech.edu/gtDMMB/RNADB-reloaded). 
Updated to the sequence data on which the construction script housed in this repository operates on are 
housed in [this repository](https://github.gatech.edu/gtDMMB/RNADB-sequence-data). 

## Installation and usage options for the construction script (Linux and Mac)

### OPTION 1: Install via git and run the script from your terminal (Linux and Mac users)

#### Dependency: readlink (Linux) or greadlink (Mac OSX)

##### On Linux

Run the following: 
```
$ export READLINK=`which readlink`
```

##### On Mac OSX

Run the following: 
```
$ brew update
$ brew install coreutils
$ export READLINK=`which greadlink`
```

#### Dependency: GTFoldPython library

Follow the instructions given [here](https://github.gatech.edu/gtDMMB/GTFoldPython/blob/master/Python/Docs/Install.md).

#### Installation of the script

To install this repository locally run the following commands:
```
$ cd ~
$ git clone https://github.gatech.edu/gtDMMB/RNADB-sequence-data.git 
$ git clone https://github.gatech.edu/gtDMMB/RNADB-construction.git
```
If you ever need to update the contents of either of these repositories to the most recent version, 
run the following commands:
```
$ cd ~/RNADB-sequence-data
$ git pull
$ cd ~/RNADB-construction
$ git pull
```

#### Running the script 

Run the script by running the following command (assumes the BASH profile configuration setup above):
```
$ SAMPLE_OUTDIR="RNADBSampleOutputs-`date +"%Y.%m.%d-%H%M%S"`" && mkdir -p $SAMPLE_OUTDIR rm -rf $SAMPLE_OUTDIR/* && \
  time RNADBConstruction --InDir=~/rnadb-construction-sequence-data/SampleSequenceData/sample_data \
  --OutDir=$SAMPLE_OUTDIR --OutCSV=$SAMPLE_OUTDIR/rnadb-sample-out.csv
```
OR:
```bash
$ chmod +x ~/rnadb-construction/rna2ndary/UtilityScripts/generate_all_structures.sh
$ ~/rnadb-construction/rna2ndary/UtilityScripts/generate_all_structures.sh
```

### OPTION 2: Install an easy-to-use wrapper script via brew (Mac OSX users only)

To make your life a little easier in using these command line scripts, I have again put together some ``brew`` packages for Mac OSX which make both installing the necessary files and locating the scripts and their dependencies a breeze.
Here is the setup (run the following at your terminal):
```
$ echo "export HOMEBREW_GITHUB_API_TOKEN=\"0493a65c8c899c6acf54c112df4e31bfa65e237e\"" >> ~/.bash_profile
$ echo "export HOMEBREW_NO_GITHUB_API=1" >> ~/.bash_profile
$ export GTUSERNAME="asaaidi3" # substitute with your GT login ID, e.g., cheitsch3 or mschmidt34
$ source ~/.bash_profile
$ echo -e "machine github.gatech.edu\n    login ${GTUSERNAME}\n    password cadfeeca26a660b2a6adf82af897e000e795be79" >> ~/.netrc
$ brew tap gtDMMB/homebrew-gtdmmb https://github.gatech.edu/gtDMMB/homebrew-gtdmmb
$ brew install --verbose gtDMMB/gtdmmb/rnadbconstruction
```
Once the packages are installed, run the following command to start the (long, by hours) process of (re)generating the sequence data featured in the RNADB:
```
$ /bin/bash RunRNADBConstruction.sh
```

