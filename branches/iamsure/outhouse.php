<?php
/**
* Version:	0.6
* Date:		July 31, 2003
* Author:	John J. Collins
* Email:	collinsj@yahoo.com
* 
* Purpose:	Provide a fun module to Legend of the Green Dragon
* Program Flow:	The player can choose to use the Private or Public Toilet. It costs Gold
* to use the Private Toilet. The Public Toilet is free. After using one of the toilet's,
* the play can was their hands or return. If they choose to wash their hands, there is a
* chance that they can get their gold back. If they don't choose to wash their hands, there
* is a chance that they will lose some gold. If they loose gold there is an entry added
* to the daily news.
*/
require_once("common.php");

// How much does it cost to use the Private Toilet?
$cost = 5;
// How much gold must user have in hand before they can lose money
$goldinhand = 1;
// How much gold to give back if the player is rewarded for washing their hands
$giveback = 3;
// How much gold to take if the user is punished for not washing their hands
$takeback = 1;
// Minium random number for good habits
$goodminimum = 1;
// Maximum randdom number for good habits
$goodmaximum = 10;
// Odds of getting your money back
$goodmusthit = 6;
// Minimum random number for bad habits
$badminimum = 1;
// Maximum random number for bad habits
$badminimum = 4;
// Odds of losing money
$badmusthit = 2;
// Turn on to give the player a chance of finding a Gem if they visit the Private Toilet and Wash their hands.
// Turn on = 1
// Turn off = 0
$giveagem = 1;
// Give a gem if you visit the pay toilet and wash your hands. 1 in 4 chance of getting the gem.
// How often do you want to give out a Gem?
// Default is 1 out of 4 odds.
$givegempercent = 25;
$gemminimum = 1;
$gemmaximum = 100;
// Do you want to give the player a turn if they use the Pay Toilet and wash their hands.
// 1 give a turn
// 0 does not give an extra turn
$giveaturn = 0;
// Where do you want the player to go after leaving here?
// Usually this is the forest, you don't want no stinking toilet in the village do you, but can be anywhere.
$returnto = "forest.php";
// Does the player have enough gold to use the Private Toilet?


//You should really not have to edit anything below this line!
if ($session[user][gold] >= $cost) $canpay = True;

if ($_GET[op] == "pay"){
	page_header("Private Toilet");
	$session[user][usedouthouse] = 1;
	output("`7You pay your $cost gold to the Toilet Gnome for the privilege of using the paid outhouse.`n");
	output("This is the cleanest outhouse in the land!`n");
	output("The Toilet Paper Gnome tells you if you need anything, just ask`n");
	output (($session[user][sex]?"She":"He") . " politely turns ");
	output (($session[user][sex]?"her":"his") . " back to you and finishes cleaning the wash stand.`n");
	$session[user][gold] -= $cost;
	debuglog("spent $cost gold to use the outhouse");
	addnav("Wash your hands", "outhouse.php?op=washpay");
	addnav("Leave", "outhouse.php?op=nowash");
}elseif ($_GET[op] == "free"){
	page_header("Public Toilet!");
	$session[user][usedouthouse] = 1;
	output("`2The smell is so strong your eyes tear up and your nose hair curls!`n");
	output("After blowing his nose with it, the Toilet Paper Gnome gives you 1 sheet of single-ply TP to use.`n");
	output("After looking at the stuff covering his hands, you think you might not want to use it.`n`n");
	output("While " . ($session[user][sex]?"squating":"standing") . " over the big hole 
	 in the middle of the room with the TP Gnome observing you closeley; you almost slip in.`n");
	output("But you go ahead and take care of busines as fast as you can, you can only hold your breath so long.`n");
	addnav("Wash your hands", "outhouse.php?op=washfree");
	addnav("Leave", "outhouse.php?op=nowash");
}elseif ($_GET[op] == "washpay"|| $_GET[op] == "washfree"){
	page_header("Wash Stand");
	output("`2Washing your hands is always a good thing.  You tidy up, straighten your {$session['user']['armor']} in your reflection in the water, and head on your way.`0`n");
	$goodhabits = e_rand($goodminimum, $goodmaximum);
	if ($goodhabits > $musthit && $_GET['op']=="washpay"){
		output("`^The Wash Room Fairy blesses you!`n");
		output("`7You receive `^$giveback `7gold for being sanitary and clean!`0`n");
		$session['user']['gold'] += $giveback;
		debuglog("got $giveback gold in the outhouse for washing");
		if ($giveagem == 1){
			$givegemtemp = e_rand($gemminimum, $gemmaximum);
			if ($givegemtemp <= $givegempercent){
				$session[user][gems]++;
				debuglog("gained 1 gem in the outhouse");
				output("`&Aren't you the lucky one to find a gem there by the doorway!`0`n");
			}
			if ($giveaturn == 1){
				$session[user][turns]++;
				output("`&You gained a turn!`0`n");
			}
			if ($session['user']['drunkenness']>0){
				$session[user][drunkenness] *= .9;
				output("`&Leaving the outhouse, you feel a little more sober!`n`0");
			}
		}
	}elseif ($goodhabits > $musthit && $_GET[op] == "washfree"){
		if (e_rand(1, 3)==1) {
			output("`&You notice a small bag containing `^$giveback `7gold that someone left by the washstand.`0");
			$session['user']['gold'] += $giveback;
			debuglog("got $giveback gold in the outhouse for washing");
		}
	}
	forest(true);
}elseif (($_GET[op] == "nowash")){
	page_header("Stinky Hands");
	output("`2Your hands are soiled and real stinky!`n");
	output("Didn't your mother teach you any better?`n");
	$takeaway = e_rand($badminimum, $badmaximum);
	if ($takeaway >= $badmusthit){
		if ($session[user][gold] >= $goldinhand){
			$session[user][gold] -= $takeback;
			debuglog("lost $takeback gold in the outhouse for not washing");
			output("`nThe Toilet Paper Gnome has thrown you to the slimy, filthy floor and extracted $takeback gold piece" . ($takeback > 1?"s":"") . " from you due to your slovenliness!`n");
		}
		output("Aren't you glad an embarassing moment like this isn't in the news?`n");
		addnews("`2Always cool, " . ($session['user']['name']) . " was seen walking around 
		 with a long string of toilet paper stuck to " . ($session[user][sex]?"her":"his") . " foot.`n");
	}
	forest(true);
}else{
	page_header("The Outhouses");
	if ($session[user][usedouthouse] == 0){
		output("`2The village has two outhouses, which it keeps way out here in the forest because of the ");
		output("warding effect of their smell on creatures.`n`nIn typical caste style, there is a priviliged ");
		output("outhouse, and an underpriviliged outhouse.  The choice is yours!`0`n`n");
		addnav("Toilets");	
		if ($canpay){
			addnav("Private Toilet: ($cost gold)", "outhouse.php?op=pay");
		}else{
			output("`2The Private Toilet costs `^$cost `2gold. Looks like you are going to have to hold it or use the Public Toilet!");
		}
		addnav("Public Toilet:  (free)", "outhouse.php?op=free");
		addnav("Hold it", "forest.php");
	}else{
		output("`2The village has two outhouses, which it keeps way out here in the forest because of the ");
		output("warding effect of their smell on creatures.`n`n");
			switch(e_rand(1,3)){
			case 1:
		output("The Outhouses are closed for repairs.`nYou will have to hold it till tomorrow!");
				break;
			case 2:
				output("As you draw close to the Outhouses, you realize that you simply don't think you can bear the smell of another visit to the Outhouses today.");
				break;
			case 3:
				output("You really don't have anything left to relieve today!");
				break;
			}
		output("`n`n`7You return to the forest.`0");
		forest(true);
	}
}
page_footer();

?>
