<?
require_once "common.php";

page_header("The Gardens");

addcommentary();
checkday();

output("`b`c`2The Gardens`0`c`b");

output("`n`n You walk through a gate and on to one of the many winding paths that makes its way through the well-tended gardens.  From the flowerbeds that bloom even in darkest winter, to the hedges whose shadows promise forbidden secrets, these gardens provide a refuge for those seeking out the Green Dragon; a place where they can forget their troubles for a while and just relax.");
output("`n`nOne of the fairies buzzing about the garden flies up to remind you that the garden is a place for roleplaying, and to confine out-of-character comments to the other areas of the game.");
output("`n`n");
viewcommentary("gardens","Whisper here",30,"whispers");

addnav("Return to the village","village.php");

page_footer();
?>
