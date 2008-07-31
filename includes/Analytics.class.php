<?php

/**
 * User.class.php
 * a class to handle functions about the members
 * of the site
 */



//if(!class_exists('DB'))
//	include_once dirname(__FILE__).'/DB/DB.php';
require_once 'DB.php';
require_once 'includes/VariableSecurity.php';


class Analytics {

    
	var $Database = null;

    
    /**
     * constructor function
     */
	function Analytics() {
		
		
		global $GlobalDatabase;
		
		if(!isset($GlobalDatabase)) {
		
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
			
			$this->Database =& DB::connect($dsn, $options);
			if (PEAR::isError($this->Database)) {
				die($this->Database->getMessage());
			}
	        
	        $q = & $this->Database->query("SET NAMES utf8;");
	  
	        if (PEAR::isError($q)) {
	            die($q->getMessage());
	        }

		}
		else {
			$this->Database = $GlobalDatabase;
		}
        
	}
    
    
    
    function _isValidIP($ip) {
        if (!empty($ip) && ip2long($ip)!=-1) {
            $reserved_ips = array (
                array('0.0.0.0','2.255.255.255'),
                array('10.0.0.0','10.255.255.255'),
                array('127.0.0.0','127.255.255.255'),
                array('169.254.0.0','169.254.255.255'),
                array('172.16.0.0','172.31.255.255'),
                array('192.0.2.0','192.0.2.255'),
                array('192.168.0.0','192.168.255.255'),
                array('255.255.255.0','255.255.255.255')
                );

            foreach ($reserved_ips as $r) {
                $min = ip2long($r[0]);
                $max = ip2long($r[1]);
                if ((ip2long($ip) >= $min) && (ip2long($ip) <= $max)) return false;
            }
            return true;
        } else {
            return false;
        }
    }

    
    
    function _getIP() {
       
        if (isset($_SERVER["HTTP_CLIENT_IP"])&&$this->_isValidIP($_SERVER["HTTP_CLIENT_IP"])) {
            return $_SERVER["HTTP_CLIENT_IP"];
        }
        
        if (isset($_SERVER["HTTP_X_FORWARDED_FOR"])) {
            foreach (explode(",",$_SERVER["HTTP_X_FORWARDED_FOR"]) as $ip) {
                if ($this->_isValidIP(trim($ip))) {
                    return $ip;
                }
            }
        }
        
        if (isset($_SERVER["HTTP_X_FORWARDED"]) && $this->_isValidIP($_SERVER["HTTP_X_FORWARDED"])) {
            return $_SERVER["HTTP_X_FORWARDED"];
        }
        elseif (isset($_SERVER["HTTP_FORWARDED_FOR"]) && $this->_isValidIP($_SERVER["HTTP_FORWARDED_FOR"])) {
            return $_SERVER["HTTP_FORWARDED_FOR"];
        }
        elseif (isset($_SERVER["HTTP_FORWARDED"]) && $this->_isValidIP($_SERVER["HTTP_FORWARDED"])) {
            return $_SERVER["HTTP_FORWARDED"];
        } 
        elseif (isset($_SERVER["HTTP_X_FORWARDED"]) && $this->_isValidIP($_SERVER["HTTP_X_FORWARDED"])) {
            return $_SERVER["HTTP_X_FORWARDED"];
        } 
        else {
            if(isset($_SERVER["REMOTE_ADDR"]))
                return $_SERVER["REMOTE_ADDR"];
            else
                return "";
        }
    }
    
    
    
    
    function groupAccessed($function) {
        
        _filter_var($groupname);
        _filter_var($function);
        
        if(isset($_SESSION['valid_user']))
            $username = $_SESSION['valid_user'];
        else
            $username = "";
        
        $clientIP = $this->_getIP();
        
        $sql = 'INSERT INTO `analytics` (`analysis_id`, `type`, `member_name`, `client_ip`, `opt_obj_1`, `opt_obj_2`, `opt_obj_3`, `opt_obj_4`, `opt_obj_5`, `opt_obj_6`, `opt_obj_7`, `opt_obj_8`, `opt_obj_9`, `opt_obj_10`, `date`) VALUES (\'\', \'GroupAccessed\', \''.$username.'\', \''.$clientIP.'\', \'function="'.$function.'"\', \'\', \'\', \'\', \'\', \'\', \'\', \'\', \'\', \'\', NOW());';
        $q = & $this->Database->query($sql);
		
        if (PEAR::isError($q)) {
            die($q->getMessage());
        }            
       
        
    }
    
    
    function groupNotFound($function) {
        
        _filter_var($groupname);
        _filter_var($function);
        
        if(isset($_SESSION['valid_user']))
            $username = $_SESSION['valid_user'];
        else
            $username = "";
        
        $clientIP = $this->_getIP();
        
        $sql = 'INSERT INTO `analytics` (`analysis_id`, `type`, `member_name`, `client_ip`, `opt_obj_1`, `opt_obj_2`, `opt_obj_3`, `opt_obj_4`, `opt_obj_5`, `opt_obj_6`, `opt_obj_7`, `opt_obj_8`, `opt_obj_9`, `opt_obj_10`, `date`) VALUES (\'\', \'GroupNotFound\', \'\', \''.$username.'\', \''.$clientIP.'\', \'\', \'\', \'\', \'\', \'\', \'\', \'\', \'\', \'\', NOW());';
        $q = & $this->Database->query($sql);
		
        if (PEAR::isError($q)) {
            die($q->getMessage());
        }            
       
        
    }
    
    
    
    function invalidFunction($function) {
        
        _filter_var($groupname);
        _filter_var($function);
        
        if(isset($_SESSION['valid_user']))
            $username = $_SESSION['valid_user'];
        else
            $username = "";
        
        $clientIP = $this->_getIP();
        
        $sql = 'INSERT INTO `analytics` (`analysis_id`, `type`, `member_name`, `client_ip`, `opt_obj_1`, `opt_obj_2`, `opt_obj_3`, `opt_obj_4`, `opt_obj_5`, `opt_obj_6`, `opt_obj_7`, `opt_obj_8`, `opt_obj_9`, `opt_obj_10`, `date`) VALUES (\'\', \'InvalidFunction\', \''.$username.'\', \''.$clientIP.'\', \'invalid_function="'.$function.'"\', \'\', \'\', \'\', \'\', \'\', \'\', \'\', \'\', \'\', NOW());';
        $q = & $this->Database->query($sql);
		
        if (PEAR::isError($q)) {
            die($q->getMessage());
        }            
       
        
    }
    
    
    
    
    
    function registerRefererURL() {

        $refr = "";
        
        if(isset($_SERVER['HTTP_REFERER']))
            $refr = $_SERVER['HTTP_REFERER'];
        
        if( !empty($refr) ) { // means found
        
            $clientIP = $this->_getIP();
        
            $sql = 'INSERT INTO `analytics` (`analysis_id`, `type`,`member_name`, `client_ip`, `opt_obj_1`, `opt_obj_2`, `opt_obj_3`, `opt_obj_4`, `opt_obj_5`, `opt_obj_6`, `opt_obj_7`, `opt_obj_8`, `opt_obj_9`, `opt_obj_10`, `date`) VALUES (\'\', \'Referer\', \'\',\''.$clientIP.'\', \'referer="'.$refr.'"\', \'\', \'\', \'\', \'\', \'\', \'\', \'\', \'\', \'\', NOW());';
            $q = & $this->Database->query($sql);
		
            if (PEAR::isError($q)) {
                die($q->getMessage());
            } 
            
        }
        
    }
    
    
    function firstVisit() {
        
        $clientIP = $this->_getIP();
        
        $sql = 'INSERT INTO `analytics` (`analysis_id`, `type`,`member_name`, `client_ip`, `opt_obj_1`, `opt_obj_2`, `opt_obj_3`, `opt_obj_4`, `opt_obj_5`, `opt_obj_6`, `opt_obj_7`, `opt_obj_8`, `opt_obj_9`, `opt_obj_10`, `date`) VALUES (\'\', \'FirstVisit\', \'\', \'\', \''.$clientIP.'\', \'\', \'\', \'\', \'\', \'\', \'\', \'\', \'\', \'\', \'\', NOW());';
        $q = & $this->Database->query($sql);
		
        if (PEAR::isError($q)) {
            die($q->getMessage());
        } 
        
    }
    
    /**
     * Logs when the user logs in
     * The username parameter comes from outside
     * (instead of $_SESSION checks) because it's
     * safer we think, while user is logging in
     * and logging out
     *
     * @param string $uname username that logs in
     */
    function loggedIn($uname) {
    	
    	_filter_var($uname);
            	
    	$clientIP = $this->_getIP();
        
        $sql = 'INSERT INTO `analytics` (`analysis_id`, `type`, `member_name`, `client_ip`, `opt_obj_1`, `opt_obj_2`, `opt_obj_3`, `opt_obj_4`, `opt_obj_5`, `opt_obj_6`, `opt_obj_7`, `opt_obj_8`, `opt_obj_9`, `opt_obj_10`, `date`) VALUES (\'\', \'LoggedIn\', \''.$uname.'\', \''.$clientIP.'\', \'\', \'\', \'\', \'\', \'\', \'\', \'\', \'\', \'\', \'\', NOW());';
        $q = & $this->Database->query($sql);
		
        if (PEAR::isError($q)) {
            die($q->getMessage());
        }     	
        
    }
    

    /**
     * Logs when the user logs out
     * The username parameter comes from outside
     * (instead of $_SESSION checks) because it's
     * safer we think, while user is logging in
     * and logging out
     *
     * @param string $uname username that logs out
     */
    function loggedOut($uname) {
    	
    	_filter_var($uname);

    	$clientIP = $this->_getIP();
        
        $sql = 'INSERT INTO `analytics` (`analysis_id`, `type`,`member_name`, `client_ip`, `opt_obj_1`, `opt_obj_2`, `opt_obj_3`, `opt_obj_4`, `opt_obj_5`, `opt_obj_6`, `opt_obj_7`, `opt_obj_8`, `opt_obj_9`, `opt_obj_10`, `date`) VALUES (\'\', \'LoggedOut\',  \''.$uname.'\', \''.$clientIP.'\', \'\', \'\', \'\', \'\', \'\', \'\', \'\', \'\', \'\', \'\', NOW());';
        $q = & $this->Database->query($sql);
		
        if (PEAR::isError($q)) {
            die($q->getMessage());
        } 
        
    }
    
    
    function spreadAttempt($user,$msg,$emails) {
        
        $clientIP = $this->_getIP();
        
        _filter_var($user);
        _filter_var($group);
        _filter_var($msg);
       
        
        if(strlen($msg)>247)
            $msg = substr($msg,0,247);
        $msgobj = "msg=\"{$msg}\"";
        
        $toobj1 = "to1=\"";
        $toobj2 = "to2=\"";
        $toobj3 = "to3=\"";
        $tolength = 0;
        foreach($emails as $email) {
         
            _filter_var($email);    
            
            $curlength = $tolength + strlen($email);
            
            if($curlength < 246) {
            
                $toobj1 .= $email.";";  
                
            }
            elseif($curlength < 2*246) {
                
                $toobj2 .= $email.";";  
                
            }
            elseif($curlength < 3*246) {
                
                $toobj3 .= $email.";";  
                
            }
            else {
                
                break;
            }
                        
            $tolength = $curlength;
            
        }
        $toobj1 .= "\"";
        $toobj2 .= "\"";
        $toobj3 .= "\"";
        
        
        
        $sql = 'INSERT INTO `analytics` (`analysis_id`, `type`,`member_name`, `client_ip`, `opt_obj_1`, `opt_obj_2`, `opt_obj_3`, `opt_obj_4`, `opt_obj_5`, `opt_obj_6`, `opt_obj_7`, `opt_obj_8`, `opt_obj_9`, `opt_obj_10`, `date`) VALUES (\'\', \'SpreadAttempt\', \''.$user.'\', \''.$clientIP.'\', \''.$toobj1.'\', \''.$toobj2.'\', \''.$toobj3.'\', \'\', \'\', \'\', \'\', \'\', \'\', \'\', NOW());';
        $q = & $this->Database->query($sql);
		
        if (PEAR::isError($q)) {
            die($q->getMessage());
        } 
        
        
       
        
    }
    
    
    function groupInvitationAttempt($user,$msg,$emails) {
        
        $clientIP = $this->_getIP();
        
        _filter_var($user);
        _filter_var($group);
        _filter_var($msg);
       
        
        if(strlen($msg)>247)
            $msg = substr($msg,0,247);
        $msgobj = "msg=\"{$msg}\"";
        
        $toobj1 = "to1=\"";
        $toobj2 = "to2=\"";
        $toobj3 = "to3=\"";
        $tolength = 0;
        foreach($emails as $email) {
         
            _filter_var($email);    
            
            $curlength = $tolength + strlen($email);
            
            if($curlength < 246) {
            
                $toobj1 .= $email.";";  
                
            }
            elseif($curlength < 2*246) {
                
                $toobj2 .= $email.";";  
                
            }
            elseif($curlength < 3*246) {
                
                $toobj3 .= $email.";";  
                
            }
            else {
                
                break;
            }
                        
            $tolength = $curlength;
            
        }
        $toobj1 .= "\"";
        $toobj2 .= "\"";
        $toobj3 .= "\"";
        
        
        
        $sql = 'INSERT INTO `analytics` (`analysis_id`, `type`, `member_name`, `client_ip`, `opt_obj_1`, `opt_obj_2`, `opt_obj_3`, `opt_obj_4`, `opt_obj_5`, `opt_obj_6`, `opt_obj_7`, `opt_obj_8`, `opt_obj_9`, `opt_obj_10`, `date`) VALUES (\'\', \'GroupInvitation\', \''.$user.'\', \''.$clientIP.'\', \''.$toobj1.'\', \''.$toobj2.'\', \''.$toobj3.'\', \'\', \'\', \'\', \'\', \'\', \'\', \'\', NOW());';
        $q = & $this->Database->query($sql);
		
        if (PEAR::isError($q)) {
            die($q->getMessage());
        } 
        
        
       
        
    }
    
    
}

?>