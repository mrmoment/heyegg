<?php
include_once("lib/head.php");
session_start();
unset($_SESSION['email']);
unset($_SESSION['nickname']);
session_destroy();
print "<code>success</code>";
?>
