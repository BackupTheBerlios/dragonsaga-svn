<?php
if (!isset($session)) exit();
if ($session[user][charm]>0){
	output("`^An old man whacks you with an ugly stick, giggles and runs away!`n`nYou `%lose one`^ charm!`0");
	$session[user][charm]--;
}else{
  output("`^An old man hits you with an ugly stick, and gasps as his stick `%loses one`^ charm!  You're even uglier than his ugly stick!`0");
}
?>
