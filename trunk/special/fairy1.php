<?
if (!isset($session)) exit();
if ($HTTP_GET_VARS[op]==""){
	output("`%You encounter a fairy in the forest.  \"`^Give me a gem!`%\" she demands.  What do you do?");
	addnav("Give her a gem","forest.php?op=give");
	addnav("Don't give her a gem","forest.php?op=dont");
	$session[user][specialinc]="fairy1.php";
}else if ($HTTP_GET_VARS[op]=="give"){
  if ($session[user][gems]>0){
	  output("`%You give the fairy one of your hard-earned gems.  She looks at it, squeals with delight, ");
		output("and promises a gift in return.  She hovers over your head, sprinkles golden fairy dust down ");
		output("on you before flitting away.  You discover that ...`n`n`^");
		$session[user][gems]--;
		debuglog("gave 1 gem to a fairy");
		switch(e_rand(1,7)){
		  case 1:
			  output("You receive an extra forest fight!");
				$session[user][turns]++;
				break;
			case 2:
			case 3:
				output("You feel perceptive and notice `%TWO`^ gems nearby!");
				$session[user][gems]+=2;
				debuglog("found 2 gem from a fairy");
				break;
			case 4:
			case 5:
				output("Your maximum hitpoints are `bpermanently`b increased by 1!");
				$session[user][maxhitpoints]++;
				$session[user][hitpoints]++;
				break;
			case 6:
			case 7:
				increment_specialty();
				break;
		}
	}else{
	  output("`%You promise to give the fairy a gem, however, when you open your purse, you discover that ");
		output("you have none.  The tiny fairy floats before you, tapping her foot on the air as you try ");
		output("to explain why it is that you lied to her.");
		output("`n`nHaving had enough of your mumblings, she sprinkles some angry red fairy dust on you.  ");
		output("Your vision blacks out, and when you wake again, you cannot tell where you are.  You spend ");
		output("enough time searching for the way back to the village that you lose time for a forest fight.");
		$session[user][turns]--;
	}
}else{
  output("`%Not wanting to part with one of your precious precious gems, you swat the tiny creature to the ");
	output("ground and walk away.");
}


?>
