<?
if (!isset($session)) exit();
if ($HTTP_GET_VARS[op]==""){
	output("`5You stumble across a clearing that is oddly quiet.  To one side are three baskets, tightly lidded.  Finding this 
	curious, you cautiously approach them when you hear the faint mew of a kitten.  You reach for the lid of the first basket
	when out of no where, Crazy Audrey appears, ranting feverishly about colored kittens, and pulls the baskets to her.  Taken 
	somewhat aback, you decide you had best question her about these kittens.`n`n
	\"`#Tell me, good woman,`5\" you begin...`n`n
	\"`%GOOD GOOD good good goodgoodgoodgoodgood...`5\" Audrey begins to repeat.  Unflustered, you persist.`n`n
	\"`#What are these kittens you speak of?`5\"`n`n
	Amazingly, Crazy Audrey suddenly grows quiet and begins to speak in a regal accent both melodious and soft.`n`n
	\"`%Of these baskets, have I three,`n
	Four kittens inside each there do be.`n`n
	Minds of their own, do they have,`n
	Should two alike emerge, you'll get this salve.`n`n
	Energy it gives, to fight your foes,`n
	Merely rub it 'tween your toes.`n`n
	Should no two alike show their head,`n
	Earlier today, you'll see your bed.`n`n
	That then is my proposition,`n
	Shall thou take it, or from me run?`5\"`n`n
	Will you play her game?");
	addnav("Play","forest.php?op=play");
	addnav("Run away from Crazy Audrey","forest.php?op=run");
	$session['user']['specialinc']="audrey.php";
}else if($HTTP_GET_VARS[op]=="run"){
	$session['user']['specialinc']="";
	output("`5You run, very quickly, away from this mad woman.");
}else if($HTTP_GET_VARS[op]=="play"){
	//$session['user']['specialinc']="audrey.php";
	$kittens = array("`^C`&a`6l`7i`^c`7o","`7T`&i`7g`&e`7r","`6Orange","`&White","`^`bHedgehog`b");
	$c1 = e_rand(0,3);
	$c2 = e_rand(0,3);
	$c3 = e_rand(0,3);
	if (e_rand(1,20)==1) {
		$c1=4; $c2=4; $c3=4;
	}
	output("`5Agreeing to Crazy Audrey's preposterous game, she thumps the first basket on the lid.  A {$kittens[$c1]}`5 kitten peeks its head out.`n`n");
	output("Crazy Audrey then thumps the second basket on the lid, and a {$kittens[$c2]}`5 kitten peeks its head out.`n`n");
	output("She thumps the third basket on the lid, and a {$kittens[$c3]}`5 kitten hops out and bounds up to Crazy Audrey's shoulder.`n`n");
	if ($c1==$c2 && $c2==$c3){
		if ($c1==4){
			output("\"`%Hedgehogs?  HEDGEHOGS??  Hahahahaha, HEDGEHOGS!!!!`5\" shouts Crazy Audrey as she snatches them up and runs screaming in to the
			forest.  You notice that she has dropped a full BAG of those wonderful salves.`n`n`^You gain FIVE forest fights!");
			$session['user']['turns']+=5;
		}else{
			output("\"`%Argh, you are ALL very bad kittens!`5\" shouts Crazy Audrey before hugging her shoulder kitten and putting it back in the
			basket.  \"`%Because my kittens all were alike, I grant you TWO salves.`5\"`n`nYou rub the salves on your toes.`n`n`^You gain TWO forest fights!");
			$session['user']['turns']+=2;
		}
	}elseif ($c1==$c2 || $c2==$c3 || $c1==$c3){
		output("\"`%Garr, you crazy kittens, what do you know?  Why I ought to paint you all different colors!`5\"  Despite her threatening
		words, Crazy Audrey pets the kitten on her shoulder before placing it back in the basket, and giving you your salve, which you rub all
		over your toes.`n`n`^You gain a forest fight!");
		$session['user']['turns']++;
	}else{
		output("\"`%Well done my pretties!`5\" shouts Crazy Audrey.  Just then her shoulder-mounted kitten leaps at you.  In fending it off,
		you lose some energy.  Finally it hops back in its basket and all is quiet.  Crazy Audrey cackles quietly and looks at you.");
		output("`n`n`^You lose a forest fight!");
		$session['user']['turns']--;
	}
	//addnav("Play Again","forest.php?op=play");
	//addnav("Run away from Crazy Audrey","forest.php?op=run");
}
?>