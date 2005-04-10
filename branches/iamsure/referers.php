<?php
// This program is free software; you can redistribute it and/or modify it
// under the terms of the GNU General Public License as published by the
// Free Software Foundation; either version 2 of the License, or (at your
// option) any later version.
//
// File: referers.php

require_once "common.php";
isnewday(2);

if (!isset($_GET['op']))
{
    $_GET['op'] = '';
}
if (!isset($_GET['sort']))
{
    $_GET['sort'] = '';
}
$sql = "DELETE FROM referers WHERE last<'".date("Y-m-d H:i:s",strtotime("-".getsetting("expirecontent",180)." days"))."'";
db_query($sql);
if ($_GET['op']=="rebuild"){
	$sql = "SELECT * FROM referers";
	$result = db_query($sql);
	for ($i=0;$i<db_num_rows($result);$i++){
		$row = db_fetch_assoc($result);
		$site = str_replace("http://","",$row['uri']);
		if (strpos($site,"/")) $site = substr($site,0,strpos($site,"/"));
		$sql = "UPDATE referers SET site='".addslashes($site)."' WHERE refererid='{$row['refererid']}'";
		db_query($sql);
	}
}
addnav("G?Return to the Grotto","superuser.php");
addnav("M?Return to the Mundane","village.php");
addnav("Referer Options");
addnav("",$_SERVER['REQUEST_URI']);
addnav("Refresh","referers.php?sort=".URLEncode($_GET['sort'])."");
addnav("C?Sort by Count","referers.php?sort=count".($_GET['sort']=="count DESC"?"":"+DESC"));
addnav("U?Sort by URL","referers.php?sort=uri".($_GET['sort']=="uri"?"+DESC":""));
addnav("T?Sort by Time","referers.php?sort=last".($_GET['sort']=="last DESC"?"":"+DESC"));

addnav("Rebuild Sites","referers.php?op=rebuild");

page_header("Referers");
$order = "count DESC";
if ($_GET['sort']!="") $order=$_GET['sort'];
$sql = "SELECT SUM(count) AS count, MAX(last) AS last,site FROM referers GROUP BY site ORDER BY $order";
output("<table><tr><td>Count</td><td>Last</td><td>URL</td></tr>",true);
$result = db_query($sql);
for ($i=0;$i<db_num_rows($result);$i++){
	$row = db_fetch_assoc($result);
	output("<tr class='trdark'><td valign='top'>`b",true);
	output($row['count']);
	output("`b</td><td valign='top'>`b",true);
	$diffsecs = strtotime("now")-strtotime($row['last']);
	output((int)($diffsecs/86400)."d ".(int)($diffsecs/3600%3600)."h ".(int)($diffsecs/60%60)."m ".(int)($diffsecs%60)."s");
	output("`b</td><td>`b".HTMLEntities($row['site']==""?"`iNone`i":$row['site'])."`b</td></tr>",true);
	$sql = "SELECT count,last,uri FROM referers WHERE site='".addslashes($row['site'])."' ORDER BY {$order}";
	$result1 = db_query($sql);
	$skippedcount=0;
	$skippedtotal=0;
	for ($k=0;$k<db_num_rows($result1);$k++){
		$row1=db_fetch_assoc($result1);
		$diffsecs = strtotime("now")-strtotime($row1['last']);
		if ($diffsecs<=604800){
			output("<tr class='trlight'><td>",true);
			output($row1['count']);
			output("</td><td valign='top'>",true);
			//output((int)($diffsecs/86400)."d".(int)($diffsecs/3600%3600)."h".(int)($diffsecs/60%60)."m".(int)($diffsecs%60)."s");
			output(dhms($diffsecs));
			output("</td><td valign='top'>",true);
			if ($row1['uri']>"")
				output("<a href='".HTMLEntities($row1['uri'])."' target='_blank'>".HTMLEntities(substr($row1['uri'],0,150))."</a>`n",true);
			else
				output("`i`bNone`b`i`n");
			output("</td></tr>",true);
		}else{
			$skippedcount++;
			$skippedtotal+=$row1['count'];
		}
	}
	if ($skippedcount>0){
		output("<tr class='trlight'><td>$skippedtotal</td><td valign='top' colspan='2'>`i$skippedcount records skipped (over a week old)`i</td></tr>",true);
	}
	//output("</td></tr>",true);
}
output("</table>",true);
page_footer();
?>
