<?php
// This program is free software; you can redistribute it and/or modify it
// under the terms of the GNU General Public License as published by the
// Free Software Foundation; either version 2 of the License, or (at your
// option) any later version.
//
// File: findgold.php

$gold = e_rand($session[user][level]*10,$session[user][level]*50);
output("`^Fortune smiles on you and you find $gold gold!`0");
//addnav("Return to the forest","forest.php");
$session[user][gold]+=$gold;
debuglog("found $gold gold in the forest");
?>
