<?php
// This program is free software; you can redistribute it and/or modify it
// under the terms of the GNU General Public License as published by the
// Free Software Foundation; either version 2 of the License, or (at your
// option) any later version.
//
// File: village.php

require_once "common.php";
addcommentary();
checkday();

if ($session['user']['alive']){ }else{
	redirect("shades.php");
}
if (getsetting("automaster",1) && $session['user']['seenmaster']!=1){
	//masters hunt down truant students
	$exparray=array(1=>100,400,1002,1912,3140,4707,6641,8985,11795,15143,19121,23840,29437,36071,43930);
	while (list($key,$val)=each($exparray)){
		$exparray[$key]= round(
			$val + ($session['user']['dragonkills']/4) * $session['user']['level'] * 100
			,0);
	}
	$expreqd=$exparray[$session['user']['level']+1];
	if ($session['user']['experience']>$expreqd && $session['user']['level']<15){
		redirect("train.php?op=autochallenge");
	}
}

addnav("Blades Boulevard");
addnav("Forest","forest.php");
addnav("Bluspring's Warrior Training","train.php");
if (getsetting("pvp",1)){
	addnav("Slay Other Players","pvp.php");
}
addnav("Hall o' Fame","hof.php");

addnav("Market Street");
addnav("W?MightyE's Weaponry","weapons.php");
addnav("A?Pegasus Armor","armor.php");
addnav("B?Ye Olde Bank","bank.php");
addnav("T?Gypsy Tent","gypsy.php");
if (@file_exists("pavilion.php")) addnav("E?Eye-catching Pavilion","pavilion.php");

addnav("Tavern Street");
addnav("I?The Inn`0","inn.php",true);
addnav("Merick's Stables","stables.php");
addnav("G?The Garden", "gardens.php");
addnav("Curious Looking Rock", "rock.php");

addnav("`bOther`b");
addnav("??F.A.Q. (newbies start here)", "petition.php?op=faq",false,true);
addnav("Daily News","news.php");
addnav("Preferences","prefs.php");
addnav("List Warriors","list.php");
addnav("`%Quit`0 to the fields","login.php?op=logout",true);

if ($session['user']['superuser']>=2){
  addnav("X?`bSuperuser Grotto`b","superuser.php");
}
//let users try to cheat, we protect against this and will know if they try.
addnav("","superuser.php");
addnav("","user.php");
addnav("","taunt.php");
addnav("","creatures.php");
addnav("","configuration.php");
addnav("","badword.php");
addnav("","armoreditor.php");
addnav("","bios.php");
addnav("","badword.php");
addnav("","donators.php");
addnav("","referers.php");
addnav("","retitle.php");
addnav("","stats.php");
addnav("","viewpetition.php");
addnav("","weaponeditor.php");

if ($session['user']['superuser']){
  addnav("New Day","newday.php");
}

if (getsetting("topwebid", 0) != 0) {
	addnav("Top Web Games");
	if (date("Y-W", strtotime($session['user']['lastwebvote'])) < date("Y-W"))
		$hilight="`&";
	else
		$hilight="";
	addnav("V?".$hilight."Cast your Vote", "http://www.topwebgames.com/in.asp?id=".getsetting("topwebid", 0)."&acctid={$session['user']['acctid']}", false, true);
}

page_header("Village Square");
//output("<img src='images/trans.png' width='1' height='700' alt='' align='right'>",true);
output("`@`c`bVillage Square`b`cThe village hustles and bustles.  No one really notices that you're standing there.");
output("  You see various shops and businesses along main street.  There is a curious looking rock to one side.  ");
output("On every side the village is surrounded by deep dark forest.`n`n");
output("The clock on the inn reads `^".getgametime()."`@.");

//	$t1 = strtotime("now")*getsetting("daysperday",4);
//	$t2 = strtotime($session[user][lasthit])*getsetting("daysperday",4);
//	$d1 = date("Y-m-d",$t1);
//	$d2 = date("Y-m-d",$t2);
//output("`n`nToday is $d1, your last new day was $d2");

output("`n`n`%`@Nearby some villagers talk:`n");
viewcommentary("village","Add",25);
page_footer();
?>
