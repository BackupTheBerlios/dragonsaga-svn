<?php
require_once "common.php";

if ($session['user']['alive']) redirect("village.php");

page_header("The Graveyard");
checkday();
$session['bufflist']=array();
$session['user']['drunkenness'] = 0;
$max = $session['user']['level'] * 5 + 50;
$favortoheal = round(10 * ($max-$session['user']['soulpoints'])/$max);

if ($_GET['op']=="search"){
	if ($session['user']['gravefights']<=0){
		output("`\$`bYour soul can bear no more torment in this afterlife.`b`0");
		$_GET['op']="";
	}else{
		$session['user']['gravefights']--;
  		$battle=true;
  		$sql = "SELECT * FROM creatures WHERE location=1 ORDER BY rand(".e_rand().") LIMIT 1";
		$result = db_query($sql) or die(db_error(LINK));
		$badguy = db_fetch_assoc($result);
		$level = $session['user']['level'];
		$shift = 0;
		if ($level < 5) $shift = -1;
		$badguy['creatureattack'] = 9 + $shift + (int)(($level-1) * 1.5);
		// Make graveyard creatures easier.
		$badguy['creaturedefense'] = (int)((9 + $shift + (($level-1) * 1.5)) * .7);
		$badguy['creaturehealth'] = $level * 5 + 50;
		$badguy['creatureexp'] = e_rand(10 + round($level/3),20 + round($level/3));
		$badguy['creaturelevel'] = $level;
		//output("`#DEBUG: Creature level: {$badguy['creaturelevel']}`n");
		//output("`#DEBUG: Creature attack: {$badguy['creatureattack']}`n");
		//output("`#DEBUG: Creature defense: {$badguy['creaturedefense']}`n");
		//output("`#DEBUG: Creature health: {$badguy['creaturehealth']}`n");
		//output("`#DEBUG: Creature exp: {$badguy['creatureexp']}`n");
		$session['user']['badguy']=createstring($badguy);
	}
}
if ($HTTP_GET_VARS[op]=="fight" || $HTTP_GET_VARS[op]=="run"){
	if ($_GET['op']=="run"){
		if (e_rand(0,2)==1) {
			output("`\$Ramius`) curses you for your cowardice.`n`n");
			$favor = 5 + e_rand(0, $session['user']['level']);
			if ($favor > $session['user']['deathpower'])
				$favor = $session['user']['deathpower'];
			if ($favor > 0) {
				output("`)You have `\$LOST `^$favor`) favor with `\$Ramius.");
				$session['user']['deathpower']-=$favor;
			}
			addnav("Return to the Graveyard","graveyard.php");
		} else {
			output("`)As you try to flee, you are summoned back to the fight!`n`n");
			$battle=true;
		}
	} else {
		$battle = true;
	}
}

if ($battle){
	//make some adjustments to the user to put them on mostly even ground with the undead guy.
	$originalhitpoints = $session['user']['hitpoints'];
	$session['user']['hitpoints'] = $session['user']['soulpoints'];
	$originalattack = $session['user']['attack'];
	$originaldefense = $session['user']['defence'];
	$session['user']['attack'] = 10 + round(($session['user']['level'] - 1) * 1.5);
	$session['user']['defence'] = 10 + round(($session['user']['level'] - 1) * 1.5);
	include("battle.php");
	//reverse those adjustments, battle calculations are over.
	$session['user']['attack'] = $originalattack;
	$session['user']['defence'] = $originaldefense;
	$session['user']['soulpoints'] = $session['user']['hitpoints'];
	$session['user']['hitpoints'] = $originalhitpoints;
	if ($victory) {
		output("`b`&{$badguy['creaturelose']}`0`b`n"); 
		output("`b`\$You have tormented {$badguy['creaturename']}!`0`b`n");
		output("`#You receive `^{$badguy['creatureexp']}`# favor with `\$Ramius`#!`n`0");
		$session['user']['deathpower']+=$badguy['creatureexp'];
		$badguy=array();
		$_GET['op']="";
	}else{
		if ($defeat){
			//addnav("Return to the shades","shades.php");
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
			
			addnews("`)".$session['user']['name']."`) has been defeated in the graveyard by {$badguy['creaturename']}`n$taunt");
			output("`b`&You have been defeated by `%{$badguy['creaturename']} `&!!!`n");
			output("You may not torment any more souls today.");
			$session['user']['gravefights']=0;
			addnav("R?Return to the Graveyard","graveyard.php");
		}else{
			addnav("F?Torment","graveyard.php?op=fight");
			addnav("R?Flee","graveyard.php?op=run");
		}
	}
}

if ($_GET['op']==""){
	output("`)`c`bThe Graveyard`b`c");
	output("Your spirit wanders in to a lonely graveyard, overgrown with sickly weeds which seem to grab at your spirit as you float past them.
	Around you are the remains of many broken tombstones, some lying on their face, some shattered to pieces.  You can almost hear the
	wails of the souls trapped within each plot lamenting their fates.
	`n`n In the center of the graveyard is an ancient looking mausoleum which has been worn by the effects of untold years.  A sinister
	looking gargoyle adorns the apex of its roof; its eyes seem to follow  you, and its mouth gapes with sharp stone teeth.
	The plaque above the door reads `\$Ramius, Overlord of Death`).");

	addnav("Search for something to torment","graveyard.php?op=search");
	addnav("M?Enter the Mausoleum","graveyard.php?op=enter");
	addnav("List Warriors","list.php");
	addnav("Return to the shades","shades.php");
}elseif ($_GET['op']=="enter"){
//	output("You enter the mausoleum and find yourself in a cold marble chamber.  no don't type that it's distracting.  Can't think, too much typing, stop, stoooppppp.  Now why couldn't you type verbatim a minute ago.  Ugh, sonofabitch.  The air is so, no the air is dammit.  The air within the chamber chils you to the bone?  You don't have bones, shit.  What'd I say before? Ugh.  Hahahaha. hahaha.  Hiccup.  Hiccup.  Hiccup.  Stop it, you're gonna kill me, stop stop!  Shit I forget what we actually stop that!  dammit.  Ok, you enter the mausoleum, and find yourself in a cold stark marble chamber.  The air surrounding you is chilled, no, is, the air around you carries the chill of death itself.  From the darkness two black eyes seem to stare in to your soul.  Within your mind you hear (dramatic pause) Whahahaha.  Within your mind you hear YOUR MOM.  That's what he would say.  Ok, within your mind you hear.  Ugh.  How are you gonna pick out the good crap in this, I mean, if I just keep talking, will you really just keep typing it?  Crap, you wrapped around, I didn't even know you could do that.  All right, where did we stop at ? Within something.  Within your mind you hear.  Within your mind a deep voice, no, um, a deep voice penetrates your mind.  Period.  Now whatever Ramius says.  What's he say, what'd you have him say before?  I am Ramius, your mom.  Why have you come here, no, I know no, allright, lemme think.  Ok, this'll be like text, so I don't know if it will be in those quotation mark things.  Allright.  Your mortal coil has forsaken you.  Now you turn to me.  To prove your worth in death go out amongst the ugh.  Go out amongst the... We need a word for, cause the other things are clearly dead, go out amongst.  Hmm.  Meow.  Don't type that.  Ugh, hehehe.  hahahaha.  Stop it, you're gonna, stop it.  all right.  Go out and find those that have eluded my grasp.  Send their souls where yours has gone.  Then return to me to seek the favor you, no, to fi-- no.  And return to me, and I shall reward you well.  Period.  Oh well, yeah, you already put a period.  Holy crap, you wrapped around a second time?  Hehehehe.  Well.  I think at this point, we need to go through and get rid of all the crap that you typed and you didn't need to type.  I'm gonna talk so fast that you can't hj.  Bjwuhehe.  Bwahahaha.  Choahaha.  Heh, you ass.  I'm done, I don't have anything else to say.  Ugh.  I'm gonna go check my swimming skill.  Aah, look, I'm 95, I'm done.  Alright, I'm gonna go camp.  Are you still freaking typing what I'm saying?  ugh.  I am a stupid moron.  I have the ass of a flaming monkey.  Hehe.  I'm the one who ate the fart.  Look at you, look at the things you're typing.  No, cause I clearly said Choo, See eightch Ooh Ooh.  See eitch Ooh Ooh, what? I didn't say that.  Hehhehehehe.  Huh.  (evil glare)");
	output("`)`b`cThe Mausoleum`c`b");
	output("You enter the mausoleum and find yourself in a cold, stark marble chamber.  The air around you carries the chill of death itself.
	From the darkness, two black eyes stare into your soul.  A clammy grasp seems to clutch your mind, and fill it with the words of the Overlord of Death, `\$Ramius`) himself.`n`n
	\"`7Your mortal coil has forsaken you.  Now you turn to me.  There are those within this land that have eluded my grasp and posess a life beyond life.  To prove your worth to me 
	and earn my favor, go out and torment their souls.  Should you gain enough of my favor, I will reward you.`)\"");
	addnav("Question `\$Ramius`0 about the worth of your soul","graveyard.php?op=question");
	addnav("S?Restore Your Soul ($favortoheal favor)","graveyard.php?op=restore");
	
	addnav("R?Return to The Graveyard","graveyard.php");
}elseif ($_GET['op']=="restore"){
	output("`)`b`cThe Mausoleum`c`b");
	if ($session['user']['soulpoints']<$max){
		if ($session['user']['deathpower']>=$favortoheal){
			output("`\$Ramius`) calls you weak for needing restoration, but as you have enough favor with him, he grants your request at the cost of `4$favortoheal`) favor.");
			$session['user']['deathpower']-=$favortoheal;
			$session['user']['soulpoints']=$max;
		}else{
			output("`\$Ramius`) curses you and throws you from the Mausoleum, you must gain more favor with him before he will grant restoration.");
		}
	}else{
		output("`\$Ramius`) sighs and mumbles something about, \"`7just 'cause they're dead, does that mean they don't have to think?`)\"`n`n");
		output("Perhaps you'd like to actually `ineed`i restoration before you ask for it.");
	}
	addnav("Question `\$Ramius`0 about the worth of your soul","graveyard.php?op=question");
	//addnav("Restore Your Soul ($favortoheal favor)","graveyard.php?op=restore");
	
	addnav("Return to The Graveyard","graveyard.php");
}elseif ($_GET['op']=="question"){
	if ($session['user']['deathpower']>=100) {
		output("`\$Ramius`) speaks, \"`7You have impressed me indeed.  I shall grant you the ability to visit your foes in the mortal world.`)\"");
		addnav("Ramius Favors");
		addnav("H?Haunt a foe (25 favor)","graveyard.php?op=haunt");
		addnav("e?Resurrection (100 favor)","newday.php?resurrection=true");
		addnav("Other");
	}elseif ($session['user']['deathpower'] >= 25){
		output("`\$Ramius`) speaks, \"`7I am moderately impressed with your efforts.  A minor favor I now grant to you, but continue my work, and I may yet have more power to bestow.`)\""); 
		addnav("Ramius Favors");
		addnav("H?Haunt a foe (25 favor)","graveyard.php?op=haunt");
		addnav("Other");
	}else{
		output("`\$Ramius`) speaks, \"`7I am not yet impressed with your efforts.  Continue my work, and we may speak further.`)\"");
	}
	output("`n`nYou have `6{$session['user']['deathpower']}`) favor with `\$Ramius`).");
	addnav("Question `\$Ramius`0 about the worth of your soul","graveyard.php?op=question");
	addnav("S?Restore Your Soul ($favortoheal favor)","graveyard.php?op=restore");
	
	addnav("Return to The Graveyard","graveyard.php");
}elseif ($_GET['op']=="haunt"){
	output("`\$Ramius`) is impressed with your actions, and grants you the power to haunt a foe.`n`n");
	output("<form action='graveyard.php?op=haunt2' method='POST'>",true);
	addnav("","graveyard.php?op=haunt2");
	output("Who would you like to haunt? <input name='name' id='name'> <input type='submit' class='button' value='Search'>",true);
	output("</form>",true);
	output("<script type='text/javascript'>document.getElementById('name').focus()</script>",true);
	addnav("Return to the mausoleum","graveyard.php?op=enter");
}elseif ($_GET['op']=="haunt2"){
	$string="%";
	for ($x=0;$x<strlen($_POST['name']);$x++){
		$string .= substr($_POST['name'],$x,1)."%";
	}
	$sql = "SELECT login,name,level FROM accounts WHERE name LIKE '".addslashes($string)."' AND locked=0 ORDER BY level,login";
	$result = db_query($sql);
	if (db_num_rows($result)<=0){
		output("`\$Ramius`) could find no one who matched the name you gave him.");
	}elseif(db_num_rows($result)>100){
		output("`\$Ramius`) thinks you should narrow down the number of people you wish to haunt.");
		output("<form action='graveyard.php?op=haunt2' method='POST'>",true);
		addnav("","graveyard.php?op=haunt2");
		output("Who would you like to haunt? <input name='name' id='name'> <input type='submit' class='button' value='Search'>",true);
		output("</form>",true);
		output("<script type='text/javascript'>document.getElementById('name').focus()</script>",true);
	}else{
		output("`\$Ramius`) will allow you to try to haunt these people:`n");
		output("<table cellpadding='3' cellspacing='0' border='0'>",true);
		output("<tr class='trhead'><td>Name</td><td>Level</td></tr>",true);
		for ($i=0;$i<db_num_rows($result);$i++){
			$row = db_fetch_assoc($result);
			output("<tr class='".($i%2?"trlight":"trdark")."'><td><a href='graveyard.php?op=haunt3&name=".HTMLEntities($row['login'])."'>",true);
			output($row['name']);
			output("</a></td><td>",true);
			output($row['level']);
			output("</td></tr>",true);
			addnav("","graveyard.php?op=haunt3&name=".HTMLEntities($row['login']));
		}
		output("</table>",true);
	}
	addnav("Question `\$Ramius`0 about the worth of your soul","graveyard.php?op=question");
	addnav("S?Restore Your Soul ($favortoheal favor)","graveyard.php?op=restore");
	addnav("M?Return to the Mausoleum","graveyard.php?op=enter");
}elseif ($_GET['op']=="haunt3"){
	output("`)`c`bThe Mausoleum`b`c");
	$sql = "SELECT name,level,hauntedby,acctid FROM accounts WHERE login='{$_GET['name']}'";
	$result = db_query($sql);
	if (db_num_rows($result)>0){
		$row = db_fetch_assoc($result);
		if ($row['hauntedby']!=""){
			output("That person has already been haunted, please select another target");
		}else{
			$session['user']['deathpower']-=25;
			$roll1 = e_rand(0,$row['level']);
			$roll2 = e_rand(0,$session['user']['level']);
			if ($roll2>$roll1){
				output("You have successfully haunted `7{$row['name']}`)!");
				$sql = "UPDATE accounts SET hauntedby='{$session['user']['name']}' WHERE login='{$_GET['name']}'";
				db_query($sql);
				addnews("`7{$session['user']['name']}`) haunted `7{$row['name']}`)!");
		 		systemmail($row['acctid'],"`)You have been haunted","`)You have been haunted by {$session['user']['name']}"); 
			}else{
				addnews("`7{$session['user']['name']}`) unsuccessfully haunted `7{$row['name']}`)!");
				switch (e_rand(0,5)){
				case 0:
					output("Just as you were about to haunt `7{$row['name']}`) good, they sneezed, and missed it completely.");
					break;
				case 1:
					output("You haunt `7{$row['name']}`) real good like, but unfortunately they're sleeping and are completely unaware of your presence.");
					break;
				case 2:
					output("You're about to haunt `7{$row['name']}`), but trip over your ghostly tail and land flat on your, um... face.");
					break;
				case 3:
					output("You go to haunt `7{$row['name']}`) in their sleep, but they look up at you, and roll over mumbling something about eating sausage just before going to bed.");
					break;
				case 4:
					output("You wake `7{$row['name']}`) up, who looks at you for a moment before declaring, \"Neat!\" and trying to catch you.");
					break;
				case 5:
					output("You go to scare `7{$row['name']}`), but catch a glimpse of yourself in the mirror and panic at the sight of a ghost!");
					break;
				}
			}
		}
	}else{
		output("`\$Ramius`) has lost their concentration on this person, you cannot haunt them now.");
	}
	addnav("Question `\$Ramius`0 about the worth of your soul","graveyard.php?op=question");
	addnav("S?Restore Your Soul ($favortoheal favor)","graveyard.php?op=restore");
	addnav("M?Return to the Mausoleum","graveyard.php?op=enter");
}

page_footer();
?>
