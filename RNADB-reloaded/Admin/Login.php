<?php
//// Login.php : Page to login administrative users who oversee the databases;
//// Author: Maxie D. Schmidt (maxieds@gmail.com)
//// Created: 2019.06.15
?>

<html>
<head>
	<title>gtDMMB RNADB Administrative BackEnd Login</title>
	<link type="text/css" href="css/main.css" rel="stylesheet" />
	<link type="text/css" href="css/smoothness/jquery-ui-tabs.css" rel="stylesheet" />
	<script type="text/javascript" src="js/jquery-1.7.2.min.js"></script>
	<script type="text/javascript" src="js/jquery-ui-1.8.20.custom.min.js"></script>
</head>

<body>

<?php

include_once "Sessions.php";
Sessions::StartSessionTracking();

include_once "LoginUtils.php";

$loginSuccess = true;
$performedLoginOp = false;
if((!isset($_SESSION['loginValid']) || !$_SESSION['loginValid']) && 
   isset($_POST['userName']) && isset($_POST['userPwd'])) {
     $loginSuccess = LoginUtils::PerformLogin($_POST['userName'], $_POST['userPwd']);
     $performedLoginOp = true;
}

include("NavigationBar.php");
PrintNavBarHTML(LOGIN_ADMIN_PAGE);

?>

<h1>Login to the gtDMMB RNADB administration pages:</h1>

<?php

if(!$loginSuccess) {
     echo '<span style="color: #ff0000"><b>' . "User name and password do not match!" . "</b></span>\n<br/><br/>\n";
     if(BackendConfig::DEBUGGING_ON) {
          include_once "UserUtils.php";
          echo "<b>User Name: </b>" . $_POST['userName'] . "<br/>\n";
          echo "<b>Attempted Password Hash: </b>" . UserUtils::HashPassword($_POST['userPwd']) . "<br/>\n";
          echo "<b>Actual Password Hash: </b>" . LoginUtils::GetUserRowData($_POST['userName'])['pwd_hash'] . "<br/><br/>\n\n";
     }
}
else if($performedLoginOp) {
     echo "<span>Success logging you in, " . Sessions::GetCurrentUserFullName() . "!</span>\n<br/><br/>\n";
}

?>

<div id="loginCredentials">
    <form action="Login.php" method="post">
      <table>
         <tr>
            <td><img src="images/loginUserIcon.png" /></td>
            <td><b>User Name: </b></td> 
            <td><input type="text" name="userName" /></td>
         </tr>
         <tr>
            <td><img src="images/loginPasswordIcon.png" /></td>
            <td><b>Password: &nbsp;</b></td> 
            <td><input type="password" name="userPwd" /></td>
        </tr>
        <tr>
            <td></td>
            <td></td> 
            <td><input type="submit" value="Login ..." /></td>
        </tr>
      </table>
    </form>
</div>

</div><!-- tabs-1 -->
</div><!-- tabs -->

<script type="text/javascript">
	$(document).ready(function() {
		$("#tabs").tabs();
		$("#loginCredentials").button();
	});
	$(window).load(function() {
	    $('#tabs-1').append('<div id="bottomClearDiv" style="clear:both;" class="clear"></div>');
    });
</script>

</body>
</html>
