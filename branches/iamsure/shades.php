<?
require_once "common.php";

page_header("Land of the Shades");
addcommentary();
checkday();

if ($session['user']['alive']) redirect("village.php");
output("`\$You walk among the dead now, you are a shade.  Everywhere around you are the souls of those who have fallen in battle, in old age, 
and in grievous accidents.  Each bears telltale signs of the means by which they met their end.
`n`n
Their souls whisper their torments, haunting your mind with their despair:`n");
viewcommentary("shade","Despair",25,"despairs");
addnav("The Graveyard","graveyard.php");
addnav("Return to the news","news.php");
if ($session[user][superuser]>=2){
  addnav("Superuser Grotto","superuser.php");
}

page_footer();
?>
