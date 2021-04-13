# Command line options to the script

## Getting basic help for the script

Run the following command to obtain basic help for the options the script accepts:
```bash
$ cd rna2ndary
$ python3 GenerateRNADBSequenceData.py -h
usage: GenerareRNADBSequenceData [-h] --InDir IN_DIR --OutDir OUT_DIR --OutCSV OUT_CSV [--NoUpdate] [--StartingRID STARTING_RID] [--Papers P1,...,PN] [--NoTerminalColor] [-q] [-v] [-vv]
                                 [-d {ON,OFF,YES,NO,Y,N,0,1,True,False,TRUE,FALSE}] [-ponly]

Program for generating CSV and zipped sequence data files to upload to the RNADB web interface.

optional arguments:
  -h, --help            show this help message and exit
  --InDir IN_DIR        Directory containing family subdirectories.
  --OutDir OUT_DIR      Dirname for output ct, txt and zipped data files.
  --OutCSV OUT_CSV      Filename path for output csv file.
  --NoUpdate            Re-run the script on *all* sequences, even ones that have not been updated.
  --StartingRID STARTING_RID
                        Starting value for rid field (primary index key in database).
  --Papers P1,...,PN    Comma delimited with no spaces list of papers (all lc alphanumeric) to append to all sequences in the batch.
  --NoTerminalColor     Suppress printing of ANSI escape sequences in terminal output, i.e., color formatted text.
  -q, --Quiet           Suppress printing of all output except fatal exceptions and errors.
  -v, --Verbose         Print more detailed output to console.
  -vv, --VVerbose       Print the most detailed standard output to console.
  -d {ON,OFF,YES,NO,Y,N,0,1,True,False,TRUE,FALSE}, --Debugging {ON,OFF,YES,NO,Y,N,0,1,True,False,TRUE,FALSE}
                        Turn printing of debugging-only messages ON/OFF by default.
  -ponly, --PrintActionsOnly
                        Print mock actions on the inputs without actually generating the output files

------------------------------
```

## Description of core components and command line options

* **--InDir=&lt;InputDirectoryPath&gt;** *(Required)* Directory containing family subdirectories
* **--OutDir=&lt;OutputDirectoryPath&gt;** *(Required)* Dirname for output ct, txt and zipped data files
* **--OutCSV=&lt;OutputCSVFilePath&gt;** *(Required)* Filename path for output csv file
* **--NoUpdate** Re-run the script on &ast;all&ast; sequences, even ones that have not been updated
* **--StartingRID=&lt;StartingRowIDAsUINT&gt;** Starting value for rid field (primary index key in database)
* **--Papers=&lt;P1,P2,...,PN&gt;** Comma delimited with no spaces list of papers (all lc alphanumeric) to append to all sequences in the batch
* **--NoTerminalColor** Suppress printing of ANSI escape sequences in terminal output, i.e., color formatted text
* **-q,--Quiet** Suppress printing of all output except fatal exceptions and errors
* **-v,--Verbose** Print more detailed output to console
* **-vv,--VVerbose** Print the most detailed standard output to console
* **-d,--Debugging=&lt;ON|OFF&gt;** Turn printing of debugging-only messages ON/OFF by default
* **-ponly,--PrintActionsOnly** Print mock actions on the inputs without actually generating the output files