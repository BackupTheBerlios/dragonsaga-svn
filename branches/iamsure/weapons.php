<?
require_once "common.php";
checkday();

page_header("MightyE's Weapons");
output("`c`b`&MightyE's Weapons`0`b`c");
$tradeinvalue = round(($session['user']['weaponvalue']*.75),0);
if ($HTTP_GET_VARS['op']==""){
  output("`!MightyE `7stands behind a counter and appears to pay little attention to you as you enter, ");
	output("but you know from experience that he has his eye on every move you make.  He may be a humble ");
	output("weapons merchant, but he still carries himself with the grace of a man who has used his weapons ");
	output("to kill mightier ".($session['user']['gender']?"women":"men")." than you.`n`n");
	output("The massive hilt of a claymore protrudes above his shoulder; its gleam in the torch light not ");
	output("much brighter than the gleam off of `!MightyE's`7 bald forehead, kept shaved mostly as a strategic ");
	output("advantage, but in no small part because nature insisted that some level of baldness was necessary. ");
	output("`n`n`!MightyE`7 finally nods to you, stroking his goatee and looking like he wished he could ");
	output("have an opportunity to use one of these weapons.");
  addnav("Peruse Weapons","weapons.php?op=peruse");
	addnav("Return to the village","village.php");
}else if ($HTTP_GET_VARS['op']=="peruse"){
	$sql = "SELECT max(level) AS level FROM weapons WHERE level<=".(int)$session['user']['dragonkills'];
	$result = db_query($sql) or die(db_error(LINK));
	$row = db_fetch_assoc($result);
	
  $sql = "SELECT * FROM weapons WHERE level = ".(int)$row['level']." ORDER BY damage ASC";
	$result = db_query($sql) or die(db_error(LINK));
	output("`7You stroll up the counter and try your best to look like you know what most of these contraptions do. ");
	output("`!MightyE`7 looks at you and says, \"`#I'll give you `^$tradeinvalue`# ");
	output("tradein value for your `5".$session['user']['weapon']."`#.  Just click on the weapon you wish to buy, what ever 'click' means`7,\" and ");
	output("looks utterly confused.  He stands there a few seconds, snapping his fingers and wondering if that is what ");
	output("is meant by \"click,\" before returning to his work: standing there and looking good.");
	output("<table border='0' cellpadding='0'>",true);
	output("<tr class='trhead'><td>`bName`b</td><td align='center'>`bDamage`b</td><td align='right'>`bCost`b</td></tr>",true);
	for ($i=0;$i<db_num_rows($result);$i++){
	  $row = db_fetch_assoc($result);
		$bgcolor=($i%2==1?"trlight":"trdark");
		if ($row['value']<=($session['user']['gold']+$tradeinvalue)){
			output("<tr class='$bgcolor'><td><a href='weapons.php?op=buy&id=$row[weaponid]'>$row[weaponname]</a></td><td align='center'>$row[damage]</td><td align='right'>$row[value]</td></tr>",true);
			addnav("","weapons.php?op=buy&id=$row[weaponid]");
		}else{
			output("<tr class='$bgcolor'><td>$row[weaponname]</td><td align='center'>$row[damage]</td><td align='right'>$row[value]</td></tr>",true);
			addnav("","weapons.php?op=buy&id=$row[weaponid]");
		}
	}
	output("</table>",true);
	addnav("Return to the village","village.php");
}else if ($HTTP_GET_VARS[op]=="buy"){
  $sql = "SELECT * FROM weapons WHERE weaponid='$HTTP_GET_VARS[id]'";
	$result = db_query($sql) or die(db_error(LINK));
	if (db_num_rows($result)==0){
	  output("`!MightyE`7 looks at you, confused for a second, then realizes that you've apparently taken one too many bonks on the head, and nods and smiles.");
		addnav("Try again?","weapons.php");
		addnav("Return to the village","village.php");
	}else{
	  $row = db_fetch_assoc($result);
		if ($row[value]>($session[user][gold]+$tradeinvalue)){
		  output("Waiting until `!MightyE`7 looks away, you reach carefully for the `5$row[weaponname]`7, which you silently remove from the rack upon which ");
			output("it sits.  Secure in your theft, you turn around and head for the door, swiftly, quietly, like a ninja, only to discover that upon reaching ");
			output("the door, the ominous `!MightyE`7 stands, blocking your exit.  You execute a flying kick.  Mid flight, you hear the \"SHING\" of a sword ");
			output("leaving its sheath.... your foot is gone.  You land on your stump, and `!MightyE`7 stands in the doorway, claymore once again in its back holster, with ");
			output("no sign that it had been used, his arms folded menacingly across his burly chest.  \"`#Perhaps you'd like to pay for that?`7\" is all he has ");
			output("to say as you collapse at his feet, lifeblood staining the planks under your remaining foot.");
			$session[user][alive]=false;
			debuglog("lost " . $session['user']['gold'] . " gold on hand due to stealing from MightyE");
			$session[user][gold]=0;
			$session[user][hitpoints]=0;
			$session[user][experience]=round($session[user][experience]*.9,0);
			output("`b`&You have been slain by `!MightyE`&!!!`n");
			output("`4All gold on hand has been lost!`n");
			output("`410% of experience has been lost!`n");
			output("You may begin fighting again tomorrow.");
			addnav("Daily news","news.php");
			addnews("`%".$session[user][name]."`5 has been slain for trying to steal from `!MightyE`5's Weapons Shop.");
		}else{
			output("`!MightyE`7 takes your `5".$session[user][weapon]."`7 and promptly puts a price on it, setting it out for display with the rest of his weapons. ");
			debuglog("spent " . ($row['value']-$tradeinvalue) . " gold on the " . $row['weaponname'] . " weapon");
		  $session[user][gold]-=$row[value];
			$session[user][weapon] = $row[weaponname];
			$session[user][gold]+=$tradeinvalue;
			$session[user][attack]-=$session[user][weapondmg];
			$session[user][weapondmg] = $row[damage];
			$session[user][attack]+=$session[user][weapondmg];
			$session[user][weaponvalue] = $row[value];
			output("`n`nIn return, he hands you a shiny new `5$row[weaponname]`7 which you swoosh around the room, nearly taking off `!MightyE`7's head, which he ");
			output("deftly ducks; you're not the first person to exuberantly try out a new weapon.");
			addnav("Return to the village","village.php");
		}
	}
}

page_footer();
?>
