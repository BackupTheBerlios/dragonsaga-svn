<?php
// do some cleanup here to make sure magic_quotes_gpc is ON,
// and magic_quotes_runtime is OFF, and error reporting is all but notice.
error_reporting (E_ALL ^ E_NOTICE);
ini_set("arg_separator.output","&amp;");

//error_reporting (E_ALL);
if (!get_magic_quotes_gpc()){
	set_magic_quotes($_GET);
	set_magic_quotes($_POST);
	set_magic_quotes($_SESSION);
	set_magic_quotes($_COOKIE);
	set_magic_quotes($HTTP_GET_VARS);
	set_magic_quotes($HTTP_POST_VARS);
	set_magic_quotes($HTTP_COOKIE_VARS);
	ini_set("magic_quotes_gpc",1);
}
set_magic_quotes_runtime(0);

function set_magic_quotes(&$vars) {
	//eval("\$vars_val =& \$GLOBALS[$vars]$suffix;");
	if (is_array($vars)) {
		reset($vars);
		while (list($key,$val) = each($vars))
			set_magic_quotes($vars[$key]);
	}else{
		$vars = addslashes($vars);
		//eval("\$GLOBALS$suffix = \$vars_val;");
	}
}

define('DBTYPE',"mysql");

$dbqueriesthishit=0;

function db_query($sql){
	global $session,$dbqueriesthishit, $db;
	$dbqueriesthishit++;
	$fname = DBTYPE."_query";
	$r = $fname($sql) or die(($session['user']['superuser']>=3 || 1?"<pre>".HTMLEntities($sql)."</pre>":"").db_error(LINK));
	return $r;
}

function new_db_query($sql){
	global $session,$dbqueriesthishit, $db;
	$dbqueriesthishit++;
	$fname = DBTYPE."_query";
//	$r = $fname($sql) or die(($session['user']['superuser']>=3 || 1?"<pre>".HTMLEntities($sql)."</pre>":"").db_error(LINK));

        $executed = $db->Execute($sql);
        $r = $executed->fields;
	return $r;
}

function db_error($link){
	$fname = DBTYPE."_error";
	$r = $fname($link);
	return $r;
}

function db_fetch_assoc($result){
	$fname = DBTYPE."_fetch_assoc";
	$r = $fname($result);
	return $r;
}

function db_num_rows($result){
	$fname = DBTYPE."_num_rows";
	$r = $fname($result);
	return $r;
}

function db_affected_rows($link=false){
	$fname = DBTYPE."_affected_rows";
	if ($link===false) {
		$r = $fname();
	}else{
		$r = $fname($link);
	}
	return $r;
}

// this function is not a persistant contection to db
// to use persistant connections uncomment the function below
// and comment this one out.
function db_pconnect($host,$user,$pass){
	$fname = DBTYPE."_connect";
	$r = $fname($host,$user,$pass);

/* This is a horrible way to shoehorn this in, but for the time being, it works. */
// Adodb handles database abstraction
if (!@include_once ("./backends/adodb/adodb.inc.php"))
{
    echo "adodb.inc.php ";
    echo "cannot be found, and it is required for TKI/BNT to run.";
    die();
}

// Adodb handles database abstraction
if (!@include_once ("./backends/adodb/adodb-perf.inc.php"))
{
    echo "adodb-perf.inc.php ";
    echo "cannot be found, and it is required for TKI/BNT to run.";
    die();
}

// XML Schema handler
if (!@include_once ("./backends/adodb/adodb-xmlschema.inc.php"))
{
    echo "adodb-xmlschema.inc.php ";
    echo "cannot be found, and it is required for TKI/BNT to run.";
    die();
}

/*
// encrypted session handler
if (!@include_once ("./backends/adodb/session/adodb-cryptsession.php"))
{
    echo "adodb-cryptsession.php ";
    echo "cannot be found, and it is required for TKI/BNT to run.";
    die();
}

// compressed session handler
if (!@include_once ("./backends/adodb/session/adodb-compress-gzip.php"))
{
    echo "adodb-compress-gzip.php ";
    echo "cannot be found, and it is required for TKI/BNT to run.";
    die();
}
*/
global $dbport, $ADODB_SESSION_USER, $ADODB_SESSION_PWD, $ADODB_SESSION_CONNECT;
global $db, $ADODB_SESSION_DB, $ADODB_SESSION_DRIVER;

$dbport = '3306';
$ADODB_SESSION_USER = $user;
$ADODB_SESSION_PWD = $pass;
$ADODB_SESSION_CONNECT = 'localhost';
$ADODB_SESSION_DB = '';
$ADODB_SESSION_DRIVER = 'mysqlt';

adodb_connectdb();
	return $r;

}

/*
function db_pconnect($host,$user,$pass){
	$fname = DBTYPE."_pconnect";
	$r = $fname($host,$user,$pass);
	return $r;
}
*/

function db_select_db($dbname){
	$fname = DBTYPE."_select_db";
	$r = $fname($dbname);
	return $r;
}
function db_free_result($result){
	$fname = DBTYPE."_free_result";
	$r = $fname($result);
	return $r;
}

function adodb_connectdb()
{
    // connect to database - and if we can't stop right there
    global $dbport, $ADODB_SESSION_USER, $ADODB_SESSION_PWD, $ADODB_SESSION_CONNECT;
    global $db, $ADODB_SESSION_DB, $ADODB_SESSION_DRIVER;

    $ADODB_NEVER_PERSIST   = true; // Prevent any persistent connections ever.
    $ADODB_COUNTRECS = false; // This *deeply* improves the speed of adodb.

    if (!function_exists('mysql_connect'))
    {
        die ("The mysql_connect function is not loaded - you need the php-mysql module installed for this game to function");
        return 0;
    }

    if (!empty($dbport))
    {
        $ADODB_SESSION_CONNECT.= ":$dbport";
    }

    $db = ADONewConnection("$ADODB_SESSION_DRIVER");
    $db->debug=0;
    $db->autoRollback = true;
    $result = $db->Connect("$ADODB_SESSION_CONNECT", "$ADODB_SESSION_USER", "$ADODB_SESSION_PWD", "$ADODB_SESSION_DB");

    if (!$result)
    {
        die ("Unable to connect to the database: " . $db->ErrorMsg());
        return 0;
    }
}
?>
