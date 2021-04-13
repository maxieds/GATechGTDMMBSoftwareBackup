# ScriptConfig.py : Global script configuration parameters;
# Author: Maxie D. Schmidt (maxieds@gmail.com)
# Created: 2019.06.18

import os

class ScriptConfig(object):

    VERSION = "1.0.6-testing"
    
    QUIET = False
    VERBOSE = False
    VVERBOSE = False
    DEBUGGING = False
    COLOR_PRINTING = True
    
    PROGRAM_NAME = "GenerareRNADBSequenceData"
    PROGRAM_DESC = "Program for generating CSV and zipped sequence data files to upload to the RNADB web interface."
    PROGRAM_HELP_EPILOG = " ------------------------------ "
    PROGRAM_PRINT_ACTIONS_ONLY = False

    CT_FEXT = '.ct'
    NOPCT_FEXT = '.nopct'
    COMMENTS_FEXT = '.comments'
    PAPERS_FEXT = '.paper'
    PROCESSED_HASH_FEXT = '.processed_hash'
    CSV_FEXT = '.csv'
    ZIP_FEXT = '.zip'

    OSDIR_SEPARATOR = os.sep # '/'

    OUTPUT_CSV_FIELDS = [
        'rid',
        'latin_name',
        'family',
        'accession',
        'papers',
        'length',
        'acc_length',
        'seq_start',
        'seq_stop',
        'gc_content',
        'fasta_txt',
        'initial_fragment',
        'seq_checksum',
        'orig_ct',
        'nop_ct',
        'canon_ct',
        'clean_ct',
        'mfe_ct',
        'forced_ct',
        'orig_bp',
        'nop_bp',
        'canon_bp',
        'clean_bp',
        'mfe_bp',
        'forced_bp',
        'knots_txt',
        'pseudoknots',
        'noncanonical_txt',
        'isolated_txt',
        'clean_energy',
        'mfe_energy',
        'forced_energy',
        'completeness',
        'tp',
        'fp',
        'fn',
        'precision_val',
        'recall',
        'f_measure',
        'ambiguous',
        'notes',
        'history',
    ]
    
    UPDATE_ONLY_CSV_FIELDS = [
        'org_family', 
        'org_base_name',
        'org_cthash',
        'org_nopct_hash',
        'org_comments_hash',
        'org_papers_hash',
        'org_date_last_updated',
    ]

    ## ANSI color codes """
    BLACK = "\033[0;30m"
    RED = "\033[0;31m"
    GREEN = "\033[0;32m"
    BROWN = "\033[0;33m"
    BLUE = "\033[0;34m"
    PURPLE = "\033[0;35m"
    CYAN = "\033[0;36m"
    LIGHT_GRAY = "\033[0;37m"
    DARK_GRAY = "\033[1;30m"
    LIGHT_RED = "\033[1;31m"
    LIGHT_GREEN = "\033[1;32m"
    YELLOW = "\033[1;33m"
    LIGHT_BLUE = "\033[1;34m"
    LIGHT_PURPLE = "\033[1;35m"
    LIGHT_CYAN = "\033[1;36m"
    LIGHT_WHITE = "\033[1;37m"
    BOLD = "\033[1m"
    FAINT = "\033[2m"
    ITALIC = "\033[3m"
    UNDERLINE = "\033[4m"
    BLINK = "\033[5m"
    NEGATIVE = "\033[7m"
    CROSSED = "\033[9m"
    END = "\033[0m"

    @staticmethod
    def PrintError(emsg):
        if not ScriptConfig.QUIET and ScriptConfig.COLOR_PRINTING:
             print(ScriptConfig.BOLD + ScriptConfig.LIGHT_RED + " >> " +
                   ScriptConfig.RED + "ERROR: " + ScriptConfig.LIGHT_BLUE + emsg + ScriptConfig.END)
        elif not ScriptConfig.QUIET:
            print(" >> ERROR: " + emsg)
        ##
    ##
    
    @staticmethod
    def PrintWarning(emsg, headerPrefix = "WARNING"):
        if not ScriptConfig.QUIET and ScriptConfig.COLOR_PRINTING:
             print(ScriptConfig.BOLD + ScriptConfig.LIGHT_GRAY + " >> " +
                   ScriptConfig.YELLOW + "{0}: ".format(headerPrefix) + ScriptConfig.PURPLE + emsg + ScriptConfig.END)
        elif not ScriptConfig.QUIET:
            print(" >> {0}: {1}".format(headerPrefix, emsg))
        ##
    ##

    @staticmethod
    def PrintInfo(emsg):
        if not ScriptConfig.QUIET and ScriptConfig.COLOR_PRINTING:
            print(ScriptConfig.BOLD + ScriptConfig.LIGHT_GRAY + " >> " +
                  ScriptConfig.LIGHT_GREEN + "INFO: " + ScriptConfig.LIGHT_BLUE + emsg + ScriptConfig.END)
        elif not ScriptConfig.QUIET:
            print(" >> INFO: " + emsg)
        ##
    ##

    @staticmethod
    def PrintDebug(emsg):
        if not ScriptConfig.QUIET and ScriptConfig.DEBUGGING and ScriptConfig.COLOR_PRINTING:
            print(ScriptConfig.BOLD + ScriptConfig.LIGHT_GRAY + " >> " +
                  ScriptConfig.LIGHT_GREEN + "DEBUGGING: " +
                  ScriptConfig.LIGHT_BLUE + emsg + ScriptConfig.END)
        elif not ScriptConfig.QUIET and ScriptConfig.DEBUGGING:
            print(" >> DEBUGGING: " + emsg)
        ##
    ##
    
    @staticmethod
    def PrintHighlighted(emsg, headerPrefix = "STATUS"):
        if not ScriptConfig.QUIET and ScriptConfig.COLOR_PRINTING:
            print(ScriptConfig.BOLD + ScriptConfig.LIGHT_GREEN + " >> "  + ScriptConfig.UNDERLINE + 
                  ScriptConfig.LIGHT_CYAN + "{0}: ".format(headerPrefix) +
                  ScriptConfig.LIGHT_PURPLE + emsg + ScriptConfig.END)
        elif not ScriptConfig.QUIET:
            print(" >> {0}: ".format(headerPrefix) + emsg)
        ##
    ##

    EXIT_SUCCESS = 0
    ERROR_GENERIC = -1
    ERROR_PARAMETER_ARGS = -2
    ERROR_TODO_FIX_CODE = -3
    EXIT_KDBINT = 1

## ScriptConfig
