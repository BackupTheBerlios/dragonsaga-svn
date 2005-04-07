<?
require_once "common.php";
if ((int)getsetting("expirecontent",180)>0){
	$sql = "DELETE FROM news WHERE newsdate<'".date("Y-m-d H:i:s",strtotime("-".getsetting("expirecontent",180)." days"))."'";
	//echo $sql;
	db_query($sql);
}
if ($session['user']['slainby']!=""){
	page_header("You have been slain!");
		output("`\$You were slain in ".$session['user'][killedin]."`\$ by `%".$session['user'][slainby]."`\$.  They cost you 5% of your experience, and took any gold you had.  Don't you think it's time for some revenge?");
	addnav("Continue",$REQUEST_URI);
	$session['user'][slainby]="";
	page_footer();
}else{
	
	if ($session['user']['loggedin']) checkday();
	$newsperpage=50;
	
	$offset = (int)$HTTP_GET_VARS['offset'];
	$timestamp=strtotime((0-$offset)." days");
	$sql = "SELECT count(newsid) AS c FROM news WHERE newsdate='".date("Y-m-d",$timestamp)."'";
	$result = db_query($sql);
	$row = db_fetch_assoc($result);
	$totaltoday=$row['c'];
	$pageoffset = (int)$_GET['page'];
	if ($pageoffset>0) $pageoffset--;
	$pageoffset*=$newsperpage;
	$sql = "SELECT * FROM news WHERE newsdate='".date("Y-m-d",$timestamp)."' ORDER BY newsid DESC LIMIT $pageoffset,$newsperpage";
	$result = db_query($sql) or die(db_error(LINK));
	page_header("TDS News");
	$date=date("D, M j, Y",$timestamp);
	
	output("`c`b`!News for $date".($totaltoday>$newsperpage?" (Items ".($pageoffset+1)." - ".min($pageoffset+$newsperpage,$totaltoday)." of $totaltoday)":"")."`c`b`0");
	
	for ($i=0;$i<db_num_rows($result);$i++){
		$row = db_fetch_assoc($result);
		output("`c`2-=-`@=-=`2-=-`@=-=`2-=-`@=-=`2-=-`0`c");
		if ($session['user'][superuser]>=3){
			output("[ <a href='superuser.php?op=newsdelete&newsid=$row[newsid]&return=".URLEncode($_SERVER['REQUEST_URI'])."'>Del</a> ]&nbsp;",true);
			addnav("","superuser.php?op=newsdelete&newsid=$row[newsid]&return=".URLEncode($_SERVER['REQUEST_URI']));
		}
		output("$row[newstext]`n");
	}
	if (db_num_rows($result)==0){
		output("`c`2-=-`@=-=`2-=-`@=-=`2-=-`@=-=`2-=-`0`c");
		output("`1`b`c Nothing of note happened this day.  All in all a boring day. `c`b`0");
	}
	output("`c`2-=-`@=-=`2-=-`@=-=`2-=-`@=-=`2-=-`0`c");
	if ($totaltoday>$newsperpage){
		addnav("Today's news");
		for ($i=0;$i<$totaltoday;$i+=$newsperpage){
			addnav("Page ".($i/$newsperpage+1),"news.php?offset=$offset&page=".($i/$newsperpage+1));
		}
		addnav("Other");
	}
	if (!$session['user']['loggedin']) {
		addnav("Login Screen", "index.php");
	} else if ($session['user']['alive']){
		addnav("Village Square","village.php");
	}else{
		if($session['user']['sex'] == 1) {
			addnav("`!`bYou're dead, Jane!`b`0");
		} else {
			addnav("`!`bYou're dead, Jim!`b`0");
		}
		addnav("Preferences","prefs.php");
		addnav("Land of Shades","shades.php");
		addnav("Log out","login.php?op=logout");
		addnav("News");
	}
	addnav("Previous News","news.php?offset=".($offset+1));
	if ($offset>0){
		addnav("Next News","news.php?offset=".($offset-1));
	}
	addnav("About this game","about.php");
	if ($session['user']['superuser']){
		addnav("New Day","newday.php");
	}
	addnav("","news.php");

	page_footer();
}
?>
