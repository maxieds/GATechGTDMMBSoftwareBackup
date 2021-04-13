<?php
//// NavigationBar.php : Common upper navigation bar information gets printed from here;
//// Author: Maxie D. Schmidt (maxieds@gmail.com)
//// Created: 2019.06.15
?>

<?php

include_once "Sessions.php";
Sessions::StartSessionTracking();

const RNADB_FRONTEND_PAGE = 0;
const SEQS_ADMIN_PAGE     = 1;
const PAPERS_ADMIN_PAGE   = 2;
const USERS_ADMIN_PAGE    = 3;
const LOGIN_ADMIN_PAGE    = 4;
const TOTAL_TAB_COUNT     = 5;

function PrintNavBarHTML($whichIsCurrentTab) {

     echo "<div id=\"tabs\">\n\n";
     
     $userLoggedIn = false;
     if(Sessions::VerifyLogin(false)) {
          $userLoggedIn = true;
          echo "<span class=\"loginSummaryStats\"><b>";
          $userNameStr = Sessions::GetCurrentUserFullName() . " (" . 
                         Sessions::GetCurrentUserName() . ")";
          echo "     &#x1f9d8;&nbsp; Currently logged in as " . $userNameStr . ". &nbsp&nbsp\n";
          echo "&nbsp;&nbsp;&nbsp;&nbsp; ... &nbsp;&nbsp;&nbsp;&nbsp; &#x1f5a7; ";
          echo "     <i>Last Login:</i> " . Sessions::GetCurrentUserLastLogin() . "&nbsp;&nbsp;\n";
          echo "</b></span>\n\n</br>\n";
          echo "<hr/>\n\n<br/>\n\n";
     }

     echo "<ul class=\"ui-tabs\">\n";
     for($tabNum = 0; $tabNum < TOTAL_TAB_COUNT; $tabNum++) {
          $tabTitle = "Unknown Tab Title";
          $tabLink = "#";
          $tabIcon = "";
          if($tabNum == RNADB_FRONTEND_PAGE) {
               $tabIcon = "&#x1f310;";
               $tabTitle = "RNADB FrontEnd";
               $tabLink = "/FrontEnd/Search.php";
          }
          else if($tabNum == SEQS_ADMIN_PAGE) {
               $tabIcon = "&#x1f9ec;";
               $tabTitle = "Manage Sequences";
               $tabLink = "ManageSequences.php";
          }
          else if($tabNum == PAPERS_ADMIN_PAGE) {
               $tabIcon = "&#x1f4f0;";
               $tabTitle = "Manage Papers";
               $tabLink = "ManagePapers.php";
          }
          else if($tabNum == USERS_ADMIN_PAGE) {
               $tabIcon = "&#x1f5b3;";
               $tabTitle = "Manage Users";
               $tabLink = "ManageUsers.php";
          }
          else if($tabNum == LOGIN_ADMIN_PAGE) {
               $tabIcon = $userLoggedIn ? "&#x1f512;" : "&#x1f513;";
               $tabTitle = $userLoggedIn ? "Logout" : "Login";
               $tabLink = $userLoggedIn ? "Logout.php" : "Login.php";
          }
          if($tabNum == $whichIsCurrentTab) {
               echo "     <li class=\"ui-tabs-selected\"><a style=\"text-decoration: none;\" " . 
                    "href=\"#tabs-1\" onclick=\"window.location.href='$tabLink'\">$tabIcon&nbsp; $tabTitle</a></li>\n";
          }
          else {
               echo "     <li class=\"liInline\"><a style=\"text-decoration: none;\" ". 
                    "href=\"$tabLink\" onclick=\"window.location.href='$tabLink'\">$tabIcon&nbsp; $tabTitle</a></li>\n";
          }
     }
     echo "</ul>\n\n";
     echo "<div id=\"tabs-1\" style=\"height:auto;\">\n";

}

?>
