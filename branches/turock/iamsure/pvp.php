<?
require_once "common.php";
$pvptime = getsetting("pvptimeout",600);
$pvptimeout = date("Y-m-d H:i:s",strtotime("-$pvptime seconds"));
page_header("PvP Combat!");
if ($HTTP_GET_VARS[op]=="" && $HTTP_GET_VARS[act]!="attack"){
	//if ($session['user']['age']<=5 && $session['user']['dragonkills']==0){
	//  output("`\$Warning!`^ Players are immune from Player vs Player (PvP) combat for their first 5 days in the game.  If you choose to attack another player, you will lose this immunity!`n`n");
	//}
	checkday();
	pvpwarning();
  output("`4You head out to the fields, where you know some unwitting warriors are sleeping.`n`nYou have `^".$session[user][playerfights]."`4 PvP fights left for today.");
	addnav("List Warriors","pvp.php?op=list");
  addnav("Return to the Village","village.php");
}else if ($HTTP_GET_VARS[op]=="list"){
	checkday();
	pvpwarning();
	$days = getsetting("pvpimmunity", 5);
	$exp = getsetting("pvpminexp", 1500);
  $sql = "SELECT name,alive,location,sex,level,laston,loggedin,login,pvpflag FROM accounts WHERE 
	(locked=0) AND 
	(age > $days OR dragonkills > 0 OR pk > 0 OR experience > $exp) AND
	(level >= ".($session[user][level]-1)." AND level <= ".($session[user][level]+2).") AND 
	(alive=1 AND location=0) AND 
	(laston < '".date("Y-m-d H:i:s",strtotime("-".getsetting("LOGINTIMEOUT",900)." sec"))."' OR loggedin=0) AND
	(acctid <> ".$session[user][acctid].")
	ORDER BY level DESC";
	//echo ("<pre>$sql</pre>");
  $result = db_query($sql) or die(db_error(LINK));
	output("<table border='0' cellpadding='3' cellspacing='0'><tr><td>Name</td><td>Level</td><td>Ops</td></tr>",true);
	for ($i=0;$i<db_num_rows($result);$i++){
	  $row = db_fetch_assoc($result);
	  $biolink="bio.php?char=".rawurlencode($row[login])."&ret=".urlencode($_SERVER['REQUEST_URI']);
	  addnav("", $biolink);
		if($row[pvpflag]>$pvptimeout){
		  output("<tr class='".($i%2?"trlight":"trdark")."'><td>$row[name]</td><td>$row[level]</td><td>[ <a href='$biolink'>Bio</a> | `i(Attacked too recently)`i ]</td></tr>",true);
		}else{
		  output("<tr class='".($i%2?"trlight":"trdark")."'><td>$row[name]</td><td>$row[level]</td><td>[ <a href='$biolink'>Bio</a> | <a href='pvp.php?act=attack&name=".rawurlencode($row[login])."'>Attack</a> ]</td></tr>",true);
			addnav("","pvp.php?act=attack&name=".rawurlencode($row[login]));
		}
	}
	output("</table>",true);
	addnav("List Warriors","pvp.php?op=list");
  addnav("Return to the Village","village.php");
} else if ($HTTP_GET_VARS[act] == "attack") {
  $sql = "SELECT name AS creaturename,
	               level AS creaturelevel,
								 weapon AS creatureweapon,
								 gold AS creaturegold,
								 experience AS creatureexp,
								 maxhitpoints AS creaturehealth,
								 attack AS creatureattack,
								 defence AS creaturedefense,
								 bounty AS creaturebounty,
								 loggedin,
								 location,
								 laston,
								 alive,
								 acctid,
								 pvpflag
					FROM accounts
					WHERE login=\"$HTTP_GET_VARS[name]\"";
	$result = db_query($sql) or die(db_error(LINK));
	if (db_num_rows($result)>0){
		$row = db_fetch_assoc($result);
		if (abs($session[user][level]-$row[creaturelevel])>2){
		  output("`\$Error:`4 That user is out of your level range!");
		}elseif ($row[pvpflag] > $pvptimeout){
			output("`\$Oops:`4 That user is currently engaged by someone else, you'll have to wait your turn! $row[pvpflag] : $pvptimeout");
		}else{
		  if (strtotime($row[laston]) > strtotime("-".getsetting("LOGINTIMEOUT",900)." sec") && $row[loggedin]){
			  output("`\$Error:`4 That user is now online.");
			}else{
			  if ((int)$row[location]!=0 && 0){
				  output("`\$Error:`4 That user is not in a location that you can attack them.");
				}else{
				  if((int)$row[alive]!=1){
					  output("`\$Error:`4 That user is not alive.");
					}else{
					  if ($session[user][playerfights]>0){
							$sql = "UPDATE accounts SET pvpflag=now() WHERE acctid=$row[acctid]";
							db_query($sql);
							$battle=true;
							$row[pvp]=1;
							$row[creatureexp] = round($row[creatureexp],0);
							$row[playerstarthp] = $session[user][hitpoints];
							$session[user][badguy]=createstring($row);
							$session[user][playerfights]--;
							$session['user']['buffbackup']="";
							pvpwarning(true);
						}else{
						  output("`4Judging by how tired you are, you think you had best not engage in another player battle today.");
						}
					}
				}
			}
		}
	}else{
	  output("`\$Error:`4 That user was not found!  How'd you get here anyhow?");
	}
  if ($battle){
	  
	}else{
	  addnav("Return to the village","village.php");
	}
}
if ($HTTP_GET_VARS[op]=="run"){
  output("Your honor prevents you from running");
	$HTTP_GET_VARS[op]="fight";
}
if ($HTTP_GET_VARS[skill]!=""){
  output("Your honor prevents you from using a special ability");
	$HTTP_GET_VARS[skill]="";
}
if ($HTTP_GET_VARS[op]=="fight" || $HTTP_GET_VARS[op]=="run"){
	$battle=true;
}
if ($battle){
  include("battle.php");
	if ($victory){
		//$badguy[creaturegold]=e_rand(0,$badguy[creaturegold]);
		$exp = round(getsetting("pvpattgain",10)*$badguy[creatureexp]/100,0);
		$expbonus = round(($exp * (1+.1*($badguy[creaturelevel]-$session[user][level]))) - $exp,0);
		output("`b`&$badguy[creaturelose]`0`b`n"); 
		output("`b`\$You have slain $badguy[creaturename]!`0`b`n");
		output("`#You recieve `^$badguy[creaturegold]`# gold!`n");
		// Bounty Check - Darrell Morrone
		if ($badguy[creaturebounty]>0){
		    output("`#You also recieve `^$badguy[creaturebounty]`# in bounty gold!`n");
			}
	    // End Bounty Check - Darrell Morrone
		if ($expbonus>0){
		  output("`#***Because of the difficult nature of this fight, you are awarded an additional `^$expbonus`# experience!`n");
		}else if ($expbonus<0){
		  output("`#***Because of the simplistic nature of this fight, you are penalized `^".abs($expbonus)."`# experience!`n");
		}
		output("You receive `^".($exp+$expbonus)."`# experience!`n`0");
		$session['user']['gold']+=$badguy['creaturegold'];
		if ($badguy['creaturegold']) {
			debuglog("gained {$badguy['creaturegold']} gold for killing ", $badguy['acctid']);
		}
		// Add Bounty Gold - Darrell Morrone
		$session['user']['gold']+=$badguy['creaturebounty'];
		if ($badguy['creaturebounty']) {
			debuglog("gained {$badguy['creaturebounty']} gold bounty for killing ", $badguy['acctid']);
		}
		$session['user']['experience']+=($exp+$expbonus);
		if ($badguy['location']){
		  addnews("`4".$session['user']['name']."`3 defeated `4{$badguy['creaturename']}`3 by sneaking in to their room in the inn!");
			$killedin="`6The Inn";
		}else{
		  addnews("`4".$session['user']['name']."`3 defeated `4{$badguy['creaturename']}`3 in fair combat in the fields.");
			$killedin="`@The Fields";
		}
		// Add Bounty Kill to the News - Darrell Mororne
		if ($badguy['creaturebounty']>0){
		    addnews("`4".$session['user']['name']."`3 collected `4{$badguy['creaturebounty']} gold bounty by turning in `4{$badguy['creaturename']}'s head!");
			}
		$sql = "SELECT gold FROM accounts WHERE acctid='".(int)$badguy['acctid']."'";
		$result = db_query($sql);
		$row = db_fetch_assoc($result);
		$badguy[creaturegold]=((int)$row[gold]>(int)$badguy[creaturegold]?(int)$badguy[creaturegold]:(int)$row[gold]);
		//$sql = "UPDATE accounts SET alive=0, killedin='$killedin', goldinbank=goldinbank-IF(gold<$badguy[creaturegold],gold-$badguy[creaturegold],0),gold=gold-$badguy[creaturegold], experience=experience*.95, slainby=\"".addslashes($session[user][name])."\" WHERE acctid=$badguy[acctid]";
// \/- Gunnar Kreitz
		$lostexp = round($badguy['creatureexp']*getsetting("pvpdeflose",5)/100,0);
 		$mailmessage = "`^".$session['user']['name']."`2 attacked you in $killedin`2 with %p `^".$session['user']['weapon']."`2, and defeated you!"
				." `n`nYou noticed %o had an initial hp of `^".$badguy['playerstarthp']."`2 and just before you died %o had `^".$session['user']['hitpoints']."`2 remaining."
				." `n`nAs a result, you lost `\$".getsetting("pvpdeflose",5)."%`2 of your experience (approximately $lostexp points), and `^".$badguy[creaturegold]."`2 gold. They also received `^".$badguy[creaturebounty]." `2in bounty gold."
				." `n`nDon't you think it's time for some revenge?";
 		$mailmessage = str_replace("%p",($session['user']['sex']?"her":"his"),$mailmessage);
 		$mailmessage = str_replace("%o",($session['user']['sex']?"she":"he"),$mailmessage);
 		systemmail($badguy['acctid'],"`2You were killed in $killedin`2",$mailmessage); 
// /\- Gunnar Kreitz

		$sql = "UPDATE accounts SET alive=0, bounty=0, goldinbank=goldinbank-IF(gold<$badguy[creaturegold],gold-$badguy[creaturegold],0),gold=gold-$badguy[creaturegold], experience=experience-$lostexp WHERE acctid=".(int)$badguy[acctid]."";		
		db_query($sql);
		
		$HTTP_GET_VARS[op]="";
		if ($badguy['location']){
			addnav("Return to the inn","inn.php");
		} else {
			addnav("Return to the village","village.php");
		}
		$badguy=array();
	}else{
		if($defeat){
			addnav("Daily news","news.php");
			$sql = "SELECT taunt FROM taunts ORDER BY rand(".e_rand().") LIMIT 1";
			$result = db_query($sql) or die(db_error(LINK));
			$taunt = db_fetch_assoc($result);
			$taunt = str_replace("%s",($session[user][sex]?"her":"him"),$taunt[taunt]);
			$taunt = str_replace("%o",($session[user][sex]?"she":"he"),$taunt);
			$taunt = str_replace("%p",($session[user][sex]?"her":"his"),$taunt);
			$taunt = str_replace("%x",($session[user][weapon]),$taunt);
			$taunt = str_replace("%X",$badguy[creatureweapon],$taunt);
			$taunt = str_replace("%W",$badguy[creaturename],$taunt);
			$taunt = str_replace("%w",$session[user][name],$taunt);
			if ($badguy[location]){
				$killedin="`6The Inn";
			}else{
				$killedin="`@The Fields";
			}
			$badguy[acctid]=(int)$badguy[acctid];
			$badguy[creaturegold]=(int)$badguy[creaturegold];
			systemmail($badguy[acctid],"`2You were successful in $killedin`2","`^".$session[user][name]."`2 attacked you in $killedin`2, but you were victorious!`n`nAs a result, you recieved `^".round($session[user][experience]*getsetting("pvpdefgain",10)/100,0)."`2 experience and `^".$session[user][gold]."`2 gold!"); 
			addnews("`%".$session[user][name]."`5 has been slain when ".($session[user][sex]?"she":"he")." attacked $badguy[creaturename] in $killedin`5.`n$taunt");
			$sql = "UPDATE accounts SET gold=gold+".(int)$session[user][gold].", experience=experience+".round($session[user][experience]*getsetting("pvpdefgain",10)/100,0)." WHERE acctid=".(int)$badguy[acctid]."";
			db_query($sql);
			$session[user][alive]=false;
			debuglog("lost {$session['user']['gold']} gold being slain by ", $badguy['acctid']);
			$session[user][gold]=0;
			$session[user][hitpoints]=0;
			$session[user][experience]=round($session[user][experience]*(100-getsetting("pvpattlose",15))/100,0);
			$session[user][badguy]="";
			output("`b`&You have been slain by `%$badguy[creaturename]`&!!!`n");
			output("`4All gold on hand has been lost!`n");
			output("`4".getsetting("pvpattlose",15)."% of experience has been lost!`n");
			output("You may begin fighting again tomorrow.");
			
			page_footer();
		}else{
		  fightnav(false,false);
		}
	}
}
page_footer();
?>
