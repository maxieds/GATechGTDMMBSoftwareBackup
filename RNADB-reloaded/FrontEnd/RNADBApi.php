<?php
//// RNADBApi.php : Defines the interface to our front end (from searching to download options we must process);
//// Author: Maxie D. Schmidt (maxieds@gmail.com), modified from existing sources
//// Created: 2019.06.18
?>

<?php

include_once "FrontendConfig.php";
include_once "DatabaseUtils.php";
include_once "RNADBUtils.php";
include_once "FileUtils.php";

if (isset($_GET[FrontendConfig::RNADB_API_SEARCH])) {
	$arr = array();
	$arr["rows"] = getSequences($_POST);
	$arr["offset"] = $_POST["offset"];
	echo json_encode($arr);
}
else if(isset($_GET[FrontendConfig::RNADB_API_GETSIZE])) {
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
	// set the other parameters in the search:
	$_POST['lenmin'] = $_POST['seqLength'][0];
	$_POST['lenmax'] = $_POST['seqLength'][1];
	$_POST['mfeaccmin'] = $_POST['mfeAccuracy'][0];
	$_POST['mfeaccmax'] = $_POST['mfeAccuracy'][1];
	$_POST['gccontentmin'] = $_POST['gccontent'][0];
	$_POST['gccontentmax'] = $_POST['gccontent'][1];
	$_POST['completenessmin'] = $_POST['completeness'][0];
	$_POST['completenessmax'] = $_POST['completeness'][1];
	$_POST['size'] = 100000;
	$seqs = getSequences($_POST);
	$arr = array();
	$arr["seqsArray"] = prepareHTMLMatchesList($seqs, $_POST['organism']);
	$arr["setId"] = $_POST['sizeId']; 
	$arr["setSize"] = count($seqs); //$size;
	$arr["startTime"] = $_POST['startTime'];
	$arr["ranges"] = getRanges($seqs);
	echo json_encode($arr);
}
else if (isset($_GET[FrontendConfig::RNADB_API_DOWNLOAD_MINIMAL])) {
	$arr = explode(",", $_POST['selected']);
	if (count($arr) == 1 && $arr[0] === "") {
		echo json_encode(array("error" => "No files are selected."));
	} 
	else {
		$filenames = array();
		$fileTypes = array('fasta_txt',
		                   'nop_ct',
		                  );
		$csvFileName = filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . "/" . 
                       FrontendConfig::DOWNLOADS_URL . '/search-results-full.csv';
		$csvDest = fopen($csvFileName, "w") or exit("Cannot open CSV file \"$csvFileName\"\n");
		$fields = "rid,family,latin_name,accession,length,acc_length,gc_content,completeness,ambiguous,notes";
		fwrite($csvDest, $fields . "\n");
		$fieldsArr = explode(',', $fields);
		for($i = 0; $i < count($arr); $i++) {
			list($csvRow, $fnames) = getSequenceFilename($arr[$i], $fieldsArr, $fileTypes);
			for ($j = 0; $j < count($fileTypes); $j++) {
				array_push($filenames, $fnames[$j]);
			} 
			fwrite($csvDest, $csvRow);
		}
		fclose($csvDest);
		array_push($filenames, $csvFileName);
		$zipFileLoc = FileUtils::CreateZipFile($filenames, "MinimalSequences");
		$jsonDataArr = array("link" => $zipFileLoc);
		if($zipFileLoc.substr(0, 3) === "NULL") {
		     $jsonDataArr["error"] = "NULL zip file path!";
		}
		else {
		     $jsonDataArr["error"] = "";
		}
		echo json_encode($jsonDataArr);
	}
}
else if (isset($_GET[FrontendConfig::RNADB_API_DOWNLOAD_MAXIMAL])) {
	$arr = explode(",", $_POST['selected']);
	if (count($arr) == 1 && $arr[0] === "") {
		echo json_encode(array("error" => "No files are selected."));
	} 
	else {
		$filenames = array();
		$fileTypes = array('fasta_txt',
		                   'nop_ct',
		                   'orig_ct',
		                   'canon_ct',
		                   'clean_ct',
		                   'mfe_ct',
		                   'forced_ct',
		                   'knots_txt',
		                   'noncanonical_txt',
		                   'isolated_txt'
		                  );
		$csvFileName = filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . "/" . 
                       FrontendConfig::DOWNLOADS_URL . '/search-results-full.csv';
		$csvDest = fopen($csvFileName, "w") or exit("Cannot open CSV file \"$csvFileName\"\n");
		$fields = "rid,family,latin_name,accession,length," . 
		          "acc_length,seq_start,seq_stop,gc_content," . 
		          "initial_fragment,seq_checksum,orig_bp,nop_bp,canon_bp," . 
		          "clean_bp,mfe_bp,forced_bp,clean_energy,mfe_energy,forced_energy," . 
		          "completeness,tp,fp,fn,precision_val,recall,f_measure,ambiguous,notes";
		fwrite($csvDest, $fields . "\n");
		$fieldsArr = explode(',', $fields);
		for($i = 0; $i < count($arr); $i++) {
			list($csvRow, $fnames) = getSequenceFilename($arr[$i], $fieldsArr, $fileTypes);
			for ($j = 0; $j < count($fileTypes); $j++) {
			    if($fnames[$j] !== '') {
				    array_push($filenames, $fnames[$j]);
				}
			} 
			fwrite($csvDest, $csvRow);
		}
		fclose($csvDest);
		array_push($filenames, $csvFileName);
        $zipFileLoc = FileUtils::CreateZipFile($filenames, "MaximalSequences");
		$jsonDataArr = array("link" => $zipFileLoc);
		if($zipFileLoc.substr(0, 3) == "NULL") {
		     $jsonDataArr["error"] = "NULL zip file path!";
		}
		else {
		     $jsonDataArr["error"] = "";
		}
		echo json_encode($jsonDataArr);
	}
}

?>
