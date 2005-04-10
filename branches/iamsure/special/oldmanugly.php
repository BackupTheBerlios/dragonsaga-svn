<?php
// This program is free software; you can redistribute it and/or modify it
// under the terms of the GNU General Public License as published by the
// Free Software Foundation; either version 2 of the License, or (at your
// option) any later version.
//
// File: oldmanugly.php

if (!isset($session)) exit();
if ($session[user][charm]>0){
	output("`^An old man whacks you with an ugly stick, giggles and runs away!`n`nYou `%lose one`^ charm!`0");
	$session[user][charm]--;
}else{
  output("`^An old man hits you with an ugly stick, and gasps as his stick `%loses one`^ charm!  You're even uglier than his ugly stick!`0");
}
?>
