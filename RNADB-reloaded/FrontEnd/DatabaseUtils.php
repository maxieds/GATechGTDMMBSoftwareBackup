<?php
//// DatabaseUtils.php : Utilities for accessing our MySQL databases;
//// Author: Maxie D. Schmidt (maxieds@gmail.com)
//// Created: 2019.06.15
?>

<?php

include_once "FrontendConfig.php";

class DatabaseUtils {

     private const MYSQL_CONNECTDB_ERROR_STR = "Could not connect to MySQL DB. "; 
     private const MYSQL_SELECTDB_ERROR_STR  = "Unable to select MySQL DB. ";

     public static function ConnectToDB($host, $uname, $upwd, $dbname) {
          $dbCon = mysqli_connect($host, $uname, $upwd) or 
                   die(DatabaseUtils::MYSQL_CONNECTDB_ERROR_STR . mysqli_connect_error());
          mysqli_select_db($dbCon, "$dbname") or 
                   die(DatabaseUtils::MYSQL_SELECTDB_ERROR_STR . mysqli_connect_error());
          return $dbCon;
     }

     public static function ConnectToRNADB($oldWhichDB = false) {
          if(!$oldWhichDB) {
               return DatabaseUtils::ConnectToDB(FrontendConfig::MYSQL_HOST, 
                                                 FrontendConfig::MYSQL_USER,
                                                 FrontendConfig::MYSQL_PWD,
                                                 FrontendConfig::MYSQL_RNADB
                                     );
          }
          else {
               return DatabaseUtils::ConnectToDB(FrontendConfig::MYSQL_HOST, 
                                                 FrontendConfig::MYSQL_USER,
                                                 FrontendConfig::MYSQL_PWD,
                                                 FrontendConfig::MYSQL_OLD_RNADB
                                     );
          }
     }
     
     public static function CloseRNADBConnection($dbCon) {
          return mysqli_close($dbCon);
     }
     
     public static function DeSQL($sqlQuery, $dbCon) {
          $safeSQLQuery = stripslashes($sqlQuery);
          return mysqli_query($dbCon, $safeSQLQuery);
     }

};


?>
