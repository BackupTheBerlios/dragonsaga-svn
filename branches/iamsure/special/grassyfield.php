<?php
//Original idea by Sean McKillion
//modifications by Eric Stevens
//further modifications by JT Traub
if ($_GET['op']=="return") {
    $session['user']['specialmisc']="";
    $session['user']['specialinc']="";
    redirect("forest.php");
}

checkday();

output("`n`c`#You Stumble Upon a Grassy Field`c");
addnav("Return to the forest","forest.php?op=return");
if ($session['user']['specialmisc'] != "Nothing to see here, move along.") {
    if ($session[user][hashorse]>0){
        $buff = unserialize($playermount['mountbuff']);
        if ($session['bufflist']['mount']['rounds'] > $buff['rounds']*.5) {
            if ($playermount['partrecharge']) {
                output("`n`n{$playermount['partrecharge']}");
            } else {
                output("`n`n`&You allow your {$playermount['mountname']} to frolic and gambol in the field.");
            }
        } else {
            if ($playermount['recharge']) {
                output("`n`n{$playermount['recharge']}");
            } else {
                output("`n`n`&You allow your {$playermount['mountname']} to hunt and rest in the field.");
            }
        }
    
        $session['bufflist']['mount']=$buff;

        if ($session[user][hitpoints]<$session[user][maxhitpoints]){
            output("`n`^Your nap leaves you completely healed!");
            $session[user][hitpoints] = $session[user][maxhitpoints];
        }
        $session['user']['turns']--;
        output("`n`n`^You lose a forest fight for today.");
    } else {
        output("`n`n`&Deciding to take a moment and a load off your poor weary feet you take a quick break from your ventures to take in the beautiful surroundings.");
        output("`n`n`^Your break leaves you completely healed!");
        if ($session[user][hitpoints]<$session[user][maxhitpoints])
            $session[user][hitpoints] = $session[user][maxhitpoints];
    }
    $session['user']['specialmisc'] = "Nothing to see here, move along.";
} else {
    output("`n`n`&You relax a while in the fields enjoying the sun and the shade.");
}
$session['user']['specialinc'] = "grassyfield.php";

output("`n`n`@Talk with the others lounging here.`n");
viewcommentary("grassyfield","Add",10);
?>
