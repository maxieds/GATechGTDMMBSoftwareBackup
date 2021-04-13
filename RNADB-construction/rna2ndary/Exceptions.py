# Exceptions.py : Miscellaneous exceptions used throughout this code.
# Author: Maxie D. Schmidt (maxieds@gmail.com)
# Created: 2019.06.18

class DirectoryError(Exception):
    def __init__(self, value):
        self.value = value

    def __str__(self):
        return repr(self.value)
##

class SequenceError(Exception):
    def __init__(self, value):
        self.value = value

    def __str__(self):
        return repr(self.value)
##

class AmbiguousSequenceException(Exception):
    def __init__(self, value):
        self.value = value

    def __str__(self):
        return repr(self.value)
##

class NoOutput(Exception):
    def __init__(self, value):
        self.value = value

    def __str__(self):
        return repr(self.value)
##

class FileHashingError(Exception):
    def __init__(self, value):
        self.value = value

    def __str__(self):
        return repr(self.value)
##
