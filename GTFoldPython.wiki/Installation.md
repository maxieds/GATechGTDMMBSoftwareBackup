# Installation notes for GTFoldPython

## Installation of software prerequisites (from scratch)

To keep all of these repositories stashed in a convenient place for reference, let's setup a common 
space in the user's home directory that we can refer to later:

**(On Mac -- from a Bash shell in Terminal)**
```bash
$ echo -e "export GTDMMB_HOME=\"$HOME/GTDMMBSoftware\"" >> ~/.bash_profile
$ source ~/.bash_profile
$ mkdir -p $GTDMMB_HOME
```
**(On Linux -- from a Bash shell in your favorite XTerm emulator)**
```bash
$ echo -e "export GTDMMB_HOME=\"$HOME/GTDMMBSoftware\"" >> ~/.bashrc
$ source ~/.bashrc
$ mkdir -p $GTDMMB_HOME
```

## Platform specific install instructions

### Dependencies: Linux (Debian-based, e.g., Ubuntu or Mint)

To get the necessary Python3 development and other C source headers 
installed, run the following:
```bash
$ sudo apt-get install python3-dev shtool m4 texinfo libgomp1 libgmp-dev
$ sudo apt-get install python3-numpy
```
Next, check the version of the ``gcc`` compiler you are running by typing
```bash
$ gcc --version
gcc (Ubuntu 9.2.1-9ubuntu2) 9.2.1 20191008
Copyright (C) 2019 Free Software Foundation, Inc.
This is free software; see the source for copying conditions.  There is NO
warranty; not even for MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
```
If you are running an older distribution using a version of ``gcc`` prior to ``gcc-8``, then 
you will need to upgrade your GNU C compiler:
```bash
$ apt-cache search gcc-8 gcc-9
$ sudo apt-get install gcc-8
$ which gcc && ls -l $(which gcc)
/usr/bin/gcc
lrwxrwxrwx 1 root root 5 Jan 28 07:13 /usr/bin/gcc -> gcc-9
$ sudo rm /usr/bin/gcc
$ sudo ln -s gcc-8 /usr/bin/gcc
$ gcc --version
```
Note that on ``math-mulberry`` the sysadmins will need to ensure that some python packages are installed. 
You can request they run
```bash
$ sudo pip3 install numpy
```

### Dependencies (Mac OSX instructions)

The full set of platform indeterminate [install instructions](https://github.gatech.edu/gtDMMB/GTFoldPython/blob/master/Python/Docs/Install.md) 
can be referenced (or adapted) to install these Python bindings, and the associated 
Python wrapper library, on a Linux or semi-standard Unix platform besides MacOS. The 
next instructions are Mac OSX (10.14.x -- codename Mojave) specific: 
```bash
$ brew install coreutils gnu-sed numpy python shtool
$ brew install autoconf automake libtool
$ brew install binutils
$ pip3 install numpy
#### NO LONGER NEEDED ???:
#$ brew link --overwrite python3
#$ pip3 install requests
#$ pip3 install enum34
```

#### Optional terminal packages (with brew)

In general, we want to be able to assume the user has access to reasonable 
(e.g., GNU versioned, non-Apple-default) standardized interfaces to common 
shell commands like ``sed,grep,bash`` (among others). Otherwise we can run into 
syntactical compatibility issues stemming from off-standard support for POSIX 
(and other de facto GNU-like) interfaces supported via commandline options when 
running on Mac OSX. To avoid this known issue on Mac, install GNU compatible 
replacements for these core Unix shell utilities using the following commands:
```bash
$ brew install grep coreutils make bash
```
Note that the ``grep`` package may be optional. Restart your terminal session under Mac to use the new Bash shell version. 

## Cloning and building from source (all platforms)

On ``math-mulberry`` make sure we are using the latest compiler tools by first running
```bash
scl enable devtoolset-9 /bin/bash
```
Now to build the GTFold python bindings run the following commands:
```bash
$ cd $GTDMMB_HOME
$ git clone https://github.gatech.edu/gtDMMB/GTFoldPython.git
$ cd GTFoldPython/Python
$ make clean && make
... PATIENCE: THIS WILL TAKE A WHILE TO BUILD ...
$ make bash-configure
(ON LINUX) $ source ~/.bashrc
(ON MACOS) $ source ~/.bash_profile
$ make test
```
If the build is successful, but some or all of the test cases fail, please post a new issue to the 
[GTFoldPython issues](https://github.gatech.edu/gtDMMB/GTFoldPython/issues) pages so the problem can be addressed.

## Testing the installation

Try running the following from within the same directory to test and verify the installation:
```bash
$ python3 Testing/RunBasicInterface.py
```
