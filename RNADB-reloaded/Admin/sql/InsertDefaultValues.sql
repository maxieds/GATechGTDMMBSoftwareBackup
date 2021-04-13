-- InsertDefaultValues.sql : Inserts some defaults into the initial tables;
-- Author: Maxie D. Schmidt (maxieds@gmail.com)
-- Created: 2019.06.15

SOURCE CreateRNADB.sql;

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

