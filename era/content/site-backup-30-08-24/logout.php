<?php
   session_start();
   unset($_SESSION["eravalid"]);
   unset($_SESSION["erauser"]);
   
   session_destroy();
   
   echo '<div align="center">Signed out successfully. Redirecting back...</div>';
   header('Refresh: 2; URL = index.php');
?>
