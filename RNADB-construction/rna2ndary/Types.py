# Types.py : Custom types (and namedtuples) for the runtime script;
# Author: Maxie D. Schmidt (maxieds@gmail.com)
# Created: 2019.06.18

from collections import namedtuple

DirInfoType = namedtuple('DirectoryInfo', ['ct_paths', 'out_dir', 'family'])

NopCTMetaData = namedtuple('NopCTMetaData', ['filename', 'organism_name', 'accession_number', 'url'])

