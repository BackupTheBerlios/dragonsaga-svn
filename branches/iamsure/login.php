<?php
require_once "common.php";

if (!isset($session['loggedin']))
{
    $session['loggedin']='';
}

if (isset($_POST['name']) && ($_POST['name']!="")){
	if ($session['loggedin']){
		redirect("badnav.php");
	}else{
		if(0){
		}else{
			$sql = "SELECT * FROM accounts WHERE login='".$_POST['name']."' AND password='".$_POST['password']."' AND locked=0";
			$result = db_query($sql);
			if (db_num_rows($result)==1){
				$session['user']=db_fetch_assoc($result);
				//echo "Ooga Booga";
				//flush();
				//exit();
				checkban($session['user']['login']); //check if this account is banned
				checkban(); //check if this computer is banned

				if ($session['user']['emailvalidation']!="" && substr($session['user']['emailvalidation'],0,1)!="x"){
					$session['user']=array();
					$session['message']="`4Error, you must validate your email address before you can log in.";
					echo $session['message'];
					//header("Location: index.php");
					exit();
				}else{
					//loaduser($session['user']);
					$session['loggedin']=true;
					$session['output']=$session['user']['output'];
					$session['laston']=date("Y-m-d H:i:s");
					$session['sentnotice']=0;
					$session['user']['dragonpoints']=unserialize($session['user']['dragonpoints']);
					$session['user']['prefs']=unserialize($session['user']['prefs']);
					$session['bufflist']=unserialize($session['user']['bufflist']);
					if (!is_array($session['user']['dragonpoints'])) $session['user']['dragonpoints']=array();
					if ($session['user']['loggedin']){
						$session['allowednavs']=unserialize($session['user']['allowednavs']);
						saveuser();
						header("Location: {$session['user']['restorepage']}");
						exit();
						//redirect($session['user']['page']);//"badnav.php");
					}
					db_query("UPDATE accounts SET loggedin=".true.", location=0 WHERE acctid = ".$session['user']['acctid']);
					$session['user']['loggedin']=true;
					$location = $session['user']['location'];
					$session['user']['location']=0;
					if ($session['user']['alive']==0 && $session['user']['slainby']!=""){
						//they're not really dead, they were killed in pvp.
						$session['user']['alive']=true;
					}
					if (getsetting("logdnet",0)){
						//register with LoGDnet
						@file(getsetting("logdnetserver","http://lotgd.net/")."logdnet.php?addy=".URLEncode(getsetting("serverurl","http://".$_SERVER['SERVER_NAME'].dirname($_SERVER['REQUEST_URI'])))."&desc=".URLEncode(getsetting("serverdesc","Another LoGD Server"))."");
					}
					if ($location==0){
						redirect("news.php");
					}else if($location==1){
						redirect("inn.php?op=strolldown");
					}else{
						saveuser();
						header("Location: {$session['user']['restorepage']}");
						exit();
					}
				}
			}else{
				$session['message']="`4Error, your login was incorrect`0";
				//now we'll log the failed attempt and begin to issue bans if there are too many, plus notify the admins.
				$sql = "DELETE FROM faillog WHERE date<'".date("Y-m-d H:i:s",strtotime("-".(getsetting("expirecontent",180)/4)." days"))."'";
				checkban();
				db_query($sql);
				$sql = "SELECT acctid FROM accounts WHERE login='{$_POST['name']}'";
				$result = db_query($sql);
				if (db_num_rows($result)>0){ // just in case there manage to be multiple accounts on this name.
					while ($row=db_fetch_assoc($result)){
						$sql = "INSERT INTO faillog VALUES (0,now(),'".addslashes(serialize($_POST))."','{$_SERVER['REMOTE_ADDR']}','{$row['acctid']}','{$_COOKIE['lgi']}')";
						db_query($sql);
						$sql = "SELECT faillog.*,accounts.superuser,name,login FROM faillog INNER JOIN accounts ON accounts.acctid=faillog.acctid WHERE ip='{$_SERVER['REMOTE_ADDR']}' AND date>'".date("Y-m-d H:i:s",strtotime("-1 day"))."'";
						$result2 = db_query($sql);
						$c=0;
						$alert="";
						$su=false;
						while ($row2=db_fetch_assoc($result2)){
							if ($row2['superuser']>0) {$c+=1; $su=true;}
							$c+=1;
							$alert.="`3{$row2['date']}`7: Failed attempt from `&{$row2['ip']}`7 [`3{$row2['id']}`7] to log on to `^{$row2['login']}`7 ({$row2['name']}`7)`n";
						}
						if ($c>=10){ // 5 failed attempts for superuser, 10 for regular user
							$sql = "INSERT INTO bans VALUES ('{$_SERVER['REMOTE_ADDR']}','','".date("Y-m-d H:i:s",strtotime("+".($c*3)." hours"))."','Automatic System Ban: Too many failed login attempts.')";
							db_query($sql);
							if ($su){ // send a system message to admins regarding this failed attempt if it includes superusers.
								$sql = "SELECT acctid FROM accounts WHERE superuser>=3";
								$result2 = db_query($sql);
								$subj = "`#{$_SERVER['REMOTE_ADDR']} failed to log in too many times!";
								for ($i=0;$i<db_num_rows($result2);$i++){
									$row2 = db_fetch_assoc($result2);
									//delete old messages that 
									$sql = "DELETE FROM mail WHERE msgto={$row2['acctid']} AND msgfrom=0 AND subject = '$subj' AND seen=0";
									db_query($sql);
									if (db_affected_rows()>0) $noemail = true; else $noemail = false;
									systemmail($row2['acctid'],"$subj","This message is generated as a result of one or more of the accounts having been a superuser account.  Log Follows:`n`n$alert",0,$noemail);
								}//end for
							}//end if($su)
						}//end if($c>=10)
					}//end while
				}else{
					
				}//end if (db_num_rows)
				redirect("index.php");
			}
		}
	}
}else if ($_GET['op']=="logout"){
        if (!isset($session['user']['loggedin']))
        {
            $session['user']['loggedin']='';
        }

	if ($session['user']['loggedin']){
	  $sql = "UPDATE accounts SET loggedin=0 WHERE acctid = ".$session['user']['acctid'];
		db_query($sql) or die(sql_error($sql));
	}
	$session=array();
	redirect("index.php");
}
// If you enter an empty username, don't just say oops.. do something useful.
$session=array();
$session['message']="`4Error, your login was incorrect`0";
redirect("index.php");
?>
