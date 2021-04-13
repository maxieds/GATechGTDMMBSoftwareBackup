<?php
//// Login.php : Manage sequences in the database;
//// Author: Maxie D. Schmidt (maxieds@gmail.com)
//// Created: 2019.06.15
?>

<?php

include_once "Sessions.php";
include_once "LoginUtils.php";

Sessions::StartSessionTracking();
if(!Sessions::VerifyLogin() || !Sessions::CurrentUserIsPapersAdmin()) {
     header("Location: Login.php");
     exit();
}

?>

<html>
<head>
	<title>gtDMMB RNADB Active Papers Administration</title>
	<link type="text/css" href="css/main.css" rel="stylesheet" />
	<link type="text/css" href="css/smoothness/jquery-ui-tabs.css" rel="stylesheet" />
	<script type="text/javascript" src="js/jquery-1.7.2.min.js"></script>
	<script type="text/javascript" src="js/jquery-ui-1.8.20.custom.min.js"></script>
</head>
<body>

<?php

include_once "BackendConfig.php";
include_once "LoginUtils.php";
include_once "DatabaseUtils.php";
include_once "PaperUtils.php";

include      "NavigationBar.php";
PrintNavBarHTML(PAPERS_ADMIN_PAGE);

?>

<h1>Active Papers Administration</h1>

<p>
This page is used for administrators in the <i>papers</i> group to modify active papers and their 
attributes in the papers database. The papers in this database may be referenced by paper key in the 
sequences database. For a listing of the active papers in the frontend RNADB website see 
<a href="https://rnadb.gatech.edu/FrontEnd/Papers.php">this link</a>. 
</p>

<?php

$paperKey = "";
$authors = "";
$title = "";
$pubData = "";
$date = "";
$doi = "";
$comments = "";
$historyMod = "";
$operation = "None";

if(isset($_POST['DisplayFormOperation'])) {

     // Get the submitted form input values:
     $paperKey = $_POST['DisplayFormPaperKey'];
     $authors = $_POST['DisplayFormPaperAuthors'];
     $title = $_POST['DisplayFormPaperTitle'];
     $pubData = $_POST['DisplayFormPaperPubData'];
     $date = $_POST['DisplayFormPaperDate'];
     $doi = $_POST['DisplayFormPaperDOIInfo'];
     $comments = $_POST['DisplayFormPaperComments'];
     $operation = $_POST['DisplayFormOperation'];

     $paperDataStruct = PaperUtils::LookupPaperData($paperKey);
     $historyMod = "";
     if($paperDataStruct != NULL) {
          $historyMod = $paperDataStruct->GetPaperHistory();
          if(strcmp($historyMod, "")) {
               $historyMod .= "\n";
          }
     }
     $historyMod .=  "Paper $operation" . "-ed by " . 
                     Sessions::GetCurrentUserName() . " on " . 
                     strftime("%Y.%m.%d at %R");

     // Now do error basic checking:
     $processingError = false;
     $errorMsg = "";
     if($operation === "Create" && $paperDataStruct != NULL) {
          $processingError = true;
          $errorMsg .= "Cannot create paper with an existing paper key! ";
     }
     else if($operation === "Create" && !strcmp($paperKey, "")) {
          $processingError = true;
          $errorMsg .= "Insufficient data supplied: need a new paper key to use as index! ";
     }
     else if($operation === "Create" && !strcmp($title, "") && !strcmp($authors, "") && 
             !strcmp($pubData, "") && !strcmp($date, "") && !strcmp($doi, "") && 
             !strcmp($comments, "")) {
          $processingError = true;
          $errorMsg .= "Insufficient metadata associated with paper supplied: all metadata is blank! ";
     }
     else if($operation === "Modify" && $paperDataStruct == NULL) {
          $processingError = true;
          $errorMsg .= "Cannot modify non-existent paper key! ";
     }
     else if($operation === "Delete" && $paperDataStruct == NULL) {
          $processingError = true;
          $errorMsg .= "Cannot delete non-existent paper key! ";
     }
     
     // Decision tree:
     if($processingError) {
          echo '<span style="color: #ff0000"><b>' . $errorMsg . "</b></span>\n<br/>\n";
     }
     else if($operation === "Create") {
          $dbCon = DatabaseUtils::ConnectToRNADB();
          $sqlOp = "INSERT INTO " . BackendConfig::DB_PAPERS_TBL . 
                   " (paper_key,authors,title,pub_data,date,doi,comments,history) VALUES " . 
                   "('$paperKey','$authors','$title','$pubData','$date','$doi','$comments','$historyMod');";
          $dbOpResult = DatabaseUtils::DeSQL($sqlOp, $dbCon);
          if($dbOpResult) {
               echo '<span>' . "New paper <i>$paperKey</i> created successfully!" . "</span><br/>\n\n";
          }
          else {
               echo "<span style=\"color: #ff0000\">Error creating new paper <i>$paperKey</i> " . 
                    "with SQL query ='$sqlOp': " . "<br/>" . mysqli_error($dbCon) . "</span><br/>\n\n";
          }
          DatabaseUtils::CloseRNADBConnection($dbCon);
     }
     else if($operation === "Modify") {
          $dbCon = DatabaseUtils::ConnectToRNADB();
          $sqlOp = "UPDATE " . BackendConfig::DB_PAPERS_TBL . 
                   " SET paper_key='$paperKey'," . 
                   "authors='$authors',title='$title',pub_data='$pubData'," . 
                   "date='$date',doi='$doi',comments='$comments',history='$historyMod'" .  
                   "WHERE paper_key='$paperKey';";
          $dbOpResult = DatabaseUtils::DeSQL($sqlOp, $dbCon);
          if($dbOpResult) {
               echo '<span>' . "New paper <i>$paperKey</i> modified successfully!" . "</span><br/>\n\n";
          }
          else {
               echo "<span style=\"color: #ff0000\">Error modifying paper <i>$paperKey</i> " . 
                    "with SQL query ='$sqlOp': " . "<br/>" . mysqli_error($dbCon) . "</span><br/>\n\n";
          }
          DatabaseUtils::CloseRNADBConnection($dbCon);
     }
     else if($operation === "Delete") {
          $dbCon = DatabaseUtils::ConnectToRNADB();
          $sqlOp = "DELETE FROM " . BackendConfig::DB_PAPERS_TBL . 
                   " WHERE paper_key='$paperKey';";
          $dbOpResult = DatabaseUtils::DeSQL($sqlOp, $dbCon);
          if($dbOpResult) {
               echo '<span>' . "Paper <i>$paperKey</i> deleted successfully!" . "</span><br/>\n\n";
          }
          else {
               echo "<span style=\"color: #ff0000\">Error deleting paper <i>$paperKey</i> " . 
                    "with SQL query ='$sqlOp': " . "<br/>" . mysqli_error($dbCon) . "</span><br/>\n\n";
          }
          DatabaseUtils::CloseRNADBConnection($dbCon);
     }
     
     include_once "BackendConfig.php";
     if(BackendConfig::DEBUGGING_ON) { 
          echo "<br/>\n\n<b>Debugging Information:</b> <br/>";
          echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Paper Key Input: " . $paperKey . "<br/>\n";
          echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Paper Authors Input: " . $authors . "<br/>\n";
          echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Paper Title Input: " . $title . "<br/>\n";
          echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Paper Publication Data Input: " . $pubData . "<br/>\n";
          echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Paper Date Input: " . $date . "<br/>\n";
          echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Paper DOI Input: " . $doi . "<br/>\n";
          echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Paper Comments Input: " . $comments . "<br/>\n";
          echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Operation Input: " . $operation . "<br/>\n";
     }

}

?>

<script type="text/javascript">

function FetchPaperData(paperKey) {
     var jsonUserData = {};
     jsonUserData.paperKey = paperKey;
     $.ajax({
		type: 'POST',
		url: "PaperUtils.php?getPaperJSONData",
		data: jsonUserData,
		success: DisplayPaperDataInForm
	 });
}

function DisplayPaperDataInForm(phpJSONData) {
     //console.log(phpJSONData);     
     var jsonObj = JSON.parse(phpJSONData);
     $("#DisplayFormPaperKey").prop("value", jsonObj.paper_key);
     $("#DisplayFormPaperAuthors").prop("value", jsonObj.authors);
     $("#DisplayFormPaperTitle").prop("value", jsonObj.title);
     $("#DisplayFormPaperPubData").prop("value", jsonObj.pub_data);
     $("#DisplayFormPaperDate").prop("value", jsonObj.date);
     $("#DisplayFormPaperDOIInfo").prop("value", jsonObj.doi);
     $("#DisplayFormPaperComments").prop("value", jsonObj.comments);
     $("#DisplayFormPaperHistory").prop("value", jsonObj.history);
     $("#DisplayFormOperation").prop("selectedIndex", "0");
}

function ResetPaperDataForm() {
    $("#DisplayFormPaperKey").prop("value", "");
    $("#DisplayFormPaperAuthors").prop("value", "");
    $("#DisplayFormPaperTitle").prop("value", "");
    $("#DisplayFormPaperDate").prop("value", "");
    $("#DisplayFormPaperDOIInfo").prop("value", "");
    $("#DisplayFormPaperPubData").prop("value", "");
    $("#DisplayFormPaperComments").prop("value", "");
    $("#DisplayFormPaperHistory").prop("value", "");
}

</script>

<h2>Active Papers List:</h2>

<table style="width:90%; text-align: center;" align="center">
  <tr>
    <th>Paper Key</th>
    <th>Title</th> 
    <th>Operations</th>
  </tr>

<?php

include_once "Sessions.php";
include_once "DatabaseUtils.php";

$dbCon = DatabaseUtils::ConnectToRNADB();
$userRowData = NULL;

if(Sessions::CurrentUserIsPapersAdmin()) {
    $userSQLQuery = "SELECT * FROM " . BackendConfig::DB_PAPERS_TBL . ";";
    $userRowData = DatabaseUtils::DeSQL($userSQLQuery, $dbCon);
}
while($userRowData && $row = mysqli_fetch_assoc($userRowData)) {
     echo "<tr>\n";
     echo "     <td>" . $row['paper_key'] . "</td>\n";
     echo "     <td>" . $row['title'] . "</td>\n";
     echo '     <td><button class="DisplayPaperButton" value="' . $row['paper_key'] . 
          "\" onclick=\"FetchPaperData('" . $row['paper_key'] . "')\">Display Paper</button></td>\n";
     echo "</tr>\n";
}
DatabaseUtils::CloseRNADBConnection($dbCon);

?>

</table>

<h2>Create / Modify / Delete Paper Form:</h2>

<form action="ManagePapers.php" method="post">
   <table>
     <tr>
        <td><b>Unique Paper Key: </b></td>
        <td><input type="text" size="100" name="DisplayFormPaperKey" id="DisplayFormPaperKey" /></td>
     </tr>
     <tr>
        <td><b>Authors List: </b></td>
        <td><input type="text" size="100" name="DisplayFormPaperAuthors" id="DisplayFormPaperAuthors" /></td>
     </tr>
     <tr>
        <td><b>Title: </b></td>
        <td><input type="text" size="100" name="DisplayFormPaperTitle" id="DisplayFormPaperTitle" /></td>
     </tr>
     <tr>
        <td><b>Date: </b></td>
        <td><input type="text" size="100" name="DisplayFormPaperDate" id="DisplayFormPaperDate" /></td>
     </tr>
     <tr>
        <td><b>DOI Link (URL): </b></td>
        <td><input type="text" size="100" name="DisplayFormPaperDOIInfo" id="DisplayFormPaperDOIInfo" /></td>
     </tr>
     <tr>
        <td><b>Publication Data: </b></td>
        <td><textarea cols="100" name="DisplayFormPaperPubData" id="DisplayFormPaperPubData"></textarea></td>
     </tr>
     <tr>
        <td><b>Comments: </b></td>
        <td><textarea cols="100" name="DisplayFormPaperComments" id="DisplayFormPaperComments"></textarea></td>
     </tr>
     <tr>
        <td><b>History (Do Not Modify): </b></td>
        <td><textarea cols="100" readonly name="DisplayFormPaperHistory" id="DisplayFormPaperHistory"></textarea></td>
     </tr>
     <tr>
        <td><b>Operation To Perform: </b></td>
        <td><select name="DisplayFormOperation" id="DisplayFormOperation">
                   <option name="None" value="None"> -- Select Paper Operation -- </option>
                   <option name="Create" value="Create">Create New Paper</option>
                   <option name="Modify" value="Modify">Modify Existing Paper</option>
                   <option name="Delete" value="Delete">Delete Paper</option>
            </select>
        </td>
     </tr>
     <tr>
        <td><b>Submit Operation Data: </b></td>
        <td><input type="submit" value="Go!" /></td>
     </tr>
     <tr>
        <td><b>Reset Paper Data: </b></td>
        <td><input type="reset" value="Clear Form" /></td>
     </tr>
   </table>
</form>

</div><!-- tabs-1 -->
</div><!-- tabs -->

<script type="text/javascript">

function RepopulatePaperDataForm() {
    $("#DisplayFormPaperKey").prop("value", '<?php echo $paperKey; ?>');
    $("#DisplayFormPaperAuthors").prop("value", '<?php echo $authors; ?>');
    $("#DisplayFormPaperTitle").prop("value", '<?php echo $title; ?>');
    $("#DisplayFormPaperDate").prop("value", '<?php echo $date; ?>');
    $("#DisplayFormPaperDOIInfo").prop("value", '<?php echo $doi; ?>');
    $("#DisplayFormPaperPubData").prop("value", '<?php echo $pubData; ?>');
    $("#DisplayFormPaperComments").prop("value", '<?php echo $comments; ?>');
    $("#DisplayFormPaperHistory").prop("value", '<?php echo $historyMod; ?>');
}

RepopulatePaperDataForm();

</script>

<script type="text/javascript">
	$(document).ready(function() {
		$("#tabs").tabs();
	});
	$(window).load(function() {
	    $('#tabs-1').append('<div id="bottomClearDiv" style="clear:both;" class="clear"></div>');
    });
</script>

</body>
</html>
