<?php
if (!isset($session)) exit();
// The addition of the commentary is handled by the forest.php
// addcommentary();
output("`c`b<span style='color: #787878'>Dark Horse Tavern`b`c",true);
$session[user][specialinc]="darkhorse.php";
switch($HTTP_GET_VARS[op]){
case "":
	checkday();
	output("A cluster of trees nearby looks familiar... You're sure you've seen this place before.  ");
	output("As you approach the grove, a strange mist creeps in around you; your mind begins to buzz, ");
	output("and you're no longer sure exactly how you got here");
	if ($session['user']['hashorse']) output(", but your horse seems to have known the way"); // BoarVolk's idea
	output(".`n`nThe mist clears, and before you is ");
	output("a log building with smoke trailing from its chimney.  A sign over the door says `7\"Dark Horse Tavern.\"`0");
	addnav("Enter the tavern","forest.php?op=tavern");
	addnav("Leave this place","forest.php?op=leaveleave");
	$session[user][specialinc]="darkhorse.php";
	break;	
case "tavern":
	checkday();
	output("You stand near the entrance of the tavern and survey the scene before you.  Whereas most taverns ");
	output("are noisy and raucous, this one is quiet, and nearly empty.  In the corner, an old man plays with ");
	output("some dice.  You notice that the tables have been etched on by previous adventurers who have found ");
	output("this place before, and behind the bar, a stick of an old man hobbles around, polishing glasses, ");
	output("as though there were anyone here to use them.");
	
	addnav("Talk to the old man","forest.php?op=oldman");
	addnav("Talk to the bartender","forest.php?op=bartender");
	addnav("Examine the tables","forest.php?op=tables");
	addnav("Exit the tavern","forest.php?op=leave");
	$session[user][specialinc]="darkhorse.php";
	break;
case "tables":
	output("You examine the etchings in the table:`n`n");
	viewcommentary("darkhorse","Add your own etching:");
	addnav("Return to the tavern","forest.php?op=tavern");
	break;
case "bartender":
	if ($HTTP_GET_VARS[what]==""){
		output("The grizzled old man behind the bar reminds you very much of a strip of beef jerkey.`n`n");
		output("\"`7Shay, what can I do for you ".($session[user][sex]?"lasshie":"shon")."?`0\" inquires the toothless ");
		output("fellow.  \"`7Don't shee the likesh of your short too offen 'round theshe partsh.`0\"");
		addnav("Learn about my enemies","forest.php?op=bartender&what=enemies");
		addnav("Learn about colors","forest.php?op=bartender&what=colors");
		//addnav("Buy swill","forest.php?op=bartender&what=swill");
	}else if($HTTP_GET_VARS[what]=="swill"){
		
	}else if($HTTP_GET_VARS[what]=="colors"){
			  output("The old man leans on the bar.  \"`%Sho you want to know about colorsh, do you?`0\" he asks.");
				output("  You are about to answer when you realize the question was posed in the rhetoric.  ");
				output("He continues, \"`%To do colorsh, here'sh what you need to do.  Firsht, you ushe a &#0096; mark ",true);
				output("(found right above the tab key) followed by 1, 2, 3, 4, 5, 6, 7, !, @, #, $, %, ^, &.  Each of thoshe correshpondsh with ");
				output("a color to look like this: `n`1&#0096;1 `2&#0096;2 `3&#0096;3 `4&#0096;4 `5&#0096;5 `6&#0096;6 `7&#0096;7 ",true);
				output("`n`!&#0096;! `@&#0096;@ `#&#0096;# `\$&#0096;\$ `%&#0096;% `^&#0096;^ `&&#0096;& `n",true);
				output("`% got it?`0\"  You can practice below:");
				output("<form action=\"$REQUEST_URI\" method='POST'>",true);
				output("You entered ".str_replace("`","&#0096;",HTMLEntities($HTTP_POST_VARS[testtext]))."`n",true);
				output("It looks like ".$HTTP_POST_VARS[testtext]." `n");
				output("<input name='testtext'><input type='submit' class='button' value='Try'></form>",true);
				output("`0`n`nThese colors can be used in your name, and in any conversations you have.");
				addnav("",$REQUEST_URI);
	}else if($HTTP_GET_VARS[what]=="enemies"){
		if ($HTTP_GET_VARS[who]==""){
			output("\"`7Sho, you want to learn about your enemiesh, do you?  Who do you want to know about?  Well?  Shpeak up!  It only costs `^100`7 gold per person for information.`0\"");
			if ($_GET['subop']!="search"){
				output("<form action='forest.php?op=bartender&what=enemies&subop=search' method='POST'><input name='name'><input type='submit' class='button' value='Search'></form>",true);
				addnav("","forest.php?op=bartender&what=enemies&subop=search");
			}else{
				addnav("Search Again","forest.php?op=bartender&what=enemies");
				$search = "%";
				for ($i=0;$i<strlen($_POST['name']);$i++){
					$search.=substr($_POST['name'],$i,1)."%";
				}
				$sql = "SELECT name,alive,location,sex,level,laston,loggedin,login FROM accounts WHERE (locked=0 AND name LIKE '$search') ORDER BY level DESC";
				//output($sql);
				$result = db_query($sql) or die(db_error(LINK));
				$max = db_num_rows($result);
				if ($max > 100) {
					output("`n`n\"`7Hey, whatsh you think yoush doin'.  That'sh too many namesh to shay.  I'll jusht tell you 'bout shome of them.`0`n");
					$max = 100;
				}
				output("<table border=0 cellpadding=0><tr><td>Name</td><td>Level</td></tr>",true);
				for ($i=0;$i<$max;$i++){
					$row = db_fetch_assoc($result);
					output("<tr><td><a href='forest.php?op=bartender&what=enemies&who=".rawurlencode($row[login])."'>$row[name]</a></td><td>$row[level]</td></tr>",true);
					addnav("","forest.php?op=bartender&what=enemies&who=".rawurlencode($row[login]));
				}
				output("</table>",true);
			}
		}else{
			if ($session[user][gold]>=100){
				$sql = "SELECT name,alive,location,maxhitpoints,gold,sex,level,weapon,armor,attack,defence FROM accounts WHERE login=\"$HTTP_GET_VARS[who]\"";
				$result = db_query($sql) or die(db_error(LINK));
				if (db_num_rows($result)>0){
					$row = db_fetch_assoc($result);
					output("\"`7Well... letsh shee what I know about ".str_replace("s","sh",$row[name])."`7,`0\" he says...`n`n");
					output("`4`bName:`b`6 $row[name]`n");
					output("`4`bLevel:`b`6 $row[level]`n");
					output("`4`bHitpoints:`b`6 $row[maxhitpoints]`n");
					output("`4`bGold:`b`6 $row[gold]`n");
					output("`4`bWeapon:`b`6 $row[weapon]`n");
					output("`4`bArmor:`b`6 $row[armor]`n");
					output("`4`bAttack:`b`6 $row[attack]`n");
					output("`4`bDefense:`b`6 $row[defence]`n");
					$session[user][gold]-=100;
					debuglog("spent 100 gold to learn about an enemy");
				}else{
					output("\"`7Eh..?  I don't know anyone named that.`0\"");
				}
			}else{
				output("\"`7Well... letsh shee what I know about cheapshkates like you,`0\" he says...`n`n");
				output("`4`bName:`b`6 Get some money`n");
				output("`4`bLevel:`b`6 You're too broke`n");
				output("`4`bHitpoints:`b`6 Probably more than you`n");
				output("`4`bGold:`b`6 Definately richer than you`n");
				output("`4`bWeapon:`b`6 Something good enough to lay the smackdown on you`n");
				output("`4`bArmor:`b`6 Probably something more fashionable than you`n");
				output("`4`bAttack:`b`6 Eleventy billion`n");
				output("`4`bDefense:`b`6 Super Duper`n");
			}
		}
	}
	addnav("Return to the tavern","forest.php?op=tavern");
	break;
case "oldman":
	addnav("Old Man");
	switch($HTTP_GET_VARS[game]){
	case "":
		checkday();
		output("The old man looks up at you, his eyes sunken and hollow.  His red eyes make it seem that he may have been crying recently ");
		output("so you ask him what is bothering him.  \"`7Aah, I met an adventurer in the woods, and figured I'd play a little game ");
		output("with ".($session[user][sex]?"her":"him").  ", but ".($session[user][sex]?"she":"he")." won, and took ");
		output("almost all of my money.");
		output("`n`n`0\"`7Say... why not do an old man a favor and let me try to win some of it back from you?  I can play several ");
		output("games!`0\"");
		$session[user][specialinc]="darkhorse.php";
		$session['user']['specialmisc']="";
		addnav("D?Play Dice Game","forest.php?op=oldman&game=dice");
		addnav("S?Play Stones Game","forest.php?op=oldman&game=stones");
	
		addnav("Return to the tavern","forest.php?op=tavern");
		break;
	case "stones":
		$stones = unserialize($session['user']['specialmisc']);
		if (!is_array($stones)) $stones = array();
		if ($_GET['side']=="likepair") $stones['side']="likepair";
		if ($_GET['side']=="unlikepair") $stones['side']="unlikepair";
		if (isset($_POST['bet'])) $stones['bet']=min($session['user']['gold'],abs((int)$_POST['bet']));
		if ($stones['side']==""){
			output("`3The old man explains his game, \"`7I have a bag with 6 red stones, and 10 blue stones in it.  You can choose between 'like pair' or 'unlike pair.'  I will");
			output("then draw out pairs of stones two at a time.  If they are the same color as each other, they go to which ever of us is 'like pair,'");
			output("and otherwise they go to which ever of us is 'unlike pair.'  Whoever has the most stones at the end will win.  If we have the same number,");
			output("then it is a draw, and no one wins.`3\"");
			addnav("Never Mind","forest.php?op=oldman");
			addnav("Like Pair","forest.php?op=oldman&game=stones&side=likepair");
			addnav("Unlike Pair","forest.php?op=oldman&game=stones&side=unlikepair");
			$stones['red']=6;
			$stones['blue']=10;
			$stones['player']=0;
			$stones['oldman']=0;
		}elseif ($stones['bet']==0){
			output("`3\"`7".($stones['side']=="likepair"?"Like pair for you, and unlike pair":"Unlike pair for you, and like pair")." for me it is then!");
			output("How much do you bet?`3\"");
			output("<form action='forest.php?op=oldman&game=stones' method='POST'><input name='bet' id='bet'><input type='submit' class='button' value='Bet'></form>",true);
			output("<script type='text/javascript'>document.getElementById('bet').focus();</script>",true);
			addnav("","forest.php?op=oldman&game=stones");
			addnav("Never Mind","forest.php?op=oldman");
		}elseif ($stones['red']+$stones['blue'] > 0 && $stones['oldman']<=8 && $stones['player']<=8){
			$s1=""; $s2="";
			$rstone = "`\$red`3";
			$bstone = "`!blue`3";
			while ($s1=="" || $s2==""){
				$s1 = e_rand(1,($stones['red']+$stones['blue']));
				if ($s1<=$stones['red']) {
					$s1=$rstone;
					$stones['red']--;
				}else{
					$s1=$bstone;
					$stones['blue']--;
				}
				if ($s2=="") {$s2=$s1; $s1="";}
			}
			output("`3The old man reaches in to his bag and withdraws two stones.  They are $s1 and $s2.  Your bet is `^{$stones['bet']}`3.`n`n");
			if ($stones['side']=="likepair"){
				output("Since you are like pairs, ");
				if ($s1==$s2) {
					output("the old man places the stones in your pile.");
					$stones['player']+=2;
				}else{
					output("the old man places the stones in his pile.");
					$stones['oldman']+=2;
				}
			}else{
				output("Since you are unlike pairs, ");
				if ($s1==$s2) {
					output("the old man places the stones in his pile.");
					$stones['oldman']+=2;
				}else{
					output("the old man places the stones in your pile.");
					$stones['player']+=2;
				}
			}
			output("`n`nYou currently have `^{$stones['player']}`3 stones in your pile, and the old man has `^{$stones['oldman']}`3 stones.");
			output("`n`nThere are {$stones['red']} $rstone stones and {$stones['blue']} $bstone stones in the bag yet.");
			addnav("Continue","forest.php?op=oldman&game=stones");
		}else{
			if ($stones['player']>$stones['oldman']){
				output("`3Having defeated the old man at his game, you claim your `^{$stones['bet']}`3 gold.");
				$session['user']['gold']+=$stones['bet'];
				debuglog("won {$stones['bet']} gold in the stones game");
			}elseif ($stones['player']<$stones['oldman']){
				output("`3Having defeated you at his game, the old man claims your `^{$stones['bet']}`3 gold.");
				$session['user']['gold']-=$stones['bet'];
				debuglog("lost {$stones['bet']} gold in the stones game");
			}else{
				output("`3Having tied the old man, you call it a draw.");
			}
			$stones=array();
			addnav("Play again?","forest.php?op=oldman&game=stones");
			addnav("Other Games","forest.php?op=oldman");
			addnav("Return to the tavern","forest.php?op=tavern");
		}
		$session['user']['specialmisc']=serialize($stones);
		break;
	case "guess":
		if ($session[user][gold]>0){
			$bet = abs((int)$HTTP_GET_VARS[bet] + (int)$HTTP_POST_VARS[bet]);
			if ($bet<=0){
				output("`3\"`!You have 6 tries to guess the number I am thinking of, from 1 to 100.  Each time I will tell you if you are too high or too low.`3\"`n`n");
				output("`3\"`!How much would you bet young ".($session[user][sex]?"lady":"man")."?`3\"");
				output("<form action='forest.php?op=oldman&game=guess' method='POST'><input name='bet'><input type='submit' class='button' value='Bet'></form>",true);
				addnav("","forest.php?op=oldman&game=guess");
				$session[user][specialmisc]=e_rand(1,100);
			}else if($bet>$session[user][gold]){
				output("`3The old man reaches out with his stick and pokes your coin purse.  \"`!I don't believe you have `^$bet`! gold!`3\" he declares.`n`n");
				output("Desperate to really show him good, you open up your purse and spill out it's contents: `^".$session[user][gold]."`3 gold.");
				output("`n`nEmbarrased, you think you'll head back to the tavern.");
				addnav("Return to the tavern","forest.php?op=tavern");
			}else{
				if ($HTTP_POST_VARS[guess]!==NULL){
					$try = (int)$HTTP_GET_VARS[try];
					if ($HTTP_POST_VARS[guess]==$session[user][specialmisc]){
						if ($try == 1) {
							output("`3\"`!INCREDIBLE!!!!`3\" the old man shouts, \"`!You guessed the number in only `^one try`!! Well, congratulations to you, and I am thoroughly impressed! It is almost as if you read my mind.`3\" He looks at you suspiciously and thinks about trying to make off with your winnings, but remembers your seemingly psychic abilities and hands over the `^$bet`3 gold that he owes you.");
						} else {
							output("`3\"`!AAAH!!!!`3\" the old man shouts, \"`!You guessed the number in only $try tries!  It was `^".$session[user][specialmisc]."`!!!  Well, congratulations to you, ");
							output("I think I'll just be going now... `3\" he says as he heads for the door.  A swift blow from your ".$session[user][weapon]);
							output(" knocks him back in to his seat.  You help yourself to his coinpurse, retrieving the `^$bet`3 gold that he owes you.");
						}
						$session[user][gold]+=$bet;
						debuglog("won $bet gold in the guessing game");
						$session[user][specialinc]="darkhorse.php";
						addnav("Return to the tavern","forest.php?op=tavern");
					}else{
						if ($HTTP_GET_VARS[try]>=6){
							output("`3The old man chuckles.  \"`!The number was `^".$session[user][specialmisc]."`!,`3\" he says.  You, being the honorable citizen ");
							output("that you are, give the man the `^$bet`3 gold that you owe him, ready to be away from him.");
							$session[user][specialinc]="darkhorse.php";
							$session[user][gold]-=$bet;
							debuglog("lost $bet gold in the guessing game");
							addnav("Return to the tavern","forest.php?op=tavern");
						}else{
							if ((int)$HTTP_POST_VARS[guess]>$session[user][specialmisc]){
								output("`3\"`!Nope, not `^".(int)$HTTP_POST_VARS[guess]."`!, it's lower than that!  That was try `^$try`!.`3\"`n`n");
							}else{
								output("`3\"`!Nope, not `^".(int)$HTTP_POST_VARS[guess]."`!, it's higher than that!  That was try `^$try`!.`3\"`n`n");
							}
							output("`3You have bet `^$bet`3.  What is your guess?");
							output("<form action='forest.php?op=oldman&game=guess&bet=$bet&try=".(++$try)."' method='POST'><input name='guess'><input type='submit' class='button' value='Guess'></form>",true);
							addnav("","forest.php?op=oldman&game=guess&bet=$bet&try=$try");
						}
					}
				}else{
					output("`3You have bet `^$bet`3.  What is your guess?");
					output("<form action='forest.php?op=oldman&game=guess&bet=$bet&try=1' method='POST'><input name='guess'><input type='submit' class='button' value='Guess'></form>",true);
					addnav("","forest.php?op=oldman&game=guess&bet=$bet&try=1");
				}
			}
		}else{
			output("`3The old man reaches out with his stick and pokes your coin purse.  \"`!Empty?!?!  How can you bet with no money??`3\" he shouts.");
			output("  With that, he turns back to his dice, apparently having already forgotten his anger.");
			addnav("Return to the tavern","forest.php?op=tavern");
			//$session[user][specialinc]="darkhorse.php";
		}
		break;
	case "dice":
		if ($session[user][gold]>0){
			$bet = abs((int)$HTTP_GET_VARS[bet] + (int)$HTTP_POST_VARS[bet]);
			if ($bet<=0){
				output("`3\"`!You get to roll a die, and choose to keep or pass on the roll.  If you pass, you get up to two more chances");
				output(" to roll, for a total of three rolls.  Once you keep your roll (or on the third roll), I will do the same.  ");
				output("In the end, if my die is higher than yours, I win, if yours is higher, you win, and if they are a tie, ");
				output("neither of us wins, and we each keep our bet.`3\"`n`n");
				output("`3\"`!How much would you bet young ".($session[user][sex]?"lady":"man")."?`3\"");
				output("<form action='forest.php?op=oldman&game=dice' method='POST'><input name='bet'><input type='submit' class='button' value='Bet'></form>",true);
				addnav("","forest.php?op=oldman&game=dice");
			}else if($bet>$session[user][gold]){
				output("`3The old man reaches out with his stick and pokes your coin purse.  \"`!I don't believe you have `^$bet`! gold!`3\" he declares.`n`n");
				output("Desperate to really show him good, you open up your purse and spill out it's contents: `^".$session[user][gold]."`3 gold.");
				output("`n`nEmbarrased, you think you'll head back to the tavern.");
				addnav("Return to the tavern","forest.php?op=tavern");
			}else{
				if ($HTTP_GET_VARS[what]!="keep"){
					$session[user][specialmisc]=e_rand(1,6);
					$try=$HTTP_GET_VARS[try];
					$try++;
					output("You roll your ".($try==1?"first":($try==2?"second":"third"))." die, and it comes up as `b".$session[user][specialmisc]."`b`n`n");
					output("`3You have bet `^$bet`3.  What do you do?");
					addnav("Keep","forest.php?op=oldman&game=dice&what=keep&bet=$bet");
					if ($try<3) addnav("Pass","forest.php?op=oldman&game=dice&what=pass&try=$try&bet=$bet");
				}else{
					output("Your final roll was `b".$session[user][specialmisc]."`b, the old man will now try to beat it:`n`n");
					$r = e_rand(1,6);
					output("The old man rolls a $r...`n");
					if ($r>$session[user][specialmisc] || $r==6){
						output("\"`7I think I'll stick with that roll!`0\" he says.`n");
					}else{
						$r = e_rand(1,6);
						output("The old man rolls again and gets a $r...`n");
						if ($r>=$session[user][specialmisc]){
							output("\"`7I think I'll stick with that roll!`0\" he says.`n");
						}else{
							$r = e_rand(1,6);
							output("The old man rolls his final roll and gets a $r...`n");
						}
					}
					if ($r>$session[user][specialmisc]){
						output("`n\"`7Yeehaw, I knew the likes of you would never stand up to the likes of me!`0\" exclaims the old man as you hand him your `^$bet`0 gold.");
						$session[user][gold]-=$bet;
						debuglog("lost $bet gold at dice");
					}else{
						if ($r==$session[user][specialmisc]){
							output("`n\"`7Yah... well, looks as though we tied.`0\" he says.");
						}else{
							output("`n\"`7Aaarrgh!!!  How could the likes of you beat me?!?!?`0\" shouts the old man as he gives you the gold he owes.");
							$session[user][gold]+=$bet;
							debuglog("won $bet gold at dice");
						}
					}
					addnav("Return to the tavern","forest.php?op=tavern");
				}
			}
		}else{
			output("`3The old man reaches out with his stick and pokes your coin purse.  \"`!Empty?!?!  How can you bet with no money??`3\" he shouts.");
			output("  With that, he turns back to his dice, apparently having already forgotten his anger.");
			addnav("Return to the tavern","forest.php?op=tavern");
		}
		break;	
	}
	break; //end of old man.
case "leave":
	output("You duck out of the tavern, and wander in to the thick foliage around you.  That strange mist ");
	output("revisits you, making your mind buzz.  The mist clears, and you find yourself again in the forest ");
	output("that you are familiar with.  How exactly you got to the tavern is not exactly clear.");
	$session[user][specialinc]="";
	break;
case "leaveleave":
	$session[user][specialinc]="";
	redirect("forest.php");
}
output("</span>",true);
?>
