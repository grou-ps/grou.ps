<?php


//if(!class_exists('DB'))
//	include_once dirname(__FILE__).'/DB/DB.php';
require_once 'DB.php';
if(!function_exists("_filter_var"))
	include_once 'includes/VariableSecurity.php';




function getUsernameByMembershipID($dbconn,$membership_id) {

    
    _filter_var($membership_id);
    
    $sql = "SELECT member_id FROM memberships WHERE membership_id='{$membership_id}';";
    
    $member_id = $dbconn->getOne($sql);
    if (PEAR::isError($member_id)) {
        die($member_id->getMessage());
    }
    
    $sql = "SELECT member_login FROM members WHERE member_id='{$member_id}';";
    
    $username = $dbconn->getOne($sql);
    if (PEAR::isError($username)) {
        die($username->getMessage());
    }
    
    return $username;
    
    
}


class User {

    
	var $Database = null;
    var $Username = null;
    
    var $MembershipID = null;
    var $Avatar = null;
    var $Birthday = null;
    var $Profile = null;
    
    var $MemberID = null;
    
    var $tempActivityRank = 0;

    
    /**
     * constructor function
     */
	function User($username) {
	
		global $GlobalDatabase;
		
		
		_filter_var($username);
        
        $this->Username = $username;
		
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
    
    
	
	function hasProfile() {
		
		if($this->Profile!=null) {
			return $this->Profile;
		}
		else {
			
			$mid = $this->_getMemberID();
			
			$sql = "SELECT COUNT(profile_id) FROM profiles WHERE member_id='{$mid}';";
			
			$res = $this->Database->getOne($sql);	
			
			if (PEAR::isError($res)) {
				die($res->getMessage());
			}	
			
			
			if($res==0) {
				$this->Profile = false;
				return false;
			}
			else {
				$this->Profile = true;
				return true;
			}
			
		}
		
	}
	
	
	
	function createProfile() {
		
		if($this->hasProfile())
			return true;
		
			if($this->duplicateOldProfile()) {
				$this->Profile = true;
				return true;
			}
			else {
				
				
				
		        $member_id = $this->_getMemberID();
				
				$sql = 'INSERT INTO `profiles` (`profile_id` , `member_id`, `modified_on`, `favorite_songs`, `favorite_singers`, `favorite_movies`, `favorite_actors`, `favorite_books`, `favorite_authors`, `favorite_sportsmen`, `favorite_artists`, `favorite_cities`, `favorite_colors`, `motto`, `adored_people`, `birthday`, `religion`, `nationality`, `second_nationality`, `ethnic_race`, `marital_status`, `children`, `sexe`, `sexual_orientation`, `occupation`, `hobbies`, `fobbies`, `contact_aim`, `contact_yahoo`, `contact_icq`, `contact_jabber`, `contact_msn`, `tags`, `show_tags`, `show_favourites`, `show_lovestuff`, `show_businessstuff`, `show_healthstuff` ) VALUES (NULL,  \''.$member_id.'\', NOW(), \'\', \'\', \'\', \'\', \'\', \'\', \'\', \'\', \'\', \'\', \'\', \'\', \'1982-10-10\', \'\', \'us\', \'\', \'\', \'never_married\', \'0\', \'female\', \'heterosexual\', \'\', \'\', \'\', \'\', \'\', \'\', \'\', \'\', \'\', \'Y\', \'Y\', \'Y\', \'Y\', \'Y\');';
				
				$res = $this->Database->query($sql);
				
				if (PEAR::isError($res)) {
					die($res->getMessage());
				}
				
				if($res) {
		            $this->Profile = true;
		            return true;
		        }
		        else {
		            $this->Profile = false;
		            return false;
		        }
			}
	}
	
	function duplicateOldProfile() {
		
		$mid = $this->_getMemberID();
			
		$sql = "SELECT * FROM profiles WHERE member_id='{$mid}' ORDER BY profile_id DESC LIMIT 1;";
		
		$res = $this->Database->getRow($sql,array(),2);
		
		
		
		if(isset($res['profile_id'])&&!empty($res['profile_id'])) {
			
			$sql = "INSERT INTO profiles (profile_id,member_id,modified_on,favorite_songs,favorite_singers,favorite_movies,favorite_actors,favorite_books,favorite_authors,favorite_sportsmen,favorite_artists,favorite_cities,favorite_colors,motto,adored_people,birthday,religion,nationality,second_nationality,ethnic_race,marital_status,children,sexe,sexual_orientation,occupation,hobbies,fobbies,contact_aim,contact_yahoo,contact_icq,contact_jabber,contact_msn,tags,show_tags,show_favourites,show_lovestuff,show_businessstuff,show_healthstuff) VALUES (NULL,'".mysql_real_escape_string($mid)."',NOW(),'".mysql_real_escape_string($res['favorite_songs'])."','".mysql_real_escape_string($res['favorite_singers'])."','".mysql_real_escape_string($res['favorite_movies'])."','".mysql_real_escape_string($res['favorite_actors'])."','".mysql_real_escape_string($res['favorite_books'])."','".mysql_real_escape_string($res['favorite_authors'])."','".mysql_real_escape_string($res['favorite_sportsmen'])."','".mysql_real_escape_string($res['favorite_artists'])."','".mysql_real_escape_string($res['favorite_cities'])."','".mysql_real_escape_string($res['favorite_colors'])."','".mysql_real_escape_string($res['motto'])."','".mysql_real_escape_string($res['adored_people'])."','".mysql_real_escape_string($res['birthday'])."','".mysql_real_escape_string($res['religion'])."','".mysql_real_escape_string($res['nationality'])."','".mysql_real_escape_string($res['second_nationality'])."','".mysql_real_escape_string($res['ethnic_race'])."','".mysql_real_escape_string($res['marital_status'])."','".mysql_real_escape_string($res['children'])."','".mysql_real_escape_string($res['sexe'])."','".mysql_real_escape_string($res['sexual_orientation'])."','".mysql_real_escape_string($res['occupation'])."','".mysql_real_escape_string($res['hobbies'])."','".mysql_real_escape_string($res['fobbies'])."','".mysql_real_escape_string($res['contact_aim'])."','".mysql_real_escape_string($res['contact_yahoo'])."','".mysql_real_escape_string($res['contact_icq'])."','".mysql_real_escape_string($res['contact_jabber'])."','".mysql_real_escape_string($res['contact_msn'])."','".mysql_real_escape_string($res['tags'])."','".mysql_real_escape_string($res['show_tags'])."','".mysql_real_escape_string($res['show_favourites'])."','".mysql_real_escape_string($res['show_lovestuff'])."','".mysql_real_escape_string($res['show_businessstuff'])."','".mysql_real_escape_string($res['show_healthstuff'])."');";
		
			$res = $this->Database->query($sql) or die($sql);
			
			if(PEAR::isError($res)) {
				return false;
			}
			else {
				return true;
			}
		}
		else {
			return false;
		}
		
	}
	
	
	
    function getEmail() {
        
        $email = & $this->Database->getOne("SELECT email FROM members WHERE member_login='{$this->Username}'");
		
		if (PEAR::isError($email)) {
			die($email->getMessage());
		}
		
		_filter_res_var($email);
		return $email;
        
    }
    
    
    function getID() {
    	
    	return $this->_getMemberID();
        
    	/*
        $id = & $this->Database->getOne("SELECT member_id FROM members WHERE member_login='{$this->Username}'");
		
		if (PEAR::isError($id)) {
			die($id->getMessage());
		}
		
		_filter_res_var($id);
		return $id;
		*/
        
    }
    
    
    
    function getNameSurname() {
        
        $membership_id = $this->getMembershipID();
        
        $namesurname = & $this->Database->getOne("SELECT member_name FROM memberships WHERE membership_id = '{$membership_id}'");
		
		if (PEAR::isError($namesurname)) {
			die($namesurname->getMessage());
		}
		
        
        if(!empty($namesurname)&&$namesurname!=$this->Username)
            return $namesurname;
        else {
            $email = $this->getEmail();
            $email = explode("@",$email);
            return $email[0];
        }
        
    }
    
    function userExists() {
        
        $res = & $this->Database->getOne("SELECT COUNT(member_id) FROM members WHERE member_login='{$this->Username}'");
		
		if (PEAR::isError($res)) {
			// die($res->getMessage());
            return false;
		}
		
		if($res==1)
            return true;
        else
            return false;
        
    }
    
    
    function getMembershipID() {
        
        if( $this->MembershipID!=null  ) {
         
            return $this->MembershipID;
            
        }
        else {
            
            $member_id = _getMemberID($this->Username);
        
            $res = & $this->Database->getOne("SELECT membership_id FROM memberships WHERE member_id = '{$member_id}'");
		
            if (PEAR::isError($res)) {
                die($res->getMessage());
            }
            
            $this->MembershipID = $res;
               
            
            return $res;
            
        }
        
    }
    
    
    
    
    function getBirthday($formatted=false) {
    
        if( $this->Birthday!=null  ) {
         
        	if(!$formatted)
            	return $this->Birthday;
            else 
            
            	return date("m/d/Y", strtotime($this->Birthday));
            
        }
        else {
		
		$has_profile = $this->hasProfile();
		
		if(!$has_profile) {
			// default value
			$res = "1980-10-10";
		}
		else {
            
		    $member_id = $this->_getMemberID();
		
		    $res = & $this->Database->getOne("SELECT birthday FROM profiles WHERE member_id = '{$member_id}' ORDER BY profile_id DESC LIMIT 0,1");
			
		    if (PEAR::isError($res)) {
			die($res->getMessage());
		    }
		}
            
           
                $this->Birthday = $res;
             
            
            if(!$formatted)
            	return $res;
            else 
            	return date("m/d/Y", strtotime($res));
            
        }
        
    }
    
    
    
    function getAge($birthday=null) {
        
		if($birthday==null)
			$birthday = $this->getBirthday();
	
		$birth_year = substr($birthday,0,4); 
        $birth_month = substr($birthday,5,2);
        $birth_day = substr($birthday,8,2);
		
        $datestamp = date("d.m.Y", mktime());
        $t_arr = explode("." , $datestamp);
     
        $current_day = $t_arr[0];
        $current_month = $t_arr[1];
        $current_year = $t_arr[2]; 
	
		$year_dif = $current_year - $birth_year;
        
        if(($birth_month > $current_month) || ($birth_month == $current_month && $current_day < $birth_day))
            $age = $year_dif - 1;
            else
                $age = $year_dif; 
		
		return $age;
        
        
    }
    
    
    function getZSign() {
	
        
        $birthday_date = $this->getBirthday();
	
        $birthday = strtotime($birthday_date);
        $birthday = intval(date('md', $birthday));

        //Since the signs are not even across one year, add 1200 to anything less than 321
        if ($birthday < 321) $birthday += 1200;


        $signs = array(
            'Aries' => array(321, 419),
            'Taurus' => array(420, 520),
            'Gemini' => array(521, 621),
            'Cancer' => array(622, 722),
            'Leo' => array(723, 822),
            'Virgo' => array(823, 922),
            'Libra' => array(923, 1022),
            'Scorpio' => array(1023, 1121),
            'Sagittarius' => array(1122, 1221),
            'Capricorn' => array(1222, 1319),
            'Aquarius' => array(1320, 1418),
            'Pisces' => array(1419, 1520)
            );

        foreach ($signs as $sign=>$s){
            if ($birthday >= $s[0] && $birthday <= $s[1]){
                break;
            }
        }
        
        return $sign; 
	
	
	}
    
    
    function getTags() {
	    
        $member_id = $this->_getMemberID();
        
        
	if($this->hasProfile())
		$res = & $this->Database->getOne("SELECT tags FROM profiles WHERE member_id = '{$member_id}' ORDER BY profile_id DESC LIMIT 0,1");
	else
		$res = "";
                
        if (PEAR::isError($res)) {
            die($res->getMessage());
        }
        
        return $res;
    }
    
    
    function getMotto() {
        
        $member_id = $this->_getMemberID();
        
	if($this->hasProfile())
		$res = & $this->Database->getOne("SELECT motto FROM profiles WHERE member_id = '{$member_id}' ORDER BY profile_id DESC LIMIT 0,1");
	else
		$res = "";
		
        if (PEAR::isError($res)) {
            die($res->getMessage());
        }
        
        return $res;
        
    }
    
    function getWebSite() {
        
        $member_id = $this->_getMemberID();
        
	if($this->hasProfile())
		$res = & $this->Database->getOne("SELECT adored_people FROM profiles WHERE member_id = '{$member_id}' ORDER BY profile_id DESC LIMIT 0,1");
	else
		$res = "";
		
        if (PEAR::isError($res)) {
            die($res->getMessage());
        }
        
        
        if(!eregi("^(http|https)+(:\/\/)+[a-z0-9_-]+\.+[a-z0-9_-]",$res)) {
        	$res = "";
        }
        
        return $res;
        
    }    
    
    function getNationality() {
        

        $member_id = $this->_getMemberID();
        
	if($this->hasProfile())
		$res = & $this->Database->getOne("SELECT nationality FROM profiles WHERE member_id = '{$member_id}' ORDER BY profile_id DESC LIMIT 0,1");
	else
		$res = "us"; // default value
		
        if (PEAR::isError($res)) {
            die($res->getMessage());
        }
        
        return $res;
        
    }
    
    

    
    
    
    function getSexe() {
        

        $member_id = $this->_getMemberID();
        
	if($this->hasProfile())
		$res = & $this->Database->getOne("SELECT sexe FROM profiles WHERE member_id = '{$member_id}' ORDER BY profile_id DESC LIMIT 0,1");
	else
		$res = 'female';
		
        if (PEAR::isError($res)) {
            die($res->getMessage());
        }
        
        return $res;
        
    }
    
    
    
    function getSexeIcon($sexe) {
    
    global $service_host;
        if(!isset($service_host)) {
        if($sexe=='female')
            return "http://grou.ps/images/female.png";
        else	
            return "http://grou.ps/images/male.png";
        }
	else {
	if($sexe=='female')
            return $service_host."images/female.png";
        else	
            return $service_host."images/male.png";
        }
	
    }
    
    
    
    
    function getMaritalStatus() {
        
        $member_id = $this->_getMemberID();
        
	if($this->hasProfile())
		$res = & $this->Database->getOne("SELECT marital_status FROM profiles WHERE member_id = '{$member_id}'   ORDER BY profile_id DESC LIMIT 0,1");
	else
		$res = "never_married";
		
        if (PEAR::isError($res)) {
            die($res->getMessage());
        }
        
        // small fix
        if($res=='never_married')
            $res = 'single';
        
        return $res;
        
    }
    
    
    function getContactAIM($gname) {
                
        $member_id = $this->_getMemberID();
        
        
	if($this->hasProfile())
		$res = & $this->Database->getOne("SELECT contact_aim FROM profiles WHERE member_id = '{$member_id}'    ORDER BY profile_id DESC LIMIT 0,1");
	else
		$res = "";
		
        if (PEAR::isError($res)) {
            die($res->getMessage());
        }
                
        return $res;
    }
    
    
    function getContactICQ() {
        
        $member_id = $this->_getMemberID();
        
        
	if($this->hasProfile())
		$res = & $this->Database->getOne("SELECT contact_icq FROM profiles WHERE member_id = '{$member_id}'    ORDER BY profile_id DESC LIMIT 0,1");
	else
		$res = "";
		
        if (PEAR::isError($res)) {
            die($res->getMessage());
        }
                
        return $res;
    }
    
    function getContactMSN() {
        
    	
        $member_id = $this->_getMemberID();
        
        
        $res = & $this->Database->getOne("SELECT contact_msn FROM profiles WHERE member_id = '{$member_id}'    ORDER BY profile_id DESC LIMIT 0,1");
		
        if (PEAR::isError($res)) {
            die($res->getMessage());
        }
                
        return $res;
    }
    
    
    function getContactYahoo() {
        
    	
        $member_id = $this->_getMemberID();
        
        
        $res = & $this->Database->getOne("SELECT contact_yahoo FROM profiles WHERE member_id = '{$member_id}'    ORDER BY profile_id DESC LIMIT 0,1");
		
        if (PEAR::isError($res)) {
            die($res->getMessage());
        }
                
        return $res;
    }
    
    
    function getContactJabber() {
        
	    
        $member_id = $this->_getMemberID();
        
        
	    if($this->hasProfile())
		    $res = & $this->Database->getOne("SELECT contact_jabber FROM profiles WHERE member_id = '{$member_id}'    ORDER BY profile_id DESC LIMIT 0,1");
		else
		$res = "";
		
        if (PEAR::isError($res)) {
            die($res->getMessage());
        }
                
        return $res;
    }
    
    
    
    
    /**
     * This function returns absolute URL of the avatar
     */
    function getAvatar() {
        
        global $service_host;
        
        // we'll return absolute URL of the
        // avatar
        $addr = "";
        
        $addr_base = "{$service_host}avatars/%s/80.png";
        $addr_file = dirname(__FILE__)."/../avatars/%s/80.png";
        
        
        $membership_id = $this->getMembershipID();
        
        $avatar = & $this->Database->getOne("SELECT avatar FROM memberships WHERE membership_id='{$membership_id}'");
		
		if (PEAR::isError($avatar)) {
			die($avatar->getMessage());
		}
		
		_filter_res_var($avatar);
        
        $avatar = intval($avatar);
        
        // one of the defaul avatars
        // when the avatar # increases
        // the 15 number SHOULD change also
        if($avatar>=1&&$avatar<=15) {
            
            $iid = "b"; // $iid = "a{$avatar}"; // aTOb
            $addr = sprintf($addr_base,$iid);
            return $addr;
            
        }
        elseif($avatar==-1) {
        
            $addr = sprintf($addr_base, $membership_id);
            $addr_file = sprintf($addr_file, $membership_id);
            
            if(!file_exists($addr_file)) {
                // default pict
                $addr = sprintf($addr_base, "b"); // $addr = sprintf($addr_base, "a12"); // aTOb
                return $addr;
            }
            else {
                return $addr;
            }
        }
        else {
            
            // default pict
            $addr = sprintf($addr_base, "b"); // $addr = sprintf($addr_base, "a12"); // aTOb
            return $addr;
            
        }
        
    }
    
    
    /**
     * Returns the user avatar in HTML string format
     *
     * @param string $group_name
     * @return string address in HTML format
     */
    function getAvatarAsHTML($padding="", $border=false, $withname=false) {
    	
    	global $service_host;
    	
    	$w = 80;
    	$h = 80;
    	
    	$addr = $this->getAvatar();
    	$namesurname = $this->getNameSurname();
    	
    	$style = "";
    	
   	   	if(!empty($padding)&&is_int($padding)) {
    		$style .= "padding:{$padding}px;";
    	}
    	
    	if($border) {
    		$style .= "border: 1px dotted #f00;";
    	}
    	
    	
    	
    	if($withname) {
    		$addr = $service_host."avatar.php?q=".base64_url_encode($namesurname."::::".$addr);
    		$h = 100;
    	}
    	
    	$html = sprintf("<img src=\"%s\" width=\"{$w}\" height=\"{$h}\" border=\"0\" alt=\"%s\" style=\"%s\" />"
    	, $addr
    	, $namesurname
    	, $style
    	);
    	
    	return $html;
    	
    	
    }
    
    
    
    function setAvatar($avatar) {
        
        $membership_id = $this->getMembershipID();
        
        $sql = "UPDATE memberships SET avatar='{$avatar}' WHERE membership_id='{$membership_id}'";
        $res = $this->Database->query($sql);
        
        if (PEAR::isError($res)) {
			die($res->getMessage());
		}
        
        return $res;
        
    }
    
    function getAvatarNo($group_name) {
    	
    	$membership_id = $this->getMembershipID($group_name);
    	
    	$sql = "SELECT avatar FROM memberships WHERE membership_id='{$membership_id}'";
    	$res = $this->Database->getOne($sql);
    	
    	if (PEAR::isError($res)) {
			die($res->getMessage());
		}
        
        return $res;
    	
    }
    
    
    function copyAvatar($from,$to) {
    	
        $dir_from = dirname(__FILE__)."/../avatars/{$from}/";
        $dir_to = dirname(__FILE__)."/../avatars/{$to}/";
        
        
        @mkdir($dir_to);
        @mkdir($dir_from);
        
       $src = dir($dir_from);

		while (false !== $entry = $src->read()) {

			if ($entry != '.' && $entry != '..') {
				
				copy($dir_from.$entry,$dir_to);
				
			}
			
		}
		
		$src->close();
        
        $this->setAvatar($group_name,$to);
    	
    }
    
    
    function setCustomAvatar() {
     
        // same as setAvatar
        // with the only difference as parameter -1
        $res = $this->setAvatar(-1);
        return $res;
    }
    
    
    /**
     * This function returns absolute URL of the avatar
     */
    function getMiniIcon() {
        
        global $app_base;
        
        // we'll return absolute URL of the
        // avatar
        $addr = "";
        
        $addr_base = "http://grou.ps/avatars/%s/16.png";
        $addr_file = $app_base."avatars/%s/16.png";
        
        
        $membership_id = $this->getMembershipID($group_name);
        
        $avatar = & $this->Database->getOne("SELECT avatar FROM memberships WHERE membership_id='{$membership_id}'");
		
		if (PEAR::isError($avatar)) {
			die($avatar->getMessage());
		}
		
		_filter_res_var($avatar);
        
        $avatar = intval($avatar);
        
        // one of the defaul avatars
        // when the avatar # increases
        // the 15 number SHOULD change also
        if($avatar>=1&&$avatar<=15) {
            
            $iid = "b"; // $iid = "a{$avatar}"; // aTOb
            $addr = sprintf($addr_base,$iid);
            return $addr;
            
        }
        elseif($avatar==-1) {
        
            $addr = sprintf($addr_base, $membership_id);
            $addr_file = sprintf($addr_file, $membership_id);
            
            if(!file_exists($addr_file)) {
                // default pict
                $addr = sprintf($addr_base, "b"); // $addr = sprintf($addr_base, "a12"); // aTOb
                return $addr;
            }
            else {
                return $addr;
            }
        }
        else {
            
            // default pict
            $addr = sprintf($addr_base, "b"); // $addr = sprintf($addr_base, "a12"); // aTOb
            return $addr;
            
        }
        
    }
    
    function getMiniIconAsHTML($group_name,$withnamesurname=false) {
    	$icon = $this->getMiniIcon($group_name);
    	if(!$withnamesurname)
    		return sprintf("<a href=\"http://grou.ps/%s/people/%s\"><img src=\"%s\" width=\"16\" height=\"16\" alt=\"\" border=\"0\" /></a>",$group_name,$this->Username,$icon);
    	else 
    		return sprintf("<a href=\"http://grou.ps/%s/people/%s\"><img src=\"%s\" width=\"16\" height=\"16\" alt=\"\" border=\"0\" /> %s</a>",$group_name,$this->Username,$icon,$this->getNameSurname($group_name));
    }
    
    
    /**
     * This function returns absolute URL of the avatar
     */
    function getMapMarker($group_name,$type='') {
        
        global $app_base;
        
        // we'll return absolute URL of the
        // avatar
        $addr = "";
        
        if($type=='souvenir') {
            $addr_base = "http://grou.ps/avatars/%s/marker.png";
            $addr_file = $app_base."avatars/%s/marker.png";
        }
        elseif($type=='current_location') {
            $addr_base = "http://grou.ps/avatars/%s/marker-blue.png";
            $addr_file = $app_base."avatars/%s/marker-blue.png";
        }
        else {
            $addr_base = "http://grou.ps/avatars/%s/marker.png";
            $addr_file = $app_base."avatars/%s/marker.png";
        }
        
        $membership_id = $this->getMembershipID($group_name);
        
        $avatar = & $this->Database->getOne("SELECT avatar FROM memberships WHERE membership_id='{$membership_id}'");
		
		if (PEAR::isError($avatar)) {
			die($avatar->getMessage());
		}
		
		_filter_res_var($avatar);
        
        $avatar = intval($avatar);
        
        // one of the defaul avatars
        // when the avatar # increases
        // the 15 number SHOULD change also
        if($avatar>=1&&$avatar<=15) {
            
            $iid = "b"; // $iid = "a{$avatar}"; // aTOb
            $addr = sprintf($addr_base,$iid);
            return $addr;
            
        }
        elseif($avatar==-1) {
        
            $addr = sprintf($addr_base, $membership_id);
            $addr_file = sprintf($addr_file, $membership_id);
            
            if(!file_exists($addr_file)) {
                // default pict
                $addr = sprintf($addr_base, "b"); // $addr = sprintf($addr_base, "a12"); // aTOb
                return $addr;
            }
            else {
                return $addr;
            }
            
            return $addr;
        }
        else {
            
            // default pict
            $addr = sprintf($addr_base, "b"); // $addr = sprintf($addr_base, "a12"); // aTOb
            return $addr;
            
        }
        
        
    }
    
    
    
    
    function canShowFavourites() {
        
        
        $member_id = $this->_getMemberID();
        
        
	if($this->hasProfile()) {
		$sql = "SELECT show_favourites FROM profiles WHERE member_id='{$member_id}'    ORDER BY profile_id DESC LIMIT 0,1;";
        
	
        $res = $this->Database->getOne($sql);
        		
		if (PEAR::isError($res)) {
			die($res->getMessage());
		}
	}
	else
		$res = 'Y';
        
        if($res=='Y')
            return true;
        else
            return false;
        
        
    }
    
    function canShowTags() {
        
                
        
        $member_id = $this->_getMemberID();
        
        
	if($this->hasProfile()) {
        $sql = "SELECT show_tags FROM profiles WHERE member_id='{$member_id}'    ORDER BY profile_id DESC LIMIT 0,1;";
        
        $res = $this->Database->getOne($sql);
        		
		if (PEAR::isError($res)) {
			die($res->getMessage());
		}
	}
	else
		$res = 'Y';
        
        if($res=='Y')
            return true;
        else
            return false;
        
    }
    
    function canShowLoveStuff() {
                
        
        $member_id = $this->_getMemberID();
        
        
	if($this->hasProfile()) {
        $sql = "SELECT show_lovestuff FROM profiles WHERE member_id='{$member_id}'    ORDER BY profile_id DESC LIMIT 0,1;";
        
        $res = $this->Database->getOne($sql);
        		
		if (PEAR::isError($res)) {
			die($res->getMessage());
		}
        }
	else
		$res = 'Y';
		
        if($res=='Y')
            return true;
        else
            return false;
    }
    
    function canShowHealthStuff() {
                
        
        $member_id = $this->_getMemberID();
        
        
	if($this->hasProfile()) {
        $sql = "SELECT show_healthstuff FROM profiles WHERE member_id='{$member_id}'    ORDER BY profile_id DESC LIMIT 0,1;";
        
        $res = $this->Database->getOne($sql);
        		
		if (PEAR::isError($res)) {
			die($res->getMessage());
		}
	}
	else
		$res = 'Y';
        
        if($res=='Y')
            return true;
        else
            return false;
    }
    
    function canShowBusinessStuff() {
                
        
        $member_id = $this->_getMemberID();
        
        
	if($this->hasProfile()) {
        $sql = "SELECT show_businessstuff FROM profiles WHERE member_id='{$member_id}'    ORDER BY profile_id DESC LIMIT 0,1;";
        
        $res = $this->Database->getOne($sql);
        		
		if (PEAR::isError($res)) {
			die($res->getMessage());
		}
	}
	else
		$res = 'Y';
        
        if($res=='Y')
            return true;
        else
            return false;
        
    }
    
    function showLoveStuff() {
                
        
        $member_id = $this->_getMemberID();
        
        
	if(!$this->hasProfile()) 
		$this->createProfile($gname);
	
        $sql = "UPDATE profiles SET show_lovestuff='Y' WHERE member_id='{$member_id}'    ORDER BY profile_id DESC LIMIT 1;";
        
        $res = $this->Database->query($sql);
        		
		if (PEAR::isError($res)) {
			die($res->getMessage());
		}
	
        
        return $res;
        
    }
    
    function showBusinessStuff() {
                
        
        $member_id = $this->_getMemberID();
        
	
	if(!$this->hasProfile()) 
		$this->createProfile($gname);
        
        $sql = "UPDATE profiles SET show_businessstuff='Y' WHERE member_id='{$member_id}'    ORDER BY profile_id DESC LIMIT 1;";
        
        $res = $this->Database->query($sql);
        		
		if (PEAR::isError($res)) {
			die($res->getMessage());
		}
        
        return $res;
        
    }
    
    function showHealthStuff() {
                
        
        $member_id = $this->_getMemberID();
        
	
	if(!$this->hasProfile()) 
		$this->createProfile($gname);
        
        $sql = "UPDATE profiles SET show_healthstuff='Y' WHERE member_id='{$member_id}'    ORDER BY profile_id DESC LIMIT 1;";
        
        $res = $this->Database->query($sql);
        		
		if (PEAR::isError($res)) {
			die($res->getMessage());
		}
        
        return $res;
        
    }
    
    
    
    
    
    
    function showFavourites() {
                
        
        $member_id = $this->_getMemberID();
        
	
	if(!$this->hasProfile()) 
		$this->createProfile($gname);
        
        $sql = "UPDATE profiles SET show_favourites='Y' WHERE member_id='{$member_id}'    ORDER BY profile_id DESC LIMIT 1;";
        
        $res = $this->Database->query($sql);
        		
		if (PEAR::isError($res)) {
			die($res->getMessage());
		}
        
        return $res;
        
    }
    
    function showTags() {
                
        
        $member_id = $this->_getMemberID();
        
	
	if(!$this->hasProfile()) 
		$this->createProfile($gname);
        
        $sql = "UPDATE profiles SET show_tags='Y' WHERE member_id='{$member_id}'  ORDER BY profile_id DESC LIMIT 1;";
        
        $res = $this->Database->query($sql);
        		
		if (PEAR::isError($res)) {
			die($res->getMessage());
		}
        
        return $res;
        
    }
     
    function hideLoveStuff() {
                
        
        $member_id = $this->_getMemberID();
        
	
	if(!$this->hasProfile()) 
		$this->createProfile($gname);
        
        $sql = "UPDATE profiles SET show_lovestuff='N' WHERE member_id='{$member_id}'    ORDER BY profile_id DESC LIMIT 1;";
        
        $res = $this->Database->query($sql);
        		
		if (PEAR::isError($res)) {
			die($res->getMessage());
		}
        
        return $res;
        
    }
    
    function hideBusinessStuff() {
                
        
        $member_id = $this->_getMemberID();
        
	
	if(!$this->hasProfile()) 
		$this->createProfile($gname);
        
        $sql = "UPDATE profiles SET show_businessstuff='N' WHERE member_id='{$member_id}'    ORDER BY profile_id DESC LIMIT 1;";
        
        $res = $this->Database->query($sql);
        		
		if (PEAR::isError($res)) {
			die($res->getMessage());
		}
        
        return $res;
        
    }
    
    function hideHealthStuff() {
                
        
        $member_id = $this->_getMemberID();
        
	
	if(!$this->hasProfile()) 
		$this->createProfile($gname);
        
        $sql = "UPDATE profiles SET show_healthstuff='N' WHERE member_id='{$member_id}'    ORDER BY profile_id DESC LIMIT 1;";
        
        $res = $this->Database->query($sql);
        		
		if (PEAR::isError($res)) {
			die($res->getMessage());
		}
        
        return $res;
        
    }
    
    function hideFavourites() {
                
        
        $member_id = $this->_getMemberID();
        
	
	if(!$this->hasProfile()) 
		$this->createProfile($gname);
        
        $sql = "UPDATE profiles SET show_favourites='N' WHERE member_id='{$member_id}'    ORDER BY profile_id DESC LIMIT 1;";
        
        $res = $this->Database->query($sql);
        		
		if (PEAR::isError($res)) {
			die($res->getMessage());
		}
        
        return $res;
        
    }
    
    function hideTags() {
                
        
        $member_id = $this->_getMemberID();
        
	
	if(!$this->hasProfile()) 
		$this->createProfile($gname);
        
        $sql = "UPDATE profiles SET show_tags='N' WHERE member_id='{$member_id}'    ORDER BY profile_id DESC LIMIT 1;";
        
        $res = $this->Database->query($sql);
        		
		if (PEAR::isError($res)) {
			die($res->getMessage());
		}
        
        return $res;
        
    }
    
    
    
    function getFavouriteSongs() {
                
        
        $member_id = $this->_getMemberID();
        
	
	$res = "";
	if($this->hasProfile()) { 
        
        $sql = "SELECT favorite_songs FROM profiles WHERE member_id = '{$member_id}'    ORDER BY profile_id DESC LIMIT 0,1;";
        
        $res = $this->Database->getOne($sql);
        		
		if (PEAR::isError($res)) {
			die($res->getMessage());
		}
        }
        return $this->_explodeCommas($res);;
        
    }
    
    
    function getFavouriteSingers() {
                
        
        $member_id = $this->_getMemberID();
        
        
	$res = "";
	if($this->hasProfile()) { 
        $sql = "SELECT favorite_singers FROM profiles WHERE member_id = '{$member_id}'    ORDER BY profile_id DESC LIMIT 0,1;";
        
        $res = $this->Database->getOne($sql);
        		
		if (PEAR::isError($res)) {
			die($res->getMessage());
		}
        }
        return $this->_explodeCommas($res);;
        
    }
    
    
    function getFavouriteMovies() {
                
        
        $member_id = $this->_getMemberID();
        
        
	$res = "";
	if($this->hasProfile()) { 
        $sql = "SELECT favorite_movies FROM profiles WHERE member_id = '{$member_id}'     ORDER BY profile_id DESC LIMIT 0,1;";
        
        $res = $this->Database->getOne($sql);
        		
		if (PEAR::isError($res)) {
			die($res->getMessage());
		}
        }
        return $this->_explodeCommas($res);;
        
    }
    
    function getFavouriteActors() {
                
        
        $member_id = $this->_getMemberID();
        
        
	$res = "";
	if($this->hasProfile()) { 
        $sql = "SELECT favorite_actors FROM profiles WHERE member_id = '{$member_id}'     ORDER BY profile_id DESC LIMIT 0,1;";
        
        $res = $this->Database->getOne($sql);
        		
		if (PEAR::isError($res)) {
			die($res->getMessage());
		}
        }
        return $this->_explodeCommas($res);;
        
    }
        
        
    function getFavouriteBooks() {
                
        
        $member_id = $this->_getMemberID();
        
        
	$res = "";
	if($this->hasProfile()) { 
        $sql = "SELECT favorite_books FROM profiles WHERE member_id = '{$member_id}'    ORDER BY profile_id DESC LIMIT 0,1;";
        
        $res = $this->Database->getOne($sql);
        		
        if (PEAR::isError($res)) {
            die($res->getMessage());
        }
        }
        return $this->_explodeCommas($res);
        
    }
        
    function getFavouriteAuthors() {
                
        
        $member_id = $this->_getMemberID();
        
        
        $res = "";
        
	if($this->hasProfile()) { 
        $sql = "SELECT favorite_authors FROM profiles WHERE member_id = '{$member_id}'    ORDER BY profile_id DESC LIMIT 0,1;";
        
        $res = $this->Database->getOne($sql);
        		
		if (PEAR::isError($res)) {
			die($res->getMessage());
		}
        }
        return $this->_explodeCommas($res);
        
    }
            
            
    function getFavouriteColors() {
                
        
        $member_id = $this->_getMemberID();
        
        
        $res = "";
        
	if($this->hasProfile()) { 
        $sql = "SELECT favorite_colors FROM profiles WHERE member_id = '{$member_id}'    ORDER BY profile_id DESC LIMIT 0,1;";
        
        $res = $this->Database->getOne($sql);
        		
		if (PEAR::isError($res)) {
			die($res->getMessage());
		}
        }
        return $this->_explodeCommas($res);;
        
    }
    
                
                
    function getFavouriteSportsmen() {
                
        
        $member_id = $this->_getMemberID();
        
        
        $res = "";
        
	if($this->hasProfile()) { 
        $sql = "SELECT favorite_sportsmen FROM profiles WHERE member_id = '{$member_id}'    ORDER BY profile_id DESC LIMIT 0,1;";
        
        $res = $this->Database->getOne($sql);
        		
		if (PEAR::isError($res)) {
			die($res->getMessage());
		}
        }
        return $this->_explodeCommas($res);;
        
    }
                    
                    
    function getFavouriteArtists() {
                
        
        $member_id = $this->_getMemberID();
        
        
        $res = "";
        
	if($this->hasProfile()) { 
		
        $sql = "SELECT favorite_artists FROM profiles WHERE member_id = '{$member_id}'    ORDER BY profile_id DESC LIMIT 0,1;";
        
        $res = $this->Database->getOne($sql);
        		
		if (PEAR::isError($res)) {
			die($res->getMessage());
		}
        }
        return $this->_explodeCommas($res);
        
    }
                        
                        
    function getFavouriteCities() {
                
        
        $member_id = $this->_getMemberID();
        
        
        $res = "";
        
	if($this->hasProfile()) { 
		
        $sql = "SELECT favorite_cities FROM profiles WHERE member_id = '{$member_id}'    ORDER BY profile_id DESC LIMIT 0,1;";
        
        $res = $this->Database->getOne($sql);
        		
		if (PEAR::isError($res)) {
			die($res->getMessage());
		}
        }
        return $this->_explodeCommas($res);
                
    }
    
    function _explodeCommas($res) {
        
        if(empty($res))
            return null;
        elseif(strpos($res,',')!==false) {
            $nres = array();
            $tmp = explode(',',$res);
            foreach($tmp as $t)
                if(!empty($t))
                    $nres[] = $t;
            return $nres;
        }
        else
            return array($res);
                
    }
    
    
    
    
    function setNameSurname($group_name,$namesurname) {
        
        $group_name = mysql_real_escape_string($group_name);
        $namesurname = mysql_real_escape_string($namesurname);
        
        $mid = $this->getMembershipID($group_name);
    
        $sql = "UPDATE memberships SET member_name='{$namesurname}' WHERE membership_id='{$mid}';";
    
        $q = $this->Database->query($sql);
        
        if (PEAR::isError($q)) {
			die($q->getMessage());
		}
        
        return $q;
        
    }
    
    
    function setBirthday($group_name,$birthday) {
        
        $group_name = mysql_real_escape_string($group_name);
        $birthday = mysql_real_escape_string($birthday);
        
        // date control
        $a = strtotime($birthday);
        $b = date('m/d/Y',$a);
        
	
	if($birthday!=$b)
            return false;
    
    	
        
        $birthday = date('Y-m-d',$a);
	
	$age = $this->getAge($group_name,$birthday);
	
	
	
	if($age<0||$age>150)
		return false;
	
        
        
        $member_id = $this->_getMemberID();
        $group_id = $this->_getGroupID($group_name);
    
	if(!$this->hasProfile($group_name))
		$this->createProfile($group_name);
		
        $sql = "UPDATE profiles SET birthday='{$birthday}' WHERE member_id='{$member_id}'    ORDER BY profile_id DESC LIMIT 1;";
	
        $q = $this->Database->query($sql);
        
        if (PEAR::isError($q)) {
			die($q->getMessage());
		}
        
        return $q;    
    
    }
    
    
    function setMotto($group_name,$motto) {
        
        $group_name = mysql_real_escape_string($group_name);
        $motto = mysql_real_escape_string($motto);
  
        
        
        $member_id = $this->_getMemberID();
        $group_id = $this->_getGroupID($group_name);
	
	if(!$this->hasProfile($group_name))
		$this->createProfile($group_name);
    
        $sql = "UPDATE profiles SET motto='{$motto}' WHERE member_id='{$member_id}'    ORDER BY profile_id DESC LIMIT 1;";
        
        $q = $this->Database->query($sql);
        
        if (PEAR::isError($q)) {
			die($q->getMessage());
		}
        
        return $q;    
    
    }
    
    
    
    
    function setWebSite($group_name,$website) {
        
        $group_name = mysql_real_escape_string($group_name);
        $website = mysql_real_escape_string($website);
  
        
        
        $member_id = $this->_getMemberID();
        $group_id = $this->_getGroupID($group_name);
	
	if(!$this->hasProfile($group_name))
		$this->createProfile($group_name);
    
        $sql = "UPDATE profiles SET adored_people='{$website}' WHERE member_id='{$member_id}'    ORDER BY profile_id DESC LIMIT 1;";
        
        $q = $this->Database->query($sql);
        
        if (PEAR::isError($q)) {
			die($q->getMessage());
		}
        
        return $q;    
    
    }
    
    
    function setNationality($group_name,$nationality) {
    
        $group_name = mysql_real_escape_string($group_name);
        $nationality = mysql_real_escape_string($nationality);
  
        
        $member_id = $this->_getMemberID();
        $group_id = $this->_getGroupID($group_name);
    
	if(!$this->hasProfile($group_name))
		$this->createProfile($group_name);
	
        $sql = "UPDATE profiles SET nationality='{$nationality}' WHERE member_id='{$member_id}'    ORDER BY profile_id DESC LIMIT 1;";
    
        $q = $this->Database->query($sql);
        
        if (PEAR::isError($q)) {
			die($q->getMessage());
		}
        
        return $q;   
        
    
    }
    
        function setSexe($group_name,$sexe='female') {
    
        $group_name = mysql_real_escape_string($group_name);
        $sexe = mysql_real_escape_string($sexe);
  
        
        
        $member_id = $this->_getMemberID();
        $group_id = $this->_getGroupID($group_name);
    
	if(!$this->hasProfile($group_name))
		$this->createProfile($group_name);
	
	if($sexe!='male'&&$sexe!='female')
		$sexe='female';
	
        $sql = "UPDATE profiles SET sexe='{$sexe}' WHERE member_id='{$member_id}'    ORDER BY profile_id DESC LIMIT 1;";
    
        $q = $this->Database->query($sql);
        
        if (PEAR::isError($q)) {
			die($q->getMessage());
		}
        
        return $q;   
        
    
    }
    
    
    function setEmail($member_name,$email) {
	    
	        
        $member_name = mysql_real_escape_string($member_name);
        $email  = mysql_real_escape_string($email);
  
        if(!isset($_SESSION['valid_user'])||$member_name!=$_SESSION['valid_user'])
            return false;
        
        // format check
        $atom = '[-a-z0-9!#$%&\'*+/=?^_`{|}~]';    // allowed characters for part before "at" character
        $domain = '([a-z0-9]([-a-z0-9]*[a-z0-9]+)?)'; // allowed characters for part after "at" character
        $regex = '^' . $atom . '+' .        // One or more atom characters.
            '(\.' . $atom . '+)*'.              // Followed by zero or more dot separated sets of one or more atom characters.
            '@'.                                // Followed by an "at" character.
            '(' . $domain . '{1,63}\.)+'.        // Followed by one or max 63 domain characters (dot separated).
            $domain . '{2,63}'.                  // Must be followed by one set consisting a period of two
            '$';                                // or max 63 domain characters.
        if(!eregi($regex,$email))
            return false;        
        
        // check for existing email
        $c = $this->Database->getOne("SELECT COUNT(member_id) FROM members WHERE email='{$email}' AND member_login <> '{$member_name}'");
		
		if (PEAR::isError($c)) {
			die($c->getMessage());
		}
		
		if($c!=0) {
            
			return false;
		}
        
        
        
        
	
        $sql = "UPDATE members SET email='{$email}' WHERE member_login='{$member_name}';";
    
        $q = $this->Database->query($sql);
        
        if (PEAR::isError($q)) {
			die($q->getMessage());
		}
        
        return $q;   
	    
    }
    
    
    function setAIM($group_name,$aim) {
	    
	        
        $group_name = mysql_real_escape_string($group_name);
        $aim = mysql_real_escape_string($aim);
  
        
        
        $member_id = $this->_getMemberID();
        $group_id = $this->_getGroupID($group_name);
    
	if(!$this->hasProfile($group_name))
		$this->createProfile($group_name);
	
        $sql = "UPDATE profiles SET contact_aim='{$aim}' WHERE member_id='{$member_id}'    ORDER BY profile_id DESC LIMIT 1;";
    
        $q = $this->Database->query($sql);
        
        if (PEAR::isError($q)) {
			die($q->getMessage());
		}
        
        return $q;   
	    
    }
    
    function setICQ($group_name,$icq) {
	    
	            $group_name = mysql_real_escape_string($group_name);
        $icq = mysql_real_escape_string($icq);
  
        
        
        $member_id = $this->_getMemberID();
        $group_id = $this->_getGroupID($group_name);
    
	if(!$this->hasProfile($group_name))
		$this->createProfile($group_name);
	
        $sql = "UPDATE profiles SET contact_icq='{$icq}' WHERE member_id='{$member_id}'    ORDER BY profile_id DESC LIMIT 1;";
    
        $q = $this->Database->query($sql);
        
        if (PEAR::isError($q)) {
			die($q->getMessage());
		}
		
        
        return $q;   
	    
    }
    
    function setMSN($gname,$msn) {
	    
	            $gname = mysql_real_escape_string($gname);
        $msn = mysql_real_escape_string($msn);
  
        
        
        $member_id = $this->_getMemberID();
        
    
	if(!$this->hasProfile())
		$this->createProfile($gname);
	
        $sql = "UPDATE profiles SET contact_msn='{$msn}' WHERE member_id='{$member_id}'    ORDER BY profile_id DESC LIMIT 1;";
    
        $q = $this->Database->query($sql);
        
        if (PEAR::isError($q)) {
			die($q->getMessage());
		}
        
        return $q;   
	    
    }
    
    function setJabber($gname,$jabber) {
	    
	            $gname = mysql_real_escape_string($gname);
        $jabber = mysql_real_escape_string($jabber);
  
        
        
        $member_id = $this->_getMemberID();
        
    
	if(!$this->hasProfile())
		$this->createProfile($gname);
	
        $sql = "UPDATE profiles SET contact_jabber='{$jabber}' WHERE member_id='{$member_id}'    ORDER BY profile_id DESC LIMIT 1;";
    
        $q = $this->Database->query($sql);
        
        if (PEAR::isError($q)) {
			die($q->getMessage());
		}
        
        return $q;   
	    
    }
    
    function setYahoo($gname,$yahoo) {
	    
	    
	            $gname = mysql_real_escape_string($gname);
        $yahoo = mysql_real_escape_string($yahoo);
  
        
        
        $member_id = $this->_getMemberID();
        
    
	if(!$this->hasProfile($name))
		$this->createProfile($gname);
	
        $sql = "UPDATE profiles SET contact_yahoo='{$yahoo}' WHERE member_id='{$member_id}'    ORDER BY profile_id DESC LIMIT 1;";
    
        $q = $this->Database->query($sql);
        
        if (PEAR::isError($q)) {
			die($q->getMessage());
		}
        
        return $q;   
	    
    }
    
    
                  
    function setTags($gname,$tags) {
	    
	$gname = mysql_real_escape_string($gname);
        $tags = mysql_real_escape_string($tags);
        
        
        $member_id = $this->_getMemberID();
        
    
	if(!$this->hasProfile())
		$this->createProfile($gname);
	
        $sql = "UPDATE profiles SET tags='{$tags}' WHERE member_id='{$member_id}'    ORDER BY profile_id DESC LIMIT 1;";
    
        $q = $this->Database->query($sql);
        
        if (PEAR::isError($q)) {
			die($q->getMessage());
		}
        
        return $q;   
    }      
    
    
    
    
    
    
    
    
    
    function setFavouriteSongs($gname,$fav) {
                
        $gname = mysql_real_escape_string($gname);
        $fav = mysql_real_escape_string($fav);
        
        
        $member_id = $this->_getMemberID();
        
    
        if(!$this->hasProfile())
            $this->createProfile($gname);
	
        $sql = "UPDATE profiles SET favorite_songs='{$fav}' WHERE member_id='{$mid}'    ORDER BY profile_id DESC LIMIT 1;";
    
        $q = $this->Database->query($sql);
        
        if (PEAR::isError($q)) {
            die($q->getMessage());
        }
        
        return $q;   
        
    }
    
    
    function setFavouriteSingers($gname,$fav) {
                
        $gname = mysql_real_escape_string($gname);
        $fav = mysql_real_escape_string($fav);
        
        
        $member_id = $this->_getMemberID();
        
    
        if(!$this->hasProfile())
            $this->createProfile($gname);
	
        $sql = "UPDATE profiles SET favorite_singers='{$fav}' WHERE member_id='{$member_id}'    ORDER BY profile_id DESC LIMIT 1;";
    
        $q = $this->Database->query($sql);
        
        if (PEAR::isError($q)) {
            die($q->getMessage());
        }
        
        return $q;   
        
    }
    
    
    function setFavouriteMovies($gname,$fav) {
                
        $gname = mysql_real_escape_string($gname);
        $fav = mysql_real_escape_string($fav);
        
        
        $member_id = $this->_getMemberID();
        
    
        if(!$this->hasProfile())
            $this->createProfile($gname);
	
        $sql = "UPDATE profiles SET favorite_movies='{$fav}' WHERE member_id='{$member_id}'    ORDER BY profile_id DESC LIMIT 1;";
    
        $q = $this->Database->query($sql);
        
        if (PEAR::isError($q)) {
            die($q->getMessage());
        }
        
        return $q;   
        
    }
    
    function setFavouriteActors($gname,$fav) {
                
        $gname = mysql_real_escape_string($gname);
        $fav = mysql_real_escape_string($fav);
        
        
        $member_id = $this->_getMemberID();
        
    
        if(!$this->hasProfile())
            $this->createProfile($gname);
	
        $sql = "UPDATE profiles SET favorite_actors='{$fav}' WHERE member_id='{$member_id}'    ORDER BY profile_id DESC LIMIT 1;";
    
        $q = $this->Database->query($sql);
        
        if (PEAR::isError($q)) {
            die($q->getMessage());
        }
        
        return $q;   
        
    }
        
        
    function setFavouriteBooks($gname,$fav) {
                
        $gname = mysql_real_escape_string($gname);
        $fav = mysql_real_escape_string($fav);
        
        
        $member_id = $this->_getMemberID();
        
    
        if(!$this->hasProfile())
            $this->createProfile($gname);
	
        $sql = "UPDATE profiles SET favorite_books='{$fav}' WHERE member_id='{$member_id}'    ORDER BY profile_id DESC LIMIT 1;";
    
        $q = $this->Database->query($sql);
        
        if (PEAR::isError($q)) {
            die($q->getMessage());
        }
        
        return $q;   
        
    }
        
    function setFavouriteAuthors($gname,$fav) {
                
        $gname = mysql_real_escape_string($gname);
        $fav = mysql_real_escape_string($fav);
        
        
        $member_id = $this->_getMemberID();
        
    
        if(!$this->hasProfile())
            $this->createProfile($gname);
	
        $sql = "UPDATE profiles SET favorite_authors='{$fav}' WHERE member_id='{$member_id}'    ORDER BY profile_id DESC LIMIT 1;";
    
        $q = $this->Database->query($sql);
        
        if (PEAR::isError($q)) {
            die($q->getMessage());
        }
        
        return $q;   
        
    }
            
            
    function setFavouriteColors($gname,$fav) {
                
        $gname = mysql_real_escape_string($gname);
        $fav = mysql_real_escape_string($fav);
        
        
        $member_id = $this->_getMemberID();
        
    
        if(!$this->hasProfile())
            $this->createProfile($gname);
	
        $sql = "UPDATE profiles SET favorite_colors='{$fav}' WHERE member_id='{$mid}'    ORDER BY profile_id DESC LIMIT 1;";
    
        $q = $this->Database->query($sql);
        
        if (PEAR::isError($q)) {
            die($q->getMessage());
        }
        
        return $q;   
        
    }
    
                
                
    function setFavouriteSportsmen($gname,$fav) {
                
        $gname = mysql_real_escape_string($gname);
        $fav = mysql_real_escape_string($fav);
        
        
        $member_id = $this->_getMemberID();
        
    
        if(!$this->hasProfile())
            $this->createProfile($gname);
	
        $sql = "UPDATE profiles SET favorite_sportsmen='{$fav}' WHERE member_id='{$member_id}'    ORDER BY profile_id DESC LIMIT 1;";
    
        $q = $this->Database->query($sql);
        
        if (PEAR::isError($q)) {
            die($q->getMessage());
        }
        
        return $q;   
        
    }
                    
                    
    function setFavouriteArtists($gname,$fav) {
                
        $gname = mysql_real_escape_string($gname);
        $fav = mysql_real_escape_string($fav);
        
        
        $member_id = $this->_getMemberID();
        
    
        if(!$this->hasProfile())
            $this->createProfile($gname);
	
        $sql = "UPDATE profiles SET favorite_artists='{$fav}' WHERE member_id='{$member_id}'    ORDER BY profile_id DESC LIMIT 1;";
    
        $q = $this->Database->query($sql);
        
        if (PEAR::isError($q)) {
            die($q->getMessage());
        }
        
        return $q;   
        
    }
                        
                        
    function setFavouriteCities($gname,$fav) {
                
        $gname = mysql_real_escape_string($gname);
        $fav = mysql_real_escape_string($fav);
        
        
        $member_id = $this->_getMemberID();
        
    
        if(!$this->hasProfile())
            $this->createProfile($gname);
	
        $sql = "UPDATE profiles SET favorite_cities='{$fav}' WHERE member_id='{$member_id}'    ORDER BY profile_id DESC LIMIT 1;";
    
        $q = $this->Database->query($sql);
        
        if (PEAR::isError($q)) {
            die($q->getMessage());
        }
        
        return $q;   
                
    }
    
    
    /*****************
    ***********
    ***** we may add a new class for these functions like
    *** Watchlist.class.php
    ***********
    ********************/
    
    
    function isWatching($gname,$wid) {
	    
	    $memberid = $this->getID();
	    $gid = $this->_getGroupID($gname);
	    
	    $sql = "SELECT COUNT(watch_id) FROM watchlist WHERE group_id='{$gid}' AND subject_id='{$memberid}' AND object_id='{$wid}';";
	    
	    $res = $this->Database->getOne($sql);
	    
	    if (PEAR::isError($res)) {
		    die($res->getMessage());
	    }
	    
	    if($res==0)
		    return false;
	    else
	    	return true;
	    
    }
    
    function addToWatchlist($gname,$wid) {
	    
	    $memberid = $this->getID();
	    $gid = $this->_getGroupID($gname);
	    
	    $sql = 'INSERT INTO `watchlist` (`watch_id`, `group_id`, `subject_id`, `object_id`, `add_date`) VALUES (NULL, \''.$gid.'\', \''.$memberid.'\', \''.$wid.'\', NOW());';
    
	    $res = $this->Database->query($sql);
	    
	    if (PEAR::isError($res)) {
		    die($res->getMessage());
	    }
	    
	    return $res;
    
    }
    
    function getMMSPin($gid) {
        $memberid = $this->getID();
        $sql = "SELECT pin FROM pins WHERE member_id='{$memberid}' AND group_id='{$gid}';";
        
        $res = $this->Database->getOne($sql);
	    
	    if (PEAR::isError($res)) {
		    die($res->getMessage());
	    }
        
        if(!empty($res)) {
            return $res;            
        }
        else {
         
            // no pin yet; create a new one
            $npin = rand(1000,9999);
            
            $sql = 'INSERT INTO `pins` (`pin_id`, `member_id`, `group_id`, `pin`) VALUES (NULL, \''.$memberid.'\', \''.$gid.'\', \''.$npin.'\');';
            
            $res = $this->Database->query($sql);
	    
            if (PEAR::isError($res)) {
                die($res->getMessage());
            }
            
            return $npin;
            
            
        }
        
    }
    
    
    
    /*****************
    ***********
    ***********
    ********************/
    
    
    
    function setYahooMigrator() {
        
        $memberid = $this->getID();
        $sql = 'INSERT INTO `migrators_yahoo` (`migrator_id`, `member_id`, `migration_date`) VALUES (NULL, \''.$memberid.'\', NOW());';

	    $res = $this->Database->query($sql);
	    
	    if (PEAR::isError($res)) {
		    die($res->getMessage());
	    }
	    
	    return $res;
        
        
    }
    
    function deleteYahooMigrator() {
        
        $memberid = $this->getID();
        $sql = 'DELETE FROM `migrators_yahoo` WHERE member_id = \''.$memberid.'\';';
        $sql .= 'INSERT INTO `migrators_yahoo_deleted` (`migrator_id`, `member_id`, `migration_date`) VALUES (NULL, \''.$memberid.'\', NOW());';
        
	    $res = $this->Database->query($sql);
	    
	    if (PEAR::isError($res)) {
		    die($res->getMessage());
	    }
	    
	    return $res;
        
        
    }
    
    
    /**
     * Get username by membership_id
     *
     * @access protected
     * @param int $membership_id
     * @return string username
     */
    function _getUsernameByMembershipID($membership_id) {

    
    _filter_var($membership_id);
    
    $sql = "SELECT member_id FROM memberships WHERE membership_id='{$membership_id}';";
    
    $member_id = $this->Database->getOne($sql);
    if (PEAR::isError($member_id)) {
        die($member_id->getMessage());
    }
    
    $sql = "SELECT member_login FROM members WHERE member_id='{$member_id}';";
    $username = $this->Database->getOne($sql);
    if (PEAR::isError($username)) {
        die($username->getMessage());
    }
    
    return $username;
    
    
	}
	
	
	/**
	 * Sorts per name
	 *
	 * @return unknown
	 */
	function getMemberships() {

		$memberships = array();
		$membership_names = array();
		$membership_ids = array();
		
		$userid = $this->_getMemberID();
		
		$sql = "SELECT membership_id,membership_name FROM memberships WHERE member_id='{$userid}';";
		
		$res = $this->Database->getAll($sql,array(),2);
		
		if (PEAR::isError($res)) {
			die($res->getMessage());
		}
		
		foreach ($res as $i=>$r) {
			
			if(empty($r['membership_name'])) {
				$memberships[$i]['name'] = "Profile #{$r['membership_id']}";	
			}
			else {
				$memberships[$i]['name'] = $r['membership_name'];
			}
			
			$memberships[$i]['id'] = $r['membership_id'];
			
			$membership_ids[$i] = $r['membership_id'];
			$membership_names[$i] = $r['membership_name'];
						
		}
		
		array_multisort($membership_names, SORT_ASC, SORT_STRING, $membership_ids, SORT_ASC, SORT_NUMERIC, $memberships);
		
		return $memberships;
		
	}
	
	
	function _getMemberID() {
		
		if($this->MemberID!=null) {
			return $this->MemberID;
		}
		else {
		
			$res = & $this->Database->getOne("SELECT member_id FROM members WHERE member_login='{$this->Username}'");
			
			if (PEAR::isError($res)) {
				die($res->getMessage());
			}
			
			$this->MemberID = $res;
			
			return $res;
		}
		
	}

    
    
	function getNationalitySelectOptions($gname,$with_default=true) {
		
		
		$ret = <<<EOS

		
			<option value="AF">Afghanistan</option>
            <option value="AL">Albania</option>
            <option value="DZ">Algeria</option>
            <option value="AS">American Samoa</option>
            <option value="AD">Andorra</option>
            <option value="AO">Angola</option>
            <option value="AI">Anguilla</option>
            <option value="AQ">Antarctica</option>
            <option value="AG">Antigua and Barbuda</option>
            <option value="AR">Argentina</option>
            <option value="AM">Armenia</option>
            <option value="AW">Aruba</option>
            <option value="AU">Australia</option>
            <option value="AT">Austria</option>
            <option value="AZ">Azerbaijan</option>
            <option value="AP">Azores</option>
            <option value="BS">Bahamas</option>
            <option value="BH">Bahrain</option>
            <option value="BD">Bangladesh</option>
            <option value="BB">Barbados</option>
            <option value="BY">Belarus</option>
            <option value="BE">Belgium</option>
            <option value="BZ">Belize</option>
            <option value="BJ">Benin</option>
            <option value="BM">Bermuda</option>
            <option value="BT">Bhutan</option>
            <option value="BO">Bolivia</option>
            <option value="BA">Bosnia And Herzegowina</option>
            <option value="XB">Bosnia-Herzegovina</option>
            <option value="BW">Botswana</option>
            <option value="BV">Bouvet Island</option>
            <option value="BR">Brazil</option>
            <option value="IO">British Indian Ocean Territory</option>
            <option value="VG">British Virgin Islands</option>
            <option value="BN">Brunei Darussalam</option>
            <option value="BG">Bulgaria</option>
            <option value="BF">Burkina Faso</option>
            <option value="BI">Burundi</option>
            <option value="KH">Cambodia</option>
            <option value="CM">Cameroon</option>
            <option value="CA">Canada</option>
            <option value="CV">Cape Verde</option>
            <option value="KY">Cayman Islands</option>
            <option value="CF">Central African Republic</option>
            <option value="TD">Chad</option>
            <option value="CL">Chile</option>
            <option value="CN">China</option>
            <option value="CX">Christmas Island</option>
            <option value="CC">Cocos (Keeling) Islands</option>
            <option value="CO">Colombia</option>
            <option value="KM">Comoros</option>
            <option value="CG">Congo</option>
            <option value="CD">Congo, The Democratic Republic</option>
            <option value="CK">Cook Islands</option>
            <option value="XE">Corsica</option>
            <option value="CR">Costa Rica</option>
            <option value="CI">Cote d\\'Ivoire (Ivory Coast)</option>
            <option value="HR">Croatia</option>
            <option value="CU">Cuba</option>
            <option value="CY">Cyprus</option>
            <option value="CZ">Czech Republic</option>
            <option value="DK">Denmark</option>
            <option value="DJ">Djibouti</option>
            <option value="DM">Dominica</option>
            <option value="DO">Dominican Republic</option>
            <option value="TP">East Timor</option>
            <option value="EC">Ecuador</option>
            <option value="EG">Egypt</option>
            <option value="SV">El Salvador</option>
            <option value="GQ">Equatorial Guinea</option>
            <option value="ER">Eritrea</option>
            <option value="EE">Estonia</option>
            <option value="ET">Ethiopia</option>
            <option value="FK">Falkland Islands (Malvinas)</option>
            <option value="FO">Faroe Islands</option>
            <option value="FJ">Fiji</option>
            <option value="FI">Finland</option>
            <option value="FR">France</option>
            <option value="GF">French Guiana</option>
            <option value="PF">French Polynesia</option>
            <option value="TA">French Polynesia (Tahiti)</option>
            <option value="TF">French Southern Territories</option>
            <option value="GA">Gabon</option>
            <option value="GM">Gambia</option>
            <option value="GE">Georgia</option>
            <option value="DE">Germany</option>
            <option value="GH">Ghana</option>
            <option value="GI">Gibraltar</option>
            <option value="GR">Greece</option>
            <option value="GL">Greenland</option>
            <option value="GD">Grenada</option>
            <option value="GP">Guadeloupe</option>
            <option value="GU">Guam</option>
            <option value="GT">Guatemala</option>
            <option value="GN">Guinea</option>
            <option value="GW">Guinea-Bissau</option>
            <option value="GY">Guyana</option>
            <option value="HT">Haiti</option>
            <option value="HM">Heard And Mc Donald Islands</option>
            <option value="VA">Holy See (Vatican City State)</option>
            <option value="HN">Honduras</option>
            <option value="HK">Hong Kong</option>
            <option value="HU">Hungary</option>
            <option value="IS">Iceland</option>
            <option value="IN">India</option>
            <option value="ID">Indonesia</option>
            <option value="IR">Iran</option>
            <option value="IQ">Iraq</option>
            <option value="IE">Ireland</option>
            <option value="EI">Ireland (Eire)</option>
            <option value="IL">Israel</option>
            <option value="IT">Italy</option>
            <option value="JM">Jamaica</option>
            <option value="JP">Japan</option>
            <option value="JO">Jordan</option>
            <option value="KZ">Kazakhstan</option>
            <option value="KE">Kenya</option>
            <option value="KI">Kiribati</option>
            <option value="KP">Korea, Democratic People Repub</option>
            <option value="KW">Kuwait</option>
            <option value="KG">Kyrgyzstan</option>
            <option value="LA">Laos</option>
            <option value="LV">Latvia</option>
            <option value="LB">Lebanon</option>
            <option value="LS">Lesotho</option>
            <option value="LR">Liberia</option>
            <option value="LY">Libya</option>
            <option value="LI">Liechtenstein</option>
            <option value="LT">Lithuania</option>
            <option value="LU">Luxembourg</option>
            <option value="MO">Macao</option>
            <option value="MK">Macedonia</option>
            <option value="MG">Madagascar</option>
            <option value="ME">Madeira Islands</option>
            <option value="MW">Malawi</option>
            <option value="MY">Malaysia</option>
            <option value="MV">Maldives</option>
            <option value="ML">Mali</option>
            <option value="MT">Malta</option>
            <option value="MH">Marshall Islands</option>
            <option value="MQ">Martinique</option>
            <option value="MR">Mauritania</option>
            <option value="MU">Mauritius</option>
            <option value="YT">Mayotte</option>
            <option value="MX">Mexico</option>
            <option value="FM">Micronesia, Federated States Of</option>
            <option value="MD">Moldova, Republic Of</option>
            <option value="MC">Monaco</option>
            <option value="MN">Mongolia</option>
            <option value="MS">Montserrat</option>
            <option value="MA">Morocco</option>
            <option value="MZ">Mozambique</option>
            <option value="MM">Myanmar (Burma)</option>
            <option value="NA">Namibia</option>
            <option value="NR">Nauru</option>
            <option value="NP">Nepal</option>
            <option value="NL">Netherlands</option>
            <option value="AN">Netherlands Antilles</option>
            <option value="NC">New Caledonia</option>
            <option value="NZ">New Zealand</option>
            <option value="NI">Nicaragua</option>
            <option value="NE">Niger</option>
            <option value="NG">Nigeria</option>
            <option value="NU">Niue</option>
            <option value="NF">Norfolk Island</option>
            <option value="MP">Northern Mariana Islands</option>
            <option value="NO">Norway</option>
            <option value="OM">Oman</option>
            <option value="PK">Pakistan</option>
            <option value="PW">Palau</option>
            <option value="PS">Palestinian Territory, Occupied</option>
            <option value="PA">Panama</option>
            <option value="PG">Papua New Guinea</option>
            <option value="PY">Paraguay</option>
            <option value="PE">Peru</option>
            <option value="PH">Philippines</option>
            <option value="PN">Pitcairn</option>
            <option value="PL">Poland</option>
            <option value="PT">Portugal</option>
            <option value="PR">Puerto Rico</option>
            <option value="QA">Qatar</option>
            <option value="RE">Reunion</option>
            <option value="RO">Romania</option>
            <option value="RU">Russian Federation</option>
            <option value="RW">Rwanda</option>
            <option value="KN">Saint Kitts And Nevis</option>
            <option value="SM">San Marino</option>
            <option value="ST">Sao Tome and Principe</option>
            <option value="SA">Saudi Arabia</option>
            <option value="SN">Senegal</option>
            <option value="XS">Serbia-Montenegro</option>
            <option value="SC">Seychelles</option>
            <option value="SL">Sierra Leone</option>
            <option value="SG">Singapore</option>
            <option value="SK">Slovak Republic</option>
            <option value="SI">Slovenia</option>
            <option value="SB">Solomon Islands</option>
            <option value="SO">Somalia</option>
            <option value="ZA">South Africa</option>
            <option value="GS">South Georgia And The South Sand</option>
            <option value="KR">South Korea</option>
            <option value="ES">Spain</option>
            <option value="LK">Sri Lanka</option>
            <option value="NV">St. Christopher and Nevis</option>
            <option value="SH">St. Helena</option>
            <option value="LC">St. Lucia</option>
            <option value="PM">St. Pierre and Miquelon</option>
            <option value="VC">St. Vincent and the Grenadines</option>
            <option value="SD">Sudan</option>
            <option value="SR">Suriname</option>
            <option value="SJ">Svalbard And Jan Mayen Islands</option>
            <option value="SZ">Swaziland</option>
            <option value="SE">Sweden</option>
            <option value="CH">Switzerland</option>
            <option value="SY">Syrian Arab Republic</option>
            <option value="TW">Taiwan</option>
            <option value="TJ">Tajikistan</option>
            <option value="TZ">Tanzania</option>
            <option value="TH">Thailand</option>
            <option value="TG">Togo</option>
            <option value="TK">Tokelau</option>
            <option value="TO">Tonga</option>
            <option value="TT">Trinidad and Tobago</option>
            <option value="XU">Tristan da Cunha</option>
            <option value="TN">Tunisia</option>
            <option value="TR">Turkey</option>
            <option value="TM">Turkmenistan</option>
            <option value="TC">Turks and Caicos Islands</option>
            <option value="TV">Tuvalu</option>
            <option value="UG">Uganda</option>
            <option value="UA">Ukraine</option>
            <option value="AE">United Arab Emirates</option>
            <option value="UK">United Kingdom</option>
            <option value="GB">Great Britain</option>
            <option value="US">United States</option>
            <option value="UM">United States Minor Outlying Isl</option>
            <option value="UY">Uruguay</option>
            <option value="UZ">Uzbekistan</option>
            <option value="VU">Vanuatu</option>
            <option value="XV">Vatican City</option>
            <option value="VE">Venezuela</option>
            <option value="VN">Vietnam</option>
            <option value="VI">Virgin Islands (U.S.)</option>
            <option value="WF">Wallis and Furuna Islands</option>
            <option value="EH">Western Sahara</option>
            <option value="WS">Western Samoa</option>
            <option value="YE">Yemen</option>
            <option value="YU">Yugoslavia</option>
            <option value="ZR">Zaire</option>
            <option value="ZM">Zambia</option>
            <option value="ZW">Zimbabwe</option>	
		
		
		
EOS;

		if($with_default) {
			$nat = $this->getNationality($gname);
			$ret = str_replace('value="'.strtoupper($nat).'"','value="'.strtoupper($nat).'" selected', $ret);
		}
		else {
			// US is the default default
			$ret = str_replace('value="US"','value="US" selected', $ret);	
		}
		
		return $ret;
		
	}
	
	
	function getSexeSelectOptions($gname,$with_default=true) {
		
		$ret = "";
		
		$sexe = $this->getSexe($gname);
		
		if($with_default && $sexe=="female")
			$ret .= '<option value="female" selected>Female</option>';
		else 
			$ret .= '<option value="female">Female</option>';
		                
			
			
		if($with_default && $sexe=="male")
			$ret .= '<option value="male" selected>Male</option>';
		else 
			$ret .= '<option value="male">Male</option>';
			
			
			return $ret;
			
	}
	
	

	function getSubscribedGroups() {
	

		$q = $this->Database->getAll("SELECT membership_id, gnippet_ids FROM memberships WHERE member_id='".$this->getID()."'", array(), 2 /** assoc */);
		
		if (PEAR::isError($q)) {
			die($q->getMessage());
		}
		
		if(sizeof($q)==0) {
			return array();
		}
		else { 
			$allgnippets = array();
			$gnippet_ids = array();
			$membership_id = '';
			
			foreach($q as $qu) {
			
				$gnippet_ids = explode(',',$qu['gnippet_ids']);
				
				// unset empty ones.
				foreach($gnippet_ids as $i=>$g) {
					if(empty($g)) {
						unset($gnippet_ids[$i]);
					}
				}
				
				$membership_id = $qu['membership_id'];
				
				foreach($gnippet_ids as $gid) {
				
					$q2 = $this->Database->getRow("SELECT gnippet_name, gnippet_title FROM gnippets WHERE gnippet_id='{$gid}'", array(), 2 /*assoc */);
			
					if (PEAR::isError($q2)) {
						die($q2->getMessage());
					}
					
					$gnippet_title = $q2['gnippet_title'];
					$gnippet_name = $q2['gnippet_name'];
					
					$allgnippets[] = array('gnippet_id'=>$gid, 'gnippet_name'=>$gnippet_name, 'gnippet_title'=>$gnippet_title, 'profile_id'=>$membership_id);
			
				}
			
			
			}
			
			return $allgnippets;
			
		}
	
	}
	
	
	function getWallPostsNum($mid=null) {
		
		if($mid==null)
			$mid = $this->getID();
			
			
		// $sql = 'SELECT COUNT(`comment_id`) FROM `comments` WHERE `member_id` = ? AND `gnippet_id` = ? `comment`, `added_on`) VALUES (NULL, ?, ?, ?, ?, NOW());';
		
		$allc = 0;
		$newc = 0;
		
		$sql = 'SELECT COUNT(`comment_id`) FROM `comments` WHERE `member_id` = ?';
		$all = & $this->Database->getOne($sql,array($mid));

		if (!PEAR::isError($all)&&$all>0) {
			$allc = $all;
			
			$sql = 'SELECT COUNT(`comment_id`) FROM `comments` WHERE `member_id` = ? AND `added_on` >= DATE_SUB(CURDATE(), INTERVAL 7 DAYS)';
			$new = & $this->Database->getOne($sql,array($mid));
			
			if (!PEAR::isError($new)&&$new>0) {
				$newc = $new;
			}
			
		}
		
		if($allc!=0&&$allc==$newc)
			$newc = "all";

		$this->tempActivityRank += $allc;
			
		return array($allc,$newc);
	}
	
	
}

?>