<?
if (!isset($session)) exit();
if ($HTTP_GET_VARS[op]==""){
  output("`@You encounter a strange old man!`n`n\"`#I am lost,`@\" he says, \"`#can you lead me back to town?`@\"`n`n");
	output("You know that if you do, you will lose time for a forest fight for today, will you help out this poor old man?");
	addnav("Walk him to town","forest.php?op=walk");
	addnav("Leave him here","forest.php?op=return");
	$session[user][specialinc] = "oldmantown.php";
}else if ($HTTP_GET_VARS[op]=="walk"){
  $session[user][turns]--;
	if (e_rand(0,1)==0){
	  output("`@You take the time to lead the old man back to town.`n`n In exchange he whacks you with his pretty stick, and you receive `%one charm point`@!");
		$session[user][charm]++;
	}else{
    output("`@You take the time to lead the old man back to town.`n`n In exchange he gives you `%a gem`@!");
		$session[user][gems]++;
		debuglog("got 1 gem for walking old man to village");
	}
	//addnav("Return to the forest","forest.php");
	$session[user][specialinc]="";
}else if ($HTTP_GET_VARS[op]=="return"){
  output("`@You tell the old man that you are far too busy to aid him.`n`nNot a big deal, he should be able to find his way back ");
	output("to town on his own, he made his way out here, didn't he?  A wolf bays in the distance to your left, and a few seconds later ");
	output("one bays somewhere closer to your right.  Yep, he should be fine.");
	//addnav("Return to the forest","forest.php");
	$session[user][specialinc]="";
}
?>
