# MainArgumentParser.py : Define and parse the commandline (terminal) arguments to the main script;
# Author: Maxie D. Schmidt (maxieds@gmail.com)
# Created: 2019.06.18

import argparse
from argparse import ArgumentParser
from sys import argv
from ScriptConfig import ScriptConfig
from os.path import abspath

from Utils import Utils

class MainArgParser:

    def __init__(self, progName=ScriptConfig.PROGRAM_NAME,
                 progDesc=ScriptConfig.PROGRAM_DESC,
                 progEpilog=ScriptConfig.PROGRAM_HELP_EPILOG):
        self.argParserInst = ArgumentParser(prog=progName, description=progDesc,
                                            epilog=progEpilog,
                                            argument_default=argparse.SUPPRESS,
                                            add_help=True)
        self.argParserInst.add_argument('--InDir', required=True,
                                        help="Directory containing family subdirectories.",
                                        dest="in_dir")
        self.argParserInst.add_argument('--OutDir', required=True,
                                        help="Dirname for output ct, txt and zipped data files.",
                                        dest="out_dir")
        self.argParserInst.add_argument('--OutCSV', required=True,
                                        help="Filename path for output csv file.",
                                        dest="out_csv")
        self.argParserInst.add_argument('--NoUpdate', required=False, action='store_false', 
                                        default=True, 
                                        help="Re-run the script on *all* sequences, even ones that have not been updated.", 
                                        dest="update_only")
        self.argParserInst.add_argument('--StartingRID', type=int, default=1,
                                        help="Starting value for rid field (primary index key in database).",
                                        dest="starting_rid")
        self.argParserInst.add_argument('--Papers', type=str, default="",
                                        metavar="P1,...,PN", nargs=1, 
                                        help="Comma delimited with no spaces list of papers (all lc alphanumeric) to append to all sequences in the batch.",
                                        dest="papers_list")
        self.argParserInst.add_argument('--NoTerminalColor', action="store_false",
                                        default=True,
                                        help="Suppress printing of ANSI escape sequences in terminal output, i.e., color formatted text.",
                                        dest="terminal_color")
        self.argParserInst.add_argument('-q', '--Quiet', action="store_true",
                                        default=False,
                                        help="Suppress printing of all output except fatal exceptions and errors.",
                                        dest="quiet")
        self.argParserInst.add_argument('-v', '--Verbose', action="store_true",
                                        default=False,
                                        help="Print more detailed output to console.",
                                        dest="verbose")
        self.argParserInst.add_argument('-vv', '--VVerbose', action="store_true",
                                        default=False,
                                        help="Print the most detailed standard output to console.",
                                        dest="vverbose")
        self.argParserInst.add_argument('-d', '--Debugging', type=str,
                                        choices=[
                                            "ON", "OFF", "YES", "NO", "Y", "N", "0", "1", "True", "False", "TRUE", "FALSE"],
                                        default="OFF",
                                        help="Turn printing of debugging-only messages ON/OFF by default.",
                                        dest="debugging")
        self.argParserInst.add_argument('-ponly', '--PrintActionsOnly',
                                        action="store_true", default=False,
                                        help="Print mock actions on the inputs without actually generating the output files",
                                        dest="print_actions_only")
        self.argsParsed = False
        self.parsedVars = None
    ##

    def ParseArgs(self, args=argv[1:]):
        if not self.argsParsed:
            self.argsParsed = True
            self.parsedVars = self.argParserInst.parse_args(args)
        return self.parsedVars
    ##

    def PrintUsage(self, progName=argv[0]):
        self.argParserInst.print_help(progName)
    ##

    def SetupGlobalConfig(self):
        self.ParseArgs()
        argParserVars = self.parsedVars
        ScriptConfig.COLOR_PRINTING = argParserVars.terminal_color
        ScriptConfig.QUIET = argParserVars.quiet
        ScriptConfig.DEBUGGING = argParserVars.debugging in ["ON", "YES", "Y", "1", "True", "TRUE"]
        ScriptConfig.VERBOSE = argParserVars.verbose or argParserVars.vverbose or ScriptConfig.DEBUGGING
        ScriptConfig.VVERBOSE = argParserVars.vverbose or ScriptConfig.DEBUGGING
        ScriptConfig.PROGRAM_PRINT_ACTIONS_ONLY = argParserVars.print_actions_only
        ScriptConfig.UPDATE_ONLY = argParserVars.update_only
    ##

    def GetInputDir(self):
        self.ParseArgs()
        return abspath(str(self.parsedVars.in_dir))
    ##

    def GetOutputDir(self):
        self.ParseArgs()
        return abspath(str(self.parsedVars.out_dir))
    ##

    def GetOutputCSVPath(self):
        self.ParseArgs()
        return abspath(str(self.parsedVars.out_csv))
    ##

    def MarkProgress(self):
        self.ParseArgs()
        return self.parsedVars.mark_progress
    ##

    def GetStartingRID(self):
        self.ParseArgs()
        return self.parsedVars.starting_rid
    ##

    def GetPapersList(self):
        self.ParseArgs()
        return self.parsedVars.papers_list
    ##
    
    def GetUpdateOnlyStatus(self):
        self.ParseArgs()
        return self.parsedVars.update_only
    ##

##
