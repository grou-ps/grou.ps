<?php

define("LANGUAGE_FULLY_SUPPORTED",1);
define("LANGUAGE_BETA_SUPPORTED",2);
define("LANGUAGE_ALPHA_SUPPORTED",3);

define("LANGUAGE_WITH_SITE_SUPPORT",1);
define("LANGUAGE_WITHOUT_SITE_SUPPORT",0);

$supported_languages = array();

$supported_languages[] = array("code"=>"english", "code_short"=>"en", "title"=>"English", "status"=>LANGUAGE_FULLY_SUPPORTED, "site_support"=>LANGUAGE_WITH_SITE_SUPPORT);


function is_language_supported($lang) {
	global $supported_languages;
	foreach ($supported_languages as $sl) {
		if($sl['code']==$lang)
			return true;
	}
	return false;
}

function is_language_site_supported($lang) {
	global $supported_languages;
	foreach ($supported_languages as $sl) {
		if($sl['code_short']==$lang&&$sl['site_support']==LANGUAGE_WITH_SITE_SUPPORT)
			return true;
	}
	return false;
}

function get_langcode_by_langname($langname) {
	global $supported_languages;
	foreach ($supported_languages as $l) {
		if($l['code']==$langname)
			return $l['code_short'];
	}
	return "en";
}

?>