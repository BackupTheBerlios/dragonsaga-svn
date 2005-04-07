<?php
// this mod needs to be updated.  Lots of spaghetti...

/*
 Updated Gardens
 Re-Coded By: Turock
 Date: 07/06/04

 Notes: Added a fishing pond to the garden area so players would have something to do here.
 You will need to add a general store or somewhere that sells the things players need to go
 fishing. ie: fishingpole, worms, minnows, & lures.

 INSTALL:

 Add to mySQL:
 ALTER TABLE accounts ADD fishturns int(2) not null default'10';
 ALTER TABLE accounts ADD fishingpole int(1) not null default'0';
 ALTER TABLE accounts ADD lures int(2) not null default'0';
 
 You may or may not have these already in your database depending on the mods you have installed:
 ALTER TABLE accounts ADD backpack int(2) not null default'0';
 ALTER TABLE accounts ADD worms int(2) not null default'0';
 ALTER TABLE accounts ADD minnows int(2) not null default'0';

 In newday.php add:
 $session['user']['fishturns']=10;

*/
 
require_once "common.php";
page_header("The Gardens");
checkday();
addcommentary();

if($_GET['op']=="") {
    if($session['user']['fishturns']>0) {
    output("`b`c`2The Gardens`0`c`b");
    output("`n`n`@You walk through a gate and on to one of the many winding paths that makes its way through the well-tended gardens.  From the flowerbeds that bloom even in darkest winter, to the hedges whose shadows promise forbidden secrets, these gardens provide a refuge for those seeking out the Green Dragon; a place where they can forget their troubles for a while and just relax.`n`n");
    output("`^A large pond is here that looks to hold many types of fish. Perhaps you would like to try your luck at some fishing?");
    output("`n`n`^You have `&".$session['user']['fishturns']." `^fishing turns left for today.`n");
    addnav("Things to do");
    addnav("Fish in the pond","gardens.php?op=fishcheck");
    addnav("Return to the Village","village.php");
    viewcommentary("gardens","Chat Here:",30,"whispers");
    } else {
        output("`b`c`2The Gardens`0`c`b");
        output("`n`n`@You walk through a gate and on to one of the many winding paths that makes its way through the well-tended gardens.  From the flowerbeds that bloom even in darkest winter, to the hedges whose shadows promise forbidden secrets, these gardens provide a refuge for those seeking out the Green Dragon; a place where they can forget their troubles for a while and just relax.`n`n");
        output("`^A large pond is here that looks to hold many types of fish.");
        output("`n`n`%You are too tired to fish anymore today`&...`n");
        addnav("Things to do");
        addnav("Return to the Village","village.php");
        viewcommentary("gardens","Chat Here:",30,"whispers");
    }
}
elseif($_GET['op']=="fishcheck") {
    output("`b`c`2The Gardens`0`c`b");
    if($session['user']['fishingpole']!=1) {
        output("`nYou need a fishing pole to go fishing in the pond. Buy one at the general store.`n");
    }
    if(($session['user']['worms']<1) && ($session['user']['minnows']<1) && ($session['user']['lures']<1)) {
        output("`nYou need some kind of bait or lure to fish with. Buy some at the general store.`n");
        addnav("Continue","gardens.php");
        addnav("Return to the Village","village.php");
    } else {
        redirect("gardens.php?op=gofishing");
    }
}
elseif($_GET[op]=="gofishing") {
    output("`b`c`2The Gardens`0`c`b");
    $sql = "SELECT worms,minnows,lures FROM accounts WHERE acctid=".$session['user']['acctid']."";
    $result = db_query($sql);
    $row = db_fetch_assoc($result);
    output("`n`^Looking in your pack you see that you have:`n`n`%$row[worms] `&- `^worms`n`%$row[minnows] `&- `^minnows`n`%$row[lures] `&- `^lures");
    output("`n`n`&What would you like to fish with?");
    if($session['user']['worms']>=1) addnav("Worms","gardens.php?op=cast&bait=worms");
    if($session['user']['minnows']>=1) addnav("Minnows","gardens.php?op=cast&bait=minnows");
    if($session['user']['lures']>=1) addnav("Lures","gardens.php?op=cast&bait=lures");
    addnav("Exit","gardens.php");
}
elseif($_GET[op]=="cast") {
    $bait = $_REQUEST['bait'];
    output("`b`c`2The Gardens`0`c`b");
    output("`n`^You think that maybe $bait would catch you a prize fish today.");
    output("`^You pick out the best one you can find, and cast out.");
    output("`n`nYou sit and wait for a while... ");
    $session['user']['fishturns']--;
    if($bait == "worms") $session['user']['worms']--;
    if($bait == "minnows") $session['user']['minnows']--;
    $rand = e_rand(1,22);
    switch($rand) {
        case 1:
        output("`^when all of a sudden... `4WHAM!`4 `^You get a bite and hook a fish!`n");
        $rand2 = e_rand(1,150);
        if($rand2 >= 130) {
            $rand3 = $session['user']['level'] + e_rand(1,650);
            $size = round(sqrt(($rand3)*.75),2);
            output("`n`^After fighting a skillful battle you manage to land a `%trophy size Largemouth Bass `^that weighs: `&$size lbs`^.`n");
            addnews("`%".$session['user']['name']." `^caught a trophy size Largemouth Bass that weighed `&$size `^lbs! while fishing in the gardens.");
            $sql = "SELECT size FROM fish WHERE fishtype='1'";
            $results = db_query($sql);
            $row = db_fetch_assoc($results);
            $currentrec = $row['size'];
            if($currentrec < $size) {
                output("`n`&We have a new record!!!`n");
                $sql = "UPDATE fish SET name='".$session['user']['name']."',fishtype='1',size='$size' WHERE fishtype='1'";
                db_query($sql);
            } else {
                output("`n`&Its a whopper all right!, but the current record is: `%$size `&lbs");
            }
        $_GET[op]="";
        }
        elseif($rand2 >= 70 && $rand2 < 130) {
            $rand3 = $session['user']['level'] + e_rand(1,100);
            $size = round(sqrt(($rand3)*.75),2);
            output("`n`^After fighting a skillful battle you manage to land a `%Largemouth Bass `^that weighs: `&$size lbs`^.`n");
            $_GET[op]="";
        }
        elseif($rand2 > 50 && $rand2 < 70) {
            $rand3 = $session['user']['level'] + e_rand(1,10);
            $size = round(sqrt(($rand3)*.75),2);
            output("`n`^After fighting a skillful battle you manage to land a `%Largemouth Bass `^that weighs: `&$size lbs`^.`n");
            $_GET[op]="";
        } else {
           output("`n`^You fight a mighty battle but in the end the fish gets away...");
        }
        addnav("Continue","gardens.php");
        break;

        case 2:
        output("`^you feel something heavy on the line... you pull up ");
        $catch = e_rand(1,6);
        if($catch == 1) {
            output("an old boot!");
        }
        if($catch == 2) {
            $findgold = $session['user']['level']*100;
            $session['user']['gold']+=$findgold;
            output("a small pouch with $findgold gold in it!");
            debuglog("found $findgold gold while fishing in the pond");
        }
        if($catch == 3) {
            $findgem = e_rand(1,3);
            $session['user']['gems']+=$findgem;
            output("a small pouch with $findgem gems in it!");
            debuglog("found $findgem gems while fishing in the pond");
        }
        if($catch == 4) {
            output("an old can!");
        }
        if($catch == 5) {
            output("a mess of fishing line!");
        }
        if($catch == 6) {
            output("a stick!");
        }

        addnav("Continue","gardens.php");
        break;

        case 3:
        output("`^when all of a sudden... `4WHAM!`4 `^You get a bite and hook a fish!`n");
        $rand2 = e_rand(1,150);
        if($rand2 >= 130) {
            $rand3 = $session['user']['level'] + e_rand(1,60);
            $size = round(sqrt(($rand3)*.75),2);
            output("`n`^After fighting a skillful battle you manage to land a `%trophy size Trout `^that weighs: `&$size lbs`^.`n");
            addnews("`%".$session['user']['name']." `^caught a trophy size Trout that weighed `&$size `^lbs! while fishing in the gardens.");
            $sql = "SELECT size FROM fish WHERE fishtype='2'";
            $results = db_query($sql);
            $row = db_fetch_assoc($results);
            $currentrec = $row['size'];
            if($currentrec < $size) {
                output("`n`&We have a new record!!!`n");
                $sql = "UPDATE fish SET name='".$session['user']['name']."',fishtype='2',size='$size' WHERE fishtype='2'";
                db_query($sql);
            } else {
                output("`n`&Its a whopper all right!, but the current record is: `%$size `&lbs");
            }
        $_GET[op]="";
        }
        elseif($rand2 >= 70 && $rand2 < 130) {
            $rand3 = $session['user']['level'] + e_rand(1,40);
            $size = round(sqrt(($rand3)*.75),2);
            output("`n`^After fighting a skillful battle you manage to land a `%Trout `^that weighs: `&$size lbs`^.`n");
            $_GET[op]="";
        }
        elseif($rand2 > 50 && $rand2 < 70) {
            $rand3 = $session['user']['level'] + e_rand(1,20);
            $size = round(sqrt(($rand3)*.75),2);
            output("`n`^After fighting a skillful battle you manage to land a `%Trout `^that weighs: `&$size lbs`^.`n");
            $_GET[op]="";
        } else {
           output("`n`^You fight a mighty battle but in the end the fish gets away...");
        }
        addnav("Continue","gardens.php");
        break;

        case 4:
        output("`^but nothing happens... Maybe the fish aren't biting today");
        addnav("Continue","gardens.php");
        break;

        case 5:
        output("`^when all of a sudden... `4WHAM!`4 `^You get a bite and hook a fish!`n");
        $rand2 = e_rand(1,150);
        if($rand2 >= 130) {
            $rand3 = $session['user']['level'] + e_rand(1,175);
            $size = round(sqrt(($rand3)*.75),2);
            output("`n`^After fighting a skillful battle you manage to land a `%trophy size Catfish `^that weighs: `&$size lbs`^.`n");
            addnews("`%".$session['user']['name']." `^caught a trophy size Catfish that weighed `&$size `^lbs! while fishing in the gardens.");
            $sql = "SELECT size FROM fish WHERE fishtype='3'";
            $results = db_query($sql);
            $row = db_fetch_assoc($results);
            $currentrec = $row['size'];
            if($currentrec < $size) {
                output("`n`&We have a new record!!!`n");
                $sql = "UPDATE fish SET name='".$session['user']['name']."',fishtype='3',size='$size' WHERE fishtype='3'";
                db_query($sql);
            } else {
                output("`n`&Its a whopper all right!, but the current record is: `%$size `&lbs");
            }
        $_GET[op]="";
        }
        elseif($rand2 >= 70 && $rand2 < 130) {
            $rand3 = $session['user']['level'] + e_rand(1,95);
            $size = round(sqrt(($rand3)*.75),2);
            output("`n`^After fighting a skillful battle you manage to land a `%Catfish `^that weighs: `&$size lbs`^.`n");
            $_GET[op]="";
        }
        elseif($rand2 > 50 && $rand2 < 70) {
            $rand3 = $session['user']['level'] + e_rand(1,45);
            $size = round(sqrt(($rand3)*.75),2);
            output("`nAfter fighting a skillful battle you manage to land a `%Catfish `^that weighs: `&$size lbs`^.`n");
            $_GET[op]="";
        } else {
           output("`n`^You fight a mighty battle but in the end the fish gets away...");
        }
        addnav("Continue","gardens.php");
        break;

        case 6:
        output("`^but nothing happens... Maybe the fish aren't biting today");
        addnav("Continue","gardens.php");
        break;

        case 7:
        if($bait == "lures") {
        output("`^when... your lure gets snagged on something... Try as you might, you can't it unhung and you lose a lure.");
        $session[user][lures]--;
        } else {
            output("`^when... you seemed to have become snagged on something. Try as you might, you can't it unhung and your line breaks");
        }
        addnav("Continue","gardens.php");
        break;

        case 8:
        output("`^when all of a sudden... `4WHAM!`4 `^You get a bite and hook a fish!`n");
        $rand2 = e_rand(1,150);
        if($rand2 >= 130) {
            $rand3 = $session['user']['level'] + e_rand(1,40);
            $size = round(sqrt(($rand3)*.75),2);
            output("`n`^After fighting a skillful battle you manage to land a `%trophy size Crappie `^that weighs: `&$size lbs`^.`n");
            addnews("`%".$session['user']['name']." `^caught a trophy size Crappie that weighed `&$size `^lbs! while fishing in the gardens.");
            $sql = "SELECT size FROM fish WHERE fishtype='4'";
            $results = db_query($sql);
            $row = db_fetch_assoc($results);
            $currentrec = $row['size'];
            if($currentrec < $size) {
                output("`n`&We have a new record!!!`n");
                $sql = "UPDATE fish SET name='".$session['user']['name']."',fishtype='4',size='$size' WHERE fishtype='4'";
                db_query($sql);
            } else {
                output("`n`&Its a whopper all right!, but the current record is: `%$size `&lbs");
            }
        $_GET[op]="";
        }
        elseif($rand2 >= 70 && $rand2 < 130) {
            $rand3 = $session['user']['level'] + e_rand(1,20);
            $size = round(sqrt(($rand3)*.75),2);
            output("`n`^After fighting a skillful battle you manage to land a `%Crappie `^that weighs: `&$size lbs`^.`n");
            $_GET[op]="";
        }
        elseif($rand2 > 50 && $rand2 < 70) {
            $rand3 = $session['user']['level'] + e_rand(1,10);
            $size = round(sqrt(($rand3)*.75),2);
            output("`n`^After fighting a skillful battle you manage to land a `%Crappie `^that weighs: `&$size lbs`^.`n");
            $_GET[op]="";
        } else {
           output("`n`^You fight a mighty battle but in the end the fish gets away...");
        }
        addnav("Continue","gardens.php");
        break;

        case 9:
        output("`^when all of a sudden... `4WHAM!`4 `^You get a bite and hook a fish!`n");
        $rand2 = e_rand(1,150);
        if($rand2 >= 130) {
            $rand3 = $session['user']['level'] + e_rand(1,75);
            $size = round(sqrt(($rand3)*.75),2);
            output("`n`^After fighting a skillful battle you manage to land a `%trophy size Smallmouth Bass `^that weighs: `&$size lbs`^.`n");
            addnews("`%".$session['user']['name']." `^caught a trophy size Smallmouth Bass that weighed `&$size `^lbs! while fishing in the gardens");
            $sql = "SELECT size FROM fish WHERE fishtype='5'";
            $results = db_query($sql);
            $row = db_fetch_assoc($results);
            $currentrec = $row['size'];
            if($currentrec < $size) {
                output("`n`&We have a new record!!!`n");
                $sql = "UPDATE fish SET name='".$session['user']['name']."',fishtype='5',size='$size' WHERE fishtype='5'";
                db_query($sql);
            } else {
                output("`n`&Its a whopper all right!, but the current record is: `%$size `&lbs");
            }
        $_GET[op]="";
        }
        elseif($rand2 >= 70 && $rand2 < 130) {
            $rand3 = $session['user']['level'] + e_rand(1,45);
            $size = round(sqrt(($rand3)*.75),2);
            output("`n`^After fighting a skillful battle you manage to land a `%Smallmouth Bass `^that weighs: `&$size lbs`^.`n");
            $_GET[op]="";
        }
        elseif($rand2 > 50 && $rand2 < 70) {
            $rand3 = $session['user']['level'] + e_rand(1,25);
            $size = round(sqrt(($rand3)*.75),2);
            output("`n`^After fighting a skillful battle you manage to land a `%Smallmouth Bass `^that weighs: `&$size lbs`^.`n");
            $_GET[op]="";
        } else {
           output("`n`^You fight a mighty battle but in the end the fish gets away...");
        }
        addnav("Continue","gardens.php");
        break;

        case 10:
        if($bait == "lures") {
        output("`^when... your lure gets snagged on something... Try as you might, you can't it unhung and you lose a lure.");
        $session[user][lures]--;
        } else {
            output("`^when... you seemed to have become snagged on something. Try as you might, you can't it unhung and your line breaks");
        }
        addnav("Continue","gardens.php");
        break;

        case 11:
        output("`^you feel something heavy on the line... you pull up ");
        $catch = e_rand(1,6);
        if($catch == 1) {
            output("an old boot!");
        }
        if($catch == 2) {
            $findgold = $session['user']['level']*100;
            $session['user']['gold']+=$findgold;
            output("a small pouch with $findgold gold in it!");
            debuglog("found $findgold gold while fishing in the pond");
        }
        if($catch == 3) {
            $findgem = e_rand(1,3);
            $session['user']['gems']+=$findgem;
            output("a small pouch with $findgem gems in it!");
            debuglog("found $findgem gems while fishing in the pond");
        }
        if($catch == 4) {
            output("an old can!");
        }
        if($catch == 5) {
            output("a mess of fishing line!");
        }
        if($catch == 6) {
            output("a stick!");
        }

        addnav("Continue","gardens.php");
        break;

        case 12:
        output("`^when all of a sudden... `4WHAM!`4 `^You get a bite and hook a fish!`n");
        $rand2 = e_rand(1,150);
        if($rand2 >= 130) {
            $rand3 = $session['user']['level'] + e_rand(1,1150);
            $size = round(sqrt(($rand3)*.75),2);
            output("`n`^After fighting a skillful battle you manage to land a `%trophy size Carp `^that weighs: `&$size lbs`^.`n");
            addnews("`%".$session['user']['name']." `^caught a trophy size Carp that weighed `&$size `^lbs! while fishing in the gardens");
            $sql = "SELECT size FROM fish WHERE fishtype='6'";
            $results = db_query($sql);
            $row = db_fetch_assoc($results);
            $currentrec = $row['size'];
            if($currentrec < $size) {
                output("`n`&We have a new record!!!`n");
                $sql = "UPDATE fish SET name='".$session['user']['name']."',fishtype='6',size='$size' WHERE fishtype='6'";
                db_query($sql);
            } else {
                output("`n`&Its a whopper all right!, but the current record is: `%$size `&lbs");
            }
        $_GET[op]="";
        }
        elseif($rand2 >= 70 && $rand2 < 130) {
            $rand3 = $session['user']['level'] + e_rand(1,500);
            $size = round(sqrt(($rand3)*.75),2);
            output("`n`^After fighting a skillful battle you manage to land a `%Carp `^that weighs: `&$size lbs`^.`n");
            $_GET[op]="";
        }
        elseif($rand2 > 50 && $rand2 < 70) {
            $rand3 = $session['user']['level'] + e_rand(1,250);
            $size = round(sqrt(($rand3)*.75),2);
            output("`n`^After fighting a skillful battle you manage to land a `%Carp `^that weighs: `&$size lbs`^.`n");
            $_GET[op]="";
        } else {
           output("`n`^You fight a mighty battle but in the end the fish gets away...");
        }
        addnav("Continue","gardens.php");
        break;

        case 13:
        output("`^when all of a sudden... `4WHAM!`4 `^You get a bite and hook a fish!`n");
        $rand2 = e_rand(1,150);
        if($rand2 >= 130) {
            $rand3 = $session['user']['level'] + e_rand(1,550);
            $size = round(sqrt(($rand3)*.75),2);
            output("`n`^After fighting a skillful battle you manage to land a `%trophy size Muskie `^that weighs: `&$size lbs`^.`n");
            addnews("`%".$session['user']['name']." `^caught a trophy size Muskie that weighed `&$size `^lbs! while fishing in the gardens");
            $sql = "SELECT size FROM fish WHERE fishtype='7'";
            $results = db_query($sql);
            $row = db_fetch_assoc($results);
            $currentrec = $row['size'];
            if($currentrec < $size) {
                output("`n`&We have a new record!!!`n");
                $sql = "UPDATE fish SET name='".$session['user']['name']."',fishtype='7',size='$size' WHERE fishtype='7'";
                db_query($sql);
            } else {
                output("`n`&Its a whopper all right!, but the current record is: `%$size `&lbs");
            }
        $_GET[op]="";
        }
        elseif($rand2 >= 70 && $rand2 < 130) {
            $rand3 = $session['user']['level'] + e_rand(1,300);
            $size = round(sqrt(($rand3)*.75),2);
            output("`n`^After fighting a skillful battle you manage to land a `%Muskie `^that weighs: `&$size lbs`^.`n");
            $_GET[op]="";
        }
        elseif($rand2 > 50 && $rand2 < 70) {
            $rand3 = $session['user']['level'] + e_rand(1,150);
            $size = round(sqrt(($rand3)*.75),2);
            output("`n`^After fighting a skillful battle you manage to land a `%Muskie `^that weighs: `&$size lbs`^.`n");
            $_GET[op]="";
        } else {
           output("`n`^You fight a mighty battle but in the end the fish gets away...");
        }
        addnav("Continue","gardens.php");
        break;
        
        case 14:
        output("`^you feel something heavy on the line... you pull up ");
        $catch = e_rand(1,6);
        if($catch == 1) {
            output("an old boot!");
        }
        if($catch == 2) {
            $findgold = $session['user']['level']*100;
            $session['user']['gold']+=$findgold;
            output("a small pouch with $findgold gold in it!");
            debuglog("found $findgold gold while fishing in the pond");
        }
        if($catch == 3) {
            $findgem = e_rand(1,3);
            $session['user']['gems']+=$findgem;
            output("a small pouch with $findgem gems in it!");
            debuglog("found $findgem gems while fishing in the pond");
        }
        if($catch == 4) {
            output("an old can!");
        }
        if($catch == 5) {
            output("a mess of fishing line!");
        }
        if($catch == 6) {
            output("a stick!");
        }

        addnav("Continue","gardens.php");
        break;

        case 15:
        output("`^when all of a sudden... `4WHAM!`4 `^You get a bite and hook a fish!`n");
        $rand2 = e_rand(1,150);
        if($rand2 >= 130) {
            $rand3 = $session['user']['level'] + e_rand(1,175);
            $size = round(sqrt(($rand3)*.75),2);
            output("`n`^After fighting a skillful battle you manage to land a `%trophy size Pike `^that weighs: `&$size lbs`^.`n");
            addnews("`%".$session['user']['name']." `^caught a trophy size Pike that weighed `&$size `^lbs! while fishing in the gardens");
            $sql = "SELECT size FROM fish WHERE fishtype='8'";
            $results = db_query($sql);
            $row = db_fetch_assoc($results);
            $currentrec = $row['size'];
            if($currentrec < $size) {
                output("`n`&We have a new record!!!`n");
                $sql = "UPDATE fish SET name='".$session['user']['name']."',fishtype='8',size='$size' WHERE fishtype='8'";
                db_query($sql);
            } else {
                output("`n`&Its a whopper all right!, but the current record is: `%$size `&lbs");
            }
        $_GET[op]="";
        }
        elseif($rand2 >= 70 && $rand2 < 130) {
            $rand3 = $session['user']['level'] + e_rand(1,95);
            $size = round(sqrt(($rand3)*.75),2);
            output("`n`^After fighting a skillful battle you manage to land a `%Pike `^that weighs: `&$size lbs`^.`n");
            $_GET[op]="";
        }
        elseif($rand2 > 50 && $rand2 < 70) {
            $rand3 = $session['user']['level'] + e_rand(1,45);
            $size = round(sqrt(($rand3)*.75),2);
            output("`n`^After fighting a skillful battle you manage to land a `%Pike `^that weighs: `&$size lbs`^.`n");
            $_GET[op]="";
        } else {
           output("`n`^You fight a mighty battle but in the end the fish gets away...");
        }
        addnav("Continue","gardens.php");
        break;

        case 16:
        output("`^when all of a sudden... `4WHAM!`4 `^You get a bite and hook a fish!`n");
        $rand2 = e_rand(1,150);
        if($rand2 >= 130) {
            $rand3 = $session['user']['level'] + e_rand(1,750);
            $size = round(sqrt(($rand3)*.75),2);
            output("`n`^After fighting a skillful battle you manage to land a `%trophy size Salmon `^that weighs: `&$size lbs`^.`n");
            addnews("`%".$session['user']['name']." `^caught a trophy size Salmon that weighed `&$size `^lbs! while fishing in the gardens");
            $sql = "SELECT size FROM fish WHERE fishtype='9'";
            $results = db_query($sql);
            $row = db_fetch_assoc($results);
            $currentrec = $row['size'];
            if($currentrec < $size) {
                output("`n`&We have a new record!!!`n");
                $sql = "UPDATE fish SET name='".$session['user']['name']."',fishtype='9',size='$size' WHERE fishtype='9'";
                db_query($sql);
            } else {
                output("`n`&Its a whopper all right!, but the current record is: `%$size `&lbs");
            }
        $_GET[op]="";
        }
        elseif($rand2 >= 70 && $rand2 < 130) {
            $rand3 = $session['user']['level'] + e_rand(1,300);
            $size = round(sqrt(($rand3)*.75),2);
            output("`n`^After fighting a skillful battle you manage to land a `%Salmon `^that weighs: `&$size lbs`^.`n");
            $_GET[op]="";
        }
        elseif($rand2 > 50 && $rand2 < 70) {
            $rand3 = $session['user']['level'] + e_rand(1,150);
            $size = round(sqrt(($rand3)*.75),2);
            output("`n`^After fighting a skillful battle you manage to land a `%Salmon `^that weighs: `&$size lbs`^.`n");
            $_GET[op]="";
        } else {
           output("`n`^You fight a mighty battle but in the end the fish gets away...");
        }
        addnav("Continue","gardens.php");
        break;
        case 17:
        output("`^when all of a sudden... `4WHAM!`4 `^You get a bite and hook a fish!`n");
        $rand2 = e_rand(1,150);
        if($rand2 >= 130) {
            $rand3 = $session['user']['level'] + e_rand(1,650);
            $size = round(sqrt(($rand3)*.75),2);
            output("`n`^After fighting a skillful battle you manage to land a `%trophy size Walleye `^that weighs: `&$size lbs`^.`n");
            addnews("`%".$session['user']['name']." `^caught a trophy size Walleye that weighed `&$size `^lbs! while fishing in the gardens");
            $sql = "SELECT size FROM fish WHERE fishtype='10'";
            $results = db_query($sql);
            $row = db_fetch_assoc($results);
            $currentrec = $row['size'];
            if($currentrec < $size) {
                output("`n`&We have a new record!!!`n");
                $sql = "UPDATE fish SET name='".$session['user']['name']."',fishtype='10',size='$size' WHERE fishtype='10'";
                db_query($sql);
            } else {
                output("`n`&Its a whopper all right!, but the current record is: `%$size `&lbs");
            }
        $_GET[op]="";
        }
        elseif($rand2 >= 70 && $rand2 < 130) {
            $rand3 = $session['user']['level'] + e_rand(1,150);
            $size = round(sqrt(($rand3)*.75),2);
            output("`n`^After fighting a skillful battle you manage to land a `%Walleye `^that weighs: `&$size lbs`^.`n");
            $_GET[op]="";
        }
        elseif($rand2 > 50 && $rand2 < 70) {
            $rand3 = $session['user']['level'] + e_rand(1,25);
            $size = round(sqrt(($rand3)*.75),2);
            output("`n`^After fighting a skillful battle you manage to land a `%Walleye `^that weighs: `&$size lbs`^.`n");
            $_GET[op]="";
        } else {
           output("`n`^You fight a mighty battle but in the end the fish gets away...");
        }
        addnav("Continue","gardens.php");
        break;

        case 18:
        output("`^but nothing happens... Maybe the fish aren't biting today");
        addnav("Continue","gardens.php");
        break;

        case 19:
        output("`^when all of a sudden... `4WHAM!`4 `^You get a bite and hook a fish!`n");
        $rand2 = e_rand(1,150);
        if($rand2 >= 130) {
            $rand3 = $session['user']['level'] + e_rand(1,40);
            $size = round(sqrt(($rand3)*.75),2);
            output("`n`^After fighting a skillful battle you manage to land a `%trophy size Perch `^that weighs: `&$size lbs`^.`n");
            addnews("`%".$session['user']['name']." `^caught a trophy size Perch that weighed `&$size `^lbs! while fishing in the gardens");
            $sql = "SELECT size FROM fish WHERE fishtype='11'";
            $results = db_query($sql);
            $row = db_fetch_assoc($results);
            $currentrec = $row['size'];
            if($currentrec < $size) {
                output("`n`&We have a new record!!!`n");
                $sql = "UPDATE fish SET name='".$session['user']['name']."',fishtype='11',size='$size' WHERE fishtype='11'";
                db_query($sql);
            } else {
                output("`n`&Its a whopper all right!, but the current record is: `%$size `&lbs");
            }
        $_GET[op]="";
        }
        elseif($rand2 >= 70 && $rand2 < 130) {
            $rand3 = $session['user']['level'] + e_rand(1,20);
            $size = round(sqrt(($rand3)*.75),2);
            output("`n`^After fighting a skillful battle you manage to land a `%Perch `^that weighs: `&$size lbs`^.`n");
            $_GET[op]="";
        }
        elseif($rand2 > 50 && $rand2 < 70) {
            $rand3 = $session['user']['level'] + e_rand(1,10);
            $size = round(sqrt(($rand3)*.75),2);
            output("`n`^After fighting a skillful battle you manage to land a `%Perch `^that weighs: `&$size lbs`^.`n");
            $_GET[op]="";
        } else {
           output("`n`^You fight a mighty battle but in the end the fish gets away...");
        }
        addnav("Continue","gardens.php");
        break;

        case 20:
        output("`^when all of a sudden... `4WHAM!`4 `^You get a bite and hook a fish!`n");
        $rand2 = e_rand(1,150);
        if($rand2 >= 130) {
            $rand3 = $session['user']['level'] + e_rand(1,1150);
            $size = round(sqrt(($rand3)*.75),2);
            output("`n`^After fighting a skillful battle you manage to land a `%trophy size Striped Bass `^that weighs: `&$size lbs`^.`n");
            addnews("`%".$session['user']['name']." `^caught a trophy size Striped Bass that weighed `&$size `^lbs! while fishing in the gardens");
            $sql = "SELECT size FROM fish WHERE fishtype='12'";
            $results = db_query($sql);
            $row = db_fetch_assoc($results);
            $currentrec = $row['size'];
            if($currentrec < $size) {
                output("`n`&We have a new record!!!`n");
                $sql = "UPDATE fish SET name='".$session['user']['name']."',fishtype='12',size='$size' WHERE fishtype='12'";
                db_query($sql);
            } else {
                output("`n`&Its a whopper all right!, but the current record is: `%$size `&lbs");
            }
        $_GET[op]="";
        }
        elseif($rand2 >= 70 && $rand2 < 130) {
            $rand3 = $session['user']['level'] + e_rand(1,500);
            $size = round(sqrt(($rand3)*.75),2);
            output("`n`^After fighting a skillful battle you manage to land a `%Striped Bass `^that weighs: `&$size lbs`^.`n");
            $_GET[op]="";
        }
        elseif($rand2 > 50 && $rand2 < 70) {
            $rand3 = $session['user']['level'] + e_rand(1,250);
            $size = round(sqrt(($rand3)*.75),2);
            output("`n`^After fighting a skillful battle you manage to land a `%Striped Bass `^that weighs: `&$size lbs`^.`n");
            $_GET[op]="";
        } else {
           output("`n`^You fight a mighty battle but in the end the fish gets away...");
        }
        addnav("Continue","gardens.php");
        break;

        case 21:
        output("`^but nothing happens... Maybe the fish aren't biting today");
        addnav("Continue","gardens.php");
        break;

        case 22:
        output("`^you feel something heavy on the line... you pull up ");
        $catch = e_rand(1,6);
        if($catch == 1) {
            output("an old boot!");
        }
        if($catch == 2) {
            $findgold = $session['user']['level']*100;
            $session['user']['gold']+=$findgold;
            output("a small pouch with $findgold gold in it!");
            debuglog("found $findgold gold while fishing in the pond");
        }
        if($catch == 3) {
            $findgem = e_rand(1,3);
            $session['user']['gems']+=$findgem;
            output("a small pouch with $findgem gems in it!");
            debuglog("found $findgem gems while fishing in the pond");
        }
        if($catch == 4) {
            output("an old can!");
        }
        if($catch == 5) {
            output("a mess of fishing line!");
        }
        if($catch == 6) {
            output("a stick!");
        }

        addnav("Continue","gardens.php");
        break;
    }
}
page_footer();
?>
