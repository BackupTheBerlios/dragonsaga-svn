<?php
// This program is free software; you can redistribute it and/or modify it
// under the terms of the GNU General Public License as published by the
// Free Software Foundation; either version 2 of the License, or (at your
// option) any later version.
//
// File: petition.php

require_once "common.php";
if ($_GET['op']=="primer"){
popup_header("New Player Primer");
	output("
<a href='petition.php?op=faq'>Contents</a>`n`n
`^Welcome to the Dragon Saga New Player Primer`n`n
`^`bThe Village Square`b`n`@
The Dragon Saga (TDS) is turning out to be a fairly expansive game, with a lot of areas to explore.  It's easy to get lost with all that there is to do out there,
so keep in mind that the village square is pretty much the center of the game.  This area will give you access to most other areas that you can get to, with a few exceptions 
(we'll talk about those in a little while).  If you ever get lost, or are not sure what's going on, head to the village square and regain your bearings.`n
`n
`^`bYour first day`b`n`@
Your first day in the world can be very confusing!  You're presented with a lot of information, and you don't need almost any of it!  It's true!  One thing you should
probably keep an eye on though, are your hit points.  This is found under \"Vital Info.\"  No matter what profession you choose, in the end, you are some kind of warrior
or fighter, and so you need to learn how to do battle.  The best way to do this is to look for creatures to kill in the forest.  When you find one, check it out, and make
sure that it's not a higher level than you, because if it is, you might not live through the fight.  Keep in mind that you can always try to run away from something that
you encountered, but some times it might take several tries before you get away.  You might want to buy armor and weapons in the village square in order to give yourself
a better chance against these creatures out in the forest.`n
`n
Once you have defeated a creature, you'll notice that you're probably a little hurt.  Head on over to the Healer's Hut, and you can get patched up in short order.  While
you're level 1, healing is free, but as you advance, it becomes more and more expensive.  Also keep in mind that it's more expensive to heal 1 point, then later heal 1 point
again than it is to heal 2 in one shot.  So if you're trying to save up some money, and you're barely hurt, you might risk a fight or two while you're a little hurt, and 
heal the damage from several fights in one shot.`n
`n
After you've killed a few creatures, you should head back to the village, in to Bluspring's Warrior Training, and talk to your master.  Your master will tell you when
you are ready to challenge him, and when you are ready, you should give him a shot (make sure you're healed up first though!).  Your master won't kill you if you lose,
instead he'll give you a complimentary healing potion and send you on your way.",true);
	if (getsetting("multimaster",1) == 0) {
		output(" You can only challenge your master once a day.");
	}
output("
`n
`n
`^`bDeath`b`n`@
Death is a natural part of any games that contains some kind of combat.  In The Dragon Saga, being dead is only a temporary condition.  When you die, you'll
lose any money that you had on hand (money in the bank is safe!), and some of the experience you've accumulated.  While you're dead, you can explore the land of the shades
and the graveyard.  In the graveyard, you'll find Ramius the Overlord of Death.  He has certain things that he would like you to do for him, and in return, he will grant
you special powers or favors.  The graveyard is one of those areas that you can't get to from the Village Square.  In fact, while you're dead, you can't go to the village
square at all!`n
`n
Unless you can convince Ramius to resurrect you, you'll remain dead until the next game day.  There are ".getsetting("daysperday",2)." game days each real day.  These 
occur when the clock in the village square reaches midnight.`n
`n
`^`bNew Days`b`n`@
As stated just above, there are ".getsetting("daysperday",2)." game days each real day.  These occur when the clock in the village square reaches midnight.  When you get 
a new day, you'll be granted new forest fights, interest on gold you have in the bank (if the bankers are pleased with your performance!), and a lot of your other
statistics will be refreshed.  You'll also be resurrected if you were dead, and get another chance to take on the world.  If you don't log on over the course of an 
entire game day, you'll miss your opportunity to partake in that game day (this means that new game days are only assigned when you actually log on, being away from the
game for a few days won't grant you a whole bunch of new days).  Forest fights, pvp battles, special power usages and other things that get refreshed on a daily basis do
NOT get carried over from one day to the next (you can't build up a whole bunch of them).`n
`n",true);
if (getsetting("pvp",1)){
output("
`^`bPvP (Player versus Player)`b`n`@
The Dragon Saga contains a PvP element, where players can attack each other.  As a new player, you are protected from PvP for your first ".getsetting("pvpimmunity",5) . " game days or until you accumulate " . getsetting("pvpminexp",1500) . ", unless
you choose to attack another player.  Some servers might have the PvP aspect turned off, in which case there is no chance that you'll be attacked by any other players.  You
can tell if the server you play on has PvP turned off by looking in the Village Square for \"Slay Other Players.\"  If it's not there, you can't engage (or be engaged) in PvP.`n
`n
When you die in PvP, you only lose gold you had on hand, and " . getsetting("pvpdeflose", 5) . "% of your experience.  You won't lose any turns in the forest, or any other stats.  If you attack someone else in
PvP, you can get " . getsetting("pvpattgain", 10) . "% of the experience they had, and any gold they had on hand.  If you attack someone else and lose, however, you'll lose " . getsetting("pvpattlose", 15) . "% of your experience, and you'll lose
any gold that you had on hand.  If someone else attacks you and they lose, you'll gain the gold they had on hand, and " . getsetting("pvpdefgain", 10) . "% of their experience.  You can only attack someone who is
close to your level, so don't worry that as a level 1, some big level 15 player is going to come along and beat on you.`n
`n
If you buy a room in the inn when you decide to quit the game, you'll protect yourself from casual attacking.  The only way for someone to attack you when you're in the inn
is for them to bribe the bartender, which can be a costly procedure.  Quitting to the fields means that someone can attack you with out having to pay money to the bartender.  You
cannot be attacked while you are online, only while you are offline, so the more  you play, the more protected you are ;-).  Also, if you are attacked and die, no one else can
attack you again until you log on again, so don't worry that you'll be attacked 30 or 40 times in one night.  Logging back in to the game will make you a viable PvP target again
if you've already been killed today.`n
`n",true);
}
output("
`^`bReady to take on the world!`b`n`@
You should now have a pretty good idea of how the basics of the game work, how to advance, and how to protect yourself.  There's a whole lot more to the world, so explore it!
Don't be afraid of dieing, particularly when you're young, as even when you're dead, there's yet more stuff to do!
",true); 

}else if($_GET['op']=="faq3"){
popup_header("Specific and Technical Questions");
output("
<a href='petition.php?op=faq'>Contents</a>`n`n
`c`bSpecific and technical questions`b`c
`^1.a. How can I have been killed by another player while I was currently playing?`@`n
The biggest cause of this is someone who began attacking you while you were offline, and completed the fight while you were online.  This can even happen if you have
been playing nonstop for the last hour, when someone starts a fight, they are forced by the game to finish it at some point.  If they start a fight with you, and close
their browser, the next time they log on, they will have to finish the fight.  You will lose the lesser of the gold you had on hand when they attacked you, or the gold
on hand when they finished the fight.  So if you logged out with 1 gold on hand, they attack you, you log on, accumulate 2000 gold on hand, and they complete the fight,
they will only come away from it with 1 gold.  The same is true if you logged out with 2000 gold, and when they completed killing you, you only had 1 gold.`n
`n
`^1.b. Why did it say I was killed in the fields when I slept in the inn?`@`n
The same thing can happen where someone started attacking you when you were in the fields, and finished after you had retired to the inn for the day.  Keep in mind
that if you are idle on the game for too long, you become a valid target for others to attack you in the fields.  If you're going to go away from your computer for
a few minutes, it's a good idea to head to the inn for your room first so that you don't risk someone attacking you while you're idle.`n
`n
`^2. The game tells me that I'm not accepting cookies, what are they and what do I do?`@`n
Cookies are little bits of data that websites store on your computer so they can identify you from other players.  Sometimes if you have a firewall it will block cookies, and some web browsers will let you block cookies.  Check the documentation for your browser or firewall, or look around in its preferences for settings to modify whether or not you accept cookies.  You need to at least accept session cookies to play the game, though all cookies are better. `n
`n
",true);
	
}else if ($_GET['op']=="faq"){
popup_header("Frequently Asked Questions (FAQ)");
output("
`^Welcome to The Dragon Saga. `n
`n`@
You wake up one day, and you're in a village for some reason.  You wander around, bemused, until you stumble upon the Village Square.  Once there you start asking lots of stupid questions.  People (who are mostly naked for some reason) throw things at you.  You escape by ducking into the Inn and find a rack of pamphlets by the door.  The title of the pamphlet reads: \"Everything You Wanted to Know About the TDS, but Were Afraid to Ask.\"  Looking furtively around to make sure nobody's watching, you open one and read:`n
`n
\"So, you're a Newbie.  Welcome to the club.  Here you will find answers to the questions that plague you.  Well, actually you will find answers to the questions that plagued US.  So, here, read and learn, and leave us alone!\" `n
`n
`bContents:`b`n
<a href='petition.php?op=primer'>New Player Primer</a>`n
<a href='petition.php?op=faq1'>Frequently Asked Questions on Game Play (General)</a>`n
<a href='petition.php?op=faq2'>Frequently Asked Questions on Game Play (with spoilers)</a>`n
<a href='petition.php?op=faq3'>Frequently Asked Questions on Technical Issues</a>`n
`n
~Thank you,`n
the Management.`n
",true);

}else if($_GET['op']=="faq1"){
popup_header("General Questions");
output("
<a href='petition.php?op=faq'>Contents</a>`n`n

`c`bGeneral questions`b`c
`^1.  What is the purpose of this game?`@`n
To get chicks.`n
Seriously, though.  The purpose is to slay the green dragon.`n
`n
`^2.  How do I find the green dragon?`@`n
You can't.`n
Well, sort of.  You can't find her until you've reached a certain level. When you're at that level, it will be immediately obvious.`n
`n
`^3.  How do I increase my level?`@`n
You increase your experience by fighting creatures in the forest.  Once you've gotten enough experience, you can challenge your master in the village.`n
`n
`^4.  Why can't I beat my master?`@`n
He's far too wiley for the likes of you.`n
Did you ask him if you have enough experience?`n
Have you tried purchasing some armor or weapons in the village?`n
`n
`^5.  I used up all my turns.   How do I get more?`@`n
Send money.`n
No, put your wallet away.  There *are* a few ways to get an extra turn or two, but by and large you just have to wait for tomorrow.  When a new day comes you'll have more energy.`n
Don't bother asking us what those few ways are - some things are fun to find on your own.`n
`n
`^6.  When does a new day start?`@`n
Right after the old one ends.`n
`n
`^7.  Arghhh, you guys are killing me with your smart answers - can't you just give me a straight answer?`@`n
Nope.`n
Well, okay, new days correspond with the clock in the village (can also be viewed in the inn).  When the clock strikes midnight, expect a new day to begin.  The number of times a clock in TDS strikes midnight per calendar day may vary by server.  Beta server has 4 play days per calendar day, SourceForge server has 2.  Other servers depend on the admin.`nThis server has ".getsetting("daysperday",2) . " days per calendar day.`n
`n
`^8.  Something's gone wrong!!!  How do I let you know?`@`n
Send money.  Better yet, send a petition.  A petition should not say 'this doesn't work' or 'I'm broken' or 'I can't log in' or 'yo.  Sup?'  A petition *should* be very complete in describing *what* doesn't work.  Please tell us what happened, what the error message is (copy and paste is your friend), when it occurred, and anything else that may be helpful.  \"I'm broken\" is not helpful.  \"There are salmon flying out of my moniter when I log in\" is much more descriptive.  And humorous.  Although there's not much we can do about it.  In general, please be patient with these requests - many people play the game, and as long as the admin is swamped with 'yo - Sup?' petitions, it will take some time to sift through them. `n
`n
`^9.  What if all I have to say is 'yo - sup?'?`@`n
If you don't have something nice (or useful, or interesting, or creative that adds to the general revelry of the game) to say, don't say anything.`n
But if you do want to converse with someone, send them an email through Ye Olde Post Office.`n
`n
`^10.  How do I use emotes?`@`n
Type :: before your text.`n
`n
`^11.  What's an emote?`@`n
`&AnObviousAnswer punches you in the gut.`n
`@That's an emote.  You can emote in the village if you want to do an action rather than simply speaking.`n
`n
`^12.  How do you get colors in your name?`@`n
Eat funny mushrooms.`n
No, put that mushroom away, colors signify that the character was integral to the beta-testing process - finding a bug, helping to create creatures, etc, or being married to the admin.  (*cough*Appleshiner*cough*)`n
`n
`^13.  Sup dOOd, iz  it cool 2 uz  common IM wurds in the village?  Cuz u no, it's faster.  R u down wit that?`@`n
NO, for the love of pete, use full words and good grammar, PLEASE! These are not words: U, R, Ur, Cya, K, Kay, d00d, L8tr, sup, na and anything else like that!`n
`n
",true);
}else if($_GET['op']=="faq2"){
popup_header("General Questions with Spoilers");
output("
<a href='petition.php?op=faq'>Contents</a>`n`n
`&(Warning, the FAQs below might contain some spoilers, so if you really want to discover things on your own, you'd be better off not reading too far.  This is not a manual.  It's a self-help pamphlet.)`&
`n
`n
`n
`n
`n
`n
`n
`n
`n
`n
`n
`n
`n
`^1.  How do you get gems?`@`n
To the mines with you!!`n
Actually, you can't mine them.  (Well, you can, but only if you get luck and find the mine.  Warning though, mines can be dangerous.) Gems can be found in the forest during 'special events' that happen randomly - if you play often enough, you're bound to stumble across one at some point. Gems can also be gotten very occasionaly from a forest fight.",true);
if (getsetting("topwebid",0) != 0) {
	output("  Lastly, you can get a gem for free just for voting for this server at Top Web Games (see the link off of the village).");
}
output("
`n
`n
`^2.  Why do some people seem to have so many hitpoints at a low level?`@`n
Cause they're bigger than you.`n
No, really, they *are* bigger than you.  You'll be big too someday.`n
`n
`^3.  Does that have something to do with the titles that people have?`@`n
But of course!`n
Indeed, every time you kill the dragon, you get a new title and return to level one.  So low level players with titles have had opportunities to embiggen themselves.  (see Hall of Fame)`n
`n
`^4.  Why does that old man keep hitting me with an ugly/pretty stick in the forest?`@`n
You look like a pinata!`n
It's a special event that can add or remove charm.`n
`n
`^5.  Well, what's the point of charm?`@`n
To get chicks.`n
Well, actually, that *is* the point.  Visit some folks at the inn, and you ought to be able to figure this one out.  The more charm you have, the more successful you'll be wooing said folks.`n
`n
`^6.  Okay, I saw the man in the forest and he hit me with his ugly stick, but it says I'm uglier than the stick, and I made it lose a charm point. What's going on?`@`n
You're clearly the least charming person on the planet.  And if you're the person who actually *asked* this question, you're also the dumbest. Use a little power of inference, wouldja?  No.  Really.`n
Okay, we did say you were the dumbest, so: it means you currently have zero charm points.`n
`n
`^7.  How do I check my charm?`@`n
Take a peek in the mirror once in a while.`n
We jest - there's no mirror.  You'll have to ask a friend how you look today - the responses may be vague, but they'll give you a clue how you're doing.`n
`n
`^8.  How do we go to other villages?`@`n
Take the train downtown.  We're talking downtown.`n
Actually, there aren't any other villages to travel to.  Any references to them (i.e. Eythgim folks you meet in the forest) only exist to give the game more depth. `n
`n
`^9.  Who is the Management?`@`n
Appleshiner and Foilwench are in charge of this FAQ,  but if something goes wrong, email MightyE.  He's in charge of everything else. `n
`n
`^10.  How did they get to be so darn attractive, anyway?`@`n
Lots of at-home facials, my dear!!  MightyE especially enjoys the Grapefruit Essence Facial Masque.`n
",true);
}else{
	popup_header("Petition for Help");
	if (count($_POST)>0){
		$p = $session[user][password];
		unset($session[user][password]);
		/*
		mail(getsetting("gameadminemail","postmaster@localhost"),"TDS Petition",output_array($_POST,"POST:").output_array($session,"Session:"));
		$sql = "SELECT acctid FROM accounts WHERE emailaddress='".getsetting("gameadminemail","postmaster@localhost")."'";
		//output($sql);
		$result = db_query($sql);
		if (db_num_rows($result)==0){
			$sql = "SELECT acctid FROM accounts WHERE superuser>=3";
			$result = db_query($sql);
		}
		for ($i=0;$i<db_num_rows($result);$i++){
			$row = db_fetch_assoc($result);
			systemmail($row[acctid],"Petition",output_array($_POST),(int)$session[user][acctid]);
		}
		*/
		$sql = "INSERT INTO petitions (author,date,body,pageinfo) VALUES (".(int)$session[user][acctid].",now(),\"".addslashes(output_array($_POST))."\",\"".addslashes(output_array($session,"Session:"))."\")";
		db_query($sql);
		$session[user][password]=$p;
		output("Your petition has been sent to the server admin.  Please be patient, most server admins
		have jobs and obligations beyond their game, so sometimes responses will take a while to be received.");
		
	}else{
		output("<form action='petition.php?op=submit' method='POST'>
		Your Character's Name: <input name='charname'>`n
		Your email address: <input name='email'>`n
		Description of the problem:`n
		<textarea name='description' cols='30' rows='5' class='input'></textarea>`n
		<input type='submit' class='button' value='Submit'>`n
		Please be as descriptive as possible in your petition.  If you have questions about how the game works,
		please check out the <a href='petition.php?op=faq'>FAQ</a>.  Petitions about game mechanics will more than
		likely not be answered unless they have something to do with a bug.
		</form>
		",true);
	}
}
popup_footer();
?>
