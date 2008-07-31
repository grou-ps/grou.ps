<?php

$forgroup = false;

function topbarTranslate($str) {
	global $treng;
	if(isset($treng)) {
		return $treng->_($str, "topbar");
	}
	else {
		return $str;
	}
}


function getGroupTopBar($gname,$gtitle,$function="") {
	
	global $forgroup, $service_host;
	$forgroup = true; // this is to pass to getTopBar function
	
	$html = "";
	$args = array();

	
	$args[] = array($service_host, $gtitle);
	
	if(!empty($function)&&$function!="home") {
		$args[] = array($service_host.$function, ucfirst($function));
	}
	
	$html = getTopBar($args);
	
	return $html;
	
}

function showTopBar() {
	if(func_num_args()==0)
		$res = getTopBar();
	else 
		$res = getTopBar(func_get_args());
	echo $res;
}



function getTopBar($args=null) {
	
	global $forgroup;
	global $group_name;
	global $group;
	global $access_isGroupMember, $access_isGroupAdmin;
	global $allow_chat;
	global $service_host;
	
	if($args==null)
		$args = func_get_args();
	
	// we'll return this one
	$html = ""; 
	
	$html .= "<div id=\"topbar\">";

	
	// First member status
	$html .= "<div id=\"memberstatus\">";
	
	
	if(isset($_SESSION['valid_user'])) {
	
		$ue = _usernameToEmail($_SESSION['valid_user']);
		if($forgroup) {
			
			if($ue[0])
				$html .= $ue[1]." | <a href=\"{$service_host}?function=dashboard\">My Account</a> &middot;";
			else 
				$html .= $_SESSION['valid_user']." | <a href=\"{$service_host}?function=dashboard\">My Account</a> &middot;";
				
			
				
			if($allow_chat) {
				
				$html .= " <a href=\"javascript:void()\" onclick=\"switchChatVisibility()\">".topbarTranslate("Chat On/Off")."</a> &middot;";
				
				
			}
				
				
			if($access_isGroupAdmin) {
				$html .= " <a href=\"{$service_host}?function=admin\">".topbarTranslate("Group Admin Panel")."</a> &middot;";
			}
			elseif (!$access_isGroupMember) {
				if(!$group->membershipApplied(_getMemberID($_SESSION['valid_user']))) {
					$html .= " <a href=\"{$service_host}?function=join\">".topbarTranslate("Join This Group")."</a> &middot;";
				}
				else {
					$html .= " <i>".topbarTranslate("Waiting Membership Authorization")."</i> &middot;";
				}
			}
			
			$html .= " <a href=\"{$service_host}?function=signout\">".topbarTranslate("Sign Out")."</a>";
				
		}
	}
	else {
		
		if($forgroup) {
			$html .= "<a href=\"{$service_host}?function=signin\">".topbarTranslate("Sign In")."</a> or <a href=\"{$service_host}?function=join\">".topbarTranslate("Join")."</a>";
		}
		
	}
	
	$html .= " | ";
	
	// problematic
	// $im = sizeof($args)-1;
	foreach ($args as $i=>$a) {
		// $html .= " &gt; <a href=\"{$a[0]}\">".topbarTranslate($a[1])."</a>";
		$html .= "<a href=\"{$a[0]}\">".topbarTranslate($a[1])."</a> &gt; ";
		// if($i!=$im) {
		//	  $html .= "";
		// }
	}
	$html = substr($html,0,-6);
	
	$html .= "</div>";

	

	$html .= "</div>";
	
	
	return $html;
}



?>