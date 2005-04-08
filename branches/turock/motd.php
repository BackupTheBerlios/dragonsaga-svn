<?php
require_once "common.php";
addcommentary();
session_write_close();
popup_header("LoGD Message of the Day (MoTD)");
output(($session['user']['superuser']>=3?" [<a href='motd.php?op=add'>Add MoTD</a>|<a href='motd.php?op=addpoll'>Add Poll</a>]`n":""),true);
function motditem($subject,$body){
	output("`b$subject`b`n",true);
	output("$body");
	output("<hr>",true);
}
function pollitem($id,$subject,$body){
	global $session;
	$sql = "SELECT count(resultid) AS c, MAX(choice) AS choice FROM pollresults WHERE motditem='$id' AND account='{$session['user']['acctid']}'";
	$result = db_query($sql);
	$row = db_fetch_assoc($result);
	$choice = $row['choice'];
	$body = unserialize($body);
	if ($row['c']==0 && 0){
		output("<form action='motd.php?op=vote' method='POST'>",true);
		output("<input type='hidden' name='motditem' value='$id'>",true);
		output("`bPoll: $subject`b`n",true);
		output(stripslashes($body['body']));
		while (list($key,$val)=each($body['opt'])){
			if (trim($val)!=""){
				output("`n<input type='radio' name='choice' value='$key'>",true);
				output(stripslashes($val));
			}
		}
		output("`n<input type='submit' class='button' value='Vote'>",true);
		output("</form>",true);
	}else{
		output("<form action='motd.php?op=vote' method='POST'>",true);
		output("<input type='hidden' name='motditem' value='$id'>",true);
		output("`bPoll: $subject`b`n",true);
		output(stripslashes($body['body']));
		$sql = "SELECT count(resultid) AS c, choice FROM pollresults WHERE motditem='$id' GROUP BY choice ORDER BY choice";
		$result = db_query($sql);
		$choices=array();
		$totalanswers=0;
		$maxitem = 0;
		for ($i=0;$i<db_num_rows($result);$i++){
			$row = db_fetch_assoc($result);
			$choices[$row['choice']]=$row['c'];
			$totalanswers+=$row['c'];
			if ($row['c']>$maxitem) $maxitem = $row['c'];
		}
		while (list($key,$val)=each($body['opt'])){
			if (trim($val)!=""){
				if ($totalanswers<=0) $totalanswers=1;
				$percent = round($choices[$key] / $totalanswers * 100,1);
				output("`n<input type='radio' name='choice' value='$key'".($choice==$key?" checked":"").">",true);
				output(stripslashes($val)." (".(int)$choices[$key]." - $percent%)");
				if ($maxitem==0){ $width=1; } else { $width = round(($choices[$key]/$maxitem) * 400,0); }
				$width = max($width,1);
				output("`n<img src='images/rule.png' width='$width' height='2' alt='$percent'>",true);
				//output(stripslashes($val)."`n");
			}
		}
		output("`n<input type='submit' class='button' value='Vote'></form>",true);
	}
	output("<hr>",true);
}
if ($_GET['op']=="vote"){
	$sql = "DELETE FROM pollresults WHERE motditem='{$_POST['motditem']}' AND account='{$session['user']['acctid']}'";
	db_query($sql);
	$sql = "INSERT INTO pollresults (choice,account,motditem) VALUES ('{$_POST['choice']}','{$session['user']['acctid']}','{$_POST['motditem']}')";
	db_query($sql);
	header("Location: motd.php");
	exit();
}
if ($_GET['op']=="addpoll"){
	if($session['user']['superuser']>=3){
		if ($_POST['subject']=="" || $_POST['body']==""){
			output("<form action='motd.php?op=addpoll' method='POST'>",true);
			addnav("","motd.php?op=add");
			output("<input type='text' size='50' name='subject' value='".HTMLEntities(stripslashes($_POST['subject']))."'>`n",true);
			output("<textarea class='input' name='body' cols='37' rows='5'>".HTMLEntities(stripslashes($_POST['body']))."</textarea>`n",true);
			output("Opt <input name='opt[]'>`n",true);
			output("Opt <input name='opt[]'>`n",true);
			output("Opt <input name='opt[]'>`n",true);
			output("Opt <input name='opt[]'>`n",true);
			output("Opt <input name='opt[]'>`n",true);
			output("Opt <input name='opt[]'>`n",true);
			output("<input type='submit' class='button' value='Add'></form>",true);
		}else{
			$body = array("body"=>$_POST['body'],"opt"=>$_POST['opt']);
			$sql = "INSERT INTO motd (motdtitle,motdbody,motddate,motdtype) VALUES ('".$_POST['subject']."','".addslashes(serialize($body))."',now(),1)";
			db_query($sql);
			header("Location: motd.php");
			exit();
		}
	}else{
		if ($session['user']['loggedin']){
			//$session[user][hitpoints]=0;
			//$session[user][alive]=0;
			$session['user']['experience']=round($session['user']['experience']*0.9,0);
			addnews($session['user']['name']." was penalized for attempting to defile the gods.");
			output("You've attempted to defile the gods.  You are struck with a wand of forgetfulness.  Some of what you knew, you no longer know.");
			saveuser();
		}	
	}
}
if ($_GET['op']=="add"){
	if ($session['user']['superuser']>=3){
		if ($_POST['subject']=="" || $_POST['body']==""){
			output("<form action='motd.php?op=add' method='POST'>",true);
			addnav("","motd.php?op=add");
			output("<input type='text' size='50' name='subject' value='".HTMLEntities(stripslashes($_POST['subject']))."'>`n",true);
			output("<textarea class='input' name='body' cols='37' rows='5'>".HTMLEntities(stripslashes($_POST['body']))."</textarea>`n",true);
			output("<input type='submit' class='button' value='Add'></form>",true);
		}else{
			$sql = "INSERT INTO motd (motdtitle,motdbody,motddate) VALUES ('".$_POST['subject']."','".$_POST['body']."',now())";
			db_query($sql);
			header("Location: motd.php");
			exit();
		}
	}else{
		if ($session['user']['loggedin']){
			//$session[user][hitpoints]=0;
			//$session[user][alive]=0;
			$session['user']['experience']=round($session['user']['experience']*0.9,0);
			addnews($session['user']['name']." was penalized for attempting to defile the gods.");
			output("You've attempted to defile the gods.  You are struck with a wand of forgetfulness.  Some of what you knew, you no longer know.");
			saveuser();
		}
	}
}
if ($_GET['op']=="del"){
	if ($session['user']['superuser']>=3){
			$sql = "DELETE FROM motd WHERE motditem='".$_GET['id']."'";
			db_query($sql);
			header("Location: motd.php");
			exit();
	}else{
		if ($session['user']['loggedin']){
			//$session[user][hitpoints]=0;
			//$session[user][alive]=0;
			$session['user']['experience']=round($session['user']['experience']*0.9,0);
			addnews($session['user']['name']." was penalized for attempting to defile the gods.");
			output("You've attempted to defile the gods.  You are struck with a wand of forgetfulness.  Some of what you knew, you no longer know.");
			saveuser();
		}
	}
}


if ($_GET['op']==""){
	output("`&");
	motditem("Testing!","Please see the testing message below.");
	output("`%");

	$sql = "SELECT * FROM motd ORDER BY motddate DESC limit 20";
	$result = db_query($sql);
	for ($i=0;$i<db_num_rows($result);$i++){
		$row = db_fetch_assoc($result);
		if ($row['motddate']>$session['user']['lastmotd'] || $i<5){
			if ($row['motdtype']==0){
				motditem($row['motdtitle'].($session['user']['superuser']>=3?"[<a href='motd.php?op=del&id=".$row['motditem']."' onClick=\"return confirm('Are you sure you want to delete this item?');\">Del</a>]":""),$row['motdbody']);
			}else{
				pollitem($row['motditem'],$row['motdtitle'].($session['user']['superuser']>=3?"[<a href='motd.php?op=del&id=".$row['motditem']."' onClick=\"return confirm('Are you sure you want to delete this item?');\">Del</a>]":""),$row['motdbody']);
			}
		}
	}
	output("`&");
	motditem("Testing!","For those who might be unaware, this game is in testing.  We are working on it when we have time.  Feel free to drop suggestions, I'm open to anything :-)");
	output("`@Commentary:`0`n");
	viewcommentary("motd");
}
/*
motditem("Spring Fashions","My lovely wife is in the process of updating the armor and weapons found in the local shoppes.  There will be a new set for each dragon kill, so you'll never wear the same armor or wield the same weapon twice!");
motditem("Dragon Points!","You probably already noticed by now, but there is a new system named Dragon Points.  These afford you another way to permanently increase  your character's abilities.  You gain one Dragon Point for each dragon slaying.");
motditem("Getting slain in the fields","There is a new drawback, and a new upside to getting attacked in the fields.  If you die in the fields when someone else attacked you, you lose 5% of your exp, along with your gold.  But if you are victorious when someone else attacks you, you get their gold, and 10% of THEIR exp.  When you attack others in the field has not changed, you can get 10% of your victim's exp and all their gold if you win, and you lose 15% of your exp, and all of your gold if you lose.  You cannot attack someone 2 levels below you any longer either, only 1 level below, and 2 levels above.");
motditem("New Days","New days should now occur automatically when the clock in the village reaches midnight.  Due to a bug on my part though, some accounts got goofed up.  Please email me at trash@mightye.org if you are not able to play the game, and I'll fix your account.");
*/

$session['needtoviewmotd']=false;

	$sql = "SELECT motddate FROM motd ORDER BY motditem DESC LIMIT 1";
	$result = db_query($sql);
	$row = db_fetch_assoc($result);
	$session['user']['lastmotd']=$row['motddate'];

popup_footer();
?>
