#!/bin/bash

TESTOUTDIR=Testing/TestOutputs
EXACTOUTDIR=Testing/TestCases/Outputs

echo -e "Checking Test Outputs Against Generated Files:\n";

for dir in $TESTOUTDIR/*/; do 
     echo "Dir" $dir;
     for file in $dir*; do
          echo "File" $file;
          exactFile=$EXACTOUTDIR/$(basename $file);
          diffFile=$file;
          diffResult="$(diff $exactFile $diffFile)"
          [[ "$diffResult" == "" ]] && diffStatus="[OK]" || diffStatus="[XX]";
          echo " >> Diffing $file ... $diffStatus";
          if [[ "$diffResult" != "" ]]; then 
               echo $diffResult;
          fi
     done
done
