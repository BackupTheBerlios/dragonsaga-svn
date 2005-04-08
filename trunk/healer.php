<?php
require_once "common.php";
$config = unserialize($session['user']['donationconfig']);
if ($config['healer']) $golinda = 1;

if ($golinda) {
	page_header("Golinda's Hut");
	output("`#`b`cGolinda's Hut`c`b`n");
} else {
	page_header("Healer's Hut");
	output("`#`b`cHealer's Hut`c`b`n");
}
$loglev = log($session['user']['level']);
$cost = ($loglev * ($session['user']['maxhitpoints']-$session['user']['hitpoints'])) + ($loglev*10);
if ($golinda) $cost *= .5;
$cost = round($cost,0);

if ($HTTP_GET_VARS['op']==""){
  	checkday();
	if ($golinda) {
		output("`3A very petite and beautiful brunette looks up as you enter.  \"`6Ahh.. You must be {$session['user']['name']}.`6  I was told to expect you.  Come in.. come in!`3\" she exclaims.`n`nYou make your way deeper into the hut.`n`n");
	} else {
		output("`3You duck in to the small smoke filled grass hut.  The pungent aroma makes you cough, attracting the attention of a grizzled old person that does a remarkable job of reminding you of a rock, which probably explains why you didn't notice them until now.  Couldn't be your failure as a warrior.  Nope, definitely not.`n`n");
	}
	if ($session['user']['hitpoints'] < $session['user']['maxhitpoints']){
		if ($golinda) {
			output("`3\"`6Now.. Let's see here.  Hmmm. Hmmm. You're a bit banged up it seems.`3\"`n`n\"`5Uh, yeah.  I guess.  What will this cost me?`3\" you ask, looking sheepish. \"`5I don't normally get this hurt you know.`3\"`n`n\"`6I know.  I know.  None of you `^ever`6 does.  Anyhow, I can set you right as rain for `$`b$cost`b`6 gold pieces.  I can also give you partial doses at a lower price if you cannot afford a full potion,`3\" says Golinda, smiling.");
		} else {
			output("\"`6See you, I do.  Before you did see me, I think, hmm?`3\" the old thing remarks.  \"`6Know you, I do; healing you seek.  Willing to heal am I, but only if willing to pay are you.`3\"`n`n\"`5Uh, um.  How much?`3\" you ask, ready to be rid of the smelly old thing.`n`nThe old being thumps your ribs with a gnarly staff.  \"`6For you... `$`b$cost`b`6 gold pieces for a complete heal!!`3\" it says as it bends over and pulls a clay vial from behind a pile of skulls sitting in the corner.  The view of the thing bending over to remove the vial almost does enough mental damage to require a larger potion.  \"`6I also have some, erm... 'bargain' potions available,`3\" it says as it gestures at a pile of dusty, cracked vials.  \"`6They'll heal a certain percent of your `idamage`i.`3\"");
		}
		addnav("Potions");
		addnav("`^Complete Healing`0","healer.php?op=buy&pct=100");
		for ($i=90;$i>0;$i-=10){
			addnav("$i% - ".round($cost*$i/100,0)."gp","healer.php?op=buy&pct=$i");
		}
		addnav("`bReturn`b");
		addnav("Back to the forest", "forest.php");
		addnav("Back to the village","village.php");
	}else if($session['user']['hitpoints'] == $session['user']['maxhitpoints']){
		if ($golinda) {
			output("`3Golinda looks you over carefully.  \"`6Well, you do have that hangnail there, but other than that, you seem in perfect health! `^I`6 think you just came in here because you were lonely,`3\" she chuckles.`n`nRealizing that she is right, and that you are keeping her from other patients, you wander back out to the forest.");
		} else {
			output("`3The old creature grunts as it looks your way. \"`6Need a potion, you do not.  Wonder why you bother me, I do.`3\" says the hideous thing.  The aroma of its breath makes you wish you hadn't come in here in the first place.  You think you had best leave.");
		}
		forest(true);
	}else{
		if ($golinda) {
			output("`3Golinda looks you over carefully.  \"`6My, my! You don't even have a hangnail for me to fix!  You are a perfect speciman of " . ($session['user']['sex'] == 1 ? "womanhood" : "manhood") . "!  Do come back if you get hurt, please,`3\" she says, turning back to her potion mixing.`n`n\"`6I will,`3\"you stammer, unaccountably embarrased as you head back out to the forest.");
		} else {
			output("`3The old creature glances at you, then in a `^whirlwind of movement`3 that catches you completely off guard, brings its gnarled staff squarely in contact with the back of your head.  You gasp as you collapse to the ground.`n`nSlowly you open your eyes and realize the beast is emptying the last drops of a clay vial down your throat.`n`n\"`6No charge for that potion.`3\" is all it has to say.  You feel a strong urge to leave as quickly as you can.");
			$session['user']['hitpoints'] = $session['user']['maxhitpoints'];
		}
		forest(true);
	}
}else{
	$newcost=round($HTTP_GET_VARS['pct']*$cost/100,0);
	if ($session['user']['gold']>=$newcost){
		$session['user']['gold']-=$newcost;
		debuglog("spent $newcost gold on healing");
		$diff = round(($session['user']['maxhitpoints']-$session['user']['hitpoints'])*$HTTP_GET_VARS['pct']/100,0);
		$session['user']['hitpoints'] += $diff;
		if ($golinda) {
			output("`3Expecting a foul concoction you begin to up-end the potion.  As it slides down your throat however, you taste cinnamon, honey, and a fruit flavor.  You feel warmth spread throughout your body as your muscles knit themselves back together.  Clear-headed and feeling much better, you hand Golinda the gold you owe and head back to the forest.");
		} else {
			output("`3With a grimace, you up-end the potion the creature hands you, and despite the foul flavor, you feel a warmth spreading through your veins as your muscles knit back together.  Staggering some, you hand it your gold and are ready to be out of here.");
		}
		output("`n`n`#You have been healed for $diff points!");
		forest(true);
	}else{
		if ($golinda) {
			output("`3\"`6Tsk, tsk!`3\" Golinda murmers.  \"`6Maybe you should go visit the Bank and return when you have `b`\$$newcost`6`b gold?`3\" she asks.`n`nYou stand there feeling sheepish for having wasted her time.`n`n\"`6Or maybe a cheaper potion would suit you better?`3\" she suggests kindly.");
		} else {
			output("`3The old creature pierces you with a gaze hard and cruel.  Your lightning quick reflexes enable you to dodge the blow from its gnarled staff.  Perhaps you should get some more money before you attempt to engage in local commerce.`n`nYou recall that the creature had asked for `b`\$$newcost`3`b gold.");
		}
		addnav("Potions");
		addnav("`^Complete Healing`0","healer.php?op=buy&pct=100");
		for ($i=90;$i>0;$i-=10){
			addnav("$i% - ".round($cost*$i/100,0)."gp","healer.php?op=buy&pct=$i");
		}
		addnav("`bReturn`b");
		addnav("Back to the forest","forest.php");
		addnav("Back to the village","village.php");
	}
}
page_footer();
?>
