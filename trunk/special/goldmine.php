<?
/* ******************* 
Gold-mine 
Written by Ville Valtokari
******************* */  

$hashorse = $session['user']['hashorse'];
$horsecanenter = 0;
$horsecandie = 0;
$horsecansave = 0;
if ($hashorse) {
	$horsecanenter = $playermount['mine_canenter'];
	// And a 10% chance that they tether their horse anyway.
	if (e_rand(1,10) == 1) $horsecanenter=0;
	if ($horscanenter) {
		// The horse cannot die or save you if it cannot enter.
		$horsecandie = $playermount['mine_candie'];
		$horsecansave = $playermount['mine_cansave'];
	}
}

if ($HTTP_GET_VARS[op]==""){ 
	$session[user][specialinc]="goldmine.php"; 
	output("`2You found an old abandoned mine in the depths of the forest. There is some old mining equipment nearby.`n`n");
	output("`^As you look around you realize that this is going to be a lot of work.  So much so in fact that you will lose a forest fight for the day if you attempt it.`n`n");
	output("`^Looking around a bit more, you do notice what looks like evidence of occasional cave-ins in the mine.`n`n");
	addnav("Mine for gold and gems","forest.php?op=mine"); 
	addnav("Return to the forest","forest.php?op=no");
	addnav("","forest.php?op=mine"); 
	addnav("","forest.php?op=no");

}else if ($HTTP_GET_VARS[op]=="no"){ 
	output("`2Nope you don't have time for this slow way to gain gold and gems so you leave the old mine...`n"); 
	$session[user][specialinc]="";
 
} elseif ($HTTP_GET_VARS[op]=="mine") {
	$session[user][specialinc]="goldmine.php"; 
	if ($session[user][turns]<=0) {
		output("`2You are too tired to mine anymore..`n");
		$session[user][specialinc]=""; 
	} else {
		// Horsecanenter is a percent, so, if rand(1-100) > enterpercent,
		// tether it.  Set enter percent to 0 (the default), to always tether.
		if (e_rand(1, 100) > $horsecanenter) {
			if ($playermount['mine_tethermsg']) {
				output($playermount['mine_tethermsg']);
			} else {
				output("`&Seeing that the mine entrace is too small for your {$playermount['mountname']}`&, you tether it off to the side of the entrance.`n");
			}
		}
		output("`2You pick up the mining equipment and start mining for gold and gems..`n`n");
		$rand = e_rand(1,20);
		switch ($rand){

		  case 1:case 2:case 3:case 4: case 5:
			output("`2After a few hours of hard work you have only found worthless stones and one skull..`n`n");
			output("`^You lose one forest fight while digging.`n`n");
			if ($session[user][turns]>0) $session[user][turns]--;
			$session[user][specialinc]="";
			break;
		  case 6: case 7: case 8:case 9: case 10:
			$gold = e_rand($session[user][level]*5, $session[user][level]*20);
			output("`^After a few hours of hard work, you find $gold gold!`n`n"); 
			$session[user][gold] += $gold; 
			debuglog("found $gold gold in the goldmine");
			output("`^You lose one forest fight while digging.`n`n");
			if ($session[user][turns]>0) $session[user][turns]--;
			$session[user][specialinc]="";
			break;
	      case 11: case 12: case 13: case 14: case 15:
		    $gems = e_rand(1, $session[user][level]/7+1);
			output("`^After a few hours of hard work, you find $gems gems!`n`n");
			$session[user][gems] += $gems;
			debuglog("found $gems gems in the goldmine");
			output("`^You lose one forest fight while digging.`n`n");
			if ($session[user][turns]>0) $session[user][turns]--;
			$session[user][specialinc]="";
			break;
		  case 16: case 17: case 18:
			$gold = e_rand($session[user][level]*10, $session[user][level]*40);
		    $gems = e_rand(1, $session[user][level]/3+1);
			output("`^You have found the mother lode!`n`n");
			output("`^After a few hours of hard work, you find $gems gems and $gold gold!`n`n");
			$session[user][gems] += $gems;
			$session[user][gold] += $gold;
			debuglog("found $gold gold and $gems gems in the goldmine");
			output("`^You lose one forest fight while digging.`n`n");
			if ($session[user][turns]>0) $session[user][turns]--;
			$session[user][specialinc]="";
			break;
		  case 19: case 20:
		  	output("`2After a lot of hard work you believe you have spotted a `&huge`2 gem and some `6gold`2.`n");
			output("`2Anxious to be rich, you rear back and slam the pick home, knowing that the harder you hit, the quicker you will be done....`n");
			output("`7Unfortunatly, you are quickly done in.`n");
			output("Your over-exuberant hit caused a massive cave in.`n");
			// Dwarves are very wiley so will only ever die in the mines
			// infrequently.
			if ($session['user']['race'] != 4) {
				$dead = 1;
				// Non dwarves will survive on luck 10% of the time.
				if (e_rand(1,10) == 1) $dead = 0;
			} else {
				// Dwarves can only die 5% of the time.
				if (e_rand(1,20) == 1) $dead = 1;
			}
			// Now, if the player died, see if their horse save them
			if ($dead) {
				if (e_rand(1,100) < $horsecansave) {
					$dead = 0;
					$horsesave = 1;
				}
			}
			// If we are still dead, see if the horse dies too.
			if ($dead) {
				if (e_rand(1,100) < $horsecandie) $horsedead = 1;
			}

			$session[user][specialinc]="";
			if ($dead == 1) {
				output("You have been crushed under a ton of rock.`n`nPerhaps the next adventurer will recover your body and bury it properly.`n");
				if ($horsedead) {
					if ($playermount['mine_deathmessage'])
						output($playermount['mine_deathmessage']);
					else
						output("Your {$playermount['mountname']}'s bones were buried right alongside yours.");
					$session[user][hashorse] = 0;
					if(isset($session[bufflist]['mount']))
						unset($session[bufflist]['mount']);
				} else {
					if ($horsecanenter) {
						output("Your {$playermount['mountname']} managed to escape being crushed. You know that it is trained to return to the village.`n");
					} else {
						output("Fortunately you left your {$playermount['mountname']} tethered outside.  You know that it is trained to return to the village.`n");
					}
				}
				$exp=$session[user][experience]*0.6;
				output("At least you learned something about mining from this experience and have gained $exp experience.`n`n");
				output("`3You may continue to play tommorrow`n");
				$session[user][experience]+=$exp;
				$session[user][alive]=false;
				$session[user][hitpoints]=0;
			    debuglog("lost {$session['user']['gold']} gold and {$session['user']['gems']} gems by dying in the goldmine");
				$session[user][gold]=0;
				$session[user][gems]=0;
				addnav("Daily News","news.php");
				addnews($session[user][name]." was completely buried when ".($session[user][sex]?"she":"he")." became greedy digging in the mines");
			} else {
				if ($session[user][race] == 4) {
					output("Fortunately your dwarven skill let you escape unscathed.`n");
				} elseif ($horsesave) {
					if ($playermount['mine_savemsg'])
						output($playermount['mine_savemsg']);
					else
						output("Your {$playermount['mountname']} managed to drag you to safety in the nick of time!`n");
				} else {
					output("Through sheer luck you manage to escape the cave-in intact!`n");
				}
				output("Your close call scared you so badly that you cannot face any more opponents today.`n");
				$session[user][turns]=0;
			}
			break;
		}
	}
}
?>
