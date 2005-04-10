<?php
// do some cleanup here to make sure magic_quotes_gpc is ON,
// and magic_quotes_runtime is OFF, and error reporting is all but notice.
include_once("./global_includes.php");

//error_reporting (E_ALL ^ E_NOTICE);
error_reporting (E_ALL);
ini_set("arg_separator.output","&amp;");

if (!get_magic_quotes_gpc()){
	set_magic_quotes($_GET);
	set_magic_quotes($_POST);
	set_magic_quotes($_SESSION);
	set_magic_quotes($_COOKIE);
	set_magic_quotes($_GET);
	set_magic_quotes($_POST);
	set_magic_quotes($_COOKIE);
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
//	$r = $fname($sql) or die(($session['user']['superuser']>=3 || 1?"<pre>".HTMLEntities($sql)."</pre>":"").db_error(LINK));

        $r = $db->Execute($sql);

        global $result;
        $result = $r;
	return $r;
}

function db_error($link){
        global $db;
        $r = $db->ErrorMsg();
	return $r;
}

function db_fetch_assoc($result){
        global $result;

        if (!$result->EOF)
        {
            $r = $result->GetRowAssoc(false);
        }
        else
        {
            $r = FALSE;
        }

        $result->MoveNext();

	return $r;
}

function db_num_rows($result){
        $r = $result->RecordCount();
	return $r;
}

function db_free_result($result){
        $r = $result->Close();
        return $r;
}

function db_affected_rows($link=false){
// Check to make sure this matches the original functionality.
	$fname = DBTYPE."_affected_rows";
	if ($link===false) {
//		$r = $fname();
	}else{
//		$r = $fname($link);
	}

        global $db;
        $r = $db->Affected_Rows();
	return $r;
}

// this function is not a persistant contection to db
// to use persistant connections uncomment the function below
// and comment this one out.
function db_pconnect($host,$user,$pass)
{
    global $dbport, $ADODB_SESSION_USER, $ADODB_SESSION_PWD, $ADODB_SESSION_CONNECT;
    global $db, $ADODB_SESSION_DB, $ADODB_SESSION_DRIVER;
    global $DB_NAME;

    $dbport = '3306';
    $ADODB_SESSION_USER = $user;
    $ADODB_SESSION_PWD = $pass;
    $ADODB_SESSION_CONNECT = 'localhost';
    $ADODB_SESSION_DB = $DB_NAME;
    $ADODB_SESSION_DRIVER = 'mysqlt';

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

    return $db;
}
?>
