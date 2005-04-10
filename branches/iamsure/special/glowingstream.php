<?php
// This program is free software; you can redistribute it and/or modify it
// under the terms of the GNU General Public License as published by the
// Free Software Foundation; either version 2 of the License, or (at your
// option) any later version.
//
// File: glowingstream.php

if ($_GET['op']==""){
  output("`#You discover a small stream of faintly glowing water that babbles over round pure white stones.  You can sense a magical ");
	output("power in the water.  Drinking this water may yield untold powers, or it may result ");
	output("in crippling disability.  Do you wish to take a drink?");
	output("`n`n<a href='forest.php?op=drink'>Drink</a>`n<a href='forest.php?op=nodrink'>Don't drink</a>",true);
	addnav("Drink","forest.php?op=drink");
	addnav("Don't Drink","forest.php?op=nodrink");
	addnav("","forest.php?op=drink");
	addnav("","forest.php?op=nodrink");
	$session[user][specialinc]="glowingstream.php";
}else{
  $session[user][specialinc]="";
	if ($_GET['op']=="drink"){
	  $rand = e_rand(1,10);
		output("`#Knowing that the water could yield deadly, you decide to take your chances.  Kneeling down ");
		output("at the edge of the stream, you take a long hard draught from the cold stream.  You feel a warmth ");
		output("growing out from your chest, ");
		switch ($rand){
		  case 1:
        output("`ifollowed by a dreadful clammy cold`i.  You stagger and claw at your breast as you feel what ");
				output("you imagine to be the hand of the reaper placing its unbreakable grip on your heart.");
				output("`n`nYou collapse by the edge of the stream, only just now noticing that the stones you observed ");
				output("before were actually the bleached skulls of other adventurers as unfortunate as you.");
				output("`n`nDarkness creeps in around the edges of your vision as you lay staring up through ");
				output("the trees.  Your breath comes shallower and less and less frequently ");
				output("as warm sunshine splashes on your face, in sharp contrast to the void taking residence in ");
				output("your heart.");
				output("`n`n`^You have died to the foul power of the stream.`n");
				output("As the woodland creatures know the danger of this place, none are here to scavenge from your corpse, you may keep your gold.`n");
				output("The life lesson learned here balances any experience you would have lost.`n");
				output("You may continue playing again tomorrow.");
				$session[user][alive]=false;
				$session[user][hitpoints]=0;
				addnav("Daily News","news.php");
				addnews($session[user][name]." encountered strange powers in the forest, and was not seen again.");
			break;
			case 2:
        output("`ifollowed by a dreadful clammy cold`i.  You stagger and claw at your breast as you feel what ");
				output("you imagine to be the hand of the reaper placing its unbreakable grip on your heart.");
				output("`n`nYou collapse by the edge of the stream, only just now noticing that the stones you observed ");
				output("before were actually the bleached skulls of other adventurers as unfortunate as you.");
				output("`n`nDarkness creeps in around the edges of your vision as you lay staring up through ");
				output("the trees.  Your breath comes shallower and less and less frequently ");
				output("as warm sunshine splashes on your face, in sharp contrast to the void taking residence in ");
				output("your heart.`n`n");
				output("As you exhale your last breath, you distantly hear a tiny giggle.  You find the strength ");
				output("to open your eyes, and find yourself staring at a tiny fairy who, flying just above your face ");
				output("is inadvertantly sprinkling its fairy dust all over you, granting you the power to crawl once ");
				output("again to your feet.  The lurch to your feet startles the tiny creature, and before you have a ");
				output("chance to thank it, it flits off.");
				output("`n`n`^You narrowly avoid death, you lose a forest fight, and most of your hitpoints.");
				if ($session[user][turns]>0) $session[user][turns]--;
				if ($session[user][hitpoints]>($session[user][hitpoints]*.1)) $session[user][hitpoints]=round($session[user][hitpoints]*.1,0);
			break;
			case 3:
			  output("you feel INVIGORATED!");
				output("`n`n`^Your hitpoints have been restored to full, and you feel the energy for another turn in the forest.");
				if ($session[user][hitpoints]<$session[user][maxhitpoints]) $session[user][hitpoints]=$session[user][maxhitpoints];
				$session[user][turns]++;
				break;
			case 4:
			  output("you feel PERCEPTIVE!  You notice something glittering under one of the pebbles that line the stream.");
				output("`n`n`^You find a GEM!");
				$session[user][gems]++;
				debuglog("found 1 gem by the stream");
				break;
			case 5:
			case 6:
			case 7:
			  output("you feel ENERGETIC!");
				output("`n`n`^You receive an extra forest fight!");
				$session[user][turns]++;
				break;
			default:
			  output("you feel HEALTHY!");
				output("`n`n`^Your hitpoints have been restored to full.");
				if ($session[user][hitpoints]<$session[user][maxhitpoints]) $session[user][hitpoints]=$session[user][maxhitpoints];
		}
	}else{
	  output("`#Fearing the dreadful power in the water, you decide to let it be, and return to the forest.");
	}
}
?>
