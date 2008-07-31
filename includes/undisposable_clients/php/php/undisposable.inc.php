<?php

$url = "http://www.undisposable.org/services/php/";

// we prefer to use primitive string functions
// @TODO make it with XML functions
function undorg_isDisposableEmail($email) {
	global $url;
	$url .= "isDisposableEmail/?email=".addslashes($email);
	$res = @file_get_contents($url);
	$uns = @unserialize($res);
	if($uns['stat']=='ok')
		return $uns['email']['isdisposable'];
	else
	    return false;
}

// we prefer to use primitive string functions
// @TODO make it with XML functions
function undorg_isDisposableHost($host) {
	global $url;
	$url .= "isDisposableHost/?host=".addslashes($host);
	$res = @file_get_contents($url);
	$uns = @unserialize($res);
	if($uns['stat']=='ok')
	    return $uns['host']['isdisposable'];
	else
	    return false;
}

?>
