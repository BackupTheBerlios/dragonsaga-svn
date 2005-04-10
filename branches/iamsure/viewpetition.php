<?php
require_once "common.php";
isnewday(2);
addcommentary();

$statuses=array(0=>"`bUnseen`b","Seen","Closed");

if (!isset($_GET['op']))
{
    $_GET['op'] = '';
}
if (!isset($_GET['setstat']))
{
    $_GET['setstat'] = '';
}
if ($_GET['op']=="del"){
	$sql = "DELETE FROM petitions WHERE petitionid='$_GET[id]'";
	db_query($sql);
	$sql = "DELETE FROM commentary WHERE section='pet-{$_GET['id']}'";
	db_query($sql);
	$_GET['op']="";
}
page_header("Petition Viewer");
addnav("G?Return to the Grotto","superuser.php");
addnav("M?Return to the Mundane","village.php");
if ($_GET['op']==""){
	$sql = "DELETE FROM petitions WHERE status=2 AND date<'".date("Y-m-d H:i:s",strtotime("-7 days"))."'";
	db_query($sql);
	if ($_GET['setstat']!=""){
		$sql = "UPDATE petitions SET status='{$_GET['setstat']}' WHERE petitionid='{$_GET['id']}'";
		db_query($sql);
		//output($sql);
	}
	$sql = "SELECT petitionid,accounts.name,petitions.date,petitions.status,petitions.body FROM petitions LEFT JOIN accounts ON accounts.acctid=petitions.author ORDER BY status ASC, date ASC";
	$result = db_query($sql);
	addnav("Refresh","viewpetition.php");
	output("<table border='0'><tr class='trhead'><td>Num</td><td>Ops</td><td>From</td><td>Sent</td><td>Status</td><td>Comments</td></tr>",true);
	for ($i=0;$i<db_num_rows($result);$i++){
		$row = db_fetch_assoc($result);
		$sql = "SELECT count(commentid) AS c FROM commentary WHERE section='pet-{$row['petitionid']}'";
		$res = db_query($sql);
		$counter = db_fetch_assoc($res);
		output("<tr class='".($i%2?"trlight":"trdark")."'><td>{$row['petitionid']}</td><td>[<a href='viewpetition.php?op=view&id={$row['petitionid']}'>View</a>|<a href='viewpetition.php?op=del&id={$row['petitionid']}' onClick='return confirm(\"Are you sure you wish to delete this petition?\");'>Del</a>|<a href='viewpetition.php?setstat=0&id={$row['petitionid']}'>Unseen</a>|<a href='viewpetition.php?setstat=1&id={$row['petitionid']}'>Seen</a>|<a href='viewpetition.php?setstat=2&id={$row['petitionid']}'>Close</a>]</td>",true);
		output("<td>",true);
		if ($row['name']==""){
			output(preg_replace("'[^a-zA-Z0-91234567890\\[\\]= @.!,?-]'","",substr($row['body'],0,strpos($row['body'],"[email"))));
		}else{
			output($row['name']);
		}
		output("</td><td>{$row['date']}</td><td>{$statuses[$row['status']]}</td><td>{$counter['c']}</td></tr>",true);
		addnav("","viewpetition.php?op=view&id=$row[petitionid]");
		addnav("","viewpetition.php?op=del&id=$row[petitionid]");
		addnav("","viewpetition.php?setstat=0&id=$row[petitionid]");
		addnav("","viewpetition.php?setstat=1&id=$row[petitionid]");
		addnav("","viewpetition.php?setstat=2&id=$row[petitionid]");
	}
	output("</table>",true);
	output("`i(Closed petitions will automatically delete themselves when they are 7 days old)`i");
	output("`n`bKey:`b`nUnseen: No one is currently working on this problem, and it has not been dealt with yet.
	`nSeen: Someone is probably currently working on this issue.
	`nClosed: This issue has been dealt with, no further work should be necessary.`n`n
	When viewing a petition, it is automatically marked as seen unless it was marked as closed. 
	If you cannot deal with a problem right away, please mark it unseen again so that someone else
	can help the player.`n
	Petitions that are marked \"Seen\" are probably being worked on by someone else, so please leave them
	be unless they have been around for some time (in which case someone probably forgot to mark them unseen
	after they looked at it).`n
	If you have dealt with an issue, mark it closed, and it will auto delete when it is 7 days old.");
}elseif($_GET['op']=="view"){
	if ($_GET['viewpageinfo']==1){
		addnav("Hide Details","viewpetition.php?op=view&id={$_GET['id']}");
	}else{
		addnav("D?Show Details","viewpetition.php?op=view&id={$_GET['id']}&viewpageinfo=1");
	}
	addnav("Petition Viewer","viewpetition.php");

	addnav("Petition Ops");
	addnav("Close Petition","viewpetition.php?setstat=2&id=$_GET[id]");
	addnav("U?Mark Unseen","viewpetition.php?setstat=0&id=$_GET[id]");
	addnav("S?Mark Seen","viewpetition.php?setstat=1&id=$_GET[id]");
	
	$sql = "SELECT accounts.name,accounts.login,accounts.acctid,petitions.date,petitions.status,petitionid,body,pageinfo FROM petitions LEFT JOIN accounts ON accounts.acctid=petitions.author WHERE petitionid='$_GET[id]' ORDER BY date ASC";
	$result = db_query($sql);
	$row = db_fetch_assoc($result);
	if ($row['acctid']>0){
		addnav("Edit User Record","user.php?op=edit&userid={$row['acctid']}&returnpetition={$_GET['id']}");
	}
	output("`@From: ");
	$row[body]=stripslashes($row[body]);
	if ($row['login']>"") output("<a href=\"mail.php?op=write&to=".rawurlencode($row[login])."&body=".URLEncode("\n\n----- Your Petition -----\n".$row[body])."&subject=RE:+Petition\" target=\"_blank\" onClick=\"".popup("mail.php?op=write&to=".rawurlencode($row[login])."&body=".URLEncode("\n\n----- Your Petition -----\n".$row[body])."&subject=RE:+Petition").";return false;\"><img src='images/newscroll.png' width='16' height='16' alt='Write Mail' border='0'></a>",true);
	output("`^`b$row[name]`b`n");
	output("`@Date: `^`b$row[date]`b`n");
	output("`@Body:`^`n");
	$body = HTMLEntities($row[body]);
	$body = preg_replace("'([[:alnum:]_.-]+[@][[:alnum:]_.-]{2,}([.][[:alnum:]_.-]{2,})+)'i","<a href='mailto:\\1?subject=RE: Petition&body=".str_replace("+"," ",URLEncode("\n\n----- Your Petition -----\n".$row[body]))."'>\\1</a>",$body);
	$body = preg_replace("'([\\[][[:alnum:]_.-]+[\\]])'i","<span class='colLtRed'>\\1</span>",$body);
	$output.="<span style='font-family: fixed-width'>".nl2br($body)."</span>";
	output("`n`@Commentary:`n");
	viewcommentary("pet-{$_GET['id']}","Add",200);
	if ($_GET['viewpageinfo']){
		output("`n`n`@Page Info:`&`n");
		$row[pageinfo]=stripslashes($row[pageinfo]);
		$body = HTMLEntities($row[pageinfo]);
		$body = preg_replace("'([[:alnum:]_.-]+[@][[:alnum:]_.-]{2,}([.][[:alnum:]_.-]{2,})+)'i","<a href='mailto:\\1?subject=RE: Petition&body=".str_replace("+"," ",URLEncode("\n\n----- Your Petition -----\n".$row[body]))."'>\\1</a>",$body);
		$body = preg_replace("'([\\[][[:alnum:]_.-]+[\\]])'i","<span class='colLtRed'>\\1</span>",$body);
		$output.="<span style='font-family: fixed-width'>".nl2br($body)."</span>";
	}	
	if ($row[status]==0) {
		$sql = "UPDATE petitions SET status=1 WHERE petitionid='$_GET[id]'";
		$result = db_query($sql);
	}
}
page_footer();
?>
