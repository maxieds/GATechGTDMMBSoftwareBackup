<?php
//// UserUtils.php : Utility and helper functions to authenticate users;
//// Author: Maxie D. Schmidt (maxieds@gmail.com)
//// Created: 2019.06.15
?>

<?php

class AdminUserData {
     
     private $userName = "<NONE>";
     private $fullName = "<NONE>";
     private $userEmail = "<NONE>";
     private $lastLogin = "<NONE>";
     private $currentLogin = "<NONE>";
     private $isActive = false;
     private $userGroups = "";
     private $userPwdHash = "";
     
     public function PopulateFromTableQuery($userRowData) {
          if($userRowData == NULL) {
               return NULL;
          }
          $this->userName = $userRowData['user_name'];
          $this->fullName = $userRowData['full_name'];
          $this->userEmail = $userRowData['email'];
          $this->lastLogin = $userRowData['last_login'];
          $this->currentLogin = $userRowData['current_login'];
          $this->isActive = $userRowData['active'] == 1 ? true : false;
          $this->userGroups = $userRowData['groups'];
          $this->userPwdHash = $userRowData['pwd_hash'];
          return $this;
     }
     
     public function UpdateLoginTimes() {
          $this->lastLogin = $this->currentLogin;
          $this->currentLogin = UserUtils::GenerateLoginString();
          return $this;
     }
     
     public function GetUserName() {
          return $this->userName;
     }
     
     public function GetFullName() {
          return $this->fullName;
     }
     
     public function GetUserEmail() {
          return $this->userEmail;
     }
     
     public function GetLastLogin() {
          return $this->lastLogin;
     }
     
     public function GetCurrentLogin() {
          return $this->currentLogin;
     }
     
     public function IsActive() {
          return $this->isActive;
     }
     
     public function GetUserGroups() {
          return $this->userGroups;
     }
     
     public function GetPasswordHash() {
          return $this->userPwdHash;
     }

};

class UserUtils {

     public static function HashPassword($pwdString) {
          return password_hash($pwdString, CRYPT_BLOWFISH);
     }
     
     public static function GenerateLoginString() {
          return strftime("%Y.%m.%d at %R %p"); //. " from " . $_SERVER['HTTP_CLIENT_ID'];
     }

};

if(isset($_GET['getUserJSONData'])) {
     include_once "Sessions.php";
     include_once "LoginUtils.php";
     Sessions::StartSessionTracking();
     if(!strcmp(Sessions::GetCurrentUserName(), $_POST['userName']) || 
        Sessions::CurrentUserIsWheelAdmin()) {
        $userRowData = LoginUtils::GetUserRowData($_POST['userName']);
        print json_encode($userRowData);
     }
}

?>
