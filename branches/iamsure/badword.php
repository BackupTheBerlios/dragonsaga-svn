<?
require_once "common.php";

isnewday(2);

page_header("Bad word editor.");
addnav("G?Return to the Grotto","superuser.php");
addnav("M?Return to the Mundane","village.php");
addnav("Refresh the list","badword.php");
output("Here you can edit the words that the game filters.  Using * at the start or end of a word will be a ");
output("wildcard matching anything else attached to the word.  These words are only filtered if bad word filtering ");
output("is turned on in the game settings page.");
//output("<form action='badword.php?op=add' method='POST'>Add a word: <input name='word'><input type='submit' value='Add'></form>",true);
//output("<form action='badword.php?op=remove' method='POST'>Remove a word: <input name='word'><input type='submit' value='Remove'></form>",true);
//output("<form action='badword.php?op=test' method='POST'>Test a word: <input name='word'><input type='submit' value='Test'></form>",true);
output("<form action='badword.php?op=add' method='POST'>Add a word:<input name='word'><input type='submit' class='button' value='Add'></form>",true);
output("<form action='badword.php?op=remove' method='POST'>Remove a word: <input name='word'><input type='submit' class='button' value='Remove'></form>",true);
output("<form action='badword.php?op=test' method='POST'>Test a word: <input name='word'><input type='submit' class='button' value='Test'></form>",true);

addnav("","badword.php?op=add");
addnav("","badword.php?op=remove");
addnav("","badword.php?op=test");
$sql = "SELECT * FROM nastywords";
$result = db_query($sql);
$row = db_fetch_assoc($result);
$words = split(" ",$row['words']);
reset($words);

if ($_GET['op']=="add"){
    array_push($words,stripslashes($_POST['word']));
}
if ($_GET['op']=="remove"){
    unset($words[array_search(stripslashes($_POST['word']),$words)]);
}
if ($_GET['op']=="test"){
    output("`7The result of your word test is `^".soap($_POST['word'])."`7.  (If you do not have bad word filtering turned on, this test will not work).`n`n");
}
sort($words);
$lastletter="";
while (list($key,$val)=each($words)){
    if (trim($val)==""){
        unset($words[$key]);
    }else{
        if (substr($val,0,1)!=$lastletter){
            $lastletter = substr($val,0,1);
            output("`n`n`^`b" . strtoupper($lastletter) . "`b`@`n");
        }
        output($val." ");
    }
}
if ($_GET['op']=="add" || $_GET['op']=="remove"){
    $sql = "DELETE FROM nastywords";
    db_query($sql);
    $sql = "INSERT INTO nastywords VALUES ('" . addslashes(join(" ",$words)) . "')";
    db_query($sql);
}
page_footer();
?>
