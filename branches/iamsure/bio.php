<?
require_once "common.php";
checkday();
$result = db_query("SELECT login,name,level,sex,title,specialty,hashorse,acctid,resurrections,bio,dragonkills,race FROM accounts WHERE login='$_GET[char]'");
$row = db_fetch_assoc($result);
$row[login] = rawurlencode($row[login]);

page_header("Character Biography: ".preg_replace("'[`].'","",$row[name]));
$specialty=array(0=>"Unspecified","Dark Arts","Mystical Powers","Thieving Skills");
//$horses=array(0=>"None","Pony","Gelding","Stallion");
output("`^Biography for $row[name]");
if ($session[user][loggedin]) output("<a href=\"mail.php?op=write&to=$row[login]\" target=\"_blank\" onClick=\"".popup("mail.php?op=write&to=$row[login]").";return false;\"><img src='images/newscroll.gif' width='16' height='16' alt='Write Mail' border='0'></a>",true);
output("`n`n");
output("`^Title: `@$row[title]`n");
output("`^Level: `@$row[level]`n");
output("`^Resurrections: `@$row[resurrections]`n");
output("`^Race: `@{$races[$row['race']]}`n");
output("`^Gender: `@".($row[sex]?"Female":"Male")."`n");
output("`^Specialty: `@".$specialty[$row[specialty]]."`n");

$sql = "SELECT mountname FROM mounts WHERE mountid='{$row['hashorse']}'";
$result = db_query($sql);
$mount = db_fetch_assoc($result);
if ($mount['mountname']=="")
       $mount['mountname'] = "`iNone`i";
output("`^Creature: `@{$mount['mountname']}`n");

if ($row['dragonkills']>0)
	output("`^Dragon Kills: `@{$row['dragonkills']}`n");
if ($row['bio']>"")
	output("`^Bio: `@`n".soap($row['bio'])."`n");
output("`n`^Recent accomplishments (and defeats) of $row[name]`^");
$result = db_query("SELECT * FROM news WHERE accountid=$row[acctid] ORDER BY newsdate DESC,newsid ASC LIMIT 100");
$odate="";
for ($i=0;$i<db_num_rows($result);$i++){
	$row = db_fetch_assoc($result);
	if ($odate!=$row[newsdate]){
		output("`n`b`@".date("D, M d",strtotime($row[newsdate]))."`b`n");
		$odate=$row[newsdate];
	}
	output($row[newstext]."`n");
}

if ($_GET[ret]==""){
	addnav("Return to the warrior list","list.php");
}else{
	$return = preg_replace("'[&?]c=[[:digit:]-]+'","",$_GET[ret]);
	$return = substr($return,strrpos($return,"/")+1);
	addnav("Return whence you came",$return);
}
page_footer();

?>
