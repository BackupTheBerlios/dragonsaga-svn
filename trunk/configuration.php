<?php
require_once "common.php";
isnewday(3);

if ($_GET[op]=="save"){
	if ($_POST['blockdupeemail']==1) $_POST['requirevalidemail']=1;
	if ($_POST['requirevalidemail']==1) $_POST['requireemail']=1;
	reset($_POST);
	while (list($key,$val)=each($_POST)){
		savesetting($key,stripslashes($val));
		output("Setting $key to ".stripslashes($val)."`n");
	}
}

page_header("Game Settings");
addnav("G?Return to the Grotto","superuser.php");
addnav("M?Return to the Mundane","village.php");
addnav("",$REQUEST_URI);
//$nextnewday = ((gametime()%86400))/4 ; //abs(((86400- gametime())/getsetting("daysperday",4))%86400 );
//echo date("h:i:s a",strtotime("-$nextnewday seconds"))." (".($nextnewday/60)." minutes) ".date("h:i:s a",gametime()).gametime();
$time = (strtotime(date("1971-m-d H:i:s",strtotime("now -".getsetting("gameoffsetseconds",0)." seconds"))))*getsetting("daysperday",4) % strtotime("1971-01-01 00:00:00"); 
$time = gametime();
$tomorrow = strtotime(date("Y-m-d H:i:s",$time)." + 1 day");
$tomorrow = strtotime(date("Y-m-d 00:00:00",$tomorrow));
$today = strtotime(date("Y-m-d 00:00:00",$time));
$dayduration = ($tomorrow-$today) / getsetting("daysperday",4);
$secstotomorrow = $tomorrow-$time;
$secssofartoday = $time - $today;
$realsecstotomorrow = $secstotomorrow / getsetting("daysperday",4);
$realsecssofartoday = $secssofartoday / getsetting("daysperday",4);
//output("Current server time: ".date("Y-m-d H:i:s").", current game time: ".date("Y-m-d H:i:s",$time).", tomorrow is ".date("Y-m-d H:i:s",$tomorrow).", $secstotomorrow secs to tomorrow which is $realsecstotomorrow real secs.");
//output("Current server time: ".date("h:i:s a").", current game time: ".date("h:i:s a",$time).", next new day at ".date("h:i:s a",strtotime("+$realsecstotomorrow seconds")).".");
$enum="enum";
for ($i=0;$i<=86400;$i+=900){
	$enum.=",$i,".((int)($i/60/60)).":".($i/60 %60)."";
}
$setup = array(
	"Game Setup,title",
	"loginbanner"=>"Login Banner (under login prompt: 255 chars)",
	"soap"=>"Clean user posts (filters bad language and splits words over 45 chars long),bool",
	"maxcolors"=>"Max # of color changes usable in one comment,int",
	"gameadminemail"=>"Admin Email",
	"paypalemail"=>"Email address of Admin's paypal account",
	"defaultlanguage"=>"Default Language,enum,en,English,dk,Danish,de,Deutsch,es,Español,fr,French",
	"automaster"=>"Masters hunt down truant students,bool",
	"multimaster"=>"Master can be challenged multiple times in a day?,bool",
	"topwebid"=>"ID for Top Web Games (if you are registered),int",
	"beta"=>"Enable beta features for all players?,bool",

	"Account Creation,title",
	"superuser"=>"Default superuser level,enum,0,Standard play days per calendar day,1,Unlimited play days per calendar day,2,Admin creatures and taunts,3,Admin users",
	"newplayerstartgold"=>"Amount of gold to start a new character with,int",
	"requireemail"=>"Require users to enter their email address,bool",
	"requirevalidemail"=>"Require users to validate their email address,bool",
	"blockdupeemail"=>"One account per email address,bool",
	"spaceinname"=>"Allow spaces in user names,bool",
	"selfdelete"=>"Allow player to delete their character,bool",
	
	"New Days,title",
	"fightsforinterest"=>"Max forest fights remaining to earn interest?,int",
	"maxinterest"=>"Max Interest Rate (%),int",
	"mininterest"=>"Min Interest Rate (%),int",
	"daysperday"=>"Game days per calendar day,int",
	"specialtybonus"=>"Extra daily uses in specialty area,int",
	
	"Forest,title",
	"turns"=>"Forest Fights per day,int",
	"dropmingold"=>"Forest Creatures drop at least 1/4 of max gold,bool",
	"lowslumlevel"=>"Minimum level perfect fights give extra turn,int",

	"Bounty,title",
	"bountymin"=>"Minimum amount per level of target for bounty,int",
	"bountymax"=>"Maximum amount per level of target for bounty,int",
	"bountylevel"=>"Minimum player level for being a bounty target,int",
	"bountyfee"=>"Percentage of bounty kept by Dag Durnick,int",
	"maxbounties"=>"How many bounties can a person set per day,int",
	
	"Bank Settings,title",	
	"borrowperlevel"=>"Max player can borrow per level (val * level for max),int",
	"allowgoldtransfer"=>"Allow players to transfer gold,bool",
	"transferperlevel"=>"Max player can receive from a transfer (val * level),int",
	"mintransferlev"=>"Min level a player (0 DK's) needs to transfer gold,int",
	"transferreceive"=>"Total transfers a player can receive in one day,int",
	"maxtransferout"=>"Amount player can transfer to others (val * level),int",
	"innfee"=>"Fee for express inn payment (x or x%),int",

	"Mail Settings,title",
	"mailsizelimit"=>"Message size limit per message,int",
	"inboxlimit"=>"Limit # of messages in inbox,int",
	"oldmail"=>"Automatically delete old messages after (days),int",
	
	"PvP,title",
	"pvp"=>"Enable Slay Other Players,bool",
	"pvpday"=>"Player Fights per day,int",
	"pvpimmunity"=>"Days that new players are safe from PvP,int",
	"pvpminexp"=>"Experience below which player is safe from PvP,int",
	"pvpattgain"=>"Percent of victim experience attacker gains on win,int",
	"pvpattlose"=>"Percent of experience attacker loses on loss,int",
	"pvpdefgain"=>"Percent of attacker experience defender gains on win,int",
	"pvpdeflose"=>"Percent of experience defender loses on loss,int",

	"Content Expiration,title",
	"expirecontent"=>"Days to keep comments and news?  (0 = infinite),int",
	"expiretrashacct"=>"Days to keep never logged-in accounts? (0 = infinite),int",
	"expirenewacct"=>"Days to keep 1 level (0 dragon) accounts? (0 =infinite),int",
	"expireoldacct"=>"Days to keep all other accounts? (0 = infinite),int",
	"LOGINTIMEOUT"=>"Seconds of inactivity before auto-logoff,int",
	
	"Useful Information,title",
	"Last new day: ".date("h:i:s a",strtotime("-$realsecssofartoday seconds")).",viewonly",
	"Next new day: ".date("h:i:s a",strtotime("+$realsecstotomorrow seconds")).",viewonly",
	"Current game time: ".getgametime().",viewonly",
	"Day Duration: ".($dayduration/60/60)." hours,viewonly",
	"Current Server Time: ".date("Y-m-d h:i:s a").",viewonly",
	"gameoffsetseconds"=>"Real time to offset new day,$enum", 
	
	"LoGDnet Setup (LoGDnet does require PHP to have file wrappers enabled!!),title",
	"logdnet"=>"Register with LoGDnet?,bool",
	"serverurl"=>"Server URL",
	"serverdesc"=>"Server Description (255 chars)",
	"logdnetserver"=>"Master LoGDnet Server (default http://lotgd.net/)",
	
	"End Game Setup,title"
	);
	
if ($_GET[op]==""){
	loadsettings();
	output("<form action='configuration.php?op=save' method='POST'>",true);
	addnav("","configuration.php?op=save");
	showform($setup,$settings);
	output("</form>",true);
}
page_footer();
?>
