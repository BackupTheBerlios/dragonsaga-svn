<?
require_once "common.php";

/***************
 **  SETTINGS **
 ***************/
$turnsperday = getsetting("turns",10);
$maxinterest = ((float)getsetting("maxinterest",10)/100) + 1; //1.1;
$mininterest = ((float)getsetting("mininterest",1)/100) + 1; //1.1;
//$mininterest = 1.01;
$dailypvpfights = getsetting("pvpday",3);

$resline = $_GET['resurrection']=="true" ? "&resurrection=true" : "" ;
/******************
 ** End Settings **
 ******************/
if (count($session['user']['dragonpoints']) <$session['user']['dragonkills']&&$_GET['dk']!=""){
	array_push($session['user']['dragonpoints'],$_GET[dk]);
	switch($_GET['dk']){
	case "hp":
		$session['user']['maxhitpoints']+=5;
		break;
	case "at":
		$session['user']['attack']++;
		break;
	case "de":
		$session['user']['defence']++;
		break;	
	}
}
if (count($session['user']['dragonpoints'])<$session['user']['dragonkills'] && $_GET['dk']!="ignore"){
	page_header("Dragon Points");
	addnav("Max Hitpoints + 5","newday.php?dk=hp$resline");
	addnav("Forest Fights + 1","newday.php?dk=ff$resline");
	addnav("Attack + 1","newday.php?dk=at$resline");
	addnav("Defense + 1","newday.php?dk=de$resline");
	//addnav("Ignore (Dragon Points are bugged atm)","newday.php?dk=ignore$resline");
	output("`@You have `^".($session['user']['dragonkills']-count($session['user']['dragonpoints']))."`@ unspent dragon points.  How do you wish to spend them?`n`n");
	output("You earn one dragon point each time you slay the dragon.  Advancements made by spending dragon points are permanent!");
}else if ((int)$session['user']['race']==0){
	page_header("A little history about yourself");
	if ($_GET['setrace']!=""){
		$session['user']['race']=(int)($_GET['setrace']);
		switch($_GET['setrace']){
		case "1":
			$session['user']['attack']++;
			output("`2As a troll, and having always fended for yourself, the ways of battle are not foreign to you.`n`^You gain an attack point!");
			break;
		case "2":
			$session['user']['defence']++;
			output("`^As an elf, you are keenly aware of your surroundings at all times, very little ever catches you by surprise.`nYou gain a defense point!");
			break;
		case "3":
			output("`&As a human, your size and strength permit you the ability to effortlessly wield weapons, tireing much less quickly than other races.`n`^You gain an extra forest fight each day!");
			break;
		case "4":
			output("`#As a dwarf, you are more easily able to identify the value of certain goods.`n`^You gain extra gold from forest fights!");
			break;
		}
		addnav("Continue","newday.php?continue=1$resline");
	}else{
		output("Where do you recall growing up?`n`n");
		output("<a href='newday.php?setrace=1$resline'>In the swamps of Glukmoore</a> as a `2troll`0, fending for yourself from the very moment you crept out of your leathery egg, slaying your yet unhatched siblings, and feasting on their bones.`n`n",true);
		output("<a href='newday.php?setrace=2$resline'>High among the trees</a> of the Glorfindal forest, in frail looking elaborate `^Elvish`0 structures that look as though they might collapse under the slightest strain, yet have existed for centuries.`n`n",true);
		output("<a href='newday.php?setrace=3$resline'>On the plains in the city of Romar</a>, the city of `&men`0; always following your father and looking up to his every move, until he sought out the `@Green Dragon`0, never to be seen again.`n`n",true);
		output("<a href='newday.php?setrace=4$resline'>Deep in the subterranean strongholds of Qexelcrag</a>, home to the noble and fierce `#Dwarven`0 people whose desire for privacy and treasure bears no resemblance to their tiny stature.`n`n",true);
		addnav("Choose your Race");
		addnav("`2Troll`0","newday.php?setrace=1$resline");
		addnav("`^Elf`0","newday.php?setrace=2$resline");
		addnav("`&Human`0","newday.php?setrace=3$resline");
		addnav("`#Dwarf`0","newday.php?setrace=4$resline");
		addnav("","newday.php?setrace=1$resline");
		addnav("","newday.php?setrace=2$resline");
		addnav("","newday.php?setrace=3$resline");
		addnav("","newday.php?setrace=4$resline");
	}
}else if ((int)$session['user']['specialty']==0){
  if ($HTTP_GET_VARS['setspecialty']===NULL){
		addnav("","newday.php?setspecialty=1$resline");
		addnav("","newday.php?setspecialty=2$resline");
		addnav("","newday.php?setspecialty=3$resline");
		page_header("A little history about yourself");
		
		output("Growing up as a child, you remember:`n`n");
		output("<a href='newday.php?setspecialty=1$resline'>Killing a lot of woodland creatures (`\$Dark Arts`0)</a>`n",true);
		output("<a href='newday.php?setspecialty=2$resline'>Dabbling in mystical forces (`%Mystical Powers`0)</a>`n",true);
		output("<a href='newday.php?setspecialty=3$resline'>Stealing from the rich and giving to yourself (`^Thievery`0)</a>`n",true);
		addnav("`\$Dark Arts","newday.php?setspecialty=1$resline");
		addnav("`%Mystical Powers","newday.php?setspecialty=2$resline");
		addnav("`^Thievery","newday.php?setspecialty=3$resline");
  }else{
	  addnav("Continue","newday.php?continue=1$resline");
		switch($HTTP_GET_VARS['setspecialty']){
		  case 1:
			  page_header("Dark Arts");
				output("`5Growing up, you recall killing many small woodland creatures, insisting that they were ");
				output("plotting against you.  Your parents, concerned that you had taken to killing the creatures ");
				output("barehanded, bought you your very first pointy twig.  It wasn't until your teenage years that ");
				output("you began performing dark rituals with the creatures, dissapearing into the forest for days ");
				output("on end, no one quite knowing where those sounds came from.");
				break;
			case 2:
			  page_header("Mystical Forces");
				output("`3Growing up, you remember knowing there was more to the world than the physical, and what you ");
				output("could place your hands on.  You realized that your mind itself, with training, could be turned ");
				output("in to a weapon.  Over time, you began to control the thoughts of small creatures, commanding ");
				output("them to do your bidding, and also to begin to tap in to the mystical force known as mana, ");
				output("which could be shaped in to numerous elemental forms, fire, water, ice, earth, wind, and also ");
				output("used as a weapon against your foes.");
				break;
			case 3:
			  page_header("Thievery");
				output("`6Growing up, you recall discovering that a casual bump in a crowded room could earn you ");
				output("the coin purse of someone otherwise more fortunate than you.  You also discovered that ");
				output("the back side of your enemies were considerably more prone to a narrow blade than the ");
				output("front side was to even a powerful weapon.");
				break;
		}
		$session['user']['specialty']=$HTTP_GET_VARS['setspecialty'];
	}
}else{
  if ($session['user']['slainby']!=""){
		page_header("You have been slain!");
		output("`\$You were slain in the ".$session['user']['killedin']." by `%".$session['user']['slainby']."`\$.  They cost you 5% of your experience, and took any gold you had.  Don't you think it's time for some revenge?");
		addnav("Continue","newday.php?continue=1$resline");
	  $session['user']['slainby']="";
	}else{
		page_header("It is a new day!");
		$interestrate = e_rand($mininterest*100,$maxinterest*100)/(float)100;
		output("`c<font size='+1'>`b`#It is a New Day!`0`b</font>`c",true);

		if ($session['user']['alive']!=true){
			$session['user']['resurrections']++;
			output("`@You are resurrected!  This is your ".ordinal($session['user']['resurrections'])." resurrection.`0`n");
			$session['user']['alive']=true;
		}
		$session[user][age]++;
		$session[user][seenmaster]=0;
		output("You open your eyes to discover that a new day has been bestowed upon you, it is your `^".ordinal($session['user']['age'])."`0 day.  ");
		output("You feel refreshed enough to take on the world!`n");
		output("`2Turns for today set to `^$turnsperday`n");
		if ($session['user']['turns']>getsetting("fightsforinterest",4) && $session['user']['goldinbank']>=0) {
			$interestrate=1;
			output("`2Today's interest rate: `^0% (Bankers in this village only give interest to those who work for it)`n");
		}else{
			output("`2Today's interest rate: `^".(($interestrate-1)*100)."% `n");
			if ($session['user']['goldinbank']>=0){
				output("`2Gold earned from interest: `^".(int)($session['user']['goldinbank']*($interestrate-1))."`n");
			}else{
				output("`2Interest Accrued on Debt: `^".-(int)($session['user']['goldinbank']*($interestrate-1))."`2 gold.`n");
			}
		}
		output("`2Hitpoints have been restored to `^".$session['user']['maxhitpoints']."`n");
		$skills = array(1=>"Dark Arts","Mystical Powers","Thievery");
		$sb = getsetting("specialtybonus",1);
		output("`2For being interested in `&".$skills[$session['user']['specialty']]."`2, you receive $sb extra `&".$skills[$session['user']['specialty']]."`2 use for today.`n");
		$session['user']['darkartuses'] = (int)($session['user']['darkarts']/3) + ($session['user']['specialty']==1?$sb:0);
		$session['user']['magicuses'] = (int)($session['user']['magic']/3) + ($session['user']['specialty']==2?$sb:0);
		$session['user']['thieveryuses'] = (int)($session['user']['thievery']/3) + ($session['user']['specialty']==3?$sb:0);
		//$session['user']['bufflist']=array(); // with this here, buffs are always wiped, so the preserve stuff fails!
		if ($session['user']['marriedto']==4294967295){
			output("`n`%You're  married,  so there's no reason to keep up that perfect image, and you let yourself go a little today.`n");
			$session['user']['charm']--;
			if ($session['user']['charm']<=0){
				output("`bWhen  you  wake  up, you find a note next to you, reading`n`5Dear ");
				output($session['user']['name']);
				output("`5,`nDespite  many  great  kisses, I find that I'm simply no longer attracted to you the way I used to be.`n`n");
				output("Call  me fickle, call me flakey, but I need to move on.  There are other warriors in the village, and I think");
				output("some of them are really hot.  So it's not you, it's me, etcetera etceterea.");
				output("`n`nNo hard feelings, Love,`n".($session['user']['sex']?"Seth":"Violet")."`b`n");
				addnews("`\$".($session['user']['sex']?"Seth":"Violet") ." has left {$session['user']['name']}`\$ to pursue \"other interests.\"");
				$session['user']['marriedto']=0;
			}
		}

		//clear all standard buffs
		$tempbuf = unserialize($session['user']['bufflist']);
		$session['user']['bufflist']="";
		$session['bufflist']=array();
		while(list($key,$val)=@each($tempbuff)){
			if ($val['survivenewday']==1){
				$session['bufflist'][$key]=$val;
				output("{$val['newdaymessage']}`n");
			}
		}

		reset($session['user']['dragonpoints']);
		$dkff=0;
		while(list($key,$val)=each($session['user']['dragonpoints'])){
			if ($val=="ff"){
				$dkff++;
			}
		}
		if ($session[user][hashorse]){
			$session['bufflist']['mount']=unserialize($playermount['mountbuff']);
		}
		if ($dkff>0) output("`n`2You gain `^$dkff`2 forest fights from spent dragon points!"); 
		$r1 = e_rand(-1,1);
		$r2 = e_rand(-1,1);
		$spirits = $r1+$r2;
		if ($_GET['resurrection']=="true"){
			addnews("`&{$session['user']['name']}`& has been resurrected by `\$Ramius`&.");
			$spirits=-6;
			$session['user']['deathpower']-=100;
			$session['user']['restorepage']="village.php?c=1";
		}
		$sp = array((-6)=>"Resurrected",(-2)=>"Very Low",(-1)=>"Low","0"=>"Normal",1=>"High",2=>"Very High");
		output("`n`2You are in `^".$sp[$spirits]."`2 spirits today!`n");
		if (abs($spirits)>0){
			output("`2As a result, you `^");
			if($spirits>0){
				output("gain ");
			}else{
				output("lose ");
			}
			output(abs($spirits)." forest fights`2 for today!`n");
		}
		$rp = $session['user']['restorepage'];
		$x = max(strrpos("&",$rp),strrpos("?",$rp));
		if ($x>0) $rp = substr($rp,0,$x);
		if (substr($rp,0,10)=="badnav.php"){
			addnav("Continue","news.php");
		}else{
			addnav("Continue",preg_replace("'[?&][c][=].+'","",$rp));
		}
		
		$session['user']['laston'] = date("Y-m-d H:i:s");
		$bgold = $session['user']['goldinbank'];
		$session['user']['goldinbank']*=$interestrate;
		$nbgold = $session['user']['goldinbank'] - $bgold;

		if ($nbgold != 0) {
			debuglog(($nbgold >= 0 ? "earned " : "paid ") . abs($nbgold) . " gold in interest");
		}
		$session['user']['turns']=$turnsperday+$spirits+$dkff;
		$session['user']['hitpoints'] = $session[user][maxhitpoints];
		$session['user']['spirits'] = $spirits;
		$session['user']['playerfights'] = $dailypvpfights;
		$session['user']['transferredtoday'] = 0;
		$session['user']['amountouttoday'] = 0;
		$session['user']['seendragon'] = 0;
		$session['user']['seenmaster'] = 0;
		$session['user']['seenlover'] = 0;
		$session['user']['usedouthouse'] = 0;
		if ($_GET['resurrection']!="true"){
			$session['user']['soulpoints']=50 + 5 * $session['user']['level'];
			$session['user']['gravefights']=getsetting("gravefightsperday",10);
		}
		$session['user']['seenbard'] = 0;
		$session['user']['boughtroomtoday'] = 0;
		$session['user']['recentcomments']=$session['user']['lasthit'];
		$session['user']['lasthit'] = date("Y-m-d H:i:s");
		if ($session['user']['drunkenness']>66){
		  output("`&Coming off of a hangover, you lose 1 forest fight today");
			$session['user']['turns']--;
		}
		if ($session['user']['hashorse']){
			//$horses=array(1=>"pony","gelding","stallion");
			//output("`n`&You strap your `%".$session['user']['weapon']."`& to your ".$horses[$session['user']['hashorse']]."'s saddlebags and head out for some adventure.`0");
			//output("`n`&Because you have a ".$horses[$session['user']['hashorse']].", you gain ".((int)$session['user']['hashorse'])." forest fights for today!`n`0");
			//$session['user']['turns']+=((int)$session['user']['hashorse']);
			output(str_replace("{weapon}",$session['user']['weapon'],"`n`&{$playermount['newday']}`n`0"));
			if ($playermount['mountforestfights']>0){
				output("`n`&Because you have a {$playermount['mountname']}, you gain `^".((int)$playermount['mountforestfights'])."`& forest fights for today!`n`0");
				$session['user']['turns']+=(int)$playermount['mountforestfights'];
			}
		}else{
			output("`n`&You strap your `%".$session['user']['weapon']."`& to your back and head out for some adventure.`0");
		}
		if ($session['user']['race']==3) {
			$session['user']['turns']++;
			output("`n`&Because you are human, you gain `^1`& forest fight for today!`n`0");
		}
		$config = unserialize($session['user']['donationconfig']);
		if (!is_array($config['forestfights'])) $config['forestfights']=array();
		reset($config['forestfights']);
		while (list($key,$val)=each($config['forestfights'])){
			$config['forestfights'][$key]['left']--;
			output("`@You gain an extra turn from points spent on `^{$val['bought']}`@.");
			$session['user']['turns']++;
			if ($val['left']>1){
				output("  You have `^".($val['left']-1)."`@ days left on this buy.`n");
			}else{
				unset($config['forestfights'][$key]);
				output("  This buy has expired.`n");
			}
		}
		if ($config['healer'] > 0) {
			$config['healer']--;
			if ($config['healer'] > 0) {
				output("`n`@Golinda will be willing to see you for {$config['healer']} more day" . ($config['healer'] > 1 ? "s." : "."));
			} else {
				output("`n`@Golinda will no longer treat you.");
				unset($config['healer']);
			}
		}
		$session['user']['donationconfig']=serialize($config);
		if ($session['user']['hauntedby']>""){
			output("`n`n`)You have been haunted by {$session['user']['hauntedby']}`), as a result, you lose a forest fight!");
			$session['user']['turns']--;
			$session['user']['hauntedby']="";
		}
		$session['user']['drunkenness']=0;
		$session['user']['bounties']=0;
	}
	if (
		strtotime(
			getsetting(
				"lastdboptimize",
				date(
					"Y-m-d H:i:s",
					strtotime("-1 day")
				)
			)
		) < strtotime("-1 day")
	){
		savesetting("lastdboptimize",date("Y-m-d H:i:s"));
		$result = db_query("SHOW TABLES");
		for ($i=0;$i<db_num_rows($result);$i++){
			list($key,$val)=each(db_fetch_assoc($result));
			db_query("OPTIMIZE TABLE $val");
		}
	}
}
page_footer();
?>
