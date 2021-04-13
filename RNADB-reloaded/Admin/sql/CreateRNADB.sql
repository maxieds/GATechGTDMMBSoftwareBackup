-- CreateRNADB.sql : Creates the full RNADB and all of its sub-tables;
-- Author: Maxie D. Schmidt (maxieds@gmail.com)
-- Created: 2019.06.15

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

