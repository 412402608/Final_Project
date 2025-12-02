<?php
session_start();
session_unset(); 
session_destroy();
header("Location: doom_back.php");
exit;
?>