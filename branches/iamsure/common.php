<?php
require_once ("./dbwrapper.php");
require_once ("./global_functions.php");

$pagestarttime = getmicrotime();

$nestedtags = array();
$output = "";

mt_srand(make_seed());

$accesskeys = array();
$quickkeys = array();
if (file_exists("dbconnect.php"))
{
    require_once "dbconnect.php";
}
else
{
    echo "You must edit the file named \"dbconnect.php.dist,\" and provide the requested information, then save it as \"dbconnect.php\"".
    exit();
}

$link = db_pconnect($DB_HOST, $DB_USER, $DB_PASS) or die (db_error($link));
//define("LINK",$link);

require_once "translator.php";

session_register("session");
function register_global(&$var)
{
    @reset($var);
    while (list($key,$val)=@each($var))
    {
        global $$key;
        $$key = $val;
    }
    @reset($var);
}
$session =& $_SESSION['session'];
//echo nl2br(htmlentities(output_array($session)));
//register_global($_SESSION);
register_global($_SERVER);

if (!isset($session['lasthit']))
{
    $session['lasthit'] = '';
}

if (strtotime("-".getsetting("LOGINTIMEOUT",900)." seconds") > $session['lasthit'] && $session['lasthit']>0 && $session['loggedin'])
{
    // force the abandoning of the session when the user should have been sent to the fields.
    // echo "Session abandon:".(strtotime("now")-$session[lasthit]);

    $session = array();
    $session['message'].="`nYour session has expired!`n";
}

$session['lasthit'] = strtotime("now");

$revertsession = $session;

if (isset($_SERVER['PATH_INFO']) && ($_SERVER['PATH_INFO'] != ""))
{
    $SCRIPT_NAME = $_SERVER['PATH_INFO'];
    $REQUEST_URI = "";
}

if ($REQUEST_URI == ""){
        //necessary for some IIS installations (CGI in particular)
        if (is_array($_GET) && count($_GET)>0){
                $REQUEST_URI=$SCRIPT_NAME."?";
                reset($_GET);
                $i=0;
                while (list($key,$val)=each($_GET)){
                        if ($i>0) $REQUEST_URI.="&";
                        $REQUEST_URI.="$key=".URLEncode($val);
                        $i++;
                }
        }else{
                $REQUEST_URI=$SCRIPT_NAME;
        }
        $_SERVER['REQUEST_URI'] = $REQUEST_URI;
}

$SCRIPT_NAME=substr($SCRIPT_NAME,strrpos($SCRIPT_NAME,"/")+1);
if (strpos($REQUEST_URI,"?"))
{
    $REQUEST_URI = $SCRIPT_NAME.substr($REQUEST_URI,strpos($REQUEST_URI,"?"));
}
else
{
    $REQUEST_URI=$SCRIPT_NAME;
}

$allowanonymous = array("index.php"=>true,"login.php"=>true,"create.php"=>true,"about.php"=>true,"list.php"=>true,"petition.php"=>true,"connector.php"=>true,"logdnet.php"=>true,"referral.php"=>true,"news.php"=>true,"motd.php"=>true,"topwebvote.php"=>true);
$allownonnav = array("badnav.php"=>true,"motd.php"=>true,"petition.php"=>true,"mail.php"=>true,"topwebvote.php"=>true);
if ($session['loggedin']){
	$sql = "SELECT * FROM accounts WHERE acctid = '".$session['user']['acctid']."'";
	$result = db_query($sql);
	if (db_num_rows($result)==1){
		$session['user']=db_fetch_assoc($result);
		$session['output']=$session['user']['output'];
		$session['user']['dragonpoints']=unserialize($session['user']['dragonpoints']);
		$session['user']['prefs']=unserialize($session['user']['prefs']);
		if (!is_array($session['user']['dragonpoints'])) $session['user']['dragonpoints']=array();
		if (is_array(unserialize($session['user']['allowednavs']))){
			$session['allowednavs'] = unserialize($session['user']['allowednavs']);
		}else{
			//depreciated, left only for legacy support.
			$session['allowednavs'] = createarray($session['user']['allowednavs']);
		}
		if (!$session['user']['loggedin'] || (0 && (date("U") - strtotime($session['user']['laston'])) > getsetting("LOGINTIMEOUT",900)) ){
			$session=array();
			redirect("index.php?op=timeout","Account not logged in but session thinks they are.");
		}
	}else{
		$session=array();
		$session['message'] = "`4Error, your login was incorrect`0";
		redirect("index.php","Account Disappeared!");
	}
	db_free_result($result);
        if (!isset($session['allowednavs'][$REQUEST_URI]))
        {
            $session['allowednavs'][$REQUEST_URI] = '';
        }

        if (!isset($session['allowednavs'][$REQUEST_URI]))
        {
            $session['allowednavs'][$REQUEST_URI]='';
        }

        if (!isset($allownonnav[$SCRIPT_NAME]))
        {
            $allownonnav[$SCRIPT_NAME]='';
        }

	if ($session['allowednavs'][$REQUEST_URI] && !$allownonnav[$SCRIPT_NAME]){
		$session['allowednavs']=array();
	}else{
		if (!$allownonnav[$SCRIPT_NAME]){
			redirect("badnav.php","Navigation not allowed to $REQUEST_URI");
		}
	}
}else{
	//if ($SCRIPT_NAME != "index.php" && $SCRIPT_NAME != "login.php" && $SCRIPT_NAME != "create.php" && $SCRIPT_NAME != "about.php"){
	if (!$allowanonymous[$SCRIPT_NAME]){
		$session['message'] = "You are not logged in, this may be because your session timed out.";
		redirect("index.php?op=timeout","Not logged in: $REQUEST_URI");
	}
}
//if ($session['user']['loggedin']!=true && $SCRIPT_NAME != "index.php" && $SCRIPT_NAME != "login.php" && $SCRIPT_NAME != "create.php" && $SCRIPT_NAME != "about.php"){
if (!isset($session['user']['loggedin']))
{
    $session['user']['loggedin'] = '';
}

if ($session['user']['loggedin'] != true && !$allowanonymous[$SCRIPT_NAME])
{
    redirect("login.php?op=logout");
}

if (!isset($session['counter']))
{
    $session['counter'] = '';
}

$session['counter']++;
$nokeeprestore = array("newday.php"=>1,"badnav.php"=>1,"motd.php"=>1,"mail.php"=>1,"petition.php"=>1);
if (!isset($nokeeprestore[$SCRIPT_NAME]))
{
    $nokeeprestore[$SCRIPT_NAME]='';
}

if (!$nokeeprestore[$SCRIPT_NAME]) { //strpos($REQUEST_URI,"newday.php")===false && strpos($REQUEST_URI,"badnav.php")===false && strpos($REQUEST_URI,"motd.php")===false && strpos($REQUEST_URI,"mail.php")===false
  $session['user']['restorepage']=$REQUEST_URI;
}else{

}

if (!isset($session['user']['hitpoints']))
{
    $session['user']['hitpoints']='';
}

if ($session['user']['hitpoints']>0)
{
    $session['user']['alive'] = true;
}
else
{
    $session['user']['alive'] = false;
}

if (isset($session['user']['bufflist']))
{
    $session['bufflist'] = unserialize($session['user']['bufflist']);
}

if (!isset($session['bufflist']))
{
    $session['bufflist'] = '';
}

if (!is_array($session['bufflist'])) $session['bufflist']=array();
$session['user']['lastip'] = $REMOTE_ADDR;
if (strlen($_COOKIE['lgi'])<32){
	if (strlen($session['user']['uniqueid'])<32){
		$u=md5(microtime());
		setcookie("lgi",$u,strtotime("+365 days"));
		$_COOKIE['lgi']=$u;
		$session['user']['uniqueid']=$u;
	}else{
		setcookie("lgi",$session['user']['uniqueid'],strtotime("+365 days"));
	}
}else{
	$session['user']['uniqueid']=$_COOKIE['lgi'];
}

$url = "http://".$_SERVER['SERVER_NAME'].dirname($_SERVER['REQUEST_URI']);
$url = substr($url,0,strlen($url)-1);

if (!isset($_SERVER['HTTP_REFERER']))
{
    $_SERVER['HTTP_REFERER']='';
}

if (substr($_SERVER['HTTP_REFERER'],0,strlen($url))==$url || $_SERVER['HTTP_REFERER'] == ""){

}else{
	$sql = "SELECT * FROM referers WHERE uri='{$_SERVER['HTTP_REFERER']}'";
	$result = db_query($sql);
	$row = db_fetch_assoc($result);
	db_free_result($result);
	$site = str_replace("http://","",$_SERVER['HTTP_REFERER']);
	if (strpos($site,"/")) 
		$site = substr($site,0,strpos($site,"/"));
	if ($row['refererid']>""){
		$sql = "UPDATE referers SET count=count+1,last=now(),site='".addslashes($site)."' WHERE refererid='{$row['refererid']}'";
	}else{
		$sql = "INSERT INTO referers (uri,count,last,site) VALUES ('{$_SERVER['HTTP_REFERER']}',1,now(),'".addslashes($site)."')";
	}
	db_query($sql);
}

if ($_COOKIE['template'] != "") $templatename = $_COOKIE['template'];
if (!file_exists("templates/$templatename") || $templatename == "") $templatename = "yarbrough.htm";
$template = loadtemplate($templatename);
// tags that must appear in the header
$templatetags = array("title","headscript","script");
while (list($key,$val)=each($templatetags)){
	if (strpos($template['header'],"{".$val."}")===false) $templatemessage.="You do not have {".$val."} defined in your header\n";
}
// tags that must appear in the footer
$templatetags=array();
while (list($key,$val) = each($templatetags)){
	if (strpos($template['footer'],"{".$val."}")===false) $templatemessage.="You do not have {".$val."} defined in your footer\n";
}
// tags that may appear anywhere but must appear
$templatetags=array("nav","stats","petition","motd","mail","copyright","source");
while (list($key,$val) = each($templatetags)){
	if (strpos($template['header'],"{".$val."}")===false && strpos($template['footer'],"{".$val."}")===false) $templatemessage.="You do not have {".$val."} defined in either your header or footer\n";
}

if (!isset($templatemessage))
{
    $templatemessage = '';
}

if ($templatemessage != "")
{
    echo "<b>You have one or more errors in your template page!</b><br>".nl2br($templatemessage);
    $template = loadtemplate("yarbrough.htm");
}

$races = array(1=>"Troll",2=>"Elf",3=>"Human",4=>"Dwarf",0=>"Unknown",50=>"Hoversheep");

$logd_version = "TDS-0.01";
$session['user']['laston'] = date("Y-m-d H:i:s");

if (isset($session['user']['hashorse']))
{
    $playermount = getmount($session['user']['hashorse']);
}
else
{
    $playermount = '';
}

$titles = array(
	0=>array("Farmboy","Farmgirl"),
	1=>array("Page", "Page"),
	2=>array("Squire", "Squire"),
	3=>array("Gladiator", "Gladiatrix"),
	4=>array("Legionnaire","Legioness"),
	5=>array("Centurion","Centurioness"),
	6=>array("Sir","Madam"),
	7=>array("Reeve", "Reeve"),
	8=>array("Steward", "Stewardess"),
	9=>array("Mayor", "Mayoress"),
	10=>array("Baron", "Baroness"),
	11=>array("Count", "Countess"),
	12=>array("Viscount", "Viscountess"),
	13=>array("Marquis", "Marquisette"),
	14=>array("Chancellor", "Chancelress"),
	15=>array("Prince", "Princess"),
	16=>array("King", "Queen"),
	17=>array("Emperor", "Empress"),
	18=>array("Angel", "Angel"),
	19=>array("Archangel", "Archangel"),
	20=>array("Principality", "Principality"),
	21=>array("Power", "Power"),
	22=>array("Virtue", "Virtue"),
	23=>array("Dominion", "Dominion"),
	24=>array("Throne", "Throne"),
	25=>array("Cherub", "Cherub"),
	26=>array("Seraph", "Seraph"),
	27=>array("Demigod", "Demigoddess"),
	28=>array("Titan", "Titaness"),
	29=>array("Archtitan", "Archtitaness"),
	30=>array("Undergod", "Undergoddess"),
);

if (!isset($session['user']['beta']))
{
    $session['user']['beta']='';
}

$beta = (getsetting("beta",0) == 1 || $session['user']['beta']==1);
?>
