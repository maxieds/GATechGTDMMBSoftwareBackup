<?php
//// Help.php
//// Author: Maxie D. Schmidt (maxieds@gmail.com), modified from existing sources
//// Created: 2019.06.18
?>

<?php
     session_start();
     // TODO: Check that user is logged in through CAS else redirect
?>

<html>
<head>
	<title>Georgia Institute of Technology RNA Database</title>
	<link type="text/css" href="css/smoothness/jquery-ui-1.8.20.custom.css" rel="stylesheet" />
	<link type="text/css" href="css/main.css" rel="stylesheet" />
	<script type="text/javascript" src="js/jquery-1.7.2.min.js"></script>
	<script type="text/javascript" src="js/jquery-ui-1.8.20.custom.min.js"></script>
	<script type="text/javascript">
		
	</script>
	<style type="text/css">
		.navDiv {
			float:left;
			width:180px;
			padding:15px;
		}
		.leftMain {
			float:left;
			width:800px;
			padding:10px;
			padding-left:25px;
		}
		.divLink { 
			text-align:center;
			font-weight: bold;
			background: #e0ffc7;
			border: 1px solid #000;
			width: 100%;
			padding: 15px;
			color: #000;
		}
		.topLink {
			margin-top: 50px;
			border-radius: 8px 8px 0px 0px;
		}
		.botLink {
			border-radius: 0px 0px 8px  8px;
		}
		.divLink:Hover {
			background: #a4d47d;
		}
		.formItem {
			margin-left: 50px;
		}
		.formHeader {
		 	font-weight: bold;
		 	padding-top: 25px;
		}
	</style>
</head>
<body>
	<div id="container">
		<!-- Tabs -->
		<div id="tabs">
		    <h1 class="pageHeader">
		    Georgia Institute of Technology RNA Database
		    </h1>
		    <br/>
			<ul>
				<li class="liInline"><a href="Search.php" onclick="window.location.href='Search.php'">Search</a></li>
				<li class="liInline"><a href="Papers.php" onclick="window.location.href='Papers.php'">Papers</a></li>
				<li class="ui-tabs-selected"><a href="#tabs-1">Help</a></li>
			</ul>
			<div id="tabs-1" style="height:auto;">
				<!-- Content-->
				<div id="sequenceGrid" class="leftMain">

					<h3>Sequence Data:</h3>

					This database contains the sequences used in research papers published by the 
					Heitsch Discrete Math and Molecular Biology Lab at Georgia Tech.
					Full paper/sequence information can be found 
					<a href="papers.php">here</a>.
					The tRNA, 5S, 16S and 23S ribosomal structures were obtained from the 
					<a href="http://www.rna.ccbb.utexas.edu/DAT/">Comparative RNA Website</a>,
					with the others generated from 
					<a href="http://rfam.xfam.org">Rfam</a>'s database
					(structures inferred from its alignments). Contact 
					<a href="mailto:gtdmmb@gatech.edu">gtdmmb@gatech.edu</a> to report any significant 
					issues with the sequence data in RNADB. 
					
					<h3>Contents of returned search and default download:</h3>
					In the CSV file:
					<ul>
						<li><b><i>RID</i></b>: the primary key indicating a unique row number</li>
						<li><b><i>Family</i></b>: Rfam family of the sequence, i.e. tRNA</li>
						<li><b><i>Organism</i></b>: the Latin name of the genus and species of the organism</li>
						<li><b><i>Accession</i></b>: GenBank accession number of sequence</li>
						<li><b><i>Length</i></b>: length in nucleotides of sequence</li>
						<li><b><i>MFE accuracy</i></b>: as calculated as the F-measure (see below)</li>
						<li><b><i>Completeness</i></b>: ratio of 'clean' structure bps to 'forced' structure bp (see below)</li>
						<li><b><i>GC Content</i></b>: percent of sequence that is either a 'G' or a 'C'</li>
						<li><b><i>Ambiguous</i></b>: "Yes" if the ct file contains ambiguous nucleotides, "No" otherwise</li>
					</ul>
					Files downloaded:
					<ul>
						<li><b><i>*_nop.ct</i></b> file</li>
						<li><b><i>*_fasta.txt</i></b> file</li>
					</ul>
					
					<h4>MFE Prediction Accuracy:</h4>
					A MFE structure was predicted for each sequence using the 
					<a href="https://www.tbi.univie.ac.at/RNA/">ViennaRNA</a> library.
					An F-Measure score was computed as the accuracy metric for each sequence comparing 
					the MFE predicted structure and the native structure.
					True positives (<i>TP</i>) are defined as predicted base pairs (i,j)
					which also exist in the native structure;
					false positives (<i>FP</i>) are falsely predicted basepairs which do not exist 
					in the native structure;
					and false negatives (<i>FN</i>) are base pairs in the native structure which 
					were not predicted.
					The accuracy is calculated as 
					2<i>TP</i>/(2<i>TP</i> + <i>FP + FN</i>).

					<h4>Completeness:</h4>
					A '<i>clean</i>' structure is generated from the original CT file,
					with isolated, pseudoknotted, noncanonical, and tight hairpin closing loops
					(i,j difference less than 4) removed. 
					This 'clean' structure is then used as a constraint set within the 
					<a href="https://www.tbi.univie.ac.at/RNA/">ViennaRNA</a> library, 
					which forces it to fold to the lowest energy structure 
					(called the '<i>forced</i>' structure) containing at least the '<i>clean</i>' structure.
					The Completeness ratio is the number of base pairs of the '<i>clean</i>' structure,
					divided by the number of base pairs of the '<i>forced</i>' structure.
					A high completeness ratio indicates the comparative structure is relatively complete,
					while a low ratio indicates a sparse native structure with a higher likelihood
					of false negative native base pairs.
					
					<h3>
						Contents of full download:
					</h3>
					A full download of all the information present in the database is possible after a search,
					by clicking on a '<i>maximal</i>' download button for either all sequences, or just the selected.
					A full download will download all stored files related to the sequence, and a CSV
					file with all fields in the database minus the stored file names.
					<br>
					In the CSV file (in addition to those listed above):
					<ul>
						<li><b><i>Acc Length</i></b>: the length of the sequence referenced by the accession number</li>
						<li><b><i>Seq start</i></b> the start location of the actual sequence within the potentially larger accession sequence</li>
						<li><b><i>Seq stop</i></b>: the stop location of the sequence within the accession sequence. If this is smaller than Seq_start, then the sequence is located is the reverse complement</li>
						<li><b><i>Initial fragment</i></b>: the first 30 nucleotides of the sequence, used for uniqueness sanity check</li>
						<li><b><i>Checksum</i></b>: a number whose value is determined by the sequence (a sanity check to ensure uniqueness of sequences in database)</li>
						<li><b><i>Orig_bp</i></b>: the number of base pairs in the original CT file</li>
						<li><b><i>Nop_bp</i></b>: the number of base pairs in the .nopct file</li>
						<li><b><i>Canon_bp</i></b>: the number of base pairs in the canon CT file</li>
						<li><b><i>Clean_bp</i></b>: the number of base pairs in the clean CT file</li>
						<li><b><i>MFE_bp</i></b>: the number of base pairs in the predicted MFE structure</li>
						<li><b><i>Forced_bp</i></b>: the number of base pairs when the MFE structure is constrained to have the clean structure</li>
						<li><b><i>Clean energy</i></b>: energy of the clean structure</li>
						<li><b><i>MFE energy</i></b>: energy of the MFE predicted structure</li>
						<li><b><i>Forced energy</i></b>: energy of the MFE structure constrained on clean structure</li>
						<li><b><i>TP</i></b>: number of true positives between the native clean structure and MFE</li>
						<li><b><i>FP</i></b>: number of false positives between the native clean structure and MFE</li>
						<li><b><i>FN</i></b>: number of false negatives between the native clean structure and MFE</li>
						<li><b><i>Precision</i></b>: TP/(TP + FP)</li>
						<li><b><i>Recall</i></b>: TP/(TP + FN)</li>
					</ul>
					Files downloaded, in addition to those above:
					<ul>
						<li><b><i>*_orig_ct</i></b>: the original downloaded ct file</li>
						<li><b><i>*_canon_ct</i></b>: the nop ct file with the noncanonical bps removed</li>
						<li><b><i>*_clean_ct</i></b>: the canon ct with the isolated and tight hairpin closing bps removed</li>
						<li><b><i>*_MFE_ct</i></b>: the ct file containing the predicted MFE pairings</li>
						<li><b><i>*_forced_ct</i></b>: the ct file containing the predicted MFE pairings constrained on the clean structure</li>
						<li><b><i>*_knots_txt</i></b>: list of pseudoknotted base pairs removed from original ct to generate nop.ct</li>
						<li><b><i>*_noncanon_txt</i></b>: list of noncanonical base pairs removed from nop.ct to form canon.ct</li>
						<li><b><i>*_isolated_txt</i></b>: list of isolated or tight hairpin closing base pairs removed from canon.ct to form clean.ct</li>
					</ul>
					<h5>
For a full description of database fields and derivation, see also <a href="../Docs/rnadb_file_info_orig.pdf">this PDF file</a>.
					</h5>

				</div>
			</div>
		</div>
	</div>
	<script type="text/javascript">
		$(document).ready(function() {
			$('#tabs').tabs();
		});
		$(window).load(function() {
			$('#tabs-1').append('<div id="bottomClearDiv" style="clear:both;" class="clear"></div>');
		});
	</script>
</body>
</html>
