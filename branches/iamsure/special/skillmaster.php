<?php
// This program is free software; you can redistribute it and/or modify it
// under the terms of the GNU General Public License as published by the
// Free Software Foundation; either version 2 of the License, or (at your
// option) any later version.
//
// File: skillmaster.php

if (!isset($session)) exit();
$session[user][specialinc]="skillmaster.php";
switch((int)$session[user][specialty]){
case 1:
	$c="`$";
	break;
case 2:
	$c="`%";
	break;
case 3:
	$c="`^";
	break;
default:
	output("You have no direction in the world, you should rest and make some important decisions about your life.");
	$session[user][specialinc]="";
	//addnav("Return to the forest", "forest.php");
}
$skills = array(1=>"Dark Arts","Mystical Powers","Thievery");

if ($_GET[op]=="give"){
	if ($session[user][gems]>0){
		output("$c You give `@Foil`&wench$c a gem, and she hands you a slip of parchment with instructions on how to advance in your specialty.`n`n");
		output("You study it intensely, shred it up, and eat it lest infidels get ahold of the information.`n`n`@Foil`&wench$c sighs... \"`&You didn't");
		output("have to eat it...  Oh well, now be gone from here!$c\"`#");
		increment_specialty();
		$session[user][gems]--;
		debuglog("gave 1 gem to Foilwench");
	}else{
		output("$c You hand over your imaginary gem.  `@Foil`&wench$c stares blankly back at you.  \"`&Come back when you have a `breal`b gem you simpleton.$c\"`n`n");
		output("\"`#Simpleton?$c\" you ask.`n`n");
		output("With that, `@Foil`&wench$c throws you out.");
	}	
	$session[user][specialinc]="";
	//addnav("Return to the forest", "forest.php");
}else if($_GET[op]=="dont"){
	output("$c You inform `@Foil`&wench$c that if she would like to get rich, she will have to do so on her efforts, and stomp away.");
	$session[user][specialinc]="";
	//addnav("Return to the forest", "forest.php");
}else if($session[user][specialty]>0){
	output("$c You are seeking prey in the forest when you stumble across a strange hut.  Ducking inside, you are met by the grizzled face of a battle-hardened");
	output("old woman.  \"`&Greetings ".$session[user][name].", I am `@Foil`&wench$c, master of all.$c\"`n`n\"`#Master of all?$c\" you inquire.`n`n");
	output("\"`&Yes, master of all.  All the skills are mine to control, and to teach.$c\"`n`n\"`#Yours to teach?$c\" you query.`n`n");
	output("The old woman sighs, \"`&Yes, mine to teach.  I will teach you how to advance in ".$skills[$session[user][specialty]]." on two conditions.$c\"`n`n");
	output("\"`#Two conditions?$c\" you repeat inquisitively.`n`n");
	output("\"`&Yes.  First, you must give me a gem, and second you must stop repeating what I say in the form of a question!$c\"`n`n");
	output("\"`#A gem!$c\" you state definitively.`n`n");
	output("\"`&Well... I guess that wasn't a question.  So how about that gem?$c\"");
	addnav("Give her a gem","forest.php?op=give");
	addnav("Don't give her a gem","forest.php?op=dont");
}
?>
