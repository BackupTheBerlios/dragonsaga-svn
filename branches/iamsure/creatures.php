<?php
require_once "common.php";
isnewday(2);

//select distinct creaturelevel,max(creaturehealth) as creaturehealth,max(creatureattack) as creatureattack,max(creaturedefense) as creaturedefense,max(creatureexp) as creatureexp,max(creaturegold) as creaturegold from creatures where creaturelevel<17 group by creaturelevel;
$creaturestattable="
+---------------+----------------+----------------+-----------------+-------------+--------------+
| creaturelevel | creaturehealth | creatureattack | creaturedefense | creatureexp | creaturegold |
+---------------+----------------+----------------+-----------------+-------------+--------------+
|             1 |             10 |              1 |               1 |          14 |           36 |
|             2 |             21 |              3 |               3 |          24 |           97 |
|             3 |             32 |              5 |               4 |          34 |          148 |
|             4 |             43 |              7 |               6 |          45 |          162 |
|             5 |             53 |              9 |               7 |          55 |          198 |
|             6 |             64 |             11 |               8 |          66 |          234 |
|             7 |             74 |             13 |              10 |          77 |          268 |
|             8 |             84 |             15 |              11 |          89 |          302 |
|             9 |             94 |             17 |              13 |         101 |          336 |
|            10 |            105 |             19 |              14 |         114 |          369 |
|            11 |            115 |             21 |              15 |         127 |          402 |
|            12 |            125 |             23 |              17 |         141 |          435 |
|            13 |            135 |             25 |              18 |         156 |          467 |
|            14 |            145 |             27 |              20 |         172 |          499 |
|            15 |            155 |             29 |              21 |         189 |          531 |
|            16 |            166 |             31 |              22 |         207 |          563 |
+---------------+----------------+----------------+-----------------+-------------+--------------+
";
$creaturestats=Array();
$creaturestattable=split("\n",$creaturestattable);
$x=0;
while (list($key,$val)=each($creaturestattable)){
	if (strpos($val,"|")!==false){
		//echo("$val`n");
		$x++;
		$a = split("\\|",$val);
		if ($x==1){
			$stats=array();
			while (list($key1,$val1)=each($a)){
				if (trim($val1)>"") {
					$stats[$key1]=trim($val1);
					//output($val1." is col $key1`n");
				}
			}
		}else{
			reset($stats);
			while (list($key1,$val1)=each($stats)){
				$creaturestats[(int)$a[1]][$val1]=trim($a[$key1]);
				//output ("[".(int)$a[1]."][$val1]=".trim($a[$key1])."`n");
			}
		}
	}
}

page_header("Creature Editor");

if ($session['user']['superuser'] >= 2){
	addnav("G?Return to the Grotto","superuser.php");
	addnav("M?Return to the Mundane","village.php");
	if ($_POST['save']<>""){
		if (!isset($_POST['location'])) $_POST['location']=0;
		if ($_POST['id']!=""){
			$sql="UPDATE creatures SET ";
			//unset($_POST[save]);
			while (list($key,$val)=each($_POST)){
				if (substr($key,0,8)=="creature") $sql.="$key = \"$val\", ";
			}
			reset($creaturestats[(int)$_POST['creaturelevel']]);
			while (list($key,$val)=each($creaturestats[$_POST['creaturelevel']])){
				if ( $key!="creaturelevel" && substr($key,0,8)=="creature"){
					$sql.="$key = \"".addslashes($val)."\", ";
				}
			}
			$sql.=" location=\"".(int)($_POST['location'])."\", ";
			//$sql = substr($sql,0,strlen($sql)-2);
			$sql.= " createdby=\"".addslashes($session['user']['login'])."\" ";
			$sql.= " WHERE creatureid='$_POST[id]'";
			//output($sql);
			db_query($sql) or output("`\$".db_error(LINK)."`0`n`#$sql`0`n");
			output(db_affected_rows()." record changed.");
		}else{
			$cols = array();
			$vals = array();
			
			while (list($key,$val)=each($_POST)){
				if (substr($key,0,8)=="creature" || $key=="location") {
					array_push($cols,$key);
					array_push($vals,$val);
					//$sql.="$key = \"$val\", ";
				}
			}
			reset($creaturestats[(int)$_POST['creaturelevel']]);
			while (list($key,$val)=each($creaturestats[$_POST['creaturelevel']])){
				if ($key!="creaturelevel"){
					//$sql.="$key = \"".addslashes($val)."\", ";
					array_push($cols,$key);
					array_push($vals,$val);
				}
			}
			$sql="INSERT INTO creatures (".join(",",$cols).",createdby) VALUES (\"".join("\",\"",$vals)."\",\"".addslashes($session['user']['login'])."\")";
			//echo $sql;
			db_query($sql);
		}
	}
	if ($_GET['op']=="del"){
		$sql = "DELETE FROM creatures WHERE creatureid='".$_GET['id']."'";
		db_query($sql);
		if (db_affected_rows()>0){
			output("Creature deleted`n`n");
		}else{
			output("Creature not deleted: ".db_error(LINK));
		}
		$_GET['op']="";
	}
	if ($_GET['op']==""){
		$sql = "SELECT * FROM creatures ORDER BY creaturelevel,creaturename";
		$result = db_query($sql) or die(db_error(LINK));
		addnav("Add a creature","creatures.php?op=add");
		output("<table><tr><td>Ops</td><td>Creature Name</td><td>Creature Level</td><td>Weapon</td><td>Die</td><td>Author</td></tr>",true);
		addnav("","creatures.php");
		for ($i=0;$i<db_num_rows($result);$i++){
			$row = db_fetch_assoc($result);
			if ($row['creaturelevel']==17 || $row['creaturelevel']==18){
				output("<tr><td> [Edit|Del] </td><td>",true);
			}else{
				output("<tr><td> [<a href='creatures.php?op=edit&id='".$row['creatureid']."'>Edit</a>|".
				"<a href='creatures.php?op=del&id='".$row['creatureid']."' onClick='return confirm(\"Are you sure you wish to delete this creature?\");'>Del</a>] </td><td>",true);
				addnav("","creatures.php?op=edit&id='".$row['creatureid']."'");
				addnav("","creatures.php?op=del&id='".$row['creatureid']."'");
			}
			output($row['creaturename']);
			output("</td><td>",true);
			output($row['creaturelevel']);
			output("</td><td>",true);
			output($row['creatureweapon']);
			output("</td><td>",true);
			output($row['creaturelose']);
			output("</td><td>",true);
			output($row['createdby']);
			output("</td></tr>",true);
		}
		output("</table>",true);
	}else{
		if ($_GET['op']=="edit" || $_GET['op']=="add"){
			if ($_GET['op']=="edit"){
				$sql = "SELECT * FROM creatures WHERE creatureid='".$_GET['id']."'";
				$result = db_query($sql) or die(db_error(LINK));
				if (db_num_rows($result)<>1){
					output("`4Error`0, that creature was not found!");
				}else{
					$row = db_fetch_assoc($result);
				}
			}
			output("<form action='creatures.php' method='POST'>",true);
			output("<input name='id' value=\"".HTMLEntities($_GET[id])."\" type='hidden'>",true);
			output("<table border='0' cellpadding='2' cellspacing='0'>",true);
			output("<tr><td>Creature Name:</td><td><input name='creaturename' maxlength='50' value='".HTMLEntities($row['creaturename'])."'></td></tr>",true);
			output("<tr><td>Weapon: </td><td><input name='creatureweapon' maxlength='50' value='".HTMLEntities($row['creatureweapon'])."'></td></tr>",true);
			output("<tr><td colspan='2'>Death Message: <br><input name='creaturelose' size='65' maxlength='120' value='".HTMLEntities($row['creaturelose'])."'></td></tr>",true);
			output("<tr><td>Level: </td><td><select name='creaturelevel'>",true);
			for ($i=1;$i<=16;$i++){
				output("<option value='$i'".($row['creaturelevel']==$i?" selected":"").">$i</option>\n",true);
			}
			output("</select></td></tr>",true);
			output("<tr><td>Creature is also in Graveyard</td><td><input type='radio' name='location' value='1'".($row['location']==1?" checked":"").">Yes <input type='radio' name='location' value='0'".($row['location']==0?" checked":"").">No </td></tr>",true);
			output("<tr><td colspan='2'><input type='hidden' name='save' value='Save'><input type='submit' class='button' name='submit' value='Save'></td></tr>",true);
			output("</table>",true);
			output("</form>",true);
			addnav("","creatures.php");
		}else{
		
		}
		addnav("Return to the creature editor","creatures.php");
	}
}else{
	output("For attempting to defile the gods, you have been smitten down!");
	addnews("`&".$session['user']['name']." was smote down for attempting to defile the gods (they tried to hack superuser pages).");
	$session['user']['hitpoints']=0;
}
page_footer();
?>
