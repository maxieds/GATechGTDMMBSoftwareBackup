<?php
//// index.php : The default loaded page when accessing the URL;
//// Author: Maxie D. Schmidt (maxieds@gmail.com)
//// Created: 2019.06.15
?>

<?php
     include_once "FrontEnd/FrontendConfig.php";
     header("Location: " . FrontendConfig::RNADB_FRONTEND_URL . "/Search.php");
     exit();
?>
