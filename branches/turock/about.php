<?
require_once "common.php";
page_header("About Legend of the Green Dragon");
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

checkday();

if ($_GET['op']==""){
	$order=array("1","2");
	while (list($key,$val)=each($order)){
		switch($val){
		case "2":
			/* NOTICE
			 * NOTICE Server admins may put their own information here, please leave the main about body untouched.
			 * NOTICE
			 */
			output("<hr>",true);
			//output("Put your own information here and uncomment this line by removing the '/' marks.");
			
			break;
		case "1":
			/* NOTICE
			 * NOTICE This section may not be modified, please modify the Server Specific section above.
			 * NOTICE
			 */
			output("`@Legend of the Green Dragon`nBy Eric Stevens`n`n");
			output("`cLoGD version {$logd_version}`c");
			//This section may not be modified, please modify the Server Specific section above.
			output("MightyE tells you, \"`2Legend of the Green Dragon is my remake of the classic");
			output("BBS Door game, Legend of the Red Dragon (aka LoRD) by Seth Able Robinson.  ");
			output("`n`n`@\"`2LoRD is now owned by Gameport (<a href='http://www.gameport.com/bbs/lord.html'>http://www.gameport.com/bbs/lord.html</a>), and ",true);
			output("they retain exclusive rights to the LoRD name and game.  That's why all content in ");
			//This section may not be modified, please modify the Server Specific section above.
			output("Legend of the Green Dragon is new, with only a very few nods to the original game, such ");
			output("as the buxom barmaid, Violet, and the handsome bard, Seth.`n`n");
			output("`@\"`2Although serious effort was made to preserve the original feel of the game, ");
			output("numerous departures were taken from the original game to enhance playability, and ");
			//This section may not be modified, please modify the Server Specific section above.
			output("to adapt it to the web.`n`n");
			output("`@\"`2LoGD is released under the GNU General Public License (GPL), which essentially means ");
			output("that the source code to the game, and all derivatives of the game must remain open and");
			output("available upon request.`n`n");
			//This section may not be modified, please modify the Server Specific section above.
			output("`@\"`2You can download the latest version of LoGD at <a href='http://sourceforge.net/projects/lotgd' target='_blank'>http://sourceforge.net/projects/lotgd</a>",true);
			output(" and you can play the latest version at <a href='http://lotgd.net/'>http://lotgd.net</a>.`n`n",true);
			output("`@\"`2LoGD is programmed in PHP with a MySQL backend.  It is known to run on Windows and Linux with appropriate
				setups.  Most code has been written by Eric Stevens, with some pieces by other authors (denoted in source at these locations), 
				and the code has been released under the 
				<a href=\"http://www.gnu.org/copyleft/gpl.html\">GNU General Public License</a>.  Users of the source
				are bound to the terms therein.`@\"`n`n",true);
			//This section may not be modified, please modify the Server Specific section above.
			output("`@\"`2Users of the source are free to view and modify the source, but original copyright information, and
				original text from the about page must be preserved, though they may be added to.`@\"`n`n");
			output("`@\"`2I hope you enjoy the game!`@\"");
			//This section may not be modified, please modify the Server Specific section above.
			break;
		}
	}
	
	addnav("Game Setup Info","about.php?op=setup");
}elseif($_GET['op']=="setup"){
	addnav("About LoGD","about.php");
	$setup = array(
		"Game Setup,title",
		"pvp"=>"Enable Slay Other Players,viewonly",
		"pvpday"=>"Player Fights per day,viewonly",
		"pvpimmunity"=>"Days that new players are safe from PvP,viewonly",
		"pvpminexp"=>"Amount of experience when players become killable in PvP,viewonly",
		"soap"=>"Clean user posts (filters bad language and splits words over 45 chars long),viewonly",
		"newplayerstartgold"=>"Amount of gold to start a new character with,viewonly",
		"New Days,title",
		"fightsforinterest"=>"Player must have fewer than how many forest fights to earn interest?,viewonly",
		"maxinterest"=>"Max Interest Rate (%),viewonly",
		"mininterest"=>"Min Interest Rate (%),viewonly",
		"daysperday"=>"Game days per calendar day,viewonly",
		"specialtybonus"=>"Extra daily uses in specialty area,viewonly",

		"Bank settings,title",
		"borrowperlevel"=>"Max amount player can borrow per level,viewonly",
		"transferperlevel"=>"Max amount player can transfer per level of recipient,viewonly",
		"mintransferlev"=>"Minimum level a player has to be before they can transfer gold,viewonly",
		"transferreceive"=>"Total transfers a player can receive in one play day,viewonly",
		"maxtransferout"=>"Max amount total a player can transfer to others per level,viewonly",
		
		"Bounty,title",
		"bountymin"=>"Minimum amount per level of target for bounty,viewonly",
		"bountymax"=>"Maximum amount per level of target for bounty,viewonly",
		"bountylevel"=>"Minimum player level for being a bounty target,viewonly",
		"bountyfee"=>"Percentage of bounty kept by Dag Durnick,viewonly",
		"maxbounties"=>"How many bounties can a person set per day,viewonly",

		"Forest,title",
		"turns"=>"Forest Fights per day,viewonly",
		"dropmingold"=>"Forest Creatures always drop at least 1/4 of possible gold,viewonly",
		"lowslumlevel"=>"Minimum level to allow slumming,viewonly",
		
		"Mail Settings,title",
		"mailsizelimit"=>"Message size limit per message,viewonly",
		"inboxlimit"=>"Limit # of messages in inbox,viewonly",
		"oldmail"=>"Automatically delete old messages after (days),viewonly",
	
		"Content Expiration,title",
		"expirecontent"=>"Days to keep comments and news?  (0 for infinite),viewonly",
		"expiretrashacct"=>"Days to keep accounts that were never logged in to? (0 for infinite),viewonly",
		"expirenewacct"=>"Days to keep level 1 accounts with no dragon kills? (0 for infinite),viewonly",
		"expireoldacct"=>"Days to keep all other accounts? (0 for infinite),viewonly",
		"LOGINTIMEOUT"=>"Seconds of inactivity before auto-logoff,viewonly",
	
		"Useful Information,title",
		"Day Duration: ".round(($dayduration/60/60),0)." hours,viewonly",
		"Current Server Time: ".date("Y-m-d h:i:s a").",viewonly",
		"Last new day: ".date("h:i:s a",strtotime("-$realsecssofartoday seconds")).",viewonly",
		"Current game time: ".getgametime().",viewonly",
		"Next new day: ".date("h:i:s a",strtotime("+$realsecstotomorrow seconds"))." (".date("H\\h i\\m s\\s",strtotime("1970-01-01 00:00:00 + $realsecstotomorrow seconds"))."),viewonly"
		);
	
	output("`@<h3>Settings for this game</h3>`n`n",true);
	//output("<table border=1>",true);
	showform($setup,$settings,true);
	//output("</table>",true);
}else{

}
if ($session['user']['loggedin']) {
	addnav("Return to the news","news.php");
}else{
	addnav("Login Page","index.php");
}
page_footer();
?>
