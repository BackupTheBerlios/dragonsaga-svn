<?
require_once "common.php";
if ($session[user][loggedin]) {
	checkday();
	if ($session[user][alive]) {
		addnav("Return to the village","village.php");
	} else {
		addnav("Return to the graveyard", "graveyard.php");
	}
	addnav("Currently Online","list.php");
}else{
	addnav("Login Screen","index.php");
	addnav("Currently Online","list.php");
}
page_header("List Warriors");

$playersperpage=50;

$sql = "SELECT count(acctid) AS c FROM accounts WHERE locked=0";
$result = db_query($sql);
$row = db_fetch_assoc($result);
$totalplayers = $row['c'];

if ($_GET['op']=="search"){
	$search="%";
	for ($x=0;$x<strlen($_POST['name']);$x++){
		$search .= substr($_POST['name'],$x,1)."%";
	}
	$search=" AND name LIKE '".addslashes($search)."' ";
	//addnav("List Warriors","list.php");
}else{
	$pageoffset = (int)$_GET['page'];
	if ($pageoffset>0) $pageoffset--;
	$pageoffset*=$playersperpage;
	$from = $pageoffset+1;
	$to = min($pageoffset+$playersperpage,$totalplayers);
	
	$limit=" LIMIT $pageoffset,$playersperpage ";
}
addnav("Pages");
for ($i=0;$i<$totalplayers;$i+=$playersperpage){
	addnav("Page ".($i/$playersperpage+1)." (".($i+1)."-".min($i+$playersperpage,$totalplayers).")","list.php?page=".($i/$playersperpage+1));
}

// Order the list by level, dragonkills, name so that the ordering is total!
// Without this, some users would show up on multiple pages and some users
// wouldn't show up
if ($_GET['page']=="" && $_GET['op']==""){
	output("`c`bWarriors Currently Online`b`c");
	$sql = "SELECT name,login,alive,location,sex,level,laston,loggedin,lastip,uniqueid FROM accounts WHERE locked=0 AND loggedin=1 AND laston>'".date("Y-m-d H:i:s",strtotime("-".getsetting("LOGINTIMEOUT",900)." seconds"))."' ORDER BY level DESC, dragonkills DESC, login ASC";
}else{
	output("`c`bWarriors in the realm (Page ".($pageoffset/$playersperpage+1).": $from-$to of $totalplayers)`b`c");
	$sql = "SELECT name,login,alive,location,sex,level,laston,loggedin,lastip,uniqueid FROM accounts WHERE locked=0 $search ORDER BY level DESC, dragonkills DESC, login ASC $limit";
}
if ($session[user][loggedin]){
	output("<form action='list.php?op=search' method='POST'>Search by name: <input name='name'><input type='submit' class='button' value='Search'></form>",true);
	addnav("","list.php?op=search");
}

$result = db_query($sql) or die(sql_error($sql));
$max = db_num_rows($result);
if ($max>100) {
	output("`\$Too many names match that search.  Showing only the first 100.`0`n");
}

output("<table border=0 cellpadding=2 cellspacing=1 bgcolor='#999999'>",true);
output("<tr class='trhead'><td><b>Alive</b></td><td><b>Level</b></td><td><b>Name</b></td><td><b>Location</b></td><td><b>Sex</b></td><td><b>Last on</b></tr>",true);
for($i=0;$i<$max;$i++){
	$row = db_fetch_assoc($result);
	output("<tr class='".($i%2?"trdark":"trlight")."'><td>",true);
	output($row[alive]?"`1Yes`0":"`4No`0");
	output("</td><td>",true);
	output("`^$row[level]`0");
	output("</td><td>",true);
	if ($session[user][loggedin]) output("<a href=\"mail.php?op=write&to=".rawurlencode($row['login'])."\" target=\"_blank\" onClick=\"".popup("mail.php?op=write&to=".rawurlencode($row['login'])."").";return false;\"><img src='images/newscroll.gif' width='16' height='16' alt='Write Mail' border='0'></a>",true);
	if ($session[user][loggedin]) output("<a href='bio.php?char=".rawurlencode($row['login'])."'>",true);
	if ($session[user][loggedin]) addnav("","bio.php?char=".rawurlencode($row['login'])."");
	output("`&$row[name]`0");
	if ($session[user][loggedin]) output("</a>",true);
	output("</td><td>",true);
	$loggedin=(date("U") - strtotime($row[laston]) < getsetting("LOGINTIMEOUT",900) && $row[loggedin]);
	output($row[location]
				?"`3Boar's Head Inn`0"
				:(
					$loggedin
					?"`#Online`0"
					:"`3The Fields`0"
					)
			);
	output("</td><td>",true);
	output($row[sex]?"`!Female`0":"`!Male`0");
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
page_footer();
?>
