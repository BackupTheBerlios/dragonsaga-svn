<?php
require_once "common.php";

isnewday(2);

if ($_GET['op']=="block"){
    $sql = "UPDATE accounts SET bio='`iBlocked for inappropriate usage`i',biotime='9999-12-31 23:59:59' WHERE acctid='{$_GET['userid']}'";
	systemmail($_GET['userid'],"Your bio has been blocked","The system administrators have decided that your bio entry is inappropriate, so it has been blocked.`n`nIf you wish to appeal this decision, you may do so with the petition link.");
	db_query($sql);
}
if ($_GET['op']=="unblock"){
	$sql = "UPDATE accounts SET bio='',biotime='0000-00-00 00:00:00' WHERE acctid='{$_GET['userid']}'";
	systemmail($_GET['userid'],"Your bio has been unblocked","The system administrators have decided to unblock your bio.  You can once again enter a bio entry.");
	db_query($sql);
}
$sql = "SELECT name,acctid,bio,biotime FROM accounts WHERE biotime<'9999-12-31' AND bio>'' ORDER BY biotime DESC LIMIT 100";
$result = db_query($sql);
page_header("User Bios");
output("`b`&Player Bios:`0`b`n");
for ($i=0;$i<db_num_rows($result);$i++){
    $row = db_fetch_assoc($result);
    if ($row['biotime']>$session['user']['recentcomments'])
        output("<img src='images/new.png' alt='&gt;' width='3' height='5' align='middle'> ",true);
    output("`![<a href='bios.php?op=block&userid={$row['acctid']}'>Block</a>]",true);
    addnav("","bios.php?op=block&userid={$row['acctid']}");
    output("`&".$row['name'].": `^".soap($row['bio'])."`n");
}
db_free_result($result);
addnav("G?Return to the Grotto","superuser.php");
addnav("M?Return to the Mundane","village.php");
addnav("Refresh","bios.php");
$sql = "SELECT name,acctid,bio,biotime FROM accounts WHERE biotime>'9000-01-01' AND bio>'' ORDER BY biotime DESC LIMIT 100";
$result = db_query($sql);
output("`n`n`b`&Blocked Bios:`0`b`n");
for ($i=0;$i<db_num_rows($result);$i++){
    $row = db_fetch_assoc($result);
    output("`![<a href='bios.php?op=unblock&userid={$row['acctid']}'>Unblock</a>]",true);
    addnav("","bios.php?op=unblock&userid={$row['acctid']}");
    output("`&{$row['name']}: `^".soap($row['bio'])."`n");
}
db_free_result($result);
page_footer();
?>
