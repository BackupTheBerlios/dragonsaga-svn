<?
require_once "common.php";
page_header("Merick's Stables");

addnav("Return to the village","village.php");

$repaygold = round($playermount['mountcostgold']*2/3,0);
$repaygems = round($playermount['mountcostgems']*2/3,0);
if ($_GET[op]==""){
	checkday();
	output("`7Behind the inn, and a little to the left of Pegasus Armor, is as fine a stable as one
	might expect to find in any village. 
	In it, Merick, a burly looking dwarf tends to various beasts.
	`n`n
	You approach, and he whirls around, pointing a pitchfork in your general direction, \"`&Ach, 
	sorry m'".($session[user][sex]?"lass":"lad").", I dinnae hear ya' comin' up on me, an' I thoht
	fer sure ye were Cedrik; he what been tryin' to improve on his dwarf tossin' skills.  Naahw, wha'
	can oye do fer ya?`7\" he asks.  ");
}elseif($_GET['op']=="examine"){
	$sql = "SELECT * FROM mounts WHERE mountid='{$_GET['id']}'";
	$result = db_query($sql);
	if (db_num_rows($result)<=0){
		output("`7\"`&Ach, thar dinnae be any such beestie here!`7\" shouts the dwarf!");
	}else{
		output("`7\"`&Aye, tha' be a foyne beestie indeed!`7\" comments the dwarf.`n`n");
		$mount = db_fetch_assoc($result);
		output("`7Creature: `&{$mount['mountname']}`n");
		output("`7Description: `&{$mount['mountdesc']}`n");
		output("`7Cost: `^{$mount['mountcostgold']}`& gold, `%{$mount['mountcostgems']}`& gems`n");
		output("`n");
		addnav("Buy this creature","stables.php?op=buymount&id={$mount['mountid']}");
	}
}elseif($_GET['op']=='buymount'){
	$sql = "SELECT * FROM mounts WHERE mountid='{$_GET['id']}'";
	$result = db_query($sql);
	if (db_num_rows($result)<=0){
		output("`7\"`&Ach, thar dinnae be any such beestie here!`7\" shouts the dwarf!");
	}else{
		$mount = db_fetch_assoc($result);
		if ( 
			($session['user']['gold']+$repaygold) < $mount['mountcostgold']
			 || 
			($session['user']['gems']+$repaygems) < $mount['mountcostgems']
		){
			output("`7Merick looks at you sorta sideways.  \"`&'Ere, whadday ya think yeer doin'?  Cannae ye see that {$mount['mountname']} costs `^{$mount['mountcostgold']}`& gold an' `%{$mount['mountcostgems']}`& gems?`7\"");
		}else{
			if ($session['user']['hashorse']>0){
				output("`7You hand over the reins to your {$playermount['mountname']} and the purchase price of your new critter, and Merick leads out a fine new `&{$mount['mountname']}`7 for you!`n`n");
			}else{
				output("`7You hand over the purchase price of your new critter, and Merick leads out a fine `&{$mount['mountname']}`7 for you!`n`n");
		    }
			$session['user']['hashorse']=$mount['mountid'];
			$goldcost = $repaygold-$mount['mountcostgold'];
			$session['user']['gold']+=$goldcost;
			$gemcost = $repaygems-$mount['mountcostgems'];
			$session['user']['gems']+=$gemcost;
			debuglog(($goldcost <= 0?"spent ":"gained ") . abs($goldcost) . " gold and " . ($gemcost <= 0?"spent ":"gained ") . abs($gemcost) . " gems trading for a new mount");
			$session['bufflist']['mount']=unserialize($mount['mountbuff']);
			// Recalculate so the selling stuff works right
			$playermount = getmount($mount['mountid']);
			$repaygold = round($playermount['mountcostgold']*2/3,0);
			$repaygems = round($playermount['mountcostgems']*2/3,0);
		}
	}
}elseif($_GET['op']=='sellmount'){
	$session['user']['gold']+=$repaygold;
	$session['user']['gems']+=$repaygems;
	debuglog("gained $repaygold gold and $repaygems gems selling their mount");
	unset($session['bufflist']['mount']);
	$session['user']['hashorse']=0;
	output("`7As sad as it is to do so, you give up your precious {$playermount['mountname']}, and a lone tear escapes your eye.`n`n");
	output("However, the moment you spot the ".($repaygold>0?"`^$repaygold`7 gold ".($repaygems>0?" and ":""):"").($repaygems>0?"`%$repaygems`7 gems":"").", you find that you're feeling quite a bit better.");
}

if ($session['user']['hashorse']>0){
	output("`n`nMerick offers you `^$repaygold`& gold and `%$repaygems`& gems for your {$playermount['mountname']}.");
	addnav("Sell your {$playermount['mountname']}","stables.php?op=sellmount");
}

$sql = "SELECT mountname,mountid,mountcategory FROM mounts WHERE mountactive=1 ORDER BY mountcategory,mountcostgems,mountcostgold";
$result = db_query($sql);
$category="";
for ($i=0;$i<db_num_rows($result);$i++){
	$row = db_fetch_assoc($result);
	if ($category!=$row['mountcategory']){
		addnav($row['mountcategory']);
		$category = $row['mountcategory'];
	}
	addnav("Examine {$row['mountname']}`0","stables.php?op=examine&id={$row['mountid']}");
}

page_footer();
?>
