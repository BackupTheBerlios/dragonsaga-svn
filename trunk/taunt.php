<?
require_once "common.php";
isnewday(2);

page_header("Taunt Editor");
addnav("G?Return to the Grotto","superuser.php");
addnav("M?Return to the Mundane","village.php");
if ($_GET[op]=="edit"){
	addnav("Return to the taunt editor","taunt.php");
	output("<form action='taunt.php?op=save&tauntid=$_GET[tauntid]' method='POST'>",true);
	addnav("","taunt.php?op=save&tauntid=$_GET[tauntid]");
	if ($_GET[tauntid]!=""){
		$sql = "SELECT * FROM taunts WHERE tauntid=\"$_GET[tauntid]\"";
		$result = db_query($sql) or die(db_error(LINK));
		$row = db_fetch_assoc($result);
		$taunt = $row[taunt];
		$taunt = str_replace("%s","him",$taunt);
		$taunt = str_replace("%o","he",$taunt);
		$taunt = str_replace("%p","his",$taunt);
		$taunt = str_replace("%x","Pointy Twig",$taunt);
		$taunt = str_replace("%X","Sharp Teeth",$taunt);
		$taunt = str_replace("%W","Large Green Rat",$taunt);
		$taunt = str_replace("%w","JoeBloe",$taunt);
		output("Preview: $taunt`0`n`n");
	}
	$output.="Taunt: <input name='taunt' value=\"".HTMLEntities($row[taunt])."\" size='70'><br>";
	output("The following codes are supported (case matters):`n");
	output("%w = Fight loser name`n");
	output("%x = Fight loser weapon`n");
	output("%s = Fight loser Subjective (him her)`n");
	output("%p = Fight loser possessive (his her)`n");
	output("%o = Fight loser objective (he she)`n");
	output("%W = Fight winner name`n");
	output("%X = Fight winner weapon`n");
	output("<input type='submit' class='button' value='Save'>",true);
	output("</form>",true);
}else if($_GET[op]=="del"){
	$sql = "DELETE FROM taunts WHERE tauntid=\"$_GET[tauntid]\"";
	db_query($sql) or die(db_error(LINK));
	redirect("taunt.php?c=x");
}else if($_GET[op]=="save"){
	if ($_GET[tauntid]!=""){
		$sql = "UPDATE taunts SET taunt=\"$_POST[taunt]\",editor=\"".addslashes($session[user][login])."\" WHERE tauntid=\"$_GET[tauntid]\"";
	}else{
		$sql = "INSERT INTO taunts (taunt,editor) VALUES (\"$_POST[taunt]\",\"".addslashes($session[user][login])."\")";
	}
	db_query($sql) or die(db_error(LINK));
	redirect("taunt.php?c=x");
}else{
	$sql = "SELECT * FROM taunts";
	$result = db_query($sql) or die(db_error(LINK));
	output("<table>",true);
	for ($i=0;$i<db_num_rows($result);$i++){
		$row=db_fetch_assoc($result);
		output("<tr>",true);
		output("<td>",true);
		output("[<a href='taunt.php?op=edit&tauntid=$row[tauntid]'>Edit</a>|<a href='taunt.php?op=del&tauntid=$row[tauntid]' onClick='return confirm(\"Are you sure you wish to delete this taunt item?\");'>Del</a>]",true);
		addnav("","taunt.php?op=edit&tauntid=$row[tauntid]");
		addnav("","taunt.php?op=del&tauntid=$row[tauntid]");
		output("</td>",true);
		output("<td>",true);
		output($row[taunt]);
		output("</td>",true);
		output("<td>",true);
		output($row[editor]);
		output("</td>",true);
		output("</tr>",true);
	}
	addnav("","taunt.php?c=$_GET[c]");
	output("</table>",true);
	addnav("Add a new taunt","taunt.php?op=edit");
}
page_footer();
?>