<?
if (!isset($session)) exit();
if ($HTTP_GET_VARS[op]==""){
  output("`3An old man stops you as you wander through the woods.  \"`!How would you like to play a little ");
	output("guessing game?`3\" he asks.  Knowing his sort, you know he will insist on a small wager if you do. ");
	output("`n`nDo you wish to play his game?`n`n<a href='forest.php?op=yes'>Yes</a>`n<a href='forest.php?op=no'>No</a>",true);
	addnav("Yes","forest.php?op=yes");
	addnav("No","forest.php?op=no");
	addnav("","forest.php?op=yes");
	addnav("","forest.php?op=no");
	$session[user][specialinc]="oldmanbet.php";
}else if($HTTP_GET_VARS[op]=="yes"){
  if ($session[user][gold]>0){
		$session[user][specialinc]="oldmanbet.php";
		$bet = abs((int)$HTTP_GET_VARS[bet] + (int)$HTTP_POST_VARS[bet]);
		if ($bet<=0){
			output("`3\"`!You have 6 tries to guess the number I am thinking of, from 1 to 100.  Each time I will tell you if you are too high or too low.`3\"`n`n");
			output("`3\"`!How much would you bet young ".($session[user][sex]?"lady":"man")."?`3\"");
			output("<form action='forest.php?op=yes' method='POST'><input name='bet' id='bet'><input type='submit' class='button' value='Bet'></form>",true);
			output("<script language='JavaScript'>document.getElementById('bet').focus();</script>",true); // Bravebrain
			addnav("","forest.php?op=yes");
			$session[user][specialmisc]=e_rand(1,100);
		}else if($bet>$session[user][gold]){
		  output("`3The old man reaches out with his stick and pokes your coin purse.  \"`!I don't believe you have `^$bet`! gold!`3\" he declares.`n`n");
			output("Desperate to really show him good, you open up your purse and spill out it's contents: `^".$session[user][gold]."`3 gold.");
			output("`n`nEmbarrased, you think you'll head back in to the forest.");
			$session[user][specialinc]="";
			//addnav("Return to the forest","forest.php");
		}else{
			if ($HTTP_POST_VARS[guess]!==NULL){
			  $try = (int)$HTTP_GET_VARS[try];
			  if ($HTTP_POST_VARS[guess]==$session[user][specialmisc]){
			  	if ($try == 1) {
						output("`3\"`!INCREDIBLE!!!!`3\" the old man shouts, \"`!You guessed the number in only `^one try`!! Well, congratulations to you, and I am thoroughly impressed! It is almost as if you read my mind.`3\" He looks at you suspiciously and thinks about trying to make off with your winnings, but remembers your seemingly psychic abilities and hands over the `^$bet`3 gold that he owes you.");
					} else {
						output("`3\"`!AAAH!!!!`3\" the old man shouts, \"`!You guessed the number in only $try tries!  It was `^".$session[user][specialmisc]."`!!!  Well, congratulations to you, ");
						output("I think I'll just be going now... `3\" he says as he heads for the underbrush.  A swift blow from your ".$session[user][weapon]);
						output(" knocks him unconscious.  You help yourself to his coinpurse, retrieving the `^$bet`3 gold that he owes you.");
					}
					$session[user][gold]+=$bet;
					debuglog("won $bet gold from the old man in the forest");
					$session[user][specialinc]="";
				}else{
				  if ($HTTP_GET_VARS[try]>=6){
					  output("`3The old man chuckles.  \"`!The number was `^".$session[user][specialmisc]."`!,`3\" he says.  You, being the honorable citizen ");
						output("that you are, give the man the `^$bet`3 gold that you owe him, ready to be away from here.");
						$session[user][specialinc]="";
						$session[user][gold]-=$bet;
						debuglog("lost $bet gold to the old man in the forest");
					}else{
					  if ((int)$HTTP_POST_VARS[guess]>$session[user][specialmisc]){
						  output("`3\"`!Nope, not `^".(int)$HTTP_POST_VARS[guess]."`!, it's lower than that!  That was try `^$try`!.`3\"`n`n");
						}else{
						  output("`3\"`!Nope, not `^".(int)$HTTP_POST_VARS[guess]."`!, it's higher than that!  That was try `^$try`!.`3\"`n`n");
						}
						output("`3You have bet `^$bet`3.  What is your guess?");
						output("<form action='forest.php?op=yes&bet=$bet&try=".(++$try)."' method='POST'><input name='guess' id='guess'><input type='submit' class='button' value='Guess'></form>",true);
						output("<script language='JavaScript'>document.getElementById('guess').focus();</script>",true); // Bravebrain
						addnav("","forest.php?op=yes&bet=$bet&try=$try");
					}
				}
			}else{
				output("`3You have bet `^$bet`3.  What is your guess?");
				output("<form action='forest.php?op=yes&bet=$bet&try=1' method='POST'><input name='guess'><input type='submit' class='button' value='Guess'></form>",true);
				addnav("","forest.php?op=yes&bet=$bet&try=1");
			}
		}
	}else{
	  output("`3The old man reaches out with his stick and pokes your coin purse.  \"`!Empty?!?!  How can you bet with no money??`3\" he shouts.");
		output("  With that, he turns with a HARUMPH, and dissapears in to the underbrush.");
		//addnav("Return to the forest","forest.php");
		$session[user][specialinc]="";
	}
}else if($HTTP_GET_VARS[op]=="no"){
  output("`3Afraid to part with your precious precious money, you decline the old man his game.  There wasn't ");
	output("much point to it anyhow, as you certainly would have won.  Yep, definately not afraid of the old man, nope.");
	//addnav("Return to the forest","forest.php");
	$session[user][specialinc]="";
}
?>
