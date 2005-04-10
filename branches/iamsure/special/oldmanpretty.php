<?php
// This program is free software; you can redistribute it and/or modify it
// under the terms of the GNU General Public License as published by the
// Free Software Foundation; either version 2 of the License, or (at your
// option) any later version.
//
// File: oldmanpretty.php

if (!isset($session)) exit();
output("`^An old man whacks you with a pretty stick, giggles and runs away!`n`nYou `%receive one`^ charm!`0");
$session[user][charm]++;
?>
