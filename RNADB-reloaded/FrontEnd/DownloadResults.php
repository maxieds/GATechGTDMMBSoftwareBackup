<?php
//// Results.php
//// Author: Maxie D. Schmidt (maxieds@gmail.com), modified from existing sources
//// Created: 2019.06.18
?>

<?php

include_once 'RNADBApi.php';

session_start();

if(!isset($_POST['family'])) {
	header("Location: Search.php");
}

/*
Array
(
	[rid] => 0
    [family] => tRna,5S,16S,23S
	[organism] => 
	[accession] => 0
    [lenmin] => 0
    [lenmax] => 3000
    [mfeaccmin] => 0
    [mfeaccmax] => 1000
    [gccontent] => 0
    [gccontent] => 1000
    [completeness] => 0
    [completeness] => 1000
    [ambiguous] => true
)
 */

function getRnaHtml($rna) {
	$tableRowData = '<tr>' .
	                '   <td><input type="checkbox" rnaId=\"' . $rna['rid'] . '\" /></td>' . 
		            '   <td>' . $rna["rid"] . '</td>' . 
		            '   <td>' . $rna["family"] . '</td>' . 
	                '   <td>' . $rna["organism"] . '</td>' . 
                    '   <td>' . $rna["accession"] . '</td>' . 
		            '   <td>' . $rna["seqLength"] . '</td>' . 
		            '   <td>' . substr($rna["mfeAcc"], 0 , 5) . '</td>' . 
		            '   <td>' . substr($rna["mfeAcc_old"], 0 , 5) . '</td>' . 
	                '   <td>' . substr($rna["gccontent"], 0 , 5) . '</td>' .
		            '   <td>' . substr($rna["completeness"], 0 , 5) . '</td>' . 
		            '   <td>' . ( $rna["ambiguous"] ? "Yes" : "No" ) . '</td>' .
                    '   <td>' . $rna['notes'] . '</td>' . 
                    '</tr>';
     return $tableRowData;
}

function populateTable($searchParams) {
	$seqResultsArr = getSequences($searchParams);
	for($i = 0; $i < count($seqResultsArr); $i++) {
		echo getRnaHtml($seqResultsArr[$i]);
    }
}

?>

<html>
<head>
	<title>Georgia Institute of Technology RNA Database</title>
	<link type="text/css" href="css/smoothness/jquery-ui-1.8.20.custom.css" rel="stylesheet" />
	<link type="text/css" href="css/main.css" rel="stylesheet" />
	<script type="text/javascript" src="js/jquery-1.7.2.min.js"></script>
	<script type="text/javascript" src="js/jquery-ui-1.8.20.custom.min.js"></script>
	<script type="text/javascript">
	     var max = <?php echo getSize_db($_POST, 100000); ?>;
	     var searchParams = <?php echo json_encode($_POST); ?>;
	</script>
	<script type="text/javascript" 
		 <?php 
	     	$dateTimeNow = date_create();
			$dstamp = date_timestamp_get($dateTimeNow);
         	echo "src='js/rnaDB.js?donotcache=", $dstamp, "'";
		 ?>
	></script>
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
				<li class="liInline"><a href="#tabs-1">Search Results</a></li>
				<li class="liInline"><a href="Search.php" onclick="window.location.href='Search.php'">New Search</a></li>
				<li class="liInline"><a href="Papers.php" onclick="window.location.href='Papers.php'">Papers</a></li>
				<li class="liInline"><a href="Help.php" onclick="window.location.href='Help.php'">Help</a></li>
			</ul>
			<div id="tabs-1" style="height:auto;">
				<!-- Content-->
				<div id="downloadButtons" class="leftMain">
				    <h2>Download Sequence Data: </h2>
				    <p>
				          Download the selected sample sequence CT file data from RNADB using the UI buttons below. 
				          If you only require the sequence sample data for a subset of the search results from 
				          the previous page, download the zipped data associated with the <i>selected</i> 
				          files which can be included in the results by checking the corresponding row for 
				          each sequence search result below. See the <a href="Help.php">help link</a> for other 
				          questions related to the precise specifications of the four download options above.				          
				    </p>
					<table>
					   <tr>
					       <td><button onclick="downloadOut('allMinimal');" class="downloadButton">
					            Download All Sequences (Minimal CSV Data)</button></td>
					       <td><button onclick="downloadOut('selectedMinimal');" class="downloadButton"
					           >Download Selected in Table (Minimal CSV Data)</button></td>
					   </tr>
					   <tr>
					       <td><button onclick="downloadOut('allMaximal');" class="downloadButton">
					           Download All Sequences (Maximal CSV Data)</button></td>
					       <td><button onclick="downloadOut('selectedMaximal');" class="downloadButton">
					           Download Selected in Table (Maximal CSV Data)</button></td>
					   </tr>
					</table>
					<hr />
				    <p>The download links will appear below shortly after clicking on the corresponding 
				       download type button above: 
				    </p>
				    <div id="downloadLinks" class="leftMain">
					    &nbsp;
				    </div>
				    <div id="downloadLinksLoading" class="leftMain">
					    &nbsp;
				    </div>
				    <hr/>
				</div>
				<div id="sequenceGrid" class="leftMain">
					<div id="setSelectionLinks1" style="display: none;"></div>
					<h2>Selected Search Results:</h2>
					<p>
					Select only a subset of the sequence search results to download. 
					All sequences can be selected by checking the upper-leftmost 
					checkbox marked with the icon below. 
					</p>
					<table border="1px"; class="rnaTable" id="rnaTable">
						<tr>
							<th><span class="ui-icon ui-icon-arrow-4-diag"></span><input id="cbAll" type="checkbox"onclick="changeAllCheckboxes();" /></th>
							<th>RID</th>
							<th>Family</th>
							<th>Organism</th>
							<th>Accession</th>
							<th>Seq. Length</th>
							<th>MFE Acc.</th>
							<th>MFE Acc. (GTFold)</th>
							<th>GC Content</th>
							<th>Completeness</th>
							<th>Ambiguous</th>
							<th>Notes</th>
						</tr>
						<span id="resultsBody">
						<?php populateTable($_POST); ?>
						</span>
					</table>
					<div id="setSelectionLinks2" style="display: none;"></div>
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
			populateSetSelectionLinks();
		});
	</script>
</body>
</html>
