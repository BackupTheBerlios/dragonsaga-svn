<?
require_once "common.php";
isnewday(2);
addcommentary();

addnav("M?Return to the Mundane","village.php");
if ($_GET[op]=="newsdelete"){
	$sql = "DELETE FROM news WHERE newsid='$_GET[newsid]'";
	db_query($sql);
	$return = $_GET['return'];
	$return = preg_replace("'[?&]c=[[:digit:]-]*'","",$return);
	$return = substr($return,strrpos($return,"/")+1);
	redirect($return);
}
if ($_GET[op]=="commentdelete"){
	$sql = "DELETE FROM commentary WHERE commentid='$_GET[commentid]'";
	db_query($sql);
	$return = $_GET['return'];
	$return = preg_replace("'[?&]c=[[:digit:]-]*'","",$return);
	$return = substr($return,strrpos($return,"/")+1);
	if (strpos($return,"?")===false && strpos($return,"&")!==false){
		$x = strpos($return,"&");
		$return = substr($return,0,$x-1)."?".substr($return,$x+1);
	}
	redirect($return);
}

page_header("Superuser Grotto");
if ($_GET[op]=="checkcommentary"){
	addnav("G?Return to the Grotto","superuser.php");
	viewcommentary("' or '1'='1","X",100);
}else if ($_GET[op] == "bounties") {
	addnav("G?Return to the Grotto","superuser.php");
	output("`c`bThe Bounty List`b`c`n");
	$sql = "SELECT name,alive,sex,level,laston,loggedin,lastip,uniqueid,bounty FROM accounts WHERE bounty>0 ORDER BY bounty DESC";
	$result = db_query($sql) or die(sql_error($sql));
	output("<table border=0 cellpadding=2 cellspacing=1 bgcolor='#999999'>",true);
	output("<tr class='trhead'><td><b>Bounty Amount</b></td><td><b>Level</b></td><td><b>Name</b></td><td><b>Location</b></td><td><b>Sex</b></td><td><b>Alive</b></td><td><b>Last on</b></tr>",true);
	for($i=0;$i<db_num_rows($result);$i++){
	  $row = db_fetch_assoc($result);
	  output("<tr class='".($i%2?"trdark":"trlight")."'><td>",true);
	  output("`^$row[bounty]`0");
	  output("</td><td>",true);
	  output("`^$row[level]`0");
	  output("</td><td>",true);
	  output("`&$row[name]`0");
	  if ($session[user][loggedin]) output("</a>",true);
	  output("</td><td>",true);
	  $loggedin=(date("U") - strtotime($row[laston]) < getsetting("LOGINTIMEOUT",900) && $row[loggedin]);
	  output($row[location] ?"`3Boar's Head Inn`0" :($loggedin ?"`#Online`0" :"`3The Fields`0"));
	  output("</td><td>",true);
	  output($row[sex]?"`!Female`0":"`!Male`0");
	  output("</td><td>",true);
	  output($row[alive]?"`1Yes`0":"`4No`0");
	  output("</td><td>",true);
	  $laston=round((strtotime("0 days")-strtotime($row[laston])) / 86400,0)." days";
	  if (substr($laston,0,2)=="1 ") $laston="1 day";
	  if (date("Y-m-d",strtotime($row[laston])) == date("Y-m-d")) $laston="Today";
	  if (date("Y-m-d",strtotime($row[laston])) == date("Y-m-d",strtotime("-1 day"))) $laston="Yesterday";
	  if ($loggedin) $laston="Now";
	  output($laston);
	  output("</td></tr>",true);
	}
	output("</table>",true); 
}else{
	if ($session[user][sex]){
		output("`^You duck in to a secret cave that few know about.  Inside you are greeted ");
		output("by the sight of numerous muscular bare-chested men who wave palm fronds at you ");
		output("and offer to feed you grapes as you lounge on Greko-Roman couches draped with ");
		output("silk.`n`n");
	}else{
		output("`^You duck in to a secret cave that few know about.  Inside you are greeted ");
		output("by the sight of numerous scantily clad buxom women who wave palm fronds at you ");
		output("and offer to feed you grapes as you lounge on Greko-Roman couches draped with ");
		output("silk.`n`n");
	}
	viewcommentary("superuser","Engage in idle conversation with other gods:",25);
	addnav("Actions");
	addnav("Petition Viewer","viewpetition.php");
	if ($session[user][superuser]>=3) addnav("C?Recent Commentary","superuser.php?op=checkcommentary");
	addnav("B?Player Bios","bios.php");
	if ($session[user][superuser]>=3) addnav("Donator Page","donators.php");
	if ($session[user][superuser]>=3) addnav("Retitler","retitle.php");
	addnav("Display Bounties", "superuser.php?op=bounties");

	addnav("Editors");
	if ($session[user][superuser]>=3) addnav("User Editor","user.php");
	addnav("E?Creature Editor","creatures.php");
	if ($session[user][superuser]>=3) addnav("Mount Editor","mounts.php");
	addnav("Taunt Editor","taunt.php");
	addnav("Weapon Editor","weaponeditor.php");
	addnav("Armor Editor","armoreditor.php");
	addnav("Nasty Word Editor","badword.php");

	addnav("Mechanics");
	if ($session[user][superuser]>=3) addnav("Game Settings","configuration.php");
	addnav("Referring URLs","referers.php");
	addnav("Stats","stats.php");


}
page_footer();
?>
