<?
require_once "dbconnect.php";
if ($_SERVER['REMOTE_ADDR']=="207.245.115.116" || $_SERVER['REMOTE_ADDR']=="192.168.0.1" || $_SERVER['REMOTE_ADDR']=="127.0.0.1"){
	$name = "logdbackup-".date("Y-m-d H:i").".sql.gz";
	header("Content-Type: application/x-gzip; name=\"".htmlentities($name)."\";");
	header("Content-Disposition: attachment/download; filename=\"".htmlentities($name)."\";");
	$cmd = "mysqldump -u \"$DB_USER\" --pass=\"$DB_PASS\" -h \"$DB_HOST\" \"$DB_NAME\" | gzip -9";
	//echo $cmd;
	//exit;
	//$out = e_exec($cmd);
	//header("Content-Length: " & strlen($out));
	//echo $out;
	e_exec($cmd);
	
	exec("webalizer -c ~lotgd/webalizer.conf");
}else{
	//echo $_SERVER['REMOTE_ADDR'];
}

function e_exec($cmd){
  $fp=popen("$cmd",'r');
	fpassthru($fp);
  //$output="";
  //while (!feof($fp)){
  // $b=fgets($fp,4096);
  //  $output.=$b;
  //}
  //pclose($fp);
  //return $output;
}

?>