# RNADB construction script user install procedure

The script is written in ``python3``. It's source can be fetched for command line use by running 
the following commands:
```bash
$ cd $GTDMMB_HOME
$ git clone https://github.gatech.edu/gtDMMB/RNADB-construction.git
```
Users will need to install the following ``python3`` packages to run the script:
```bash
$ pip3 install numpy enum
```
Note that to run the script, you will need the [GTFold Python3 bindings](https://github.gatech.edu/gtDMMB/GTFoldPython/wiki) installed on the target system. See the [example usage](https://github.gatech.edu/gtDMMB/RNADB-construction/wiki/ExampleUsage) and [helper scripts subdirectory](https://github.gatech.edu/gtDMMB/RNADB-construction/tree/master/rna2ndary/UtilityScripts) for information on how to run the script once the sources are installed.