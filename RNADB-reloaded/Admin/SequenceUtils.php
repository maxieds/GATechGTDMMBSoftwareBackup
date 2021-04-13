<?php
//// SequenceUtils.php : Utilities and help functions to parse and process sequences in the database;
//// Author: Maxie D. Schmidt (maxieds@gmail.com)
//// Created: 2019.06.18
?>

<?php

include_once "BackendConfig.php";
include_once "DatabaseUtils.php";

function sortByRID($srow1, $srow2) {
     return $srow1['rid'] - $srow2['rid'];
}

function sortByLatinName($srow1, $srow2) {
     return strcmp($srow1['latin_name'], $srow2['latin_name']);
}

class SequenceUtils {

     public static function LookupAllSequences() {
          $dbCon = DatabaseUtils::ConnectToRNADB();
          $sqlQuery = "SELECT * FROM " .
                      BackendConfig::DB_SEQUENCES_TBL . ";";
          $seqRowData = DatabaseUtils::DeSQL($sqlQuery, $dbCon);
          $seqDataArr = array();
          while($row = mysqli_fetch_assoc($seqRowData)) {
               array_push($seqDataArr, $row);
          }
          DatabaseUtils::CloseRNADBConnection($dbCon);
          usort($seqDataArr, 'sortByLatinName');
          return $seqDataArr;
     }

     public static function LookupSequenceData($dbField, $fieldValue, $quoteValue = false, $printErrors = false) {
          $dbCon = DatabaseUtils::ConnectToRNADB();
          $quote = $quoteValue ? "'" : "";
          $sqlQuery = "SELECT * FROM " .
                      BackendConfig::DB_SEQUENCES_TBL . 
                      " WHERE " . $dbField . "=" . $quote . $fieldValue . $quote . ";";
          $seqRowData = DatabaseUtils::DeSQL($sqlQuery, $dbCon);
          if($seqRowData) {
               $seqRowData = mysqli_fetch_assoc($seqRowData);
          }
          else if($printErrors) {
               SequenceUtils::PrintHTMLError("Unable to fetch $dbField='$fieldValue': " . mysqli_error($dbCon));
          }
          DatabaseUtils::CloseRNADBConnection($dbCon);
          return $seqRowData;
     }
     
     public static function GetFileDirectory($filename) {
          return pathinfo($filename, PATHINFO_DIRNAME);
     }
     
     public static function GetFileName($filename) {
          return pathinfo($filename, PATHINFO_BASENAME);
     }
     
     public static function GetFileBaseName($filename) {
          return pathinfo($filename, PATHINFO_FILENAME);
     }
     
     public static function GetFileExtension($filename) {
          return pathinfo($filename, PATHINFO_EXTENSION);
     }
     
     public static function GetTempDirectory() {
          return sys_get_temp_dir();
     }
     
     public static function GetTempFilename($prefix = '') {
          return tempnam(SequenceUtils::GetTempDirectory(), $prefix);
     }
     
     public static function FileExists($filePath) {
          return file_exists($filePath);
     }
     
     public static function MoveFile($srcPath, $destPath, $overwrite = true) {
          if(!$overwrite && file_exists($destPath)) {
               return false;
          }
          else if($overwrite && file_exists($destPath)) {
               unlink($destPath);
          }
          return copy($srcPath, $destPath);
     }
     
     public static function UnzipArchive($zipFilePath, $destDir) {
         $zipArchive = new ZipArchive;
         $openStatus = $zipArchive->open($zipFilePath);
         if($openStatus) {
              $openStatus = $openStatus && $zipArchive->extractTo($destDir);
              $zipArchive->close();
         }
         return $openStatus;
     }
     
     public static function GetFileUploadErrorMsg($ecode) {
          switch($ecode) {
               case UPLOAD_ERR_OK:
                    return "No error uploading file. ";
               case UPLOAD_ERR_INI_SIZE: 
                    return "The uploaded file exceeds the upload_max_filesize directive in php.ini. ";
               case UPLOAD_ERR_FORM_SIZE: 
                    return "The uploaded file exceeds the MAX_FILE_SIZE directive specified in the HTML form. "; 
               case UPLOAD_ERR_PARTIAL: 
                    return "The uploaded file was only partially uploaded. "; 
               case UPLOAD_ERR_NO_FILE: 
                    return "No file was uploaded. "; 
               case UPLOAD_ERR_NO_TMP_DIR: 
                    return "Missing a temporary folder. "; 
               case UPLOAD_ERR_CANT_WRITE: 
                    return "Failed to write file to disk. "; 
               case UPLOAD_ERR_EXTENSION: 
                    return "File upload stopped by extension. "; 
               default: 
                    return "Unknown upload error. "; 
        } 
        return "???";
     }
     
     public static function PrintHTMLError($errorMsg) {
          echo "<span style=\"color: #ff0000\"><b>" . $errorMsg . "</b></span>\n<br/>\n";
     }
     
     public static function GetDBUpdateColumnParamsLine($columnsStr, $valuesStr, $delim, $quote = false) {
          $columnFields = explode($delim, $columnsStr);
          $valueFields = explode($delim, $valuesStr);
          if(count($columnFields) != count($valueFields)) {
              error_log("In GetDBUpdateColumnParamsLine: count(columnFields) != count(valueFields) ... " . 
                        count($columnFields) . " != " . count($valueFields));
              return "";
          }
          $quoteStr = $quote ? "'" : "";
          $concatStr = "";
          for($fld = 0; $fld < count($columnFields); $fld++) {
               if($fld > 0) {
                    $concatStr .= ",";
               }
               $concatStr .= $columnFields[$fld] . "=" . $quoteStr . $valueFields[$fld] . $quoteStr;
               $concatStr = str_replace(";", ",", $concatStr);
          }
          return $concatStr;
     }
     
     public static function ProcessSequenceCSVFile($csvFilePath, $optUpdateExisting, $printErrors = true) {
          if(!file_exists($csvFilePath) && $printErrors) {
               SequenceUtils::PrintHTMLError("CSV file path \"" . $csvFilePath . "\" does not exist on server! ");
               return false;
          }
          else if(!file_exists($csvFilePath)) {
               return false;
          }
          $csvFileHandle = @fopen($csvFilePath, "r");
          if(!$csvFileHandle) {
               if($printErrors) {
                    SequenceUtils::PrintHTMLError("Unable to open CSV file \"" . $csvFilePath . "\"! ");
               }
               return false;
          }
          $csvLine = substr(fgets($csvFileHandle, 4096), 0, -1);
          $csvHeaderLine = str_replace("rid,", "", str_replace("'", "", $csvLine));
          $errorsEncountered = false;
          $clineIdx = 0;
          while($csvLine && !feof($csvFileHandle)) {
               $clineIdx++;
               $csvLine = substr($csvLine, strpos($csvLine, ",") + 1); // remove the script-generated RID field
               $csvLine = substr(fgets($csvFileHandle, 4096), 0, -1);
               $csvLine = str_replace("'NULL'", "0", substr(strstr($csvLine, ","), 1));
               if($csvLine === "") {
                    break;
               }
               // check if the accession number already exists:
               $seqChecksum = explode(",", $csvLine)[11];
               if($seqChecksum[0] == "'" || $seqChecksum[0] == "\"") {
                    $seqChecksum = substr($seqChecksum, 1);
               }
               $seqChecksumLastPos = strlen($seqChecksum) - 1;
               if($seqChecksum[$seqChecksumLastPos] == "'" || $seqChecksum[$seqChecksumLastPos] == "\"") {
                    $seqChecksum = substr($seqChecksum, 0, -1);
               }
               $sqlQuery = "";
               if(SequenceUtils::LookupSequenceData('seq_checksum', $seqChecksum, true, true) != NULL) {
                    if(!$optUpdateExisting) {
                         if($printErrors) {
                              SequenceUtils::PrintHTMLError("Sequence checksum " . $seqChecksum . " on line #" . 
                                                            $clineIdx . " already exists! ");
                         }
                         $errorsEncountered = true;
                    }
                    // modify this existing sequence in the db:
                    $sqlQuery = "UPDATE " . BackendConfig::DB_SEQUENCES_TBL . " SET " . 
                                SequenceUtils::GetDBUpdateColumnParamsLine($csvHeaderLine, $csvLine, ",") . 
                                " WHERE seq_checksum='" . $seqChecksum . "';";
               }
               else {
                    // insert the new sequence into the db: 
                    $sqlQuery = "INSERT INTO " . BackendConfig::DB_SEQUENCES_TBL . 
                                "(" . $csvHeaderLine . ") VALUES (" . $csvLine . ");";
               }
               $dbCon = DatabaseUtils::ConnectToRNADB();
               $dbOpResult = DatabaseUtils::DeSQL($sqlQuery, $dbCon);
               if(!$dbOpResult && $printErrors) {
                    SequenceUtils::PrintHTMLError("Error with sequence on line #" . $clineIdx . ": " . 
                                                  mysqli_error($dbCon));
                    $errorsEncountered = true;
               }
               else if($dbOpResult) {
                    $errorsEncountered = true;
               }
               DatabaseUtils::CloseRNADBConnection($dbCon);
          }
          fclose($csvFileHandle);
          return $errorsEncountered;
     }
     
     public static function ProcessSequenceZipFiles($zipFilePath, $optOverwriteDBFiles, $printErrors = true) {
          $tempExtractDir = SequenceUtils::GetTempFilename(SequenceUtils::GetFileBaseName($zipFilePath));
          if(file_exists($tempExtractDir)) {
               unlink($tempExtractDir);
          }
          mkdir($tempExtractDir);
          if(!SequenceUtils::UnzipArchive($zipFilePath, $tempExtractDir)) {
               if($printErrors) {
                    SequenceUtils::PrintHTMLError("Unable to extract zip file \"" . $zipFilePath . "\"! ");
               }
               unlink($tempExtractDir);
               return false;
          }
          $zippedFilesFamilyDirs = scandir($tempExtractDir);
          $processingError = false;
          for($d = 0; $d < count($zippedFilesFamilyDirs); $d++) {
               if(!is_dir($tempExtractDir . "/" . $zippedFilesFamilyDirs[$d]) || 
                  $zippedFilesFamilyDirs[$d] === "." || 
                  $zippedFilesFamilyDirs[$d] === "..") {
                    continue;
               }
               $family = SequenceUtils::GetFileBaseName($zippedFilesFamilyDirs[$d]);
               $fileSrcDir = $tempExtractDir . "/" . $family;
               $fileDestDir = filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . "/" . 
                              BackendConfig::DBRAW_DATA_URL . "/" . $family;
               if(!file_exists($fileDestDir)) {
                    mkdir($fileDestDir, 0755, true);
               }
               $familySubfilesList = scandir($fileSrcDir);
               for($f = 0; $f < count($familySubfilesList); $f++) {
                    $filename = SequenceUtils::GetFileName($familySubfilesList[$f]);
                    if($filename === "." || $filename === "..") {
                         continue;
                    }
                    $srcFile = $fileSrcDir . "/" . $filename;
                    $destFile = $fileDestDir . "/" . $filename;
                    if(file_exists($destFile) && $optOverwriteDBFiles || !file_exists($destFile)) {
                         if(!SequenceUtils::MoveFile($srcFile, $destFile, $optOverwriteDBFiles)) {
                              $errorMsg = "Unable to move \"" . $srcFile . "\" to \"" . $destFile . "\"! ";
                              if($printErrors) {
                                   SequenceUtils::PrintHTMLError($errorMsg);
                              }
                              $processingError = true;
                         }
                    }
                    else if ($optOverwriteDBFiles) {
                         if($printErrors) {
                              SequenceUtils::PrintHTMLError("Unable to copy \"" . $srcFile . "\" to \"" . $destFile . "\"! ");
                         }
                         $processingError = true;
                    }
               }
          }
          return $processingError;
     }
     
     public static function GetFullPapersList() {
          $dbCon = DatabaseUtils::ConnectToRNADB();
	      $sqlQuery = "SELECT * FROM " . BackendConfig::DB_PAPERS_TBL . ";";
	      $dbResults = DatabaseUtils::DeSQL($sqlQuery, $dbCon);
	      $paperKeysList = array();
	      while($dbResults && $paperRow = mysqli_fetch_array($dbResults)) {
	           array_push($paperKeysList, $paperRow['paper_key']);
	      }
	      return $paperKeysList;
     }
     
     public static function ExpandPaperKeyField($paperKey) {
          $paperKeyAuthorStart = preg_replace("%^([a-zA-Z]+)%", "$1", $paperKey);
          $paperKeyAuthorStart[0] = strtoupper($paperKeyAuthorStart[0]);
          $paperKeyAuthorStart = preg_replace("([0-9]+)", "", $paperKeyAuthorStart);
          $paperKeyExp = preg_replace("%^([a-zA-Z]+)%", "", $paperKey);
          $paperKeyExp = preg_replace("%([0-9]+)%", " '$1", $paperKeyExp);
          $paperKeyExp = $paperKeyAuthorStart . $paperKeyExp;
          return $paperKeyExp;
     }
     
}

if(isset($_GET['getSequenceJSONData'])) {
     include_once "Sessions.php";
     Sessions::StartSessionTracking();
     if(Sessions::CurrentUserIsSequencesAdmin()) {
        $sequenceRowData = SequenceUtils::LookupSequenceData('rid', $_POST['sequenceRID']);
        print json_encode($sequenceRowData);
     }
}

?>
