<?
require_once "common.php";

if ($session['user']['loggedin']){
	page_header("Referral Page");
	addnav("L?Return to the Lodge","lodge.php");
	output("You will automatically receive 25 points for each person that you refer to this website who makes it to level 4.
	`n`n
	How does the site know that I referred a person?`n
  Easy!  When you tell your friends about this site, give out the following link:`n`n
  ".getsetting("serverurl","http://".$_SERVER['SERVER_NAME'].dirname($_SERVER['REQUEST_URI']))."referral.php?r=". rawurlencode($session['user']['login'])."`n`n
	and the site will know that you were the one who sent them here.  When they reach level 4 for the first time, you'll get your points!");
	
	$sql = "SELECT name,level,refererawarded FROM accounts WHERE referer={$session['user']['acctid']} ORDER BY dragonkills,level";
  //    output($sql);
	$result = db_query($sql);
  output("`n`nAccounts which you referred:`n<table border='0' cellpadding='3' cellspacing='0'><tr><td>Name</td><td>Level</td><td>Awarded?</td></tr>",true);
	for ($i=0;$i<db_num_rows($result);$i++){
		$row = db_fetch_assoc($result);
		output("<tr class='".($i%2?"trlight":"trdark")."'><td>",true);
		output($row['name']);
		output("</td><td>{$row['level']}</td><td>".($row['refererawarded']?"`@Yes!`0":"`\$No!`0")."</td></tr>",true);
	}
	if (db_num_rows($result)==0){
		output("<tr><td colspan='3' align='center'>`iNone!</td><?tr>",true);
	}
	output("</table>",true);
	page_footer();
}else{
	page_header("Welcome to Legend of the Green Dragon");
	output("`@Legend of the Green Dragon is a remake of the classic BBS Door Game Legend of the Red Dragon.  
	Adventure in to the classic realm that was one of the world's very first multiplayer roleplaying games!
	");
	addnav("Create a character","create.php?r=".HTMLEntities($_GET['r']));
	addnav("Login Page","index.php");
	page_footer();
}?>