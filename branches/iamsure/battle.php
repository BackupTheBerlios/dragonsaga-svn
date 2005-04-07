<?php
/*
 * Major MAJOR revamps by JT from logd.dragoncat.net  Frankly I threw out my code and used his.
 *
 */
require_once "common.php";

function activate_buffs($tag) {
	global $session, $badguy;
	reset($session['bufflist']);
	$result = array();
	$result['invulnerable'] = 0;
	$result['dmgmod'] = 1;
	$result['badguydmgmod'] = 1;
	$result['atkmod'] = 1;
	$result['badguyatkmod'] = 1;
	$result['defmod'] = 1;
	$result['badguydefmod'] = 1;
	$result['lifetap'] = array();
	$result['dmgshield'] = array();

	while(list($key,$buff) = each($session['bufflist'])) {
		if (isset($buff['startmsg'])) {
			$msg = $buff['startmsg'];
			$msg = str_replace("{badguy}", $badguy['creaturename'], $msg);
			output("`%$msg`0");
			unset($session['bufflist'][$key]['startmsg']);
		}
		$activate = strpos($buff['activate'], $tag);
		if ($activate !== false) $activate = true; // handle strpos == 0;
		
		// If this should activate now and it hasn't already activated,
		// do the round message and mark it.
		if ($activate && !$buff['used']) {
			// mark it used.
			$session['bufflist'][$key]['used'] = 1;
			// if it has a 'round message', run it.
			if (isset($buff['roundmsg'])) {
				$msg = $buff['roundmsg'];
				$msg = str_replace("{badguy}", $badguy['creaturename'], $msg);
				output("`)$msg`0`n");
			}
		}

		// Now, calculate any effects and run them if needed.
		if (isset($buff['invulnerable'])) {
			$result['invulnerable'] = 1;
		}
		if (isset($buff['atkmod'])) {
			$result['atkmod'] *= $buff['atkmod'];
		}
		if (isset($buff['badguyatkmod'])) {
			$result['badguyatkmod'] *= $buff['badguyatkmod'];
		}
		if (isset($buff['defmod'])) {
			$result['defmod'] *= $buff['defmod'];
		}
		if (isset($buff['badguydefmod'])) {
			$result['badguydefmod'] *= $buff['badguydefmod'];
		}
		if (isset($buff['dmgmod'])) {
			$result['dmgmod'] *= $buff['dmgmod'];
		}
		if (isset($buff['badguydmgmod'])) {
			$result['badguydmgmod'] *= $buff['badguydmgmod'];
		}
		if (isset($buff['lifetap'])) {
			array_push($result['lifetap'], $buff);
		}
		if (isset($buff['damageshield'])) {
			array_push($result['dmgshield'], $buff);
		}
		if (isset($buff['regen']) && $activate) {
			$hptoregen = (int)$buff['regen'];
			$hpdiff = $session['user']['maxhitpoints'] -
			$session['user']['hitpoints'];
			// Don't regen if we are above max hp
			if ($hpdiff < 0) $hpdiff = 0;
			if ($hpdiff < $hptoregen) $hptoregen = $hpdiff;
			$session['user']['hitpoints'] += $hptoregen;
			// Now, take abs value just incase this was a damaging buff
			$hptoregen = abs($hptoregen);
			if ($hptoregen == 0) $msg = $buff['effectnodmgmsg'];
			else $msg = $buff['effectmsg'];
			$msg = str_replace("{badguy}", $badguy['creaturename'], $msg);
			$msg = str_replace("{damage}", $hptoregen, $msg);
			output("`)$msg`0`n");
		}
		if (isset($buff['minioncount']) && $activate) {
			$who = -1;
			if ($buff['maxbadguydamage']  <> 0) {
				$max = $buff['maxbadguydamage'];
				$min = $buff['minbadguydamage'];
				$who = 0;
			} else {
				$max = $buff['maxgoodguydamage'];
				$min = $buff['mingoodguydamage'];
				$who = 1;
			}
			for ($i = 0; $who >= 0 && $i < $buff['minioncount']; $i++) {
				$damage = e_rand($min, $max);
				if ($who == 0) {
					$badguy['creaturehealth'] -= $damage;
				} else if ($who == 1) {
					$session['user']['hitpoints'] -= $damage;
				}
				if ($damage < 0) {
					$msg = $buff['effectfailmsg'];
				} else if ($damage == 0) {
					$msg = $buff['effectnodmgmsg'];
				} else if ($damage > 0) {
					$msg = $buff['effectmsg'];
				}
				if ($msg>"") {
					$msg = str_replace("{badguy}", $badguy['creaturename'], $msg);
					$msg = str_replace("{goodguy}", $session['user']['name'], $msg);
					$msg = str_replace("{damage}", $damage, $msg);
					output("`)$msg`0`n");
				}
			}
		}
	}
	return $result;
}

function process_lifetaps($ltaps, $damage) {
	global $session, $badguy;
	reset($ltaps);
	while(list($key,$buff) = each($ltaps)) {
		$healhp = $session['user']['maxhitpoints'] -
			$session['user']['hitpoints'];
		if ($healhp < 0) $healhp = 0;
		if ($healhp == 0) {
			$msg = $buff['effectnodmgmsg'];
		} else {
			if ($healhp > $damage * $buff['lifetap'])
				$healhp = $damage * $buff['lifetap'];
			if ($healhp < 0) $healhp = 0;
			if ($damage > 0) {
				$msg = $buff['effectmsg'];
			} else if ($damage == 0) {
				$msg = $buff['effectfailmsg'];
			} else if ($damage < 0) {
				$msg = $buff['effectfailmsg'];
			}
		}
		$session['user']['hitpoints'] += $healhp;
		$msg = str_replace("{badguy}",$badguy['creaturename'], $msg);
		$msg = str_replace("{damage}",$healhp, $msg);
		if ($msg > "") output("`)$msg`n");
	}
}

function process_dmgshield($dshield, $damage) {
	global $session, $badguy;
	reset($dshield);
	while(list($key,$buff) = each($dshield)) {
		$realdamage = $damage * $buff['damageshield'];
		if ($realdamage < 0) $realdamage = 0;
		if ($realdamage > 0) {
			$msg = $buff['effectmsg'];
		} else if ($realdamage == 0) {
			$msg = $buff['effectnodmgmsg'];
		} else if ($realdamage < 0) {
			$msg = $buff['effectfailmsg'];
		}
		$badguy['creaturehealth'] -= $realdamage;
		$msg = str_replace("{badguy}",$badguy['creaturename'], $msg);
		$msg = str_replace("{damage}",$realdamage, $msg);
		if ($msg > "") output("`)$msg`n");
	}
}

function expire_buffs() {
	global $session, $badguy;
	reset($session['bufflist']);
	while (list($key, $buff) = each($session['bufflist'])) {
		if ($buff['used']) {
			$session['bufflist'][$key]['used'] = 0;
			$session['bufflist'][$key]['rounds']--;
			if ($session['bufflist'][$key]['rounds'] <= 0) {
				if ($buff['wearoff']) {
					$msg = $buff['wearoff'];
					$msg = str_replace("{badguy}", $badguy['creaturename'], $msg);
					output("`)$msg`n");
				}
				unset($session['bufflist'][$key]);
			}
		}
	}
}


$badguy = createarray($session['user']['badguy']);

if (date("m-d")=="04-01"){
	if (!strpos($badguy['creaturename'],"bork bork")){
		$badguy['creaturename']=$badguy['creaturename']." bork bork";
	}
}

$adjustment = ($session['user']['level']/$badguy['creaturelevel']);
if ($badguy['pvp']) $adjustment=1;

if ($HTTP_GET_VARS[op]=="fight"){
	if ($HTTP_GET_VARS['skill']=="godmode"){
		$session['bufflist']['godmode']=array(
			"name"=>"`&GOD MODE",
			"rounds"=>1,
			"wearoff"=>"You feel mortal again.",
			"atkmod"=>25,
			"defmod"=>25,
			"invulnerable"=>1,
			"startmsg"=>"`n`&You feel godlike`n`n",
			"activate"=>"roundstart"
		);
	}
	if ($HTTP_GET_VARS['skill']=="MP"){
		if ($session['user']['magicuses'] >= $HTTP_GET_VARS['l']){
			$creaturedmg = 0;
			switch($HTTP_GET_VARS['l']){
			case 1:
				$session['bufflist']['mp1'] = array(
					"startmsg"=>"`n`^You begin to regenerate!`n`n",
					"name"=>"`%Regeneration",
					"rounds"=>5,
					"wearoff"=>"You have stopped regenerating",
					"regen"=>$session['user']['level'],
					"effectmsg"=>"You regenerate for {damage} health.",
					"effectnodmgmsg"=>"You have no wounds to regenerate.",
					"activate"=>"roundstart");
				break;
			case 2:
				$session['bufflist']['mp2'] = array(
					"startmsg"=>"`n`^{badguy}`% is clutched by a fist of earth and slammed to the ground!`n`n",
					"name"=>"`%Earth Fist",
					"rounds"=>5,
					"wearoff"=>"The earthen fist crumbles to dust.",
					"minioncount"=>1,
					"effectmsg"=>"A huge fist of earth pummels {badguy} for `^{damage}`) points.",
					"minbadguydamage"=>1,
					"maxbadguydamage"=>$session['user']['level']*3,
					"activate"=>"roundstart"
					);
				break;
			case 3:
				$session['bufflist']['mp3'] = array(
					"startmsg"=>"`n`^Your weapon glows with an unearthly presence.`n`n",
					"name"=>"`%Siphon Life",
					"rounds"=>5,
					"wearoff"=>"Your weapon's aura fades.",
					"lifetap"=>1, //ratio of damage healed to damage dealt
					"effectmsg"=>"You are healed for {damage} health.",
					"effectnodmgmsg"=>"You feel a tingle as your weapon tries to heal your effectivly healthy body.",
					"effectfailmsg"=>"Your weapon wails as you deal no damage to your opponent.",
					"activate"=>"offense,defense",
					);
				break;
			case 5:
				$session['bufflist']['mp5'] = array(
					"startmsg"=>"`n`^Your skin sparkles as you assume an aura of lightning`n`n",
					"name"=>"`%Lightning Aura",
					"rounds"=>5,
					"wearoff"=>"With a fizzle, your skin returns to normal.",
					"damageshield"=>2,
					"effectmsg"=>"{badguy} recoils as lightning arcs out from your skin, hitting for `^{damage}`) damage.",
					"effectnodmg"=>"{badguy} is slightly singed by your lightning, but otherwise unharmed.",
					"effectfailmsg"=>"{badguy} is slightly singed by your lightning, but otherwise unharmed.",
					"activate"=>"offense,defense"
				);
				break;
			}
			$session['user']['magicuses']-=$HTTP_GET_VARS['l'];
		}else{
			$session['bufflist']['mp0'] = array(
				"startmsg"=>"`nYou furrow your brow and call on the powers of the elements.  A tiny flame appears.  {badguy} lights a cigarette from it, giving you a word of thanks before swinging at you again.`n`n",
				"rounds"=>1,
				"activate"=>"roundstart"
			);
		}
	}
	if ($HTTP_GET_VARS['skill']=="DA"){
		if ($session['user']['darkartuses'] >= $HTTP_GET_VARS['l']){
			$creaturedmg = 0;
			switch($HTTP_GET_VARS['l']){
			case 1:
				$session['bufflist']['da1']=array(
					"startmsg"=>"`n`\$You call on the spirits of the dead, and skeletal hands claw at {badguy} from beyond the grave.`n`n",
					"name"=>"`\$Skeleton Crew",
					"rounds"=>5,
					"wearoff"=>"Your skeleton minions crumble to dust.",
					"minioncount"=>round($session['user']['level']/3)+1,
					"maxbadguydamage"=>round($session['user']['level']/2,0)+1,
					"effectmsg"=>"`)An undead minion hits {badguy} for `^{damage}`) damage.",
					"effectnodmgmsg"=>"`)An undead minion tries to hit {badguy} but `\$MISSES`)!",
					"activate"=>"roundstart"
					);
				break;
			case 2:
				$session['bufflist']['da2']=array(
					"startmsg"=>"`n`\$You pull out a tiny doll that looks like {badguy}`n`n",
					"effectmsg"=>"You thrust a pin into the {badguy} doll hurting it for `^{damage}`) points!",
					"minioncount"=>1,
					"maxbadguydamage"=>round($session['user']['attack']*3,0),
					"minbadguydamage"=>round($session['user']['attack']*1.5,0),
					"activate"=>"roundstart"
					);
				break;
			case 3:
				$session['bufflist']['da3']=array(
					"startmsg"=>"`n`\$You place a curse on {badguy}'s ancestors.`n`n",
					"name"=>"`\$Curse Spirit",
					"rounds"=>5,
					"wearoff"=>"Your curse has faded.",
					"badguydmgmod"=>0.5,
					"roundmsg"=>"{badguy} staggers under the weight of your curse, and deals only half damage.",
					"activate"=>"defense"
					);
				break;
			case 5:
				$session['bufflist']['da5']=array(
					"startmsg"=>"`n`\$You hold out your hand and {badguy} begins to bleed from its ears.`n`n",
					"name"=>"`\$Wither Soul",
					"rounds"=>5,
					"wearoff"=>"Your victim's soul has been restored.",
					"badguyatkmod"=>0,
					"badguydefmod"=>0,
					"roundmsg"=>"{badguy} claws at its eyes, trying to release its own soul, and cannot attack or defend.",
					"activate"=>"offense,defense"
					);
				break;
			}
			$session['user']['darkartuses']-=$HTTP_GET_VARS['l'];
		}else{
			$session['bufflist']['da0'] = array(
				"startmsg"=>"`nExhausted, you try your darkest magic, a bad joke.  {badguy} looks at you for a minute, thinking, and finally gets the joke.  Laughing, it swings at you again.`n`n",
				"rounds"=>1,
				"activate"=>"roundstart"
				);
		}
	}
	if ($HTTP_GET_VARS['skill']=="TS"){
		if ($session['user']['thieveryuses'] >= $HTTP_GET_VARS['l']){
			$creaturedmg = 0;
			switch($HTTP_GET_VARS['l']){
			case 1:
				$session['bufflist']['ts1']=array(
					"startmsg"=>"`n`^You call {badguy} a bad name, making it cry.`n`n",
					"name"=>"`^Insult",
					"rounds"=>5,
					"wearoff"=>"Your victim stops crying and wipes its nose.",
					"roundmsg"=>"{badguy} feels dejected and cannot attack as well.",
					"badguyatkmod"=>0.5,
					"activate"=>"defense"
					);
				break;
			case 2:
				$session['bufflist']['ts2']=array(
					"startmsg"=>"`n`^You apply some poison to your ".$session['user']['weapon'].".`n`n",
					"name"=>"`^Poison Attack",
					"rounds"=>5,
					"wearoff"=>"Your victim's blood has washed the poison from your blade.",
					"atkmod"=>2,
					"roundmsg"=>"Your attack is multiplied!",
					"activate"=>"offense"
					);
				break;
			case 3:
				$session['bufflist']['ts3'] = array(
					"startmsg"=>"`n`^With the skill of an expert thief, you virtually dissapear, and attack {badguy} from a safer vantage point.`n`n",
					"name"=>"`^Hidden Attack",
					"rounds"=>5,
					"wearoff"=>"Your victim has located you.",
					"roundmsg"=>"{badguy} cannot locate you.",
					"badguyatkmod"=>0,
					"activate"=>"defense"
					);
				break;
			case 5:
				$session['bufflist']['ts5']=array(
					"startmsg"=>"`n`^Using your skills as a thief, dissapear behind {badguy} and slide a thin blade between its vertibrae!`n`n",
					"name"=>"`^Backstab",
					"rounds"=>5,
					"wearoff"=>"Your victim won't be so likely to let you get behind it again!",
					"atkmod"=>3,
					"defmod"=>3,
					"roundmsg"=>"Your attack is multiplied, as is your defense!",
					"activate"=>"offense,defense"
					);
				break;
			}
			$session['user']['thieveryuses']-=$HTTP_GET_VARS['l'];
		}else{
			$session['bufflist']['ts0'] = array(
				"startmsg"=>"`nYou try to attack {badguy} by putting your best thievery skills in to practice, but instead, you trip over your feet.`n`n",
				"rounds"=>1,
				"activate"=>"roundstart"
				);
		}
	}
}
	
if ($badguy['creaturehealth']>0 && $session['user']['hitpoints']>0) {
	output ("`\$`c`b~ ~ ~ Fight ~ ~ ~`b`c`0");
	
	output("`@You have encountered `^".$badguy['creaturename']."`@ which lunges at you with `%".$badguy['creatureweapon']."`@!`0`n`n");
	if ($session['user']['alive']){
		output("`2Level: `6".$badguy['creaturelevel']."`0`n");
	}else{
		output("`2Level: `6Undead`0`n");
	}

	output("`2`bStart of round:`b`n");
	output("`2".$badguy['creaturename']."`2's ".($session['user']['alive']?"Hitpoints":"Soulpoints").": `6".$badguy['creaturehealth']."`0`n");
	output("`2YOUR ".($session['user']['alive']?"Hitpoints":"Soulpoints").": `6".$session['user']['hitpoints']."`0`n");
}

reset($session['bufflist']);
while (list($key,$buff)=each($session['bufflist'])){
	// reset the 'used this round state'
	$buff['used']=0;
}

if ($badguy['pvp'] &&
	count($session['bufflist'])>0 &&
	is_array($session['bufflist'])) {
	if ($session['user']['buffbackup']>""){
		
	}else{
		output("`&The gods have suspended any special effects!`n");
		$session['user']['buffbackup']=serialize($session['bufflist']);
		$session['bufflist']=array();
		if ($_GET['bg']==1){
			$session['bufflist']['bodyguard'] = array(
				"startmsg"=>"`n`\${$badguy['creaturename']}'s bodyguard protects them!`n`n",
				"name"=>"`&Bodyguard",
				"rounds"=>5,
				"wearoff"=>"The bodyguard seems to have fallen asleep.",
				"minioncount"=>1,
				"maxgoodguydamage"=> round($session['user']['level']/2,0) +1,
				"effectmsg"=>"`7{badguy}'s bodyguard hits you for `\${damage}`7 damage.",
				"effectnodmgmsg"=>"`7{badguy}'s bodyguard tries to hit you but `\$MISSES`7!",
				"activate"=>"roundstart"
				);
		}
	}
}
// Run the beginning of round buffs (this also calculates all modifiers)
$buffset = activate_buffs("roundstart");

$creaturedefmod=$buffset['badguydefmod'];
$creatureatkmod=$buffset['badguyatkmod'];
$atkmod=$buffset['atkmod'];
$defmod=$buffset['defmod'];

if ($badguy['creaturehealth']>0 && $session['user']['hitpoints']>0){
	if ($badguy['pvp']) {
		$adjustedcreaturedefense = $badguy['creaturedefense'];
	} else {
		$adjustedcreaturedefense =
			 ($creaturedefmod*$badguy['creaturedefense'] /
			 ($adjustment*$adjustment));
	}
	$creatureattack = $badguy['creatureattack']*$creatureatkmod;
	$adjustedselfdefense = ($session['user']['defence'] * $adjustment * $defmod);
	
	while($creaturedmg==0 && $selfdmg==0){//---------------------------------
		$atk = $session['user']['attack']*$atkmod;
		if (e_rand(1,20)==1) $atk*=3;
		$patkroll = e_rand(0,$atk);
		$catkroll = e_rand(0,$adjustedcreaturedefense);
		$creaturedmg = 0-(int)($catkroll - $patkroll);
		if ($creaturedmg<0) {
			//output("`#DEBUG: Initial (<0) creature damage $creaturedmg`n");
			$creaturedmg = (int)($creaturedmg/2);
			//output("`#DEBUG: Modified (<0) creature damage $creaturedmg`n");
			$creaturedmg = round($buffset['badguydmgmod']*$creaturedmg,0);
			//output("`#DEBUG: Modified (<0) creature damage $creaturedmg`n");
		}
		if ($creaturedmg > 0) {
			//output("`#DEBUG: Initial (>0) creature damage $creaturedmg`n");
			$creaturedmg = round($buffset['dmgmod']*$creaturedmg,0);
			//output("`#DEBUG: Modified (>0) creature damage $creaturedmg`n");
		}
		//output("`#DEBUG: Attack score: $atk`n");
		//output("`#DEBUG: Creature Defense Score: $adjustedcreaturedefense`n");
		//output("`#DEBUG: Player Attack roll: $patkroll`n");
		//output("`#DEBUG: Creature Defense roll: $catkroll`n");
		//output("`#DEBUG: Final Creature Damage: $creaturedmg`n");
		$pdefroll = e_rand(0,$adjustedselfdefense);
		$catkroll = e_rand(0,$creatureattack);
		$selfdmg = 0-(int)($pdefroll - $catkroll);
		if ($selfdmg<0) {
			//output("`#DEBUG: Initial (<0) self damage $selfdmg`n");
			$selfdmg=(int)($selfdmg/2);
			//output("`#DEBUG: Modified (<0) self damage $selfdmg`n");
			$selfdmg = round($selfdmg*$buffset['dmgmod'], 0);
			//output("`#DEBUG: Modified (<0) self damage $selfdmg`n");
		}
		if ($selfdmg > 0) {
			//output("`#DEBUG: Initial (>0) self damage $selfdmg`n");
			$selfdmg = round($selfdmg*$buffset['badguydmgmod'], 0);
			//output("`#DEBUG: Modiied (>0) self damage $selfdmg`n");
		}
		//output("`#DEBUG: Defense score: $adjustedselfdefense`n");
		//output("`#DEBUG: Creature Attack score: $creatureattack`n");
		//output("`#DEBUG: Player Defense roll: $pdefroll`n");
		//output("`#DEBUG: Creature Attack roll: $catkroll`n");
		//output("`#DEBUG: Final Player damage: $selfdmg`n");
	}
}else{
	$creaturedmg=0;
	$selfdmg=0;
}
// Handle god mode's invulnerability
if ($buffset['invulnerable']) {
	$creaturedmg = abs($creaturedmg);
	$selfdmg = -abs($selfdmg);
}

if (e_rand(1,3)==1 &&
	($HTTP_GET_VARS[op]=="search" ||
	 ($badguy['pvp'] && $HTTP_GET_VARS['act']=="attack"))) {
	if ($badguy['pvp']){
		output("`b`^".$badguy['creaturename']."`\$'s skill allows them to get the first round of attack!`0`b`n`n");
	}else{
		output("`b`^".$badguy['creaturename']."`\$ surprises you and gets the first round of attack!`0`b`n`n");
	}
	$HTTP_GET_VARS['op']="run";
	$surprised=true;
}else{
	if ($HTTP_GET_VARS['op']=="search")
		output("`b`\$Your skill allows you to get the first attack!`0`b`n`n");
	$surprised=false;
}

if ($HTTP_GET_VARS['op']=="fight" || $HTTP_GET_VARS['op']=="run"){
	if ($HTTP_GET_VARS['op']=="fight"){
		if ($badguy['creaturehealth']>0 && $session['user']['hitpoints']>0){
			$buffset = activate_buffs("offense");
			if ($atk > $session['user']['attack']) {
				if ($atk > $session['user']['attack']*3){
					if ($atk>$session['user']['attack']*4){
						output("`&`bYou execute a <font size='+1'>MEGA</font> power move!!!`b`n",true);
					}else{
						output("`&`bYou execute a DOUBLE power move!!!`b`n");
					}
				}else{
					if ($atk>$session['user']['attack']*2){
						output("`&`bYou execute a power move!!!`b`0`n");
					}elseif ($atk>$session['user']['attack']*1.25){
						output("`7`bYou execute a minor power move!`b`0`n");
					}
				}
			}
			if ($creaturedmg==0){
				output("`4You try to hit `^".$badguy['creaturename']."`4 but `\$MISS!`n");
				process_dmgshield($buffset['dmgshield'], 0);
				process_lifetaps($buffset['lifetap'], 0);
			}else if ($creaturedmg<0){
				output("`4You try to hit `^".$badguy['creaturename']."`4 but are `\$RIPOSTED `4for `\$".(0-$creaturedmg)."`4 points of damage!`n");
				$badguy['diddamage']=1;
				$session['user']['hitpoints']+=$creaturedmg;
				process_dmgshield($buffset['dmgshield'],-$creaturedmg);
				process_lifetaps($buffset['lifetap'],$creaturedmg);
			}else{
				output("`4You hit `^".$badguy['creaturename']."`4 for `^$creaturedmg`4 points of damage!`n");
				$badguy['creaturehealth']-=$creaturedmg;
				process_dmgshield($buffset['dmgshield'],-$creaturedmg);
				process_lifetaps($buffset['lifetap'],$creaturedmg);
			}
		}
	}else if($HTTP_GET_VARS[op]=="run" && !$surprised){
		output("`4You are too busy trying to run away like a cowardly dog to try to fight `^".$badguy['creaturename']."`4.`n");
	}
	// We need to check both user health and creature health. Otherwise the user
	 // can win a battle by a RIPOSTE after he has gone <= 0 HP.
	//-- Gunnar Kreitz
	if ($badguy['creaturehealth']>0 && $session['user']['hitpoints']>0){
		$buffset = activate_buffs("defense");
		if ($selfdmg==0){
			output("`^".$badguy['creaturename']."`4 tries to hit you but `\$MISSES!`n");
			process_dmgshield($buffset['dmgshield'], 0);
			process_lifetaps($buffset['lifetap'], 0);
		}else if ($selfdmg<0){
			output("`^".$badguy['creaturename']."`4 tries to hit you but you `^RIPOSTE`4 for `^".(0-$selfdmg)."`4 points of damage!`n");
			$badguy['creaturehealth']+=$selfdmg;
			process_lifetaps($buffset['lifetap'], -$selfdmg);
			process_dmgshield($buffset['dmgshield'], $selfdmg);
		}else{
			output("`^".$badguy['creaturename']."`4 hits you for `\$$selfdmg`4 points of damage!`n");
			$session['user']['hitpoints']-=$selfdmg;
			process_dmgshield($buffset['dmgshield'], $selfdmg);
			process_lifetaps($buffset['lifetap'], -$selfdmg);
			$badguy['diddamage']=1;
		}
	}
}
expire_buffs();

if ($session['user']['hitpoints']>0 &&
	$badguy['creaturehealth']>0 &&
	($HTTP_GET_VARS['op']=="fight" || $HTTP_GET_VARS['op']=="run")){
	output("`2`bEnd of Round:`b`n");
	output("`2".$badguy['creaturename']."`2's ".($session['user']['alive']?"Hitpoints":"Soulpoints").": `6".$badguy['creaturehealth']."`0`n");
	output("`2YOUR ".($session['user']['alive']?"Hitpoints":"Soulpoints").": `6".$session['user']['hitpoints']."`0`n");
}
if ($badguy['creaturehealth']<=0){
	$victory=true;
	$defeat=false;
}else{
	if ($session['user']['hitpoints']<=0){
		$defeat=true;
		$victory=false;
	}else{
		$defeat=false;
		$victory=false;
	}
}
if ($victory || $defeat){
	// Unset the bodyguard buff at the end of the fight.
	// Without this, the bodyguard persists *and* the older buffs are held
	// off for a while! :/
	if (isset($session['bufflist']['bodygaurd']))
		unset($session['bufflist']['bodyguard']);
	if (!is_array($session['bufflist']) || count($session['bufflist']) <= 0) {
		$session['bufflist'] = unserialize($session['user']['buffbackup']);
		if (is_array($session['bufflist'])) {
			if (count($session['bufflist'])>0 && $badguy[pvp])
				output("`&The gods have restored your special effects.`n`n");
		} else {
			$session['bufflist'] = array();
		}
	}
	$session['user']['buffbackup'] = "";
}
$session['user']['badguy']=createstring($badguy);
?>
