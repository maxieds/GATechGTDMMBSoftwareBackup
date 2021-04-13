<?php
//// ManageUsers.php : Real-time user administration page;
//// Author: Maxie D. Schmidt (maxieds@gmail.com)
//// Created: 2019.06.15
?>

<?php

include_once "Sessions.php";
include_once "LoginUtils.php";

Sessions::StartSessionTracking();
if(!Sessions::VerifyLogin()) {
     header("Location: Login.php");
     exit();
}

?>

<html>
<head>
	<title>gtDMMB RNADB User Administration</title>
	<link type="text/css" href="css/main.css" rel="stylesheet" />
	<link type="text/css" href="css/smoothness/jquery-ui-tabs.css" rel="stylesheet" />
	<script type="text/javascript" src="js/jquery-1.7.2.min.js"></script>
	<script type="text/javascript" src="js/jquery-ui-1.8.20.custom.min.js"></script>
</head>

<body>

<?php

include_once "BackendConfig.php";
include_once "DatabaseUtils.php";
include_once "UserUtils.php";

include      "NavigationBar.php";
PrintNavBarHTML(USERS_ADMIN_PAGE);

?>

<h1>User Administration</h1>

<p>
This page is used for administrators in the <i>wheel</i> group to create, modify, and 
delete users who may log in and edit the papers and sequences database entries. 
Any user may use the facilities on this page to reset their own password 
(subject to a minimum complexity requirement). 
</p>

<?php

$userName = "";
$fullName = "";
$email = "";
$isActive = "YES";
$userGroups = "";
$operation = "None";

if(isset($_POST['DisplayFormOperation'])) {

     // Get the submitted form input values:
     $userName = $_POST['DisplayFormUserName'];
     $fullName = $_POST['DisplayFormFullName'];
     $email = $_POST['DisplayFormEmail'];
     $isActive = $_POST['DisplayFormActive'];
     $userGroups = "";
     if(isset($_POST['DisplayFormGroups'])) {
          foreach($_POST['DisplayFormGroups'] as $groupName) {
               $userGroups = $userGroups . $groupName . ",";
          }
          if(substr($userGroups, -1) === ",") {
               $userGroups = substr($userGroups, 0, -1);
          }
     }
     $password = $_POST['DisplayFormPassword'];
     $passwordAgain = $_POST['DisplayFormPasswordAgain'];
     $passwordHash = !strcmp($password, "") ? 
                     LoginUtils::GetUserRowData($userName)['pwd_hash'] : 
                     UserUtils::HashPassword($password);
     $operation = $_POST['DisplayFormOperation'];

     // Now do error basic checking:
     $processingError = false;
     $errorMsg = "";
     if(strcmp($password, $passwordAgain)) {
          $processingError = true;
          $errorMsg .= "Passwords do not match! ";
     }
     if(strcmp($password, "") && 
            (strlen($password) < BackendConfig::MIN_PASSWORD_LENGTH || 
             strlen($passwordAgain) < BackendConfig::MIN_PASSWORD_LENGTH)) {
          $processingError = true;
          $errorMsg .= "Password minimum complexity requirement not met! ";
     }
     if(($operation === "Delete" || strcmp($userGroups, "")) && !(Sessions::CurrentUserIsWheelAdmin())) {
          $processingError = true;
          $errorMsg .= "User must be in the wheel (admin) group to perform this operation! ";
     }
     if(!strcmp($userName, "") || ($operation === "Create" && !strcmp($fullName, ""))) {
          $processingError = true;
          $errorMsg .= "User name(s) must be provided! ";
     }
     $userDataRow = LoginUtils::GetUserRowData($userName);
     if($operation === "Create" && $userDataRow != NULL) {
          $processingError = true;
          $errorMsg .= "Cannot create new user (unique user name already exists)! ";
     }
     else if($operation === "Modify" && $userDataRow == NULL) {
          $processingError = true;
          $errorMsg .= "Cannot modify data for a non-existent user! ";
     }
     else if($operation === "None") {
          $processingError = true;
          $errorMsg .= "No operation selected! ";
     }
     
     // Decision tree:
     if($processingError) {
          echo '<span style="color: #ff0000"><b>' . $errorMsg . "</b></span>\n<br/>\n";
     }
     else if($operation === "Create") {
          $dbCon = DatabaseUtils::ConnectToRNADB();
          $sqlOp = "INSERT INTO " . BackendConfig::DB_USERS_TBL . 
                   " (user_name,full_name,email,active,pwd_hash,last_login,current_login,groups) VALUES " . 
                   "('$userName','$fullName','$email',$isActive,'$passwordHash','NONE','NONE','$userGroups');";
          $dbOpResult = DatabaseUtils::DeSQL($sqlOp, $dbCon);
          if($dbOpResult) {
               echo '<span>' . "New user <i>$userName</i> created successfully!" . "</span><br/>\n\n";
          }
          else {
               echo "<span style=\"color: #ff0000\">Error creating new user <i>$userName</i> " . 
                    "with SQL query ='$sqlOp': " . "<br/>" . mysqli_error($dbCon) . "</span><br/>\n\n";
          }
          DatabaseUtils::CloseRNADBConnection($dbCon);
     }
     else if($operation === "Modify") {
          $dbCon = DatabaseUtils::ConnectToRNADB();
          $sqlOp = "UPDATE " . BackendConfig::DB_USERS_TBL . 
                   " SET full_name='$fullName'," . 
                   "email='$email',pwd_hash='$passwordHash',groups='$userGroups' " . 
                   "WHERE user_name='$userName';";
          $dbOpResult = DatabaseUtils::DeSQL($sqlOp, $dbCon);
          if($dbOpResult) {
               echo '<span>' . "New user <i>$userName</i> modified successfully!" . "</span><br/>\n\n";
          }
          else {
               echo "<span style=\"color: #ff0000\">Error modifying user <i>$userName</i> " . 
                    "with SQL query ='$sqlOp': " . "<br/>" . mysqli_error($dbCon) . "</span><br/>\n\n";
          }
          DatabaseUtils::CloseRNADBConnection($dbCon);
     }
     else if($operation === "Delete") {
          $dbCon = DatabaseUtils::ConnectToRNADB();
          $sqlOp = "DELETE FROM " . BackendConfig::DB_USERS_TBL . 
                   " WHERE user_name='$userName';";
          $dbOpResult = DatabaseUtils::DeSQL($sqlOp, $dbCon);
          if($dbOpResult) {
               echo '<span>' . "User <i>$userName</i> deleted successfully!" . "</span><br/>\n\n";
          }
          else {
               echo "<span style=\"color: #ff0000\">Error deleting user <i>$userName</i> " . 
                    "with SQL query ='$sqlOp': " . "<br/>" . mysqli_error($dbCon) . "</span><br/>\n\n";
          }
          DatabaseUtils::CloseRNADBConnection($dbCon);
     }
     
     include_once "BackendConfig.php";
     if(BackendConfig::DEBUGGING_ON) { 
          echo "<br/>\n\n<b>Debugging Information:</b> <br/>";
          echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;User Name Input: " . $userName . "<br/>\n";
          echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Full Name Input: " . $fullName . "<br/>\n";
          echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Email Input: " . $email . "<br/>\n";
          echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Is Active Input: " . $isActive . "<br/>\n";
          echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;User Groups Input: " . $userGroups . "<br/>\n";
          echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Operation Input: " . $operation . "<br/>\n";
     }

}

?>

<script type="text/javascript">

function FetchUserData(userName) {
     var jsonUserData = {};
     jsonUserData.userName = userName;
	$.ajax({
		type: 'POST',
		url: "UserUtils.php?getUserJSONData",
		data: jsonUserData,
		success: DisplayUserDataInForm
	});
}

function DisplayUserDataInForm(phpJSONData) {
     //console.log(phpJSONData);
     var jsonObj = JSON.parse(phpJSONData);
     $("#DisplayFormUserName").prop("value", jsonObj.user_name);
     $("#DisplayFormFullName").prop("value", jsonObj.full_name);
     $("#DisplayFormEmail").prop("value", jsonObj.email);
     $("#DisplayFormActive").prop("selectedIndex", jsonObj.active == "1" ? 1 : 0);
     var inWheelGroup = (jsonObj.groups.search("wheel") >= 0);
     var inPapersGroup = (jsonObj.groups.search("papers") >= 0);
     var inSeqGroup = (jsonObj.groups.search("sequences") >= 0);
     $("#DisplayFormWheelGroup").prop("checked", inWheelGroup);
     $("#DisplayFormPapersGroup").prop("checked", inPapersGroup);
     $("#DisplayFormSequencesGroup").prop("checked", inSeqGroup);
     var currentUserIsAdmin = <?php echo Sessions::CurrentUserIsWheelAdmin() ? "true" : "false"; ?>;
     if(!currentUserIsAdmin) {
          $("#DisplayFormWheelGroup").prop("disabled", true);
          $("#DisplayFormPapersGroup").prop("disabled", true);
          $("#DisplayFormSequencesGroup").prop("disabled", true);
     }
     else {
          $("#DisplayFormWheelGroup").prop("disabled", false);
          $("#DisplayFormPapersGroup").prop("disabled", false);
          $("#DisplayFormSequencesGroup").prop("disabled", false);
     }
     $("#DisplayFormPassword").prop("value", "");
     $("#DisplayFormPasswordAgain").prop("value", "");
     $("#DisplayFormOperation").prop("selectedIndex", "0");
}

function ResetUserDataForm() {
    $("#DisplayFormUserName").prop("value", "");
    $("#DisplayFormFullName").prop("value", "");
    $("#DisplayFormEmail").prop("value", "");
    $("#DisplayFormActive").prop("selectedIndex", 1);
    $("#DisplayFormWheelGroup").prop("checked", false);
    $("#DisplayFormPapersGroup").prop("checked", false);
    $("#DisplayFormSequencesGroup").prop("checked", false);
}

</script>

<h2>Users List:</h2>

<table style="width:80%; text-align: center;" align="center">
  <tr>
    <th>User Name</th>
    <th>Last Login</th> 
    <th>Is Active</th>
    <th>Group Permissions</th>
    <th>Operations</th>
  </tr>

<?php

include_once "Sessions.php";
include_once "DatabaseUtils.php";

$dbCon = DatabaseUtils::ConnectToRNADB();
$userRowData = NULL;

if(!Sessions::CurrentUserIsWheelAdmin()) { // display only this user's data: 
     $userSQLQuery = "SELECT * FROM " . BackendConfig::DB_USERS_TBL . 
                     " WHERE user_name='" . Sessions::GetCurrentUserName() . "';";
     $userRowData = DatabaseUtils::DeSQL($userSQLQuery, $dbCon);
}
else {
    $userSQLQuery = "SELECT * FROM " . BackendConfig::DB_USERS_TBL . ";";
    $userRowData = DatabaseUtils::DeSQL($userSQLQuery, $dbCon);
}

while($row = mysqli_fetch_assoc($userRowData)) {
     echo "<tr>\n";
     echo "     <td>" . $row['full_name'] . " (" . $row['user_name'] . ")</td>\n";
     echo "     <td>" . $row['last_login'] . "</td>\n";
     echo "     <td>" . ($row['active'] === '1' ? "Yes" : "No") . "</td>\n";
     echo "     <td>" . $row['groups'] . "</td>\n";
     echo '     <td><button class="DisplayUserButton" value="' . $row['user_name'] . 
          "\" onclick=\"FetchUserData('" . $row['user_name'] . "')\">Display User</button></td>\n";
     echo "</tr>\n";
}
DatabaseUtils::CloseRNADBConnection($dbCon);

?>

</table>

<h2>Create / Modify / Delete User Form:</h2>

<form action="ManageUsers.php" method="post">
   <table>
     <tr>
        <td><b>Unique User Name: </b></td>
        <td><input type="text" name="DisplayFormUserName" id="DisplayFormUserName" /></td>
     </tr>
     <tr>
        <td><b>Full (Prefered) Name: </b></td>
        <td><input type="text" name="DisplayFormFullName" id="DisplayFormFullName" /></td>
     </tr>
     <tr>
        <td><b>Email: </b></td>
        <td><input type="text" name="DisplayFormEmail" id="DisplayFormEmail" /></td>
     </tr>
     <tr>
        <td><b>Is Active? </b></td>
        <td><select name="DisplayFormActive" id="DisplayFormActive">
                   <option value="0">NO</option>
                   <option value="1">YES</option>
            </select>
        </td>
     </tr>
     <tr>
        <td><b>Group Permissions: </b></td>
        <td><input type="checkbox" value="wheel" name="DisplayFormGroups[]" id="DisplayFormWheelGroup" /> Wheel 
            <input type="checkbox" value="papers" name="DisplayFormGroups[]" id="DisplayFormPapersGroup"> Papers
            <input type="checkbox" value="sequences" name="DisplayFormGroups[]" id="DisplayFormSequencesGroup"> Sequences
        </td>
     </tr>
     <tr>
        <td><b>Password: </b></td>
        <td><input type="password" name="DisplayFormPassword" id="DisplayFormPassword" /></td>
     </tr>
     <tr>
        <td><b>Password (Again): </b></td>
        <td><input type="password" name="DisplayFormPasswordAgain" id="DisplayFormPasswordAgain" /></td>
     </tr>
     <tr>
        <td><b>Operation To Perform: </b></td>
        <td><select name="DisplayFormOperation" id="DisplayFormOperation">
                   <option name="None" value="None"> -- Select User Operation -- </option>
                   <option name="Create" value="Create">Create New User</option>
                   <option name="Modify" value="Modify">Modify Existing User</option>
                   <option name="Delete" value="Delete">Delete User</option>
            </select>
        </td>
     </tr>
     <tr>
        <td><b>Submit Operation Data: </b></td>
        <td><input type="submit" value="Go!" /></td>
     </tr>
     <tr>
        <td><b>Clear Form Data: </b></td>
        <td><input type="reset" value="Clear Form" /></td>
     </tr>
   </table>
</form>

</div><!-- tabs-1 -->
</div><!-- tabs -->

<script type="text/javascript">

function RepopulateUserDataForm() {
    $("#DisplayFormUserName").prop("value", '<?php echo $userName; ?>');
    $("#DisplayFormFullName").prop("value", '<?php echo $fullName; ?>');
    $("#DisplayFormEmail").prop("value", '<?php echo $email; ?>');
    $("#DisplayFormActive").prop("selectedIndex", <?php echo $isActive === "YES" ? "1" : "0"; ?>);
    var userGroupsList = "<?php echo $userGroups; ?>";
    if(userGroupsList.search("wheel") >= 0) {
         $("#DisplayFormWheelGroup").prop("checked", true);
    }
    if(userGroupsList.search("papers") >= 0) {
         $("#DisplayFormPapersGroup").prop("checked", true);
    }
    if(userGroupsList.search("sequences") >= 0) {
         $("#DisplayFormSequencesGroup").prop("checked", true);
    }
}

RepopulateUserDataForm();
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
