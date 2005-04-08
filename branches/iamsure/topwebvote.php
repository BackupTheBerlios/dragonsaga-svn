<?php
require_once("common.php");

$id = $_POST['acctid'];
$sql = "SELECT lastwebvote FROM accounts WHERE acctid=$id";
$res = db_query($sql);
$row = db_fetch_assoc($res);
$d = date("Y-W");
$dt = date("Y-m-d");
$old = date("Y-W", strtotime("{$row['lastwebvote']}"));
if ($_POST['test']) {
	echo("Date: $d<br>");
	echo("Old: $old<br>");
	echo("LastWebVote: {$row['lastwebvote']}<br>");
}
if ($old < $d) {
	$sql = "UPDATE accounts SET lastwebvote='$dt',gems=gems+1 WHERE acctid=$id";
	db_query($sql);
	$sql = "INSERT INTO debuglog VALUES(0,now(),$id,0,'gained 1 gem for topwebgames')";
	db_query($sql);
	if ($_POST['test']) {
		echo("done.<br>");
	}
} else {
	if ($_POST['test']) {
		echo("Already voted.<br>");
	}
}
?>
