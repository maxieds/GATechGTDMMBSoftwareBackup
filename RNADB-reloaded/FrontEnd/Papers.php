<?php
//// Papers.php
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

	<style type="text/css">
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
		.seqs {
			display: none;
		}
		.show-more-button {
			color: blue;
			font-size: small;
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
				<li class="ui-tabs-selected"><a href="#tabs-1">Papers</a></li>
				<li class="liInline"><a href="Help.php" onclick="window.location.href='Help.php'">Help</a></li>
			</ul>
			<div id="tabs-1" style="height:auto;">
				<!-- Content-->
				<div id="sequenceGrid" class="leftMain">
				
				<!-- Main Content START -->
					
					<h3>Papers of the Heitsch Lab, with their respective sequences:</h3>
					Sequence names are from the Gutell database for tRNA, 5S, 16S, and 23S sequences, and from Rfam for all other families.
					The family of the sequence is given in parentheses.
					<ul>
					
					<?php
					     include_once "FrontendConfig.php";
					     include_once "DatabaseUtils.php";
					     include_once "RNADBUtils.php";
					     $fullPaperKeyList = getFullPapersList();
					     sort($fullPaperKeyList);
					     foreach($fullPaperKeyList as $paperKey) {
					          $dbCon = DatabaseUtils::ConnectToRNADB();
					          $sqlQuery = "SELECT * FROM " . FrontendConfig::RNADB_PAPERS_TBL . 
					                      " WHERE paper_key='" . $paperKey . "';";
					          $dbResult = DatabaseUtils::DeSQL($sqlQuery, $dbCon);
					          if($dbResult) {
					               $dbResult = mysqli_fetch_array($dbResult);
					          }
					          else {
					               continue;
					          }
					          DatabaseUtils::CloseRNADBConnection($dbCon);
					          $paperTitle = $dbResult['title'];
					          $paperAuthors = $dbResult['authors'];
					          $paperLink = $dbResult['doi'];
					          $paperSeqs = getPaperAssociatedSequences($paperKey);
					          echo "<li><b>" . expandPaperKeyField($paperKey) . ":</b><br/>\n";
					          echo "<i>Sequences: </i>\n";
					          echo "<span id=\"$paperKey-show\" class=\"show-more-button\" itemref=\"0\" itemprop=\"$paperKey\">" . 
					               "Show sequences in paper</span><br/>\n";
					          echo "<div id=\"$paperKey-seqs\" class=\"seqs\">\n";
					          echo "   <ul>\n";
					          #echo "      <li>Dummy sequence...</li>\n";
					          foreach($paperSeqs as $seq) {
					               echo "      <li><i>" . $seq['latin_name'] . "</i> (" . $seq['family'] . ") <br/>" . 
					                    "      <span style=\"font-size:12;\"><b>Accession Number:</b> " . $seq['accession'] . 
					                    "      </span><br/>" . 
					                    "      <span style=\"font-size:12;\"><b>Initial Fragment:</b> " . 
					                    strtoupper($seq['initial_fragment']) . 
					                    "      </span></li>\n";
					          }
					          echo "   </ul>\n";
					          echo "</div>\n";
					          echo "<i>Paper link: </i>";
					          echo "   <a href=\"$paperLink\">$paperTitle</a><br/>\n";
					          echo "<i>Authors: </i>" . $paperAuthors;
					          echo "</li>\n\n";
					     }
					     
					?>
					
					</ul>
					
				<!-- Main Content END -->

				</div>
			</div>
		</div>
	</div>
	
		<script type="text/javascript">
		
		$(document).ready(function() {
			$('#tabs').tabs();
		});
		$('.show-more-button').each(function (index) {
		    $(this).click(function() {
			var seqsHandle = $(this).attr("itemprop") + "-seqs";
			if($(this).attr('itemref') == 0) {
				$(this).attr('itemref', 1);
				$(this).html('Collapse sequences list');
				document.getElementById(seqsHandle).style.display = 'block';
			} 
			else {
				$(this).attr('itemref', 0);
				$(this).html('Show sequences in paper');
				document.getElementById(seqsHandle).style.display = 'none';
			}
		  });
		});
		$(window).load(function() {
			$('#tabs-1').append('<div id="bottomClearDiv" style="clear:both;" class="clear"></div>');
		});

	    </script>

</body>
</html>
