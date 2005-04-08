<?php
require_once "common.php";
addcommentary();
$cost = $session['user']['level']*20;
if ($_GET[op]=="pay"){
	if ($session['user']['gold']>=$cost){
		$session['user']['gold']-=$cost;
		debuglog("spent $cost gold to speak to the dead");
		redirect("gypsy.php?op=talk");
	}else{
		page_header("Gypsy Seer's tent");
		addnav("Return to the village","village.php");
		output("`5You offer the old gypsy woman your `^{$session[user][gold]}`5 gold for your gen-u-wine say-ance, however she informs you that the dead 
		may be dead, but they ain't cheap.");
	}
}elseif ($_GET['op']=="talk"){
	page_header("In a deep trance, you talk with the shades");
	output("`5While in a deep trance, you are able to talk with the dead:`n");
	viewcommentary("shade","Project",25,"projects");
	addnav("Snap out of your trance","village.php");
}else{
	checkday();
	page_header("Gypsy Seer's tent");
	output("`5You duck in to a gypsy tent behind `%Pegasus'`5 wagon which promises to let you talk with the deceased.  In typical gypsy style, the old woman sitting behind
	a somewhat smudgy crystal ball informs you that the dead only speak with the paying.  Your price is `^$cost`5 gold.");
	addnav("Pay to talk to the dead","gypsy.php?op=pay");
	addnav("Forget it","village.php");
	if ($session['user']['superuser']>1) addnav("Superuser Entry","gypsy.php?op=talk");
}
page_footer();
?>
