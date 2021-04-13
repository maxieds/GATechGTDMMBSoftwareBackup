# SequenceUpdates.py : Handles writing the updates output CSV file and 
#                      determining whether the data for a given input 
#                      sequence has changed since last running the script
# Author: Maxie D. Schmidt (maxieds@gmail.com)
# Created: 2019.11.08

from os.path import splitext, basename, exists, isdir, dirname, join
import os as os
import sys
import csv as CSV

from Utils import Utils, HashAlgorithmType
from ScriptConfig import ScriptConfig
from Exceptions import SequenceError

class SequenceUpdates:

    def __init__(self):
        self.updatesOutFD = None
        self.csvUpdatesOutWriter = None
        self.InitOldUpdatesLookupTable(oldUpdatesFilePath)
        self.InitUpdatesOutfile(updatesOutfilePath)
    ##
    
    @staticmethod
    def GetSequenceDataHashes(fullCTBaseName, family, ctBaseName, quietOutput = False):
        updateData = SequenceUpdates.GetEmptySequenceUpdateDataStruct()
        updateData['org_family'] = family
        updateData['org_base_name'] = ctBaseName
        updateData['org_cthash'] = Utils.HashFileFromPath(fullCTBaseName + ScriptConfig.CT_FEXT, hashType=HashAlgorithmType.HASHALG_SHA512, quiet=quietOutput)
        updateData['org_nopct_hash'] = Utils.HashFileFromPath(fullCTBaseName + ScriptConfig.NOPCT_FEXT, hashType=HashAlgorithmType.HASHALG_SHA512, quiet=quietOutput)
        updateData['org_comments_hash'] = Utils.HashFileFromPath(fullCTBaseName + ScriptConfig.COMMENTS_FEXT, hashType=HashAlgorithmType.HASHALG_SHA1, quiet=True)
        updateData['org_papers_hash'] = Utils.HashFileFromPath(fullCTBaseName + ScriptConfig.PAPERS_FEXT, hashType=HashAlgorithmType.HASHALG_MD5, quiet=True)
        updateData['org_date_last_updated'] = " -- Hash data created on {0} -- ".format(Utils.GetDateStamp())
        return updateData
    ##

    @staticmethod
    def GetSequenceDataHasChanged(fullCTBaseName, seqUpdateData):
        seqProcessedHashFilePath = fullCTBaseName + ScriptConfig.PROCESSED_HASH_FEXT
        if not exists(seqProcessedHashFilePath):
            return (True, True)
        seqDataHashFieldsToCheck = [
            'org_cthash', 
            'org_nopct_hash',
        ]
        seqMetadataHashFieldsToCheck = [
            'org_comments_hash',
            'org_papers_hash',
        ]
        numHashFileLinesToCheck = len(seqDataHashFieldsToCheck) + len(seqMetadataHashFieldsToCheck)
        hashFileDataLines = []
        with open(seqProcessedHashFilePath, 'r', encoding='utf-8') as hashFileHandler:
            for lineNum in range(0, numHashFileLinesToCheck):
                hashFileDataLines.append(hashFileHandler.readline()[:-1])
        coreSeqDataChange = False in [ hashFileDataLines[hidx] == seqUpdateData[ seqDataHashFieldsToCheck[hidx] ] \
                                       for hidx in range(0, len(seqDataHashFieldsToCheck)) ]
        hashFileDataLines = hashFileDataLines[len(seqDataHashFieldsToCheck):]
        metaSeqDataChange = False in [ hashFileDataLines[hidx] == seqUpdateData[ seqMetadataHashFieldsToCheck[hidx] ] \
                                       for hidx in range(0, len(seqMetadataHashFieldsToCheck)) ]
        return (coreSeqDataChange, metaSeqDataChange)
    ##

    @staticmethod
    def WriteSequenceProcessedHashDataFile(fullCTBaseName, seqUpdateData):
        seqHashDataFields = [
            'org_cthash',
            'org_nopct_hash',
            'org_comments_hash',
            'org_papers_hash',
            'org_date_last_updated',
        ]
        procHashFilePath = fullCTBaseName + ScriptConfig.PROCESSED_HASH_FEXT
        if exists(procHashFilePath):
            os.unlink(procHashFilePath)
        with open(procHashFilePath, "w", encoding='utf-8') as procFD:
            for (fidx, dataField) in enumerate(seqHashDataFields):
                procFD.write(seqUpdateData[dataField] + '\n')
    ##

    @staticmethod
    def GetEmptySequenceUpdateDataStruct():
        return dict((fld, '') for fld in ScriptConfig.UPDATE_ONLY_CSV_FIELDS)
    ##

##
