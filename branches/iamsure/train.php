<?php
require_once "common.php";
checkday();
page_header("Bluspring's Warrior Training");

output("`b`cBluspring's Warrior Training`c`b");
$sql = "SELECT * FROM masters WHERE creaturelevel = ".$session['user']['level'];
$result = db_query($sql) or die(sql_error($sql));
if (!isset($_GET['op']))
{
    $_GET['op'] = '';
}
if (!isset($battle))
{
    $battle = '';
}
if (!isset($_GET['victory']))
{
    $_GET['victory'] = '';
}
if (!isset($session['user']['experience']))
{
    $session['user']['experience'] = '';
}
if (!isset($session['user']['seenmaster']))
{
    $session['user']['seenmaster'] = '';
}
if (!isset($_GET['skill']))
{
    $_GET['skill'] = '';
}
if (db_num_rows($result) > 0){
	$master = db_fetch_assoc($result);
	if ($master['creaturename'] == "Gadriel the Elven Ranger" && $session['user']['race'] == 2) {
		$master['creaturewin'] = "You call yourself an Elf?? Maybe Half-Elf! Come back when you've been better trained.";
		$master['creaturelose'] = "It is only fitting that another Elf should best me.  You make good progress.";
	}
	$level = $session['user']['level'];
	//$exprequired=((pow((($level-1)/15),3)*3+1)*100*$level);
	//$exparray=array(1=>100,400,602,1012,1540,2207,3041,4085,5395,7043,9121,11740,15037,19171,24330);
//	$exparray=array(1=>100,300,602,1012,1540,2207,3041,4085,5395,7043,9121,11740,15037,19171,24330);
	$exparray=array(1=>100,400,1002,1912,3140,4707,6641,8985,11795,15143,19121,23840,29437,36071,43930);
	while (list($key,$val)=each($exparray)){
		$exparray[$key]= round(
			$val + ($session['user']['dragonkills']/4) * $session['user']['level'] * 100
		,0);
	}
	$exprequired=$exparray[$session['user']['level']];
	//output("`\$Exp Required: $exprequired; exp possessed: ".$session['user']['experience']."`0`n");
	
	if ($_GET['op']==""){
		output("The sound of conflict surrounds you.  The clang of weapons in grizzly battle inspires your warrior heart. ");
		output("`n`nYour master is `^$master[creaturename]`0.");
		addnav("Question Master","train.php?op=question");
		addnav("Challenge Master","train.php?op=challenge");
		if ($session['user']['superuser'] > 2) {
			addnav("Superuser Gain level","train.php?op=challenge&victory=1");
		}
		addnav("Return to the Village","village.php");
	}else if($_GET['op']=="challenge"){
		if ($_GET['victory']) {
			$victory=true;
			$defeat=false;
			if ($session['user']['experience'] < $exprequired)
				$session['user']['experience'] = $exprequired;
			$session['user']['seenmaster'] = 0;
		}
		if ($session['user']['seenmaster']){
			output("You think that, perhaps, you've seen enough of your master for today, the lessons you learned earlier prevent you from so willingly ");
			output("subjecting yourself to that sort of humiliation again.");
			addnav("Return to the village","village.php");
		}else{
			if (getsetting("multimaster",1)==0) $session['user']['seenmaster'] = 1;
			if ($session['user']['experience']>=$exprequired){
				$atkflux = e_rand(0,$session['user']['dragonkills']);
				$defflux = e_rand(0,($session['user']['dragonkills']-$atkflux));
				$hpflux = ($session['user']['dragonkills'] - ($atkflux+$defflux)) * 5;
				$master['creatureattack']+=$atkflux;
				$master['creaturedefense']+=$defflux;
				$master['creaturehealth']+=$hpflux;
				$session['user']['badguy']=createstring($master);
 
				$battle=true;
				if ($victory) {
					$badguy = createarray($session['user']['badguy']);
					output("With a flurry of blows you dispatch your master.`n");
				}
			}else{
				output("You ready your ".$session['user']['weapon']." and ".$session['user']['armor']." and approach `^$master[creaturename]`0.`n`nA small crowd of onlookers ");
				output("has gathered, and you briefly notice the smiles on their faces, but you feel confident.  You bow before `^$master[creaturename]`0, and execute ");
				output("a perfect spin-attack, only to realize that you are holding NOTHING!  `^$master[creaturename]`0 stands before you holding your weapon.  ");
				output("Meekly you retrieve your ".$session['user']['weapon'].", and slink out of the training grounds to the sound of boisterous guffaws.");
				addnav("Return to the village.","village.php");
				$session['user']['seenmaster']=1;
			}
		}
	}else if($_GET['op']=="question"){
		output("You approach `^$master[creaturename]`0 timidly and inquire as to your standing in his class.");
		if($session['user']['experience']>=$exprequired){
			output("`n`n`^$master[creaturename]`0 says, \"Gee, your muscles are getting bigger than mine...\"");
		}else{
			output("`n`n`^$master[creaturename]`0 states that you will need `%".($exprequired-$session['user']['experience'])."`0 more experience before you are ready to challenge him in battle.");
		}
		addnav("Question Master","train.php?op=question");
		addnav("Challenge Master","train.php?op=challenge");
		if ($session['user']['superuser'] > 2) {
			addnav("Superuser Gain level","train.php?op=challenge&victory=1");
		}
		addnav("Return to the Village","village.php");
	}else if($_GET['op']=="autochallenge"){
		addnav("Fight Your Master","train.php?op=challenge");
		output("`^{$master['creaturename']}`0 has heard of your prowess as a warrior, and heard of rumors that you think
		you are so much more powerful than him that you don't even need to fight him to prove anything.  His ego is
		understandably bruised, and so he has come to find you.  `^{$master['creaturename']}`0 Demands an immediate
		battle from you, and your own pride prevents you from refusing his demand.");
		if ($session['user']['hitpoints']<$session['user']['maxhitpoints']){
			output("`n`nBeing a fair person, your master gives you a healing potion before the fight begins.");
			$session['user']['hitpoints']=$session['user']['maxhitpoints'];
		}
		addnews("`3{$session['user']['name']}`3 was hunted down by their master `^{$master['creaturename']}`3 for being truant.");
	}
	if ($_GET['op']=="fight"){
		$battle=true;
	}
	if ($_GET['op']=="run"){
		output("`\$Your pride prevents you from running from this conflict!`0");
		$_GET['op']="fight";
		$battle=true;
	}
	
	if($battle){
		if (count($session['bufflist'])>0 && is_array($session['bufflist']) || $_GET['skill']!=""){
			$_GET['skill']="";
			if ($_GET['skill']=="") $session['user']['buffbackup']=serialize($session['bufflist']);
			$session['bufflist']=array();
			output("`&Your pride prevents you from using any special abilities during the fight!`0");
		}
		if (!$victory) include("battle.php");
		if ($victory){
			//$badguy['creaturegold']=e_rand(0,$badguy['creaturegold']);
			$search=array(	"%s",
											"%o",
											"%p",
											"%X",
											"%x",
											"%w",
											"%W"
										);
			$replace=array(	($session['user']['sex']?"her":"him"),
											($session['user']['sex']?"she":"he"),
											($session['user']['sex']?"her":"his"),
											($session['user']['weapon']),
											$badguy['creatureweapon'],
											$badguy['creaturename'],
											$session['user']['name']
										);
			$badguy['creaturelose']=str_replace($search,$replace,$badguy['creaturelose']);
	
			output("`b`&$badguy[creaturelose]`0`b`n"); 
			output("`b`\$You have defeated $badguy[creaturename]!`0`b`n");

			$session['user']['level']++;
			$session['user']['maxhitpoints']+=10;
			$session['user']['soulpoints']+=5;
			$session['user']['attack']++;
			$session['user']['defence']++;
			$session['user']['seenmaster']=0;
			output("`#You advance to level `^".$session['user']['level']."`#!`n");
			output("Your maximum hitpoints are now `^".$session['user']['maxhitpoints']."`#!`n");
			output("You gain an attack point!`n");
			output("You gain a defense point!`n");
			if ($session['user']['level']<15){
				output("You have a new master.`n");
			}else{
				output("None in the land are mightier than you!`n");
			}
			if ($session['user']['referer']>0 && $session['user']['level']>=4 && $session['user']['refererawarded']<1){
				$sql = "UPDATE accounts SET donation=donation+25 WHERE acctid={$session['user']['referer']}";
				db_query($sql);
				$session['user']['refererawarded']=1;
				systemmail($session['user']['referer'],"`%One of your referrals advanced!`0","`%{$session['user']['name']}`# has advanced to level `^{$session['user']['level']}`#, and so you have earned `^25`# points!");
			}
			increment_specialty();
			addnav("Question Master","train.php?op=question");
			addnav("Challenge Master","train.php?op=challenge");
			if ($session['user']['superuser'] > 2) {
				addnav("Superuser Gain level","train.php?op=challenge&victory=1");
			}
			addnav("Return to the Village","village.php");
			addnews("`%".$session['user']['name']."`3 has defeated ".($session['user']['sex']?"her":"his")." master, `%$badguy[creaturename]`3 to advance to level `^".$session['user']['level']."`3 on ".($session['user']['sex']?"her":"his")." `^".ordinal($session['user']['age'])."`3 day!!");
			$badguy=array();
			$session['user']['hitpoints'] = $session['user']['maxhitpoints'];
			//$session['user']['seenmaster']=1;
		}else{
			if($defeat){
				//addnav("Daily news","news.php");
				$sql = "SELECT taunt FROM taunts ORDER BY rand(".e_rand().") LIMIT 1";
				$result = db_query($sql) or die(db_error(LINK));
				$taunt = db_fetch_assoc($result);
				$taunt = str_replace("%s",($session['user']['gender']?"him":"her"),$taunt[taunt]);
				$taunt = str_replace("%o",($session['user']['gender']?"he":"she"),$taunt);
				$taunt = str_replace("%p",($session['user']['gender']?"his":"her"),$taunt);
				$taunt = str_replace("%x",($session['user']['weapon']),$taunt);
				$taunt = str_replace("%X",$badguy['creatureweapon'],$taunt);
				$taunt = str_replace("%W",$badguy['creaturename'],$taunt);
				$taunt = str_replace("%w",$session['user']['name'],$taunt);
				
				addnews("`%".$session['user']['name']."`5 has challenged their master, $badguy[creaturename] and lost!`n$taunt");
				//$session['user']['alive']=false;
				//$session['user']['gold']=0;
				$session['user']['hitpoints']=$session['user']['maxhitpoints'];
				output("`&`bYou have been defeated by `%$badguy[creaturename]`&!`b`n");
				output("`%$badguy[creaturename]`\$ halts just before delivering the final blow, and instead extends a hand to help you to your feet, and hands you a complimentary healing potion.`n");
				$search=array(	"%s",
												"%o",
												"%p",
												"%x",
												"%X",
												"%W",
												"%w"
											);
				$replace=array(	($session[user][gender]?"him":"her"),
												($session[user][gender]?"he":"she"),
												($session[user][gender]?"his":"her"),
												($session[user][weapon]),
												$badguy[creatureweapon],
												$badguy[creaturename],
												$session[user][name]
											);
				$badguy[creaturewin]=str_replace($search,$replace,$badguy[creaturewin]);
				output("`^`b$badguy[creaturewin]`b`0`n");
				addnav("Question Master","train.php?op=question");
				addnav("Challenge Master","train.php?op=challenge");
				if ($session['user']['superuser'] > 2) {
					addnav("Superuser Gain level","train.php?op=challenge&victory=1");
				}
				addnav("Return to the Village","village.php");
				$session[user][seenmaster]=1;
			}else{
			  fightnav(false,false);
			}
		}
	}
}else{
  output("You stroll in to the battle grounds.  Younger warriors huddle together and point as you pass by.  ");
	output("You know this place well.  Bluspring hails you, and you grasp her hand firmly.  There is nothing ");
	output("left for you here but memories.  You remain a moment longer, and look at the warriors in training ");
	output("before you turn to return to the village.");
	addnav("Return to the village","village.php");
}
page_footer();
?>
