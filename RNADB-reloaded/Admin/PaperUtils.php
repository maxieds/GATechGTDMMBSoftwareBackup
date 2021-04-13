<?php
//// PaperUtils.php : Utilities and help functions to parse and process papers in the database;
//// Author: Maxie D. Schmidt (maxieds@gmail.com)
//// Created: 2019.06.18
?>

<?php

class PaperDataStruct {

     private $paperKey = "";
     private $authors = "";
     private $title = "";
     private $pubData = "";
     private $date = "";
     private $doi = "";
     private $comments = "";
     private $history = "";
     
     public function PopulateFromRowData($paperRowData) {
          if($paperRowData == NULL) {
               return NULL;
          }
          $this->paperKey = $paperRowData['paper_key'];
          $this->authors = $paperRowData['authors'];
          $this->title = $paperRowData['title'];
          $this->pubData = $paperRowData['pub_data'];
          $this->date = $paperRowData['date'];
          $this->doi = $paperRowData['doi'];
          $this->comments = $paperRowData['comments'];
          $this->history = $paperRowData['history'];
          return $this;
     }
     
     public function GetPaperKey() {
          return $this->paperKey;
     }
     
     public function GetPaperAuthors() {
          return $this->authors;
     }
     
     public function GetPaperTitle() {
          return $this->title;
     }
     
     public function GetPaperPublicationData() {
          return $this->pubData;
     }
     
     public function GetPaperDate() {
          return $this->date;
     }
     
     public function GetPaperDOIInformation() {
          return $this->doi;
     }
     
     public function GetPaperComments() {
          return $this->comments;
     }
     
     public function GetPaperHistory() {
          return $this->history;
     }

};

include_once "Sessions.php";
include_once "BackendConfig.php";
include_once "DatabaseUtils.php";

class PaperUtils {

     public static function GetPaperDBRowData($paperKey) {
          if(!Sessions::CurrentUserIsPapersAdmin()) {
               return NULL;
          }
          $dbCon = DatabaseUtils::ConnectToRNADB();
          $sqlQuery = "SELECT * FROM " . BackendConfig::DB_PAPERS_TBL . 
                      " WHERE paper_key='" . $paperKey . "';";
          $paperRowData = DatabaseUtils::DeSQL($sqlQuery, $dbCon);
          $paperRowData = mysqli_fetch_assoc($paperRowData);
          DatabaseUtils::CloseRNADBConnection($dbCon);
          return $paperRowData;
     }

     public static function LookupPaperData($paperKey) {
          $paperRowData = PaperUtils::GetPaperDBRowData($paperKey);
          if($paperRowData == NULL) {
               return NULL;
          }
          $paperDataStruct = new PaperDataStruct;
          return $paperDataStruct->PopulateFromRowData($paperRowData);
     }
     
     public static function GetAssociatedSequences($paperKey) {
          return NULL; // TODO
     }

};

if(isset($_GET['getPaperJSONData'])) {
     Sessions::StartSessionTracking();
     if(Sessions::CurrentUserIsPapersAdmin()) {
        $paperRowData = PaperUtils::GetPaperDBRowData($_POST['paperKey']);
        print json_encode($paperRowData);
     }
}

?>
