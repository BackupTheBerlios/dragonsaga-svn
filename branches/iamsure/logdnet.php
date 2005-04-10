<?php
// This program is free software; you can redistribute it and/or modify it
// under the terms of the GNU General Public License as published by the
// Free Software Foundation; either version 2 of the License, or (at your
// option) any later version.
//
// File: logdnet.php

require_once "common.php";

if ($_GET['op']==""){
	
	$sql = "SELECT lastupdate,serverid FROM logdnet WHERE address='$_GET[addy]'";
	$result = db_query($sql);
	$row = db_fetch_assoc($result);
	
	if (db_num_rows($result)>0){
		if (strtotime($row['lastupdate'])<strtotime("-1 minutes")){
			//echo strtotime($row[lastupdate])."<br>".strtotime("-5 minutes");
			$sql = "UPDATE logdnet SET priority=priority*0.99";
			db_query($sql);
			//use PHP server time for lastupdate in case mysql server and PHP server have different times.
			$sql = "UPDATE logdnet SET priority=priority+1,description='".soap($_GET['desc'])."',lastupdate='".date("Y-m-d H:i:s")."' WHERE serverid='".$row['serverid']."'";
			//echo $sql;
			db_query($sql);
			echo "Ok - updated";
		}else{
			echo "Ok - too soon to update";
		}
	}else{
		$sql = "INSERT INTO logdnet (address,description,lastupdate) VALUES ('$_GET[addy]','".soap($_GET['desc'])."',now())";
		$result = db_query($sql);
		echo "Ok - added";
	}
}elseif ($_GET['op']=="net"){
	$sql = "SELECT address,description FROM logdnet WHERE lastupdate > '".date("Y-m-d H:i:s",strtotime("-7 days"))."' ORDER BY priority DESC";
	$result=db_query($sql);
	for ($i=0;$i<db_num_rows($result);$i++){
		$row = db_fetch_assoc($result);
		$row = serialize($row);
		echo $row."\n";
	}
}else{
	page_header("LoGD Net");
	//$sql = "SELECT * FROM logdnet ORDER BY priority DESC";
	//$result=db_query($sql);
	addnav("Return to the login page","index.php");
	output("`@Below are a list of other LoGD servers that have registered with the LoGD Net.");
	output("<table>",true);
	$servers=file(getsetting("logdnetserver","http://lotgd.net/")."logdnet.php?op=net");
	while (list($key,$val)=each($servers)){
		$row=unserialize($val);
		if (trim($row['description'])=="") $row['description']="Another LoGD Server";
		if (substr($row['address'],0,7)!="http://"){
		
		}else{
			output("<tr><td><a href='".HTMLEntities($row['address'])."' target='_blank'>".soap(HTMLEntities($row['description']))."`0</a></td></tr>",true);
		}
	}
	output("</table>",true);
	page_footer();
}
?>
