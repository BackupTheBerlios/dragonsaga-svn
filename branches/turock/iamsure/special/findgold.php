<?
$gold = e_rand($session[user][level]*10,$session[user][level]*50);
output("`^Fortune smiles on you and you find $gold gold!`0");
//addnav("Return to the forest","forest.php");
$session[user][gold]+=$gold;
debuglog("found $gold gold in the forest");
?>
