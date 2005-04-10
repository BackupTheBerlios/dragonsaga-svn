<?php
require_once "common.php";
checkday();

page_header("Pegasus Armor");
output("`c`b`%Pegasus Armor`0`b`c");
$tradeinvalue = round(($session['user']['armorvalue']*.75),0);
if (!isset($_GET['op']))
{
    $_GET['op'] = '';
}
if ($_GET['op']==""){
	output("`5The fair and beautiful `#Pegasus`5 greets you with a warm smile as you stroll over to her brightly colored ");
	output("gypsy wagon, which is placed, not out of coincidence, right next to `!MightyE`5's weapon shop.  Her outfit is ");
	output("as brightly colored and outrageous as her wagon, and it is almost (but not quite) enough to make you look away from her huge ");
	output("gray eyes and flashes of skin between her not-quite-sufficent gypsy clothes.");
	output("`n`n");
	addnav("Browse `#Pegasus`0' wares","armor.php?op=browse");
	addnav("Return to the village","village.php");
}else if ($_GET['op']=="browse"){
	$sql = "SELECT max(level) AS level FROM armor WHERE level<=".$session['user']['dragonkills'];
	$result = db_query($sql) or die(db_error(LINK));
	$row = db_fetch_assoc($result);

  $sql = "SELECT * FROM armor WHERE level=$row[level] ORDER BY value";
	$result = db_query($sql) or die(db_error(LINK));
	output("`5You look over the various pieces of apparal, and wonder if `#Pegasus`5 would be so good as to try some of them ");
	output("on for you, when you realize that she is busy staring dreamily at `!MightyE`5 through the window of his shop ");
	output("as he, barechested, demonstrates the use of one of his fine wares to a customer.  Noticing for a moment that ");
	output("you are browsing her wares, she glances at your ".$session['user']['armor']." and says that she'll give you `^$tradeinvalue`5 for them.");
	output("<table border='0' cellpadding='0'>",true);
	output("<tr class='trhead'><td>`bName`b</td><td align='center'>`bDefense`b</td><td align='right'>`bCost`b</td></tr>",true);
	for ($i=0;$i<db_num_rows($result);$i++){
	  $row = db_fetch_assoc($result);
		$bgcolor=($i%2==1?"trlight":"trdark");
		if ($row['value']<=($session['user']['gold']+$tradeinvalue)){
			output("<tr class='$bgcolor'><td><a href='armor.php?op=buy&id=$row[armorid]'>$row[armorname]</a></td><td align='center'>$row[defense]</td><td align='right'>$row[value]</td></tr>",true);
			addnav("","armor.php?op=buy&id=$row[armorid]");
		}else{
			output("<tr class='$bgcolor'><td>$row[armorname]</td><td align='center'>$row[defense]</td><td align='right'>$row[value]</td></tr>",true);
			addnav("","armor.php?op=buy&id=$row[armorid]");
		}
	}
	output("</table>",true);
	addnav("Return to the village","village.php");
}else if ($_GET['op']=="buy"){
  $sql = "SELECT * FROM armor WHERE armorid='$_GET[id]'";
	$result = db_query($sql) or die(db_error(LINK));
	if (db_num_rows($result)==0){
	  output("`#Pegasus`5 looks at you, confused for a second, then realizes that you've apparently taken one too many bonks on the head, and nods and smiles.");
		addnav("Try again?","armor.php");
		addnav("Return to the village","village.php");
	}else{
	  $row = db_fetch_assoc($result);
		if ($row['value']>($session['user']['gold']+$tradeinvalue)){
		  output("`5Waiting until `#Pegasus`5 looks away, you reach carefully for the `%$row[armorname]`5, which you silently remove from the stack of clothes on which ");
			output("it sits.  Secure in your theft, you begin to turn around only to realize that your turning action is hindered by a fist closed tightly around your ");
			output("throat.  Glancing down, you trace the fist to the arm on which it is attached, which in turn is attached to a very muscular `!MightyE`5.  You try to ");
			output("explain what happened here, but your throat doesn't seem to be able to open up to let your voice through, let alone essential oxygen.  ");
			output("`n`nAs darkness creeps in on the edge of your vision, you glance pleadingly, but futilly at `%Pegasus`5 who is staring dreamily at `!MightyE`5, her ");
			output("hands clutched next to her face, which is painted with a large admiring smile.");
			$session['user']['alive']=false;
			debuglog("lost " . $session['user']['gold'] . " gold on hand due to stealing from Pegasus");
			$session['user']['gold']=0;
			$session['user']['hitpoints']=0;
			$session['user']['experience']=round($session['user']['experience']*.9,0);
			output("`b`&You have been slain by `!MightyE`&!!!`n");
			output("`4All gold on hand has been lost!`n");
			output("`410% of experience has been lost!`n");
			output("You may begin fighting again tomorrow.");
			addnav("Daily news","news.php");
			addnews("`%".$session['user']['name']."`5 has been slain by `!MightyE`5 for trying to steal from `#Pegasus`5' Armor Wagon.");
		}else{
			output("`#Pegasus`5 takes your gold, and much to your surprise she also takes your `%".$session['user']['armor']."`5 and promptly puts a price on it, setting it neatly on another stack of clothes. ");
			output("`n`nIn return, she hands you a beautiful  new `%$row[armorname]`5.");
			output("`n`nYou begin to protest, \"`@Won't I look silly wearing nothing but a `&$row[armorname]`@?`5\" you ask.  You ponder it a moment, and then realize that everyone else in ");
			output("the town is doing the same thing.  \"`@Oh well, when in Rome...`5\"");
			debuglog("spent " . ($row['value']-$tradeinvalue) . " gold on the " . $row['armorname'] . " armor");
		  $session['user']['gold']-=$row['value'];
			$session['user']['armor'] = $row['armorname'];
			$session['user']['gold']+=$tradeinvalue;
			$session['user']['defence']-=$session['user']['armordef'];
			$session['user']['armordef'] = $row['defense'];
			$session['user']['defence']+=$session['user']['armordef'];
			$session['user']['armorvalue'] = $row['value'];
			addnav("Return to the village","village.php");
		}
	}
}
page_footer();
?>
