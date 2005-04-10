<?php
// This program is free software; you can redistribute it and/or modify it
// under the terms of the GNU General Public License as published by the
// Free Software Foundation; either version 2 of the License, or (at your
// option) any later version.
//
// File: prefs.php

if (isset($_POST['template'])){
	setcookie("template",$_POST['template'],strtotime("+45 days"));
	$_COOKIE['template']=$_POST['template'];
}
require_once "common.php";
page_header("Preferences");

if (!isset($_GET['op']))
{
    $_GET['op'] = '';
}
if ($_GET['op']=="suicide" && getsetting("selfdelete",0)!=0) {
    $sql = "DELETE FROM accounts WHERE acctid='$_GET[userid]'";
	db_query($sql);
	output("Your character has been deleted!");
	addnews("`#{$session['user']['name']} committed suicide.");
	addnav("Login Page", "index.php");
	$session=array();
	$session[user] = array();
	$session[loggedin] = false;
	$session[user][loggedin] = false;
} else {

checkday();
if ($session['user']['alive']){
	addnav("Return to the village","village.php");
}else{
	addnav("Return to the news","news.php");
}
if (count($_POST)==0){
}else{
	if ($_POST['pass1']!=$_POST['pass2']){
		output("`#Your passwords do not match.`n");
	}else{
		if ($_POST['pass1']!=""){
			if (strlen($_POST['pass1'])>3){
				$session['user']['password']=$_POST['pass1'];
				output("`#Your password has been changed.`n");
			}else{
				output("`#Your password is too short.  It must be at least 4 characters.`n");
			}
		}
	}
	reset($_POST);
	$nonsettings = array("pass1"=>1,"pass2"=>1,"email"=>1,"template"=>1,"bio"=>1);
	while (list($key,$val)=each($_POST)){
                if (!isset($_POST[$key]))
                {
                    $_POST[$key] = '';
                }
                if (!isset($nonsettings[$key]))
                {
                    $nonsettings[$key] = '';
                }
		if (!$nonsettings[$key]) $session['user']['prefs'][$key]=$_POST[$key];
	}
	if (stripslashes($_POST['bio'])!=$session['user']['bio']){
		if ($session['user']['biotime']>"9000-01-01"){
			output("`n`\$You cannot modify your bio, it's been blocked by the administrators!`0`n");
		}else{
			$session['user']['bio']=stripslashes($_POST['bio']);
			$session['user']['biotime']=date("Y-m-d H:i:s");
		}
	}
	if ($_POST['email']!=$session['user']['emailaddress']){
		if (is_email($_POST['email'])){
			if (getsetting("requirevalidemail",0)==1){
				output("`#Your email cannot be changed, system settings prohibit it.  (Emails may only be changed if the server allows more than one account per email).  Use the Petition link to ask the  server administrator to change your email address if this one is no longer valid.`n");
			}else{
				output("`#Your email address has been changed.`n");
				$session['user']['emailaddress']=$_POST['email'];
			}
		}else{
			if (getsetting("requireemail",0)==1){
				output("`#That is not a valid email address.`n");
			}else{
				output("`#Your email address has been changed.`n");
				$session['user']['emailaddress']=$_POST['email'];
			}
		}
	}
	output("Settings Saved");
}

	$form=array(
		"Preferences,title"
		,"emailonmail"=>"Send email when you get new Ye Olde Mail?,bool"
		,"systemmail"=>"Send email for system generated messages?,bool"
		,"dirtyemail"=>"Allow profanity in received Ye Olde Poste messages?,bool"
		,"language"=>"Language (Not Yet Complete),enum,en,English,de,Deutsch,dk,Danish,es,Español"
		,"bio"=>"Short Character Biography (255 chars max)`n"
	);
	output("
	<form action='prefs.php?op=save' method='POST'>",true);
	if ($handle = @opendir("templates")){
		$skins = array();
		while (false !== ($file = @readdir($handle))){
			if (strpos($file,".htm")>0){
				array_push($skins,$file);
			}
		}
		if (count($skins)==0){
			output("`b`@Aww, your administrator has decided you're not allowed to have any skins.  Complain to them, not me.`n");
		}else{
			output("<b>Skin:</b><br>",true);
			while (list($key,$val)=each($skins)){
				output("<input type='radio' name='template' value='$val'".($_COOKIE['template']==""&&$val=="yarbrough.htm" || $_COOKIE['template']==$val?" checked":"").">".substr($val,0,strpos($val,".htm"))."<br>",true);
			}
		}
	}else{
		output("`c`b`\$ERROR!!!`b`c`&Unable to open the templates folder!  Please notify the administrator!!");
	}
	
	output("
	New Password: <input name='pass1' type='password'> (leave blank if you don't want to change it)`n
	Retype: <input name='pass2' type='password'>`n
	Email address: <input name='email' value=\"".HTMLEntities($session['user']['emailaddress'])."\">`n
	",true);
	$prefs = $session['user']['prefs'];
	$prefs['bio'] = $session['user']['bio'];
	showform($form,$prefs);
	output("
	</form>",true);
	addnav("","prefs.php?op=save");

	// Stop clueless lusers from deleting their character just because a
	// monster killed them.
	if ($session['user']['alive'] && getsetting("selfdelete",0)!=0) {
		output("<form action='prefs.php?op=suicide&userid={$session['user']['acctid']}' method='POST'>",true);
		output("<input type='submit' class='button' value='Delete Character' onClick='return confirm(\"Are you sure you wish to delete your character?\");'>", true);
		output("</form>",true);
		addnav("","prefs.php?op=suicide&userid={$session['user']['acctid']}");
	}
}
page_footer();
?>
