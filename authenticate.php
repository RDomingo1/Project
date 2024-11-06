<!-- TEMPORARY --> 
 <!-- SORRY ALAN -->


<?php

  define('ADMIN_LOGIN','RDomingo1');

  define('ADMIN_PASSWORD','hungry');

  if (!isset($_SERVER['PHP_AUTH_USER']) || !isset($_SERVER['PHP_AUTH_PW'])

      || ($_SERVER['PHP_AUTH_USER'] != ADMIN_LOGIN)

      || ($_SERVER['PHP_AUTH_PW'] != ADMIN_PASSWORD)) {

    header('HTTP/1.1 401 Unauthorized');

    header('WWW-Authenticate: Basic realm="Our Website"');

    exit("Access Denied: Username and password required.");

  }

   

?>