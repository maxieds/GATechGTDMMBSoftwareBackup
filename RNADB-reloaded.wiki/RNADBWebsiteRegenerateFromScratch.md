# RNADB website regeneration (frontend and backend) -- For group administrators

This step will regenerate, or replicate, the web server configuration needed to setup RNADB.
```bash
$ cd $GTDMMB_HOME
$ git clone https://github.gatech.edu/gtDMMB/RNADB-reloaded.git
$ cd RNADB-reloaded
```
Now we are going to have to upload the PHP / JavaScript source for the web interface to 
an active webserver. For the purposes of this documentation, we will perform this reinstall 
action on the live RNADB webserver housed by *PleskWebAdmin* at GA Tech. Users that want to develop this 
code and actively test it on their own personal machine locally will need to configure 
``apache2`` and ``mysql-server`` and ``php7/8`` on their own, 
build the MySQL tables (and initial data) and create the users, and 
create a symlink from the source directory we just cloned into the ``$WEBROOT`` for testing. 

## Optional: Instructions to configure MySQL on the production GA Tech webserver

Within the [GA Tech PleskWebAdmin](https://hosting.gatech.edu) 
we already have added a MySQL database called ``rnadb_reloaded`` (under the LHS *Databases* panel 
tab). Make sure that a user for this database with username **compbiouser4** and 
password **qazWSX123$%^--++1234..** are added before attempting to proceed. If this needs to be 
re-initialized (ever, for any reason -- otherwise skip this step, and assume the the one titled 
group's resident *Code Goddess* has handled this for you already) complete the following steps: 
open up the *PhpMyAdmin* interface to this database, 
delete all pre-existing tables and values, and then navigate to the top (upper) *SQL* tab to 
enter SQL scripting commands directly. 
Note that it might be necessary to use the login information 
*username* (**rnadb**) with *password* (**44R2w_uf**) if you are prompted when trying to 
access the web administration interface. 
Then enter the following version of the 
[SQL scripts](https://github.gatech.edu/gtDMMB/RNADB-reloaded/tree/master/Admin/sql) 
used to re-create the RNADB tables under local developer testing conditions:
```sql
DROP DATABASE IF EXISTS rnadb_reloaded;
CREATE DATABASE IF NOT EXISTS rnadb_reloaded;
USE rnadb_reloaded;

CREATE TABLE IF NOT EXISTS rnadb_reloaded.AdminUsers (
     `uid`                INT(10) unsigned NOT NULL AUTO_INCREMENT,
     `user_name`          VARCHAR(20) NOT NULL, 
     `full_name`          VARCHAR(50) NOT NULL,
     `email`              VARCHAR(30),
     `pwd_hash`           VARCHAR(256) NOT NULL,
     `last_login`         VARCHAR(75),
     `current_login`      VARCHAR(75),
     `active`             INT(2) NOT NULL,
     `groups`             SET('wheel', 'sequences', 'papers'),  
     PRIMARY KEY(`user_name`),
     INDEX(`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS rnadb_reloaded.Papers (
     `pid`                INT(10) unsigned NOT NULL AUTO_INCREMENT,
     `paper_key`          VARCHAR(128) NOT NULL,
     `authors`            TEXT NOT NULL,
     `title`              TEXT NOT NULL,
     `pub_data`           TEXT NOT NULL,
     `date`               TEXT NOT NULL,
     `doi`                TEXT NOT NULL,
     `comments`           TEXT,
     `history`            TEXT,
     PRIMARY KEY(`paper_key`),
     INDEX(`pid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS rnadb_reloaded.Sequences (  
     `rid`               INT(10) unsigned NOT NULL AUTO_INCREMENT, 
     `latin_name`        TEXT NOT NULL,
     `family`            TEXT NOT NULL,
     `accession`         TEXT NOT NULL,
     `papers`            TEXT NOT NULL,
     `length`            INT(10) NOT NULL,
     `acc_length`        INT(10) NOT NULL,
     `seq_start`         INT(10),
     `seq_stop`          INT(10),
     `gc_content`        FLOAT(12) NOT NULL,
     `fasta_txt`         TEXT,
     `initial_fragment`  TEXT,
     `seq_checksum`      TEXT NOT NULL,
     `orig_ct`           TEXT,
     `nop_ct`            TEXT,
     `canon_ct`          TEXT,
     `clean_ct`          TEXT,
     `mfe_ct`            TEXT,
     `forced_ct`         TEXT,
     `orig_bp`           INT(10) NOT NULL,
     `nop_bp`            INT(10) NOT NULL,
     `canon_bp`          INT(10) NOT NULL,
     `clean_bp`          INT(10) NOT NULL,
     `mfe_bp`            INT(10) NOT NULL,
     `forced_bp`         INT(10) NOT NULL,
     `knots_txt`         TEXT,
     `pseudoknots`       TEXT,
     `noncanonical_txt`  TEXT,
     `isolated_txt`      TEXT,
     `clean_energy`      FLOAT(12) NOT NULL,
     `mfe_energy`        FLOAT(12) NOT NULL,
     `forced_energy`     FLOAT(12) NOT NULL,
     `completeness`      FLOAT(12) NOT NULL,
     `tp`                INT(10) NOT NULL,
     `fp`                INT(10) NOT NULL,
     `fn`                INT(10) NOT NULL,
     `precision_val`     FLOAT(12) NOT NULL,
     `recall`            FLOAT(12) NOT NULL,
     `f_measure`         FLOAT(12) NOT NULL,
     `ambiguous`         INT(2) NOT NULL,
     `notes`             TEXT, 
     `history`           TEXT,
     PRIMARY KEY(`rid`), 
     INDEX(`rid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

SET @defaultPasswordHash := '$2y$10$GhXtDgH78xkxDIqAvz5gB.Hvul8CSZbspksRyRsqyO46KBA3FBHTe'; -- Defaults To: "gtDMMB1234!!" (no quotes)
SET @defaultGroups := 'papers,sequences';
SET @adminGroups := 'wheel,papers,sequences';

INSERT INTO rnadb_reloaded.AdminUsers
     (`uid`, `user_name`, `full_name`, `email`, `pwd_hash`, `last_login`, 
      `current_login`, `active`, `groups`) VALUES
     (1, 'maxieds', 'Maxie', 'maxieds@gmail.com', @defaultPasswordHash, 
         'None', 'None', 1, @adminGroups),
     (2, 'cheitsch3', 'Christine', 'heitsch@math.gatech.edu', @defaultPasswordHash, 
         'None', 'None', 1, @defaultGroups)
;

INSERT INTO rnadb_reloaded.Papers
     (`pid`, `paper_key`, `authors`, `title`, `pub_data`, `date`, 
      `doi`, `comments`, `history`) VALUES
     (1, 'poznanovic14', 'Svetlana Poznanovic and Christine E. Heitsch', 
         'Asymptotic distribution of motifs in a stochastic context-free grammar model of RNA', 
         'Journal of Mathematical Biology', '2014', 
         'https://link.springer.com/article/10.1007%2Fs00285-013-0750-y', '', ''), 
     (1, 'rogers14', 'Emily Rogers and Christine Heitsch', 
         'Profiling small RNA reveals multimodal substructural signals in a Boltzmann ensemble', 
         'Nucleic Acids Research', '2014', 
         'https://academic.oup.com/nar/article/42/22/e171/2411910', '', ''), 
     (1, 'rogers16', 'Emily Rogers and Christine Heitsch', 
         'New insights from cluster analysis methods for RNA secondary structure prediction', 
         'WIREs RNA', '2016', 
         'https://onlinelibrary.wiley.com/doi/abs/10.1002/wrna.1334', '', ''), 
     (1, 'rogers17', 'Emily Rogers, David Murrugarra, and Christine Heitsch', 
         'Conditioning and Robustness of RNA Boltzmann Sampling under Thermodynamic Parameter Perturbations', 
         'Biophysical Journal', '2017', 
         'https://www.sciencedirect.com/science/article/pii/S0006349517305659?via%3Dihub', '', ''), 
     (1, 'sukosd13', 
         'Zsuzsanna Sukosd, M. Shel Swenson, Jorgen Kjems, and Christine E. Heitsch', 
         'Evaluating the accuracy of SHAPE-directed RNA secondary structure predictions', 
         'Nucleic Acids Research', '2013', 
         'https://academic.oup.com/nar/article/41/5/2807/2414458', '', ''),
     (1, 'unpublished', 
         '', 
         '', 
         'Reserved for sequences in submitted, though not yet published papers of the group.', 
         '', '', '', '')
;
```

## Optional: Instructions to upload source files onto the production webserver

We need to upload the source files onto the website via ``scp`` (copying files from 
local client to remote server using SSH). By policy, GA Tech restricts access to the 
*Plesk* web hosting services for users off campus. To perform the next actions, you will 
either need to be located on the campus network, or have an active authentication with it 
via an external VPN client application on your local desktop machine 
(see [official OIT instructions](https://faq.oit.gatech.edu/content/how-do-i-get-started-campus-vpn)). You will also (probably) need to update a public 
RSA/SSH key into the *Plesk: Websites & Domains > SSH Keys* interface 
(see [instructions for generating the key](https://secure.vexxhost.com/billing/index.php/knowledgebase/171/How-can-I-generate-SSH-keys-on-Mac-OS-X.html)). 
Once you are able to access the server this way, complete the 
following commands:
```bash
$ cd $GTDMMB_HOME/RNADB-reloaded
$ scp -o user=rnadb -rp ./* 130.207.188.153:httpsdocs/
```