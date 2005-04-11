<?php
// This file is just to give you a view of what perfmon will do for tds.. I'll work on integrating it into the admin panel later.
// Set these variables to match your db settings
$server = 'localhost';
$user = '';
$pwd = '';
$db = ''; // name of the database

include_once('backends/adodb/adodb.inc.php');
session_start(); # session variables required for monitoring
$conn = ADONewConnection('mysqlt');
$conn->Connect($server,$user,$pwd,$db);
$perf =& NewPerfMonitor($conn);
$perf->UI($pollsecs=5);
?>
