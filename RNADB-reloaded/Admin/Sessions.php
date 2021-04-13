<?php
//// Sessions.php : Control session data for users who can update the database;
//// Author: Maxie D. Schmidt (maxieds@gmail.com)
//// Created: 2019.06.15
?>

<?php

class Sessions {

     public static function StartSessionTracking() {
          if(session_id() == '' || !isset($_SESSION)) {
               session_start();
          }
          if(!isset($_SESSIONS['loginValid'])) {
               $_SESSIONS['loginValid'] = false;
          }
          if(!isset($_SESSIONS['userName'])) {
               $_SESSIONS['userName'] = "";
          }
          if(!isset($_SESSIONS['fullName'])) {
               $_SESSIONS['fullName'] = "";
          }
          if(!isset($_SESSIONS['lastLogin'])) {
               $_SESSIONS['lastLogin'] = "";
          }
          if(!isset($_SESSIONS['userGroups'])) {
               $_SESSIONS['userGroups'] = "";
          }
     }

     public static function VerifyLogin() {
          if(!isset($_SESSION['loginValid'])) {
               return false;
          }
          return $_SESSION['loginValid'];
     }
     
     public static function GetCurrentUserName() {
          if(isset($_SESSION['userName'])) {
               return $_SESSION['userName'];
          }
          return "";
     }
     
     public static function GetCurrentUserFullName() {
          if(isset($_SESSION['fullName'])) {
               return $_SESSION['fullName'];
          }
          return "";
     }
     
     public static function GetCurrentUserLastLogin() {
          if(isset($_SESSION['lastLogin'])) {
               return $_SESSION['lastLogin'];
          }
          return "";
     }
     
     public static function CurrentUserInGroup($groupName) {
          if(!isset($_SESSION['userGroups'])) {
               return false;
          }
          return strpos($_SESSION['userGroups'], $groupName) >= 0;
     }
     
     public static function CurrentUserIsWheelAdmin() {
          return Sessions::CurrentUserInGroup("wheel");
     }
     
     public static function CurrentUserIsPapersAdmin() {
          return Sessions::CurrentUserInGroup("papers");
     }
     
     public static function CurrentUserIsSequencesAdmin() {
          return Sessions::CurrentUserInGroup("sequences");
     }
     
};

?>
