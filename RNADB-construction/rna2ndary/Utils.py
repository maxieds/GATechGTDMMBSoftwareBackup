# Utils.py : Utility and helper functions;
# Author: Maxie D. Schmidt (maxieds@gmail.com)
# Created: 2019.06.18

from os.path import splitext, basename, exists, isdir, dirname
from tempfile import mkdtemp
from shutil import rmtree
from subprocess import Popen, PIPE
from datetime import datetime
from sys import stdout
from collections import namedtuple
from numpy import unique as npunique
import shutil as ziputil
from shutil import copyfile, rmtree
import tempfile
from enum import Enum, unique
import hashlib

from Exceptions import FileHashingError
from ScriptConfig import ScriptConfig

@unique
class HashAlgorithmType(Enum):
         
    HASHALG_MD5    = 1
    HASHALG_SHA1   = 2
    HASHALG_SHA224 = 3
    HASHALG_SHA256 = 4
    HASHALG_SHA384 = 5
    HASHALG_SHA512 = 6
        
    @staticmethod
    def searchByString(algTypeSearchStr, matchExact = False):
        if matchExact and re.search(r'\md5\b', algTypeSearchStr, re.I):
            return HashAlgorithmType.HASHALG_MD5
        elif matchExact and re.search(r'\sha1\b', algTypeSearchStr, re.I): 
            return HashAlgorithmType.HASHALG_SHA1
        elif matchExact and re.search(r'\sha224\b', algTypeSearchStr, re.I):
            return HashAlgorithmType.HASHALG_SHA224
        elif matchExact and re.search(r'\sha256\b', algTypeSearchStr, re.I):
            return HashAlgorithmType.HASHALG_SHA256
        elif matchExact and re.search(r'\sha384\b', algTypeSearchStr, re.I):
            return HashAlgorithmType.HASHALG_SHA384
        elif matchExact and re.search(r'\sha512\b', algTypeSearchStr, re.I):
            return HashAlgorithmType.HASHALG_SHA512
        elif 'md5' in algTypeSearchStr.lower():
            return HashAlgorithmType.HASHALG_MD5
        elif 'sha1' in algTypeSearchStr.lower():
            return HashAlgorithmType.HASHALG_SHA1
        elif 'sha224' in algTypeSearchStr.lower():
            return HashAlgorithmType.HASHALG_SHA224
        elif 'sha256' in algTypeSearchStr.lower():
            return HashAlgorithmType.HASHALG_SHA256
        elif 'sha384' in algTypeSearchStr.lower():
            return HashAlgorithmType.HASHALG_SHA384
        elif 'sha512' in algTypeSearchStr.lower():
            return HashAlgorithmType.HASHALG_SHA512
        elif 'default' in algTypeSearchStr.lower():
            return HashAlgorithmType.getDefault()
        else:
            return None
    ##
        
    @staticmethod
    def getDefault():
        return HashAlgorithmType.HASHALG_SHA512
    ##
        
    @staticmethod
    def checkValidHashAlgorithm(hashType):
         return hashType in HashAlgorithmType._value2member_map_
    ##
        
    @staticmethod
    def getHasher(hashType):
        if hashType == HashAlgorithmType.HASHALG_MD5:
            return hashlib.md5()
        elif hashType == HashAlgorithmType.HASHALG_SHA1:
            return hashlib.sha1()
        elif hashType == HashAlgorithmType.HASHALG_SHA224:
            return hashlib.sha224()
        elif hashType == HashAlgorithmType.HASHALG_SHA256:
            return hashlib.sha256()
        elif hashType == HashAlgorithmType.HASHALG_SHA384:
            return hashlib.sha384()
        elif hashType == HashAlgorithmType.HASHALG_SHA512:
            return hashlib.sha512()
        else:
            return None
    ##
        
    @staticmethod
    def getDefaultHasher():
        return HashAlgorithmType.getHasher(HashAlgorithmType.getDefault())
    ##
        
##

class Utils:

    @staticmethod
    def Python3BytesToStringConvert(s):
        """Simplify PythonV3 string versus bytes type handling."""
        if isinstance(s, str):
            return s
        elif isinstance(s, bytes):
            return str(s.decode('utf-8').encode('utf-8'))[2:-1]
        return s
    
    @staticmethod
    def oprint(message):
        """Overprint to stdout."""
        stdout.write("{0}\r".format(message))
        stdout.flush()

    @staticmethod
    def timestamp(message):
        """Return a timestamped message."""
        return "{1}: {0}".format(datetime.now(), message)

    @staticmethod
    def timestamp_stdout(message):
        """Print a timestamped message to stdout."""
        return "[{0}] {1}".format(datetime.now(), message)

    @staticmethod
    def write_pair_list_to_file(filename, pair_list):
        if pair_list == None:
            return None
        with open(filename, 'w') as outfile:
            for x, y in pair_list:
                outfile.write("{0} {1}\n".format(x, y))
    ##

    @staticmethod
    def write_to_file(filename, content):
        with open(filename, 'w') as outfile:
            outfile.write(content)

    @staticmethod
    def run_command(command, stdin_input=None):
        """Execute command in a new process. Return stdout, stderr and return code."""
        process = Popen(command, stdin=PIPE, stdout=PIPE, stderr=PIPE,
                        close_fds=True)
        out, err = process.communicate(input=stdin_input)
        retcode = process.returncode
        return out, err, retcode

    @staticmethod
    def run_shell_command(command, stdin_input=None):
        """Execute command in a new shell. Return stdout, stderr and return code."""
        process = Popen(command, stdin=PIPE, stdout=PIPE, stderr=PIPE,
                        close_fds=True, shell=True)
        out, err = process.communicate(input=stdin_input)
        retcode = process.returncode
        return out, err, retcode

    @staticmethod
    def parse_rational(s):
        """Convert a str "a/b" representing rational to pair of integers (a,b)."""
        n, d = s.split('/') if s.find('/') > -1 else (s, '1')
        try:
            n = int(n)
            d = int(d)
        except ValueError:
            raise ValueError(
                "Error parsing this rational number: {0}".format(s))
        return (n, d)

    @staticmethod
    def rat_to_str(r):
        """Convert integer or pair of integers to a str repr of a rational.

           If a single integer a is given as input, the we simply raturn
           str(a). Otherwise, if a pair of integers (a,b) is given, we return
           "a/b".
        """
        if isinstance(r, int):
            return str(r)
        return '{0}/{1}'.format(r[0], r[1])

    @staticmethod
    def strip_path_and_ext(fname):
        return splitext(basename(fname))[0]

    @staticmethod
    def GetFileExt(fname):
        return splitext(fname)[1][1:].lower()

    @staticmethod
    def GetFileBaseName(fname):
        return splitext(basename(fname))[0]

    @staticmethod
    def GetDateStamp():
        return datetime.now().strftime("%Y.%m.%d-%H%M%S");

    @staticmethod
    def MergeSequencePaperData(seqBaseNamePath, paperDefaults):
        if not exists(seqBaseNamePath):
            return paperDefaults
        with open(seqBaseNamePath, 'r') as paperDataFile:
            seqPaperData = paperDataFile.read().replace('\n', '')
            if seqPaperData == "":
                return paperDefaults
            seqPaperData += "," + paperDefaults
            paperKeyList = seqPaperData.split(",")
            paperKeyTempDict = dict.fromkeys(paperKeyList, 1) 
            paperKeyList = list(dict(npunique(paperKeyTempDict)[0]).keys())
            paperDataFile.close()
            return ';'.join(paperKeyList)[:-1]
        return paperDefaults
    ##

    @staticmethod
    def GetSequenceCommentsFromFile(ct_file_path):
        commentsFile = ct_file_path.replace(ScriptConfig.CT_FEXT, ScriptConfig.COMMENTS_FEXT)        
        if not exists(commentsFile):
            return ""
        with open(commentsFile, 'r') as commentFileHandler:
            commentsLine = commentFileHandler.read()
            commentFileHandler.close()
            return commentsLine
        return ""
    ##
    
    @staticmethod
    def CreateZipFile(destZipFile, dirToArchive, addDateStamp = True):
        if not exists(dirToArchive) or not isdir(dirToArchive):
             return False, destZipFile
        zipFileFolder = dirname(destZipFile)
        zipFileBaseName = Utils.GetFileBaseName(destZipFile)
        zipFileDateStamp = "-" + Utils.GetDateStamp() if addDateStamp else ""
        finalZipOutFile = zipFileFolder + ScriptConfig.OSDIR_SEPARATOR + zipFileBaseName + zipFileDateStamp
        createArchiveResult = ziputil.make_archive(finalZipOutFile, 'zip', root_dir = dirToArchive)
        return createArchiveResult, "{0}{1}".format(finalZipOutFile, ScriptConfig.ZIP_FEXT)
    ##
    
    @staticmethod
    def ZipDirectoryAndSaveInDirectory(zipFileBaseName, dirToArchive, addDateStamp = True):
        if not exists(dirToArchive) or not isdir(dirToArchive):
            return False, zipFileBaseName
        tempDir = tempfile.mkdtemp()
        tempFileBaseName = tempDir + ScriptConfig.OSDIR_SEPARATOR + Utils.GetFileBaseName(zipFileBaseName)
        (zipResult, zipFullPath) = Utils.CreateZipFile(tempFileBaseName, dirToArchive, addDateStamp)
        finalZipPath = dirToArchive + ScriptConfig.OSDIR_SEPARATOR + Utils.GetFileBaseName(zipFullPath) + ScriptConfig.ZIP_FEXT
        if exists(finalZipPath):
            rmtree(tempDir)
            return False, finalZipPath
        copyfile(zipFullPath, finalZipPath)
        rmtree(tempDir)
        return True, finalZipPath
    ##
    
    @staticmethod
    def HashFileFromPath(filePath, hashType = HashAlgorithmType.getDefault(), quiet = False):
        if not exists(filePath):
            if not quiet:
                ScriptConfig.PrintWarning("Unable to hash file \"{0}\": File does not exist ...".format(filePath))
            return ""
        hasherObj = HashAlgorithmType.getHasher(hashType)
        if hasherObj == None:
            ScriptConfig.PrintError("Invalid hash algorithm selected: " + hashType)
            raise FileHashingError("Invalid hash algorithm selected: " + hashType)
            return ""
        blockSize = hasherObj.block_size
        with open(filePath, 'rb', buffering = 0) as fd:
            while True:
                fileBlockData = fd.read(blockSize)
                if not fileBlockData:
                    break
                hasherObj.update(fileBlockData)
        return hasherObj.hexdigest()
    ##

##
