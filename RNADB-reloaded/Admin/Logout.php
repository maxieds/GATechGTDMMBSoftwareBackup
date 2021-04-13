<?php
//// Logout.php : Logs the current user out of their session and deletes all session data;
//// Author: Maxie D. Schmidt (maxieds@gmail.com)
//// Created: 2019.06.15
?>

<?php 

session_unset();
session_destroy();

unset($_SESSION['userName']);
unset($_SESSION['fullName']);
unset($_SESSION['lastLogin']);
unset($_SESSION['userGroups']);

include_once "Sessions.php";
Sessions::StartSessionTracking();

$_SESSION['loginValid'] = false;

header("Location: Login.php");
exit();

?>
