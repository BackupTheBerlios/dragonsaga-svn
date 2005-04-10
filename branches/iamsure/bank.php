<?php
// This program is free software; you can redistribute it and/or modify it
// under the terms of the GNU General Public License as published by the
// Free Software Foundation; either version 2 of the License, or (at your
// option) any later version.
//
// File: bank.php

require_once "common.php";
page_header("Ye Olde Bank");
output("`^`c`bYe Olde Bank`b`c`6");
if (!isset($_GET['op']))
{
    $_GET['op'] = '';
}
if ($_GET['op']==""){
  checkday();
  output("A short man in a immaculately arranged suit greets you from behind reading spectacles.`n`n");
  output("\"`5Hello my good man,`6\" you greet him, \"`5Might I inquire as to my balance this fine day?`6\"`n`n");
  output("The banker mumbles, \"`3Hmm, ".$session['user']['name']."`3, let's see.....`6\" as he scans down a page ");
  output("in his ledger.  ");
	if ($session['user']['goldinbank']>=0){
		output("\"`3Aah, yes, here we are.  You have `^".$session['user']['goldinbank']." gold`3 in our ");
		output("prestigious bank.  Is there anything else I can do for you?`6\"");
	}else{
		output("\"`3Aah, yes, here we are.  You have a `&debt`3 of `^".abs($session['user']['goldinbank'])." gold`3 in our ");
		output("prestigious bank.  Is there anything else I can do for you?`6\"");
	}
}else if($_GET['op']=="transfer"){
	output("`6`bTransfer Money`b:`n");
	if ($session['user']['goldinbank']>=0){
		output("You may only transfer `^".getsetting("transferperlevel",25)."`6 gold per the recipient's level.`n");
		$maxout = $session['user']['level']*getsetting("maxtransferout",25);
		output("You may transfer no more than `^$maxout`6 gold total. ");
		if ($session['user']['amountouttoday'] > 0) {
			output("(You have already transferred `^{$session['user']['amountouttoday']}`6 gold today)`n`n");
		} else output("`n`n");
		output("<form action='bank.php?op=transfer2' method='POST'>Transfer <u>h</u>ow much: <input name='amount' id='amount' accesskey='h' width='5'>`n",true);
		output("T<u>o</u>: <input name='to' accesskey='o'> (partial names are ok, you will be asked to confirm the transaction before it occurs).`n",true);
		output("<input type='submit' class='button' value='Preview Transfer'></form>",true);
		output("<script type='text/javascript'>document.getElementById('amount').focus();</script>",true);
		addnav("","bank.php?op=transfer2");
	}else{
		output("`6The little old banker tells you that he refuses to transfer money for someone who is in debt.");
	}
}else if($_GET['op']=="transfer2"){
	output("`6`bConfirm Transfer`b:`n");
	$string="%";
	for ($x=0;$x<strlen($_POST['to']);$x++){
		$string .= substr($_POST['to'],$x,1)."%";
	}
	$sql = "SELECT name,login FROM accounts WHERE name LIKE '".addslashes($string)."'";
	$result = db_query($sql);
	$amt = abs((int)$_POST['amount']);
	if (db_num_rows($result)==1){
		$row = db_fetch_assoc($result);
		output("<form action='bank.php?op=transfer3' method='POST'>",true);
		output("`6Transfer `^$amt`6 to `&$row[name]`6.");
		output("<input type='hidden' name='to' value='".HTMLEntities($row['login'])."'><input type='hidden' name='amount' value='$amt'><input type='submit' class='button' value='Complete Transfer'></form>",true);
		addnav("","bank.php?op=transfer3");
	}elseif(db_num_rows($result)>100){
		output("The banker looks at you disgustedly and suggests you try narrowing down the field of who you want to send money to just a little bit!`n`n");
		output("<form action='bank.php?op=transfer2' method='POST'>Transfer <u>h</u>ow much: <input name='amount' id='amount' accesskey='h' width='5' value='$amt'>`n",true);
		output("T<u>o</u>: <input name='to' accesskey='o' value='". $_POST['to'] . "'> (partial names are ok, you will be asked to confirm the transaction before it occurs).`n",true);
		output("<input type='submit' class='button' value='Preview Transfer'></form>",true);
		output("<script type='text/javascript'>document.getElementById('amount').focus();</script>",true);
		addnav("","bank.php?op=transfer2");
	}elseif(db_num_rows($result)>1){
		output("<form action='bank.php?op=transfer3' method='POST'>",true);
		output("`6Transfer `^$amt`6 to <select name='to' class='input'>",true);
		for ($i=0;$i<db_num_rows($result);$i++){
			$row = db_fetch_assoc($result);
			//output($row[name]." ".$row[login]."`n");
			output("<option value=\"".HTMLEntities($row['login'])."\">".preg_replace("'[`].'","",$row['name'])."</option>",true);
		}
		output("</select><input type='hidden' name='amount' value='$amt'><input type='submit' class='button' value='Complete Transfer'></form>",true);
		addnav("","bank.php?op=transfer3");
	}else{
		output("`6No one matching that name could be found!  Please try again.");
	}
}else if($_GET['op']=="transfer3"){
	$amt = abs((int)$_POST['amount']);
	output("`6`bTransfer Completion`b`n");
	if ($session[user][gold]+$session[user][goldinbank]<$amt){
		output("`6How can you transfer `^$amt`6 gold when you only possess ".($session[user][gold]+$session[user][goldinbank])."`6?");
	}else{
		$sql = "SELECT name,acctid,level,transferredtoday FROM accounts WHERE login='{$_POST['to']}'";
		$result = db_query($sql);
		if (db_num_rows($result)==1){
			$row = db_fetch_assoc($result);
			$maxout = $session['user']['level']*getsetting("maxtransferout",25);
			$maxtfer = $row['level']*getsetting("transferperlevel",25);
			if ($session['user']['amountouttoday']+$amt > $maxout) {
				output("`6The transfer was not completed: You are not allowed to transfer more than `^$maxout`6 gold total per day.");
			}else if ($maxtfer<$amt){
				output("`6The transfer was not completed: `&{$row['name']}`6 may only receive up to `^$maxtfer`6 gold.");
			}else if($row['transferredtoday']>=getsetting("transferreceive",3)){
				output("`&{$row['name']}`6 has received too many transfers today, you will have to wait until tomorrow.");
			}else if($amt<(int)$session['user']['level']){
				output("`6You might want to send a worthwhile transfer, at least as much as your level.");
			}else if($row['acctid']==$session['user']['acctid']){
				output("`6You may not transfer money to yourself!  That makes no sense!");
			}else{
				debuglog("transferred $amt gold to", $row['acctid']);
				$session[user][gold]-=$amt;
				if ($session[user][gold]<0){ //withdraw in case they don't have enough on hand.
					$session[user][goldinbank]+=$session[user][gold];
					$session[user][gold]=0;
				}
				$session['user']['amountouttoday']+= $amt;
				$sql = "UPDATE accounts SET goldinbank=goldinbank+$amt,transferredtoday=transferredtoday+1 WHERE acctid='{$row['acctid']}'";
				db_query($sql);
				output("`6Transfer Completed!");
				systemmail($row['acctid'],"`^You have received a money transfer!`0","`&{$session['user']['name']}`6 has transferred `^$amt`6 gold to your bank account!");
			}
		}else{
			output("`6Transfer could not be completed, please try again!");
		}
	}
}else if($_GET['op']=="deposit"){
  output("<form action='bank.php?op=depositfinish' method='POST'>You have ".($session['user']['goldinbank']>=0?"a balance of":"a debt of")." ".abs($session['user']['goldinbank'])." gold in the bank.`n",true);
	output("`^".($session['user']['goldinbank']>=0?"Deposit":"Pay off")." <u>h</u>ow much? <input id='input' name='amount' width=5 accesskey='h'> <input type='submit' class='button' value='Deposit'>`n`iEnter 0 or nothing to deposit it all`i</form>",true);
	output("<script type='text/javascript'>document.getElementById('input').focus();</script>",true);
  addnav("","bank.php?op=depositfinish");
}else if($_GET['op']=="depositfinish"){
	$_POST[amount]=abs((int)$_POST[amount]);
	if ($_POST[amount]==0){
		$_POST[amount]=$session[user][gold];
	}
	if ($_POST[amount]>$session[user][gold]){
		output("`\$ERROR: Not enough gold in hand to deposit.`^`n`n");
		output("You plunk your `&".$session[user][gold]."`^ gold on the counter and declare that you would like to deposit all `&$_POST[amount]`^ gold of it.");
		output("`n`nThe little old man stares blankly for a few seconds until you become self conscious and count your money again, realizing your mistake.");
	}else{
		output("`^`bYou deposit `&$_POST[amount]`^ gold in to your bank account, ");
		debuglog("deposited " . $_POST[amount] . " gold in the bank");
		$session[user][goldinbank]+=$_POST[amount];
		$session[user][gold]-=$_POST[amount];
		output("leaving you with ".($session[user][goldinbank]>=0?"a balance of":"a debt of")." `&".abs($session[user][goldinbank])."`^ gold in your account and `&".$session[user][gold]."`^ gold in hand.`b");
	}
}else if($_GET['op']=="borrow"){
	$maxborrow = $session['user']['level']*getsetting("borrowperlevel",20);
  output("<form action='bank.php?op=withdrawfinish' method='POST'>You have ".($session['user']['goldinbank']>=0?"a balance of":"a debt of")." ".abs($session['user']['goldinbank'])." gold in the bank.`n",true);
  output("`^Borrow <u>h</u>ow much (you may borrow a max of $maxborrow total at your level)? <input id='input' name='amount' width=5 accesskey='h'> <input type='hidden' name='borrow' value='x'><input type='submit' class='button' value='Borrow'>`n(Money will be withdrawn until you have none left, the remainder will be borrowed)</form>",true);
	output("<script type='text/javascript'>document.getElementById('input').focus();</script>",true);
  addnav("","bank.php?op=withdrawfinish");
}else if($_GET['op']=="withdraw"){
  output("<form action='bank.php?op=withdrawfinish' method='POST'>You have ".$session['user']['goldinbank']." gold in the bank.`n",true);
  output("`^Withdraw <u>h</u>ow much? <input id='input' name='amount' width=5 accesskey='h'> <input type='submit' class='button' value='Withdraw'>`n`iEnter 0 or nothing to withdraw it all`i</form>",true);
	output("<script type='text/javascript'>document.getElementById('input').focus();</script>",true);
  addnav("","bank.php?op=withdrawfinish");
}else if($_GET['op']=="withdrawfinish"){
	$_POST[amount]=abs((int)$_POST[amount]);
	if ($_POST[amount]==0){
		$_POST[amount]=abs($session[user][goldinbank]);
	}
	if ($_POST[amount]>$session[user][goldinbank] && $_POST[borrow]=="") {
		output("`\$ERROR: Not enough gold in the bank to to withdraw.`^`n`n");
		output("Having been informed that you have `&".$session[user][goldinbank]."`^ gold in your account, you declare that you would like to withdraw all `&$_POST[amount]`^ of it.");
		output("`n`nThe little man stares blankly at you for a second, then advises you take basic arithmetic.  You realize your folly and think you should try again.");
	}else if($_POST[amount]>$session[user][goldinbank]){
		$lefttoborrow = $_POST[amount];
		$maxborrow = $session[user][level]*getsetting("borrowperlevel",20);
		if ($lefttoborrow<=$session[user][goldinbank]+$maxborrow){
			if ($session[user][goldinbank]>0){
				output("`6You withdraw your remaining `^".$session[user][goldinbank]."`6 gold, and ");
				$lefttoborrow-=$session[user][goldinbank];
				$session[user][gold]+=$session[user][goldinbank];
				$session[user][goldinbank]=0;
				debuglog("withdrew " . $_POST[amount] . " gold from the bank");
			}else{
				output("`6You ");
			}
			if ($lefttoborrow-$session[user][goldinbank] > $maxborrow){
				output("ask to borrow `^$lefttoborrow`6 gold.  The short little man looks up your account and informs you that you may only borrow up to `^$maxborrow`6 gold.");
			}else{
				output("borrow `^$lefttoborrow`6 gold.");
				$session[user][goldinbank]-=$lefttoborrow;
				$session[user][gold]+=$lefttoborrow;
				debuglog("borrows $lefttoborrow gold from the bank");
			}
		}else{
			output("`6Considering the `^{$session[user][goldinbank]}`6 gold in your account, you ask to borrow `^".($lefttoborrow-$session[user][goldinbank])."`6, but
			the short little man looks up your account and informs you that you may only borrow up to `^$maxborrow`6 gold at your level.");
		}
	}else{
		output("`^`bYou withdraw `&$_POST[amount]`^ gold from your bank account, ");
		$session[user][goldinbank]-=$_POST[amount];
		$session[user][gold]+=$_POST[amount];
		debuglog("withdrew " . $_POST[amount] . " gold from the bank");
		output("leaving you with `&".$session[user][goldinbank]."`^ gold in your account and `&".$session[user][gold]."`^ gold in hand.`b");
	}
}
addnav("Return to the Village","village.php");
if ($session['user']['goldinbank']>=0){
	addnav("Withdraw","bank.php?op=withdraw");
	addnav("Deposit","bank.php?op=deposit");
	if (getsetting("borrowperlevel",20)) addnav("Take out a loan","bank.php?op=borrow");
}else{
	addnav("Pay off debt","bank.php?op=deposit");
	if (getsetting("borrowperlevel",20)) addnav("Borrow More","bank.php?op=borrow");
}
if (getsetting("allowgoldtransfer",1)){
	if ($session['user']['level']>=getsetting("mintransferlev",3) || $session['user']['dragonkills']>0){
		addnav("Transfer Money","bank.php?op=transfer");
	}
}

page_footer();

?>
