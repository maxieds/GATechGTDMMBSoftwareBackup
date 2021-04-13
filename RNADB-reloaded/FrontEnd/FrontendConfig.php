<?php
//// FrontendConfig.php : Configuration constants for the RNADB frontend scripts;
//// Author: Maxie D. Schmidt (maxieds@gmail.com)
//// Created: 2019.06.15
?>

<?php

class FrontendConfig {

     const MYSQL_HOST        = "localhost";
     const MYSQL_USER        = "compbiouser4";
     const MYSQL_PWD         = "qazWSX123$%^--++1234..";
     const MYSQL_RNADB       = "rnadb_reloaded";
     const MYSQL_OLD_RNADB   = "rnadb_v2";
     const RNADB_USERS_TBL   = "AdminUsers";
     const RNADB_PAPERS_TBL  = "Papers";
     const RNADB_SEQS_TBL    = "Sequences";
     const RNADB_OLDSEQS_TBL = "pred";
     
     const RNADB_FRONTEND_URL = "FrontEnd";
     const DOWNLOADS_URL      = "Downloads";
     const DBRAW_DATA_URL     = "DBRawFiles";
     
     const DEBUGGING_ON = false;
     
     const RNADB_API_GETSIZE          = "getSize";
     const RNADB_API_SEARCH           = "search";
     const RNADB_API_DOWNLOAD_MINIMAL = "downloadMinimal";
     const RNADB_API_DOWNLOAD_MAXIMAL = "downloadMaximal";

}; 

if(FrontendConfig::DEBUGGING_ON) {
     ini_set('display_errors', 1); 
     error_reporting(E_ALL);
}

?>
