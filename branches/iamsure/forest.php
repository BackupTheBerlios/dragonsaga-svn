<?php
// This program is free software; you can redistribute it and/or modify it
// under the terms of the GNU General Public License as published by the
// Free Software Foundation; either version 2 of the License, or (at your
// option) any later version.
//
// File: forest.php

require_once "common.php";
$balance = getsetting("creaturebalance", 0.33);
if (!isset($_GET['op']))
{
    $_GET['op'] = '';
}

if (!isset($_GET['type']))
{
    $_GET['type'] = '';
}

// Handle updating any commentary that might be around.
addcommentary();

//savesetting("creaturebalance","0.33");
if ($_GET['op']=="darkhorse"){
	$_GET['op']="";
	$session['user']['specialinc']="darkhorse.php";
}
$fight = false;
page_header("The Forest");
if (!isset($_GET['specialinc']))
{
    $_GET['specialinc'] = '';
}
if ($session['user']['superuser']>1 && $_GET['specialinc']!=""){
  $session['user']['specialinc'] = $_GET['specialinc'];
}
if ($session['user']['specialinc']!=""){
  //echo "DEBUG:$x including special/".$session['user']['specialinc'];
	
	output("`^`c`bSomething Special!`c`b`0");
	$specialinc = $session['user']['specialinc'];
	$session['user']['specialinc'] = "";
	include("special/".$specialinc);
	if (!is_array($session['allowednavs']) || count($session['allowednavs'])==0) {
		forest(true);
		//output(serialize($session['allowednavs']));
	}
	page_footer();
	exit();
}
if ($_GET['op']=="run"){
	if (e_rand()%3 == 0){
		output ("`c`b`&You have successfully fled your oponent!`0`b`c`n");
		$_GET['op']="";
	}else{
		output("`c`b`\$You failed to flee your oponent!`0`b`c");
	}
}
if ($_GET['op']=="dragon"){
	addnav("Enter the cave","dragon.php");
	addnav("Run away like a baby","inn.php");
	output("`\$You approach the blackened entrance of a cave deep in the forest, though ");
	output(" the trees are scorched to stumps for a hundred yards all around.  ");
	output("A thin tendril of smoke escapes the roof of the cave's entrance, and is whisked away ");
	output("by a suddenly cold and brisk wind.  The mouth of the cave lies up a dozen ");
	output("feet from the forest floor, set in the side of a cliff, with debris making a ");
	output("conical ramp to the opening.  Stalactites and stalagmites near the entrance ");
	output("trigger your imagination to inspire thoughts that the opening is really ");
	output("the mouth of a great leach.  ");
	output("`n`nYou cautiously approach the entrance of the cave, and as you do, you hear, ");
	output("or perhaps feel a deep rumble that lasts thirty seconds or so, before silencing ");
	output("to a breeze of sulfur-air which wafts out of the cave.  The sound starts again, and stops ");
	output("again in a regular rhythm.  ");
	output("`n`nYou clamber up the debris pile leading to the mouth of the cave, your feet crunching ");
	output("on the apparent remains of previous heroes, or perhaps hors d'ouvers.");
	output("`n`nEvery instinct in your body wants to run, and run quickly, back to the warm inn, and ");
	output("the even warmer ".($session['user']['sex']?"Seth":"Violet").".  What do you do?");
	$session['user']['seendragon']=1;
}
if ($_GET['op']=="search"){
	checkday();
  if ($session['user']['turns']<=0){
    output("`\$`bYou are too tired to search the forest any longer today.  Perhaps tomorrow you will have more energy.`b`0");
    $_GET['op']="";
  }else{
	  $session['user']['drunkenness']=round($session['user']['drunkenness']*.9,0);
  	$specialtychance = e_rand()%7;
  	if ($specialtychance==0){
  		output("`^`c`bSomething Special!`c`b`0");
			if ($handle = opendir("special")){
			  $events = array();
			  while (false !== ($file = readdir($handle))){
				  if (strpos($file,".php")>0){
				  	  // Skip the darkhorse if the horse knows the way
					  if ($session['user']['hashorse'] > 0 && 
					  	  $playermount['tavern'] > 0 &&
						  strpos($file, "darkhorse") !== false) {
					  	continue;
					  }
					  array_push($events,$file);
					}
				}
				$x = e_rand(0,count($events)-1);
				if (count($events)==0){
				  output("`b`@Aww, your administrator has decided you're not allowed to have any special events.  Complain to them, not me.");
				}else{
				  $y = $_GET['op'];
					$_GET['op']="";
				  //echo "DEBUG:$x including special/".$events[$x];
				  include("special/".$events[$x]);
					$_GET['op']=$y;
				}
			}else{
			  output("`c`b`\$ERROR!!!`b`c`&Unable to open the special events!  Please notify the administrator!!");
			}
  		if ($nav=="") forest(true);
  	}else{
      $session['user']['turns']--;
  		$battle=true;
			if (e_rand(0,2)==1){
				$plev = (e_rand(1,5)==1?1:0);
				$nlev = (e_rand(1,3)==1?1:0);
			}else{
			  $plev=0;
				$nlev=0;
			}
			if ($_GET['type']=="slum"){
			  $nlev++;
				output("`\$You head for the section of forest you know to contain foes that you're a bit more comfortable with.`0`n");
			}
			if ($_GET['type']=="thrill"){
			  $plev++;
				output("`\$You head for the section of forest which contains creatures of your nightmares, hoping to find one of them injured.`0`n");
			}
			$targetlevel = ($session['user']['level'] + $plev - $nlev );
			if ($targetlevel<1) $targetlevel=1;
			$sql = "SELECT * FROM creatures WHERE creaturelevel = $targetlevel ORDER BY rand(".e_rand().") LIMIT 1";
			$result = db_query($sql) or die(db_error(LINK));
			$badguy = db_fetch_assoc($result);
			$expflux = round($badguy['creatureexp']/10,0);
			$expflux = e_rand(-$expflux,$expflux);
			$badguy['creatureexp']+=$expflux;

			//make badguys get harder as you advance in dragon kills.
			//output("`#Debug: badguy gets `%$dk`# dk points, `%+$atkflux`# attack, `%+$defflux`# defense, +`%$hpflux`# hitpoints.`n");
			$badguy['playerstarthp']=$session['user']['hitpoints'];
			$dk = 0;
			while(list($key, $val)=each($session['user']['dragonpoints'])) {
				if ($val=="at" || $val=="de") $dk++;
			}
			$dk += (int)(($session['user']['maxhitpoints']-
				($session['user']['level']*10))/5);
			if (!$beta) $dk = round($dk * 0.25, 0);
			else $dk = round($dk,0);

			$atkflux = e_rand(0, $dk);
			if ($beta) $atkflux = min($atkflux, round($dk/4));
			$defflux = e_rand(0, ($dk-$atkflux));
			if ($beta) $defflux = min($defflux, round($dk/4));
			$hpflux = ($dk - ($atkflux+$defflux)) * 5;
			$badguy['creatureattack']+=$atkflux;
			$badguy['creaturedefense']+=$defflux;
			$badguy['creaturehealth']+=$hpflux;
			if ($beta) {
				$badguy['creaturedefense']*=0.66;
				$badguy['creaturegold']*=(1+(.05*$dk));
				if ($session['user']['race']==4) $badguy['creaturegold']*=1.1;
			} else {
				if ($session['user']['race']==4) $badguy['creaturegold']*=1.2;
			}
			$badguy['diddamage']=0;
			$session['user']['badguy']=createstring($badguy);
			if ($beta) {
				if ($session['user']['superuser']>=3){
					output("Debug: $dk dragon points.`n");
					output("Debug: +$atkflux attack.`n");
					output("Debug: +$defflux defense.`n");
					output("Debug: +$hpflux health.`n");
				} 
			}
		}
	}
}
if ($_GET['op']=="fight" || $_GET['op']=="run"){
	$battle=true;
}
if (!isset($battle))
{
    $battle = '';
}
if ($battle){
  include("battle.php");
//	output(serialize($badguy));
	if ($victory){
		if (getsetting("dropmingold",0)){
			$badguy['creaturegold']=e_rand($badguy['creaturegold']/4,3*$badguy['creaturegold']/4);
		}else{
			$badguy['creaturegold']=e_rand(0,$badguy['creaturegold']);
		}
		$expbonus = round(
			($badguy['creatureexp'] *
				(1 + .25 *
					($badguy['creaturelevel']-$session['user']['level'])
				)
			) - $badguy['creatureexp'],0
		);
		output("`b`&".$badguy['creaturelose']."`0`b`n");
		output("`b`\$You have slain ".$badguy['creaturename']."!`0`b`n");
		output("`#You receive `^".$badguy['creaturegold']."`# gold!`n");
		if ($badguy['creaturegold']) {
			debuglog("received ".$badguy['creaturegold']." gold for slaying a monster.");
		}
		if (e_rand(1,25) == 1) {
		  output("`&You find A GEM!`n`#");
		  $session['user']['gems']++;
		  debuglog("found a gem when slaying a monster.");
		}
		if ($expbonus>0){
		  output("`#***Because of the difficult nature of this fight, you are awarded an additional `^$expbonus`# experience! `n($badguy[creatureexp] + ".abs($expbonus)." = ".($badguy['creatureexp']+$expbonus).") ");
		}else if ($expbonus<0){
		  output("`#***Because of the simplistic nature of this fight, you are penalized `^".abs($expbonus)."`# experience! `n($badguy[creatureexp] - ".abs($expbonus)." = ".($badguy['creatureexp']+$expbonus).") ");
		}
		output("You receive `^".($badguy['creatureexp']+$expbonus)."`# total experience!`n`0");
		$session['user']['gold']+=$badguy['creaturegold'];
		$session['user']['experience']+=($badguy['creatureexp']+$expbonus);
		$creaturelevel = $badguy['creaturelevel'];
		$_GET['op']="";
		//if ($session[user][hitpoints] == $session[user][maxhitpoints]){
		if ($badguy['diddamage']!=1){
			if ($session['user']['level']>=getsetting("lowslumlevel",4) || $session['user']['level']<=$creaturelevel){
				output("`b`c`&~~ Flawless Fight! ~~`\$`n`bYou receive an extra turn!`c`0`n");
				$session['user']['turns']++;
			}else{
				output("`b`c`&~~ Flawless Fight! ~~`b`\$`nA more difficult fight would have yielded an extra turn.`c`n`0");
			}
		}
		$dontdisplayforestmessage=true;
		addhistory(($badguy['playerstarthp']-$session['user']['hitpoints'])/max($session['user']['maxhitpoints'],$badguy['playerstarthp']));
		$badguy=array();
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

			addnews("`%".$session['user']['name']."`5 has been slain in the forest by ".$badguy['creaturename']."`n$taunt");
			$session['user']['alive']=false;
			debuglog("lost ".$session['user']['gold']." gold when they were slain in the forest");
			$session['user']['gold']=0;
			$session['user']['hitpoints']=0;
			$session['user']['experience']=round($session['user']['experience']*.9,0);
			$session['user']['badguy']="";
			output("`b`&You have been slain by `%".$badguy['creaturename']."`&!!!`n");
			output("`4All gold on hand has been lost!`n");
			output("`410% of experience has been lost!`n");
			output("You may begin fighting again tomorrow.");
			
			page_footer();
		}else{
		  fightnav();
		}
	}
}

function addhistory($value){
/*
        global $session,$balance;
        $history = unserialize($session['user']['history']);
        $historycount=50;
        for ($x=0;$x<$historycount;$x++){
                if (!isset($history[$x])) $history[$x]=$balance;
        }
        array_shift($history);
        array_push($history,$value);
        $history = array_values($history);
        for ($x=0;$x<$historycount;$x++){
                $history[$x] = round($history[$x],4);
                if ($session['user']['superuser']>=3) output("History: {$history[$x]}`n");
        }
        $session['user']['history']=serialize($history);
 */
}

if (!isset($dontdisplayforestmessage))
{
    $dontdisplayforestmessage = '';
}
if ($_GET['op']==""){
	// Need to pass the variable here so that we show the forest message
	// sometimes, but not others.
	forest($dontdisplayforestmessage);
}
page_footer();
?>
