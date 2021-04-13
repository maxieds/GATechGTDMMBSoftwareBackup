# TempDir.py : Classes for creating (and automatically deleting on exit) temporary directories;
# Author: Maxie D. Schmidt (maxieds@gmail.com)
# Created: 2019.06.18

import ScriptConfig


class TempDir:
    """Class for temporary directories that are automatically deleted on exit."""

    def __init__(self, suffix='_rna2ndary_'):
        self.dirname = mkdtemp(suffix)
        ScriptConfig.PrintInfo("created directory: {0}".format(self))

    def __repr__(self):
        return self.dirname

    def __str__(self):
        return self.dirname

    def __enter__(self):
        return self

    def __exit__(self, exc_type, exc_value, traceback):
        rmtree(self.dirname)
        ScriptConfig.PrintInfo("deleted directory: {0}".format(self))
##


class semitempdir:
    """Class for temporary directories. Not automatically deleted on exit."""

    def __init__(self, suffix='_rna2ndary_'):
        self.dirname = mkdtemp(suffix)
        ScriptConfig.PrintInfo("created directory: {0}".format(self))

    def __repr__(self):
        return self.dirname

    def __str__(self):
        return self.dirname

    def __enter__(self):
        return self

    def __exit__(self, exc_type, exc_value, traceback):
        ScriptConfig.PrintInfo("directory: {0} not deleted".format(self))
##
