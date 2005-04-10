<?php
// This program is free software; you can redistribute it and/or modify it
// under the terms of the GNU General Public License as published by the
// Free Software Foundation; either version 2 of the License, or (at your
// option) any later version.
//
// File: rock.php

require_once("common.php");
// This idea is Imusade's from lotgd.net
if ($session['user']['dragonkills']>0 || $session['user']['superuser']>1){
	addcommentary();
}

checkday();
if ($session['user']['dragonkills']>0 || $session['user']['superuser']>1){
	page_header("The Veteran's Club");
	
	output("`b`c`2The Veteran's Club`0`c`b");
	
	output("`n`n`4Something in you compels you to examine the curious rock.  Some dark magic, locked up in age old horrors.");
	output("`n`nWhen you arrive at the rock, an old scar on your arm begins to throb in succession with a mysterious light that ");
	output("now seems to come from the rock.  As you stare at it, the rock shimmers, shaking off an illusion.  You realize that this is ");
	output("more than a rock.  It is, in fact, a doorway, and over the threshold, you see others, bearing an identical scar to yours.  It ");
	output("somehow reminds you of the head of one of the great serpents from legend.  You have discovered The Veteran's Club.");
	output("`n`n");
	viewcommentary("veterans","Boast here",30,"boasts");
}else{
	page_header("Curious looking rock");
	output("You approach the curious looking rock.  After staring, and looking at it for a little while, it continues to look just like a curious looking rock.`n`n");
	output("Bored, you decide to return to the village.");
}
addnav("Return to the village","village.php");

page_footer();
?>
