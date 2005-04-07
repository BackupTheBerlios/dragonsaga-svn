<?php
require_once "dbwrapper.php";

$pagestarttime = getmicrotime();

$nestedtags=array();
$output="";

function get_special_var ($var, $event=FALSE, $player=FALSE) {
	global $session;
	global $specialinc;

	if ($player === FALSE) $player = $session['user']['acctid'];
	if ($event === FALSE) $event = $specialinc;

	// Query once for all-player vars, and once for the current players vars
	// There must be a more efficient way to do this.  Maybe querying both and ordering by player?
	$query = "SELECT * FROM specialvars WHERE event='$event' AND var='$var' AND player=NULL";
	$res = db_query($query) or die (db_error($link));
	if ($row = db_fetch_assoc($res))
		$retval = $row['value'];
	else $retval = NULL;

	$query = "SELECT * FROM specialvars WHERE event='$event' AND var='$var' AND player=$player";
	$res = db_query($query) or die (db_error($link));
	if ($row = db_fetch_assoc($res))
		$retval = $row['value'];
	else $retval = NULL;

	return $retval;
}
function set_special_var ($var, $value=NULL, $event = FALSE, $player = FALSE) {
    global $session;
    global $specialinc;

    if ($player === FALSE) $player = $session['user']['acctid'];
    if ($event === FALSE) $event = $specialinc;

    $query = "SELECT * FROM specialvars
                WHERE event='$event'
                AND player=$player
                AND var='$var'";
    $res = db_query($query) or die (db_error($link));
    if (db_num_rows($res))
        $query = "UPDATE specialvars SET value='$value'
                    WHERE event='$event'
                    AND player='$player'
                    AND var='$var'";
    else
        $query = "INSERT INTO specialvars (event, player, var, value)
                    VALUES ('$event', $player, '$var', '$value') ";
    if ($value === NULL)
        $query = "DELETE FROM specialvars
                    WHERE event='$event'
                    AND player='$player'
                    AND var='$var'";
    $res = db_query($query) or die(db_error($link));
    $retval = get_special_var($var);
    return $retval;
}

function get_global_var ($var) {
   global $session;
   global $specialinc;
   $query = "SELECT * FROM globalvars WHERE variable='$var'";
   $res = db_query($query) or die (db_error($link));
   if ($row = db_fetch_assoc($res))
      $retval = $row['data'];
//   else $retval = NULL;
   return $retval;
}
function set_global_var ($var,$data) {
    global $session;
    global $specialinc;
    $query = "SELECT * FROM globalvars WHERE variable='$var'";
    $res = db_query($query) or die (db_error($link));
    if (db_num_rows($res))
        $query = "UPDATE globalvars SET data='$data' WHERE variable='$var'";
    else
        $query = "INSERT INTO globalvars (variable, data)
                    VALUES ('$var', '$data') ";
    if ($data === NULL)
        $query = "DELETE FROM globalvars WHERE variable='$var'";
    $res = db_query($query) or die(db_error($link));
    $retval = get_global_var($var);
    return $retval;
}

// IGM CODE
function igm_listnavs($section,$header="Venture Forth"){
    $sql = "SELECT * FROM igms WHERE section=\"$section\" ORDER BY menuname";
    $result = db_query($sql)or die(db_error(LINK));
    addnav($header);
    while($row = mysql_fetch_array($result)){
        $menunames[]=$row["menuname"];
        $filenames[]=$row["filename"];
    }
    $ec = count($menunames);
    for($i=0;$i<$ec;$i++){
        if (@file_exists($filenames[$i])) addnav($menunames[$i],$filenames[$i]);
    }
    db_free_result($result);
}

function igm_process($section){
    $sql = "SELECT * FROM igms WHERE section=\"$section\" ORDER BY menuname";
    $result = db_query($sql)or die(db_error(LINK));
    while($row = mysql_fetch_array($result)){
        $menunames[]=$row["menuname"];
        $filenames[]=$row["filename"];
    }
    $ec = count($menunames);
    for($i=0;$i<$ec;$i++){
        if (@file_exists($filenames[$i])) include($filenames[$i]);
    }
    db_free_result($result);
}
// END IGM CODE

function commas($str) {
    return number_format($str,0,".",",");
}

function pvpwarning($dokill=false) {
	global $session;
	$days = getsetting("pvpimmunity", 5);
	$exp = getsetting("pvpminexp", 1500);
	if ($session['user']['age'] <= $days &&
                $session['user']['dragonkills'] == 0 &&
		$session['user']['user']['pk'] == 0 &&
		$session['user']['experience'] <= $exp) {
		if ($dokill) {
			output("`\$Warning!`^ Since you were still under PvP immunity, but have chosen to attack another player, you have lost this immunity!!`n`n");
			$session['user']['pk'] = 1;
		} else {
			output("`\$Warning!`^ Players are immune from Player vs Player (PvP) combat for their first $days days in the game or until they have earned $exp experience, or until they attack another player.  If you choose to attack another player, you will lose this immunity!`n`n");
		}
	}
}

function rawoutput($indata) {
	global $output;
	$output .= $indata . "\n";
}

function output($indata,$priv=false){
	global $nestedtags,$output;
	$data = translate($indata);
	if (date("m-d")=="04-01"){
		$out = appoencode($data,$priv);
		if ($priv==false) $out = borkalize($out);
		$output.=$out;
	}else{
	  $output.=appoencode($data,$priv);
	}
	$output.="\n";
	return 0;
}

function safeescape($input){
	//$subject = preg_replace("/(^\\\\)[']/","\\1\\\\"."'",$input);
	//$subject = preg_replace('/(^\\\\)["]/',"\\1\\\\".'"',$subject);
	$prevchar="";
	$output="";
	for ($x=0;$x<strlen($input);$x++){
		$char = substr($input,$x,1);
		if (($char=="'" || $char=='"') && $prevchar!="\\"){
			$char="\\$char";
		}
		$output.=$char;
		$prevchar=$char;
	}
	return $output;
}

function systemmail($to,$subject,$body,$from=0,$noemail=false){
	$subject = safeescape($subject);
	$subject=str_replace("\n","",$subject);
	$subject=str_replace("`n","",$subject);
	$body = safeescape($body);
	//echo $subject."<br>".$body;
	$sql = "SELECT prefs,emailaddress FROM accounts WHERE acctid='$to'";
	$result = db_query($sql);
	$row = db_fetch_assoc($result);
	db_free_result($result);
	$prefs = unserialize($row['prefs']);

	if ($prefs['dirtyemail']){
		//output("Not cleaning: $prefs[dirtyemail]");
	}else{
		//output("Cleaning: $prefs[dirtyemail]");
		$subject=soap($subject);
		$body=soap($body);
	}

	$sql = "INSERT INTO mail (msgfrom,msgto,subject,body,sent) VALUES ('".(int)$from."','".(int)$to."','$subject','$body',now())";
	db_query($sql);
	$email=false;
	if ($prefs['emailonmail'] && $from>0){
		$email=true;
	}elseif($prefs['emailonmail'] && $from==0 && $prefs['systemmail']){
		$email=true;
	}
	if (!is_email($row['emailaddress'])) $email=false;
	if ($email && !$noemail){
		$sql = "SELECT name FROM accounts WHERE acctid='$from'";
		$result = db_query($sql);
		$row1=db_fetch_assoc($result);
		db_free_result($result);
		if ($row1['name']!="") $fromline="From: ".preg_replace("'[`].'","",$row1['name'])."\n";
		// We've inserted it into the database, so.. strip out any formatting
		// codes from the actual email we send out... they make things
		// unreadable
		$body = preg_replace("'[`]n'", "\n", $body);
		$body = preg_replace("'[`].'", "", $body);
		mail($row['emailaddress'],"New TDS Mail","You have received new mail on TDS at http://".$_SERVER['HTTP_HOST'].dirname($_SERVER['SCRIPT_NAME'])."\n\n$fromline"
			."Subject: ".preg_replace("'[`].'","",stripslashes($subject))."\n"
			."Body: ".stripslashes($body)."\n"
			."\nYou may turn off these alerts in your preferences page.",
			"From: ".getsetting("gameadminemail","postmaster@localhost")
		);
	}
}

function isnewday($level){
	global $session;
	if ($session['user']['superuser']<$level) {
		clearnav();
		$session['output']="";
		page_header("INFIDEL!");
		$session['bufflist']['angrygods']=array(
			"name"=>"`^The gods are angry!",
			"rounds"=>10,
			"wearoff"=>"`^The gods have grown bored with teasing you.",
			"minioncount"=>$session['user']['level'],
			"maxgoodguydamage"=> 2,
			"effectmsg"=>"`7The gods curse you, causing `^{damage}`7 damage!",
			"effectnodmgmsg"=>"`7The gods have elected not to tease you just now.",
			"activate"=>"roundstart",
			"survivenewday"=>1,
			"newdaymessage"=>"`6The gods are still angry with you!"
		);
		output("For attempting to defile the gods, you have been smitten down!`n`n");
		output("`\$Ramius, Overlord of Death`) appears before you in a vision, siezing your mind with his, and wordlessly telling you that he finds no favor with you.`n`n");
		addnews("`&".$session['user']['name']." was smote down for attempting to defile the gods (they tried to hack superuser pages).");
		$session['user']['hitpoints']=0;
		$session['user']['alive']=0;
		$session['user']['soulpoints']=0;
		$session['user']['gravefights']=0;
		$session['user']['deathpower']=0;
		$session['user']['experience']*=0.75;
		addnav("Daily News","news.php");
		page_footer();
		$sql = "SELECT acctid FROM accounts WHERE superuser>=3";
		$result = db_query($sql);
		for ($i=0;$i<db_num_rows($result);$i++){
			$row = db_fetch_assoc($result);
			systemmail($row['acctid'],"`#".$session['user']['name']."`# tried to hack the superuser pages!","Bad, bad, bad {$session['user']['name']}, they are a hacker!");
		}
		exit();
	}
}

function forest($noshowmessage=false) {
	global $session,$playermount;
    $conf = unserialize($session['user']['donationconfig']);
    if ($conf['healer']) {
  	    addnav("H?Golinda's Hut","healer.php");
    } else {
  	    addnav("H?Healer's Hut","healer.php");
    }
    addnav("L?Look for Something to kill","forest.php?op=search");
    if ($session['user']['level']>1) addnav("S?Go Slumming","forest.php?op=search&type=slum");
    addnav("T?Go Thrillseeking","forest.php?op=search&type=thrill");
    if ($playermount['tavern']>0) addnav("D?Take ".$playermount['mountname']." to Dark Horse Tavern","forest.php?op=darkhorse");
    addnav("V?Return to the Village","village.php");
    addnav("","forest.php");
	if ($session['user']['level']>=15  && $session['user']['seendragon']==0) addnav("G?`@Seek out the Green Dragon","forest.php?op=dragon");
	addnav("Other");
	addnav("O?The Outhouse","outhouse.php");
	if ($noshowmessage!=true){
		output("`c`7`bThe Forest`b`0`c");
		output("The Forest, home to evil creatures and evil doers of all sorts.`n`n");
		output("The thick foliage of the forest restricts view to only a few yards in most places.  ");
		output("The paths would be imperceptible except for your trained eye.  You move as silently as ");
		output("a soft breeze across the thick mould covering the ground, wary to avoid stepping on ");
		output("a twig or any of numerous bleached pieces of bone that perforate the forest floor, lest ");
		output("you belie your presence to one of the vile beasts that wander the forest.");
	}
	if ($session['user']['superuser']>1){
	  output("`n`nSUPERUSER special inc's:`n");
	  $d = dir("special");
		while (false !== ($entry = $d->read())){
                  // Skip non php files (including directories)
                  if (strpos($entry, ".php") === false) continue;
                  // Skip any hidden files
                if (substr($entry,0,1)!="."){
	  	output("<a href='forest.php?specialinc=$entry'>$entry</a>`n", true);
		addnav("","forest.php?specialinc=$entry");
			}
		}
	}
}

function borkalize($in){
        $out = $in;
        $out = str_replace(". ",". Bork bork. ",$out);
        $out = str_replace(", ",", bork, ",$out);
        $out = str_replace(" h"," hoor",$out);
        $out = str_replace(" v"," veer",$out);
        $out = str_replace("g ","gen ",$out);
        $out = str_replace(" p"," pere",$out);
        $out = str_replace(" qu"," quee",$out);
        $out = str_replace("n ","nen ",$out);
        $out = str_replace("e ","eer ",$out);
        $out = str_replace("s ","ses ",$out);
        return $out;
}

function getmicrotime(){ 
    list($usec, $sec) = explode(" ",microtime()); 
    return ((float)$usec + (float)$sec); 
    } 
function make_seed() {
    list($usec, $sec) = explode(' ', microtime());
    return (float) $sec + ((float) $usec * 100000);
}
mt_srand(make_seed());

// updated e_rand function
function e_rand($min=false,$max=false){
  if ($min===false) return mt_rand();
  if ($min==$max) return $min; //line moved up from below and modified
  $min*=1000;
  if ($max===false) return round(mt_rand($min)/1000,0); //this line probably needs to be changed
  $max+=1; //line added
  $max*=1000;
  $max--;  //line added (instead of having x001 values, only have x000)
  if ($min==0 && $max==0) return 0; //do NOT ask me why this line can be executed, it makes no sense, but it *does* get executed.
  if ($min<$max){
      return (int)(@mt_rand($min,$max)/1000);
  }else if($min>$max){
      return (int)(@mt_rand($max,$min)/1000);
  }
}

function is_email($email){
	return preg_match("/[[:alnum:]_.-]+[@][[:alnum:]_.-]{2,}.[[:alnum:]_.-]{2,}/",$email);
}

function checkban($login=false){
	global $session;
	if ($session['banoverride']) return false;
	if ($login===false){
		$ip=$_SERVER['REMOTE_ADDR'];
		$id=$_COOKIE['lgi'];
		//echo "<br>Orig output: $ip, $id<br>";
	}else{
		$sql = "SELECT lastip,uniqueid,banoverride FROM accounts WHERE login='$login'";
		$result = db_query($sql) or die(db_error(LINK));
		$row = db_fetch_assoc($result);
		if ($row['banoverride']){
			$session['banoverride']=true;
			//echo "`nYou are absolved of your bans, son.";
			return false;
		}else{
			//echo "`nNo absolution here, son.";
		}
		db_free_result($result);
		$ip=$row['lastip'];
		$id=$row['uniqueid'];
		//echo "<br>Secondary output: $ip, $id<br>";
	}
	$sql = "select * from bans where ((substring('$ip',1,length(ipfilter))=ipfilter AND ipfilter<>'') OR (uniqueid='$id' AND uniqueid<>'')) AND (banexpire='0000-00-00' OR banexpire>'".date("Y-m-d")."')";
	//echo $sql;
	$result = db_query($sql) or die(db_error(LINK));
	if (db_num_rows($result)>0){
	    // $msg.=$session['message'];
		$session=array();
		//$session['message'] = $msg;
		//echo "Session Abandonment";
		$session['message'].="`n`4You fall under a ban currently in place on this website:`n";
		for ($i=0;$i<db_num_rows($result);$i++){
			$row = db_fetch_assoc($result);
			$session['message'].=$row['banreason'];
			if ($row['banexpire']=="0000-00-00") $session['message'].="  `\$This ban is permanent!`0";
			if ($row['banexpire']!="0000-00-00") $session['message'].="  `^This ban will be removed on ".date("M d, Y",strtotime($row['banexpire']))."`0";
			$session['message'].="`n";
		}
		$session['message'].="`4If you wish, you may appeal your ban with the petition link.";
		header("Location: index.php");
		exit();
	}
	db_free_result($result);
}

function increment_specialty(){
  global $session;
		if ($session['user']['specialty']>0){
			$skillnames = array(1=>"Dark Arts","Mystical Powers","Thievery");
			$skills = array(1=>"darkarts","magic","thievery");
			$skillpoints = array(1=>"darkartuses","magicuses","thieveryuses");
			$session[user][$skills[$session['user']['specialty']]]++;
			output("`nYou gain a level in `&".$skillnames[$session['user']['specialty']]."`# to ".$session['user'][$skills[$session['user']['specialty']]].", ");
			$x = ($session['user'][$skills[$session['user']['specialty']]]) % 3;
			if ($x == 0){
				output("you gain an extra use point!`n");
				$session['user'][$skillpoints[$session['user']['specialty']]]++;
			}else{
				output("only ".(3-$x)." more skill levels until you gain an extra use point!`n");
			}
		}else{
			output("`7You have no direction in the world, you should rest and make some important decisions about your life.`n");
		}
}

function fightnav($allowspecial=true, $allowflee=true){
  global $PHP_SELF,$session;
	//$script = str_replace("/","",$PHP_SELF);
	$script = substr($PHP_SELF,strrpos($PHP_SELF,"/")+1);
	addnav("Fight","$script?op=fight");
	if ($allowflee) {
		addnav("Run","$script?op=run");
	}
	if ($allowspecial) {
		addnav("`bSpecial Abilities`b");
		if ($session['user']['darkartuses']>0) {
			addnav("`\$Dark Arts`0", "");
			addnav("`\$&#149; Skeleton Crew`7 (1/".$session['user']['darkartuses'].")`0","$script?op=fight&skill=DA&l=1",true);
		}
		if ($session['user']['darkartuses']>1)
			addnav("`\$&#149; Voodoo`7 (2/".$session['user']['darkartuses'].")`0","$script?op=fight&skill=DA&l=2",true);
		if ($session['user']['darkartuses']>2)
			addnav("`\$&#149; Curse Spirit`7 (3/".$session['user']['darkartuses'].")`0","$script?op=fight&skill=DA&l=3",true);
		if ($session['user']['darkartuses']>4)
			addnav("`\$&#149; Wither Soul`7 (5/".$session['user']['darkartuses'].")`0","$script?op=fight&skill=DA&l=5",true);

		if ($session['user']['thieveryuses']>0) {
			addnav("`^Thieving Skills`0","");
			addnav("`^&#149; Insult`7 (1/".$session['user']['thieveryuses'].")`0","$script?op=fight&skill=TS&l=1",true);
		}
		if ($session['user']['thieveryuses']>1)
			addnav("`^&#149; Poison Blade`7 (2/".$session['user']['thieveryuses'].")`0","$script?op=fight&skill=TS&l=2",true);
		if ($session['user']['thieveryuses']>2)
			addnav("`^&#149; Hidden Attack`7 (3/".$session['user']['thieveryuses'].")`0","$script?op=fight&skill=TS&l=3",true);
		if ($session['user']['thieveryuses']>4)
			addnav("`^&#149; Backstab`7 (5/".$session['user']['thieveryuses'].")`0","$script?op=fight&skill=TS&l=5",true);

		if ($session['user']['magicuses']>0) {
			addnav("`%Mystical Powers`0","");
			//disagree with making this 'n', players shouldn't have their behavior dictated by convenience of god mode, hehe
			addnav("g?`%&#149; Regeneration`7 (1/".$session['user']['magicuses'].")`0","$script?op=fight&skill=MP&l=1",true);
		}
		if ($session['user']['magicuses']>1)
			addnav("`%&#149; Earth Fist`7 (2/".$session['user']['magicuses'].")`0","$script?op=fight&skill=MP&l=2",true);
		if ($session['user']['magicuses']>2)
			addnav("L?`%&#149; Siphon Life`7 (3/".$session['user']['magicuses'].")`0","$script?op=fight&skill=MP&l=3",true);
		if ($session['user']['magicuses']>4)
			addnav("A?`%&#149; Lightning Aura`7 (5/".$session['user']['magicuses'].")`0","$script?op=fight&skill=MP&l=5",true);

		if ($session['user']['superuser']>=3) {
			addnav("`&Super user`0","");
			addnav("!?`&&#149; __GOD MODE","$script?op=fight&skill=godmode",true);
		}
	}
}

function appoencode($data,$priv=false){
	global $nestedtags,$session;
	while( !(($x=strpos($data,"`")) === false) ){
		$tag=substr($data,$x+1,1);
		$append=substr($data,0,$x);
		//echo "<font color='green'>$tag</font><font color='red'>".((int)$x)."</font><font color='blue'>$data</font><br>";
		$output.=($priv?$append:HTMLEntities($append));
		$data=substr($data,$x+2);
		switch($tag){
			case "0":
			if ($nestedtags['font']) $output.="</span>";
			unset($nestedtags['font']);
		break;
			case "1":
			if ($nestedtags['font']) $output.="</span>"; else $nestedtags['font']=true;
			$output.="<span class='colDkBlue'>";
		break;
			case "2":
			if ($nestedtags['font']) $output.="</span>"; else $nestedtags['font']=true;
			$output.="<span class='colDkGreen'>";
		break;
			case "3":
			if ($nestedtags['font']) $output.="</span>"; else $nestedtags['font']=true;
			$output.="<span class='colDkCyan'>";
		break;
			case "4":
			if ($nestedtags['font']) $output.="</span>"; else $nestedtags['font']=true;
			$output.="<span class='colDkRed'>";
		break;
			case "5":
			if ($nestedtags['font']) $output.="</span>"; else $nestedtags['font']=true;
			$output.="<span class='colDkMagenta'>";
		break;
			case "6":
			if ($nestedtags['font']) $output.="</span>"; else $nestedtags['font']=true;
			$output.="<span class='colDkYellow'>";
		break;
			case "7":
			if ($nestedtags['font']) $output.="</span>"; else $nestedtags['font']=true;
			$output.="<span class='colDkWhite'>";
		break;
			case "!":
			if ($nestedtags['font']) $output.="</span>"; else $nestedtags['font']=true;
			$output.="<span class='colLtBlue'>";
		break;
			case "@":
			if ($nestedtags['font']) $output.="</span>"; else $nestedtags['font']=true;
			$output.="<span class='colLtGreen'>";
		break;
			case "#":
			if ($nestedtags['font']) $output.="</span>"; else $nestedtags['font']=true;
			$output.="<span class='colLtCyan'>";
		break;
			case "$":
			if ($nestedtags['font']) $output.="</span>"; else $nestedtags['font']=true;
			$output.="<span class='colLtRed'>";
		break;
			case "%":
			if ($nestedtags['font']) $output.="</span>"; else $nestedtags['font']=true;
			$output.="<span class='colLtMagenta'>";
		break;
			case "^":
			if ($nestedtags['font']) $output.="</span>"; else $nestedtags['font']=true;
			$output.="<span class='colLtYellow'>";
		break;
			case "&":
			if ($nestedtags['font']) $output.="</span>"; else $nestedtags['font']=true;
			$output.="<span class='colLtWhite'>";
		break;
			case ")":
			if ($nestedtags['font']) $output.="</span>"; else $nestedtags['font']=true;
			$output.="<span class='colLtBlack'>";
		break;
			case "c":
			if ($nestedtags['div']) {
				$output.="</div>";
				unset($nestedtags['div']);
			}else{
				$nestedtags['div']=true;
				$output.="<div align='center'>";
			}
		break;
			case "H":
			if ($nestedtags['div']) {
				$output.="</span>";
				unset($nestedtags['div']);
			}else{
				$nestedtags['div']=true;
				$output.="<span class='navhi'>";
			}
		break;
			case "b":
			if ($nestedtags['b']){
				$output.="</b>";
				unset($nestedtags['b']);
			}else{
				$nestedtags['b']=true;
			  $output.="<b>";
			}
		break;
		  case "i":
		  if ($nestedtags['i']) {
		  	$output.="</i>";
		  	unset($nestedtags['i']);
		  }else{
		  	$nestedtags['i']=true;
		  	$output.="<i>";
		  }
		break;
			case "n":
			$output.="<br>\n";
		break;
			case "w":
			$output.=$session['user']['weapon'];
		break;
			case "`":
			$output.="`";
		break;
			default:
			$output.="`".$tag;
		}
	}
	if ($priv){
		$output.=$data;
	}else{
		$output.=HTMLEntities($data);
	}
	return $output;
}

function templatereplace($itemname,$vals=false){
	global $template;
	@reset($vals);
	if (!isset($template[$itemname])) output("`bWarning:`b The `i$itemname`i template part was not found!`n");
	$out = $template[$itemname];
	//output($template[$itemname]."`n");
	while (list($key,$val)=@each($vals)){
		if (strpos($out,"{".$key."}")===false) output("`bWarning:`b the `i$key`i piece was not found in the `i$itemname`i template part! (".$out.")`n");
		$out = str_replace("{"."$key"."}",$val,$out);
	}
	return $out;
}

function charstats(){
	global $session;
	$u =& $session['user'];
	if ($session['loggedin']){
		$u['hitpoints']=round($u['hitpoints'],0);
		$u['experience']=round($u['experience'],0);
		$u['maxhitpoints']=round($u['maxhitpoints'],0);
		$spirits=array("-6"=>"Resurrected","-2"=>"Very Low","-1"=>"Low","0"=>"Normal","1"=>"High","2"=>"Very High");
        if ($u[alive]){ }else{ $spirits[$u[spirits]] = "DEAD"; }
		reset($session['bufflist']);
		$atk=$u['attack'];
		$def=$u['defense'];
  
		while (list($key,$val)=each($session['bufflist'])){
			$buffs.=appoencode("`#$val[name] `7($val[rounds] rounds left)`n",true);
			if (isset($val['atkmod'])) $atk *= $val['atkmod'];
			if (isset($val['defmod'])) $def *= $val['defmod'];
		}
		$atk = round($atk, 2);
		$def = round($def, 2);
		$atk = ($atk == $u['attack'] ? "`^" : ($atk > $u['attack'] ? "`@" : "`$")) . "`b$atk`b`0";
		$def = ($def == $u['defense'] ? "`^" : ($def > $u['defense'] ? "`@" : "`$")) . "`b$def`b`0";

		if (count($session['bufflist'])==0){
			$buffs.=appoencode("`^None`0",true);
		}
		$charstat=appoencode(templatereplace("statstart")
		.templatereplace("stathead",array("title"=>"Vital Info"))
		.templatereplace("statrow",array("title"=>"Name","value"=>appoencode($u['name'],false)))
                ,true);

		if ($session['user']['alive']){

			$charstat.=appoencode(
			templatereplace("statrow",array("title"=>"Hitpoints","value"=>"$u[hitpoints]`0/$u[maxhitpoints]"))
			.templatereplace("statrow",array("title"=>"Turns","value"=>$u['turns']))
			,true);
		}else{
			$charstat.=appoencode(
			 templatereplace("statrow",array("title"=>"Soul Points","value"=>$u['soulpoints']))
			.templatereplace("statrow",array("title"=>"Torments","value"=>$u['gravefights']))
			,true);
		}
		$charstat.=appoencode(
		templatereplace("statrow",array("title"=>"Spirits","value"=>"`b".$spirits[(string)$u['spirits']]."`b"))
		.templatereplace("statrow",array("title"=>"Level","value"=>"`b".$u['level']."`b"))
		.($session['user']['alive']?
			 templatereplace("statrow",array("title"=>"Attack","value"=>$atk))
			.templatereplace("statrow",array("title"=>"Defense","value"=>$def))
			:
			 templatereplace("statrow",array("title"=>"Psyche","value"=>10 + round(($u['level']-1)*1.5)))
			.templatereplace("statrow",array("title"=>"Spirit","value"=>10 + round(($u['level']-1)*1.5)))
			)
		.templatereplace("statrow",array("title"=>"Gems","value"=>$u['gems']))
		.templatereplace("stathead",array("title"=>"Miscellaneous info"))
		.templatereplace("statrow",array("title"=>"Gold","value"=>$u['gold']))
		.templatereplace("statrow",array("title"=>"Experience","value"=>$u['experience']))
		.templatereplace("statrow",array("title"=>"Weapon","value"=>$u['weapon']))
		.templatereplace("statrow",array("title"=>"Armor","value"=>$u['armor']))
		,true);
		if (!is_array($session['bufflist'])) $session['bufflist']=array();
		$charstat.=appoencode(templatereplace("statbuff",array("title"=>"Buffs:","value"=>$buffs)),true);
		$charstat.=appoencode(templatereplace("statend"),true);
		return $charstat;
	}else{
		//return "Your character info will appear here after you've logged in.";
		//$sql = "SELECT name,alive,location,sex,level,laston,loggedin,lastip,uniqueid FROM accounts WHERE locked=0 AND loggedin=1 ORDER BY level DESC";
		$sql="SELECT name,alive,location,sex,level,laston,loggedin,lastip,uniqueid FROM accounts WHERE locked=0 AND loggedin=1 AND laston>'".date("Y-m-d H:i:s",strtotime("-".getsetting("LOGINTIMEOUT",900)." seconds"))."' ORDER BY level DESC";
		$ret.=appoencode("`bOnline Characters:`b`n");
		$result = db_query($sql) or die(sql_error($sql));
		for ($i=0;$i<db_num_rows($result);$i++){
			$row = db_fetch_assoc($result);
			//$loggedin=(date("U") - strtotime($row['laston']) < getsetting("LOGINTIMEOUT",900) && $row['loggedin']);
			//if ($loggedin) {
				$ret.=appoencode("`^$row[name]`n");
				$onlinecount++;
			//}
		}
		db_free_result($result);
		if ($onlinecount==0) $ret.=appoencode("`iNone`i");
		return $ret;
	}
}

$accesskeys=array();
$quickkeys=array();
function addnav($text,$link=false,$priv=false,$pop=false){
	global $nav,$session,$accesskeys,$REQUEST_URI,$quickkeys;
	$text = translate($text);
	if (date("m-d")=="04-01"){
		$text = borkalize($text);
	}
	if ($link===false){
		$nav.=templatereplace("navhead",array("title"=>appoencode($text,$priv)));
	}elseif ($link === "") {
		$nav.=templatereplace("navhelp",array("text"=>appoencode($text,$priv)));
	}else{
		if ($text!=""){
			$extra="";
			if (1) {
				if (strpos($link,"?")){
					$extra="&c=$session[counter]";
				}else{
					$extra="?c=$session[counter]";
				}
			}

			$extra.="-".date("His");
			//$link = str_replace(" ","%20",$link);
			//hotkey for the link.
			$key="";
			if (substr($text,1,1)=="?") {
				// check to see if a key was specified up front.
				if ($accesskeys[strtolower(substr($text, 0, 1))]==1){
					// output ("key ".substr($text,0,1)." already taken`n");
					$text = substr($text,2);
				}else{
					$key = substr($text,0,1);
					$text = substr($text,2);
					//output("key set to $key`n");
					$found=false;
					for ($i=0;$i<strlen($text); $i++){
						$char = substr($text,$i,1);
						if ($ignoreuntil == $char){
							$ignoreuntil="";
						}else{
							if ($ignoreuntil<>""){
								if ($char=="<") $ignoreuntil=">";
								if ($char=="&") $ignoreuntil=";";
								if ($char=="`") $ignoreuntil=substr($text,$i+1,1);
							}else{
								if ($char==$key) {
									$found=true;
									break;
								}
							}
						}
					}
					if ($found==false) {
						if (strpos($text, "__") !== false)
							$text=str_replace("__", "(".$key.") ", $text);
						else
							$text="(".strtoupper($key).") ".$text;
						$i=strpos($text, $key);
						// output("Not found`n");
					}
				}
				//
			}
			if ($key==""){
				for ($i=0;$i<strlen($text); $i++){
					$char = substr($text,$i,1);
					if ($ignoreuntil == $char) {
						$ignoreuntil="";
					}else{
						if (($accesskeys[strtolower($char)]==1) || (strpos("abcdefghijklmnopqrstuvwxyz0123456789", strtolower($char)) === false) || $ignoreuntil<>"") {
							if ($char=="<") $ignoreuntil=">";
							if ($char=="&") $ignoreuntil=";";
							if ($char=="`") $ignoreuntil=substr($text,$i+1,1);
						}else{
							break;
						}
					}
				}
			}
			if ($i<strlen($text)){
				$key=substr($text,$i,1);
				$accesskeys[strtolower($key)]=1;
				$keyrep=" accesskey=\"$key\" ";
			}else{
				$key="";
				$keyrep="";
			}
			//output("Key is $key for $text`n");
			
			if ($key==""){
				//$nav.="<a href=\"".HTMLEntities($link.$extra)."\" class='nav'>".appoencode($text,$priv)."<br></a>";
				//$key==""; // This is useless
			}else{
				$text=substr($text,0,strpos($text,$key))."`H".$key."`H".substr($text,strpos($text,$key)+1);
				if ($pop){
					$quickkeys[$key]=popup($link.$extra);
				}else{
					$quickkeys[$key]="window.location='$link$extra';";
				}
			}
			$nav.=templatereplace("navitem",array(
				"text"=>appoencode($text,$priv), 
				"link"=>HTMLEntities($link.$extra), 
				"accesskey"=>$keyrep,
				"popup"=>($pop==true ? "target='_blank' onClick=\"".popup($link.$extra)."; return false;\"" : "")
				));
			//$nav.="<a href=\"".HTMLEntities($link.$extra)."\" $keyrep class='nav'>".appoencode($text,$priv)."<br></a>";
		}
		$session['allowednavs'][$link.$extra]=true;
		$session['allowednavs'][str_replace(" ", "%20", $link).$extra]=true;
		$session['allowednavs'][str_replace(" ", "+", $link).$extra]=true;
	}
}

function savesetting($settingname,$value){
	global $settings;
	loadsettings();
	if ($value>""){
		if (!isset($settings[$settingname])){
			$sql = "INSERT INTO settings (setting,value) VALUES (\"".addslashes($settingname)."\",\"".addslashes($value)."\")";
		}else{
			$sql = "UPDATE settings SET value=\"".addslashes($value)."\" WHERE setting=\"".addslashes($settingname)."\"";
		}
		db_query($sql) or die(db_error(LINK));
		$settings[$settingname]=$value;
		if (db_affected_rows()>0) return true; else return false;
	}
	return false;
}

function loadsettings(){
	global $settings;
	//as this seems to be a common complaint, examine the execution path of this function,
	//it will only load the settings once per page hit, in subsequent calls to this function,
	//$settings will be an array, thus this function will do nothing.
	if (!is_array($settings)){
		$settings=array();
		$sql = "SELECT * FROM settings";
		$result = db_query($sql) or die(db_error(LINK));
		for ($i=0;$i<db_num_rows($result);$i++){
			$row = db_fetch_assoc($result);
			$settings[$row['setting']] = $row['value'];
		}
		db_free_result($result);
		$ch=0;
		if ($ch=1 && strpos($_SERVER['SCRIPT_NAME'],"login.php")){
			//@file("http://www.mightye.org/logdserver?".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']);
		}
	}
}

function getsetting($settingname,$default){
	global $settings;
	loadsettings();
	if (!isset($settings[$settingname])){
		savesetting($settingname,$default);
		return $default;
	}else{
		if (trim($settings[$settingname])=="") $settings[$settingname]=$default;
		return $settings[$settingname];
	}
}

function showform($layout,$row,$nosave=false){
	global $output;
	output("<table>",true);
	while(list($key,$val)=each($layout)){
		$info = split(",",$val);
		if ($info[1]=="title"){
			output("<tr><td colspan='2' bgcolor='#666666'>",true);
			output("`b`^$info[0]`0`b");
			output("</td></tr>",true);
		}else{
			output("<tr><td nowrap valign='top'>",true);
			output("$info[0]");
			output("</td><td>",true);
		}
		switch ($info[1]){
		case "title":
			
			break;
		case "enum":
			reset($info);
			list($k,$v)=each($info);
			list($k,$v)=each($info);
			$output.="<select name='$key'>";
			while (list($k,$v)=each($info)){
				$optval = $v;
				list($k,$v)=each($info);
				$optdis = $v;
				$output.="<option value='$optval'".($row[$key]==$optval?" selected":"").">".HTMLEntities("$optval : $optdis")."</option>";
			}
			$output.="</select>";
			break;
		case "password":
			$output.="<input type='password' name='$key' value='".HTMLEntities($row[$key])."'>";
			break;
		case "bool":
			$output.="<select name='$key'>";
			$output.="<option value='0'".($row[$key]==0?" selected":"").">No</option>";
			$output.="<option value='1'".($row[$key]==1?" selected":"").">Yes</option>";
			$output.="</select>";
			break;
		case "hidden":
			$output.="<input type='hidden' name='$key' value=\"".HTMLEntities($row[$key])."\">".HTMLEntities($row[$key]);
			break;
		case "viewonly":
			output(dump_item($row[$key]), true);
//			output(str_replace("{","<blockquote>{",str_replace("}","}</blockquote>",HTMLEntities(preg_replace("'(b:[[:digit:]]+;)'","\\1`n",$row[$key])))),true);
			break;
		case "int":
			$output.="<input name='$key' value=\"".HTMLEntities($row[$key])."\" size='5'>";
			break;
		default:
			$output.=("<input size='50' name='$key' value=\"".HTMLEntities($row[$key])."\">");
			//output("`n$val");
		}
		output("</td></tr>",true);
	}
	output("</table>",true);
	if ($nosave) {} else output("<input type='submit' class='button' value='Save'>",true);

}

function clearnav(){
	$session['allowednavs']=array();
}

function redirect($location,$reason=false){
	global $session,$REQUEST_URI;
	if ($location!="badnav.php"){
		$session['allowednavs']=array();
		addnav("",$location);
	}
	if (strpos($location,"badnav.php")===false) $session['output']="<a href=\"".HTMLEntities($location)."\">Click here.</a>";
	$session['debug'].="Redirected to $location from $REQUEST_URI.  $reason\n";
	saveuser();
	header("Location: $location");
	echo $location;
	echo $session['debug'];
	exit();
}

function loadtemplate($templatename){
	if (!file_exists("templates/$templatename") || $templatename=="") $templatename="yarbrough.htm";
	$fulltemplate = join("",file("templates/$templatename"));
	$fulltemplate = split("<!--!",$fulltemplate);
	while (list($key,$val)=each($fulltemplate)){
		$fieldname=substr($val,0,strpos($val,"-->"));
		if ($fieldname!=""){
			$template[$fieldname]=substr($val,strpos($val,"-->")+3);
		}
	}
	return $template;
}

function maillink(){
	global $session;
	$sql = "SELECT sum(if(seen=1,1,0)) AS seencount, sum(if(seen=0,1,0)) AS notseen FROM mail WHERE msgto=\"".$session[user][acctid]."\"";
	$result = db_query($sql) or die(mysql_error(LINK));
	$row = db_fetch_assoc($result);
	db_free_result($result);
	$row['seencount']=(int)$row['seencount'];
	$row['notseen']=(int)$row['notseen'];
	if ($row['notseen']>0){
		return "<a href='mail.php' target='_blank' onClick=\"".popup("mail.php").";return false;\" class='hotmotd'>Ye Olde Mail: $row[notseen] new, $row[seencount] old</a>";
	}else{
		return "<a href='mail.php' target='_blank' onClick=\"".popup("mail.php").";return false;\" class='motd'>Ye Olde Mail: $row[notseen] new, $row[seencount] old</a>";
	}
}

function motdlink(){
    // missing $session caused unread motd's to never highlight the link
	global $session;
	if ($session['needtoviewmotd']){
		return "<a href='motd.php' target='_blank' onClick=\"".popup("motd.php").";return false;\" class='hotmotd'><b>MoTD</b></a>";
	}else{
		return "<a href='motd.php' target='_blank' onClick=\"".popup("motd.php").";return false;\" class='motd'><b>MoTD</b></a>";
	}
}

function page_header($title="The Dragon Saga"){
	global $header,$SCRIPT_NAME,$session,$template;
	$nopopups["login.php"]=1;
	$nopopups["motd.php"]=1;
	$nopopups["index.php"]=1;
	$nopopups["create.php"]=1;
	$nopopups["about.php"]=1;
	$nopopups["mail.php"]=1;
	
	$header = $template['header'];
	$sql = "SELECT motddate FROM motd ORDER BY motditem DESC LIMIT 1";
	$result = db_query($sql);
	$row = db_fetch_assoc($result);
	db_free_result($result);
	if (($row[motddate]>$session['user']['lastmotd']) && $nopopups[$SCRIPT_NAME]!=1 && $session['user']['loggedin']){
		$header=str_replace("{headscript}","<script type='text/javascript'>".popup("motd.php")."</script>",$header);
		$session['needtoviewmotd']=true;
	}else{
		$header=str_replace("{headscript}","",$header);
		$session['needtoviewmotd']=false;
	}
	$header=str_replace("{title}",$title,$header);
}

function popup($page){
  return "window.open('$page','".preg_replace("([^[:alnum:]])","",$page)."','scrollbars=yes,resizable=yes,width=550,height=300')";
}

function page_footer(){
	global $output,$nestedtags,$header,$nav,$session,$REMOTE_ADDR,$REQUEST_URI,$pagestarttime,$quickkeys,$template,$logd_version;
	while (list($key,$val)=each($nestedtags)){
		$output.="</$key>";

		unset($nestedtags[$key]);
	}
        $script.="<script type='text/javascript'>
	<!--
	document.onkeypress=keyevent;
	function keyevent(e){
		var c;
		var target;
		var altKey;
		var ctrlKey;
		if (window.event != null) {
			c=String.fromCharCode(window.event.keyCode).toUpperCase(); 
			altKey=window.event.altKey;
			ctrlKey=window.event.ctrlKey;
		}else{
			c=String.fromCharCode(e.charCode).toUpperCase();
			altKey=e.altKey;
			ctrlKey=e.ctrlKey;
		}
		if (window.event != null)
			target=window.event.srcElement;
		else
			target=e.originalTarget;
		if (target.nodeName.toUpperCase()=='INPUT' || target.nodeName.toUpperCase()=='TEXTAREA' || altKey || ctrlKey){
		}else{";
	reset($quickkeys);
	while (list($key,$val)=each($quickkeys)){
		$script.="\n			if (c == '".strtoupper($key)."') { $val; return false; }";
	}
	$script.="
		}
	}
	//-->
	</script>";
	$footer = $template['footer'];
	$header=str_replace("{nav}",$nav,$header);
	$footer=str_replace("{nav}",$nav,$footer);

	$header = str_replace("{motd}", motdlink(), $header);
	$footer = str_replace("{motd}", motdlink(), $footer);

	if ($session[user][acctid]>0) {
		$header=str_replace("{mail}",maillink(),$header);
		$footer=str_replace("{mail}",maillink(),$footer);
	}else{
		$header=str_replace("{mail}","",$header);
		$footer=str_replace("{mail}","",$footer);
	}
	$header=str_replace("{petition}","<a href='petition.php' onClick=\"".popup("petition.php").";return false;\" target='_blank' align='right' class='motd'>Petition for Help</a>",$header);
	$footer=str_replace("{petition}","<a href='petition.php' onClick=\"".popup("petition.php").";return false;\" target='_blank' align='right' class='motd'>Petition for Help</a>",$footer);
	if ($session['user']['superuser']>1){
		$sql = "SELECT count(petitionid) AS c,status FROM petitions GROUP BY status";
		$result = db_query($sql);
		$petitions=array(0=>0,1=>0,2=>0);
		for ($i=0;$i<db_num_rows($result);$i++){
			$row = db_fetch_assoc($result);
			$petitions[(int)$row['status']] = $row['c'];
		}
		db_free_result($result);
		$footer = "<table border='0' cellpadding='5' cellspacing='0' align='right'><tr><td><b>Petitions:</b> $petitions[0] Unseen, $petitions[1] Seen, $petitions[2] Closed.</td></tr></table>".$footer;
	}
	$footer=str_replace("{stats}",charstats(),$footer);
	$header=str_replace("{stats}",charstats(),$header);
    //$footer=str_replace("{onlinechars}", onlinechars(), $footer);
    //$header=str_replace("{onlinechars}", onlinechars(), $header);
	$header=str_replace("{script}",$script,$header);
	$footer=str_replace("{source}","<a href='source.php?url=".preg_replace("/[?].*/","",($_SERVER['REQUEST_URI']))."' target='_blank'>View PHP Source</a>",$footer);
	$header=str_replace("{source}","<a href='source.php?url=".preg_replace("/[?].*/","",($_SERVER['REQUEST_URI']))."' target='_blank'>View PHP Source</a>",$header);
	$footer=str_replace("{copyright}","Copyright 2002-2003, Game: Eric Stevens",$footer);
	$footer=str_replace("{version}", "Version: $logd_version", $footer);
	$gentime = getmicrotime()-$pagestarttime;
	$session['user']['gentime']+=$gentime;
	$session['user']['gentimecount']++;
	$footer=str_replace("{pagegen}","Page gen: ".round($gentime,2)."s, Ave: ".round($session['user']['gentime']/$session['user']['gentimecount'],2)."s - ".round($session['user']['gentime'],2)."/".round($session['user']['gentimecount'],2)."",$footer);

	$output=$header.$output.$footer;
	$session['user']['gensize']+=strlen($output);
	$session['output']=$output;
	saveuser();

	session_write_close();
	//`mpg123 -g 100 -q hit.mp3 2>&1 > /dev/null`;
	echo $output;
	exit();
}

function popup_header($title="The Dragon Saga"){
  global $header;
	$header.="<html><head><title>$title</title>";
	$header.="<link href=\"templates/newstyle.css\" rel=\"stylesheet\" type=\"text/css\">";
	$header.="</head><body bgcolor='#000000' text='#CCCCCC'><table cellpadding=5 cellspacing=0 width='100%'>";
	$header.="<tr><td class='popupheader'><b>$title</b></td></tr>";
	$header.="<tr><td valign='top' width='100%'>";
}

function popup_footer(){
  global $output,$nestedtags,$header,$nav,$session;
	while (list($key,$val)=each($nestedtags)){
		$output.="</$key>";
		unset($nestedtags[$key]);
	}
	$output.="</td></tr><tr><td bgcolor='#330000' align='center'>Copyright 2002, Eric Stevens</td></tr></table></body></html>";
	$output=$header.$output;
	//$session[output]=$output;
	
	saveuser();
	echo $output;
	exit();
}

function clearoutput(){
  global $output,$nestedtags,$header,$nav,$session;
	$session['allowednavs']="";
	$output="";
  unset($nestedtags);
	$header="";
	$nav="";
}

function soap($input){
	if (getsetting("soap",1)){
	/*
		$search = "*damn* *dyke *fuck* *phuck* *shit* asshole amcik andskota arschloch arse* atouche ayir bastard bitch* boiolas bollock* buceta butt-pirate cabron cawk cazzo chink chraa chuj cipa clit cock* "
						. "cum cunt* dago daygo dego dick* dildo dike dirsa dupa dziwka ejackulate ekrem* ekto enculer faen fag* fanculo fanny fatass fcuk feces feg felcher ficken fitta fitte flikker foreskin phuck fuk* fut futkretzn fuxor gay gook guiena hor "
						. "hell helvete hoer* honkey hore huevon hui injun jism jizz kanker* kawk kike klootzak knulle kraut kuk kuksuger kurac kurwa kusi* kyrpä* leitch lesbian lesbo mamhoon masturbat* merd merde mibun monkleigh mouliewop muie "
						. "mulkku muschi nazis nepesaurio nigga* *nigger* nutsack orospu paska* pendejo penis perse phuck picka pierdol* pillu* pimmel pimpis piss* pizda poontsee poop porn pron preteen preud prick pula pule pusse pussy puta puto qahbeh queef* queer* "
						. "qweef rautenberg schaffer scheiss* scheisse schlampe schmuck screw scrotum sharmuta sharmute shemale shipal shiz skribz skurwysyn slut smut sphencter spic spierdalaj splooge suka teets teez testicle tits titties titty twat twaty vittu "
						. "votze woose wank* wetback* whoar whore wichser wop yed zabourah ass";
	*/
		$sql = "SELECT * FROM nastywords";
		$result = db_query($sql);
		$row = db_fetch_assoc($result);
		$search = $row['words'];
		$search = str_replace("a",'[a4@]',$search);
		$search = str_replace("l",'[l1!]',$search);
		$search = str_replace("i",'[li1!]',$search);
		$search = str_replace("e",'[e3]',$search);
		$search = str_replace("t",'[t7+]',$search);
		$search = str_replace("o",'[o0]',$search);
		$search = str_replace("s",'[sz$]',$search);
		$search = str_replace("k",'c',$search);
		$search = str_replace("c",'[c(k]',$search);
		$start = "'(\s|\A)";
		$end = "(\s|\Z)'iU";
		$search = str_replace("*","([[:alnum:]]*)",$search);
		$search = str_replace(" ","$end $start", $search);
		$search = "$start".$search."$end";
		//echo $search;
		$search = split(" ",$search);
		//$input = " $input ";
	
		return preg_replace($search,"\\1`i$@#%`i\\2",$input);
	}else{
		return $input;
	}
}

function saveuser(){
	global $session,$dbqueriesthishit;
//	$cmd = date("Y-m-d H:i:s")." $dbqueriesthishit ".$_SERVER['REQUEST_URI'];
//	@exec("echo $cmd >> /home/groups/l/lo/lotgd/sessiondata/data/queryusage-".$session['user']['login'].".txt");
	if ($session['loggedin'] && $session['user']['acctid']!=""){
  	$session['user']['output']=$session[output];
  	$session['user']['allowednavs']=serialize($session['allowednavs']);
		$session['user']['bufflist']=serialize($session['bufflist']);
		if (is_array($session['user']['prefs'])) $session['user']['prefs']=serialize($session['user']['prefs']);
		//$session[user][laston] = date("Y-m-d H:i:s");
  	$sql="UPDATE accounts SET ";
  	reset($session[user]);
  	while(list($key,$val)=each($session[user])){
  		if (is_array($val)){
				$sql.="$key='".addslashes(serialize($val))."', ";
			}else{
				$sql.="$key='".addslashes($val)."', ";
			}
  	}
  	$sql = substr($sql,0,strlen($sql)-2);
  	$sql.=" WHERE acctid = ".$session[user][acctid];
  	db_query($sql);
  }
}

function createstring($array){
  if (is_array($array)){
    reset($array);
    while (list($key,$val)=each($array)){
      $output.=rawurlencode( rawurlencode($key)."\"".rawurlencode($val) )."\"";
    }
    $output=substr($output,0,strlen($output)-1);
  }
  return $output;
}

function createarray($string){
  $arr1 = split("\"",$string);
  $output = array();
  while (list($key,$val)=each($arr1)){
    $arr2=split("\"",rawurldecode($val));
    $output[rawurldecode($arr2[0])] = rawurldecode($arr2[1]);
  }
  return $output;
}

function output_array($array,$prefix=""){
  while (list($key,$val)=@each($array)){
    $output.=$prefix."[$key] = ";
    if (is_array($val)){
      $output.="array{\n".output_array($val,$prefix."[$key]")."\n}\n";
    }else{
      $output.=$val."\n";
    }
  }
  return $output;
}

function dump_item($item){
	$output = "";
	if (is_array($item)) $temp = $item;
	else $temp = unserialize($item);
	if (is_array($temp)) {
		$output .= "array(" . count($temp) . ") {<blockquote>";
		while(list($key, $val) = @each($temp)) {
			$output .= "'$key' = '" . dump_item($val) . "'`n";
		}
		$output .= "</blockquote>}";
	} else {
		$output .= $item;
	}
	return $output;
}

function addnews($news){
	global $session;
	$sql = "INSERT INTO news(newstext,newsdate,accountid) VALUES ('".addslashes($news)."',NOW(),".$session[user][acctid].")";
	return db_query($sql) or die(db_error($link));
}

function checkday() {
	global $session,$revertsession,$REQUEST_URI;
  //output("`#`iChecking to see if you're due for a new day: ".$session[user][laston].", ".date("Y-m-d H:i:s")."`i`n`0");
	if ($session['user']['loggedin']){
		output("<!--CheckNewDay()-->",true);
		if(is_new_day()){
			$session=$revertsession;
			$session['user']['restorepage']=$REQUEST_URI;
			$session['allowednavs']=array();
			addnav("","newday.php");
			redirect("newday.php");
		}
	}
}

function is_new_day(){
	global $session;
	$t1 = gametime(); 
	$t2 = convertgametime(strtotime($session[user][lasthit]));
	$d1 = date("Y-m-d",$t1);
	$d2 = date("Y-m-d",$t2);
	if ($d1!=$d2){
		return true;
	}else{
		return false;
	}
}

function getgametime(){
	return date("g:i a",gametime());
}

function gametime(){
	$time = convertgametime(strtotime("now"));
	return $time;
}

function convertgametime($intime){
	$time = (strtotime(date("1971-m-d H:i:s",strtotime("-".getsetting("gameoffsetseconds",0)." seconds",$intime))))*getsetting("daysperday",4) % strtotime("1971-01-01 00:00:00"); 
	return $time;
}

function sql_error($sql){
	global $session;
	return output_array($session)."SQL = <pre>$sql</pre>".db_error(LINK);
}

function ordinal($val){
  $exceptions = array(1=>"st",2=>"nd",3=>"rd",11=>"th",12=>"th",13=>"th");
	$x = ($val % 100);
	if (isset($exceptions[$x])){
	  return $val.$exceptions[$x];
	}else{
	  $x = ($val % 10);
		if (isset($exceptions[$x])){
		  return $val.$exceptions[$x];
		}else{
		  return $val."th";
		}
	}
}

function emoticons($text){
	$imgloc=$_SERVER['REQUEST_URI'];
	$text = str_replace(":(","<IMG SRC=\"./images/cry.gif\">",$text);
	$text = str_replace(":d","<IMG SRC=\"./images/devil.gif\">",$text);
	$text = str_replace(":o","<IMG SRC=\"./images/eek.gif\">",$text);
	$text = str_replace(":v","<IMG SRC=\"./images/evil.gif\">",$text);
	$text = str_replace(":h2","<IMG SRC=\"./images/hammer2.gif\">",$text);
	$text = str_replace(":lol","<IMG SRC=\"./images/laugh.gif\">",$text);
	$text = str_replace(":-","<IMG SRC=\"./images/pipe.gif\">",$text);
	$text = str_replace(":|","<IMG SRC=\"./images/sleep.gif\">",$text);
	$text = str_replace(":)","<IMG SRC=\"./images/smile.gif\">",$text);
	$text = str_replace(":tst","<IMG SRC=\"./images/toast.gif\">",$text);
	$text = str_replace(":p","<IMG SRC=\"./images/tongue.gif\">",$text);
	$text = str_replace(";)","<IMG SRC=\"./images/wink.gif\">",$text);
	return $text;
}

function addcommentary() {
	global $HTTP_POST_VARS,$session,$REQUEST_URI,$HTTP_GET_VARS,$doublepost;
	$doublepost=0;
	if ((int)getsetting("expirecontent",180)>0){
		$sql = "DELETE FROM commentary WHERE postdate<'".date("Y-m-d H:i:s",strtotime("-".getsetting("expirecontent",180)." days"))."'";
		db_query($sql);
	}
	$section=$HTTP_POST_VARS['section'];
	$talkline=$HTTP_POST_VARS['talkline'];
	if ($HTTP_POST_VARS['insertcommentary'][$section]!==NULL &&
		trim($HTTP_POST_VARS['insertcommentary'][$section])!="") {
        $commentary = str_replace("`n","",soap($HTTP_POST_VARS['insertcommentary'][$section]));
		$y = strlen($commentary);
		for ($x=0;$x<$y;$x++){
			if (substr($commentary,$x,1)=="`"){
				$colorcount++;
				if ($colorcount>=getsetting("maxcolors",10)){
					$commentary = substr($commentary,0,$x).preg_replace("'[`].'","",substr($commentary,$x));
					$x=$y;
				}
				$x++;
			}
		}
		if (substr($commentary,0,1)!=":" &&
		    substr($commentary,0,2)!="::" &&
		    substr($commentary,0,3)!="/me" &&
		    $session['user']['drunkenness']>0) {
			//drunk people shouldn't talk very straight.
			$straight = $commentary;
			$replacements=0;
			while ($replacements/strlen($straight) < ($session['user']['drunkenness'])/500 ){
				$slurs = array("a"=>"aa","e"=>"ee","f"=>"ff","h"=>"hh","i"=>"iy","l"=>"ll","m"=>"mm","n"=>"nn","o"=>"oo","r"=>"rr","s"=>"sh","u"=>"oo","v"=>"vv","w"=>"ww","y"=>"yy","z"=>"zz");
				if (e_rand(0,9)) {
					srand(e_rand());
					$letter = array_rand($slurs);
					$x = strpos(strtolower($commentary),$letter);
					if ($x!==false &&
						substr($comentary,$x,5)!="*hic*" &&
						substr($commentary,max($x-1,0),5)!="*hic*" &&
						substr($commentary,max($x-2,0),5)!="*hic*" &&
						substr($commentary,max($x-3,0),5)!="*hic*" &&
						substr($commentary,max($x-4,0),5)!="*hic*"
						){
						if (substr($commentary,$x,1)<>strtolower($letter)) $slurs[$letter] = strtoupper($slurs[$letter]); else $slurs[$letter] = strtolower($slurs[$letter]);
							$commentary = substr($commentary,0,$x).$slurs[$letter].substr($commentary,$x+1);
						$replacements++;
					}
				}else{
					$x = e_rand(0,strlen($commentary));
					if (substr($commentary,$x,5)=="*hic*") {$x+=5; } //output("moved 5 to $x "); 
					if (substr($commentary,max($x-1,0),5)=="*hic*") {$x+=4; } //output("moved 4 to $x ");
					if (substr($commentary,max($x-2,0),5)=="*hic*") {$x+=3; } //output("moved 3 to $x "); 
					if (substr($commentary,max($x-3,0),5)=="*hic*") {$x+=2; } //output("moved 2 to $x ");
					if (substr($commentary,max($x-4,0),5)=="*hic*") {$x+=1; } //output("moved 1 to $x "); 
					$commentary = substr($commentary,0,$x)."*hic*".substr($commentary,$x);
					//output($commentary."`n");
					$replacements++;
				}//end if
			}//end while
			//output("$replacements replacements (".($replacements/strlen($straight)).")`n");
			while (strpos($commentary,"*hic**hic*"))
				$commentary = str_replace("*hic**hic*","*hic*hic*",$commentary);
		}//end if
		$commentary = preg_replace("'([^[:space:]]{45,45})([^[:space:]])'","\\1 \\2",$commentary);
		if ($session['user']['drunkenness']>50) $talkline = "drunkenly $talkline";
//		$talkline = $talkline;

		if ($talkline!="says" // do an emote if the area has a custom talkline and the user isn't trying to emote already.
		&& substr($commentary,0,1)!=":" 
		&& substr($commentary,0,2)!="::" 
		&& substr($commentary,0,3)!="/me") 
		$commentary = ":`3$talkline, \\\"`#$commentary`3\\\"";
        // added for emoticons
        $commentary=htmlentities($commentary);
        // end mod
		$sql = "SELECT commentary.comment,commentary.author FROM commentary WHERE section='$section' ORDER BY commentid DESC LIMIT 1";
		$result = db_query($sql) or die(db_error(LINK));
		$row = db_fetch_assoc($result);
		db_free_result($result);
		if ($row['comment']!=$commentary || $row['author']!=$session['user']['acctid']){
		  $sql = "INSERT INTO commentary (postdate,section,author,comment) VALUES (now(),'$section',".$session['user']['acctid'].",\"$commentary\")";
			db_query($sql) or die(db_error(LINK));
		} else {
			$doublepost = 1;
		}
		}
}

function viewcommentary($section,$message="Interject your own commentary?",$limit=20,$talkline="says",$climit="0") {
	global $HTTP_POST_VARS,$session,$REQUEST_URI,$HTTP_GET_VARS, $doublepost;
	$nobios = array("motd.php"=>true);
	if ($nobios[basename($_SERVER['SCRIPT_NAME'])]) $linkbios=false; else $linkbios=true;
	//output("`b".basename($_SERVER['SCRIPT_NAME'])."`b`n");
	if ($doublepost) output("`\$`bDouble post?`b`0`n");
//	$message = translate($message);
	if ((int)getsetting("expirecontent",180)>0){
		$sql = "DELETE FROM commentary WHERE postdate<'".date("Y-m-d H:i:s",strtotime("-".getsetting("expirecontent",180)." days"))."'";
		db_query($sql);
	}
	$com=(int)$HTTP_GET_VARS[comscroll];
  $sql = "SELECT commentary.*,
	               accounts.name,
	               accounts.login
	          FROM commentary
	         INNER JOIN accounts
	            ON accounts.acctid = commentary.author
	         WHERE section = '$section'
	           AND accounts.locked=0
	         ORDER BY commentid DESC
	         LIMIT ".($com*$limit).",$limit";
	$result = db_query($sql) or die(db_error(LINK));
	$counttoday=0;
	for ($i=0;$i < db_num_rows($result);$i++){
	  $row = db_fetch_assoc($result);
		$row['comment']=preg_replace("'[`][^1234567!@#$%^&]'","",$row['comment']);
        $row['comment']=emoticons($row['comment']);
		$commentids[$i] = $row['commentid'];
		if (date("Y-m-d",strtotime($row['postdate']))==date("Y-m-d")){
            if ($row['name']==$session['user']['name'] && $climit=="0") $counttoday++;
		}
		$x=0;
		$ft="";
		for ($x=0;strlen($ft)<3 && $x<strlen($row['comment']);$x++){
			if (substr($row['comment'],$x,1)=="`" && strlen($ft)==0) {
				$x++;
			}else{
				$ft.=substr($row['comment'],$x,1);
			}
		}
		$link = "bio.php?char=".rawurlencode($row[login]) . "&ret=".URLEncode($_SERVER['REQUEST_URI']);
		if (substr($ft,0,2)=="::") $ft = substr($ft,0,2);
		else
			if (substr($ft,0,1)==":") $ft = substr($ft,0,1);
		if ($ft=="::" || $ft=="/me" || $ft==":"){
			$x = strpos($row['comment'],$ft);
			if ($x!==false){
				if ($linkbios)
					$op[$i] = str_replace("&amp;","&",substr($row['comment'],0,$x))
					."`0<a href='$link' style='text-decoration: none'>\n`&$row[name]`0</a>\n`& "
					.str_replace("&amp;","&",substr($row['comment'],$x+strlen($ft)))
						."`0`n";
				else
					$op[$i] = str_replace("&amp;","&",substr($row['comment'],0,$x))
					."`0\n`&$row[name]`0\n`& "
					.str_replace("&amp;","&",substr($row['comment'],$x+strlen($ft)))
						."`0`n";
			}
		}
		if ($op[$i]=="")
			if ($linkbios)
				$op[$i] = "`0<a href='$link' style='text-decoration: none'>`&$row[name]`0</a>`3 says, \"`#"
					.str_replace("&amp;","&",$row['comment'])."`3\"`0`n";
			else
				$op[$i] = "`0`&$row[name]`0`3 says, \"`#"
				    .str_replace("&amp;","&",$row['comment'])."`3\"`0`n";
		if ($message=="X") $op[$i]="`0($row[section]) ".$op[$i];
		if ($row['postdate']>=$session['user']['recentcomments']) $op[$i]="<img src='images/new.gif' alt='&gt;' width='3' height='5' align='absmiddle'> ".$op[$i];
		addnav("",$link);
	}
	$i--;
	$outputcomments=array();
	$sect="x";
	for (;$i>=0;$i--){
		$out="";
		if ($session['user']['superuser']>=3 && $message=="X"){
			$out.="`0[ <a href='superuser.php?op=commentdelete&commentid=$commentids[$i]&return=".URLEncode($_SERVER['REQUEST_URI'])."'>Del</a> ]&nbsp;";
			addnav("","superuser.php?op=commentdelete&commentid=$commentids[$i]&return=".URLEncode($_SERVER['REQUEST_URI']));
			$matches=array();
			preg_match("/[(][^)]*[)]/",$op[$i],$matches);
			$sect=$matches[0];
		}
		//output($op[$i],true);
		$out.=$op[$i];
		if (!is_array($outputcomments[$sect])) $outputcomments[$sect]=array();
		array_push($outputcomments[$sect],$out);
	}
	ksort($outputcomments);
	reset($outputcomments);
	while (list($sec,$v)=each($outputcomments)){
		if ($sec!="x") output("`n`b$sec`b`n");
		reset($v);
		while (list($key,$val)=each($v)){
			output($val,true);
		}
	}

	if ($session['user']['loggedin']) {
		if ($counttoday<($limit/2) || $session['user']['superuser']>=2){
			if ($message!="X"){
				if ($talkline!="says") $tll = strlen($talkline)+11; else $tll=0;
				output("<form action=\"$REQUEST_URI\" method='POST'>`@$message`n.<input name='insertcommentary[$section]' size='40' maxlength='".(200-$tll)."'><input type='hidden' name='talkline' value='$talkline'><input type='hidden' name='section' value='$section'><input type='submit' class='button' value='Add'>`n".(round($limit/2,0)-$counttoday<3?"`)(You have ".(round($limit/2,0)-$counttoday)." posts left today)":"")."`0`n</form>",true);
				addnav("",$REQUEST_URI);
			}
		}else{
			output("`@$message`nSorry, you've exhausted your posts in this section for now.`0`n");
		}
	}
	if (db_num_rows($result)>=$limit){
		$req = preg_replace("'[&]?c(omscroll)?=([[:digit:]-])*'","",$REQUEST_URI)."&comscroll=".($com+1);
			//$req = substr($REQUEST_URI,0,strpos($REQUEST_URI,"c="))."&c=$HTTP_GET_VARS[c]"."&comscroll=".($com+1);
			$req = str_replace("?&","?",$req);
			if (!strpos($req,"?")) $req = str_replace("&","?",$req);
			output("<a href=\"$req\">&lt;&lt; Previous</a>",true);
			addnav("",$req);
		}
	$req = preg_replace("'[&]?c(omscroll)?=([[:digit:]]|-)*'","",$REQUEST_URI)."&comscroll=0";
		//$req = substr($REQUEST_URI,0,strpos($REQUEST_URI,"c="))."&c=$HTTP_GET_VARS[c]"."&comscroll=".($com-1);
		$req = str_replace("?&","?",$req);
		if (!strpos($req,"?")) $req = str_replace("&","?",$req);
		output("&nbsp;<a href=\"$req\">Refresh</a>&nbsp;",true);
		addnav("",$req);
		if ($com>0){
		$req = preg_replace("'[&]?c(omscroll)?=([[:digit:]]|-)*'","",$REQUEST_URI)."&comscroll=".($com-1);
			//$req = substr($REQUEST_URI,0,strpos($REQUEST_URI,"c="))."&c=$HTTP_GET_VARS[c]"."&comscroll=".($com-1);
			$req = str_replace("?&","?",$req);
			if (!strpos($req,"?")) $req = str_replace("&","?",$req);
			output(" <a href=\"$req\">Next &gt;&gt;</a>",true);
			addnav("",$req);
		}
   $key="<br><br><table border=\"0\" width=\"70%\" cellspacing=\"0\" cellpadding=\"0\" height=\"62\"><tr><td width=\"57%\" colspan=\"7\" height=\"19\">Emoticon Key:</td>";
   $key.="<td width=\"45%\" colspan=\"7\" height=\"19\"></td></tr><tr><td width=\"8%\" align=\"center\" height=\"20\"><IMG SRC=\"./images/cry.gif\"></td><td width=\"8%\" align=\"center\" height=\"20\"><IMG SRC=\"./images/devil.gif\"></td>";
   $key.="<td width=\"8%\" align=\"center\" height=\"20\"><IMG SRC=\"./images/eek.gif\"></td><td width=\"8%\" align=\"center\" height=\"20\"><IMG SRC=\"./images/evil.gif\"></td>";
   $key.="<td width=\"8%\" align=\"center\" height=\"20\"><IMG SRC=\"./images/hammer2.gif\"></td><td width=\"8%\" align=\"center\" height=\"20\"><IMG SRC=\"./images/laugh.gif\"></td><td width=\"8%\" align=\"center\" height=\"20\"><IMG SRC=\"./images/pipe.gif\"></td>";
   $key.="<td width=\"8%\" align=\"center\" height=\"20\"><IMG SRC=\"./images/sleep.gif\"></td><td width=\"8%\" align=\"center\" height=\"20\"><IMG SRC=\"./images/smile.gif\"></td>";
   $key.="<td width=\"9%\" align=\"center\" height=\"20\"><IMG SRC=\"./images/toast.gif\"></td><td width=\"9%\" align=\"center\" height=\"20\"><IMG SRC=\"./images/tongue.gif\"></td><td width=\"9%\" align=\"center\" height=\"20\"><IMG SRC=\"./images/wink.gif\">";
   $key.="</td></tr><tr><td width=\"8%\" align=\"center\" height=\"21\">:(</td><td width=\"8%\" align=\"center\" height=\"21\">:d</td><td width=\"8%\" align=\"center\" height=\"21\">:o</td><td width=\"8%\" align=\"center\" height=\"21\">:v</td>";
   $key.="<td width=\"8%\" align=\"center\" height=\"21\">:h2</td><td width=\"8%\" align=\"center\" height=\"21\">:lol</td><td width=\"8%\" align=\"center\" height=\"21\">:-</td><td width=\"8%\" align=\"center\" height=\"21\">:|</td>";
   $key.="<td width=\"8%\" align=\"center\" height=\"21\">:)</td><td width=\"9%\" align=\"center\" height=\"21\">:tst</td><td width=\"9%\" align=\"center\" height=\"21\">:p</td><td width=\"9%\" align=\"center\" height=\"21\">;)</td></tr></table>";
   rawoutput($key,true);

db_free_result($result);
}

function dhms($secs,$dec=false){
	if ($dec===false) $secs=round($secs,0);
	return (int)($secs/86400)."d".(int)($secs/3600%24)."h".(int)($secs/60%60)."m".($secs%60).($dec?substr($secs-(int)$secs,1):"")."s";
}

function getmount($horse=0) {
	$sql = "SELECT * FROM mounts WHERE mountid='$horse'";
	$result = db_query($sql);
	if (db_num_rows($result)>0){
		return db_fetch_assoc($result);
	}else{
		return array();
	}
}

function debuglog($message,$target=0){
	global $session;
	$sql = "DELETE from debuglog WHERE date <'".date("Y-m-d H:i:s",strtotime("-".(getsetting("expirecontent",180)/10)." days"))."'";
	db_query($sql);
	$sql = "INSERT INTO debuglog VALUES(0,now(),{$session['user']['acctid']},$target,'".addslashes($message)."')";
	db_query($sql);
}

if (file_exists("dbconnect.php")){
	require_once "dbconnect.php";
}else{
	echo "You must edit the file named \"dbconnect.php.dist,\" and provide the requested information, then save it as \"dbconnect.php\"".
	exit();
}

$link = db_pconnect($DB_HOST, $DB_USER, $DB_PASS) or die (db_error($link));
db_select_db ($DB_NAME) or die (db_error($link));
define("LINK",$link);

require_once "translator.php";


session_register("session");
function register_global(&$var){
	@reset($var);
	while (list($key,$val)=@each($var)){
		global $$key;
		$$key = $val;
	}
	@reset($var);
}
$session =& $_SESSION['session'];
//echo nl2br(htmlentities(output_array($session)));
//register_global($_SESSION);
register_global($_SERVER);

if (strtotime("-".getsetting("LOGINTIMEOUT",900)." seconds") > $session['lasthit'] && $session['lasthit']>0 && $session['loggedin']){
	//force the abandoning of the session when the user should have been sent to the fields.
	//echo "Session abandon:".(strtotime("now")-$session[lasthit]);
	
	$session=array();
	$session['message'].="`nYour session has expired!`n";
}
$session['lasthit']=strtotime("now");

$revertsession=$session;

if ($PATH_INFO != "") {
	$SCRIPT_NAME=$PATH_INFO;
	$REQUEST_URI="";
}
if ($REQUEST_URI==""){
	//necessary for some IIS installations (CGI in particular)
	if (is_array($_GET) && count($_GET)>0){
		$REQUEST_URI=$SCRIPT_NAME."?";
		reset($_GET);
		$i=0;
		while (list($key,$val)=each($_GET)){
			if ($i>0) $REQUEST_URI.="&";
			$REQUEST_URI.="$key=".URLEncode($val);
			$i++;
		}
	}else{
		$REQUEST_URI=$SCRIPT_NAME;
	}
	$_SERVER['REQUEST_URI'] = $REQUEST_URI;
}
$SCRIPT_NAME=substr($SCRIPT_NAME,strrpos($SCRIPT_NAME,"/")+1);
if (strpos($REQUEST_URI,"?")){
	$REQUEST_URI=$SCRIPT_NAME.substr($REQUEST_URI,strpos($REQUEST_URI,"?"));
}else{
	$REQUEST_URI=$SCRIPT_NAME;
}

$allowanonymous=array("index.php"=>true,"login.php"=>true,"create.php"=>true,"about.php"=>true,"list.php"=>true,"petition.php"=>true,"connector.php"=>true,"logdnet.php"=>true,"referral.php"=>true,"news.php"=>true,"motd.php"=>true,"topwebvote.php"=>true);
$allownonnav = array("badnav.php"=>true,"motd.php"=>true,"petition.php"=>true,"mail.php"=>true,"topwebvote.php"=>true);
if ($session['loggedin']){
	$sql = "SELECT * FROM accounts WHERE acctid = '".$session['user']['acctid']."'";
	$result = db_query($sql);
	if (db_num_rows($result)==1){
		$session['user']=db_fetch_assoc($result);
		$session['output']=$session['user']['output'];
		$session['user']['dragonpoints']=unserialize($session['user']['dragonpoints']);
		$session['user']['prefs']=unserialize($session['user']['prefs']);
		if (!is_array($session['user']['dragonpoints'])) $session['user']['dragonpoints']=array();
		if (is_array(unserialize($session['user']['allowednavs']))){
			$session['allowednavs']=unserialize($session['user']['allowednavs']);
		}else{
			//depreciated, left only for legacy support.
			$session['allowednavs']=createarray($session['user']['allowednavs']);
		}
		if (!$session['user']['loggedin'] || (0 && (date("U") - strtotime($session['user']['laston'])) > getsetting("LOGINTIMEOUT",900)) ){
			$session=array();
			redirect("index.php?op=timeout","Account not logged in but session thinks they are.");
		}
	}else{
		$session=array();
		$session['message']="`4Error, your login was incorrect`0";
		redirect("index.php","Account Disappeared!");
	}
	db_free_result($result);
	if ($session['allowednavs'][$REQUEST_URI] && !$allownonnav[$SCRIPT_NAME]){
		$session['allowednavs']=array();
	}else{
		if (!$allownonnav[$SCRIPT_NAME]){
			redirect("badnav.php","Navigation not allowed to $REQUEST_URI");
		}
	}
}else{
	//if ($SCRIPT_NAME!="index.php" && $SCRIPT_NAME!="login.php" && $SCRIPT_NAME!="create.php" && $SCRIPT_NAME!="about.php"){
	if (!$allowanonymous[$SCRIPT_NAME]){
		$session['message']="You are not logged in, this may be because your session timed out.";
		redirect("index.php?op=timeout","Not logged in: $REQUEST_URI");
	}
}
//if ($session['user']['loggedin']!=true && $SCRIPT_NAME!="index.php" && $SCRIPT_NAME!="login.php" && $SCRIPT_NAME!="create.php" && $SCRIPT_NAME!="about.php"){
if ($session['user']['loggedin']!=true && !$allowanonymous[$SCRIPT_NAME]){
	redirect("login.php?op=logout");
}

$session['counter']++;
$nokeeprestore=array("newday.php"=>1,"badnav.php"=>1,"motd.php"=>1,"mail.php"=>1,"petition.php"=>1);
if (!$nokeeprestore[$SCRIPT_NAME]) { //strpos($REQUEST_URI,"newday.php")===false && strpos($REQUEST_URI,"badnav.php")===false && strpos($REQUEST_URI,"motd.php")===false && strpos($REQUEST_URI,"mail.php")===false
  $session['user']['restorepage']=$REQUEST_URI;
}else{

}

if ($session['user']['hitpoints']>0){
	$session['user']['alive']=true;
}else{
	$session['user']['alive']=false;
}

$session['bufflist']=unserialize($session['user']['bufflist']);
if (!is_array($session['bufflist'])) $session['bufflist']=array();
$session['user']['lastip']=$REMOTE_ADDR;
if (strlen($_COOKIE['lgi'])<32){
	if (strlen($session['user']['uniqueid'])<32){
		$u=md5(microtime());
		setcookie("lgi",$u,strtotime("+365 days"));
		$_COOKIE['lgi']=$u;
		$session['user']['uniqueid']=$u;
	}else{
		setcookie("lgi",$session['user']['uniqueid'],strtotime("+365 days"));
	}
}else{
	$session['user']['uniqueid']=$_COOKIE['lgi'];
}
$url = "http://".$_SERVER['SERVER_NAME'].dirname($_SERVER['REQUEST_URI']);
$url = substr($url,0,strlen($url)-1);

if (substr($_SERVER['HTTP_REFERER'],0,strlen($url))==$url || $_SERVER['HTTP_REFERER']==""){

}else{
	$sql = "SELECT * FROM referers WHERE uri='{$_SERVER['HTTP_REFERER']}'";
	$result = db_query($sql);
	$row = db_fetch_assoc($result);
	db_free_result($result);
	$site = str_replace("http://","",$_SERVER['HTTP_REFERER']);
	if (strpos($site,"/")) 
		$site = substr($site,0,strpos($site,"/"));
	if ($row['refererid']>""){
		$sql = "UPDATE referers SET count=count+1,last=now(),site='".addslashes($site)."' WHERE refererid='{$row['refererid']}'";
	}else{
		$sql = "INSERT INTO referers (uri,count,last,site) VALUES ('{$_SERVER['HTTP_REFERER']}',1,now(),'".addslashes($site)."')";
	}
	db_query($sql);
}
if ($_COOKIE['template']!="") $templatename=$_COOKIE['template'];
if (!file_exists("templates/$templatename") || $templatename=="") $templatename="yarbrough.htm";
$template = loadtemplate($templatename);
//tags that must appear in the header
$templatetags=array("title","headscript","script");
while (list($key,$val)=each($templatetags)){
	if (strpos($template['header'],"{".$val."}")===false) $templatemessage.="You do not have {".$val."} defined in your header\n";
}
//tags that must appear in the footer
$templatetags=array();
while (list($key,$val)=each($templatetags)){
	if (strpos($template['footer'],"{".$val."}")===false) $templatemessage.="You do not have {".$val."} defined in your footer\n";
}
//tags that may appear anywhere but must appear
$templatetags=array("nav","stats","petition","motd","mail","copyright","source");
while (list($key,$val)=each($templatetags)){
	if (strpos($template['header'],"{".$val."}")===false && strpos($template['footer'],"{".$val."}")===false) $templatemessage.="You do not have {".$val."} defined in either your header or footer\n";
}

if ($templatemessage!=""){
	echo "<b>You have one or more errors in your template page!</b><br>".nl2br($templatemessage);
	$template=loadtemplate("yarbrough.htm");
}

$races=array(1=>"Troll",2=>"Elf",3=>"Human",4=>"Dwarf",0=>"Unknown",50=>"Hoversheep");

$logd_version = "TDS-0.01";
$session['user']['laston']=date("Y-m-d H:i:s");

$playermount = getmount($session['user']['hashorse']);

$titles = array(
	0=>array("Farmboy","Farmgirl"),
	1=>array("Page", "Page"),
	2=>array("Squire", "Squire"),
	3=>array("Gladiator", "Gladiatrix"),
	4=>array("Legionnaire","Legioness"),
	5=>array("Centurion","Centurioness"),
	6=>array("Sir","Madam"),
	7=>array("Reeve", "Reeve"),
	8=>array("Steward", "Stewardess"),
	9=>array("Mayor", "Mayoress"),
	10=>array("Baron", "Baroness"),
	11=>array("Count", "Countess"),
	12=>array("Viscount", "Viscountess"),
	13=>array("Marquis", "Marquisette"),
	14=>array("Chancellor", "Chancelress"),
	15=>array("Prince", "Princess"),
	16=>array("King", "Queen"),
	17=>array("Emperor", "Empress"),
	18=>array("Angel", "Angel"),
	19=>array("Archangel", "Archangel"),
	20=>array("Principality", "Principality"),
	21=>array("Power", "Power"),
	22=>array("Virtue", "Virtue"),
	23=>array("Dominion", "Dominion"),
	24=>array("Throne", "Throne"),
	25=>array("Cherub", "Cherub"),
	26=>array("Seraph", "Seraph"),
	27=>array("Demigod", "Demigoddess"),
	28=>array("Titan", "Titaness"),
	29=>array("Archtitan", "Archtitaness"),
	30=>array("Undergod", "Undergoddess"),
);

$beta = (getsetting("beta",0) == 1 || $session['user']['beta']==1);
?>
