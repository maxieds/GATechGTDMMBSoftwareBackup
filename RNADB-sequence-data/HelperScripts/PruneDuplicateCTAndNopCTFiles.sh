#!/bin/bash

#### PruneDuplicateCTAndNopCTFiles.sh : Bash script to delete NopCT files in the sequence data 
####                                    directory which are duplicates of the corresponding 
####                                    CT file for the sequence (and are hence irrelevant data);
####                                    If a single argument is passed to the script, it is 
####                                    interpreted as the sequence data directory we are operating on, 
####                                    whereas if no arguments are passed to the script, we assume that 
####                                    the sequence data directory is the current working directory.
####                                    Usage Examples:
####                                    (1) $ cd ~/RNADB-sequence-data/DatabaseSequenceData
####                                        $ /bin/bash ../HelperScripts/PruneDuplicateCTAndNopCTFiles.sh
####                                    (2) $ /bin/bash ~/RNADB-sequence-data/HelperScripts/PruneDuplicateCTAndNopCTFiles.sh \
####                                          ~/RNADB-sequence-data/DatabaseSequenceData
####
#### Author: Maxie D. Schmidt
#### Created: 2019.11.09

if [[ $# -gt 0 ]]; then
     nextCwd=`readlink -f $1`
     nextRelCwd=$1
     echo -e ">> Sequence data directory is \"${nextCwd}\""
     echo -e "   [\"${nextRelCwd}\"] ...\n"
     cd $1
else 
     cwd="`readlink -f `pwd``"
     echo -e ">> Sequence data directory is \"${cwd}\" ...\n"
fi

for famDir in $(ls);
do 
     echo -e ">> Processing family directory ${famDir}/* ..."
     for ctFile in $(ls $famDir/*.ct);
     do 
          ctFileBase=`basename $ctFile .ct`
          if [ -f "${famDir}/${ctFileBase}.nopct" ]; then
               ctNopCTFileDiff="$(diff $famDir/$ctFileBase.ct $famDir/$ctFileBase.nopct)"
               if [[ $ctNopCTFileDiff == "" ]]; then
                    echo -e "  >>>> Removing duplicate NopCT file [${famDir}] \"${ctFileBase}.nopct\" ..."
                    rm $famDir/$ctFileBase.nopct
                    git rm $famDir/$ctFileBase.nopct --quiet
               else
                    echo -e "  >>>> Files [${famDir}] \"${ctFileBase}.ct\" and \"${ctFileBase}.nopct\" differ ..."
               fi
          else
               echo -e "  >>>> NopCT file \"${ctFileBase}.nopct does not exist ..."
          fi
     done
     echo ""
done

echo -e ">> Done processing sequence data files ... Exiting normally\n"
exit 0

