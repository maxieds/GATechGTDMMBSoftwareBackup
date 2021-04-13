## Usage: RunRNADBConstruction.sh [SequenceDataInputDir] [SequenceDataOutputDir]
#!/usr/bin/env bash

PYTHON3=`which python3`

MATH_MULBERRY_SRC=/projects/rna/RNADBConstructionScript
SEQDATA_INDIR="$MATH_MULBERRY_SRC/RNADB-sequence-data/DatabaseSequenceData"
SAMPLE_OUTDIR=./RNADBConstructionScriptOutput-`date +"%Y.%m.%d-%H%M%S"`

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

GTFPYTHON_INSTALL_PATH="$MATH_MULBERRY_SRC/GTFoldPython/Python/PythonLibrarySrc"
GTFPYTHON_INSTALL_LIBS_PATH="$MATH_MULBERRY_SRC/GTFoldPython/Python/Lib"
RUNNER_SCRIPT_PATH="$MATH_MULBERRY_SRC/RNADB-construction/rna2ndary"

export PYTHONPATH="$GTFPYTHON_INSTALL_PATH:$RUNNER_SCRIPT_PATH"
export LD_LIBRARY_PATH="$LD_LIBRARY_PATH:$GTFPYTHON_INSTALL_PATH"
export DYLD_LIBRARY_PATH="$DYLD_LIBRARY_PATH:$GTFPYTHON_INSTALL_LIBS_PATH"
export DYLD_FALLBACK_LIBRARY_PATH="/usr/lib:/usr/local/lib:$DYLD_FALLBACK_LIBRARY_PATH"

$PYTHON3 $RUNNER_SCRIPT_PATH/GenerateRNADBSequenceData.py \
    --InDir=$SEQDATA_INDIR \
    --OutDir=$SAMPLE_OUTDIR \
    --OutCSV=$SAMPLE_OUTDIR/rnadb-sample-out.csv \
    `echo $AppendedScriptOptions`

echo -e "\nNOTE: If you had errors running the script try running the following command first:"
echo -e "      $ pip3 install --user numpy requests\n"
