<?php
require_once "common.php";
page_header("Hall of Fame");
checkday();
$sql = "SELECT name,dragonkills,level,dragonage,bestdragonage FROM accounts WHERE dragonkills > 0 ORDER BY dragonkills DESC,level DESC,experience DESC";
output("`c`b`&Heroes of the realm`b`c");
output("<table cellspacing=0 cellpadding=2 align='center'><tr><td>`bRank`b</td><td>`bName`b</td><td>`bKills`b</td><td>`bLevel`b</td><td>&nbsp;</td><td>`bDays`b</td><td>&nbsp;</td><td>`bBest Days`b</td></tr>",true);
$result = db_query($sql) or die(db_error(LINK));
if (db_num_rows($result)==0){
  output("<tr><td colspan=4 align='center'>`&There are no heroes in the land`0</td></tr>",true);
}
for ($i=0;$i<db_num_rows($result);$i++){
    $row = db_fetch_assoc($result);
  output("<tr class='".($i%2?"trlight":"trdark")."'><td>".($i+1).".</td><td>".$row['name']."</td><td>".$row['dragonkills']."</td><td>".$row['level']."</td><td>&nbsp;</td><td>".($row['dragonage']?$row['dragonage']:"unknown")."</td><td>&nbsp;</td><td>".($row['bestdragonage']?$row['bestdragonage']:"unknown")."</td></tr>",true);
}
output("</table>",true);
addnav("Return to the village","village.php");
page_footer();
?>
