<?php
require_once "common.php";
checkday();

page_header("Dag Durnick's Table");
output("<span style='color: #9900FF'>",true);
output("`c`bDag Durnick's Table`b`c");

if (!isset($_GET['op']))
{
    $_GET['op'] = '';
}
if ($_GET['op']=="list"){
	output("Dag fishes a small leather bound book out from under his cloak, flips through it to a certain page and holds it up for you to see.`n`n");
	output("`c`bThe Bounty List`b`c`n");
	$sql = "SELECT name,alive,sex,level,laston,loggedin,bounty FROM accounts WHERE bounty>0 ORDER BY bounty DESC";
	$result = db_query($sql) or die(sql_error($sql));
	output("<table border=0 cellpadding=2 cellspacing=1 bgcolor='#999999'>",true);
	output("<tr class='trhead'><td><b>Bounty Amount</b></td><td><b>Level</b></td><td><b>Name</b></td><td><b>Location</b></td><td><b>Sex</b></td><td><b>Last on</b></tr>",true);
	for($i=0;$i<db_num_rows($result);$i++){
		$row = db_fetch_assoc($result);
		output("<tr class='".($i%2?"trdark":"trlight")."'><td>",true);
		output("`^".$row['bounty']."`0");
		output("</td><td>",true);
		output("`^".$row['level']."`0");
		output("</td><td>",true);
		output("`&".$row['name']."`0");
		if ($session['user']['loggedin']) output("</a>",true);
		output("</td><td>",true);
		$loggedin=(date("U") - strtotime($row['laston']) < getsetting("LOGINTIMEOUT",900) && $row['loggedin']);
		output($row['location']
			?"`3Boar's Head Inn`0"
			:(
				$loggedin
				?"`#Online`0"
				:"`3The Fields`0"
			)
		);
		output("</td><td>",true);
		output($row['sex']?"`!Female`0":"`!Male`0");
		output("</td><td>",true);
		$laston=round((strtotime("0 days")-strtotime($row['laston'])) / 86400,0)." days";
		if (substr($laston,0,2)=="1 ") $laston="1 day";
		if (date("Y-m-d",strtotime($row['laston'])) == date("Y-m-d")) $laston="Today";
		if (date("Y-m-d",strtotime($row['laston'])) == date("Y-m-d",strtotime("-1 day"))) $laston="Yesterday";
		if ($loggedin) $laston="Now";
		output($laston);
		output("</td></tr>",true);
	}
	output("</table>",true);
}else if ($_GET['op']=="addbounty"){
	if ($session['user']['bounties'] >= getsetting("maxbounties",5)) {
		output("Dag gives you a piercing look. `7\"Ye be thinkin' I be an assassin or somewhat?  Ye already be placin' more than 'nuff bounties for t'day.  Now, be ye gone before I stick a bounty on yer head fer annoyin' me.`n`n");
	} else {
		$fee = getsetting("bountyfee",10);
		if ($fee < 0 || $fee > 100) {
			$fee = 10;
			savesetting("bountyfee",$fee);
		}
		$min = getsetting("bountymin",50);
		$max = getsetting("bountymax",400);
		output("Dag Durnick glances up at you and adjusts the pipe in his mouth with his teeth.`n`7\"So, who ye be wantin' to place a hit on? Just so ye be knowing, they got to be legal to be killin', they got to be at least level " . getsetting("bountylevel",3) . ", and they can't be having too much outstandin' bounty nor be getting hit to frequent like, so if they ain't be listed, they can't be contracted on!  We don't run no slaughterhouse here, we run a.....business.  Also, there be a " . getsetting("bountyfee",10) . "% listin' fee fer any hit ye be placin'.\"`n`n");
		output("<form action='dag.php?op=finalize' method='POST'>",true);
		output("`2Target: <input name='contractname'>`n", true);
		output("`2Amount to Place: <input name='amount' id='amount' width='5'>`n`n",true);
		output("<input type='submit' class='button' value='Finalize Contract'></form>",true);
		addnav("","dag.php?op=finalize");
	}
}elseif ($_GET['op']=="finalize") {
	//$name = "%" . rawurldecode($_POST['contractname']) . "%";
	if ($_GET['subfinal']==1){
		$sql = "SELECT acctid,name,login,level,locked,age,dragonkills,pk,experience,bounty FROM accounts WHERE name='".addslashes(rawurldecode(stripslashes($_POST['contractname'])))."' AND locked=0";
		//output($sql);
	}else{
		$contractname = stripslashes(rawurldecode($_POST['contractname']));
		$name="%";
		for ($x=0;$x<strlen($contractname);$x++){
			$name.=substr($contractname,$x,1)."%";
		}
		$sql = "SELECT acctid,name,login,level,locked,age,dragonkills,pk,experience,bounty FROM accounts WHERE name LIKE '".addslashes($name)."' AND locked=0";
	}
	$result = db_query($sql);
	if (db_num_rows($result) == 0) {
		output("Dag Durnick sneers at you, `7\"There not be anyone I be knowin' of by that name.  Maybe ye should come back when ye got a real target in mind?\"");
	} elseif(db_num_rows($result) > 100) {
		output("Dag Durnick scratches his head in puzzlement, `7\"Ye be describing near half th' town ye fool?  Why don't ye be giving me a better name now?\"");
	} elseif(db_num_rows($result) > 1) {
		output("Dag Durnick searches through his list for a moment, `7\"There be a couple of 'em that ye could be talkin' about.  Which one ye be meaning?\"`n");
		output("<form action='dag.php?op=finalize&subfinal=1' method='POST'>",true);
		output("`2Target: <select name='contractname'>",true);
		for ($i=0;$i<db_num_rows($result);$i++){
			$row = db_fetch_assoc($result);
			output("<option value='".rawurlencode($row['name'])."'>".preg_replace("'[`].'","",$row['name'])."</option>",true);
		}
		output("</select>`n`n",true);
		output("`2Amount to Place: <input name='amount' id='amount' width='5' value='".$_POST['amount']."'>`n`n",true);
		output("<input type='submit' class='button' value='Finalize Contract'></form>",true);
		addnav("","dag.php?op=finalize&subfinal=1");
	} else {
		// Now, we have just the one, so check it.
		$row  = db_fetch_assoc($result);
		if ($row['locked']) {
			output("Dag Durnick sneers at you, `7\"There not be anyone I be knowin' of by that name.  Maybe ye should come back when ye got a real target in mind?\"");
		} elseif ($row['login'] == $session['user']['login']) {
			output("Dag Durnick slaps his knee laughing uproariously, `7\"Ye be wanting to take out a contract on yerself?  I ain't be helping no suicider, now!\"");
		} elseif ($row['level'] < getsetting("bountylevel",3) ||
				  ($row['age'] < getsetting("pvpimmunity",5) &&
				   $row['dragonkills'] == 0 && $row['pk'] == 0 &&
				   $row['experience'] < getsetting("pvpminexp",1500))) {
			output("Dag Durnick stares at you angrily, `7\"I told ye that I not be an assassin.  That ain't a target worthy of a bounty.  Now get outta me sight!\"");
		} else {
			// All good!
			$amt = abs((int)$_POST['amount']);
			$min = getsetting("bountymin", 50) * $row['level'];
			$max = getsetting("bountymax", 400) * $row['level'];
			$fee = getsetting("bountyfee",10);
			if ($amt < $min) {
				output("Dag Durnick scowls, `7\"Ye think I be workin' for that pittance?  Be thinkin' again an come back when ye willing to spend some real coin.  That mark be needin' at least " . $min . " gold to be worth me time.\"");
			} elseif ($session['user']['gold'] <round($amt*1.1,0)) {
				output("Dag Durnick scowls, `7\"Ye don't be havin enough gold to be settin' that contract.  Wastin' my time like this, I aught to be puttin' a contract on YE instead!");
			} elseif ($amt + $row['bounty'] > $max) {
				output("Dag looks down at the pile of coin and just leaves them there. `7\"I'll just be passin' on that contract.  That's way more'n `^{$row['name']} `7be worth and ye know it.  I ain't no durned assassin. A bounty o' {$row['bounty']} already be on their head.  I might be willin' t'up it to $max, after me $fee% listin' fee of course\"`n`n");
			} else {
				output("You slide the coins towards Dag Durnick, who deftly palms them from the table. `7\"I'll just be takin' me $fee% listin' fee offa the top.  The word be put out that ye be wantin' `^{$row['name']} `7taken care of. Be patient, and keep yer eyes on the news.\"`n`n");
				$session['user']['bounties']++;
				$cost = round($amt*1.1,0);
				$session['user']['gold']-=$cost;
				debuglog("spent $cost gold for a $amt bounty on", $row['acctid']);
				$sql = "UPDATE accounts SET bounty=bounty+$amt WHERE login='".$row['login']."'";
				db_query($sql);
			}
		}
	}
}else{
	output("You stroll over to Dag Durnick, who doesn't even bother to look up at you. He takes a long pull on his pipe.`n");
	output("`7\"Ye probably be wantin' to know if there's a price on yer head, ain't ye.\"`n`n");
	if ($session['user']['bounty']>0){
		output("\"`3Well, it be lookin like ye have `^".$session['user']['bounty']." gold`3 on yer head currently. Ye might wanna be watchin yourself.\"");
	}else{
		output("\"`3Ye don't have no bounty on ya.  I suggest ye be keepin' it that way.\"");
	}
	addnav("Check the Wanted List","dag.php?op=list");
	addnav("Set a Bounty","dag.php?op=addbounty");
}
if ($_GET['op'] != '')
addnav("Talk to Dag Durnick", "dag.php");
addnav("Return to the inn","inn.php");
output("</span>",true);
page_footer();
?>
