<?
if (!isset($session)) exit();
output("`^An old man whacks you with a pretty stick, giggles and runs away!`n`nYou `%receive one`^ charm!`0");
$session[user][charm]++;
?>
