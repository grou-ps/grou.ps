<?php
            
            /**
             * we drop off remember_me
             * feature for now... We had some problems
             * in this implementation and we don't want to lose
             * more time on it.
             * TODO: implement it in the next releases
             * review existing commented out code..
             * Code Implementation was made in
             * Access.php: login(), isAuthenticated()
             * authentication.php: html...
             */






session_start(); // we deal with sessions!

ob_start();

//if(!class_exists('DB'))
//	include_once dirname(__FILE__).'/DB/DB.php';
require_once 'DB.php';
require_once 'includes/VariableSecurity.php';
require_once 'includes/GeneralFunctions.php';
require_once 'Mailer.class.php';
include_once('configs/globals.php');
include_once('includes/cphplib/cphplib.inc');
require_once('includes/Analytics.class.php');
require_once('includes/undisposable_clients/php/php/undisposable.inc.php');

ob_end_clean();


if(!isset($_SESSION['valid_user'])) {
	if(isset($_COOKIE['username'])&&isset($_COOKIE['password'])) {
		if(shouldRememberUser($_COOKIE['username'],$_COOKIE['password'])) {
			login($_COOKIE['username'],$_COOKIE['password'],true,false,true);
		}
		else {
			$valid_user = '';
		}
	}
	else {
		$valid_user = '';
	}
}






	function AccessDB() {
		
		
		
		global $GlobalDatabase;
		global $db_type;
		global $db_username;
		global $db_password;
		global $db_host;
		global $db_name;
		
		if(!isset($GlobalDatabase)) {
		$dsn = array(
    			'phptype'  => $db_type,
   			'username' => $db_username,
   			'password' => $db_password,
    			'hostspec' => $db_host,
   			'database' => $db_name,
		);

		$options = array(
 			   'debug'       => 2,
			   'portability' => DB_PORTABILITY_ALL,
		);
		
		$Database =& DB::connect($dsn, $options);
		if (PEAR::isError($Database)) {
			die($Database->getMessage());
		}
        
        $q = & $Database->query("SET NAMES utf8;");
  
        if (PEAR::isError($q)) {
            die($q->getMessage());
        }
		
		return $Database;
		}
		else {
			return $GlobalDatabase;
		}
	

	}
	

	
	/**
	 * logs the user in 
	 * @param $username username, comes from net
	 * @param $password password, comes from net
	 * @param $remember_me, comes from net
	 * @returns if successful, true; else false
	 */
	function login($username,$password,$remember_me=false,$openid=false,$from_remember=false) {
		
		global $valid_user;
	
		$username = mysql_real_escape_string($username);
		$password = mysql_real_escape_string($password);
		
		$db = AccessDB();
		
		if(!$openid) {
			
			if(!$from_remember)
				$password_crypted = md5($password);
			else 
				$password_crypted = $password;
				
			if(strpos($username,'@')!==false)
				$mid = $db->getOne("SELECT member_id FROM members WHERE email=? AND member_password=? AND openid='no'", array($username,$password_crypted));
			else
				$mid = $db->getOne("SELECT member_id FROM members WHERE member_login=? AND member_password=? AND openid='no'", array($username,$password_crypted));
		}
		else {
			$mid = $db->getOne("SELECT member_id FROM members WHERE member_login=? AND openid='yes'", array($username));
		}
		
		if (PEAR::isError($mid)) {
			die($mid->getMessage());
		}
		
		if(empty($mid)) {
			return false;
		}
		else {
			
			/**
			 * if this is email login
			 */
			if(strpos($username,'@')!==false) {
				$nusername = $db->getOne("SELECT member_login FROM members WHERE email=?",array($username));
				
				if (PEAR::isError($nusername)) {
					die($nusername->getMessage());
				}
		
				if(empty($nusername)) {
					return false;
				}
				
				$username = $nusername;
				
			}
			
			
            /**
             * legacy code here
             */
            $valid_user = $username;
			session_register("valid_user");
			$_SESSION['valid_user'] = $username;
            /** 
             * shold be changed to this
             */
            //$_SESSION['valid_user'] = $username;
            
            

            if($remember_me||$from_remember) {
            	
				rememberUser($username,$password_crypted);
			}
			else {
				
				dontRememberUser($username);
			}
			
            
            
            
            /** 
             * analytics code here
             */
            
            if(class_exists('Analytics')) {
            	$ans =  new Analytics();
            	$ans->loggedIn($valid_user);
            }

            
            
            
            
            // set login recovered
            // because sure that he still knows
            // his password
            $u2e = _usernameToEmail($valid_user);
            if($u2e[0]) {
                
                $email = $u2e[1];
                
                
                // check for previous login recovery
                // requests
                setLoginRecovered($email);
            }
            
            
	    
			
			return true;
		}
		
	
	}
	
	
	
	function rememberUser($username,$pass,$login=false) {
		
		setcookie('username',$username,time()+60*60*24*30*3,"/"); // 3 months
		setcookie('password',$pass,time()+60*60*24*30*3,"/"); // 3 months
	}
	
	function shouldRememberUser($username,$pass) {
		
		$username = mysql_real_escape_string($username);
		$pass = mysql_real_escape_string($pass);
		
		$db = AccessDB();
		
		$mid = $db->getOne("SELECT COUNT(member_id) FROM members WHERE member_login='{$username}' AND member_password='{$pass}' AND openid='no'");
		
		if (PEAR::isError($mid)) {
			die($mid->getMessage());
		}
		
		return $mid==1;
	}
	
	function dontRememberUser($username) {
		
		setcookie('username',false, null,"/");
		setcookie('password',false, null, "/");
	}
	
	
	function logout() {
	
		global $valid_user;
		
		$tmpuser = "";
		
		/**
		 * first check if he/she is really
		 * logged in
		 */
		if(session_is_registered('valid_user')) {
			
			$tmpuser = $_SESSION['valid_user'];
			
			$res = session_unregister('valid_user');
			session_destroy();
			
			if($res) {
				
				dontRememberUser($tmpuser);

				/** 
	             * analytics code here
	             */
	            $ans =  new Analytics();
		        $ans->loggedOut($tmpuser);
		                
		        
				return true;
			}
			else {
				return false;
			}
		
		}
		else {
			return false;
		}
	
	}
    
    
	function changeEmail($oldpassword,$email) {
	
		if(isAuthenticated()) {
            
            $db = AccessDB();
            
            $oldpassword_encrypted = md5($oldpassword);
            $username = $_SESSION['valid_user'];
            
            $sql ="SELECT COUNT(member_id) FROM members WHERE member_login='{$username}' AND member_password='{$oldpassword_encrypted}'";
          
            $p = $db->getOne($sql);
		
            if (PEAR::isError($p)) {
                return array(false, "Internal error occured");
            }
            
            if($p!=1) {
                return array(false, "Wrong Old Password");
            }
            
            
            
            $cphplib = new cphplib();
		
            if(!$cphplib->checkEmail($email)) { // password not valid
                return array(false, "Invalid New Email");
            }
            
             $sql ="SELECT COUNT(member_id) FROM members WHERE email='{$email}'";
          
            $p = $db->getOne($sql);
		
            if (PEAR::isError($p)) {
                return array(false, "Internal error occured");
            }
            
            if($p!=0) {
                return array(false, "This email already exists in the system");
            }
            
            
            $sql = "UPDATE members SET email='{$email}' WHERE member_login='{$username}' AND member_password='{$oldpassword_encrypted}'";
            
            $q = $db->query($sql);
		
            if (PEAR::isError($q)||!$q) {
                return array(false, "Internal error occured");
            }
            else {
                return array(true,"");
            }
            
        }
        else {
            return array(false,"Can't authenticate user");
        }
	}
	
    
    function changePassword($oldpassword,$newpassword) {
        
        if(isAuthenticated()) {
            
            $db = AccessDB();
            
            $oldpassword_encrypted = md5($oldpassword);
            $username = $_SESSION['valid_user'];
            
            $sql ="SELECT COUNT(member_id) FROM members WHERE member_login='{$username}' AND member_password='{$oldpassword_encrypted}'";
          
            $p = $db->getOne($sql);
		
            if (PEAR::isError($p)) {
                return array(false, "Internal error occured");
            }
            
            if($p!=1) {
                return array(false, "Wrong Old Password");
            }
            
            $cphplib = new cphplib();
		
            if(!$cphplib->checkPassword($newpassword,5)) { // password not valid
                return array(false, "Invalid New Password");
            }
            
            $newpassword_encrypted = md5($newpassword);
            
            $sql = "UPDATE members SET member_password='{$newpassword_encrypted}' WHERE member_login='{$username}' AND member_password='{$oldpassword_encrypted}'";
            
            $q = $db->query($sql);
		
            if (PEAR::isError($q)||!$q) {
                return array(false, "Internal error occured");
            }
            else {
                return array(true,"");
            }
            
        }
        else {
            return array(false,"Can't authenticate user");
        }
        
        
    }
	
	
	function isAuthenticated() {
	
		//global $valid_user;
		
		//if(session_is_registered('valid_user')) {
		if(isset($_SESSION['valid_user'])) {
		
			return true;
			
		}
        
                    
            /**
             * we drop off remember_me
             * feature for now... We had some problems
             * in this implementation and we don't want to lose
             * more time on it.
             * TODO: implement it in the next releases
             * review existing commented out code..
             * Code Implementation was made in
             * Access.php: login(),isAuthenticated()
             * authentication.php: html...
             */
        
        
		/*elseif( isset($_COOKIE['username']) && isset($_COOKIE['password']) ) {
			
			$db = AccessDB();
			$mid = $db->getOne("SELECT member_id FROM members WHERE member_login='{$_COOKIE['username']}' AND member_password='{$_COOKIE['password']}'");
			
			if (PEAR::isError($mid)) {
				die($mid->getMessage());
			}
			
			if(empty($mid)) {
				return false;
			}
			else {
				$valid_user = $_COOKIE['username'];
				session_register('valid_user');
				return true;
			}
		}*/
        
		else {
			return false;
		}
	}
	
	
	function _getMemberID($username) {
	
		$db = AccessDB();
		
		$res = & $db->getOne("SELECT member_id FROM members WHERE member_login='{$username}'");
		
		if (PEAR::isError($res)) {
			die($res->getMessage());
		}
		
		return $res;
		
		
	}
    
	function _getMemberUsername($member_id) {
	
		$db = AccessDB();
		
		$res = & $db->getOne("SELECT member_login FROM members WHERE member_id='{$member_id}'");
		
		if (PEAR::isError($res)) {
			die($res->getMessage());
		}
		
		return $res;
		
		
	}
    
    
    function _emailToUsername($email) {
        
        _filter_var($email);
	
	
		$cphplib = new cphplib();
		
		if(!$cphplib->checkEmail($email)) { // email is not valid
			return array(false,"Invalid email address");
		}
        
        /**
		 * check if the EMAIL is unique or not 
		 */
		 
        $db = AccessDB();
		$username = $db->getOne("SELECT member_login FROM members WHERE email='{$email}'");
		
		if (PEAR::isError($username)) {
			//die($username->getMessage());
            return array(false,"Internal error occured");
		}
		
		if(empty($username)) {
			return array(false,"This email is not registered");
		}
        
        return array(true,$username);
        
        
    }
    
    
    function _emailExists($email) {
    	$db = AccessDB();
		$email = $db->getOne("SELECT COUNT(email) FROM members WHERE email=?",array($email));
		if (!PEAR::isError($email)&&$email==1) {
			return true;
		}
		else {
			return false;
		}
    }
    
    
    
    function _usernameToEmail($username) {
        
        _filter_var($username);
	
	 
        $db = AccessDB();
		$email = $db->getOne("SELECT email FROM members WHERE member_login='{$username}'");
		
		if (PEAR::isError($email)) {
			//die($username->getMessage());
            return array(false,"");
		}
		
		if(empty($username)) {
			return array(false,"");
		}
        else {
            return array(true,$email);
        }
        
        
    }
    
    
	

	
	function isGroupMember() {
	
		global $valid_user;

		if(isAuthenticated()) {
			$member_id = _getMemberID($valid_user);
		}
		else {
			return false;
		}
		
		$db = AccessDB();
		
		$res = & $db->getOne("SELECT membership_id FROM memberships WHERE member_id='{$member_id}'");
		
		if (PEAR::isError($res)) {
			die($res->getMessage());
		}
		
		if(empty($res)) {
                return false;
		}
		else {
			return true;
		}
	
	}
	
	
	function isGroupAdmin() {
	
		global $valid_user;
	
		if(isAuthenticated()) { 
			$member_id = _getMemberID($valid_user);
		}
		else {
			return false;
		}
		
		
		$db = AccessDB();

		$res = & $db->getOne("SELECT admin_id FROM admins WHERE member_id='{$member_id}'");
		
		if (PEAR::isError($res)) {
			die($res->getMessage());
		}
		
		if(empty($res)) {
                return false;
		}
		else {
			return true;
		}
	
	}
	
	
	/**
	 * shows the sign up screen
	 * @param $accessing the group that the member was trying to access
	 * @param $new was the user trying to create a new group
	 * @returns array, 0=>true/false; 1=>false, reason
	 */
	function signup($username,$password1,$email,$mail_welcome=true,$primary_group=-1,$openid=false,$remember_me=false,$not_this_user=false) {
	
		global $valid_user;
		
		if(empty($username)) {
			$username = _get_rand_letters(17);
		}
		
		if(empty($password1)) {
			$password1 = _get_rand_letters(6);
		}

		
		$cphplib = new cphplib();
		
		if(!$cphplib->checkEmail($email)) { // email is not valid
			return array(false,"Invalid email address");
		}
		
		//if(endsWith($email,"@jetable.org")||endsWith($email,"@xoxy.net")||endsWith($email,"@grou.ps")) {
		if(undorg_isDisposableEmail($email)||endsWith($email,"@grou.ps")) {
			return array(false,"Invalid email host");
		}
		
		if(!$openid&&(!$cphplib->checkUserName($username,3)||strlen($username)>20)) { 
			return array(false,"Invalid username");
		}
		
		if(!$openid&&!$cphplib->checkPassword($password1,5)) { // password not valid
			return array(false,"Password too short, or contains invalid characters");
		}
		
		/**
		 * check if the username is unique or not 
		 */
		 
		 $db = AccessDB();
		 
		$mid = $db->getOne("SELECT COUNT(member_id) FROM members WHERE member_login=?",array($username));
		
		if (PEAR::isError($mid)) {
			die($mid->getMessage());
		}
        
        $mid = intval($mid);
		
		if($mid!=0) {
			return array(false,"Existing username");
		}
		
		
		/**
		 * check if the EMAIL is unique or not 
		 */
		$mid = $db->getOne("SELECT member_id FROM members WHERE email=?",array($email));
		
		if (PEAR::isError($mid)) {
			die($mid->getMessage());
		}
		
		if(!empty($mid)) {
			return array(false,"Existing email");
		}
		
		
		/**
		 * Checks are complete!
		 * If we're here; then the request was clean!
		 * Sign him/her up!
		 */
		
		if(!$openid) {
			$password_encrypted = md5($password1);
			$now_date = date('Y-m-d');
			$res = $db->query("INSERT INTO members VALUES (NULL,?,?,?,?,'no')",array($username,$password_encrypted,$email,$now_date));
		}
		else {
			$now_date = date('Y-m-d');
			$res = $db->query("INSERT INTO members VALUES (NULL,?,'',?,?,'yes')",array($username,$email,$now_date));
		}
		
		if (PEAR::isError($res)) {
			die($res->getMessage());
		}
		
		if(!$res) {
			die('2Error');
		}
        
        
        
        $new_users_id = _getMemberID($username);
        $sql2 = "INSERT INTO `memberships` ( `membership_id` `member_id` , `subscribed_on` , `member_name` , `website` , `blog` , `flickr` , `delicious` )";
        $sql2 .= "VALUES (";
        $sql2 .= "NULL , ?, NOW() , ?, '', '', '', ''";
        $sql2 .= ");";
        $res2 = $db->query($sql2,array($new_users_id,$username));
		
		if (PEAR::isError($res2)) {
			die($res2->getMessage());
		}
		
		if(!$res2) {
			die('3Error');
		}
		
		if(!$not_this_user&&$mail_welcome) {
            $valid_user = $username;
            session_register('valid_user');
            //$SESSION['valid_user'] = $valid_user;
		
            /**
             * TODO:
             * mail stuff should be added!
             */
            $mailr = new Mailer($valid_user,'',true);
            
            if(!$openid)
            	$res = $mailr->welcomeNewMember($email,$password1);
            else 
            	$res = $mailr->welcomeNewOpenIDMember($username);
        }
		
        
        // check for previous login recovery
        // requests
        if(!$openid)
        	setLoginRecovered($email);
        	
        if(!$not_this_user)	 {
        if($remember_me) {
        	rememberUser($username,$password_encrypted);
        }
        else {
        	dontRememberUser($username);
        }
        }
        
		return array(true,$password1); // return true
		
		
	}
	
	
    /****
     * we drop it off
     * that's why we put "____" at the beginning of function name
     */
	function ____recoverLoginInfo($email) {
	
	
		/*$vars = array($email);
		$res = _filter_vars($vars);
		if(!$res) {
			die('Error No: 200');
		}
		else {
			$email = $vars[0];
		}*/
        
        _filter_var($email);
	
	
		$cphplib = new cphplib();
		
		if(!$cphplib->checkEmail($email)) { // email is not valid
			return false;
		}
		
		/**
		 * check if the EMAIL is unique or not 
		 */
		 
        $db = AccessDB();
		$username = $db->getOne("SELECT member_login FROM members WHERE email='{$email}'");
		
		if (PEAR::isError($username)) {
			//die($username->getMessage());
            return false;
		}
		
		if(empty($username)) {
			return false;
		}
		else {
			$newpassword = _get_rand_letters(10);
            
            
            $q = $db->query("UPDATE members SET member_password=MD5('{$newpassword}') WHERE email='{$email}'");
		
            if (PEAR::isError($q)) {
                //die($username->getMessage());
                return false;
            }
            
            if($q) {
         
                $mailr = new Mailer($username,'',true);
            
                $res = $mailr->notifyLoginRecovery($newpassword);
            
			
                return $res;
            }
            else {
                return false;
            }
        }
		
	
	
	
	}
    
    
    
    /**
     * Change from the previous recoverLoginInfo function is:
     * we send an email before resetting the email
     * if (s)he clicks it, then reset occurs
     *
     * TODO: transavtionality
     */
    function recoverLoginInfo($email) {
        
        
        _filter_var($email);
        
        if(isOpenIDAccount($email)) {
        	return "ERROR: This is an OpenID account. We don't keep your password. You need to recover it from your OpenID provider.";
        }
        
	
        $username = "";
		$usernameres = _emailToUsername($email);
        
        if(!$usernameres[0]) { // false
            
            return "ERROR: ".$usernameres[1];
            
        }
        else {
            
            $username = $usernameres[1];
            
        }
        
        
        $ser = _generateRecoverySerial();
        $sql = 'INSERT INTO `password_recovery` (`recovery_id`, `email`, `serial`, `date`, `recovered`, `recovery_date`) VALUES (NULL, \''.$email.'\', \''.$ser.'\', NOW(), \'N\', \'0000-00-00\');';
        
        $db = AccessDB();
        $q = $db->query($sql);
		
        
		if (PEAR::isError($q)) {
			//die($q->getMessage());
            return "ERROR: An internal error occured";
		}
        
        $mailr = new Mailer($username,'',true);
            
        
        $res = $mailr->notifyLoginRecovery($ser);
        
        if(!$res) {
            return "ERROR: Could not send you the recovery email, try again or <a href=\"mailto:contact@grou.ps\">contact us</a> if the problem persists";
        }
        else {
            return "TRUE"; // means true
        }
        
    }
    
    function resetNewPassword($email,$new_pass) {
        
        _filter_var($email);
        _filter_var($new_pass);
        
        // don't even go
        if(isOpenIDAccount($email)) {
        	return true;
        }
	
	
		$cphplib = new cphplib();
		
		if(!$cphplib->checkEmail($email)) { // email is not valid
			return false;
		}
        
        if(!$cphplib->checkPassword($new_pass,5)) { // password not valid
			return false;
		}
		
		/**
		 * check if the EMAIL is unique or not 
		 */
		 
        $db = AccessDB();
		$username = _emailToUsername($email);
		
		if(empty($username)) {
			return false;
		}
		else {
            
            $q = $db->query("UPDATE members SET member_password=MD5('{$new_pass}') WHERE email='{$email}'");
		
            if (PEAR::isError($q)) {
                //die($username->getMessage());
                return false;
            }
            
            return $q;
        }
		
        
        
    }
    
    
    
    function setLoginRecovered($email) {
        
        _filter_var($email);
 
        
        $sql = 'UPDATE `password_recovery` SET `recovered`=\'Y\', `recovery_date`=NOW() WHERE email=\''.$email.'\' AND `recovered`=\'N\';';
        
        $db = AccessDB();
        $q = $db->query($sql);
		
        
		if (PEAR::isError($q)) {
			//die($q->getMessage());
            return false;
		}
        
        return $q;
        
    }
    
    
    function _generateRecoverySerial() {
     
        $ser = "";
        
        $np_charnum = rand(4,6);
        for($i=0;$i<$np_charnum;$i++) {
            $ser .= rand(10,99);
        }
        
        return $ser;
        
    }
    
    function isLoginRecoverable($email,$serial) {
     
        _filter_var($email);
        _filter_var($serial);
        
        $db = AccessDB();
        
        $time_3daysago = time() - (60*60*24*3);
        
        $passcheck = $db->getOne("SELECT COUNT(recovery_id) FROM password_recovery WHERE email='{$email}' AND serial='{$serial}' AND recovered='N' AND date >= '{$time_3daysago}'");
		
		if (PEAR::isError($username)) {
			//die($username->getMessage());
            return false;
		}
		
		if($passcheck!=0) {
			return true;
		}
        else {
            return false;
        }
        
        
    }
    
    
    
	
	
	
	/**
	 * what we call by profile is nothing but memberships
	 */
	function getProfiles() {
	
		if(isAuthenticated()) { 
			$member_id = _getMemberID($_SESSION['valid_user']);
		}
		else {
			return array(false,array());
		}
		
		$db = AccessDB();
			
		$q = $db->getAll("SELECT membership_id, member_name FROM memberships WHERE member_id='{$member_id}' ORDER BY subscribed_on ASC", array(), 2 /** assoc */);
	
		if (PEAR::isError($q)) {
			die($q->getMessage());
		}
		
		
		if(sizeof($q)==0){
			return array(false,array());
		}
		else {
		
			return array(true,$q);
		
		
		}
	}
    
    
    
    /**
     * gets the first profile of the user
     * this function is especially used when creating new groups
     * we assign automatically the first profile
     * then the "admin" can change ihis/her profile
     * this won't be a problem as he is the first one in the group
     * noone will see him/her with this profile
     * 
     * We assume that user has signed up; otherwise we return an error
     * @returns int the id of profile
     */
    function _getPrimaryProfile($uname=null) {
        
        if($uname==null&&!isset($_SESSION['valid_user'])) {
            
            die("Error No 30");
            
        }
        else {
        
        	if($uname==null)
            	$member_id = _getMemberID($_SESSION['valid_user']);
            else 
            	$member_id = _getMemberID($uname);
            
            $db = AccessDB();
			
            $q = $db->getOne("SELECT membership_id FROM memberships WHERE member_id='{$member_id}' ORDER BY membership_id DESC LIMIT 1");
	
            if (PEAR::isError($q)) {
                die($q->getMessage());
            }
            
            return $q;
        
        }
    }
    
    
    /*
     * there is a similar function in GeneralFunctions.php also
     * so drop this
     * TODO: review function placements
     */
    function _getMembershipID($gname) {
        
        if(!isAuthenticated()||!isset($_SESSION['valid_user'])) {
            
            die("Error No 3034");
            
        }
        else {
        
            $member_id = _getMemberID($_SESSION['valid_user']);
            
            $db = AccessDB();
            
            $q = $db->getOne("SELECT membership_id FROM memberships WHERE member_id='{$member_id}' LIMIT 0 , 1");
            
	
            if (PEAR::isError($q)) {
                die($q->getMessage());
            }
            
            return $q;
        
        }
        
    }
    

    
    
    function processOpenIDAccount($openid, $email) {
    	
    	if(isOpenIDAccount($email)) {
    		loginOpenID($openid);
    	}
    	else {
    		$x = signupOpenID($openid,$email);
    		if(!$x[0])
    			die($x[1]);
    	}
    	
    }
    
    
    function isOpenIDAccount($email=null) {
    	
    	if($email==null) {
    		if(!isAuthenticated()||!isset($_SESSION['valid_user']))
    			die("Error No 30342");
    		$db = AccessDB();
    		$login = mysql_real_escape_string($_SESSION['valid_user']);
    		$sql = "SELECT openid FROM members WHERE member_login='{$login}';";
    	}
    	else {
    		$db = AccessDB();
    		$email = mysql_real_escape_string($email);
    		$sql = "SELECT openid FROM members WHERE email='{$email}';";
    	}
    	$res = $db->getOne($sql);
    	return $res=='yes'?true:false;
  
    }
    
    function loginOpenID($id) {
    	login($id,null,false,true);
    }
    
    function signupOpenID($id,$email) {
    	return signup($id,null,$email,true,-1,true);
    }

    $field_membershipIDToMemberID = array();
   	function membershipIDToMemberID($db,$membership_id) {
   		global $field_membershipIDToMemberID;
    	if(!isset($field_membershipIDToMemberID[$membership_id])) {
	   		$membership_id = mysql_real_escape_string($membership_id);
	    	$sql = "SELECT member_id FROM memberships WHERE membership_id = '{$membership_id}'";
	    	$res = $db->getOne($sql);
	    	$field_membershipIDToMemberID[$membership_id] = $res;
	    	return $res;
    	}
    	else {
    		return $field_membershipIDToMemberID[$membership_id];
    	}
    }
    

function _get_rand_letters($length)
{
  if($length>0) 
  { 
  $rand_id="";
   for($i=1; $i<=$length; $i++)
   {
   mt_srand((double)microtime() * 1000000);
   $num = mt_rand(1,26);
   $rand_id .= __assign_rand_value($num);
   }
  }
return $rand_id;
}

function __assign_rand_value($num)
{
// accepts 1 - 36
  switch($num)
  {
    case "1":
     $rand_value = "a";
    break;
    case "2":
     $rand_value = "b";
    break;
    case "3":
     $rand_value = "c";
    break;
    case "4":
     $rand_value = "d";
    break;
    case "5":
     $rand_value = "e";
    break;
    case "6":
     $rand_value = "f";
    break;
    case "7":
     $rand_value = "g";
    break;
    case "8":
     $rand_value = "h";
    break;
    case "9":
     $rand_value = "i";
    break;
    case "10":
     $rand_value = "j";
    break;
    case "11":
     $rand_value = "k";
    break;
    case "12":
     $rand_value = "l";
    break;
    case "13":
     $rand_value = "m";
    break;
    case "14":
     $rand_value = "n";
    break;
    case "15":
     $rand_value = "o";
    break;
    case "16":
     $rand_value = "p";
    break;
    case "17":
     $rand_value = "q";
    break;
    case "18":
     $rand_value = "r";
    break;
    case "19":
     $rand_value = "s";
    break;
    case "20":
     $rand_value = "t";
    break;
    case "21":
     $rand_value = "u";
    break;
    case "22":
     $rand_value = "v";
    break;
    case "23":
     $rand_value = "w";
    break;
    case "24":
     $rand_value = "x";
    break;
    case "25":
     $rand_value = "y";
    break;
    case "26":
     $rand_value = "z";
    break;
    case "27":
     $rand_value = "0";
    break;
    case "28":
     $rand_value = "1";
    break;
    case "29":
     $rand_value = "2";
    break;
    case "30":
     $rand_value = "3";
    break;
    case "31":
     $rand_value = "4";
    break;
    case "32":
     $rand_value = "5";
    break;
    case "33":
     $rand_value = "6";
    break;
    case "34":
     $rand_value = "7";
    break;
    case "35":
     $rand_value = "8";
    break;
    case "36":
     $rand_value = "9";
    break;
  }
return $rand_value;
}

?>
