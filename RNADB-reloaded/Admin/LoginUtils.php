<?php
//// LoginUtils.php : Helper functions for administering user logins;
//// Author: Maxie D. Schmidt (maxieds@gmail.com)
//// Created: 2019.06.15
?>

<?php

include_once "BackendConfig.php";
include_once "DatabaseUtils.php";
include_once "UserUtils.php";

class LoginUtils {

     public static function GetUserRowData($uname) {
          //if(strcmp($uname, Sessions::GetCurrentUserName()) && 
          //   !Sessions::CurrentUserIsWheelAdmin()) {
          //     return NULL;
          //}
          $userDBCon = DatabaseUtils::ConnectToRNADB();
          $userSQLQuery = "SELECT * FROM " .
                          BackendConfig::DB_USERS_TBL . 
                          " WHERE user_name='" . $uname . "';";
          $userRowData = DatabaseUtils::DeSQL($userSQLQuery, $userDBCon);
          $userRowData = mysqli_fetch_assoc($userRowData);
          DatabaseUtils::CloseRNADBConnection($userDBCon);
          return $userRowData;
     }

     public static function ValidateLoginData($uname, $upwd) {
          $userRowData = LoginUtils::GetUserRowData($uname);
          if($userRowData == NULL) {
               return NULL;
          }
          if(!password_verify($upwd, $userRowData['pwd_hash'])) {
               return NULL;
          }
          $userDataStruct = new AdminUserData;
          $userDataStruct->PopulateFromTableQuery($userRowData);
          return $userDataStruct;
     }
     
     public static function PerformLogin($uname, $upwd) {
          $userDataStruct = LoginUtils::ValidateLoginData($uname, $upwd);
          if($userDataStruct == NULL || !($userDataStruct->IsActive())) {
               $_SESSION['loginValid'] = false;
               return false;
          }
          $userDataStruct = LoginUtils::UpdateUserLoginTimes($uname, $userDataStruct);
          $_SESSION['loginValid']   = true;
          $_SESSION['userName']     = $userDataStruct->GetUserName();
          $_SESSION['fullName']     = $userDataStruct->GetFullName();
          $_SESSION['lastLogin']    = $userDataStruct->GetLastLogin();
          $_SESSION['userGroups']   = $userDataStruct->GetUserGroups();
          session_commit();
          return true;
     }
     
     public static function UpdateUserLoginTimes($uname, $userDataStruct) {
          $userDataStruct->UpdateLoginTimes();
          $updateUserLoginQuery = "UPDATE " . BackendConfig::DB_USERS_TBL . 
                                  " SET last_login='" . 
                                  $userDataStruct->GetLastLogin() . 
                                  "',current_login='" . 
                                  $userDataStruct->GetCurrentLogin() . 
                                  "' WHERE user_name='" . $uname . "';";
          $userDBCon = DatabaseUtils::ConnectToRNADB();
          DatabaseUtils::DeSQL($updateUserLoginQuery, $userDBCon);
          DatabaseUtils::CloseRNADBConnection($userDBCon);
          return $userDataStruct;
     }
     
     public static function RedirectToLogin() {
          header("Location: Login.php");
          //exit();
     }
     
};

?>
