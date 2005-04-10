<?php
// This program is free software; you can redistribute it and/or modify it
// under the terms of the GNU General Public License as published by the
// Free Software Foundation; either version 2 of the License, or (at your
// option) any later version.
//
// File: riddles.php

/* *******************
The riddling gnome was written by Joe Naylor
Feel free to use this any way you want to, but please give credit where due.
Version 1.1
******************* */

if (!isset($session)) exit();

//** Used to remove extra words from the beginning and end of a string
// Note that string will be converted to lowercase
function filterwords($string)
{
    $string = strtolower($string);

    //Words to remove
    $filterpre = array (
        "a",
        "an",
        "and",
        "the",
        "my",
        "your",
        "someones",
        "someone's",
        "someone",
        "his",
        "her",
        "s");
    //Letters to take off the end
    $filterpost = array (
        "s",
        "ing",
        "ed");

		//split in to array of words
    $filtstr = explode(" ", trim($string));
    foreach ($filtstr as $key => $filtstr1)
        $filtstr[$key] = trim($filtstr1);

		//pop off word if found in $filterpre
    foreach ($filtstr as $key => $filtstr1)
        foreach ($filterpre as $filterpre1) 
            if (!strcasecmp($filtstr1, $filterpre1))
                $filtstr[$key] = "";
    
		//trim off common word endings
    foreach ($filtstr as $key => $filtstr1)
        foreach ($filterpost as $filterpost1) 
            if (strlen($filtstr) > strlen($filterpost1)) 
                if (!strcasecmp(substr($filtstr1, -1*strlen($filterpost1)), $filterpost1)) 
                    $filtstr[$key] = substr($filtstr1, 0, strlen($filterstr)-strlen($filterpost1));

		//rebuild filtered input
    $string = implode("", $filtstr);

    return $string;
}

if ($_GET['op']==""){
    output("`6`nA short little gnome with leaves in his hair squats beside a small tree.  He smirks, giggling behind one of his fatty hands.");
    output("For a moment, it looks like he might scramble off into the trees, but after a moment smirks and looks at you.`n`n");
    output("`6\"`@I'll give you a boon,`6\" he says, \"`@if you can think and answer my riddle!`6\"`n`n");
    output("`6He loses himself momentarily in a fit of giggling, then contains his excitement for a moment and continues.`n`n");

    output("`6\"`@But if ere long your guess is wrong, then my boon it will be!`6\"`n");
    output("`6`nDo you accept his challenge?`n`n");
    output("<a href=forest.php?op=yes>Yes</a>`n", true);
    output("<a href=forest.php?op=no>No</a>`n", true);

    addnav("Yes","forest.php?op=yes");
    addnav("No","forest.php?op=no");
    addnav("","forest.php?op=yes");
    addnav("","forest.php?op=no");
    if ($session[user][specialinc]!="riddles.php"){
        $session[user][specialmisc]=NULL;
    }
    $session[user][specialinc]="riddles.php";

}else if($_GET['op']=="yes"){
    //if ($_POST[guess]==NULL){
		if ($_GET['subop']!="answer"){
        $session[user][specialinc]="riddles.php";
        $rid = $session[user][specialmisc];
        if (!strpos($rid, "Riddle")) {
            $sq1 = "SELECT * FROM riddles ORDER BY rand(".e_rand().")";
        }else{
            $rid = substr($rid, -1*(strlen($rid)-6));    // 6 letters in "Riddle"
            $sq1 = "SELECT * FROM riddles WHERE id=$rid";
        }        
        $result = db_query($sq1) or die(db_error(LINK));
        $riddle = db_fetch_assoc($result);
        $session[user][specialmisc]="Riddle" . $riddle[id];
        output("`6Giggling with glee, he asks his riddle:`n`n");
        output("`6\"`@$riddle[riddle]`6\"`n`n");
        output("`6What is your guess?");
        output("<form action='forest.php?op=yes&subop=answer' method='POST'><input name='guess'><input type='submit' class='button' value='Guess'></form>",true);
        addnav("","forest.php?op=yes&subop=answer");
    }else{
        $rid = substr($session[user][specialmisc], 6);
        $sq1 = "SELECT * FROM riddles WHERE id=$rid";
        $result = db_query($sq1) or die(db_error(LINK));
        $riddle = db_fetch_assoc($result);


        //*** Get and filter correct answer
        $answer = explode(";", $riddle[answer]); //there can be more than one answer in the database, seperated by semicolons (;)
        foreach($answer as $key => $answer1) {
					//changed "" to " " below, I believe this is the correct implementation.
            $answer[$key] = preg_replace("/[^[:alpha:]]/"," ",$answer1); 
            $answer[$key] = filterwords($answer1);
            }
        
        //*** Get and filter players guess
        $guess = $_POST[guess];
        // $guessdebug = $guess; // This is only for debugging, see below when the answer is wrong.
        $guess = preg_replace("/[^[:alpha:]]/"," ",$guess); 
        $guess = filterwords($guess);

        $correct = 0;
				//changed to 2 on the levenshtein just for compassion's sake :-)  --MightyE
        foreach($answer as $answer1)
            if (levenshtein($guess,$answer1) <= 2) //Allow one letter to be off to compensate for silly spelling mistakes
                $correct = 1;

        if ($correct) {
            output("`n`6\"`@Lizards and polywogs!!`6\" he blusters, \"`@You got it!`6\"`n");
            output("`6\"`@Oh very well.  Here's your stupid prize.`6\"`n`n");

            // It would be nice to have some more consequences
            $rand = e_rand(1, 10);
            switch ($rand){
                case 1:
                case 2:
                case 3:
                case 4:
                    output("`^He gives you a gem!");
                    $session[user][gems]++;
					debuglog("gained 1 gem from the riddle master");
                    break;
                case 5:
                case 6:
                case 7:
                    output("`^He gives you two gems!");
                    $session[user][gems]+=2;
					debuglog("gained 2 gems from the riddle master");
                    break;
                case 8:
                case 9:
                    output("He does the hokey pokey, and turns himself around.  After that display, you feel ready for battle!");
                    output("`n`n`^You gain a forest fight!");
                    $session[user][turns]++;
                    break;
                case 10:
                    output("He looks deep in your eyes, then whacks you hard across the side of your head.  ");
                    if ($session[user][specialty]) {
                        output("When you come to, you feel a little bit smarter.`n`#");
                        increment_specialty();
                    }else{
                        output("That was a fun lesson.");
                        output("`n`n`^You gain some experience!");
                        $session[user][experience] += $session[user][level] * 10;
                    }
                    break;
            }

        }else{
            /* ************
            This saves the wrong answers in a database table, so I can review them
                from time to time and debug my answer interpretation code.  You
                don't need to run this unless you're doing something like that. */
            // $answer1 = implode (" ", $answer);
            // $sq1 = "INSERT INTO riddledebug (id,answer,guess,date,player) VALUES ($rid,'$riddle[answer]','$guessdebug',NOW(),{$session[user][acctid]})";
            // $result = db_query($sq1);
            /***************/ 

            output("`n`6The strange gnome cackles with glee and dances around you.  You feel very silly standing there with a crazy gnome prancing around like a fairy,");
            output("`6so you quietly make your exit while he's distracted.  Somehow you feel like less of a hero with his mocking laughter echoing in your ears, ");

            // It would be nice to have some more consequences
            $rand = e_rand(1, 6);
            switch ($rand){
                case 1:
                case 2:
                case 3:
                    output("it's not until much later that you also notice some of your gold is missing.");
                    output("`n`n`^You lost some gold!");
					$gold = e_rand(1, $session[user][level]*10);
					if ($gold > $session['user']['gold'])
						$gold = $session['user']['gold'];
                    $session[user][gold] -= $gold;
					debuglog("lost $gold gold to the riddlemaster");
                case 4:
                case 5:
                    output("you don't think you can face another opponent right away.");
                    output("`n`n`^You lose a forest fight!");
                    if ($session[user][turns]>0) $session[user][turns]--;
                    break;
                case 6:
                    output("what would ".($session[user][sex]?"Seth":"Violet")." think?");
                    output("`n`n`^You lose a charm point!");
                    if ($session[user][charm]>0) $session[user][charm]--;
                    break;
                }    
        }

        $session[user][specialinc]="";
        $session[user][specialmisc]="";
    }
}else if($_GET['op']=="no"){
    output("`n`6Afraid to look the fool, you decline his challenge.  He was a little bit creepy anyway.");
    output("`n`6The strange gnome giggles histerically as he disappears into the forest.");
    $session[user][specialinc]="";
    $session[user][specialmisc]="";
}
?>
