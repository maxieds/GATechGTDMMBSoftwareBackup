<?php
//// RNADBUtils.php
//// Author: Maxie D. Schmidt (maxieds@gmail.com), modified from existing sources
//// Created: 2019.06.18
?>

<?php

include_once "FrontendConfig.php";
include_once "DatabaseUtils.php";

function arraySearchPartialMatch($arr, $keyword) {
    foreach($arr as $idx => $astr) {
        if (strpos($astr, $keyword) !== FALSE)
            return $idx;
    }
    return false;
}

function getRanges($sequences) {
	$arr = array();
	$arr['seqLengthMax'] = 0;
	$arr['seqLengthMin'] = 10000;
	$arr['mfeAccuracyMax'] = 0;
	$arr['mfeAccuracyMin'] = 1;
	$arr['gccontentMax'] = 0;
	$arr['gccontentMin'] = 1;
	$arr['completenessMax'] = 0;
	$arr['completenessMin'] = 1;
	$arr['5S'] = 0;
	$arr['16S'] = 0;
	$arr['23S'] = 0;
	$arr['tRNA'] = 0;
	return $arr;
}

function getSequences($params) {
	$arr = getSequences_db($params, false);
	$arrOldData = getSequencesOld_db($params, true);
	$accInfoOldArr = array();
	for($aj = 0; $aj < count($arrOldData); $aj++) {
	     array_push($accInfoOldArr, $arrOldData[$aj]['orig_ct']);
	}
	error_log("Old Info Array: " . $accInfoOldArr[0]);
	for($ai = 0; $ai < count($arr); $ai++) {
	     $oldArrIdx = arraySearchPartialMatch($accInfoOldArr, $arr[$ai]['orig_ct']);
	     $arr[$ai]['mfeAcc_old'] = $oldArrIdx === false ? "N/A" : $arrOldData[$oldArrIdx]['acc'];
	}
	return $arr;
}

function getSequence($id) {
	return array("rid"=>$id , "family" => "5S",
			"organism" => "ecoli", "accession" => "ASDF123", "seqLength" => "135",
			"mfeAcc" => "91%", "completeness" => "73%",
			"gccontent" => "77%",
			"ambiguous" => 0 );
}

function getSequenceFilename($id, $arr, $files) {
	$dbCon = DatabaseUtils::ConnectToRNADB();
	$sqlQuery = "SELECT * FROM " . FrontendConfig::RNADB_SEQS_TBL . " WHERE rid=$id";
	$dbResult = DatabaseUtils::DeSQL($sqlQuery, $dbCon);
	$filenames = array();
	$csvContents = "";
	if($row = mysqli_fetch_array($dbResult)) {
		for($i = 0; $i < count($arr); $i++) {
			if($i > 0) {
			     $csvContents .= ",";
			}
			$csvContents .= $row[$arr[$i]];
		}
		$csvContents .= "\n";
		for($j = 0; $j < count($files); $j++) {
			array_push($filenames, filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . "/" . 
                                   FrontendConfig::DBRAW_DATA_URL . '/' . $row['family'] . '/' . 
                                   $row[$files[$j]]);
		}
	}
	DatabaseUtils::CloseRNADBConnection($dbCon);
	return array($csvContents, $filenames);
}

/* UPDATED--ER 6/4/18
	 Array
	(
			[paper] => rogers14,rogers16,rogers17,etc
			[family] => tRna,5S,16S,23S,etc
			[organism] => latin name
			[accession] => 0
			[lenmin] => 0
			[lenmax] => 3000
			[mfeaccmin] => 0
			[mfeaccmax] => 1000
			[gccontentmin] => 0
			[gccontentmax] => 1000
			[completenessmin] => 0
			[completenessmax] => 1000
			[ambiguous] => true
	)
*/
/* ROGERS'14: RIDs 30, 33, 189, 207, 215, 232, 242, 254; */
/* ROGERS'16: RIDs 55-64 (FMN); 
	   133, 134, 135, 139, 146, 148, 150, 152, 154, 156 (Intron gpII); 
	   181, 189, 190, 191, 192, 198, 199, 205, 207, 210 (THF);
	   264, 260, 268, 261, 259, 265, 272, 275, 278, 279 (U1);
	   284, 290, 291, 292, 293, 294, 299, 288, 289, 309 (U5);
	   315, 322, 323, 320, 321, 325, 328, 332, 334, 337 (UnaL2);
*/
/* ROGERS'17: RIDs 1-20 (16S); */
function getDbResults_db($dbCon, $params, $selectFromTableName = FrontendConfig::RNADB_SEQS_TBL, $size = -1) {

    //$dbCon = DatabaseUtils::ConnectToRNADB();
	$sqlQuery = "SELECT * FROM " . $selectFromTableName . " WHERE ";
	
	/*
	 * HANDLE PAPER parameters part of query: 
	 */	
	$params['paper'] = strtolower($params['paper']);
	$papersArr = explode(',', $params['paper']);
	$sum = count($papersArr);
	if(count($papersArr) > 0) {
		$sqlQuery .= '( ';
	}
	$paperAndOrQuery = "OR";
	error_log("TypeOf paperKeyAnd: " . gettype($params['paperKeyAnd']) . " VALUE: " . $params['paperKeyAnd']);
	error_log(print_r($params, true));
	if($params['paperKeyAnd'] === "true") {
	     $paperAndOrQuery = "AND";
	}
	
	$fullPapersList = getFullPapersList();
	foreach($fullPapersList as $paperKey) {
	     if(in_array($paperKey, $papersArr)) {
	          $paperKeyMatchStr = "'%$paperKey%'";
	          $sqlQuery .= FrontendConfig::RNADB_SEQS_TBL . ".papers LIKE " . 
	                       $paperKeyMatchStr . " ";
	          $sum--;
	          if($sum) {
	               $sqlQuery .= "$paperAndOrQuery ";
	          }
	     }
	}
	if(count($papersArr) > 0) {
		$sqlQuery .= " ) AND ";
	}
	
	/*
	 * HANDLE FAMILY part of query: 
	 */
	$familyArr = explode(',', $params['family']);
	$fullFamilyList = getFullFamilyList();
	$faCount = count($familyArr);
	if($faCount > 0) {
		$sqlQuery .= "( ";
	}
	foreach($familyArr as $fakey) {
		$addedToQuery = false;
		if(fuzzyStringMatchInArray($fakey, $fullFamilyList, 0.80, true)) {
		     $famSearchName = "'%" . $fakey . "%'";
		     $sqlQuery .= FrontendConfig::RNADB_SEQS_TBL . ".family LIKE " . $famSearchName . " ";
			 $addedToQuery = true;
		}
		$faCount--;
		if($faCount && $addedToQuery) {
			$sqlQuery .= "OR ";
		}
	}
	if(count($familyArr) > 0) { 
		$sqlQuery .= " ) AND ";
	}
	
	/*
	 * HANDLE organism/latin name part of query: 
	 */
	$organismSearchName = "'%" . mysqli_real_escape_string($dbCon, trim($params['organism'])) . "%'";
	$sqlQuery .= FrontendConfig::RNADB_SEQS_TBL . '.latin_name LIKE ' . $organismSearchName . ' AND ';
	
	/*
	 * HANDLE accession number part of query:
	 */
	if ($params['accession'] != '') { 
		$sqlQuery .= FrontendConfig::RNADB_SEQS_TBL . '.accession LIKE \'%' . 
		             mysqli_real_escape_string($dbCon, trim($params['accession'])) . '%\' AND ';
	}
	
	/*
	 * HANDLE AMBIGUOUS parameter part of query: 
	 */
	if(!$params['ambiguous']) {
		$sqlQuery .= FrontendConfig::RNADB_SEQS_TBL . '.ambiguous=0 AND ';
	}
	
	/*
	 * HANDLE SEQUENCE LENGTH part of query: 
	 */
	$sqlQuery .= '(' . FrontendConfig::RNADB_SEQS_TBL . '.length BETWEEN ' . $params['lenmin'];
	$sqlQuery .= ' AND ' . $params['lenmax'] . ') AND ';
	
	/*
	 * HANDLE RANGES part of query: 
	 */
	$sqlQuery .= '(f_measure BETWEEN ' . $params['mfeaccmin'];
	$sqlQuery .= ' AND ' . $params['mfeaccmax'] . ') AND ';
	$sqlQuery .= '(gc_content BETWEEN ' . $params['gccontentmin'];
	$sqlQuery .= ' AND ' . $params['gccontentmax'] . ') AND ';
	$sqlQuery .= '(completeness BETWEEN ';
	$sqlQuery .= $params['completenessmin'];
	$sqlQuery .= ' AND ' . $params['completenessmax'] . ')';
	$sqlQuery .= ';';

	$dbResult = DatabaseUtils::DeSQL($sqlQuery, $dbCon);
	error_log("SQL Query: '$sqlQuery'");
	return $dbResult;

}

function getDbResultsOld_db($dbCon, $prevSearchResults, $selectFromTableName, $size = -1) {

    //$dbCon = DatabaseUtils::ConnectToRNADB();
	$sqlQuery = "SELECT * FROM " . $selectFromTableName . ";";
	$dbResult = DatabaseUtils::DeSQL($sqlQuery, $dbCon);
	error_log("SQL Query (OLD-DB): '$sqlQuery'");
	return $dbResult;

}

function fuzzyStringMatchInArray($str, $matchesArr, $threshold = 0.85, $strnocase = true) {
     for($ai = 0; $ai < count($matchesArr); $ai++) {
          $compareStr = $strnocase ? strtolower($str) : $str;
          $arrayMatchStr = $strnocase ? strtolower($matchesArr[$ai]) : $matchesArr[$ai];
          similar_text($compareStr, $arrayMatchStr, $similarityPct);
          if($similarityPct >= $threshold) {
               return true;
          }
     }
     return false;
}

function getSize_db($params, $size = -1) {
	$dbCon = DatabaseUtils::ConnectToRNADB();
	$dbResults = getDbResults_db($dbCon, $params, $size);
	$size = mysqli_num_rows($dbResults);
	DatabaseUtils::CloseRNADBConnection($dbCon);
	return $size;
}

function getSequences_db($params, $oldWhichDB = false) {
	$dbCon = DatabaseUtils::ConnectToRNADB($oldWhichDB);
	if(!$dbCon) {
	     return NULL;
	}
	$selectTableName = $oldWhichDB ? FrontendConfig::RNADB_OLDSEQS_TBL : FrontendConfig::RNADB_SEQS_TBL;
	$dbResult = parseQueryResults(getDbResults_db($dbCon, $params, $selectTableName), true);
	DatabaseUtils::CloseRNADBConnection($dbCon);
	return $dbResult;
}

function getSequencesOld_db($params, $oldWhichDB = false) {
	$dbCon = DatabaseUtils::ConnectToRNADB($oldWhichDB);
	if(!$dbCon) {
	     return NULL;
	}
	$selectTableName = $oldWhichDB ? FrontendConfig::RNADB_OLDSEQS_TBL : FrontendConfig::RNADB_SEQS_TBL;
	$dbResult = parseQueryResults(getDbResultsOld_db($dbCon, $params, $selectTableName), false);
	DatabaseUtils::CloseRNADBConnection($dbCon);
	return $dbResult;
}

function parseQueryResults($dbResult, $parse = true) {
	$results = array();
	while($dbResult && $row = mysqli_fetch_array($dbResult, MYSQLI_ASSOC)) {
		array_push($results, parseJoinedRow($row));
    }
	return $results;
}

function parseJoinedRow($row) {
	$rnaProps = array();
	$rnaProps['rid'] = $row['rid'];
	$rnaProps['family'] = $row['family'];
	$rnaProps['organism'] = $row['latin_name'];
	$rnaProps['accession'] = $row['accession'];
	$rnaProps['papers'] = $row['papers'];
	$rnaProps['seqLength'] = $row['length'];
	$rnaProps['mfeAcc'] = $row['f_measure'];
	$rnaProps['gccontent'] = $row['gc_content'];
	$rnaProps['completeness'] = $row['completeness'];
	$rnaProps['ambiguous'] = $row['ambiguous'];	
	$rnaProps['fasta_txt'] = $row['fasta_txt'];
	$rnaProps['nop_ct'] = $row['nop_ct'];
	$rnaProps['notes'] = $row['notes'];
	$rnaProps['orig_ct'] = $row['orig_ct'];
	$rnaProps['acc'] = $row['f_measure'];
	return $rnaProps;
}

function abbreviatePapersField($papersList) {
     $paperKeys = explode(",", $papersList);
     $abbrevPaperField = "";
     foreach($paperKeys as $paperKey) {
          $paperPrefix = preg_replace("%([0-9]+)%", "", $paperKey);
          $paperAbbrev = strtoupper($paperKey[0]) . "-";
		  $nextSeqPaperKey = str_replace($paperPrefix, $paperAbbrev, $paperKey);
		  if(strcmp($abbrevPaperField, "")) {
		       $abbrevPaperField .= ",";
		  }
		  $abbrevPaperField .= $nextSeqPaperKey;
     }
     return $abbrevPaperField;
}

function expandPaperKeyField($paperKey) {
     $paperKeyAuthorStart = preg_replace("%^([a-zA-Z]+)%", "$1", $paperKey);
     $paperKeyAuthorStart[0] = strtoupper($paperKeyAuthorStart[0]);
     $paperKeyAuthorStart = preg_replace("([0-9]+)", "", $paperKeyAuthorStart);
     $paperKey = preg_replace("%^([a-zA-Z]+)%", "", $paperKey);
     $paperKey = preg_replace("%([0-9]+)%", " '$1", $paperKey);
     $paperKey = $paperKeyAuthorStart . $paperKey;
     return $paperKey;
}

function getPaperAssociatedSequences($paperKey) {
     $dbCon = DatabaseUtils::ConnectToRNADB();
     $sqlQuery = "SELECT * FROM " . FrontendConfig::RNADB_SEQS_TBL . 
                 " WHERE " . FrontendConfig::RNADB_SEQS_TBL . ".`papers` LIKE '" . 
                 $paperKey . "';";
     $dbResults = DatabaseUtils::DeSQL($sqlQuery, $dbCon);
     if(!$dbResults) {
          return array();
     }
     $seqsArr = array();
     while($dbSeqRow = mysqli_fetch_array($dbResults)) {
          array_push($seqsArr, $dbSeqRow);
     }
     DatabaseUtils::CloseRNADBConnection($dbCon);
     return $seqsArr;
}

function prepareHTMLMatchesList($seqs, $orgSearchSpec = '_') {
	 usort($seqs, function ($s1, $s2) {
          return $s1['organism'] <=> $s2['organism'];
	 });
     $listCode = "<ul style='overflow: auto; display:block; " . 
                 "list-style-type: decimal-leading-zero; " . 
		         "border-style: ridged; " . 
		         "font-family: monospace;' class='innerSearchResultsList'>\n";
	 for($s = 0; $s < count($seqs); $s++) {
	      $seqName = trim($seqs[$s]['organism']);
		  $seqFam = $seqs[$s]['family'];
		  $seqAccID = $seqs[$s]['accession'];
		  $seqPaper = abbreviatePapersField($seqs[$s]['papers']);
		  if(strlen($seqName) == 0 && strlen($seqAccID) == 0) {
			  continue;
	      }
		  if(strlen($seqAccID) > 12) { // truncate long strings:
			  $seqAccID = substr($seqAccID, 0, 9)."...";
		  }
		  $seqInfoStr = sprintf("(<i>%-4s</i>:%s)", $seqFam, $seqPaper);
		  $seqInfoStr = strlen($seqInfoStr) > 23 ? substr($seqInfoStr, 0, 19) . "...)" : $seqInfoStr;
		  $seqName = strlen($seqName) > 32 ? substr($seqName, 0, 29) . "..." : $seqName;
		  $seqLine = sprintf("<i>%-32s</i> %-23s  <b>%-16s</b>", 
							 $seqName, $seqInfoStr, $seqAccID);
		  $seqLine = "<li>".$seqLine."</li>\n";
		  if($orgSearchSpec != '_' && strlen($orgSearchSpec) >= 2) { 
			  // highlight the string:
			  $seqLine = preg_replace("/($orgSearchSpec)/i", 
						 "<span%style='background-color:#8AE234;font-weight: bold;'>$0</span>", 
						 $seqLine);
		  }
		  $seqLine = str_replace(" ", "&nbsp;", $seqLine);
		  $seqLine = str_replace("%", " ", $seqLine);
		  $listCode .= $seqLine;
	 }
	 $listCode .= "</ul>";
	 return $listCode;
}

function getFullPapersList() {
    $dbCon = DatabaseUtils::ConnectToRNADB();
	$sqlQuery = "SELECT * FROM " . FrontendConfig::RNADB_PAPERS_TBL . ";";
	$dbResults = DatabaseUtils::DeSQL($sqlQuery, $dbCon);
	$paperKeysList = array();
	while($dbResults && $paperRow = mysqli_fetch_array($dbResults)) {
	     array_push($paperKeysList, $paperRow['paper_key']);
	}
	DatabaseUtils::CloseRNADBConnection($dbCon);
	return $paperKeysList;
}

function getFullFamilyList() {
     $seqDBRawDataDir = filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . "/" . 
                        FrontendConfig::DBRAW_DATA_URL;
     $familyDirsList = scandir($seqDBRawDataDir);
     $familyList = array();
     foreach($familyDirsList as $key => $dir) {
          $dirEntry = $seqDBRawDataDir . "/" . $dir;
          if($dir === "." || $dir === "..") {
               continue;
          }
          else if(!is_dir($dirEntry)) {
               continue;
          }
          array_push($familyList, $dir);
     }
     return $familyList;
}

function GetRNADBSequenceCount() {
     $dbCon = DatabaseUtils::ConnectToRNADB();
     $sqlAllQuery = "SELECT * FROM " . FrontendConfig::RNADB_SEQS_TBL . ";";
     $seqsTableNumRows = mysqli_num_rows(DatabaseUtils::DeSQL($sqlAllQuery, $dbCon));
     DatabaseUtils::CloseRNADBConnection($dbCon);
     return $seqsTableNumRows;
}

?>
