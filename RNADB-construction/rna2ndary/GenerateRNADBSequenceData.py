#!/usr/bin/env python

# GenerateRNADBSequenceData.py : Main runner script
# Author: Maxie D. Schmidt (maxieds@gmail.com)
# Created: 2019.06.18

import sys
from shutil import copyfile, rmtree
import time

from ScriptConfig import ScriptConfig
from MainArgumentParser import MainArgParser
from Utils import Utils

if __name__=="__main__":
    parser = MainArgParser()
    parser.SetupGlobalConfig()
    totalSequenceCount = 0
##

from Types import DirInfoType
from Register import Register
from SequenceUpdates import SequenceUpdates
from argparse import ArgumentParser
from collections import namedtuple
import csv as CSV
from glob import glob
from os import listdir, makedirs
from os.path import join, exists, isdir
from Utils import Utils
import os
import gc

def GenerateCSVFiles(family_dirs, out_csv, starting_id, default_batch_papers, updateOnly):
    fieldnames = ScriptConfig.OUTPUT_CSV_FIELDS
    processedSequenceCount = 1
    seqProcInitStartTime = time.time()
    with open(out_csv, 'w', encoding='utf-8') as csvfile:
        csv_writer = CSV.DictWriter(csvfile, 
                                fieldnames=fieldnames,
                                delimiter=',',
                                quotechar='\'',
                                quoting=CSV.QUOTE_NONNUMERIC
                     )
        csv_writer.writerow(dict((fn, fn) for fn in fieldnames))
        defaults = dict((fn, 'NULL') for fn in fieldnames)
        defaults['notes'] = ''
        defaults['history'] = ''
        defaults['papers'] = default_batch_papers
        cur_row_index = starting_id
        for family_dir in family_dirs:
            if not exists(family_dir.out_dir):
                 makedirs(family_dir.out_dir)
            ##
            for ct_path in sorted(family_dir.ct_paths):
                seqProcStartTime = time.time()
                ctBase = os.path.dirname(ct_path) + ScriptConfig.OSDIR_SEPARATOR + Utils.GetFileBaseName(ct_path)
                seqUpdateData = SequenceUpdates.GetSequenceDataHashes(ctBase, family_dir.family, Utils.GetFileBaseName(ct_path))
                (coreSeqChangeQ, metaSeqChangeQ) = SequenceUpdates.GetSequenceDataHasChanged(ctBase, seqUpdateData)
                seqChangeQ = coreSeqChangeQ or metaSeqChangeQ
                processSeqQ = seqChangeQ or not updateOnly
                if not processSeqQ:
                    ScriptConfig.PrintHighlighted("Skipping pre-processed structure [{0}] {1} (sequence data has not changed) ... ".format( 
                                                  family_dir.family, Utils.GetFileBaseName(ct_path)), 
                                                  "OMITTING STRUCTURE (#{0}/{1})".format(processedSequenceCount, totalSequenceCount))
                    processedSequenceCount += 1
                    continue
                ##
                if seqChangeQ:
                    SequenceUpdates.WriteSequenceProcessedHashDataFile(ctBase, seqUpdateData)
                ScriptConfig.PrintHighlighted("Processing structure [{0}] {1}!".format(
                                              family_dir.family, Utils.GetFileBaseName(ct_path)), 
                                              "{2}STRUCTURE FOUND (#{0}/{1})".format(processedSequenceCount, totalSequenceCount, "NEW|UPDATED " if seqChangeQ else ""))
                ScriptConfig.PrintInfo('Registering CT path: {0}'.format(ct_path))
                if not exists(ctBase + ScriptConfig.NOPCT_FEXT):
                    ScriptConfig.PrintInfo("[NOTE] NOPCT File \"{0}\" does not exist!".format(ctBase + ScriptConfig.NOPCT_FEXT));
                data = defaults.copy()
                R = Register(
                             ct_path,
                             family_dir.out_dir,
                             family_dir.family,
                             cur_row_index
                    )
                data.update(R.get_register_data())
                data['papers'] = Utils.MergeSequencePaperData(ct_path.replace(ScriptConfig.CT_FEXT, ScriptConfig.PAPERS_FEXT), data['papers'])
                data['notes'] = Utils.GetSequenceCommentsFromFile(ct_path)
                data['history'] = Utils.timestamp('Sequence data created on')
                data['rid'] = cur_row_index
                convertFieldFunc = lambda dataField: str(dataField.encode('utf-8'))[2:-1] if isinstance(dataField, bytes) else dataField
                dataEncoded = dict((fieldName, convertFieldFunc(data[fieldName])) for fieldName in fieldnames)
                csv_writer.writerow(dataEncoded)
                csvfile.flush()
                cur_row_index += 1
                processedSequenceCount += 1
                seqProcEndTime = time.time()
                seqProcTimeElapsed = seqProcEndTime - seqProcStartTime
                totalTimeElapsed = seqProcEndTime - seqProcInitStartTime
                ScriptConfig.PrintWarning("Total time elapsed processing sequence {0} sec [{1}m, or {2}h, so far] ...".format(
                                          seqProcTimeElapsed, totalTimeElapsed / 60.0, totalTimeElapsed / 3600.0), headerPrefix="PROCSSING TIME")
                print("\n")
            ##
        ##
    ##
    return True
##

if __name__=="__main__":

    ## Pre-processing for the script:
    family_dirs = []
    parser.SetupGlobalConfig()
    ScriptConfig.PrintHighlighted("RNADB Construction Script -- v{0}!".format(ScriptConfig.VERSION), "SCRIPT INIT")
    if not isdir(parser.GetInputDir()):
        ScriptConfig.PrintError("The specified input sequence data directory \"{0}\" does not exist!".format(parser.GetInputDir()))
        exit(ERROR_PARAMETER_ARGS)
    ##
    
    for family in sorted(listdir(parser.GetInputDir())): # 16S, 23S, 5S, tRNA, ...
        ScriptConfig.PrintInfo("Adding new sequence family directory: {0}/*".format(family))
        ct_paths = sorted(glob(join(parser.GetInputDir(), family, '*' + ScriptConfig.CT_FEXT)))
        totalSequenceCount += len(ct_paths)
        specific_out_dir = join(parser.GetOutputDir(), family)
        ScriptConfig.PrintInfo("Specific out dir: {0}".format(specific_out_dir))
        default_batch_papers = parser.GetPapersList()
        ScriptConfig.PrintInfo("Default papers list for this batch of files: {0}".format(default_batch_papers))
        info = DirInfoType(
             ct_paths    = ct_paths,
             out_dir     = specific_out_dir,
             family      = family,
        )
        family_dirs.append(info)
    ##
    print("\n")
    
    ## Call the main work horse of the script:
    scriptStatus = GenerateCSVFiles(family_dirs, parser.GetOutputCSVPath(), parser.GetStartingRID(), 
                                    default_batch_papers, parser.GetUpdateOnlyStatus())
    if not scriptStatus:
        try:
            os.remove(parser.GetOutputCSVPath())
            shutils.rmtree(parser.GetOutputDir())
        except Exception:
            ScriptConfig.PrintError("Unable to remove output CSV file and output directory before exiting...");
        ScriptConfig.PrintError("Exiting with abnormal exit code...");
        exit(ERROR_GENERIC)
    ##
    
    ## Clean up, copy dated output files:
    datedCSVFile = Utils.GetFileBaseName(parser.GetOutputCSVPath()) + "-" + Utils.GetDateStamp() + ScriptConfig.CSV_FEXT
    datedCSVFile = parser.GetOutputDir() + ScriptConfig.OSDIR_SEPARATOR + datedCSVFile
    copyfile(parser.GetOutputCSVPath(), datedCSVFile)
    (zipReturn, zipFile) = Utils.ZipDirectoryAndSaveInDirectory(
        Utils.GetFileBaseName(parser.GetOutputCSVPath()), parser.GetOutputDir(), True)
    
    ## Print summary
    ScriptConfig.PrintHighlighted("\n\n========= SUMMARY OF CONSTRUCTION SCRIPT RUN: =========\n\n")
    ScriptConfig.PrintHighlighted("Saved \"{0}\" to working output directory ...".format(datedCSVFile), "SCRIPT DONE")
    ScriptConfig.PrintHighlighted("Zipped generated files to \"{0}\" for RNADB uploads ...".format(zipFile), "SCRIPT DONE")
    
    ## Exit indicating successful run of the script:
    exit(ScriptConfig.EXIT_SUCCESS) 

##

