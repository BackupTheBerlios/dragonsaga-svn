<?php
// This program is free software; you can redistribute it and/or modify it
// under the terms of the GNU General Public License as published by the
// Free Software Foundation; either version 2 of the License, or (at your
// option) any later version.
//
// File: dragon.php

require_once "common.php";

page_header("The Green Dragon!");
if ($_GET['op']==""){
  output("`\$Fighting down every urge to flee, you cautiously enter the cave entrance, intent ");
	output("on catching the great green dragon sleeping, so that you might slay him with a minimum ");
	output("of pain.  Sadly, this is not to be the case, for as you round a corner within the cave ");
	output("you discover the great beast sitting on its haunches on a huge pile of gold, picking its ");
	output("teeth with a rib.");
	$badguy = array("creaturename"=>"`@The Green Dragon`0","creaturelevel"=>18,"creatureweapon"=>"Great Flaming Maw","creatureattack"=>45,"creaturedefense"=>25,"creaturehealth"=>300, "diddamage"=>0);
	//toughen up each consecutive dragon.
	//      $atkflux = e_rand(0,$session['user']['dragonkills']*2);
	//      $defflux = e_rand(0,($session['user']['dragonkills']*2-$atkflux));
	//      $hpflux = ($session['user']['dragonkills']*2 - ($atkflux+$defflux)) * 5;
	//      $badguy['creatureattack']+=$atkflux;
	//      $badguy['creaturedefense']+=$defflux;
	//      $badguy['creaturehealth']+=$hpflux;

	// First, find out how each dragonpoint has been spent and count those
	// used on attack and defense.
	// Coded by JT, based on collaboration with MightyE
	$points = 0;
	while(list($key,$val)=each($session['user']['dragonpoints'])){
		if ($val=="at" || $val == "de") $points++;
	}
	// Now, add points for hitpoint buffs that have been done by the dragon
	// or by potions!
	$points += (int)(($session['user']['maxhitpoints'] - 150)/5);

	// Okay.. *now* buff the dragon a bit.
	if ($beta)	
		$points = round($points*1.5,0);
	else
		$points = round($points*.75,0);

	$atkflux = e_rand(0, $points);
	$defflux = e_rand(0,$points-$atkflux);
	$hpflux = ($points - ($atkflux+$defflux)) * 5;
	$badguy['creatureattack']+=$atkflux;
	$badguy['creaturedefense']+=$defflux;
	$badguy['creaturehealth']+=$hpflux;
	$session['user']['badguy']=createstring($badguy);
	$battle=true;
}else if($_GET['op']=="prologue1"){
	output("`@Victory!`n`n");
	$flawless = 0;
  	if ($_GET['flawless']) {
		$flawless = 1;
		output("`b`c`&~~ Flawless Fight ~~`0`c`b`n`n");
	}
	output("`2Before you, the great dragon lies immobile, its heavy breathing like acid to your lungs.  ");
	output("You are covered, head to toe, with the foul creature's thick black blood.  ");
	output("The great beast begins to move its mouth.  You spring back, angry at yourself for having been ");
	output("fooled by its ploy of death, and watch for its huge tail to come sweeping your way.  But it does ");
	output("not.  Instead the dragon begins to speak.`n`n");
	output("\"`^Why have you come here mortal?  What have I done to you?`2\" it says with obvious effort.  ");
	output("\"`^Always my kind are sought out to be destroyed.  Why?  Because of stories from distant lands ");
	output("that tell of dragons preying on the weak?  I tell you that these stories come only from misunderstanding ");
	output("of us, and not because we devour your children.`2\"  The beast pauses, breathing heavily before continuing, ");
	output("\"`^I will tell you a secret.  Behind me now are my eggs.  They will hatch, and the young will battle ");
	output("each other.  Only one will survive, but she will be the strongest.  She will quickly grow, and be as ");
	output("powerful as me.`2\"  Breath comes shorter and shallower for the great beast.`n`n");
	output("\"`#Why do you tell me this?  Don't you know that I will destroy your eggs?`2\" you ask.`n`n");
	output("\"`^No, you will not, for I know of one more secret that you do not.`2\"`n`n");
	output("\"`#Pray tell oh mighty beast!`2\"`n`n");
	output("The great beast pauses, gathering the last of its energy.  \"`^Your kind cannot tolerate the blood of ");
	output("my kind.  Even if you survive, you will be a feeble human, barely able to hold a weapon, your mind ");
	output("blank of all that you have learned.  No, you are no threat to my children, for you are already dead!`2\"`n`n");
	output("Realizing that already the edges of your vision are a little dim, you flee from the cave, bound to reach ");
	output("the healer's hut before it is too late.  Somewhere along the way you lose your weapon, and finally you ");
	output("trip on a stone in a shallow stream, sight now limited to only a small circle that seems to float around ");
	output("your head.  As you lay, staring up through the trees, you think that nearby you can hear the sounds of the ");
	output("village.  Your final thought is that although you defeated the dragon, you reflect on the irony that it ");
	output("defeated you.`n`n");
	output("As your vision winks out, far away in the dragon's lair, an egg shuffles to its side, and a small crack ");
	output("appears in its thick leathery skin.");

	if ($flawless) {
		output("`nYou fall forward, and remember at the last moment that you at least managed to grab some of the dragon's treasure, so maybe it wasn't all a total loss.");
	}
	addnav("It is a new day","news.php");
	$sql = "describe accounts";
	$result = db_query($sql) or die(db_error(LINK));
	$hpgain = $session['user']['maxhitpoints'] - ($session['user']['level']*10);
	$nochange=array("acctid"=>1
	             ,"name"=>1
				 ,"sex"=>1
				 ,"password"=>1
				 ,"marriedto"=>1
				 ,"title"=>1
				 ,"login"=>1
				 ,"dragonkills"=>1
				 ,"locked"=>1
				 ,"loggedin"=>1
				 ,"superuser"=>1
				 ,"gems"=>1
				 ,"hashorse"=>1
				 ,"gentime"=>1
				 ,"gentimecount"=>1
				 ,"lastip"=>1
				 ,"uniqueid"=>1
				 ,"dragonpoints"=>1
				 ,"laston"=>1
				 ,"prefs"=>1
				 ,"lastmotd"=>1
				 ,"emailaddress"=>1
				 ,"emailvalidation"=>1
				 ,"gensize"=>1
				 ,"bestdragonage"=>1
				 ,"dragonage"=>1
				 ,"donation"=>1
				 ,"donationspent"=>1
				 ,"donationconfig"=>1
				 ,"bio"=>1
				 ,"charm"=>1
				 ,"banoverride"=>1 // jt
				 ,"referer"=>1 //jt
				 ,"refererawarded"=>1 //jt
				 ,"lastwebvote"=>1
				 ,"ctitle"=>1
				 ,"beta"=>1
				);
	$session['user']['dragonage'] = $session['user']['age'];
	if ($session['user']['dragonage'] <  $session['user']['bestdragonage'] ||
			$session['user']['bestdragonage'] == 0) {
		$session['user']['bestdragonage'] = $session['user']['dragonage'];
	}
	for ($i=0;$i<db_num_rows($result);$i++){
	  $row = db_fetch_assoc($result);
		if ($nochange[$row['Field']]){
		
		}else{
		  $session['user'][$row['Field']] = $row["Default"];
		}
	}
	$session['bufflist'] = array();
	$session['user']['gold']=getsetting("newplayerstartgold",50);

	$newtitle=$titles[$session['user']['dragonkills']][$session['user']['sex']];
	if ($newtitle==""){
	  $newtitle = ($session['user']['sex']?"Goddess":"God");
	}


	$session['user']['gold']+=getsetting("newplayerstartgold",50)*$session['user']['dragonkills'];
	if ($session['user']['gold']>(6*getsetting("newplayerstartgold",50))){
	  $session['user']['gold']=6*getsetting("newplayerstartgold",50);
		$session['user']['gems']+=($session['user']['dragonkills']-5);
	}
	if ($flawless) {
		$session['user']['gold'] += 3*getsetting("newplayerstartgold",50);
		$session['user']['gems'] += 1;
	}
	$session['user']['maxhitpoints']+=$hpgain;
	$session['user']['hitpoints']=$session['user']['maxhitpoints'];
	// Handle custom titles
	if ($session['user']['ctitle'] == "") {
		if ($session['user']['title']!=""){
			$n = $session['user']['name'];
			$x = strpos($n,$session['user']['title']);
			if ($x!==false){
				$regname=substr($n,$x+strlen($session['user']['title']));
				$session['user']['name'] = substr($n,0,$x).$newtitle.$regname;
				$session['user']['title'] = $newtitle;
			}else{
				$regname = $session['user']['name'];
				$session['user']['name'] = $newtitle." ".$session['user']['name'];
				$session['user']['title'] = $newtitle;
			}
		}else{
			$regname = $session['user']['name'];
			$session['user']['name'] = $newtitle." ".$session['user']['name'];
			$session['user']['title'] = $newtitle;
		}
	} else {
		$regname = substr($session['user']['name'], strlen($session['user']['ctitle']));
		$session['user']['title'] = $newtitle;
	}
	while(list($key,$val)=each($session['user']['dragonpoints'])){
		if ($val=="at"){
			$session['user']['attack']++;
		}
		if ($val=="de"){
			$session['user']['defence']++;
		}
	}
	$session['user']['laston']=date("Y-m-d H:i:s",strtotime("-1 day"));
	output("`n`nYou wake up in the midst of some trees.  Nearby you hear the sounds of a village.  ");
	output("Dimly you remember that you are a new warrior, and something of a dangerous Green Dragon that is plaguing ");
	output("the area.  You decide you would like to earn a name for yourself by perhaps some day confronting this ");
	output("vile creature.");
	addnews("`#".$regname." has earned the title `&".$session['user']['title']."`# for having slain the `@Green Dragon`& `^".$session['user']['dragonkills']."`# times!");
	output("`n`n`^You are now known as `&".$session['user']['name']."`^!!");
	output("`n`n`&Because you have slain the dragon ".$session['user']['dragonkills']." times, you start with some extras.  You also keep additional hitpoints you've earned or purchased.`n");
	$session['user']['charm']+=5;
	output("`^You gain FIVE charm points for having defeated the dragon!`n");
	debuglog("slew the dragon and starts with ".$session['user']['gold']." gold and ".$session['user']['gems']." gems");
}

if ($_GET['op']=="run"){
  output("The creature's tail blocks the only exit to its lair!");
	$_GET['op']="fight";
}
if ($_GET['op']=="fight" || $_GET['op']=="run"){
	$battle=true;
}
if ($battle){
  include("battle.php");
	if ($victory){
		$flawless = 0;
		if ($badguy['diddamage'] != 1) $flawless = 1;
		$badguy=array();
		$session['user']['badguy']="";
		$session['user']['dragonkills']++;
		output("`&With a mighty final blow, `@The Green Dragon`& lets out a tremendous bellow and falls to your feet, dead at last.");
		addnews("`&".$session['user']['name']." has slain the hideous creature known as `@The Green Dragon`&.  Across all the lands, people rejoice!");
		addnav("Continue","dragon.php?op=prologue1&flawless=$flawless");
	}else{
		if($defeat){
			addnav("Daily news","news.php");
			$sql = "SELECT taunt FROM taunts ORDER BY rand(".e_rand().") LIMIT 1";
			$result = db_query($sql) or die(db_error(LINK));
			$taunt = db_fetch_assoc($result);
			$taunt = str_replace("%s",($session['user']['sex']?"her":"him"),$taunt['taunt']);
			$taunt = str_replace("%o",($session['user']['sex']?"she":"he"),$taunt);
			$taunt = str_replace("%p",($session['user']['sex']?"her":"his"),$taunt);
			$taunt = str_replace("%x",($session['user']['weapon']),$taunt);
			$taunt = str_replace("%X",$badguy['creatureweapon'],$taunt);
			$taunt = str_replace("%W",$badguy['creaturename'],$taunt);
			$taunt = str_replace("%w",$session['user']['name'],$taunt);
			
			addnews("`%".$session['user']['name']."`5 has been slain when ".($session['user']['sex']?"she":"he")." encountered `@The Green Dragon`5!!!  ".($session[user][sex]?"Her":"His")." bones now litter the cave entrance, just like the bones of those who came before.`n$taunt");
			$session[user][alive]=false;
			debuglog("lost {$session['user']['gold']} gold when they were slain");
			$session['user']['gold']=0;
			$session['user']['hitpoints']=0;
			$session['user']['badguy']="";
			output("`b`&You have been slain by `%$badguy[creaturename]`&!!!`n");
			output("`4All gold on hand has been lost!`n");
			output("You may begin fighting again tomorrow.");
			page_footer();
		}else{
		  fightnav(true,false);
		}
	}
}
page_footer();
?>
