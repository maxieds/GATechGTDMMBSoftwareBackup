<?php
//// BackendConfig.php : Global constants and configuration data;
//// Author: Maxie D. Schmidt (maxieds@gmail.com)
//// Created: 2019.06.15
?>

<?php

class BackendConfig {

     const MYSQL_HOST       = "localhost";
     const MYSQL_USER       = "compbiouser4";
     const MYSQL_PWD        = "qazWSX123$%^--++1234..";
     const MYSQL_RNADB      = "rnadb_reloaded";
     const DB_USERS_TBL     = "AdminUsers";
     const DB_PAPERS_TBL    = "Papers";
     const DB_SEQUENCES_TBL = "Sequences";
     
     const RNADB_FRONTEND_URL = "FrontEnd";
     const RNADB_ADMIN_URL    = "Admin";
     const DBRAW_DATA_URL     = "DBRawFiles";
     
     const MIN_PASSWORD_LENGTH = 8;
     const DEBUGGING_ON        = false;

};

if(BackendConfig::DEBUGGING_ON) {
     ini_set('display_errors', 1); 
     error_reporting(E_ALL & ~E_NOTICE);
     //ini_set('memory_limit', '256M');
     //ini_set('upload_max_filesize', '50M');
     //ini_set('max_file_uploads', 20);
     //ini_set('post_max_size', '100M');
}

?>
