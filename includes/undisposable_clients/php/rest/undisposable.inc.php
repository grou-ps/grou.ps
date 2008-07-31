<?php

$url = "http://www.undisposable.org/services/rest/";

// we prefer to use primitive string functions
// @TODO make it with XML functions
function undorg_isDisposableEmail($email) {
	global $url;
	$url .= "isDisposableEmail/?email=".addslashes($email);
	$res = @file_get_contents($url);
	if(strpos($res, '<email isdisposable="yes" />')!==false)
		return true;
	else
		return false;
}

// we prefer to use primitive string functions
// @TODO make it with XML functions
function undorg_isDisposableHost($host) {
	global $url;
	$url .= "isDisposableHost/?host=".addslashes($host);
	$res = @file_get_contents($url);
	if(strpos($res, '<host isdisposable="yes" />')!==false)
		return true;
	else
		return false;
}

?>
