<?
require_once "common.php";
isnewday(3);

if ($_GET[op]=="search"){
	$sql = "SELECT acctid FROM accounts WHERE ";
	$where="
	login LIKE '%{$_POST['q']}%' OR 
	acctid LIKE '%{$_POST['q']}%' OR 
	name LIKE '%{$_POST['q']}%' OR 
	emailaddress LIKE '%{$_POST['q']}%' OR 
	lastip LIKE '%{$_POST['q']}%' OR 
	uniqueid LIKE '%{$_POST['q']}%' OR 
	gentimecount LIKE '%{$_POST['q']}%' OR 
	level LIKE '%{$_POST['q']}%'";
	$result = db_query($sql.$where);
	if (db_num_rows($result)<=0){
		output("`\$No results found`0");
		$_GET[op]="";
		$where="";
	}elseif (db_num_rows($result)>100){
		output("`\$Too many results found, narrow your search please.`0");
		$_GET[op]="";
		$where="";
	}elseif (db_num_rows($result)==1){
		//$row = db_fetch_assoc($result);
		//redirect("user.php?op=edit&userid=$row[acctid]");
		$_GET[op]="";
		$_GET['page']=0;
	}else{
		$_GET[op]="";
		$_GET['page']=0;
	}
}

page_header("User Editor");
	output("<form action='user.php?op=search' method='POST'>Search by any field below: <input name='q' id='q'><input type='submit' class='button'></form>",true);
	output("<script language='JavaScript'>document.getElementById('q').focus();</script>",true);
	addnav("","user.php?op=search");
addnav("G?Return to the Grotto","superuser.php");
addnav("M?Return to the Mundane","village.php");
addnav("Add a ban","user.php?op=setupban");
addnav("List/Remove bans","user.php?op=removeban");
//addnav("User Editor Home","user.php");
$sql = "SELECT count(acctid) AS count FROM accounts";
$result = db_query($sql);
$row = db_fetch_assoc($result);
$page=0;
while ($row[count]>0){
	$page++;
	addnav("$page Page $page","user.php?page=".($page-1)."&sort=$_GET[sort]");
	$row[count]-=100;
}

$mounts=",0,Nope";
$sql = "SELECT mountid,mountname,mountcategory FROM mounts ORDER BY mountcategory";
$result = db_query($sql);
while ($row = db_fetch_assoc($result)){
	$mounts.=",{$row['mountid']},{$row['mountcategory']}: {$row['mountname']}";
}
$userinfo = array(
	"Account info,title",
	"acctid"=>"User id,viewonly",
	"login"=>"Login",
	"newpassword"=>"New Password",
	"emailaddress"=>"Email Address",
	"locked"=>"Account Locked,bool",
	"banoverride"=>"Override Bans for this account,bool",
	"superuser"=>"Superuser,enum,0,Standard play days per calendar day,1,Unlimited play days per calendar day,2,Admin creatures and taunts,3,Admin users",
	
	"Basic user info,title",
	"name"=>"Display Name",
	"title"=>"Title (must also put in display name)",
	"ctitle"=>"Custom Title (must also put in display name)",
	"sex"=>"Sex,enum,0,Male,1,Female",
// we can't change this this way or their stats will be wrong.
//	"race"=>"Race,enum,0,Unknown,1,Troll,2,Elf,3,Human,4,Dwarf",
	"age"=>"Days since level 1,int",
	"dragonkills"=>"How many times has slain the dragon,int",
	"dragonage"=>"How old when last killed dragon,int",
	"bestdragonage"=>"Youngest days when killed dragon,int",
	"bio"=>"Bio",
	
	"Stats,title",
	"level"=>"Level,int",
	"experience"=>"Experience,int",
	"hitpoints"=>"Current Hitpoints,int",
	"maxhitpoints"=>"Max Hitpoints,int",
	"turns"=>"Turns left,int",
	"playerfights"=>"Playerfights left,int",
	"attack"=>"Attack (includes weapon damage),int",
	"defence"=>"Defense (includes armor defense),int",
	"spirits"=>"Spirits (display only),enum,-2,Very Low,-1,Low,0,Normal,1,High,2,Very High",
	"resurrections"=>"Resurrections,int",
	
	"Specialty,title",
	"specialty"=>"Specialty,enum,0,Unspecified,1,Dark Arts,2,Mystical Powers,3,Thievery",
	"darkarts"=>"`4Skill in Dark Arts`0,int",
	"darkartuses"=>"`4^--uses today`0,int",
	"magic"=>"`%Skill in Mystical Powers`0,int",
	"magicuses"=>"`%^--uses today`0,int",
	"thievery"=>"`^Skill in Thievery`0,int",
	"thieveryuses"=>"`^^--uses today`0,int",

	"Grave Fights,title",
	"deathpower"=>"Favor with Ramius,int",
	"gravefights"=>"Grave fights left,int",
	"soulpoints"=>"Soulpoints (HP while dead),int",
	
	"Gear,title",
	"gems"=>"Gems,int",
	"gold"=>"Gold in hand,int",
	"goldinbank"=>"Gold in bank,int",
	"transferredtoday"=>"Number of transfers today,int",
	"amountouttoday"=>"Total value of transfers from player today,int",
	"weapon"=>"Weapon Name",
	"weapondmg"=>"Damage of weapon,int",
	"weaponvalue"=>"Purchase cost of weapon,int",
	"armor"=>"Armor Name",
	"armordef"=>"Armor defense,int",
	"armorvalue"=>"Purchase cost of armor,int",
	
	"Special,title",
	"seenlover"=>"Seen lover,bool",
	"seenbard"=>"Heard bard,bool",
	"charm"=>"Charm,int",
	"seendragon"=>"Saw dragon today,bool",
	"seenmaster"=>"Seen master,bool",
	"usedouthouse"=>"Used Outhouse Today,bool",
	"hashorse"=>"Mount,enum$mounts",
	"boughtroomtoday"=>"Bought a room today,bool",
	"drunkenness"=>"Drunkenness (0-100),int",
	
	"Misc Info,title",
	"beta"=>"Willing to participate in beta,viewonly",
	"slainby"=>"Killed by player,viewonly",
	"laston"=>"Last On,viewonly",
	"lasthit"=>"Last New Day,viewonly",
	"lastmotd"=>"Last MOTD date,viewonly",
	"lastip"=>"Last IP,viewonly",
	"uniqueid"=>"Unique ID,viewonly",
	"gentime"=>"Sum of page gen times,viewonly",
	"gentimecount"=>"Page hits,viewonly",
	"allowednavs"=>"Allowed Navs,viewonly",
	"dragonpoints"=>"Dragon points spent,viewonly",
	"bufflist"=>"Buff List,viewonly",
	"prefs"=>"Preferences,viewonly",
	"lastwebvote"=>"Last time voted at Top Web Games,viewonly",
	"donationconfig"=>"Donation buys,viewonly"
	);

if ($_GET[op]=="lasthit"){
	$output="";
	$sql = "SELECT output FROM accounts WHERE acctid='{$_GET['userid']}'";
	$result = db_query($sql);
	$row = db_fetch_assoc($result);
	echo str_replace("<iframe src=","<iframe Xsrc=",$row['output']);
	exit();
}elseif ($_GET[op]=="edit"){
	$result = db_query("SELECT * FROM accounts WHERE acctid='$_GET[userid]'") or die(db_error(LINK));
	$row = db_fetch_assoc($result) or die(db_error(LINK));
	output("<form action='user.php?op=special&userid=$_GET[userid]".($_GET['returnpetition']!=""?"&returnpetition={$_GET['returnpetition']}":"")."' method='POST'>",true);
	addnav("","user.php?op=special&userid=$_GET[userid]".($_GET['returnpetition']!=""?"&returnpetition={$_GET['returnpetition']}":"")."");
	output("<input type='submit' class='button' name='newday' value='Grant New Day'>",true);
	output("<input type='submit' class='button' name='fixnavs' value='Fix Broken Navs'>",true);
	output("<input type='submit' class='button' name='clearvalidation' value='Mark Email As Valid'>",true);
	output("</form>",true);

	if ($_GET['returnpetition']!=""){
		addnav("Return to the petition","viewpetition.php?op=view&id={$_GET['returnpetition']}");
	}
	
	addnav("View last page hit","user.php?op=lasthit&userid={$_GET['userid']}",false,true);
	output("<form action='user.php?op=save&userid=$_GET[userid]".($_GET['returnpetition']!=""?"&returnpetition={$_GET['returnpetition']}":"")."' method='POST'>",true);
	addnav("","user.php?op=save&userid=$_GET[userid]".($_GET['returnpetition']!=""?"&returnpetition={$_GET['returnpetition']}":"")."");
	addnav("","user.php?op=edit&userid=$_GET[userid]".($_GET['returnpetition']!=""?"&returnpetition={$_GET['returnpetition']}":"")."");
	addnav("Set up ban","user.php?op=setupban&userid=$row[acctid]");
	addnav("Display debug log","user.php?op=debuglog&userid={$_GET['userid']}");
	output("<input type='submit' class='button' value='Save'>",true);
	showform($userinfo,$row);
	output("</form>",true);
	output("<iframe src='user.php?op=lasthit&userid={$_GET['userid']}' width='100%' height='400'>You need iframes to view the user's last hit here.  Use the link in the nav instead.</iframe>",true);
	addnav("","user.php?op=lasthit&userid={$_GET['userid']}");
}elseif ($_GET[op]=="special"){
	if ($_POST[newday]!=""){
		$sql = "UPDATE accounts SET lasthit='".date("Y-m-d H:i:s",strtotime("-".(86500/getsetting("daysperday",4))." seconds"))."' WHERE acctid='$_GET[userid]'";
	}elseif($_POST[fixnavs]!=""){
		$sql = "UPDATE accounts SET allowednavs='',output=\"\" WHERE acctid='$_GET[userid]'";
	}elseif($_POST[clearvalidation]!=""){
		$sql = "UPDATE accounts SET emailvalidation='' WHERE acctid='$_GET[userid]'";
	}

	db_query($sql);
	if ($_GET['returnpetition']==""){
		redirect("user.php?".db_affected_rows());
	}else{
		redirect("viewpetition.php?op=view&id={$_GET['returnpetition']}");
	}
}elseif ($_GET[op]=="save"){
	$sql = "UPDATE accounts SET ";
	reset($_POST);
	while (list($key,$val)=each($_POST)){
		if (isset($userinfo[$key])){
			if ($key=="newpassword" ){
				if ($val>"") $sql.="password = \"$val\",";
			}else{
				$sql.="$key = \"$val\",";
			}
		}
	}
	$sql=substr($sql,0,strlen($sql)-1);
	$sql.=" WHERE acctid=\"$_GET[userid]\"";
	//output("<pre>$sql</pre>");
	//echo "<pre>$sql</pre>";
	//redirect("user.php");
	//output( db_affected_rows()." rows affected");
	
	//we must manually redirect so that our changes go in to effect *after* our user save.
	addnav("","viewpetition.php?op=view&id={$_GET['returnpetition']}");
	addnav("","user.php");
	saveuser();
	db_query($sql) or die(db_error(LINK));
	if ($_GET['returnpetition']!=""){
		header("Location: viewpetition.php?op=view&id={$_GET['returnpetition']}");
	}else{
		header("Location: user.php");
	}

	exit();
}elseif ($_GET[op]=="del"){
	$sql = "SELECT name from accounts WHERE acctid='$_GET[userid]'";
	$res = db_query($sql);
	$sql = "DELETE FROM accounts WHERE acctid='$_GET[userid]'";
	db_query($sql);
	output( db_affected_rows()." user deleted.");
	while ($row = db_fetch_assoc($res)) {
		addnews("`#{$row['name']} was unmade by the gods.");
	}
}elseif($_GET[op]=="setupban"){
	$sql = "SELECT name,lastip,uniqueid FROM accounts WHERE acctid=\"$_GET[userid]\"";
	$result = db_query($sql) or die(db_error(LINK));
	$row = db_fetch_assoc($result);
	if ($row[name]!="") output("Setting up ban information based on `\$$row[name]`0");
	output("<form action='user.php?op=saveban' method='POST'>",true);
	output("Set up a new ban by IP or by ID (recommended IP, though if you have several different users behind a NAT, you can try ID which is easily defeated)`n");
	output("<input type='radio' value='ip' name='type' checked> IP: <input name='ip' value=\"".HTMLEntities($row[lastip])."\">`n",true);
	output("<input type='radio' value='id' name='type'> ID: <input name='id' value=\"".HTMLEntities($row[uniqueid])."\">`n",true);
	output("Duration: <input name='duration' id='duration' size='3' value='14'> days (0 for permanent)`n",true);
	output("Reason for the ban: <input name='reason' value=\"Don't mess with me.\">`n",true);
	output("<input type='submit' class='button' value='Post Ban' onClick='if (document.getElementById(\"duration\").value==0) {return confirm(\"Are you sure you wish to issue a permanent ban?\");} else {return true;}'></form>",true);
	output("For an IP ban, enter the beginning part of the IP you wish to ban if you wish to ban a range, or simply a full IP to ban a single IP");
	addnav("","user.php?op=saveban");
}elseif($_GET[op]=="saveban"){
	$sql = "INSERT INTO bans (";
	if ($_POST[type]=="ip"){
		$sql.="ipfilter";
	}else{
		$sql.="uniqueid";
	}
	$sql.=",banexpire,banreason) VALUES (";
	if ($_POST[type]=="ip"){
		$sql.="\"$_POST[ip]\"";
	}else{
		$sql.="\"$_POST[id]\"";
	}
	$sql.=",\"".((int)$_POST[duration]==0?"0000-00-00":date("Y-m-d",strtotime("+$_POST[duration] days")))."\",";
	$sql.="\"$_POST[reason]\")";
	if ($_POST[type]=="ip"){
		if (substr($_SERVER['REMOTE_ADDR'],0,strlen($_POST[ip])) == $_POST[ip]){
			$sql = "";
			output("You don't really want to ban yourself now do you??  That's your own IP address!");
		}
	}else{
		if ($_COOKIE[lgi]==$_POST[id]){
			$sql = "";
			output("You don't really want to ban yourself now do you??  That's your own ID!");
		}
	}
	if ($sql!=""){
		db_query($sql) or die(db_error(LINK));
		output(db_affected_rows()." ban row entered.`n`n");
		output(db_error(LINK));
	}
}elseif($_GET[op]=="delban"){
	$sql = "DELETE FROM bans WHERE ipfilter = '$_GET[ipfilter]' AND uniqueid = '$_GET[uniqueid]'";
	db_query($sql);
	//output($sql);
	redirect("user.php?op=removeban");
}elseif($_GET[op]=="removeban"){	
	db_query("DELETE FROM bans WHERE banexpire < \"".date("Y-m-d")."\" AND banexpire>'0000-00-00'");
	
	$sql = "SELECT * FROM bans ORDER BY banexpire";
	$result = db_query($sql) or die(db_error(LINK));
	output("<table><tr><td>Ops</td><td>IP/ID</td><td>Duration</td><td>Message</td><td>Affects:</td></tr>",true);
	for ($i=0;$i<db_num_rows($result);$i++){
		$row = db_fetch_assoc($result);
		output("<tr class='".($i%2?"trlight":"trdark")."'><td><a href='user.php?op=delban&ipfilter=".URLEncode($row[ipfilter])."&uniqueid=".URLEncode($row[uniqueid])."'>Lift&nbsp;ban</a>",true);
		addnav("","user.php?op=delban&ipfilter=".URLEncode($row[ipfilter])."&uniqueid=".URLEncode($row[uniqueid]));
		output("</td><td>",true);
		output($row[ipfilter]);
		output($row[uniqueid]);
		output("</td><td>",true);
		$expire=round((strtotime($row[banexpire])-strtotime("now")) / 86400,0)." days";
		if (substr($expire,0,2)=="1 ") $expire="1 day";
		if (date("Y-m-d",strtotime($row[banexpire])) == date("Y-m-d")) $expire="Today";
		if (date("Y-m-d",strtotime($row[banexpire])) == date("Y-m-d",strtotime("1 day"))) $expire="Tomorrow";
		if ($row[banexpire]=="0000-00-00") $expire="Never";
		output($expire);
		output("</td><td>",true);
		output($row[banreason]);
		output("</td><td>",true);
		$sql = "SELECT DISTINCT accounts.name FROM bans, accounts WHERE (ipfilter='".addslashes($row['ipfilter'])."' AND bans.uniqueid='".addslashes($row['uniqueid'])."') AND ((substring(accounts.lastip,1,length(ipfilter))=ipfilter AND ipfilter<>'') OR (bans.uniqueid=accounts.uniqueid AND bans.uniqueid<>''))";
		$r = db_query($sql);
		for ($x=0;$x<db_num_rows($r);$x++){
			$ro = db_fetch_assoc($r);
			output("`0{$ro['name']}`n");
		}
		output("</td></tr>",true);
	}
	output("</table>",true);
}elseif ($_GET[op]=="debuglog"){
	$id = $_GET['userid'];
	$sql = "SELECT debuglog.*,a1.name as actorname,a2.name as targetname FROM debuglog LEFT JOIN accounts as a1 ON a1.acctid=debuglog.actor LEFT JOIN accounts as a2 ON a2.acctid=debuglog.target WHERE debuglog.actor=$id OR debuglog.target=$id ORDER by debuglog.date DESC,debuglog.id ASC LIMIT 500";
	addnav("Edit user info","user.php?op=edit&userid=$id");
	$result = db_query($sql);
	$odate = "";
	for ($i=0; $i<db_num_rows($result); $i++) {
		$row = db_fetch_assoc($result);
		$dom = date("D, M d",strtotime($row['date']));
		if ($odate != $dom){
			output("`n`b`@".$dom."`b`n");
			$odate = $dom;
		}
		$time = date("H:i:s", strtotime($row['date']));
		output("$time - {$row['actorname']} {$row['message']}");
		if ($row['target']) output(" {$row['targetname']}");
		output("`n");
	}
}elseif ($_GET[op]==""){
	if (isset($_GET['page'])){
		$order = "acctid";
		if ($_GET[sort]!="") $order = "$_GET[sort]";
		$offset=(int)$_GET['page']*100;
		$sql = "SELECT acctid,login,name,level,laston,gentimecount,lastip,uniqueid,emailaddress FROM accounts ".($where>""?"WHERE $where ":"")."ORDER BY \"$order\" LIMIT $offset,100";
		$result = db_query($sql) or die(db_error(LINK));
		output("<table>",true);
		output("<tr>
		<td>Ops</td>
		<td><a href='user.php?sort=login'>Login</a></td>
		<td><a href='user.php?sort=name'>Name</a></td>
		<td><a href='user.php?sort=level'>Lev</a></td>
		<td><a href='user.php?sort=laston'>Laston</a></td>
		<td><a href='user.php?sort=gentimecount'>Hits</a></td>
		<td><a href='user.php?sort=lastip'>LastIP</a></td>
		<td><a href='user.php?sort=uniqueid'>LastID</a></td>
		<td><a href='user.php?sort=emailaddress'>Email</a></td>
		</tr>",true);
		addnav("","user.php?sort=login");
		addnav("","user.php?sort=name");
		addnav("","user.php?sort=level");
		addnav("","user.php?sort=laston");
		addnav("","user.php?sort=gentimecount");
		addnav("","user.php?sort=lastip");
		addnav("","user.php?sort=uniqueid");
		$rn=0;
		for ($i=0;$i<db_num_rows($result);$i++){
			$row=db_fetch_assoc($result);
			$laston=round((strtotime("0 days")-strtotime($row[laston])) / 86400,0)." days";
			if (substr($laston,0,2)=="1 ") $laston="1 day";
			if (date("Y-m-d",strtotime($row[laston])) == date("Y-m-d")) $laston="Today";
			if (date("Y-m-d",strtotime($row[laston])) == date("Y-m-d",strtotime("-1 day"))) $laston="Yesterday";
			if ($loggedin) $laston="Now";
			$row[laston]=$laston;
			if ($row[$order]!=$oorder) $rn++;
			$oorder = $row[$order];
			output("<tr class='".($rn%2?"trlight":"trdark")."'>",true);
			
			output("<td>",true);
			output("[<a href='user.php?op=edit&userid=$row[acctid]'>Edit</a>|".
				"<a href='user.php?op=del&userid=$row[acctid]' onClick=\"return confirm('Are you sure you wish to delete this user?');\">Del</a>|".
				"<a href='user.php?op=setupban&userid=$row[acctid]'>Ban</a>|".
				"<a href='user.php?op=debuglog&userid=$row[acctid]'>Log</a>]",true);
			addnav("","user.php?op=edit&userid=$row[acctid]");
			addnav("","user.php?op=del&userid=$row[acctid]");
			addnav("","user.php?op=setupban&userid=$row[acctid]");
			addnav("","user.php?op=debuglog&userid=$row[acctid]");
			output("</td><td>",true);
			output($row[login]);
			output("</td><td>",true);
			output($row[name]);
			output("</td><td>",true);
			output($row[level]);
			output("</td><td>",true);
			output($row[laston]);
			output("</td><td>",true);
			output($row[gentimecount]);
			output("</td><td>",true);
			output($row[lastip]);
			output("</td><td>",true);
			output($row[uniqueid]);
			output("</td><td>",true);
			output($row[emailaddress]);
			output("</td>",true);
			$gentimecount+=$row[gentimecount];
			$gentime+=$row[gentime];
	
			output("</tr>",true);
		}
		output("</table>",true);
		output("Total hits: $gentimecount`n");
		output("Total CPU time: ".round($gentime,3)."s`n");
		output("Average page gen time is ".round($gentime/max($gentimecount,1),4)."s`n");
	}
}
page_footer();
?>
