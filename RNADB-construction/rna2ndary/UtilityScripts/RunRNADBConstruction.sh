## Usage: RunRNADBConstruction.sh [SequenceDataInputDir] [SequenceDataOutputDir]
#!/usr/bin/env bash

PYTHON3=`which python3`

SEQDATA_INDIR="./RNADB-sequence-data/DatabaseSequenceData"
SAMPLE_OUTDIR=$GTDMMB_HOME/RNADBConstructionScriptOutput-`date +"%Y.%m.%d-%H%M%S"`

AppendedScriptOptions=( "${@:1:${#@}}" )
if [[ "$1" != "" ]]; then
    SEQDATA_INDIR=$1
    AppendedScriptOptions=( "${@:2:${#@}-2}" )
fi
if [[ "$2" != "" ]]; then
    SAMPLE_OUTDIR=$2
    AppendedScriptOptions=( "${@:3:${#@}-2}" )
fi

mkdir -p $SAMPLE_OUTDIR
rm -rf $SAMPLE_OUTDIR/*

GTFPYTHON_INSTALL_PATH="$GTDMMB_HOME/GTFoldPython/Python/PythonLibrarySrc"
GTFPYTHON_INSTALL_LIBS_PATH="$GTDMMB_HOME/GTFoldPython/Python/Lib"
RUNNER_SCRIPT_PATH="$GTDMMB_HOME/RNADB-construction/rna2ndary"

export PYTHONPATH="$GTFPYTHON_INSTALL_PATH:$RUNNER_SCRIPT_PATH"
export LD_LIBRARY_PATH="$LD_LIBRARY_PATH:$GTFPYTHON_INSTALL_PATH"
export DYLD_LIBRARY_PATH="$DYLD_LIBRARY_PATH:$GTFPYTHON_INSTALL_LIBS_PATH"
export DYLD_FALLBACK_LIBRARY_PATH="/usr/lib:/usr/local/lib:$DYLD_FALLBACK_LIBRARY_PATH"

$PYTHON3 $RUNNER_SCRIPT_PATH/GenerateRNADBSequenceData.py \
    --InDir=$SEQDATA_INDIR \
    --OutDir=$SAMPLE_OUTDIR \
    --OutCSV=$SAMPLE_OUTDIR/rnadb-sample-out.csv \
    `echo $AppendedScriptOptions`
