<?
require_once "common.php";
$trash = getsetting("expiretrashacct",1);
$new = getsetting("expirenewacct",10);
$old = getsetting("expireoldacct",45);

checkban();

if ($_GET[op]=="val"){
	$sql = "SELECT login,password FROM accounts WHERE emailvalidation='$_GET[id]' AND emailvalidation!=''";
	$result = db_query($sql);
	if (db_num_rows($result)>0) {
		$row = db_fetch_assoc($result);
		$sql = "UPDATE accounts SET emailvalidation='' WHERE emailvalidation='$_GET[id]' AND emailvalidation!=''";
		db_query($sql);
		output("`#`cYour email has been validated.  You may now log in.`c`0");
		output("<form action='login.php' method='POST'><input name='name' value=\"$row[login]\" type='hidden'><input name='password' value=\"$row[password]\" type='hidden'>
		Your email has been validated, your login name is `^$row[login]`0.  `n`n<input type='submit' class='button' value='Click here to log in'></form>`n`n"
		.($trash>0?"Characters that have never been logged in to will be deleted after $trash day(s) of no activity.`n":"")
		.($new>0?"Characters that have never reached level 2 will be deleted after $new days of no activity.`n":"")
		.($old>0?"Characters that have reached level 2 at least once will be deleted after $old days of no activity.":"")
		."",true);
	}else{
		output("`#Your email could not be verified.  This may be because you already validated your email.  Try to log in, and if that doesn't help, use the petition link at the bottom of the page.");
	}
}
if ($_GET[op]=="forgot"){
	if ($_POST[charname]!=""){
		$sql = "SELECT login,emailaddress,emailvalidation,password FROM accounts WHERE login='$_POST[charname]'";
		$result = db_query($sql);
		if (db_num_rows($result)>0){
			$row = db_fetch_assoc($result);
			if (trim($row[emailaddress])!=""){
				if ($row[emailvalidation]==""){
					$row[emailvalidation]=substr("x".md5(date("Y-m-d H:i:s").$row[password]),0,32);
					$sql = "UPDATE accounts SET emailvalidation='$row[emailvalidation]' where login='$row[login]'";
					db_query($sql);
				}
				mail(
					$row[emailaddress],
					"LoGD Account Verification",
					"Someone from ".$_SERVER['REMOTE_ADDR']." requested a forgotten password link for your account.  If this was you, then here is your"
					." link, you may click it to log in to your account and change your password from your preferences page in the village square.\n\n"
					."If you didn't request this email, then don't sweat it, you're the one who are receiving this email, not them."
					."\n\n  http://".$_SERVER['SERVER_NAME'].$_SERVER['SCRIPT_NAME']."?op=val&id=$row[emailvalidation]\n\nThanks for playing!",
					"From: ".getsetting("gameadminemail","postmaster@localhost.com")
				);
				output("`#Sent a new validation email to the address on file for that account.  You may use the validation email to log in and change your password.");
			}else{
				output("`#We're sorry, but that account does not have an email address associated with it, and so we cannot help you with your
				forgotten password.  Use the Petition for Help link at the bottom of the page to
				request help with resolving your problem.");
			}
		}else{
			output("`#Could not locate a character with that name.  Look at the List Warriors page off the login page to make sure that the character hasn't expired and been deleted.");
		}
	}else{
		output("<form action='create.php?op=forgot' method='POST'>
		`bForgotten Passwords:`b`n`n
		Enter your character's name: <input name='charname'>`n
		<input type='submit' class='button' value='Email me my password'>
		</form>",true);
	}
}
page_header("Create A Character");
if ($HTTP_GET_VARS[op]=="create"){
	if(getsetting("spaceinname",0) == 0) {
  		$shortname = preg_replace("([^[:alpha:]_-])","",$HTTP_POST_VARS[name]);
	} else {
  		$shortname = preg_replace("([^[:alpha:] _-])","",$HTTP_POST_VARS[name]);
	}

	if (soap($shortname)!=$shortname){
		output("`\$Error`^: Bad language was found in your name, please consider revising it.");
		$HTTP_GET_VARS[op]="";
	}else{
		$blockaccount=false;
		if (getsetting("blockdupeemail",0)==1 && getsetting("requireemail",0)==1){
			$sql = "SELECT login FROM accounts WHERE emailaddress='$_POST[email]'";
			$result = db_query($sql); 
			if (db_num_rows($result)>0){
				$blockaccount=true;
				$msg.="You may have only one account.`n";
			}
		}
		if (strlen($HTTP_POST_VARS[pass1])<=3){
			$msg.="Your password must be at least 4 characters long.`n";
			$blockaccount=true;
		}
		if ($_POST[pass1]!=$_POST[pass2]){
			$msg.="Your passwords do not match.`n";
			$blockaccount=true;
		}
		if (strlen($shortname)<3){
			$msg.="Your name must be at least 3 characters long.`n";
			$blockaccount=true;
		}
		if (strlen($shortname)>25){
			$msg.="Your character's name cannot exceed 25 characters.`n";
			$blockaccount=true;
		}
		if (getsetting("requireemail",0)==1 && is_email($_POST[email]) || getsetting("requireemail",0)==0){
		}else{
			$msg.="You must enter a valid email address.`n";
			$blockaccount=true;
		}
		/*
		if ($HTTP_POST_VARS[pass1]==$HTTP_POST_VARS[pass2] 
		&& strlen($HTTP_POST_VARS[pass1])>3 
		&& strlen($shortname)>2 
		&& !$blockaccount
		&& (
				getsetting("requireemail",0)==1 
			&& is_email($_POST[email]) 
			|| getsetting("requireemail",0)==0
				)
		){*/
		if (!$blockaccount){
			$sql = "SELECT name FROM accounts WHERE login='$shortname'";
			$result = db_query($sql) or die(db_error(LINK));
			if (db_num_rows($result)>0){
				output("`\$Error`^: Someone is already known by that name in this realm, please try again.");
				$HTTP_GET_VARS[op]="";
			}else{
				$title = ($HTTP_POST_VARS[sex]?"Farmgirl":"Farmboy");
				if (getsetting("requirevalidemail",0)){
					$emailverification=md5(date("Y-m-d H:i:s").$_POST[email]);
				}
				if ($_GET['r']>""){
					$sql = "SELECT acctid FROM accounts WHERE login='{$_GET['r']}'";
					$result = db_query($sql);
					$ref = db_fetch_assoc($result);
					$referer=$ref['acctid'];
				}else{
					$referer=0;
				}
				$sql = "INSERT INTO accounts 
					(name,
					title,
					password,
					sex,
					login,
					laston,
					uniqueid,
					lastip,
					superuser,
					gold,
					emailaddress,
					emailvalidation,
					referer
				) VALUES (
					'$title $shortname',
					'$title',
					'$HTTP_POST_VARS[pass1]',
					'$HTTP_POST_VARS[sex]',
					'$shortname',
					'".date("Y-m-d H:i:s",strtotime("-1 day"))."',
					'$_COOKIE[lgi]',
					'".$_SERVER['REMOTE_ADDR']."',
					".getsetting("superuser",0).",
					".getsetting("newplayerstartgold",50).",
					'$_POST[email]',
					'$emailverification',
					'$referer'
				)";
				db_query($sql) or die(db_error(LINK));
				if (db_affected_rows(LINK)<=0){
					output("`\$Error`^: Your account was not created for an unknown reason, please try again. ");
				}else{
					if ($emailverification!=""){
						mail(
							$_POST[email],
							"LoGD Account Verification",
							"In order to verify your account, you will need to click on the link below.\n\n  http://".$_SERVER['SERVER_NAME'].$_SERVER['SCRIPT_NAME']."?op=val&id=$emailverification\n\nThanks for playing!",
							"From: ".getsetting("gameadminemail","postmaster@localhost.com")
						);
						output("`4An email was sent to `\$$_POST[email]`4 to validate your address.  Click the link in the email to activate your account.`0`n`n");
					}else{
						output("<form action='login.php' method='POST'><input name='name' value=\"$shortname\" type='hidden'><input name='password' value=\"$HTTP_POST_VARS[pass1]\" type='hidden'>
						Your account was created, your login name is `^$shortname`0.  `n`n<input type='submit' class='button' value='Click here to log in'></form>`n`n"
						.($trash>0?"Characters that have never been logged in to will be deleted after $trash day(s) of no activity.`n":"")
						.($new>0?"Characters that have never reached level 2 will be deleted after $new days of no activity.`n":"")
						.($old>0?"Characters that have reached level 2 at least once will be deleted after $old days of no activity.":"")
						."",true);
					}
				}
			}
		}else{
			/*
			output("`\$Error`^: Your password must be at least 4 characters long, 
			your name must be at least 3 characters long, 
			".(getsetting("requireemail",0)==1?"you must enter a valid email address, ":"")."
			".(getsetting("blockdupeemail",0)==1?"you must not have any other accounts by that email address, ":"")."
			and your passwords must match.");
			*/
			output("`\$Error`^:`n$msg");
			$HTTP_GET_VARS[op]="";
		}
	}
}
if ($HTTP_GET_VARS[op]==""){
	output("`&`c`bCreate a Character`b`c");
	output("`0<form action=\"create.php?op=create".($_GET['r']>""?"&r=".HTMLEntities($_GET['r']):"")."\" method='POST'>",true);
	output("How will you be known to this world? <input name='name'>`n",true);
	output("Enter a password: <input type='password' name='pass1'>`n",true);
	output("Re-enter it for confirmation: <input type='password' name='pass2'>`n",true);
	output("Enter your email address: <input name='email'> ".(getsetting("requireemail",0)==0?"(optional -- however, if you choose not to enter one, there will be no way that you can reset your password if you forget it!)":"(required".(getsetting("requirevalidemail",0)==0?"":", an email will be sent to this address to verify it before you can log in").")")."`n",true);
	output("And are you a <input type='radio' name='sex' value='1'>Female or a <input type='radio' name='sex' value='0' checked>Male?`n",true);
	output("<input type='submit' class='button' value='Create your character'>",true);
}
addnav("Login","index.php");
page_footer();
?>
