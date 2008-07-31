<?php

require("phpmailer/class.phpmailer.php");
require_once 'User.class.php';


class Mailer extends PHPMailer {
	
	
	var $Database = null;
	
	var $BodyOrig = ""; // for $Body
    
    // Set default variables for all new objects
    // override phpMailer vars
    var $From     = "dont-reply@grou.ps";
    var $FromName = "GROU.PS";
    var $Mailer   = "mail"; 
    var $CharSet  = "UTF-8";
    var $WordWrap = 75;
    
    // var $Mailer   = "sendmail";
    // var $Sendmail = "/usr/sbin/sendmail";

    
    // and now set global vars
    // specific to this class
    // the previous ones were overriding
    // phpMailer variables    
    var $to_name = "member";
    var $to_email = null;
    var $groupname = null;
    var $username = null;
    
    // for internal use in AddAddress
    var $__to = array();
    
    // constants
    var $thanks = "\r\n\r\n<p>Thanks,</p>";
    
    var $HTMLMailHeader = "";
    var $HTMLMailHeaderTemplate = "templates/mail/header.tpl";
    
    var $HTMLMailFooter = "";
    var $HTMLMailFooterTemplate = "templates/mail/footer.tpl";
    
    var $GROUPSSignature = "[[****groups_signature***]]";
    var $SignatureHTML = "<img src=\"http://maillog.grou.ps/blank.gif?e=[[[1]]]&r1=[[[2]]]&r2=[[[3]]]\" alt=\"\" width=\"1\" height=\"1\" border=\"0\" />";
    
    // var $SpreadTheWordFooter = "\r\n\r\n<br />\r\n\r\n<hr>\r\n\r\n<p>Start your own grou.p - <a href=\"http://grou.ps/create_group.do\">http://grou.ps/create_group.do</a></p>\r\n\r\n<p>Need help? Try our free Fanatical Support: <a href=\"fanaticalsupport@grou.ps\">fanaticalsupport@grou.ps</a> - If you like our services, please help us to spread the word: blog about us and/or invite your friends to join.</p>";
    var $SpreadTheWordFooter = "";
    
    // TranslationEngine object
    var $TrEng = null;
    
    
    /**
     * constructor class
     * we always need a username
     * otherwise how can we know who send the message
     * @param username
     * @param groupname
     * @param not_in_group user not in group yet; 
     */
    function Mailer($username,$groupname,$not_in_group=false) {
    	
		global $GlobalDatabase;
		global $treng;
    		
        _filter_var($username);
        _filter_var($groupname);
        
        $this->username = $username;
        $this->groupname = $groupname;
        
        if(isset($treng)&&is_a($treng,"TranslationEngine"))
        	$this->TrEng = $treng;
        
		if(class_exists("Group")) {
	        $group = new Group($groupname);
	        $grouptitle = $group->getTitle();
	        $this->FromName = $this->_quoted_printable_encode($grouptitle);
	        //$this->FromName = $grouptitle;
        }
        
        $user = new User($username);
        
        if(!$not_in_group)
            $this->to_name = $user->getNameSurname($groupname);
        else
            $this->to_name = $user->getEmail();
        
        
        $this->to_email = $user->getEmail();
        
        //$this->IsSMTP(); // telling the class to use SMTP
        //$this->Host = "mail.grou.ps"; // SMTP server
        
        
        // HTML mail related changes!
        
        // no need to embed images; we use http://
        //$this->AddEmbeddedImage("images/logo.gif", "logo", "images/logo.gif");
        //$this->AddEmbeddedImage("images/emailtop.gif", "emailtop", "images/emailtop.gif");
        
        $this->isHTML(true);
        $this->HTMLMailHeader = file_get_contents($this->HTMLMailHeaderTemplate);
        $this->HTMLMailFooter = file_get_contents($this->HTMLMailFooterTemplate);
        
        
        if(!isset($GlobalDatabase)) {
	        
	        // now the database connections
	        include('configs/globals.php');
			
			
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
			
			$this->Database = & DB::connect($dsn, $options);
			
			if (PEAR::isError($this->Database)) {
				die("Error No 523540: ".$this->Database->getMessage());
			}
	        
	        $q = & $this->Database->query("SET NAMES utf8;");
	  
	        if (PEAR::isError($q)) {
	            die("Error No 54353: ".$q->getMessage());
	        }
        }
        else {
        	$this->Database = $GlobalDatabase;
        }
        
        
        
    }
    
    
    function _($str) {
    	if($this->TrEng!=null) {
    		return $this->TrEng->_($str,"mail");
    	}
    	else {
    		return $str;
    	}
    }
    
    // Replace the default error_handler
    function error_handler($msg) {
        
        // no logging capabilities yet
        // TODO: add debugging and logging features
        return false;
    
    }
    
    //L: note $encoding that is uppercase
//L: also your PHP installation must have ctype_alpha, otherwise write it yourself
function _quoted_printable_encode($string, $encoding='UTF-8') {
	return $string;
// use this function with headers, not with the email body as it misses word wrapping
       $len = strlen($string);
       $result = '';
       $enc = false;
       for($i=0;$i<$len;++$i) {
        $c = $string[$i];
        if (ctype_alpha($c))
            $result.=$c;
        else if ($c==' ') {
            $result.='_';
            $enc = true;
        } else {
            $result.=sprintf("=%02X", ord($c));
            $enc = true;
        }
       }
       //L: so spam agents won't mark your email with QP_EXCESS
       if (!$enc) return $string;
       return '=?'.$encoding.'?q?'.$result.'?=';
}


    
    
    // override Send()
    function Send($clean = false) {
        
        $cBody = stripslashes($this->Body);
        
        if(!$clean)
        	$cBody .= $this->SpreadTheWordFooter;
        
        $this->Body = "";
        $this->Body .= $this->HTMLMailHeader;
        $this->Body .= $cBody;
        $this->Body .= $this->GROUPSSignature;
        $this->Body .= $this->HTMLMailFooter;
        
        $this->BodyOrig = $this->Body;
        
        // sign feature should go here!
        
        $this->AltBody = $this->_MakeAltBody($cBody);
        
        $res = true;
        foreach($this->__to as $__to_u) {
        	//if($__to_u[2]=='bcc')
        	//	parent::AddBCC($__to_u[0],$__to_u[1]);
        	//else
        		parent::AddAddress($__to_u[0],$__to_u[1]);
        	$this->signStatsMsg($__to_u[0]);
        	$res |= parent::Send();
        	parent::ClearAddresses();
        }
        
        return $res;
        
    }
    
    
    function signStatsMsg($email) {
    	
    	_filter_var($email);
    	
    	$rand_1 = rand(0,30000);
    	$rand_2 = rand(0,30000);
    	$sql = 'INSERT INTO `maillogs` (`log_id`, `subject`, `to_addr`, `sent_date`, `random_1`, `random_2`) VALUES (NULL, \''.$this->Subject.'\', \''.$email.'\', NOW(), \''.$rand_1.'\', \''.$rand_2.'\');';
    	$q = &$this->Database->query($sql);
    	if (PEAR::isError($q)) {
    		//no need to die
            // die("Error No 54353: ".$q->getMessage());
            $this->Body = str_replace($this->GROUPSSignature,"<!--error in db entry-->",$this->BodyOrig);
        }
        else {
        	if($q) {
        		// embed the image
        		$imghtml = $this->getSignatureHTML($email,$rand_1,$rand_2);
        		$this->Body = str_replace($this->GROUPSSignature,$imghtml,$this->BodyOrig);
        		
        	}
        	else {
        		$this->Body = str_replace($this->GROUPSSignature,"<!--error in db entry-->",$this->BodyOrig);
        	}
        
        }
        
    	
    }
    
    
    function getSignatureHTML($email,$rand_1,$rand_2) {
    	
    	$tmp = $this->SignatureHTML;
    	
    	$tmp = str_replace("[[[1]]]",$email,$tmp);
    	$tmp = str_replace("[[[2]]]",$rand_1,$tmp);
    	$tmp = str_replace("[[[3]]]",$rand_2,$tmp);
    	
    	return $tmp;
    	
    }
    
    
        /**
     * Adds a "To" address.  
     * @param string $address
     * @param string $name
     * @return void
     */
    function AddAddress($address, $name = "") {
        $cur = count($this->__to);
        $this->__to[$cur][0] = trim($address);
        $this->__to[$cur][1] = $name;
        $this->__to[$cur][2] = 'to';
    }
    
    /*
    function AddBCC($address, $name = "") {
        $cur = count($this->__to);
        $this->__to[$cur][0] = trim($address);
        $this->__to[$cur][1] = $name;
        $this->__to[$cur][2] = 'bcc';
    }
    */
    
    
    
    /**
     * Clears all recipients assigned in the TO array.  Returns void.
     * @return void
     */
    function ClearAddresses() {
        $this->__to = array();
    }
    
    
    function _MakeAltBody($body) {
    	
    	$body = str_replace("<hr>", "=============================",$body);
    	$body = strip_tags($body);
    	
    	return $body;
    	
    }
    
    function _nl2br($txt) {
    	$txt = str_replace("\r\n","<br />",$txt);
    	$txt = str_replace("\r","<br />",$txt);
    	$txt = str_replace("\n","<br />",$txt);
    	$txt = str_replace("<br />","<br />\r\n",$txt);
    	return $txt;
    }
    
    function notifyAddEvent($mails, $desc,$starts,$finishes) {
        
        
        $msg .= "<p>{$this->to_name} has added a new event, details are below:</p>";
        $msg .= "\r\n\r\n";
        $msg .= "<p>".nl2br($desc)." <br />";
        $msg .= "<p>Starts: {$starts}<br />";
        $msg .= "<p>Finishes: {$finishes}";
        $msg .= "</p>";
        $msg .= "\r\n\r\n";
        $msg .= "<p>Discuss it on <a href=\"http://grou.ps/{$this->groupname}/talks\">http://grou.ps/{$this->groupname}/talks</a></p>";
        $msg .= $this->thanks;
        
        foreach ($mails as $m) {
        	$this->addBCC($m);
        }
        
        
        $this->Body = $msg;
        
        $this->Subject = "New Event";
        
        //$this->From = $this->to_email;
        //$this->FromName = $this->to_name;
        
        $this->ClearAddresses();
        //$this->AddAddress($this->From);
        $this->AddAddress("undisclosed-recipients");
        
        // TODO: Add logging features
        if(!$this->Send()) {
            $this->ClearAddresses();
            return false; // an error has occurred
        }
        else {
            $this->ClearAddresses();
            return true;
        }    	
    }
    

    // Create an additional function
    function notifyGroupCreation($group_title) {
        
        // no need to filter $group_title
        
        $msg = "";
        $msg .= "<p>{$this->to_email},</p>";
        $msg .= "\r\n\r\n";
        $msg .= "<p>Congratulations, you have successfully created a group: <u>{$group_title}</u>.</p>";
        $msg .= "\r\n\r\n";
        $msg .= "<p>You can access it from <a href=\"http://grou.ps/{$this->groupname}\">http://grou.ps/{$this->groupname}</a></p>";
        $msg .= "\r\n\r\n";
        $msg .= "<p>Check out the short manual in the attachments. Managing your group is dead easy. Your admin panel is on <a href=\"http://grou.ps/{$this->groupname}/admin\">http://grou.ps/{$this->groupname}/admin</a></p>";
        $msg .= "\r\n\r\n";
        $msg .= "<p>A group is fun only with its members. Start inviting your friends, posting free ads on Craigslist...</p>";
        $msg .= $this->thanks;
        
        // no : {$group_title} in subject
        // because titles don't accept utf8 encoding
        // at least phpmailer don't support it!
        $this->Subject = "Group Created";
        $this->Body = $msg;
        $this->AddAddress($this->to_email, $this->to_name);
        
        $this->AddAttachment("/var/www/html/groups/html/members/founders_manual.pdf");
        
        // TODO: Add logging features
        if(!$this->Send()) {
            $this->ClearAddresses();
            return false; // an error has occurred
        }
        else {
            $this->ClearAddresses();
            return true;
        }
        
        
    }
    
    
    
    // Create an additional function
    function notifyAcceptedMembership($group_title) {
        
        // no need to filter $group_title
        
        $msg = "";
        $msg .= "<p>{$this->to_email},</p>";
        $msg .= "\r\n\r\n";
        $msg .= "<p>Congratulations, you have successfully joined <u>{$group_title}</u>.</p>";
        $msg .= "\r\n\r\n";
        $msg .= "<p>And now it's time to complete your profile: <a href=\"http://grou.ps/{$this->groupname}/join\">http://grou.ps/{$this->groupname}/join</a></p>";
        $msg .= "\r\n\r\n";
        $msg .= "<p>Your group address is: <a href=\"http://grou.ps/{$this->groupname}\">http://grou.ps/{$this->groupname}</a></p>";
        $msg .= $this->thanks;
        
        
        // no : {$group_title} in subject
        // because titles don't accept utf8 encoding
        // at least phpmailer don't support it!
        $this->Subject = "Membership Approved";
        $this->Body = $msg;
        $this->AddAddress($this->to_email, $this->to_name);
        
        // TODO: Add logging features
        if(!$this->Send()) {
            $this->ClearAddresses();
            return false; // an error has occurred
        }
        else {
            $this->ClearAddresses();
            return true;
        }
        
    }
    
    
    
    
    /**
     * We may want to notify admins also in the future
     * Or add the email of the admins because we recommend them to
     * contact the admins
     */
    function notifyRejectedMembership($group_title,$admin_mail='') {
        
        // no need to filter $group_title
        
        $msg = "";
        $msg .= "<p>{$this->to_email}</p>";
        $msg .= "\r\n\r\n";
        $msg .= "<p>The administrators of <u>{$group_title}</u> have rejected your membership request.";
        $msg .= " To contact them, simply reply this message.</p>";
        $msg .= $this->thanks;
        
        
        // no : {$group_title} in subject
        // because titles don't accept utf8 encoding
        // at least phpmailer don't support it!
        $this->Subject = "Membership Rejected";
        $this->Body = $msg;
        $this->AddAddress($this->to_email, $this->to_name);
        
        $this->AddReplyTo($admin_mail);
        
        // TODO: Add logging features
        if(!$this->Send()) {
            $this->ClearAddresses();
            return false; // an error has occurred
        }
        else {
            
            $this->ClearAddresses();
            return true;
        }
        
    }
    
    function youGotAWallMessage() {
        $msg = "";
        $msg .= "<p>{$this->to_email}</p>";
        $msg .= "\r\n\r\n";
        $msg .= "<p>Someone wrote on your wall. Check your wall from: <a href=\"http://grou.ps/{$this->groupname}/people/{$this->username}\">http://grou.ps/{$this->groupname}/people/{$this->username}</a>";
        $msg .= $this->thanks;
        
        
        // no : {$group_title} in subject
        // because titles don't accept utf8 encoding
        // at least phpmailer don't support it!
        $this->Subject = "Someone wrote on your wall";
        $this->Body = $msg;
        $this->AddAddress($this->to_email, $this->to_name);
        
        $this->AddReplyTo($admin_mail);
        
        // TODO: Add logging features
        if(!$this->Send()) {
            $this->ClearAddresses();
            return false; // an error has occurred
        }
        else {
            
            $this->ClearAddresses();
            return true;
        }
    }
    
    
    /**
     * We may want to notify admins also in the future
     * Or add the email of the admins because we recommend them to
     * contact the admins
     */
    function notifyBannedMember($group_title,$replytox='',$reasonx='') {
        
        
        _filter_var($group_title);
        _filter_var($replytox);
        _filter_var($reasonx);
        
        // no need to filter $group_title
        if(!isset($group_title)&&class_exists("Group")) {
        	$g = new Group($this->groupname);
        	$group_title = $g->getTitle();
        }
        
        $msg = "";
        $msg .= "<p>{$this->to_email}</p>";
        $msg .= "\r\n\r\n";
        $msg .= "<p>Administrators of <u>{$group_title}</u> have banned you";
        
        
        if(!empty($reasonx)) {
            $msg .= " for the following reason:</p>";
            $msg .= "\r\n\r\n";
            $msg .= "<p><i>".nl2br(strip_tags($reasonx))."</i></p>";
            $msg .= "\r\n\r\n";
        }
        else {
            $msg .= ".</p>";
            $msg .= "\r\n\r\n";
        }
                
        $msg .= "<p>To contact them, simply reply this message.</p>";
        $msg .= $this->thanks;
        
        
        // no : {$group_title} in subject
        // because titles don't accept utf8 encoding
        // at least phpmailer don't support it!
        $this->Subject = "Banned!";
        $this->Body = $msg;
        $this->AddAddress($this->to_email);
        
        //$this->AddCustomHeader("Reply-To: {$replyto}");
        $this->AddReplyTo($replytox);
        
        // TODO: Add logging features
        if(!$this->Send()) {
            $this->ClearAddresses();
            return false; // an error has occurred
        }
        else {
            $this->ClearAddresses();
            return true;
        }
        
    }
    

    
    
    
    /**
     * We may want to notify admins also in the future
     * Or add the email of the admins because we recommend them to
     * contact the admins
     */
    function notifyAdminSet($group_title) {
        
        
        _filter_var($group_title);
        
        // no need to filter $group_title
        
        
        $msg = "";
        $msg .= "<p>{$this->to_email}</p>";
        $msg .= "\r\n\r\n";
        $msg .= "<p>You are now an administrator of <u>{$group_title}</u>.</p>";
        $msg .= "\r\n\r\n";
        $msg .= "<p>Your group address is: <a href=\"http://grou.ps/{$this->groupname}\">http://grou.ps/{$this->groupname}</a> and your admin panel is: <a href=\"http://grou.ps/{$this->groupname}/admin\">http://grou.ps/{$this->groupname}/admin</a></p>";
        $msg .= $this->thanks;
        
        
        // no : {$group_title} in subject
        // because titles don't accept utf8 encoding
        // at least phpmailer don't support it!
        $this->Subject = "You Are The New Admin";
        $this->Body = $msg;
        $this->AddAddress($this->to_email);
        
        
        // TODO: Add logging features
        if(!$this->Send()) {
            $this->ClearAddresses();
            return false; // an error has occurred
        }
        else {
            $this->ClearAddresses();
            return true;
        }
        
    }

    
    
    
    
    
    /**
     * This function differs from others;
     * it does not use default to_name and to_email
     * functions.
     * It first tries to find out the admins of the group
     * because the mail will be sent to them..
     */
    function notifyJoiningRequest($group_title) {
        
        /**
         * First let's notify the admins
         */
        
        
        // no need to filter $group_title
        
        $g = new Group($this->groupname);
        $admins = $g->getAdmins();
        
        $skipper = false;
        if(!$admins||sizeof($admins)==0)
            $skipper = true;
        
        if(!$skipper) {
	        foreach($admins as $admin) {
	            
	            $admins_user_id = $admin['member_id'];
	            $admin_username = _getMemberUsername($admins_user_id);
	            $admin_obj = new User($admin_username);
	        
	            $tmp_name = $admin_obj->getNameSurname($this->groupname);
	            $tmp_email = $admin_obj->getEmail();
	            
	            
	            $this->AddAddress($tmp_email, $tmp_name);
	            
	        }
	        
	        $msg = "";
	        $msg .= "<p>To the administrator of <u>{$group_title}</u>,</p>";
	        $msg .= "\r\n\r\n";
	        $msg .= "<p>There are new people who want to join your group. It's time to visit the administration panel: <a href=\"http://grou.ps/{$this->groupname}/admin/members\">http://grou.ps/{$this->groupname}/admin/members</a></p>";
	        $msg .= $this->thanks;
	        
	        $this->Subject = "Joining Requests";
	        $this->Body = $msg;
        }
        
        // TODO: Add logging features
        if(!$skipper&&!$this->Send()) {
            $this->ClearAddresses();
            return false; // an error has occurred
        }
        else {
            
            /**
             * Success, so it's time to 
             * notify the user 
             */ 
            $this->ClearAddresses();
            
            
            $nmsg = "";
            $nmsg .= "<p>{$this->to_email}</p>";
            $nmsg .= "\r\n\r\n";
            $nmsg .= "<p>We have processed your request to join <u>{$group_title}</u>. Now, you have to wait until the administrators of this group review and approve your application.</p>";
            $nmsg .= $this->thanks;
        
            $this->Subject = "Your Joining Request";
            $this->Body = $nmsg;
            $this->AddAddress($this->to_email, $this->to_name);
        
            // TODO: Add logging features
            if(!$this->Send()) {
                
                $this->ClearAddresses();
                return false;
            
            }
            else {
                
                $this->ClearAddresses();
                return true;
            }
        }
        
    }
    
    
    /**
     * This function differs from others;
     * it does not use default to_name and to_email
     * functions.
     * It first tries to find out the admins of the group
     * because the mail will be sent to them..
     */
    function notifyJoining($group_title) {
        
        /**
         * First let's notify the admins
         */
        
        $g = new Group($this->groupname);
        $admins = $g->getAdmins();
        
        $skipper = false;
        if(!$admins||sizeof($admins)==0)
            $skipper = true;
        
        if(!$skipper) {
	        foreach($admins as $admin) {
	            
	            $admins_user_id = $admin['member_id'];
	            $admin_username = _getMemberUsername($admins_user_id);
	            $admin_obj = new User($admin_username);
	        
	            $tmp_name = $admin_obj->getNameSurname($this->groupname);
	            $tmp_email = $admin_obj->getEmail();
	            
	            
	            $this->AddAddress($tmp_email, $tmp_name);
	            
	        }
	        
	        $msg = "";
	        $msg .= "<p>Dear administrator of {$group_title},</p>";
	        $msg .= "\r\n\r\n";
	        $msg .= "<p>A new user <u>{$this->to_email}</u> has joined your group - <a href=\"http://grou.ps/{$this->groupname}\">http://grou.ps/{$this->groupname}</a></p>";
	        $msg .= $this->thanks;
	        
	        $this->Subject = "New Member";
	        $this->Body = $msg;
        }
	        
        // TODO: Add logging features
        if(!$skipper&&!$this->Send()) {
            $this->ClearAddresses();
            return false; // an error has occurred
        }
        else {
            
            /**
             * Success, so it's time to 
             * notify the user 
             */ 
            $this->ClearAddresses();
            
         
        
        
        // no need to filter $group_title
            
            $nmsg = "";
            $nmsg .= "<p>{$this->to_email}</p>";
            $nmsg .= "\r\n\r\n";
            $nmsg .= "<p>You have sucessfuly joined <u>{$group_title}</u>.</p>";
            $nmsg .= $this->thanks;
        
            $this->Subject = "Your Joining Request APPROVED!";
            $this->Body = $nmsg;
            $this->AddAddress($this->to_email, $this->to_name);
        
            // TODO: Add logging features
            if(!$this->Send()) {
                
                $this->ClearAddresses();
                return false;
            
            }
            else {
                
                $this->ClearAddresses();
                return true;
            }
        }
    }
    
    
    
    
    /**
     * This function differs from others;
     * it does not use default to_name and to_email
     * functions.
     * It first tries to find out the admins of the group
     * because the mail will be sent to them..
     */
    function helloYWrappedUser($group_title,$inviter/*,$ygroup*/,$pass) {
        
        /**
         * First let's notify the admins
         */
        
        
        // no need to filter $group_title
        
  
            
            $nmsg = "";
            $nmsg .= "<p>{$this->to_email}</p>";
            $nmsg .= "\r\n\r\n";
        
        $nmsg .= "<p>{$inviter} invites you to join <u>{$group_title}</u> on <a href=\"http://grou.ps/intro.do\">GROU.PS</a></p>";
        $nmsg .= "<p>Your username: <font color=\"red\">{$this->username}</font></p>";
        $nmsg .= "<p>Group address: <a href=\"http://grou.ps/{$this->groupname}\">http://grou.ps/{$this->groupname}</a></p>";
        $nmsg .= "<p>Password: <a href=\"http://grou.ps/migrate.do?u={$this->username}&g={$this->groupname}&p={$pass}\">click to reset your password</a>";
        
        $nmsg .= $this->thanks;
        
            $this->Subject = "Your Group Wants You";
            $this->Body = $nmsg;
            $this->AddAddress($this->to_email, $this->to_name);
        
            // TODO: Add logging features
            if(!$this->Send()) {
                
                $this->ClearAddresses();
                return false;
            
            }
            else {
                
                $this->ClearAddresses();
                return true;
            }
        
    }
    
    
    
    
    
        function notifyGroupAdminYahooMigrationSuccess($ygroup,$mig_count) {
        
        // no need to filter $group_title
        
        $msg = "";
        $msg .= "<p>Dear {$this->to_name},</p>";
        $msg .= "\r\n\r\n";
        $msg .= "<p>Congratulations, <u>{$ygroup}</u> (your Yahoo! Group) has been successfully wrapped! <u>{$mig_count}</u> user(s) have been imported.</p>";
        $msg .= "\r\n\r\n";
        $msg .= "<p>Please note that, in order to synchronize GROU.PS with your Yahoo! Groups mailing list, you should make (not invite) <u>subscriber@grou.ps</u> a member of your Yahoo! Group (if you are not the moderator, you can ask the moderator to do it for you... It's very easy...)</p>";
        $msg .= $this->thanks;
        
        // no : {$group_title} in subject
        // because titles don't accept utf8 encoding
        // at least phpmailer don't support it!
        $this->Subject = "Yahoo! Groups Successfully Wrapped";
        $this->Body = $msg;
        $this->AddAddress($this->to_email, $this->to_name);
        
        // TODO: Add logging features
        if(!$this->Send()) {
            $this->ClearAddresses();
            return false; // an error has occurred
        }
        else {
            $this->ClearAddresses();
            return true;
        }
        
        
    }
    
    
    
    
    function welcomeNewMember($email,$password) {
        
        /**
         * First let's notify the admins
         */
        
        
        // no need to filter $group_title
        
  
            
        $nmsg = "";
        $nmsg .= "<p>{$this->to_email}</p>";
        $nmsg .= "\r\n\r\n";
        $nmsg .= "<p>Welcome to Grou.ps - thanks for signing up. Here are your credentials:</p>";
        $nmsg .= "\r\n\r\n";
        $nmsg .= "<p>Email: {$email}<br>\r\n";
        $nmsg .= "Password: {$password}</p>";
        //$nmsg .= "\r\n\r\n";
        //$nmsg .= "<p>If you have any questions, feature requests or bug reports, please contact our Fanatical Support team (<a href=\"mailto:fanaticalsupport@grou.ps\">fanaticalsupport@grou.ps</a>).</p>";
        $nmsg .= $this->thanks;
        
        $this->Subject = "Welcome, Your Credentials";
        $this->Body = $nmsg;
        $this->AddAddress($this->to_email);
        
        // TODO: Add logging features
        if(!$this->Send()) {
                
            $this->ClearAddresses();
            return false;
            
        }
        else {
                
            $this->ClearAddresses();
            return true;
        }
        
    }
    
    
    
    function welcomeNewOpenIDMember($openid) {

            
        $nmsg = "";
        $nmsg .= "<p>{$this->to_email}</p>";
        $nmsg .= "\r\n\r\n";
        $nmsg .= "<p>Welcome to Grou.ps! Here is your OpenID URL: {$openid}</p>";
        //$nmsg .= "\r\n\r\n";
        //$nmsg .= "<p>If you have any questions, feature requests or bug reports, please contact our Fanatical Support team (<a href=\"mailto:fanaticalsupport@grou.ps\">fanaticalsupport@grou.ps</a>).</p>";
        $nmsg .= $this->thanks;
        
        $this->Subject = "Welcome";
        $this->Body = $nmsg;
        $this->AddAddress($this->to_email);
        
        // TODO: Add logging features
        if(!$this->Send()) {
                
            $this->ClearAddresses();
            return false;
            
        }
        else {
                
            $this->ClearAddresses();
            return true;
        }
        
    }
    
    
    
    
    function invitePeople($group_title, $emails, $text) {
        
        _filter_var($group_title);
        _filter_var($text);
        
        $msg = "";
        $msg .= "<p>You are invited to: <u>{$group_title}</u>. ";
        $msg .= "Just visit: <a href=\"http://grou.ps/{$this->groupname}/join\">http://grou.ps/{$this->groupname}/join</a></p>";
        $msg .= "\r\n\r\n";
        
        if(!empty($text)) {
            $msg .= "<p>--</p>";
            $msg .= "\r\n\r\n";
            $msg .= nl2br($text);
        }
        
        $msg .= $this->thanks;
        
        // $this->FromName = $group_title; // enabled already
        
        $this->Subject = "Join Us!";
        $this->Body = $msg;
        
        foreach($emails as $email) {
            $this->AddBCC($email);
        }
        
        $this->ClearAddresses();
        $this->AddAddress("undisclosed-recipients");
        
        // TODO: Add logging features
        if(!$this->Send()) {
            $this->ClearAddresses();
            return false; // an error has occurred
        }
        else {
            $this->ClearAddresses();
            return true;
        }
        
        
    }
    
    function invitePerson($group_title, $email, $text, $password) {
        
        _filter_var($group_title);
        _filter_var($text);
        
        $msg = "";
        $msg .= "<p>Welcome to: <u>{$group_title}</u>. Here are your credentials:</p>";
        $msg .= "\r\n\r\n";
        $msg .= "<p>Login Page: <a href=\"http://grou.ps/{$this->groupname}/signin\">http://grou.ps/{$this->groupname}/signin</a></p>";
        $msg .= "\r\n\r\n";
        $msg .= "<p>Your Email: {$email}</p>";
        $msg .= "\r\n\r\n";
        $msg .= "<p>Your Password: {$password}</p>";
      

        $msg .= "\r\n\r\n";
        
        if(!empty($text)) {
            $msg .= "<p>And here's a message for you:</p>";
            $msg .= "\r\n\r\n";
            $msg .= nl2br($text);
            $msg .= "\r\n\r\n";
        }
        
        $msg .= "\r\n\r\n";
        $msg .= "<p>If this is an unwanted invitation, go to your control panel (<a href=\"http://grou.ps/{$this->groupname}/dashboard\">http://grou.ps/{$this->groupname}/dashboard</a>) to leave this group.</p>";
        
        $msg .= $this->thanks;
        
        // $this->FromName = $group_title; // enabled already
        
        $this->Subject = "Welcome!";
        $this->Body = $msg;
        
        
        $this->AddAddress($email);
        
        
        // TODO: Add logging features
        if(!$this->Send()) {
            $this->ClearAddresses();
            return false; // an error has occurred
        }
        else {
            $this->ClearAddresses();
            return true;
        }
        
        
    }
    
    
    function spread($emails, $text) {
        
        _filter_var($text);
        
        $msg = "";
        $msg .= "<p>You have a recommendation:</p>";
        $msg .= "\r\n\r\n";
        $msg .= nl2br($text);
        
        $this->Subject = "Check this out!";
        $this->Body = $msg;
        
        foreach($emails as $email) {
            $this->AddAddress($email);
        }
        
        // TODO: Add logging features
        if(!$this->Send()) {
            $this->ClearAddresses();
            return false; // an error has occurred
        }
        else {
            $this->ClearAddresses();
            return true;
        }
        
        
    }
    

    
    
    function spreadYouCanJoin($emails,$title,$fromname) {
        
    	_filter_var($title);
        _filter_var($fromname);
        
        $msg = "";
        
        $msg .= "<p>{$title}.. Does this make sense to you? If so, join it now... Your friend, {$fromname} has just";
        $msg .= "opened up this group and wants to see all related people inside.</p>";
        
        $msg .= "\r\n\r\n";

        $msg .= "<p>This new platform will allow you guys to stay in touch and share. Note that, this ";
        $msg .= "is not just a simple web site, it will connect you via your mobile phone and desktop too... ";
        $msg .= "Amazing, right? Why don't you go, give it a try and see it by yourself... Go, go, go...";
      
        
        $this->Subject = "Check this out!";
        $this->Body = $msg;
        
        foreach($emails as $email) {
            $this->AddAddress($email);
        }
        
        // TODO: Add logging features
        if(!$this->Send()) {
            $this->ClearAddresses();
            return false; // an error has occurred
        }
        else {
            $this->ClearAddresses();
            return true;
        }
        
        
    }
    
    
    
    function sendPrivateMessage($sender, $groupname, $msg, $reply=true, $serial='') {
        
        _filter_var($sender);
        _filter_var($groupname);
        
        $nmsg = "";
        $nmsg .= "<p>{$this->to_email}</p>";
        $nmsg .= "\r\n\r\n";
        $nmsg .= "<p>{$sender} (from <a href=\"http://grou.ps/{$groupname}\">http://grou.ps/{$groupname}</a>) sent you a private message:</p>";
        $nmsg .= "\r\n\r\n";
        $nmsg .= "<p><i>";
        $nmsg .= nl2br($msg);
        $nmsg .= "</i></p>";
        
        if($reply) {
            $nmsg .= "\r\n\r\n";
            $nmsg .= "<p>To reply, you can go to your member control center or, if available, use the form below:</p>";
            $nmsg .= "\r\n\r\n";
            $nmsg .= "<form method=\"post\" action=\"http://grou.ps/msgreply.do\">";
            $nmsg .= "<textarea name=\"msg\" cols=\"50\" rows=\"12\"></textarea>";
            $nmsg .= "<input type=\"hidden\" name=\"serial\" value=\"{$serial}\" />";
            $nmsg .= "<input type=\"hidden\" name=\"from\" value=\"{$this->username}\" />";
            
            if(!empty($groupname))
                $nmsg .= "<input type=\"hidden\" name=\"group\" value=\"{$groupname}\" />";
            else
                $nmsg .= "<input type=\"hidden\" name=\"group\" value=\"\" />";
            
            $nmsg .= "\r\n\r\n<br /><br />";
            $nmsg .= "<input type=\"submit\" value=\"Reply\" /></form>";
        }
        
        $this->Subject = "Private Message";
        $this->Body = $nmsg;
        $this->AddAddress($this->to_email);
        
        $this->FromName = $sender;
        
        // TODO: Add logging features
        if(!$this->Send()) {
                
            $this->ClearAddresses();
            return false;
            
        }
        else {
            
            $this->ClearAddresses();
            return true;
        }
        
        
    }
    
    
    
    function notifyMailGroup($mailgroup,$subj,$text,$mailid) {
        
        _filter_var($mailgroup);
        _filter_var($subj);
        _filter_var($text);
        _filter_var($mailid);
        
        $this->Subject = $subj;
        $this->Body = $text;
        
        $this->AddAddress($mailgroup);
        
        $this->From = $this->to_email;
        $this->FromName = $this->to_name;
        
        $this->AddCustomHeader("Message-ID: {$mailid}");
        
        // TODO: Add logging features
        if(!$this->Send()) {
            $this->ClearAddresses();
            return false; // an error has occurred
        }
        else {
            $this->ClearAddresses();
            return true;
        }
        
        
    }
    
    
    function postForSubGroupWall($mails,$msg,$msgid) {
        
    	$this->Subject = "";
        
        $text = strip_tags($msg);
        $text = str_replace("\r\n", "<br>", $text);
        $text = str_replace(array("\n", "\r"), "<br>", $text);
        $text = str_replace("<br><br>", "<br>", $text);
        $text = str_replace("<br>", "<br />\r\n", $text);
        
        $this->Body = $text;
        
        $this->AddAddress($mailgroup);
        
        $this->From = $this->to_email;
        $this->FromName = $this->to_name;
        
        foreach ($mails as $m) {
        	$this->addBCC($m);
        }
        
        $this->ClearAddresses();
        $this->AddAddress("undisclosed-recipients");
        
        // TODO: Add logging features
        if(!$this->Send()) {
            $this->ClearAddresses();
            return false; // an error has occurred
        }
        else {
            $this->ClearAddresses();
            return true;
        }
    }
    
    function mailCopyForumMessage($mails,$mid,$subj,$text) {
        
        
        $this->Subject = $this->_quoted_printable_encode("[{$this->FromName}] {$subj}");
        
        $text = str_replace(array("<br>","<br />"), "\r\n", $text);
        $text = strip_tags($text);
        $text = str_replace("\r\n", "<br>", $text);
        $text = str_replace(array("\n", "\r"), "<br>", $text);
        $text = str_replace("<br><br>", "<br>", $text);
        $text = str_replace("<br>", "<br />\r\n", $text);
        
        $this->Body .= "<p>{$text}</p>\r\n\r\n";
        $this->Body .= "<p>--</p>\r\n\r\n";
        $this->Body .= "<p>".$this->_("DO NOT REPLY TO THIS EMAIL")."</p>\r\n\r\n";
		$this->Body .= "<p>".$this->_("To post a comment of your own, read the original message, or to read all existing comments, visit:")." <a href=\"http://grou.ps/{$this->groupname}/talks/{$mid}\">http://grou.ps/{$this->groupname}/talks/{$mid}</a></p>";
        
        
        
        foreach ($mails as $m) {
        	$this->addBCC($m);
        }
        
        
        //$this->From = $this->to_email;
        $this->FromName = $this->to_name;
        
        $this->ClearAddresses();
        //$this->AddAddress($this->From);
        $this->AddAddress("undisclosed-recipients");
        
        
        // TODO: Add logging features
        if(!$this->Send()) {
            $this->ClearAddresses();
            return false; // an error has occurred
        }
        else {
            $this->ClearAddresses();
            return true;
        }
        
        
    }
    
    
    
    /*
     * This one was sending the new password
     * but now we just send a link to reset the password
    function notifyLoginRecovery($newpassword) {
        
        $msg = "";
        $msg .= "Dear GROU.PS member,";
        $msg .= "\r\n\r\n";
        $msg .= "You have requested to recover your login information:\r\n\r\n";
        $msg .= "Username: {$this->username} \r\n";
        $msg .= "New Password: {$newpassword} \r\n";
        $msg .= $this->thanks;
        
        // no : {$group_title} in subject
        // because titles don't accept utf8 encoding
        // at least phpmailer don't support it!
        $this->Subject = "Password Recovery";
        $this->Body = $msg;
        $this->AddAddress($this->to_email);
        
        // TODO: Add logging features
        if(!$this->Send()) {
            $this->ClearAddresses();
            return false; // an error has occurred
        }
        else {
            $this->ClearAddresses();
            return true;
        }
        
    }
    */
    
    function notifyLoginRecovery($ser) {
        
        $recovery_link = "http://grou.ps/login_recovery.do?email=".urlencode($this->to_email)."&serial=".urlencode($ser);
        
        $msg = "";
        $msg .= "<p>$this->to_email,</p>";
        $msg .= "\r\n\r\n";
        $msg .= "<p>".$this->_("If you want to reset your password, follow this link:")." <a href=\"{$recovery_link}\">{$recovery_link}</a></p>";
        $msg .= "\r\n\r\n";
        $msg .= "<p>".$this->_("Note that this link will expire in 3 days. If you don't want to reset, just skip this message.")."</p>";
        $msg .= $this->thanks;
        
        // no : {$group_title} in subject
        // because titles don't accept utf8 encoding
        // at least phpmailer don't support it!
        $this->Subject = $this->_("Password Recovery");
        $this->Body = $msg;
        $this->AddAddress($this->to_email);
        
        // TODO: Add logging features
        if(!$this->Send()) {
            $this->ClearAddresses();
            return false; // an error has occurred
        }
        else {
            $this->ClearAddresses();
            return true;
        }
        
    }
    
    
    function fanaticalSupport() {

    	return true; // canceled temporarily
    	
    	$this->FromName = $this->_("GROU.PS Founder");
    	$this->From = "fanaticalsupport@grou.ps";
    	$this->Subject = $this->_("Fanatical Support Promise");
    	
    	$msg = "";
        $msg .= "<p><img src=\"http://grou.ps/avatars/99721/80.png\" align=\"left\" hspace=\"10\" vspace=\"5\" />Dear group owner,</p>";
        $msg .= "\r\n\r\n";
        $msg .= "<p>I am Emre Sokullu, the founder of Grou.ps - I sincerely thank you for your trust and starting a new group with us.</p>";
        $msg .= "\r\n\r\n";
        $msg .= "<p>We know that setting up and promoting a group is a tedious task but we are committed to make things easier and help you. Please don't hesitate to contact our free Fanatical Support team (<a href=\"mailto:fanaticalsupport@grou.ps\">fanaticalsupport@grou.ps</a>) for anything you need. We will be glad to answer your questions and implement the fetures that you request.</p>";
        $msg .= "\r\n\r\n";
        $msg .= "<p>If you feel dissatisfied with something, please contact me personally: my personal email address is <a href=\"mailto:esokullu@gmail.com\">esokullu@gmail.com</a></p>";
        $msg .= "\r\n\r\n";
        $msg .= "<p>Best,</p>";
        
        $this->Body = $msg;
        $this->AddAddress($this->to_email, $this->to_name);
        
        // TODO: Add logging features
        if(!$this->Send(true)) {
            $this->ClearAddresses();
            return false; // an error has occurred
        }
        else {
            $this->ClearAddresses();
            return true;
        }
    	
    }
    
    
    function XInvitesYouToThisSubGroup($sgtitle, $sgdesc, $to_email, $sgid, $rnum) {
    	
    	$this->Subject = $this->_("You Are Invited to a SubGroup");
    	
    	$msg = "";
        $msg .= "<p>$to_email,</p>";
        $msg .= "\r\n\r\n";
        $msg .= "<p>{$this->to_name} invites you to {$sgtitle} (a subgroup of <a href=\"http://grou.ps/{$this->groupname}\">{$this->FromName}</a>)</p>";
        $msg .= "\r\n\r\n";
        $msg .= "<p><i>{$sgdesc}</i></p>";
        $msg .= "\r\n\r\n";
        $msg .= "<p>To join it, follow this link: <a href=\"http://grou.ps/acceptsg.php?sgid={$sgid}&x={$rnum[0]}&p={$rnum[1]}&g={$this->groupname}\">http://grou.ps/acceptsg.php?sgid={$sgid}&x={$rnum[0]}&p={$rnum[1]}</a></p>";
        $msg .= "\r\n\r\n";
        $msg .= "<p>Best,</p>";
        
        $this->Body = $msg;
        $this->AddAddress($to_email);
        
        // TODO: Add logging features
        if(!$this->Send(true)) {
            $this->ClearAddresses();
            return false; // an error has occurred
        }
        else {
            $this->ClearAddresses();
            return true;
        }
        
    }
    
    function strangerXInvitesYouToThisSubGroup($sgtitle, $sgdesc, $to_email) {
    	$this->Subject = $this->_("You Are Invited to a SubGroup");
    	
    	$msg = "";
        $msg .= "<p>{$to_email},</p>";
        $msg .= "\r\n\r\n";
        $msg .= "<p>{$this->to_name} invites you to {$sgtitle} (a subgroup of <a href=\"http://grou.ps/{$this->groupname}\">{$this->FromName}</a>)</p>";
        $msg .= "\r\n\r\n";
        $msg .= "<p><i>{$sgdesc}</i></p>";
        $msg .= "\r\n\r\n";
        $msg .= "<p>To join it, you should first sign up to this group from: <a href=\"http://grou.ps/{$this->groupname}/signup\">http://grou.ps/{$this->groupname}/signup</a></p>";
        $msg .= "\r\n\r\n";
        $msg .= "<p>and then become a member of this subgroup from: <a href=\"http://grou.ps/{$this->groupname}/groups\">http://grou.ps/{$this->groupname}/groups</a></p>";
        $msg .= "\r\n\r\n";
        $msg .= "<p>Best,</p>";
        
        $this->Body = $msg;
        $this->AddAddress($to_email);
        
        // TODO: Add logging features
        if(!$this->Send(true)) {
            $this->ClearAddresses();
            return false; // an error has occurred
        }
        else {
            $this->ClearAddresses();
            return true;
        }
    }
    
    
    function XAddedYouToThisSubGroup($sgtitle, $sgdesc, $to_email, $sgid, $p=null) {

    	$this->Subject = $this->_("Your Friend thinks you are a member of...");
    	
    	$msg = "";
        $msg .= "<p>$to_email,</p>";
        $msg .= "\r\n\r\n";
        $msg .= "<p>{$this->to_name} added you to {$sgtitle} (a subgroup of <a href=\"http://grou.ps/{$this->groupname}\">{$this->FromName}</a>)</p>";
        $msg .= "\r\n\r\n";
        $msg .= "<p><i>{$sgdesc}</i></p>";
        $msg .= "\r\n\r\n";
        $msg .= "<p>If you want, you can leave this subgroup from: <a href=\"http://grou.ps/{$this->groupname}/groups\">http://grou.ps/{$this->groupname}/groups</a></p>";
        $msg .= "\r\n\r\n";
        if($p!=null) {
        	$msg .= "<p>Your password is: {$p}</p>";
        	$msg .= "\r\n\r\n";
        }
        $msg .= "<p>Best,</p>";
        
        $this->Body = $msg;
        $this->AddAddress($to_email);
        
        // TODO: Add logging features
        if(!$this->Send(true)) {
            $this->ClearAddresses();
            return false; // an error has occurred
        }
        else {
            $this->ClearAddresses();
            return true;
        }
        
    }
    
    
    function generateMailID() {
        
        $res = "";
        
        $res = $this->_get_rand_id(50).'@grou.ps';
        
        return '<'.$res.'>';
        
    }
    
    function _get_rand_id($length)
    {
        if($length>0) 
        { 
            $rand_id="";
            for($i=1; $i<=$length; $i++)
            {
                mt_srand((double)microtime() * 1000000);
                $num = mt_rand(1,36);
                $rand_id .= $this->_assign_rand_value($num);
            }
        }
        return $rand_id;
    }
    
    
    function _assign_rand_value($num)
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
    
    
}


?>