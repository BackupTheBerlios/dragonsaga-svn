<?
require_once "common.php";
addcommentary();
checkday();

if ($session[user][slainby]!=""){
	page_header("You have been slain!");
		output("`\$You were slain in ".$session[user][killedin]."`\$ by `%".$session[user][slainby]."`\$.  They cost you 5% of your experience, and took any gold you had.  Don't you think it's time for some revenge?");
	addnav("Continue",$REQUEST_URI);
	$session[user][slainby]="";
	page_footer();
}

$buff = array("name"=>"`!Lover's Protection","rounds"=>60,"wearoff"=>"`!You miss ".($session['user']['sex']?"Seth":"`5Violet`")."!.`0","defmod"=>1.2,"roundmsg"=>"Your lover inspires you to keep safe!","activate"=>"defense");

page_header("The Boar's Head Inn");
output("<span style='color: #9900FF'>",true);
output("`c`bThe Boar's Head Inn`b`c");
if ($HTTP_GET_VARS[op]=="strolldown"){
	output("You stroll down the stairs of the inn, once again ready for adventure!  ");
}
if ($HTTP_GET_VARS[op]==""){
	output("You duck in to a dim tavern that you know well.  The pungent aroma of pipe tobacco fills ");
	output("the air.");
}
if ($HTTP_GET_VARS[op]=="" || $HTTP_GET_VARS[op]=="strolldown"){
	output("  You wave to several patrons that you know, and wink at ".
				($session[user][sex]?
				"`^Seth`0 who is tuning his harp by the fire.":
				"`5Violet`0 who is serving ale to some locals.").
				" Cedrik the innkeep stands behind his counter, chatting with someone.  You can't quite"
				." make out what he is saying, but it's something about ");

	switch (e_rand(1,5)){
		case 1:
			output("dragons.");
			break;
		case 2:
			output("Seth.");
			break;
		case 3:
			output("Violet.");
			break;
		case 4:
			output("MightyE.");
			break;
		case 5:
			output("fine ales.");
	}
	if (getsetting("pvp",1)) {
		output(" Dag Durnick sits, sulking in the corner with a pipe clamped firmly in his mouth. ");
	}
	output("`n`nThe clock on the mantle reads `6".getgametime()."`0.");
	addnav("Things to do");
	if ($session[user][sex]==0) addnav("V?Flirt with Violet","inn.php?op=violet");
	if ($session[user][sex]==1) addnav("V?Chat with Violet","inn.php?op=violet");
	addnav("S?Talk to Seth the Bard","inn.php?op=seth");
	addnav("Converse with patrons","inn.php?op=converse");
	addnav("B?Talk to Cedrik the Barkeep","inn.php?op=bartender");
	if (getsetting("pvp",1)) {
		addnav("D?Talk to Dag Durnick","dag.php");
	}
	addnav("Other");
	addnav("Get a room (log out)","inn.php?op=room");
	addnav("Return to the village","village.php");
}else{
  switch($HTTP_GET_VARS[op]){
	  case "violet":
			/*
			Wink
			Kiss her hand
			Peck her on the lips
			Sit her on your lap
			Grab her backside
			Carry her upstairs
			Marry her
			*/
			if ($session[user][sex]==1){
				if ($HTTP_GET_VARS[act]==""){
					addnav("Gossip","inn.php?op=violet&act=gossip");
					addnav("Ask if your ".$session[user][armor]." makes you look fat","inn.php?op=violet&act=fat");
					output("You go over to `5Violet`0 and help her with the ales she is carrying.  Once they are passed out, ");
					output("she takes a cloth and wipes the sweat off of her brow, thanking you much.  Of course you didn't ");
					output("mind, as she is one of your oldest and truest friends!");
				}else if($HTTP_GET_VARS[act]=="gossip"){
					output("You and `5Violet`0 gossip quietly for a few minutes about not much at all.  She offers you a pickle.  ");
					output("You accept, knowing that it's in her nature to do so as a former pickle wench.  After a few minutes, ");
					output("Cedrik begins to cast burning looks your way, and you decide you had best let Violet get back to work.");
				}else if($HTTP_GET_VARS[act]=="fat"){
					$charm = $session[user][charm]+e_rand(-1,1);
					output("Violet looks you up and down very seriously.  Only a friend can be truly honest, and that is why you ");
					output("asked her.  Finally she reaches a conclusion and states, \"`%");
					switch($charm){
						case -3:
						case -2:
						case -1:
						case 0:
							output("Your outfit doesn't leave much to the imagination, but some things are best not thought about at all!  Get some less revealing clothes as a public service!");
							break;
						case 1:
						case 2:
						case 3:
							output("I've seen some lovely ladies in my day, but I'm afraid you aren't one of them.");
							break;
						case 4:
						case 5:
						case 6:

							output("I've seen worse my friend, but only trailing a horse.");
							break;
						case 7:
						case 8:
						case 9:
							output("You're of fairly average appearance my friend.");
							break;
						case 10:
						case 11:
						case 12:
							output("You certainly are something to look at, just don't get too big of a head about it, eh?");
							break;
						case 13:
						case 14:
						case 15:
							output("You're quite a bit better than average!");
							break;
						case 16:
						case 17:
						case 18:
							output("Few women could count themselves to be in competition with you!");
							break;
						default:
							output("I hate you, why, you are simply the most beautiful woman ever!");
					}
					output("`0\"");
				}
			}
			if ($session[user][sex]==0){
				  //$session[user][seenlover]=0;
			  if ($session[user][seenlover]==0){
			  	  if ($session['user']['marriedto']==4294967295){
				  	if (e_rand(1, 4)==1){
					  output("You head over to cuddle Violet and kiss her about the face and neck, but she grumbles something about");
					  switch(e_rand(1,4)){
					  case 1:
					  	output("being  too  busy serving these pigs,");
						break;
					  case 2:
					  	output("\"that time of month,\"");
						break;
					  case 3:
					  	output("\"a   little   cold...  *cough cough* see?\"");
					  	break;
					  case 4:
					  	output("men all being pigs,");
						break;
					  }
					  output("and  with a comment like that, you storm away from her!");
					  $session['user']['charm']--;
					  output("`n`n`^You LOSE a charm point!");
					} else {
						output("You  and `5Violet`0 take some time to yourselves, and you leave the inn, positively glowing!");
						$session['bufflist']['lover']=$buff;
						$session['user']['charm']++;
						output("`n`n`^You gain a charm point!");
					}
					$session['user']['seenlover']=1;
				  } elseif ($HTTP_GET_VARS[flirt]==""){
						output("You stare dreamily across the room at `5Violet`0, who leans across a table ");
						output("to serve a patron a drink.  In doing so, she shows perhaps a bit more skin ");
						output("than is necessary, but you don't feel the need to object.");
						addnav("Flirt");
						addnav("Wink","inn.php?op=violet&flirt=1");
						addnav("Kiss her hand","inn.php?op=violet&flirt=2");
						addnav("Peck her on the lips","inn.php?op=violet&flirt=3");
						addnav("Sit her on your lap","inn.php?op=violet&flirt=4");
						addnav("Grab her backside","inn.php?op=violet&flirt=5");
						addnav("Carry her upstairs","inn.php?op=violet&flirt=6");
						addnav("Marry her","inn.php?op=violet&flirt=7");
					}else{
					  $c = $session[user][charm];
						$session[user][seenlover]=1;
					  switch($HTTP_GET_VARS[flirt]){
						  case 1:
							  if (e_rand($c,2)>=2){
								  output("You wink at `5Violet`0, and she gives you a warm smile in return.");
									if ($c<4) $c++;
								}else{
								  output("You wink at `5Violet`0, but she pretends not to notice.");
								}
							  break;
						  case 2:
							  if (e_rand($c,4)>=4){
								  output("You stroll confidently across the room toward `5Violet`0.  Taking hold of her ");
									output("hand, you kiss it gently, your lips remaining for only a few seconds.  `5Violet`0 ");
									output("blushes and tucks a strand of hair behind her ear as you walk away, then presses ");
									output("the back side of her hand longingly against her cheek while watching your retreat.");
									if ($c<7) $c++;
								}else{
								  output("You stroll confidently across the room toward `5Violet`0, and grab at her hand.  ");
									output("`n`nBut `5Violet`0 takes her hand back and asks if perhaps you'd like an ale.");
								}
							  break;
						  case 3:
							  if (e_rand($c,7)>=7){
								  output("Standing with your back against a wooden column, you wait for `5Violet`0 to wander ");
									output("your way when you call her name.  She approaches, a hint of a smile on her face.  ");
									output("You grab her chin, lift it slightly, and place a firm but quick kiss on her plump ");
									output("lips.");
									if ($c<11) $c++;
								}else{
								  output("Standing with your back against a wooden column, you wait for `5Violet`0 to wander ");
									output("your way when you call her name.  She smiles and apologizes, insisting that she is ");
									output("simply too busy to take a moment from her work.");
								}
							  break;
						  case 4:
							  if (e_rand($c,11)>=11){
								  output("Sitting at a table, you wait for `5Violet`0 to come your way.  When she does so, you ");
									output("reach up and grab her firmly by the waist, pulling her down on to your lap.  She laughs ");
									output("and throws her arms around your neck in a warm hug before thumping you on the chest, ");
									output("standing up, and insisting that she really must get back to work.");
									if ($c<14) $c++;
								}else{
								  output("Sitting at a table, you wait for `5Violet`0 to come your way.  When she does so, you ");
									output("reach up to grab her by the waist, but she deftly dodges, careful not to spill the ");
									output("ale that she's carrying.");
									if ($c>0 && $c<10) $c--;
								}
							  break;
						  case 5:
							  if (e_rand($c,14)>=14){
								  output("Waiting for `5Violet`0 to brush by you, you firmly palm her backside.  She turns and ");
									output("gives you a warm, knowing smile.");
									if ($c<18) $c++;
								}else{
								  output("Waiting for `5Violet`0 to brush by you, you firmly palm her backside.  She turns and ");
									output("slaps you across the face.  Hard.  Perhaps you should go a little slower.");
									//$session[user][hitpoints]=1;
									if ($c>0 && $c<13) $c--;
								} 
							  break;
						  case 6:
							  if (e_rand($c,18)>=18){
  							  output("Like a whirlwind, you sweep through the inn, grabbing `5Violet`0, who throws her arms ");
									output("around your neck, and whisk her upstairs to her room there.  Not more than 10 minutes later ");
									output("you stroll down the stairs, smoking a pipe, and grinning from ear to ear.  ");
									if ($session['user']['turns']>0){
									  output("You feel exhausted!  ");
										$session['user']['turns']-=2;
										if ($session['user']['turns']<0) $session['user']['turns']=0;
									}
									addnews("`@".$session[user][name]."`@ and `5Violet`@ were seen heading up the stairs in the inn together.");
									if ($c<25) $c++;
								}else{
								  output("Like a whirlwind, you sweep through the inn, and grab for `5Violet`0.  She turns and ");
									output("slaps your face!  \"`%What sort of girl do you think I am, anyhow?`0\" she demands! ");
									if ($c>0) $c--;
								}
							  break;
							case 7:
								output("`5Violet`0 is working feverishly to serve patrons of the inn.  You stroll up to her ");
								output("and take the mugs out of her hand, placing them on a nearby table.  Admidst her protests ");
								output("you kneel down on one knee, taking her hand in yours.  She quiets as you stare up at her ");
								output("and utter the question that you never thought you'd utter.  She stares at you and you ");
								output("immediately know the answer by the look on her face. ");
							  if ($c>=22){
								  output("`n`nIt is a look of exceeding happiness.  \"`%Yes!`0\" she says, \"`%Yes, yes yes!!!`0\"");
									output("  Her final confirmations are buried in a flurry of kisses about your face and neck. ");
									output("`n`nThe next days are a blur; you and `5Violet`0 are married in the abbey down the street, ");
									output("in a gorgeous ceremony with many frilly girly things.");
									addnews("`&".$session[user][name]." and `%Violet`& are joined today in joyous matrimony!!!");
									$session['user']['marriedto']=4294967295; // int max. I very much doubt that anyone is going to be
									$session['bufflist']['lover']=$buff;
								}else{
								  output("`n`nIt is a look of sadness.  \"`%No`0,\" she says, \"`%I'm not yet ready to settle down`0.\"");
									output("`n`nDisheartened, you no longer possess the will to pursue any more forest adventures today.");
									$session[user][turns]=0;
								}
						}
						if ($c > $session[user][charm]) output("`n`n`^You gain a charm point!");
						if ($c < $session[user][charm]) output("`n`n`\$You LOSE a charm point!");
						$session[user][charm]=$c;
					}
				}else{
				  output("You think you had better not push your luck with `5Violet`0 today.");
				}
			}else{
			  //sorry, no lezbo action here.
			}
			break;
		case "seth":
			/*
			Wink
			Flutter Eyelashes
			Drop Hankey
			Ask the bard to buy you a drink
			Kiss the bard soundly
			Completely seduce the bard
			Marry him
			*/
      if ($HTTP_GET_VARS[subop]=="" && $HTTP_GET_VARS[flirt]==""){
        output("Seth looks at you expectantly.");
        addnav("Ask Seth to entertain","inn.php?op=seth&subop=hear");
        if ($session[user][sex]==1){
			if ($session['user']['marriedto']==4294967295) {
				addnav("Flirt with Seth", "inn.php?op=seth&flirt=1");
			} else {
				addnav("Flirt");
				addnav("Wink","inn.php?op=seth&flirt=1");
				addnav("Flutter Eyelashes","inn.php?op=seth&flirt=2");
				addnav("Drop Hanky","inn.php?op=seth&flirt=3");
				addnav("Ask him to buy you a drink","inn.php?op=seth&flirt=4");
				addnav("Kiss him soundly","inn.php?op=seth&flirt=5");
				addnav("Completely seduce the bard","inn.php?op=seth&flirt=6");
				addnav("Marry him","inn.php?op=seth&flirt=7");
			}
		} else {
			addnav("Ask Seth how he likes your new ".$session[user][armor],"inn.php?op=seth&act=armor");
		}
      }
			if ($HTTP_GET_VARS[act]=="armor"){
				$charm = $session[user][charm]+e_rand(-1,1);
				output("Seth looks you up and down very seriously.  Only a friend can be truly honest, and that is why you ");
				output("asked him.  Finally he reaches a conclusion and states, \"`%");
				switch($charm){
					case -3:
					case -2:
					case -1:
					case 0:
						output("You make me glad I'm not gay!");
						break;
					case 1:
					case 2:
					case 3:
						output("I've seen some handsome men in my day, but I'm afraid you aren't one of them.");
						break;
					case 4:
					case 5:
					case 6:
						output("I've seen worse my friend, but only trailing a horse.");
						break;
					case 7:
					case 8:
					case 9:
						output("You're of fairly average appearance my friend.");
						break;
					case 10:
					case 11:
					case 12:
						output("You certainly are something to look at, just don't get too big of a head about it, eh?");
						break;
					case 13:
					case 14:
					case 15:
						output("You're quite a bit better than average!");
						break;
					case 16:
					case 17:
					case 18:
						output("Few women would be able to resist you!");
						break;
					default:
						output("I hate you, why, you are simply the most handsome man ever!");
				}
				output("`0\"");
			}
      if ($HTTP_GET_VARS[subop]=="hear"){
        //$session[user][seenbard]=0;
        if($session[user][seenbard]){
          output("Seth clears his throat and drinks some water.  \"I'm sorry, my throat is just too dry.\"");
         // addnav("Return to the inn","inn.php");
        }else{
          $rnd = e_rand(0,18);
          output("Seth clears his throat and begins:`n`n`^");
          $session[user][seenbard]=1;
          switch ($rnd){
            case 0:
              output("`@Green Dragon`^ is green. `n`@Green Dragon`^ is fierce. `nI fancy for `na `@Green Dragon`^ to pierce. ");
              output("`n`n`0You gain TWO forest fights for today!");
              $session[user][turns]+=2;
              break;
            case 1:
              output("Mireraband I scoff at thee and tickeleth your toes. `nFor they smell most foul and seethe a stench far greater than you know! ");
              output("`n`n`0You feel jovial, and gain an extra forest fight.");
              $session[user][turns]++;
              break;
            case 2:
              output("Membrain Man, Membrain Man. `nMembrain man hates ".$session[user][name]."`^ man. `nThey have a fight, Membrain wins. `nMembrain Man. ");
              output("`n`n`0You're not quite sure what to make of this... you merely back away, and think you'll visit Seth when he's feeling better.  Having");
							output("rested a while though, you think you could face another forest creature.");
							$session[user][turns]++;
              break;
            case 3:
              output("Gather 'round and I'll tell you a tale `nmost terrible and dark `nof Cedrik and his unclean beer `nand how he hates this bard! ");
              output("`n`n`0You realize he's right, Cedrik's beer is really nasty.  That's why most patrons prefer his ale.  Though you don't really gain anything from the tale from Seth, you do happen to notice a few gold on the ground!");
			  $gain = e_rand(10,50);
              $session[user][gold]+=$gain;
			  debuglog("found $gain gold near Seth");
              break;
            case 4:
              output("So a pirate goes in to a bar with a steering wheel in his pants. `nThe bartender says, \"You know you have a steering wheel in your pants.\" `nThe pirate replies, \"Yaaarr, 'tis drivin' me nuts!\" ");
              output("`n`n`0With a good hearty chuckle in your soul, you advance on the world, ready for anything!");
              $session[user][hitpoints]=round($session[user][maxhitpoints]*1.2,0);
              break;
            case 5:
              output("Listen close and hear me well:  every second we draw even closer to death.  *wink*");
              output("`n`n`0Depressed, you head for home... and lose a forest fight!");
              $session[user][turns]--;
							if ($session[user][turns]<0) $session[user][turns]=0;
              break;
            case 6:
              output("I love MightyE, MightyE weaponry, I love MightyE, MightyE weaponry, I love MightyE, MightyE weaponry, nothing kills as good as MightyE... WEAPONRY!");
              output("`n`n`0You think Seth is quite correct... you want to go out and kill something.  You leave and think about bees and fish for some reason.");
              $session[user][turns]++;
              break;
            case 7:
              output("`0Seth seems to sit up and prepare himself for something impressive. He then burps loudly in your face.  \"`^Was that entertaining enough?`0\"");
              output("`n`n`0The smell is overwhelming, you feel a little ill and lose some hitpoints.");
              $session[user][hitpoints]-= round($session[user][maxhitpoints] * 0.1,0);
              if ($session[user][hitpoints]<=0) $session[user][hitpoints]=1;
              break;
            case 8:
				if ($session['user']['gold'] >= 5) {
	              output("`0\"`^What is the sound of one hand clapping?`0\" asks Seth.  While you ponder this connundrum, Seth \"liberates\" a small entertainment fee from your purse.");
	              output("`n`nYou lose 5 gold!");
	              $session[user][gold]-=5;
				  debuglog("lost 5 gold to Seth");
				} else {
	              output("`0\"`^What is the sound of one hand clapping?`0\" asks Seth.  While you ponder this connundrum, Seth attempts to \"liberate\" a small entertainment fee from your purse, but doesn't find enough to bother with.");
				}
              break;
            case 9:
              output("What do you call a fish with no eyes?`n`nA fsshh.");
              output("`n`nYou groan as Seth laughs heartily.  Shaking your head, you notice a gem in the dust.");
              $session[user][gems]++;
			  debuglog("got 1 gem from Seth");
              break;
            case 10:
              output("Seth plays a soft but haunting melody.");
              output("`n`nYou feel relaxed, and your wounds seem to fade away.");
              if ($session['user']['hitpoints']<$session['user']['maxhitpoints']) $session[user][hitpoints]=$session[user][maxhitpoints];
              break;
            case 11:
              output("Seth plays a melancholy dirge for you.");
              output("`n`nYou feel lower in spirits, you may not be able to face as many villians today.");
              $session[user][turns]--;
              if ($session[user][turns]<0) $session[user][turns]=0;
              break;
            case 12:
              output("The ants go marching one by one, hoorah, hoorah.`nThe ants go marching one by one, hoorah, hoorah!`nThe ants go marching one by one and the littlest one stops to suck his thumb,`nand they all go marching down, to the ground, to get out of the rain...`nbum bum bum`nThe ants go marching two by two, hoorah, hoorah!....");
              output("`n`n`0Seth continues to sing, but not wishing to learn how high he can count, you quietly leave.`n`nHaving rested a while, you feel refreshed.");
							$session[user][hitpoints]=$session[user][maxhitpoints];
              break;
            case 13:
              output("There once was a lady from Venus, her body was shaped like a ...");
              if ($session[user][sex]==1){
                output("`n`n`0Seth is cut short by a curt slap across his face!  Feeling rowdy, you gain a forest fight.");
              }else{
                output("`n`n`0Seth is cut short as you burst out in laughter, not even having to hear the end of the rhyme.  Feeling inspired, you gain a forest fight.");
              }
              $session[user][turns]++;
              break;
            case 14:
              output("Seth plays a rousing call-to-battle that wakes the warrior spirit inside of you.");
              output("`n`n`0You gain a forest fight!");
              $session[user][turns]++;
              break;
            case 15:
              output("Seth seems preoccupied with your... eyes.");
              if ($session[user][sex]==1){
                output("`n`n`0You receive one charm point!");
                $session[user][charm]++;
              }else{
                output("`n`n`0Furious, you stomp out of the bar!  You gain a forest fight in your fury.");
                $session[user][turns]++;
              }
              break;
            case 16:
              output("Seth begins to play, but a lute string snaps, striking you square in the eye.`n`n`0\"`^Whoops, careful, you'll shoot your eye out kid!`0\"");
              output("`n`nYou lose some hitpoints!");
              $session[user][hitpoints]-=round($session[user][maxhitpoints]*.1,0);
							if ($session[user][hitpoints]<1) $session[user][hitpoints]=1;
              break;
            case 17:
              output("Seth begins to play, but a rowdy patron stumbles past, spilling beer on you.  You miss the performance as you wipe the swill from your ".$session[user][armor].".");
              break;
            case 18:
              output("`0Seth stares at you thoughtfully, obviously rapidly composing an epic poem...`n`n`^U-G-L-Y, You ain't got no aliby -- you ugly, yeah yeah, you ugly!");
              $session[user][charm]--;
              if ($session[user][charm]<0){
                output("`n`n`0If you had any charm, you'd have been offended, instead, Seth breaks a lute string.");
              }else{
                output("`n`n`0Depressed, you lose a charm point.");
              }
              break;
          }
        }
      }
			if ($session[user][sex]==1 && $HTTP_GET_VARS[flirt]<>""){
			  //$session[user][seenlover]=0;
			  if ($session[user][seenlover]==0){
			  	  if ($session['user']['marriedto']==4294967295){
				    if (e_rand(1,4)==1){
					  output("You  head  over  to  snuggle up to Seth  and  kiss him about the face and neck, but he grumbles something about");
					  switch(e_rand(1,4)){
					  case 1:
					    output("being   too  busy  tuning  his lute,");
						break;
					  case 2:
					    output("\"that time of month,\"");
						break;
				 	  case 3:
					    output("\"a   little   cold...  *cough cough* see?\"");
						break;
					  case 4:
					    output("wanting  you  to  fetch  him a beer,");
						break;
					  }
					  output("and  with a comment like that, you storm away from him!");
					  $session['user']['charm']--;
					  output("`n`n`^You LOSE a charm point!");
					}else{
					  output("You  and  Seth  take  some time to yourselves, and you leave the inn, positively glowing!");
					  $session['bufflist']['lover']=$buff;
					  $session['user']['charm']++;
					  output("`n`n`^You gain a charm point!");
					}
				    $session['user']['seenlover']=1;
				  } elseif ($HTTP_GET_VARS[flirt]==""){
					}else{
					  $c = $session[user][charm];
						$session[user][seenlover]=1;
					  switch($HTTP_GET_VARS[flirt]){
						  case 1:
							  if (e_rand($c,2)>=2){
								  output("Seth grins a big toothy grin.  My, isn't the dimple in his chin cute??");
									if ($c<4) $c++;
								}else{
								  output("Seth raises an eyebrow at you, and asks if you have something in your eye.");
								}
							  break;
						  case 2:
							  if (e_rand($c,4)>=4){
								  output("Seth smiles at you and says, \"`^My, what pretty eyes you have`0\"");
									if ($c<7) $c++;
								}else{
									output("Seth smiles, and waves... to the person standing behind you.");
								}
							  break;
						  case 3:
							  if (e_rand($c,7)>=7){
								  output("Seth bends over and retrieves your hanky, while you admire his firm posterior.");
									if ($c<11) $c++;
								}else{
									output("Seth bends over and retrieves your hanky, wipes his nose with it, and gives it back.");
								}
							  break;
						  case 4:
							  if (e_rand($c,11)>=11){
								  output("Seth places his arm around your waist, and escorts you to the bar where he buys you one of the Inn's fine swills.");
									if ($c<14) $c++;
								}else{
								  output("Seth apologizes, \"`^I'm sorry m'lady, I have no money to spare,`0\" as he turns out his moth-riddled pocket.");
									if ($c>0 && $c<10) $c--;
								}
							  break;
						  case 5:
							  if (e_rand($c,14)>=14){
								  output("You walk up to Seth, grab him by the shirt, pull him to his feet, and plant a firm, long kiss right on his handsome lips.  He collapses after, hair a bit disheveled, and short on breath.");
									if ($c<18) $c++;
								}else{
								  output("You duck down to kiss Seth on the lips, but just as you do so, he bends over to tie his shoe.");
									// $session[user][hitpoints]=1; //why the heck was this here???
									if ($c>0 && $c<13) $c--;
								} 
							  break;
						  case 6:
							  if (e_rand($c,18)>=18){
  							  output("Standing at the base of the stairs, you make a come-hither gesture at Seth.  He follows you like a puppydog.");
									if ($session['user']['turns']>0){
									  output("You feel exhausted!  ");
									  $session['user']['turns']-=2;
									  if ($session['user']['turns']<0) $session['user']['turns']=0;
									}
									addnews("`@".$session[user][name]."`@ and `^Seth`@ were seen heading up the stairs in the inn together.");
									if ($c<25) $c++;
								}else{
								  output("\"`^I'm sorry m'lady, but I have a show in 5 minutes`0\"");
									if ($c>0) $c--;
								}
							  break;
							case 7:
								output("Walking up to Seth, you simply demand that he marry you.`n`nHe looks at you for a few seconds.`n`n");
							  if ($c>=22){
									output("\"`^Of course my love!`0\" he says.  The next weeks are a blur as you plan the most wonderous wedding, paid for entirely by Seth, and head on off to the deep forest for your honeymoon.");
									addnews("`&".$session[user][name]." and `^Seth`& are joined today in joyous matrimony!!!");
									$session['user']['marriedto']=4294967295; //int max.
					  				$session['bufflist']['lover']=$buff;
								}else{
									output("Seth says, \"`^I'm sorry, apparently I've given you the wrong impression, I think we should just be friends.`0\"  Depressed, you have no more desire to fight in the forest today.");
									$session[user][turns]=0;
								}
						}
						if ($c > $session[user][charm]) output("`n`n`^You gain a charm point!");
						if ($c < $session[user][charm]) output("`n`n`\$You LOSE a charm point!");
						$session[user][charm]=$c;
					}
				}else{
				  	output("You think you had better not push your luck with `^Seth`0 today.");
				}
			}else{
			  //sorry, no lezbo action here.
			}
			break;
		case "converse":
		  output("You stroll over to a table, place your foot up on the bench and listen in on the conversation:`n");
			viewcommentary("inn","Add to the conversation?",20);
			break;
		case "bartender":
		  $alecost = $session[user][level]*10;
		  if ($HTTP_GET_VARS[act]==""){
				output("Cedrik looks at you sort-of sideways like.  He never was the sort who would trust a man any ");
				output("farther than he could throw them, which gave dwarves a decided advantage, except in provinces ");
				output("where dwarf tossing was made illegal.  Cedrik polishes a glass, holds it up to the light of the door as ");
				output("another patron opens it to stagger out in to the street.  He then makes a face, spits on the glass ");
				output("and goes back to polishing it.  \"`%What d'ya want?`0\" he asks gruffly.");
				addnav("Bribe","inn.php?op=bartender&act=bribe");
				addnav("Gems","inn.php?op=bartender&act=gems");
				addnav("Ale (`^$alecost`0 gold)","inn.php?op=bartender&act=ale");
			  $drunkenness = array(-1=>"stone cold sober",
														 0=>"quite sober",
														 1=>"barely buzzed",
									 					 2=>"pleasantly buzzed",
														 3=>"almost drunk",
														 4=>"barely drunk",
														 5=>"solidly drunk",
														 6=>"sloshed",
														 7=>"hammered",
														 8=>"really hammered",
														 9=>"almost unconscious"
									);
				$drunk = round($session[user][drunkenness]/10-.5,0);
				output("`n`n`7You now feel ".$drunkenness[$drunk]."`n`n");
			}else if ($HTTP_GET_VARS[act]=="gems"){
			  if ($HTTP_POST_VARS[gemcount]==""){
					output("\"`%You have gems, do ya?`0\" Cedrik asks.  \"`%Well, I'll make you a magic elixir for `^two gems`%!`0\"");
					output("`n`nGive him how many gems?");
					output("<form action='inn.php?op=bartender&act=gems' method='POST'><input name='gemcount' value='0'><input type='submit' class='button' value='Give'>`n",true);
					output("And what do you wish for?`n<input type='radio' name='wish' value='1' checked> Charm`n<input type='radio' name='wish' value='2'> Vitality`n",true);
					addnav("","inn.php?op=bartender&act=gems");
					output("<input type='radio' name='wish' value='3'> Health`n",true);
					output("<input type='radio' name='wish' value='4'> Forgetfulness`n",true);
					output("<input type='radio' name='wish' value='5'> Transmutation</form>",true);
				}else{
				  $gemcount = abs((int)$HTTP_POST_VARS[gemcount]);
					if ($gemcount>$session[user][gems]){
					  output("Cedrik stares at you blankly.  \"`%You don't have that many gems, `bgo get some more gems!`b`0\" he says.");
					}else{
					  output("`#You place $gemcount gems on the counter.");
						if ($gemcount % 2 == 0){
							
						}else{
							output("  Cedrik, knowing about your fundamental misunderstanding of ");
							output("math, hands one of them back to you.");
							$gemcount-=1;
						}
						if ($gemcount>0) output("  You drink the potion Cedrik hands you in exchange for your gems, and.....`n`n");
						$session[user][gems]-=$gemcount;
			  			debuglog("used $gemcount gems on potions");
						if ($gemcount>0){
							switch($HTTP_POST_VARS[wish]){
								case 1:
									$session[user][charm]+=($gemcount/2);
									output("`&You feel charming! `^(You gain charm points)");
									break;
								case 2:
									$session[user][maxhitpoints]+=($gemcount/2);
									$session[user][hitpoints]+=($gemcount/2);
									output("`&You feel vigorous! `^(You gain max hitpoints)");
									break;
								case 3:
									if ($session[user][hitpoints]<$session[user][maxhitpoints]) $session[user][hitpoints]=$session[user][maxhitpoints];
									$session[user][hitpoints]+=($gemcount*10);
									output("`&You feel healthy! `^(You gain temporary hitpoints)");
									break;
								case 4:
									$session[user][specialty]=0;
									output("`&You feel completely directionless in life.  You should rest and make some important decisions about your life! `^(Your specialty has been reset)");
									break;
								case 5:
									if ($session['user']['race']==1) $session['user']['attack']--;
									if ($session['user']['race']==2) $session['user']['defence']--;
									$session['user']['race']=0;
									output("`@You double over retching from the effects of transformation potion as your bones turn to gelatin!`n`^(Your race has been reset and you will be able to chose a new one tomorrow.)");
									if (isset($session['bufflist']['transmute'])) {
										$session['bufflist']['transmute']['rounds'] += 10;
									} else {
										$session['bufflist']['transmute']=array(
										    "name"=>"`6Transmutation Sickness",
										    "rounds"=>10,
										    "wearoff"=>"You stop puking your guts up.  Literally.",
										    "atkmod"=>0.75,
										    "defmod"=>0.75,
										    "roundmsg"=>"Bits of skin and bone reshape themselves like wax.",
										    "survivenewday"=>1,
										    "newdaymessage"=>"`6Due to the effects of the Transmutation Potion, you still feel `2ill`6.",
										    "activate"=>"offense,defense"
										);
									}
									break;
							}
						}else{
						  output("`n`nYou feel as though your gems would be better used elsewhere, not on some smelly potion.");
						}
					}
				}				
			}else if ($HTTP_GET_VARS[act]=="bribe"){
				$g1 = $session[user][level]*10;
				$g2 = $session[user][level]*50;
				$g3 = $session[user][level]*100;
				if ($HTTP_GET_VARS[type]==""){
					output("How much would you like to offer him?");
					addnav("1 gem","inn.php?op=bartender&act=bribe&type=gem&amt=1");
					addnav("2 gems","inn.php?op=bartender&act=bribe&type=gem&amt=2");
					addnav("3 gems","inn.php?op=bartender&act=bribe&type=gem&amt=3");
					addnav("$g1 gold","inn.php?op=bartender&act=bribe&type=gold&amt=$g1");
					addnav("$g2 gold","inn.php?op=bartender&act=bribe&type=gold&amt=$g2");
					addnav("$g3 gold","inn.php?op=bartender&act=bribe&type=gold&amt=$g3");
				}else{
				  if ($HTTP_GET_VARS[type]=="gem"){
						if ($session['user']['gems']<$_GET['amt']){
							$try=false;
							output("You don't have {$_GET['amt']} gems!");
						}else{
						  $chance = $HTTP_GET_VARS[amt]/4;
							$session[user][gems]-=$HTTP_GET_VARS[amt];
			  				debuglog("spent {$HTTP_GET_VARS['amt']} gems on bribing Cedrik");
							$try=true;
						}
					}else{
						if ($session['user']['gold']<$_GET['amt']){
							output("You don't have {$_GET['amt']} gold!");
							$try=false;
						}else{
							$try=true;
							$chance = $HTTP_GET_VARS[amt]/($session[user][level]*40);
							$session[user][gold]-=$HTTP_GET_VARS[amt];
				  			debuglog("spent {$HTTP_GET_VARS['amt']} gold bribing Cedrik");
						}
					}
					$chance*=100;
					if ($try){
						if (e_rand(0,100)<$chance){
							output("Cedrik leans over the counter toward you.  \"`%What can I do for you kid?`0\" he asks.");
								if (getsetting("pvp",1)) {
								addnav("Who's upstairs?","inn.php?op=bartender&act=listupstairs");
							}
							addnav("Tell me about colors","inn.php?op=bartender&act=colors");
							addnav("Switch specialty","inn.php?op=bartender&act=specialty");
						}else{
							output("Cedrik begins to wipe down the counter top, an act that really needed doing a long time ago.  ");
							output("When he finished, your ".($HTTP_GET_VARS[type]=="gem"?"gem".($HTTP_GET_VARS[amt]>0?"s are":" is"):"gold is"));
							output(" gone.  You inquire about the loss, and he stares blankly back at you.");
						}
					}else{
						output("`n`nCedrik stands there staring at you blankly.");
					}
				}
			}else if ($HTTP_GET_VARS[act]=="ale"){
			  output("Pounding your fist on the bar, you demand an ale");
				if ($session[user][drunkenness]>66){
				  //************************************************************************************************************************************
					output(", but Cedrik continues to clean the glass he was working on.  \"`%You've had enough ".($session[user][sex]?"lass":"lad").",`0\" he declares.");
				}else{
				  if ($session[user][gold]>=$alecost){
					  $session[user][drunkenness]+=33;
						$session[user][gold]-=$alecost;
				  		debuglog("spent $alecost gold on ale");
						output(".  Cedrik pulls out a glass, and pours a foamy ale from a tapped barrel behind him.  ");
						output("He slides it down the bar, and you catch it with your warrior-like reflexes.  ");
						output("`n`nTurning around, you take a big chug of the hearty draught, and give ".($session[user][sex]?"Seth":"Violet"));
						output(" an ale-foam mustache smile.`n`n");
						switch(e_rand(1,3)){
						  case 1:
							case 2:
							  output("`&You feel healthy!");
								$session[user][hitpoints]+=round($session[user][maxhitpoints]*.1,0);
								break;
							case 3:
							  output("`&You feel vigorous!");
								$session[user][turns]++;
						}
						$session[bufflist][101] = array("name"=>"`#Buzz","rounds"=>10,"wearoff"=>"Your buzz fades.","atkmod"=>1.25,"roundmsg"=>"You've got a nice buzz going.","activate"=>"offense");
					}else{
					  output("You don't have enough money.  How can you have any ale if you don't have any money!?!");
					}
				}
			}else if ($HTTP_GET_VARS[act]=="listupstairs"){
				addnav("Refresh the list","inn.php?op=bartender&act=listupstairs");
				output("Cedrik lays out a set of keys on the counter top, and tells you which key opens whose room.  The choice is yours, you may sneak in and attack any one of them.");
				$pvptime = getsetting("pvptimeout",600);
				$pvptimeout = date("Y-m-d H:i:s",strtotime("-$pvptime seconds"));
				pvpwarning();
				$days = getsetting("pvpimmunity", 5);
				$exp = getsetting("pvpminexp", 1500);
				$sql = "SELECT name,alive,location,sex,level,laston,loggedin,login,pvpflag FROM accounts WHERE 
				(locked=0) AND 
				(level >= ".($session[user][level]-1)." AND level <= ".($session[user][level]+2).") AND 
				(alive=1 AND location=1) AND 
				(age > $days OR dragonkills > 0 OR pk > 0 OR experience > $exp) AND
				(laston < '".date("Y-m-d H:i:s",strtotime("-".getsetting("LOGINTIMEOUT",900)." sec"))."' OR loggedin=0) AND
				(acctid <> ".$session[user][acctid].")
				ORDER BY level DESC";
				$result = db_query($sql) or die(db_error(LINK));
				output("<table border='0' cellpadding='3' cellspacing='0'><tr><td>Name</td><td>Level</td><td>Ops</td></tr>",true);
				for ($i=0;$i<db_num_rows($result);$i++){
					$row = db_fetch_assoc($result);
					$biolink = "bio.php?char=".rawurlencode($row[login])."&ret=".urlencode($_SERVER['REQUEST_URI']);
					addnav("", $biolink);
					if($row[pvpflag]>$pvptimeout){
						output("<tr class='".($i%2?"trlight":"trdark")."'><td>$row[name]</td><td>$row[level]</td><td>[ <a href='$biolink'>Bio</a> | `i(Attacked too recently)`i ]</td></tr>",true);
					}else{
						output("<tr class='".($i%2?"trlight":"trdark")."'><td>$row[name]</td><td>$row[level]</td><td>[ <a href='$biolink'>Bio</a> | <a href='pvp.php?act=attack&bg=1&name=".rawurlencode($row[login])."'>Attack</a> ]</td></tr>",true);
						addnav("","pvp.php?act=attack&bg=1&name=".rawurlencode($row[login]));
					}
				}
				output("</table>",true);
			}else if($HTTP_GET_VARS[act]=="colors"){
			  output("Cedrik leans on the bar.  \"`%So you want to know about colors, do you?`0\" he asks.");
				output("  You are about to answer when you realize the question was posed in the rhetoric.  ");
				output("Cedrik continues, \"`%To do colors, here's what you need to do.  First, you use a &#0096; mark ",true);
				output("(found right above the tab key) followed by 1, 2, 3, 4, 5, 6, 7, !, @, #, $, %, ^, &.  Each of those corresponds with ");
				output("a color to look like this: `n`1&#0096;1 `2&#0096;2 `3&#0096;3 `4&#0096;4 `5&#0096;5 `6&#0096;6 `7&#0096;7 ",true);
				output("`n`!&#0096;! `@&#0096;@ `#&#0096;# `\$&#0096;\$ `%&#0096;% `^&#0096;^ `&&#0096;& `n",true);
				output("`% got it?`0\"  You can practice below:");
				output("<form action=\"$REQUEST_URI\" method='POST'>",true);
				output("You entered ".str_replace("`","&#0096;",HTMLEntities($HTTP_POST_VARS[testtext]))."`n",true);
				output("It looks like ".$HTTP_POST_VARS[testtext]." `n");
				output("<input name='testtext' id='input'><input type='submit' class='button' value='Try'></form>",true);
				output("<script language='javascript'>document.getElementById('input').focus();</script>",true);

				output("`0`n`nThese colors can be used in your name, and in any conversations you have.");
				addnav("",$REQUEST_URI);
			}else if($HTTP_GET_VARS[act]=="specialty"){
				if ($HTTP_GET_VARS[specialty]==""){
					output("\"`2I want to change my specialty,`0\" you announce to Cedrik.`n`n");
					output("With out a word, Cedrik grabs you by the shirt, pulls you over the counter, and behind the ");
					output("barrels behind him.  There, he rotates the tap on a small keg labeled \"Fine Swill XXX\"");
					output("`n`nYou look around for the secret door that you know must be opening nearby when Cedrik ");
					output("rotates the tap back, and lifts up a freshly filled foamy mug of what is apparently his fine swill, blue-green ");
					output("tint and all.");
					output("`n`n\"`3What?  Were you expecting a secret room?`0\" he asks.  \"`3Now then, you must be more ");
					output("careful about how loudly you say that you want to change your specialty, not everyone looks ");
					output("favorably on that sort of thing.`n`n`0\"`3What new specialty did you have in mind?`0\"");
					addnav("Dark Arts",preg_replace("/[&?]c=[[:digit:]-]*/","",$REQUEST_URI)."&specialty=1");
					addnav("Mystical Powers",preg_replace("/[&?]c=[[:digit:]-]*/","",$REQUEST_URI)."&specialty=2");
					addnav("Thieving Skills",preg_replace("/[&?]c=[[:digit:]-]*/","",$REQUEST_URI)."&specialty=3");
				
				}else{
					output("\"`3Ok then,`0\" Cedrik says, You're all set.`n`n\"`2That's it?`0\" you ask him.");
					output("`n`n\"`3Yep.  What'd you expect, some sort of fancy arcane ritual???`0\"  Cedrik ");
					output("begins laughing loudly.  \"`3You're all right, kid... just don't ever play poker, eh?`0");
					output("`n`n\"`3Oh, one more thing.  Your old use points and skill level still apply to that skill, ");
					output("you'll have to build up some points in this one to be very good at it.`0\"");
					//addnav("Return to the inn","inn.php");
					$session[user][specialty]=$HTTP_GET_VARS[specialty];
				}
			}
			break;
		case "room":
			$config = unserialize($session['user']['donationconfig']);
		  $expense = round(($session[user][level]*(10+log($session[user][level]))),0);
			if ($HTTP_GET_VARS[pay]){
				if ($_GET['coupon']==1){
			  	$config['innstays']--;
					$session['user']['donationconfig']=serialize($config);
					$session['user']['loggedin']=0;
					$session['user']['location']=1;
					$session['user']['boughtroomtoday']=1;
					saveuser();
					$session=array();
					redirect("index.php");
				}else{
					if ($HTTP_GET_VARS[pay] == 2 || $session[user][gold]>=$expense || $session[user][boughtroomtoday]){
						if ($session[user][loggedin]){
							if ($session[user][boughtroomtoday]) {
							}else{
								if ($HTTP_GET_VARS[pay] == 2) {
									$fee = getsetting("innfee", "5%");
									if (strpos($fee, "%"))
										$expense += round($expense * $fee / 100,0);
									else
										$expense += $fee;
									$goldline = ",goldinbank=goldinbank-$expense";
								} else {
									$goldline = ",gold=gold-$expense";
								}
								$goldline .= ",boughtroomtoday=1";
							}
				  			debuglog("spent $expense gold on an inn room");
							$sql = "UPDATE accounts SET loggedin=0,location=1 $goldline WHERE acctid = ".$session[user][acctid];
							db_query($sql) or die(sql_error($sql));
						}
						$session=array();
						redirect("index.php");
					}else{
						output("\"Aah, so that's how it is,\" Cedrik says as he puts the key he had retrieved back on to its hook ");
						output("behind his counter.  Perhaps you'd like to get sufficient funds before you attempt to engage in ");
						output("local commerce.");
					}
				}
			}else{
				if ($session[user][boughtroomtoday]){
					output("You already paid for a room for the day.");
					addnav("Go to room","inn.php?op=room&pay=1");
				}else{
					if ($config['innstays']>0){
						addnav("Show him your coupons for ".$config['innstays']." inn stays","inn.php?op=room&pay=1&coupon=1");
					}
					output("You stroll over to the bartender and request a room.  He eyes you up and says that it will cost `\$".$expense."`0 gold"
								." for the night.");
					$fee = getsetting("innfee", "5%");
					if (strpos($fee, "%"))
						$bankexpense = $expense + round($expense * $fee / 100,0);
					else
						$bankexpense = $expense + $fee;
					if ($session['user']['goldinbank'] >= $bankexpense && $bankexpense != $expense) {
						output("Since you are such a fine person, he also offers you a rate of `\$".$bankexpense."`0 gold if you pay direct from the bank which includes a " . (strpos($fee, "%") ? $fee : "$fee gold") . " transaction fee.");
					}
								
					output("`n`nYou debate the issue, not wanting to part with your money when the fields offer a place to sleep, "
								."however, you realize that the inn is a considerably safer place to sleep, it is far harder for vagabonds to get you "
								."in your room while you sleep.");
					addnav("Give him $expense gold","inn.php?op=room&pay=1");
					if ($session['user']['goldinbank'] >= $bankexpense) {
						addnav("Pay $bankexpense gold from bank","inn.php?op=room&pay=2");
					}
				}
			}
			break;
	}
  addnav("Return to the inn","inn.php");
}

output("</span>",true);

page_footer();
?>
