<?php

//if(!class_exists('DB'))
//	include_once dirname(__FILE__).'/DB/DB.php';
require_once 'DB.php';

class Group {

	var $Database;

	var $MAP = 100;
	var $INTRO = 101;

	function Group() {

		global $GlobalDatabase;


		if(isset($GlobalDatabase)) {

			$this->Database = $GlobalDatabase;
		}
		else {

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

	}




	/**
     * looks whether the given user has a pending
     * membership request for this group
     * @username user that awaits or not membership request
     * @return bool true=> if user really waits a membership request
     */
	function pendingMembershipRequest($username) {

		_filter_var($username);
		$uid = _getMemberID($username);

		/**
		 * first let's whether he has already made a request
		 * to get in this group
		 */
		$res = & $this->Database->getOne("SELECT request_id FROM membership_requests  WHERE member_id='{$uid}'");

		if (PEAR::isError($res)) {
			die($res->getMessage());
		}

		if(empty($res))
		return false;
		else
		return true;


	}


		function getMembershipRequests() {
        
        /**
         * only admins can get it
         */
        //$this->_makeAdminControl();
        
	
		$to_return = array();
	
		$res = & $this->Database->getAll("SELECT member_id, comment FROM membership_requests", array(), 2 /*ASSOC*/);
		
		if (PEAR::isError($res)) {
			die($res->getMessage());
		}
		
		foreach($res as $r) {
		
			$email = & $this->Database->getOne("SELECT email FROM members WHERE member_id='{$r['member_id']}'");
			
			if (PEAR::isError($email)) {
				die($email->getMessage());
			}
			
			_filter_res_var($r['member_id']);
			_filter_res_var($email);
			_filter_res_var($r['comment']);
			
			$to_return[] = array('id'=>$r['member_id'],  'email'=>$email, 'comment'=>$r['comment'] );
		
		}
		
		
		return $to_return;
	
	}
	
	
	/**
	 * This function should word atomic.
	 * It requires .... feature in database.
	 * However MySQL 3.x does not provide it!
	 *
	 * To make a membership request, one should be 
	 * a member first.
	 *
	 * User may use their existing profiles or create
	 * a new profile when subscribing to new gnippets.
	 */
	function acceptMembershipRequest($mid,$sp=false) {
        
		_filter_var($mid);
		
		$gids = ''; // gnippet_ids to update
		$res = & $this->Database->getRow("SELECT member_name, profile_id FROM membership_requests  WHERE member_id='{$mid}'", array(), 2 /*assoc*/);
		
		if (PEAR::isError($res)) {
			die($res->getMessage());
		}
		
      
		if(sizeof($res)!=2) {
			die('Error No 41');
		}
		else {
			
			if((!empty($res['member_name'])&&empty($res['profile_id']))) {
				
				$n = $res['member_name'];
				$gids = ','.$this->GroupID.',';
				
				$q = & $this->Database->query("INSERT INTO memberships (membership_id, member_id, subscribed_on, member_name, website, blog, flickr, delicious, avatar, membership_name) VALUES (NULL,'{$mid}', NOW(),'{$n}','','','','','12', '')");
				
				if(PEAR::isError($q)) {
					die($q->getMessage());
				}
				
				if(!$q) {
					return false;
				}
				else {
				
					$q = & $this->Database->query("DELETE FROM membership_requests WHERE member_id='{$mid}'");
					
					if(PEAR::isError($q)) {
						die($q->getMessage());
					}
					
	
				
				}
			
			}
			elseif(!empty($res['profile_id'])&&empty($res['member_name'])) {
			
			
				$gids = ','.$this->GroupID.',';
			
				$rr = & $this->Database->getRow("SELECT * FROM memberships  WHERE membership_id='{$res['profile_id']}'",array(),2);
	
		
				if (PEAR::isError($rr)) {
					die($rrr->getMessage());
				}
				
				
				$q = & $this->Database->query("INSERT INTO memberships (membership_id, member_id, subscribed_on, member_name, website, blog, flickr, delicious, avatar, membership_name) VALUES (NULL,'{$rr["member_id"]}',NOW(),'{$rr["member_name"]}','{$rr["website"]}','{$rr["blog"]}','{$rr["flickr"]}','{$rr["delicious"]}','{$rr["avatar"]}','{$rr["membership_name"]}')");
				
				if (PEAR::isError($q)) {
					die($q->getMessage());
				}
				
				if(!$q)
					return false;
				else {
					$q = & $this->Database->query("DELETE FROM membership_requests WHERE AND member_id='{$mid}'");
					
					if(PEAR::isError($q)) {
						die($q->getMessage());
					}
					
	
				}
			}
			// exactly the same as 1st condition; 
			// but we can't evaluate everything wehen this is somewhere else
			// this should be reviewed and restructured
			// to restructure first view..
			// changeProfile function; it adds true to $sp
			elseif($sp) {  
                
				$n = '';
							
				$q = & $this->Database->query("INSERT INTO memberships (membership_id, member_id, subscribed_on, member_name, website, blog, flickr, delicious, avatar, membership_name) VALUES (NULL,'{$mid}', NOW(),'{$n}','','','','','12', '')");
				
				if(PEAR::isError($q)) {
					die($q->getMessage());
				}
				
				
				if(PEAR::isError($q)) {
					die($q->getMessage());
				}
				
				if(!$q) {
					return false;
				}
				else {
				
					$q = & $this->Database->query("DELETE FROM membership_requests WHERE member_id='{$mid}'");
					
					if(PEAR::isError($q)) {
						die($q->getMessage());
					}
					
	
				
				}
			}
            /**
             * We thought that fist requests go there..
             * but this was faulty..
             * Yes, if when user signs up, we don't create a membership for him
             * he drops to here in his/her first group 
             * But we fixed this and all the commented out codes below are
             * useless for now
             */
            // first request
			else {
                
		
                
                die("Error No 14"); // not possible
                
			}
		}
		
		if($q) {
			
			$uname = _getMemberUsername($mid);
			
			$u = new User($uname);
			$u->createProfile($this->GroupName);
			
			if(isset($_SESSION['valid_user'])&&$_SESSION['valid_user']==$uname)
				updateUserLocations();
			
			return true;
		}
		else {
			return false;
		}
		
	}
	
	function rejectMembershipRequest($mid) {
        
        //$this->_makeAdminControl();
	
		_filter_var($mid);
	
		$res = & $this->Database->query("DELETE FROM membership_requests WHERE member_id='{$mid}'");
		
		if (PEAR::isError($res)) {
			die($res->getMessage());
		}
		
		return $res;
	
	
	}
	
	
	
	function searchMembers($q) {
		return $this->getMembers(false,false,-1,-1,null,$q);
	}
	
	
	/**
	 * returns membership_id, member_name
	 * and member_id // added now; delete if creates a problem
	 * as an array
     * admin mode says that:
     * we shouldn't assume that there is an admin in the group
     * because the group may be just created; so there may be no admin
     * yet...
     *
     * $exclude_admin is for situations in which we don't want admins
     * to be listed in the result
     *
     * @param admin_mode false by default; assume no admin yet
     * @param exclude_admin false by default; don't add admins into the list
     * @return array members
	 */
	function getMembers($admin_mode=false, $exclude_admins=false, $start_from=-1, $ends=-1, $rank=null,$q=null) {
	
        // we'll return this.
		$retres = array();
        $j=0; // to count $retres
		
        
		if($q==null) {
	        if($start_from==-1||$ends==-1) {
	        	if($rank=="activity") {
	        		$sql = "SELECT membership_id, member_name, member_id FROM memberships";
					$res = & $this->Database->getAll($sql, array(), 2 /*assoc*/);
	        	}
	        	else {
	        		$sql = "SELECT membership_id, member_name, member_id FROM memberships";
					$res = & $this->Database->getAll($sql, array(), 2 /*assoc*/);
	        	}
	        }
	        else {
	        	if($rank=="activity") {
	        		$sql = "SELECT membership_id, member_name, member_id FROM memberships LIMIT ?, ?";
					$res = & $this->Database->getAll($sql, array($start_from,$ends), 2 /*assoc*/);
	        	}
	        	elseif($rank=="date") {
					$sql = "SELECT membership_id, member_name, member_id FROM memberships ORDER BY membership_id DESC LIMIT ?, ?";
					$res = & $this->Database->getAll($sql, array($start_from,$ends), 2 /*assoc*/);	        		
				}
	        	else {
	        		$sql = "SELECT membership_id, member_name, member_id FROM memberships LIMIT ?, ?";
					$res = & $this->Database->getAll($sql, array($start_from,$ends), 2 /*assoc*/);
	        	}
	        }
		}
		else {
			$sql = "SELECT membership_id, member_name, member_id FROM memberships WHERE MATCH(`member_name`) AGAINST (?)";
			$res = & $this->Database->getAll($sql, array($q), 2 /*assoc*/);
			/*if (PEAR::isError($res)||!is_array($res)||sizeof($res)==0) {
				$sql = "SELECT membership_id, member_name, member_id FROM memberships WHERE gnippet_ids = ? AND MATCH('member_name') AGAINST (?)";
			}*/
		}
		
		if (PEAR::isError($res)) {
			die($res->getMessage());
		}
		
		if(!$admin_mode&&sizeof($res)==0) {
			//die('Error No 42'); // there should have been the admin at least!
		}
		else {
            
            // we have a few things to do here
            // 1. filter res var...
            // 2. verify $exclude_admin condition
			foreach($res as $i=>$r) {
				
                
                
                if($r['member_id']==0)
                	continue;
                
                if($exclude_admins) {
                    
                    if($this->isAdmin($r['member_id'])) { /*member_id*/
                       continue;
                    }
                    else {
                        $retres[$j] = $r;
                        $j++;
                    }
                }
                else {
                    $retres[$j] = $r;
                    $j++;
                }
                
            }
            

		}
        
        return $retres;
	
	}
	
	
	
	function getMemberEmails($except="") {
		$except = strtolower($except);
		$emails = array();
		$ms = $this->getMembers();
		foreach($ms as $m) {
			$uname = getUsernameByMembershipID($this->Database,$m['membership_id']);
			$u = new User($uname);
			$uname = strtolower($uname);
			if($uname!=$except) 
				$emails[] = $u->getEmail();
		}
		return $emails;
	}
	
	function getMemberEmailsForForum($except="") {
		$remails = array();
		$emails = $this->getMemberEmails($except);
		foreach ($emails as $email) {
			$sql = "SELECT COUNT(`id`) FROM `noemail_forum` WHERE `email` = ?";
			$res = $this->Database->getOne($sql,array($email));
			if(!PEAR::isError($res)&&$res>0) {
				continue;
			}
			else {
				$remails[] = $email;
			}
		}
		return $remails;
	}
	
	
	
	function isAdmin($userid) {
	

		
		/**
		 * is_confirmed='Y' dropped
		 * because it was creating problem in the new design
		 * a membership contains lots of gnippets
		 * you cannot se a unique is_confirmed and is_admin
		 * for all of them.
		 * SOLUTION: 
		 * drop these fields in memberships table
		 * add 2 new tables:
		 * membership_requests and admins
		 * All requests come to membership_requests first
		 * if they got accepted, then written into memberships table
		 * In the same way; admins are set in another table in relation
		 * with not membership_id but member_id
		 */
        
        
		$res = & $this->Database->getOne("SELECT COUNT(admin_id) FROM admins WHERE member_id='{$userid}'");
		
		if (PEAR::isError($res)) {
			
            // die($res->getMessage());
            // no need to die here
            return false;
        
        }
		
        
		if($res==1)
            return true;
        else
            return false;
	
	}
    
    
	function unsetAdmin($userid) {
		if($this->hasMember($userid)&&$this->isAdmin($userid)) {
			$sql = "DELETE FROM admins WHERE member_id = ? LIMIT 1";
			$this->Database->query($sql,array($userid));
			if (PEAR::isError($res)) {
                return false;
            }
            return true;
		}
		else {
			return false;
		}
	}
	
    
	function setAdmin($userid) {
	

		$control = $this->hasMember($userid);
        
        if(!$control) {
            return false;
        }
        else {
            
            // let's control whether he/she is already an admin of the
            // group
            $control_admin = $this->isAdmin($userid);
            
            if($control_admin)  { // this time we don't want it to be an admin
                
                // if he/she is, we just return true..
                // no need to return false in here
                // nothing went wrong...
                return true;
            }
        
            $res = & $this->Database->query("INSERT INTO admins VALUES('','{$userid}',NOW(),'N',0)");
		
            if (PEAR::isError($res)) {
			
                // die($res->getMessage());
                // no need to die here
                return false;
        
            }
		
            return $res;
            
        }
	
	}
	
    
    	function membershipApplied($uid) {
		$res = & $this->Database->getOne("SELECT request_id FROM membership_requests  WHERE member_id='{$uid}'");
		
		if (PEAR::isError($res)) {
			die($res->getMessage());
		}
		
		return !empty($res);
		
	}
	
	
    /**
     * uses addMembershipRequest and acceptMembershipRequest
     * functions but this is nonsense
     * This is the same as the constructor of this class
     * TODO: We can directly add the user as well
     * If we make an update to this function, we should change
     * the constructor also then
     */
    function addNewMember($membername) {

    	global $app_base;
    	
        
            
        /** and now let's add the admin user
         * into the group
         */
        
        $uid = _getMemberID($membername);
        
        $s1 = $this->addMembershipRequest("",null,'',$membername);
        
        //if(!$s1)
        //    return false;
        
        $s2 = $this->acceptMembershipRequest($uid,true);
        
        if(!$s2)
            return false;
           
        
        return true;
        
    }
    
    

}

?>
