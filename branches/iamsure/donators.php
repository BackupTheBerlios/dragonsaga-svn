<?php
// This program is free software; you can redistribute it and/or modify it
// under the terms of the GNU General Public License as published by the
// Free Software Foundation; either version 2 of the License, or (at your
// option) any later version.
//
// File: donators.php

require_once "common.php";
isnewday(3);

if (!isset($_GET['op']))
{
    $_GET['op'] = '';
}
page_header("Donator's Page");
addnav("G?Return to the Grotto","superuser.php");
addnav("M?Return to the Mundane","village.php");

output("<form action='donators.php?op=add1' method='POST'>",true);
addnav("","donators.php?op=add1");
output("`bAdd Donation Points:`b`nCharacter: <input name='name'> `nPoints: <input name='amt' size='3'>`n<input type='submit' class='button' value='Add Donation'>",true);
output("</form>",true);

if ($_GET['op']=="add2"){
	if ($_GET['id']==$session['user']['acctid']){
		$session['user']['donation']+=$_GET['amt'];
	}
	//ok to execute when this is the current user, they'll overwrite the value at the end of their page
	//hit, and this will allow the display table to update in real time.
	$sql = "UPDATE accounts SET donation=donation+'{$_GET['amt']}' WHERE acctid='{$_GET['id']}'";
	db_query($sql);
	$_GET['op']="";
}

if ($_GET['op']==""){
	$sql = "SELECT name,donation,donationspent FROM accounts WHERE donation>0 ORDER BY donation DESC";
	$result = db_query($sql);
	
	output("<table border='0' cellpadding='5' cellspacing='0'>",true);
	output("<tr><td>Name</td><td>Points</td><td>Spent</td></tr>",true);
	for ($i=0;$i<db_num_rows($result);$i++){
		$row = db_fetch_assoc($result);
		output("<tr class='".($i%2?"trlight":"trdark")."'>",true);
		output("<td>",true);
		output("`^".$row['name']."`0",true);
		output("</td><td>`@".number_format($row['donation'])."`0</td>",true);
		output("<td>`%".number_format($row['donationspent'])."`0</td>",true);
		output("</tr>",true);
	}
	output("</table>",true);
}else if ($_GET['op']=="add1"){
	$search="%";
	for ($i=0;$i<strlen($_POST['name']);$i++){
		$search.=substr($_POST['name'],$i,1)."%";
	}
	$sql = "SELECT name,acctid,donation,donationspent FROM accounts WHERE login LIKE '$search'";
	$result = db_query($sql);
	output("Confirm the addition of {$_POST['amt']} points to:`n`n");
	for ($i=0;$i<db_num_rows($result);$i++){
		$row = db_fetch_assoc($result);
		output("<a href='donators.php?op=add2&id=".$row['acctid']."&amt=".$_POST['amt']."'>",true);
		output($row['name']." (".$row['donation']."/".$row['donationspent'].")");
		output("</a>`n",true);
		addnav("","donators.php?op=add2&id=".$row['acctid']."&amt=".$_POST['amt']."");
	}
}
page_footer();
?>
