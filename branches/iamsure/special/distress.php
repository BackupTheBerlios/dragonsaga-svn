<?php
/* *******************
The damsel in distress was written by Joe Naylor
Much of the event text was written by Matt Clift, I understand it is
heavily inspired by an event in Legend of the Red Dragon.
Feel free to use this any way you want to, but please give credit where due.
Version 1.1
******************* */

if (!isset($session)) exit();

if ($HTTP_GET_VARS[op]==""){
    $session[user][specialinc]="distress.php";
    output("`n`3While searching through the forest, you find a man lying face down in the dirt. ");
    output("The Arrow in his back, the pool of blood, and the lack of movement are a good ");
    output("indication this man is dead.`n`n");

    output("`3While searching his body, you find a a crumpled piece of paper, tightly clenched within his fist. ");
    output("You carefully free the paper and realize it is a note, hastily written by the look of it.  ");
    output("The note reads:`n`n");
    
    output("`3\"`7Help! I have been imprisoned by my horrid old Uncle. He plans to force me to marry.  ");
    output("Please save me! I am being held at ...`3\"`n`n");
    
    output("`3The rest of the note is either too bloody or too badly damaged to read.`n`n");
    
    output("`3Outraged you cry \"`7I must save ".($session[user][sex]?"him":"her")."!`3\" But where will you go?`n`n");
    
    output("<a href=forest.php?op=1>Go to Wyvern Keep</a>`n", true);
    output("<a href=forest.php?op=2>Go to Castle Slaag</a>`n", true);
    output("<a href=forest.php?op=3>Go to Draco's Dungeon</a>`n", true);
    output("`n<a href=forest.php?op=no>It's not worth my time</a>", true);
    addnav("Go to");
    addnav("Wyvern Keep","forest.php?op=1");
    addnav("Castle Slaag","forest.php?op=2");
    addnav("Draco's Dungeon","forest.php?op=3");
    addnav("Ignore it","forest.php?op=no"); // Changed because too many players kept hitting 'f' by mistake due to most things in forest being a 'F'ight.
    addnav("","forest.php?op=1");
    addnav("","forest.php?op=2");
    addnav("","forest.php?op=3");
    addnav("","forest.php?op=no");

}else if ($HTTP_GET_VARS[op]=="no"){
    output("`3You crumple the note up and toss it into the trees.  You're not afraid, she's just not worth your time. ");
    output("Nope, not afraid at all, no way.  You turn your back on the poor distressed ".($session[user][sex]?"man":"maiden")."'s pitiful cry for ");
    output("help, and set off through the trees to find something a little less danger- ...er, a little more challenging.");
    //addnav("Return to the forest","forest.php");
    $session[user][specialinc]="";
}else{
    switch($HTTP_GET_VARS[op]) {
        case 1: $loc = "`#Wyvern Keep";
            break;
        case 2: $loc = "`#Castle Slaag";
            break;
        case 3: $loc = "`#Draco's Dungeon";
            break;
    }

    output("`n`3You storm through the doors of $loc `3slaying the guards and anybody else who gets in your way. ");

    switch (e_rand(1, 10)) {
        case 1:
        case 2:
        case 3:
        case 4:
            output("`3Finally you open what looks like a likely door and spy a well furnished chamber. `n`n");
            output("The chamber holds a young, ".($session[user][sex]?"handsome":"beautiful").", and grateful occupant.`n`n");
            output("\"`#Oh, you came!`3\" ".($session[user][sex]?"he says. \"`#Heroine,":"she beams. \"`#Hero,")." how can I ever thank you?`3\"`n`n");
            output("After a few hours in each others arms, you leave the ".($session[user][sex]?"princes":"princess'")." side ");
            output("and go on your way, but not without a small token of ".($session[user][sex]?"his":"her")." appreciation.`n`n");
            switch (e_rand(1, 5)) {
                case 1:
                    output("".($session[user][sex]?"He":"She")." gives you a small leather bag.`n`n");
                    $reward = e_rand(1, 2);
                    output("`^You gain $reward ".($reward?"gem":"gems")."!");
                    $session[user][gems] += $reward;
					debuglog("gained $reward gems rescuing a damsel in distress");
                    break;
                case 2:
                    output("".($session[user][sex]?"He":"She")." gives you a small leather bag.`n`n");
                    $reward = e_rand(1, $session[user][level]*30);
                    output("`^You gain $reward gold!");
                    $session[user][gold] += $reward;
					debuglog("gained $reward gold rescuing a damsel in distress");
                    break;
                case 3:
                    output("".($session[user][sex]?"He":"She")." showed you things you never dreamed of.`n`n");
                    output("`^You gain experience!");
                    $session[user][experience] *= 1.1;
                    break;
                case 4:
                    output("".($session[user][sex]?"He":"She")." taught you how to be a real ".($session[user][sex]?"woman":"man").".`n`n");
                    output("`^You gain two charm points!");
                    $session[user][charm] += 2;
                    break;
                case 5:
                    output("".($session[user][sex]?"He":"She")." gives you a carriage ride back to the forest, alone...`n`n");
                    output("`^You gain a forest fight, and are completely healed!");
                    $session[user][turns] ++;
                    if ($session['user']['hitpoints']<$session['user']['maxhitpoints']) $session[user][hitpoints] = $session[user][maxhitpoints];
                    break;
                }
            break;
        case 5:
            output("`3Finally you open what looks like a likely door and spy a well furnished chamber. `n`n");
            output("The chamber holds a large chest, muffled cries for help come from inside.  You throw the chest open ");
            output("and strike a heroic pose, then you see the chests occupant.  Out from the chest leaps a monstrous, ");
            output("and lonely, ".($session[user][sex]?"troll":"trolless")."!!  After a few hours of... excitement,");
            output(" ".($session[user][sex]?"he":"she")." lets you go on your way.  You feel more than a little dirty.`n`n");
						if ($session[user][race] == 1) {
							output("You'd almost forgotten how potent your race was!`n");
							output("`%You gain 1 forest fights!`n");
							output("`%You gain a charm point!`n");
							$session[user][turns]+=1;
							$session[user][charm]++;
						} else {
							output("`%You lose a forest fight!`n");
							output("`%You lose a charm point!`n");
							if ($session[user][turns] > 0) $session[user][turns]--;
							if ($session[user][charm] > 0) $session[user][charm]--;
						}
            break;
        case 6:
            output("`3Finally you open what looks like a likely door and spy a well furnished chamber. `n`n");
            output("The chamber holds a wrinkled old ".($session[user][sex]?"man":"hag")."!  You gasp in horror ");
            output("at the hideous thing before you, and run screaming from the room.  Somehow, you think something ");
            output("rubbed off on you.`n`n"); 
            output("`%You lose a charm point!`n");
            if ($session[user][charm] > 0) $session[user][charm]--;
            break;
        case 7:
            output("`3Finally you open what looks like a likely door and spy a well furnished chamber. `n`n");
            output("You dash into the room, and sitting at the window is a a rediculous-looking effeminate fop.  ");
            output("\"`5Oh, you came!`3\" he cries, jumping to his feet.  As he starts toward you, he trips over his ");
            output("bedpan and gets tangled in his clothes.  You take this opportunity to slip away as quickly and ");
            output("quietly as possible.  Luckily, nothing was injured but your pride.`n`n");
            break;
        case 8:
            output("`3A fierce fight ensues, and you put forth a valiant effort!  Unfortunately you are hopelessly ");
            output("outnumbered, you try to run, but soon fall beneath the blades of your enemies. `n`n");
            output("`%You have died!`n`n");
            output("`3The life lesson learned here balances any experience you would have lost.`n");
            output("You may continue playing again tomorrow.");
            $session[user][alive]=false;
            $session[user][hitpoints]=0;
            addnav("Daily News","news.php");
            addnews("`%".$session[user][name]."`3 was slain attempting to rescue a ".($session[user][sex]?"prince":"princess")." from $loc.");
            break;
        case 9:
            output("`3A fierce fight ensues, and you put forth a valiant effort!  Unfortunately you are hopelessly ");
            output("outnumbered, finally you see your chance and break free.  The last thing the denizens of $loc `3");
            output("see is your backside, fleeing into the forest.`n`n");
            output("`%You lose a forest fight!`n");
            output("`%You lose most of your hitpoints!`n");
            if ($session[user][turns]>0) $session[user][turns]--;
            if ($session[user][hitpoints]>($session[user][hitpoints]*.1)) $session[user][hitpoints]=round($session[user][hitpoints]*.1,0);
            break;
        case 10:
            output("`3Finally you open what looks like a likely door and spy a well furnished chamber. `n`n");
            output("You dash inside, and find a surprised nobleman and his wife, just sitting down to dinner.`n`n");
            output("\"`^What is the meaning of this?`3\" he demands.  You try to explain how you ended up in the ");
            output("wrong place, but he doesn't seem to pay any attention.  The authorities are called, and you ");
            output("are fined for damages.`n`n");
            output("`%You lose all the gold you were carrying!`n");
			debuglog("lost {$session['user']['gold']} gold dying while rescuing a damsel in distress");
            $session[user][gold]=0;
            break;
    }

    $session[user][specialinc]="";
}

?>
