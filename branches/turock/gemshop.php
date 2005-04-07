<?php
/***************************************************************************
//  Gem Exchange v1.0
//  Coded by: Turock
//  Allows players to exchange gems for gold
***************************************************************************/

require_once "common.php";

// exchange rate for gems
$ex_rate = get_global_var(ExchangeRate);

page_header("Gem Exchange");
addnav("Sell your Gems","gemshop.php?op=sell");
addnav("Return to the Village","village.php");

$gemsell = $session['user']['gems'];
output("`n`^You enter the shop and see an old man sitting behind a small counter.`n");
output("He doesn't say anything and instead points to a small sign...`n");
output("`nThe sign reads:  `%Gem Exchange Rate Today: `&1 `^gem = `%".commas($ex_rate)." `^gold");

if ($HTTP_GET_VARS['op']=="sell"){
	if ($session['user']['gems']<1){
		output("`n`nYou don't have any gems to sell.`n");
	    }
	else{
     $word="gems";
		if ($session['user']['gems']==1) {
			$word="gem";
		}
	output("`n`n`^You have `&$gemsell `^$word to sell.`n");
    output("`%How many would you like to sell?`n");
    output("<form action='gemshop.php?op=sell2' method='POST'><input name='sell' id='sell'><input type='submit' class='button' value='sell'></form>",true);
    addnav("","gemshop.php?op=sell2");
    }
}
if ($HTTP_GET_VARS['op']=="sell2"){
    $sell = $_POST['sell'];
	if ($session['user']['gems'] < $sell) output("`n`n`%You don't have that many gems to sell!`n");
	else{
     $word="gems";
		if ($sell==1) {
			$word="gem";
		}
		$gold = $ex_rate * $sell;
		output("`n`n`^The man takes your `&$sell `^$word and hands you `%".commas($gold)." `^gold.`n");
        debuglog("sold $sell gems in the gemshop and received $gold gold");
		$session['user']['gold']+=$gold;
		$session['user']['gems']-=$sell;
	    }
    }
page_footer();
?>
