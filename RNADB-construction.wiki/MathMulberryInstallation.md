# For group administrators (group use setup on math-mulberry)

To copy the installation to a place where other group members can access the scripts, 
we need to put the files into ``/projects/rna`` and set permissions:
```bash
$ cd /projects/rna
$ mkdir -p RNADBConstructionScript
$ cd RNADBConstructionScript
$ cp -r $GTDMMB_HOME/GTFoldPython $GTDMMB_HOME/RNADB-sequence-data $GTDMMB_HOME/RNADB-construction .

... PATIENCE: THIS MAY TAKE SOME TIME ...

$ cp RNADB-construction/rna2ndary/UtilityScripts/RunRNADBConstruction-MathMulberry.sh .
$ find . -type d -exec chmod g+rwxs {} \+ 
$ find . -type f -exec chmod g+rw {} \+ 
$ find . -type f -perm /u+x -exec chmod g+x {} \+ 
$ chgrp -R math-rnatope .
```