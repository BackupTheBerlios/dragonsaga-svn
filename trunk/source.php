<?
$url=$_GET['url'];
$dir = str_replace("\\","/",dirname($url)."/");
$subdir = str_replace("\\","/",dirname($_SERVER['SCRIPT_NAME'])."/");
//echo "<pre>$subdir</pre>";
$legal_dirs = array(
	$subdir."" => 1,
  $subdir."special/"  => 1
);
$illegal_files = array(
	($subdir=="//"?"/":$subdir)."dbconnect.php"=>"it contains sensitive information specific to this installation.",
	($subdir=="//"?"/":$subdir)."dragon.php"=>"If you want to read the dragon script, I suggest you do so by defeating it!",
	($subdir=="//"?"/":$subdir)."topwebvote.php"=>"X", // hide completely
	($subdir=="//"?"/":$subdir)."lodge.php"=>"Not released at least for now.",
	($subdir=="//"?"/":$subdir)."remotebackup.php"=>"X" // hide completely
);
$legal_files=array();

echo "<h1>View Source: ", htmlentities($url), "</h1>";
echo "<a href='#source'>Click here for the source,</a> OR<br>";
echo "<b>Other files that you may wish to view the source of:</b><ul>";
while (list($key,$val)=each($legal_dirs)){
	//echo "<pre>$key</pre>";
	$skey = substr($key,strlen($subdir));
	//echo $skey." ".$key;
	if ($key==dirname($_SERVER[SCRIPT_NAME])) $skey="";
	$d = dir("./$skey");
	if (substr($key,0,2)=="//") $key = substr($key,1);
	if ($key=="//") $key="/";
	while (false !== ($entry = $d->read())) {
			if (substr($entry,strrpos($entry,"."))==".php"){
				if ($illegal_files["$key$entry"]!=""){
					if ($illegal_files["$key$entry"]=="X"){
						//we're hiding the file completely.
					}else{
						echo "<li>$skey$entry &#151; this file cannot be viewed: ".$illegal_files["$key$entry"]."</li>\n";
					}
				}else{
					echo "<li><a href='source.php?url=$key$entry'>$skey$entry</a></li>\n";
					$legal_files["$key$entry"]=true;
				}
			}
	}
	$d->close();
}
echo "</ul>";

echo "<h1><a name='source'>Source of: ", htmlentities($url), "</a></h1>";

$page_name = substr($url,strlen($subdir)-1);
if (substr($page_name,0,1)=="/") $page_name=substr($page_name,1);
if ($legal_files[$url]){
	show_source($page_name);
}else if ($illegal_files[$url]!="" && $illegal_files[$url]!="X"){
	echo "<p>Cannot view this file: $illegal_files[$url]</p>";
}else {
	echo "<p>Cannot view this file.</p>";
}
?>
