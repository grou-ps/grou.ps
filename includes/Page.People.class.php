<?php

require_once("Page.Module.class.php");
require_once('User.class.php');
require_once('custom_avatar.inc.php');



$PagePeople_hasview = "++";
$PagePeople_noview = "--";



function commentOn($id,$comment) {

	global $group_name;

	$p = new PeoplePage($group_name);

	$c = $p->insertComment($id,$comment);

	

	if($c) {
		$m = new Mailer(_getMemberUsername($id),$group_name);
		$m->youGotAWallMessage();
	return '1'; // true
	}
	else
	return '0'; // false


}


function commentDelete($id) {
	
	global $group_name;

	$p = new PeoplePage($group_name);

	$c = $p->deleteComment($id);


	if($c)
	return 1; // true
	else
	return 0; // false
}


function addToWatchlist($member_name) {


	global $access_isGroupMember;
	global $group_name;

	_filter_var($member_name);

	if($access_isGroupMember&&isset($_SESSION['valid_user'])) {


		if($_SESSION['valid_user']==$member_name)
		return '0'; // error

		$wid = _getMemberID($member_name);

		$u = new User($_SESSION['valid_user']);

		if($u->isWatching($group_name,$wid))
		return '1';

		$res = $u->addToWatchlist($group_name,$wid);

		if($res)
		return '2';
		else
		return '0';

	}
	else {

		return '0';

	}


}

function showSectionFavourites($mid) {

	global $access_isGroupMember;
	global $group_name;

	_filter_var($mid);

	if($access_isGroupMember&&isset($_SESSION['valid_user'])) {


		$u = new User($_SESSION['valid_user']);
		$membership_id = $u->getMembershipID($group_name);

		if($mid!=$membership_id)
		return '0';

		$res = $u->showFavourites($group_name);

		if($res)
		return '1';
		else
		return '0';

	}
	else {

		return '0';

	}


}
function showSectionLove($mid) {

	global $access_isGroupMember;
	global $group_name;

	_filter_var($mid);

	if($access_isGroupMember&&isset($_SESSION['valid_user'])) {


		$u = new User($_SESSION['valid_user']);
		$membership_id = $u->getMembershipID($group_name);

		if($mid!=$membership_id)
		return '0';

		$res = $u->showLoveStuff($group_name);

		if($res)
		return '1';
		else
		return '0';

	}
	else {

		return '0';

	}


}
function showSectionTags($mid) {

	global $access_isGroupMember;
	global $group_name;

	_filter_var($mid);

	if($access_isGroupMember&&isset($_SESSION['valid_user'])) {


		$u = new User($_SESSION['valid_user']);
		$membership_id = $u->getMembershipID($group_name);

		if($mid!=$membership_id)
		return '0';

		$res = $u->showTags($group_name);

		if($res)
		return '1';
		else
		return '0';

	}
	else {

		return '0';

	}


}
function showSectionBusiness($mid) {

	global $access_isGroupMember;
	global $group_name;

	_filter_var($mid);

	if($access_isGroupMember&&isset($_SESSION['valid_user'])) {


		$u = new User($_SESSION['valid_user']);
		$membership_id = $u->getMembershipID($group_name);

		if($mid!=$membership_id)
		return '0';

		$res = $u->showBusinessStuff($group_name);

		if($res)
		return '1';
		else
		return '0';

	}
	else {

		return '0';

	}


}
function showSectionHealth($mid) {

	global $access_isGroupMember;
	global $group_name;

	_filter_var($mid);

	if($access_isGroupMember&&isset($_SESSION['valid_user'])) {


		$u = new User($_SESSION['valid_user']);
		$membership_id = $u->getMembershipID($group_name);

		if($mid!=$membership_id)
		return '0';

		$res = $u->showHealthStuff($group_name);

		if($res)
		return '1';
		else
		return '0';

	}
	else {

		return '0';

	}


}
function hideSectionFavourites($mid) {

	global $access_isGroupMember;
	global $group_name;

	_filter_var($mid);

	if($access_isGroupMember&&isset($_SESSION['valid_user'])) {


		$u = new User($_SESSION['valid_user']);
		$membership_id = $u->getMembershipID($group_name);

		if($mid!=$membership_id)
		return '0';

		$res = $u->hideFavourites($group_name);

		if($res)
		return '1';
		else
		return '0';

	}
	else {

		return '0';

	}


}
function hideSectionLove($mid) {

	global $access_isGroupMember;
	global $group_name;

	_filter_var($mid);

	if($access_isGroupMember&&isset($_SESSION['valid_user'])) {


		$u = new User($_SESSION['valid_user']);
		$membership_id = $u->getMembershipID($group_name);

		if($mid!=$membership_id)
		return '0';

		/**
             * @hack for all
             */
		$res = $u->hideLoveStuff($group_name) && $u->hideHealthStuff($group_name) && $u->hideBusinessStuff($group_name);

		if($res) {
			return '1';
		}
		else
		return '0';

	}
	else {

		return '0';

	}


}
function hideSectionTags($mid) {

	global $access_isGroupMember;
	global $group_name;

	_filter_var($mid);

	if($access_isGroupMember&&isset($_SESSION['valid_user'])) {


		$u = new User($_SESSION['valid_user']);
		$membership_id = $u->getMembershipID($group_name);

		if($mid!=$membership_id)
		return '0';

		$res = $u->hideTags($group_name);

		if($res)
		return '1';
		else
		return '0';

	}
	else {

		return '0';

	}


}
function hideSectionBusiness($mid) {

	global $access_isGroupMember;
	global $group_name;

	_filter_var($mid);

	if($access_isGroupMember&&isset($_SESSION['valid_user'])) {


		$u = new User($_SESSION['valid_user']);
		$membership_id = $u->getMembershipID($group_name);

		if($mid!=$membership_id)
		return '0';

		$res = $u->hideBusinessStuff($group_name);

		if($res)
		return '1';
		else
		return '0';

	}
	else {

		return '0';

	}
}
function hideSectionHealth($mid) {
	global $access_isGroupMember;
	global $group_name;

	_filter_var($mid);

	if($access_isGroupMember&&isset($_SESSION['valid_user'])) {


		$u = new User($_SESSION['valid_user']);
		$membership_id = $u->getMembershipID($group_name);

		if($mid!=$membership_id)
		return '0';

		$res = $u->hideHealthStuff($group_name);

		if($res)
		return '1';
		else
		return '0';

	}
	else {

		return '0';

	}

}












function registerFavourites($mid,$favsongs,$favsingers,$favmovies,$favactors,$favbooks,$favauthors,$favsportsmen,$favartists,$favcities) {

	global $access_isGroupMember;
	global $group_name;

	_filter_var($mid);
	_filter_var($favsongs);
	_filter_var($favsingers);
	_filter_var($favmovies);
	_filter_var($favactors);
	_filter_var($favbooks);
	_filter_var($favauthors);
	_filter_var($favsportsmen);
	_filter_var($favartists);
	_filter_var($favcities);


	if($access_isGroupMember&&isset($_SESSION['valid_user'])) {

		$gname = $group_name; // i'm lazy

		$u = new User($_SESSION['valid_user']);
		$membership_id = $u->getMembershipID($group_name);

		if($mid!=$membership_id)
		return '0';

		$res1 = $u->setFavouriteSongs($gname,$favsongs);
		$res2 = $u->setFavouriteSingers($gname,$favsingers);
		$res3 = $u->setFavouriteActors($gname,$favactors);
		$res4 = $u->setFavouriteBooks($gname,$favbooks);
		$res5 = $u->setFavouriteAuthors($gname,$favauthors);
		$res6 = $u->setFavouriteMovies($gname,$favmovies);
		$res7 = $u->setFavouriteSportsmen($gname,$favsportsmen);
		$res8 = $u->setFavouriteArtists($gname,$favartists);
		$res9 = $u->setFavouriteCities($gname,$favcities);


		if($res1&&$res2&&$res3&&$res4&&$res5&&$res6&&$res7&&$res8&&$res9)
		return '1';
		else
		return '0';

	}
	else {

		return '0';

	}


}


function registerTagsX($mid,$tags) {


	global $access_isGroupMember;
	global $group_name;

	_filter_var($mid);
	_filter_var($tags);

	if($access_isGroupMember&&isset($_SESSION['valid_user'])) {


		$u = new User($_SESSION['valid_user']);
		$membership_id = $u->getMembershipID($group_name);

		if($mid!=$membership_id)
		return '0';

		$res = $u->setTags($group_name,$tags);

		if($res)
		return '1';
		else
		return '0';

	}
	else {

		return '0';

	}

}


function registerContactsX($mid,$email,$aim,$icq,$msn,$jabber,$yahoo) {

	global $access_isGroupMember;
	global $group_name;

	_filter_var($mid);
	_filter_var($email);
	_filter_var($aim);
	_filter_var($icq);
	_filter_var($msn);
	_filter_var($jabber);
	_filter_var($yahoo);

	if($access_isGroupMember&&isset($_SESSION['valid_user'])) {


		$u = new User($_SESSION['valid_user']);
		$membership_id = $u->getMembershipID($group_name);

		if($mid!=$membership_id)
		return '0';

		$res1 = $u->setEmail($_SESSION['valid_user'],$email);
		$res2 = $u->setAIM($group_name,$aim);
		$res3 = $u->setICQ($group_name,$icq);
		$res4 = $u->setMSN($group_name,$msn);
		$res5 = $u->setJabber($group_name,$jabber);
		$res6 = $u->setYahoo($group_name,$yahoo);

		// error_log('..........................'.$res1.'a'.$res2.'b'.$res3.'c'.$res4.'d'.$res5.'e'.$res6.'f');

		if($res1&&$res2&$res3&&$res4&&$res5&&$res6)
		return '1';
		else
		return '0';

	}
	else {

		return '0';

	}

}


function registerProfileX($mid,$namesurname,$birthday,$myspace,$nationality,$sexe) {

	global $access_isGroupMember;
	global $group_name;

	_filter_var($mid);
	_filter_var($namesurname);
	_filter_var($birthday);
	_filter_var($myspace);
	_filter_var($nationality);
	_filter_var($sexe);

	if($access_isGroupMember&&isset($_SESSION['valid_user'])) {


		$u = new User($_SESSION['valid_user']);
		$membership_id = $u->getMembershipID($group_name);

		if($mid!=$membership_id)
		return '0';

		$res1 = $u->setNameSurname($group_name,$namesurname);
		$res2 = $u->setBirthday($group_name,$birthday);
		$res3 = $u->setWebSite($group_name,$myspace);
		$res4 = $u->setNationality($group_name,$nationality);
		$res5 = $u->setSexe($group_name,$sexe);

		if($res1&&$res2&$res3&&$res4&&$res5)
		return '1';
		else
		return '0';

	}
	else {

		return '0';

	}

}


// TODO:
// make checks!
function cropCustomPicture($src,$x,$y,$w,$h) {

	global $access_isGroupMember;
	global $group_name;

	if($access_isGroupMember&&isset($_SESSION['valid_user'])) {

		include('configs/globals.php');


		$u = new User($_SESSION['valid_user']);
		$membership_id = $u->getMembershipID($group_name);

		$bigfile = $app_base."avatars/{$membership_id}/80.png";
		$smallfile = $app_base."avatars/{$membership_id}/16.png";
		$target = $app_base."avatars/{$membership_id}/";

		$baseplace = "/var/www/html/groups/html/members/wysiwyg_files/tmp/";
		$tmpfile = $baseplace.$src;

		mkdir($target);

		makeBigAvatar($tmpfile,$bigfile,$x,$y,$w,$h);
		makeSmallAvatar($bigfile,$smallfile);
		makeFlags($smallfile,$target);

		unlink($tmpfile);

		$res = $u->setCustomAvatar($group_name);

		if($res)
		return '1';
		else
		return '0';

	}
	else {

		return '0';

	}

}



function setAvatar($avno) {

	global $access_isGroupMember;
	global $group_name;

	if($access_isGroupMember&&isset($_SESSION['valid_user'])) {

		include('configs/globals.php');


		$u = new User($_SESSION['valid_user']);

		$res = $u->setAvatar($group_name,$avno);

		if($res)
		return '1';
		else
		return '0';

	}
	else {

		return '0';

	}

}

/**
 * If a problem occurs, returns empty string
 * otherwise the html of the section
 * @param $id ID of the membership (not member)
 * @returns the html of section; if error empty string
 */
function peopleGetProfileX($id) {

	_filter_var($id);

	global $group_name;
	global $access_isAuthenticated;



	$c_content = '';

	$p = new PeoplePage($group_name);

	$c = $p->getProfileX($id);


	//$c_content .= "<input type=\"hidden\" id=\"lovestuff_stripped_content\" value=\"$c\">\n";
	//$c_content .= "<splitme>\n";

	//parse_bbcode($c);
	$c_content .= $c;
	//$c_content .= "<br />\n";



	return $c_content;

}










/**
 * If a problem occurs, returns empty string
 * otherwise the html of the section
 * @param $id ID of the membership (not member)
 * @returns the html of section; if error empty string
 */
function peopleGetLoveStuff($id,$username) {

	_filter_var($id);
	_filter_var($username);

	global $group_name;
	global $access_isAuthenticated;

	global $PagePeople_hasview;
	global $PagePeople_noview;

	$c_content = '';

	$p = new PeoplePage($group_name);

	$c = $p->getLoveStuff($id);
	$c .= $p->getBusinessStuff($id);
	$c .= $p->getHealthStuff($id);


	_filter_res_var($c);
	//$c_toedit = addslashes(strip_tags($c));
	//$c_toview = parse_bbcode($c);


	$c_content .= "<input type=\"hidden\" id=\"lovestuff_stripped_content\" value=\"$c\">\n";
	//$c_content .= "<splitme>\n";

	if(!empty($c)) {
		parse_bbcode($c);
		$c_content .= $c;
		$c_content .= "<br />\n";
	}
	else {
		$c_content .= "<center><img src=\"http://grou.ps/images/nodata.png\" alt=\"No Data\" border=\"0\" /></center>";
	}



	$canShowLoveStuff = $p->canShowLoveStuff($username) || $p->canShowBusinessStuff($username) || $p->canShowHealthStuff($username);




	if($canShowLoveStuff)
	return $PagePeople_hasview.$c_content;
	else
	return $PagePeople_noview.$c_content;

}

/*
* If a problem occurs, returns empty string
* otherwise the html of the section
* @param $id ID of the membership (not member)
* @returns the html of section; if error empty string
*/
function peopleGetHealthStuff($id,$username) {

	_filter_var($id);
	_filter_var($username);

	global $group_name;
	global $access_isAuthenticated;

	global $PagePeople_hasview;
	global $PagePeople_noview;

	$c_content = '';

	$p = new PeoplePage($group_name);

	$c = $p->getHealthStuff($id);

	_filter_res_var($c);
	//$c_toedit = addslashes(strip_tags($c));
	//$c_toview = parse_bbcode($c);

	$c_content .= "<input type=\"hidden\" id=\"healthstuff_stripped_content\" value=\"$c\">\n";
	//$c_content .= "<splitme>\n";


	if(!empty($c)) {
		parse_bbcode($c); // var passes by reference
		$c_content .= $c;
		$c_content .= "<br />\n";
	}
	else {
		$c_content .= "<center><img src=\"http://grou.ps/images/nodata.png\" alt=\"No Data\" border=\"0\" /></center>";
	}


	$canShowHealthStuff = $p->canShowHealthStuff($username);
	if( $access_isAuthenticated && $p->isOwner($id, $_SESSION['valid_user']) ) {
		$c_content .= "<div class=\"box_mid_ops\" style=\"margin-top:25px;\"><div class=\"content\">";
		$c_content .= "[ ".$p->getOperationIcon('pencil')."<a href=\"javascript:void(people_edithealth({$id}))\">".$p->_("Edit")."</a> ]";
		$c_content .= " &nbsp; ";

		if($canShowHealthStuff)
		$c_content .= "[ ".$p->getOperationIcon('hide','view_visopt_health')."<a href=\"javascript:void(people_vishealth({$id}))\"><span id=\"visopt_health\">".$p->_("Hide")."</span></a> ]";
		else {
			$c_content .= "[ ".$p->getOperationIcon('show','view_visopt_health')."<a href=\"javascript:void(people_vishealth({$id}))\"><span id=\"visopt_health\">".$p->_("Show")."</span></a> ]";
			$c_content .= "<script>peopleHideSection(8,true);</script>";
		}

		$c_content .= "</div></div>";

	}

	if($canShowHealthStuff)
	return $PagePeople_hasview.$c_content;
	else
	return $PagePeople_noview.$c_content;

}

/*
* If a problem occurs, returns empty string
* otherwise the html of the section
* @param $id ID of the membership (not member)
* @returns the html of section; if error empty string
*/
function peopleGetBusinessStuff($id,$username) {

	_filter_var($id);
	_filter_var($username);

	global $group_name;
	global $access_isAuthenticated;

	global $PagePeople_hasview;
	global $PagePeople_noview;

	$c_content = '<splitme>';

	$p = new PeoplePage($group_name);

	$c = $p->getBusinessStuff($id);

	_filter_res_var($c);



	$c_content .= "<input type=\"hidden\" id=\"businessstuff_stripped_content\" value=\"$c\">\n";

	if(!empty($c)) {
		parse_bbcode($c); // var passes by reference
		$c_content .= $c;
		$c_content .= "<br />\n";
	}
	else {
		$c_content .= "<nodata>";
	}

	$canShowBusinessStuff = $p->canShowBusinessStuff($username);

	if($canShowBusinessStuff)
	return $PagePeople_hasview.$c_content;
	else
	return $PagePeople_noview.$c_content;

}






/*
* If a problem occurs, returns empty string
* otherwise the html of the section
* @param $id ID of the membership (not member)
* @returns the html of section; if error empty string
*/
function peopleGetTags($id,$username) {

	_filter_var($id);
	_filter_var($username);

	global $group_name;
	global $access_isAuthenticated;

	global $PagePeople_hasview;
	global $PagePeople_noview;

	$c_content = '<splitme>';

	$p = new PeoplePage($group_name);

	$c = $p->getTags($username);


	if(!empty($c)) {
		// parse_bbcode($c); // var passes by reference
		$c_content .= $c;
		$c_content .= "<br />\n";
	}
	else {
		$c_content .= "<center><img src=\"http://grou.ps/images/nodata.png\" alt=\"No Data\" border=\"0\" /></center>";
	}

	$canShowTags = $p->canShowTags($username);


	if($canShowTags)
	return $PagePeople_hasview.$c_content;
	else
	return $PagePeople_noview.$c_content;

}



function peopleGetFavourites($id,$username) {

	_filter_var($id);
	_filter_var($username);

	global $group_name;
	global $access_isAuthenticated;

	global $PagePeople_hasview;
	global $PagePeople_noview;

	$p = new PeoplePage($group_name);

	$c = $p->getFavourites($username);

	$canShowFavourites = $p->canShowFavourites($username);

	if($canShowFavourites)
	return $PagePeople_hasview.$c;
	else
	return $PagePeople_noview.$c;

}


/*
* If a problem occurs, returns empty string
* otherwise the html of the section
* @param $id ID of the membership (not member)
* @returns the html of section; if error empty string
*/
function peopleGetProfile($id) {

	_filter_var($id);


	$c_content = ''; // text that we will return!

	global $group_name;
	global $access_isAuthenticated;

	$p = new PeoplePage($group_name);

	$c = $p->getProfile($id);

	$cres = $c[0];
	$cfetch = $c[1];

	if($cres) {

		$c = $cfetch; // for compatibility issues!
		$age = $p->getAge($c["birthday"]);
		$zsign = $p->getZSign($c["birthday"]);



		/**
         * now it is time to evaluate the values we have
         */
		$zsign_evaluated = $p->evaluateZSign($zsign);
		$songs_evaluated = $p->evaluateMusic($c["favorite_songs"]);
		$singers_evaluated = $p->evaluateMusic($c["favorite_singers"]);
		$movies_evaluated =  $p->evaluateMovie($c["favorite_movies"]);
		$actors_evaluated =  $p->evaluateMovie($c["favorite_actors"]);
		$books_evaluated = $p->evaluateBook($c["favorite_books"]);
		$authors_evaluated = $p->evaluateBook($c["favorite_authors"]);
		$artists_evaluated = $p->evaluateBook($c["favorite_artists"]);
		$hobbies_evaluated = $p->evaluateBook($c["hobbies"]);

		$c_content .= "<b>Motto:</b> <span id=\"area_profile_motto\">{$c["motto"]}</span><br />\n";

		// we should be explicive here
		if(empty($c["birthday"]))
		$c_content .= "<b>Birthday:</b> <span id=\"area_profile_birthday\">YYYY-MM-DD</span><br />\n";
		else
		$c_content .= "<b>Birthday:</b> <span id=\"area_profile_birthday\">{$c["birthday"]}</span><br />\n";

		$c_content .= "<b>Age:</b> <span id=\"area_profile_age\">{$age}</span><br />\n";
		$c_content .= "<span id=\"area_profile_zsign\"><b>Zodiac Sign:</b> <span id=\"area_profile_age\">{$zsign_evaluated}</span><br />\n";
		$c_content .= "<b>Sexe:</b> <span id=\"area_profile_sexe\">{$c["sexe"]}</span><br />\n";
		$c_content .= "<b>Sexual Orientation:</b> <span id=\"area_profile_sorientation\">{$c["sexual_orientation"]}</span><br />\n";
		$c_content .= "<b>Marital Status:</b> <span id=\"area_profile_mstatus\">{$c["marital_status"]}</span><br />\n";
		$c_content .= "<b>Occupation:</b> <span id=\"area_profile_occupation\">{$c["occupation"]}</span><br />\n";
		$c_content .= "<b>Nationality:</b> <span id=\"area_profile_nationality\">{$c["nationality"]}</span><br />\n";
		$c_content .= "<b>2<sup>nd</sup> Nationality:</b> <span id=\"area_profile_2nationality\">{$c["second_nationality"]}</span><br />\n";
		$c_content .= "<b>Ethnic Race:</b> <span id=\"area_profile_erace\">{$c["ethnic_race"]}</span><br />\n";
		$c_content .= "<b>Religion:</b> <span id=\"area_profile_religion\">{$c["religion"]}</span><br />\n";
		$c_content .= "<b>Favorite Songs:</b> <span id=\"area_profile_fsongs\">{$songs_evaluated}</span><br />\n";
		$c_content .= "<b>Favorite Singers:</b> <span id=\"area_profile_fsingers\">{$singers_evaluated}</span><br />\n";
		$c_content .= "<b>Favorite Movies:</b> <span id=\"area_profile_fmovies\">{$movies_evaluated}</span><br />\n";
		$c_content .= "<b>Favorite Actors/Actresses/Directors:</b> <span id=\"area_profile_factors\">{$actors_evaluated}</span><br />\n";
		$c_content .= "<b>Favorite Books:</b> <span id=\"area_profile_fbooks\">{$books_evaluated}</span><br />\n";
		$c_content .= "<b>Favorite Authors:</b> <span id=\"area_profile_fauthors\">{$authors_evaluated}</span><br />\n";
		$c_content .= "<b>Favorite Sportsmen:</b> <span id=\"area_profile_fsportsmen\">{$c["favorite_sportsmen"]}</span><br />\n";
		$c_content .= "<b>Favorite Artists:</b> <span id=\"area_profile_fartists\">{$artists_evaluated}</span><br />\n"; //*
		$c_content .= "<b>Favorite Cities:</b> <span id=\"area_profile_fcities\">{$c["favorite_cities"]}</span><br />\n"; //*
		$c_content .= "<b>Favorite Colors:</b> <span id=\"area_profile_fcolors\">{$c["favorite_colors"]}</span><br />\n";
		$c_content .= "<b>Hobbies:</b> <span id=\"area_profile_hobbies\">{$hobbies_evaluated}</span><br />\n";
		$c_content .= "<b>Fobbies:</b> <span id=\"area_profile_fobbies\">{$c["fobbies"]}</span><br />\n";
		// }


		if( $access_isAuthenticated && $p->isOwner($id, $_SESSION['valid_user']) ) {
			$c_content .= "<br />\n<div>[ <a href=\"javascript:void(people_editprofile({$id}))\">".$this->_("Edit")."</a> ]</div>";
		}

	}

	else {



		$c_content .= "<span id=\"area_profile_motto\"></span>";


		$c_content .= "<span id=\"area_profile_birthday\" style=\"position:absolute;visibility:hidden;\">YYYY-MM-DD</span>";


		$c_content .= "<span id=\"area_profile_age\"></span>";
		$c_content .= "<span id=\"area_profile_zsign\"></span><span id=\"area_profile_age\"></span>";
		$c_content .= "<span id=\"area_profile_sexe\"></span>";
		$c_content .= "<span id=\"area_profile_sorientation\"></span>";
		$c_content .= "<span id=\"area_profile_mstatus\"></span>";
		$c_content .= "<span id=\"area_profile_occupation\"></span>";
		$c_content .= "<span id=\"area_profile_nationality\"></span>";
		$c_content .= "<span id=\"area_profile_2nationality\"></span>";
		$c_content .= "<span id=\"area_profile_erace\"></span>";
		$c_content .= "<span id=\"area_profile_religion\"></span>";
		$c_content .= "<span id=\"area_profile_fsongs\"></span>";
		$c_content .= "<span id=\"area_profile_fsingers\"></span>";
		$c_content .= "<span id=\"area_profile_fmovies\"></span>";
		$c_content .= "<span id=\"area_profile_factors\"></span>";
		$c_content .= "<span id=\"area_profile_fbooks\"></span>";
		$c_content .= "<span id=\"area_profile_fauthors\"></span>";
		$c_content .= "<span id=\"area_profile_fsportsmen\"></span>";
		$c_content .= "<span id=\"area_profile_fartists\"></span>";
		$c_content .= "<span id=\"area_profile_fcities\"></span>";
		$c_content .= "<span id=\"area_profile_fcolors\"></span>";
		$c_content .= "<span id=\"area_profile_hobbies\"></span>";
		$c_content .= "<span id=\"area_profile_fobbies\"></span>\n";



		$c_content .= "<br />\n";

		if( $access_isAuthenticated && $p->isOwner($id, $_SESSION['valid_user']) ) {
			$c_content .= "<br />\n<div>[ <a href=\"javascript:void(people_editprofile({$id}))\">".$this->_("Edit")."</a> ]</div>";
		}
	}


	return $c_content;

}


function evaluateStuff($content) {

	return "!!!found nothing!!!";





}




function registerLoveStuff($mid,$content) {


	_filter_var($mid);
	_filter_var($content);

	global $group_name;
	global $access_isAuthenticated;

	$p = new PeoplePage($group_name);

	$r = $p->registerLoveStuff($mid,$content);

	if($r) {
		return '1'; // true
	}
	else {
		return '0'; // false
	}
}

function registerHealthStuff($mid,$content) {

	_filter_var($mid);
	_filter_var($content);

	global $group_name;
	global $access_isAuthenticated;

	$p = new PeoplePage($group_name);


	$r = $p->registerHealthStuff($mid,$content);

	if($r) {
		return '1'; // true
	}
	else {
		return '0'; // false
	}
}

function registerBusinessStuff($mid,$content) {

	_filter_var($mid);
	_filter_var($content);


	global $group_name;
	global $access_isAuthenticated;

	$p = new PeoplePage($group_name);


	$r = $p->registerBusinessStuff($mid,$content);

	if($r) {
		return '1'; // true
	}
	else {
		return '0'; // false
	}
}


/**
 * Registers the entered profile
 * Processes lots of variables
 * The javascript that sends vars to it is
 * x_registerProfile(g_motto,g_birthday,g_sexe,g_sorientation,g_mstatus,g_occupation,g_nationality,g_2nationality,g_erace,g_religion,g_fsongs,g_fsingers,g_fmovies,g_factors,g_fbooks,g_fauthors,g_fsportsmen,g_fartists,g_fcities,g_fcolors,g_hobbies,g_fobbies,registerProfileRes)
 */

function registerProfile($mid,$motto,$birthday,$sexe,$sorientation,$mstatus,$occupation,$nationality,$nationality2,$erace,$religion,$fsongs,$fsingers,$fmovies,$factors,$fbooks,$fauthors,$fsportsmen,$fartists,$fcities,$fcolors,$hobbies,$fobbies) {

	_filter_var($mid);
	_filter_var($motto);
	_filter_var($birthday);
	_filter_var($sexe);
	_filter_var($sorientation);
	_filter_var($mstatus);
	_filter_var($occupation);
	_filter_var($nationality);
	_filter_var($nationality2);
	_filter_var($erace);
	_filter_var($religion);
	_filter_var($fsongs);
	_filter_var($fsingers);
	_filter_var($fmovies);
	_filter_var($factors);
	_filter_var($fbooks);
	_filter_var($fauthors);
	_filter_var($fsportsmen);
	_filter_var($fartists);
	_filter_var($fcities);
	_filter_var($fcolors);
	_filter_var($hobbies);
	_filter_var($fobbies);

	global $group_name;

	$p = new PeoplePage($group_name);


	$r = $p->registerProfile($mid,$motto,$birthday,$sexe,$sorientation,$mstatus,$occupation,$nationality,$nationality2,$erace,$religion,$fsongs,$fsingers,$fmovies,$factors,$fbooks,$fauthors,$fsportsmen,$fartists,$fcities,$fcolors,$hobbies,$fobbies);

	if(!$r) {
		return '-1'; // false
	}
	else {
		return $mid; // true; so return the id
	}

}







function getPeopleBlock($membership_id) {

	_filter_var($membership_id);


	global $group_name;


	$p = new PeoplePage($group_name);


	$r = $p->getPeopleHTML($membership_id);

	return $r;


}




class PeoplePage extends ModulePage  {


	var $AmazonKey = '';

	var $Amazon = null;

	var $UserObjects = array();

	var $OnlineStatusIndicator = "";

	function PeoplePage() {

		parent::ModulePage("pagepeople");
	}



	function getPeople() {

		$res = array();

		$people = & $this->Database->getAll("SELECT membership_id, member_id, member_name FROM memberships ORDER BY member_name ASC", array(), 2 /*DB_FETCHMODE_ASSOC*/);

		if (PEAR::isError($people)) {
			die($people->getMessage());
		}

		foreach($people as $person) {

			$pid = $person['membership_id'];
			$pname = $person['member_name'];

			$uname = _getMemberUsername($person['member_id']);
			$u = new User($uname);
			$picon = $u->getMiniIcon();


			$res[] = array('id'=>$pid, 'name'=>$pname, 'icon'=>$picon, 'member_username'=>$uname);

		}

		if(sizeof($res)==0)
		return false;
		else
		return $res;

	}


	/**
     * returns the html
     * if $em_membership is set
     * then this membership is emphasized
     * @param $em_membership membership to be emphasized
     * @returns html of the People block
     */
	function getPeopleHTML($em_membership=-1) {

		$html = ''; // html to return
		$p = $this->getPeople();

		if(!$p) {
			return '';
		}
		else {

			foreach($p as $pu) {

				$pname = unicode2utf8($pu['name']);
				_filter_res_var($pname);

				if($pu['id']==$em_membership) {

					$html .= "<div style=\"margin-bottom: 5px;\">";
					$html .= "<img src=\"{$pu['icon']}\" border=\"0\" width=\"16\" height=\"16\" align=\"absbottom\" /> ";

					// I love my server
					$html .= "<a href=\"javascript:void()\" rel=\"me\">";

					$html .= "<b>{$pname}</b>"; // emphasized

					$html .= "</a></div>\n";

				}
				else {

					$html .= "<div style=\"margin-bottom: 5px;\">";
					$html .= "<img src=\"{$pu['icon']}\" border=\"0\" width=\"16\" height=\"16\" align=\"absbottom\" /> ";
					$html .= "<a href=\"javascript:void(people_show({$pu['id']},'{$pu['member_username']}'))\" rel=\"contact\">";
					$html .= $pname;
					$html .= "</a></div>\n";

				}

			}


			/**
			* no, doesn't work in getExtraHead()
			*/
			$html .= <<<EOS
            
        
        <script type="text/javascript" src="http://grou.ps/includes/wz_dragdrop.js"></script>
        <script type="text/javascript">
        <!--
        
        function crop_preloader() {
                
            SET_DHTML('theCrop');
                
        }
            
        crop_preloader();
            
            

        function my_DragFunc()
        {
            var z = xHeight('theImage')<=xWidth('theImage')?xHeight('theImage'):xWidth('theImage');
            
            dd.elements.theCrop.maxoffr = xWidth('theImage') - dd.elements.theCrop.w;
            dd.elements.theCrop.maxoffb = xHeight('theImage') - dd.elements.theCrop.h;
            dd.elements.theCrop.maxoffl = 0;
            dd.elements.theCrop.maxofft = 0;
            dd.elements.theCrop.minw = 80;
            dd.elements.theCrop.minh = 80;
            dd.elements.theCrop.maxw = (xWidth('theImage') + xPageX('theImage')) - dd.elements.theCrop.x;
            dd.elements.theCrop.maxh = (xHeight('theImage') + xPageY('theImage')) - dd.elements.theCrop.y;
        }

        function my_ResizeFunc()
        {
            var z = xHeight('theImage')<=xWidth('theImage')?xHeight('theImage'):xWidth('theImage');
            
            dd.elements.theCrop.maxoffr = xWidth('theImage') - dd.elements.theCrop.w;
            dd.elements.theCrop.maxoffb = xHeight('theImage') - dd.elements.theCrop.h;
            dd.elements.theCrop.maxoffl = 0;
            dd.elements.theCrop.maxofft = 0;
            dd.elements.theCrop.minw = 80;
            dd.elements.theCrop.minh = 80;
            dd.elements.theCrop.maxw = (xWidth('theImage') + xPageX('theImage')) - dd.elements.theCrop.x;
            dd.elements.theCrop.maxh = (xHeight('theImage') + xPageY('theImage')) - dd.elements.theCrop.y;
        }

   

        //-->
        </script>
        
        
        
EOS;

			return $html;
		}
	}





	function getPeopleHTML_Mobile() {


		$html = ''; // html to return
		$p = $this->getPeople();

		if(!$p) {
			return '';
		}
		else {

			$html .= "<ul>";

			foreach($p as $pu) {

				$uname = $pu['member_username'];
				$u = new User($uname);
				$unamesurname = $u->getNameSurname();
				$uemail = $u->getEmail();

				if(isset($_SESSION['valid_user']))
				$html .= "<li>{$unamesurname}: {$uemail}</li>";
				else
				$html .= "<li>{$unamesurname}</li>";

			}

			$html .= "</ul>";

			return $html;
		}
	}





	function getSubContent() {


		$html = "";


		// and now the iframe for photo upload
		/** and now the iframe for uploader **/
		$html .= "<iframe width=\"0\" height=\"0\" frameborder=\"0\"";
		$html .= "scrolling=\"no\" marginwidth=\"0\" marginheigh=\"0\"";
		$html .= "id=\"upload_frame\"  name=\"upload_frame\">";
		$html .= "</iframe>";





		return $html;

	}


	/**
     * obsolete now
     */
	function introduce() {

		$html = ''; // We will return this.


		/**
		 * Ýngilizcem cok cok iyi olmadigi icin bu kismi tam istedigim 
		 * gibi dolduramadim ama aslinda, soyle bisey olmasi lazim:
		 *
		 * Gnippet'imizin People kismina hosgeldiniz! Herkes arkadaslarinin
		 * ne isle mesgul oldugunu, hayatinin nasil gittigini, saglik durumunu
		 * merak eder. Bu sorulari sormak her zaman mumkun olmasa da; bu 
		 * .... Bu bolumde misyon edindigimiz sey; grup uyelerinin birbirleri
		 * hakkinda daha fazla bilgi sahibi olmasini saglamaktir.
		 * Her ne kadar bloglar, yasaminizdaki son gelismeleri ortaya koysa da
		 * bu kisim hayatiniz hakkinda daha overview ama daha genis kapsamli
		 * bilgileri baskalarinin paylasimina acmanizi sagliyor.
		 */


		$html .= "<p>Welcome to the People section of our group. In this section, you can find more information about the members of our gnippet.</p>";

		$html .= "<p>When you click on a name present on the sidebar, you will see things like \"Business Stuff\", \"Health Stuff\", \"Love Stuff\" and \"My Profile\" in this column. These are entries written by the person you are trying to reach.</p>";

		$html .= "<p>If you'd like to let your friends know about yourself, you can make your own entries too. Just click on your own name; you will see \"Edit\" buttons at the end of each empty stuff boxes. By clicking on \"Edit\", you may start telling about yourself and sharing...</p>";

		return $html;
	}



	/**
	 * @param $mid membership_id
	 * @returns string if 0: ''; else $result
	 */
	function getLoveStuff($mid) {

		_filter_var($mid);
		$member_id = membershipIDToMemberID($this->Database,$mid);
		$group_id = membershipIDToGroupID($this->Database,$mid);

		$ls = & $this->Database->getOne("SELECT content FROM all_stuff WHERE member_id = '{$member_id}' AND group_id = '{$group_id}' AND stuff_type = 'love' ORDER BY modified_on DESC LIMIT 0,1 ");

		if (PEAR::isError($ls)) {
			die($ls->getMessage());
		}

		return $ls;

	}

	/**
	 * @param $mid membership_id
	 * @returns string if 0: ''; else $result
	 */
	function getHealthStuff($mid) {

		_filter_var($mid);

		$member_id = membershipIDToMemberID($this->Database,$mid);
		$group_id = membershipIDToGroupID($this->Database,$mid);

		$ls = & $this->Database->getOne("SELECT content FROM all_stuff WHERE member_id = '{$member_id}' AND group_id = '{$group_id}' AND stuff_type = 'health' ORDER BY modified_on DESC LIMIT 0,1 ");

		if (PEAR::isError($ls)) {
			die($ls->getMessage());
		}

		return $ls;

	}


	/**
	 * @param $mid membership_id
	 * @returns string if 0: ''; else $result
	 */
	function getBusinessStuff($mid) {

		_filter_var($mid);
		$member_id = membershipIDToMemberID($this->Database,$mid);
		$group_id = membershipIDToGroupID($this->Database,$mid);

		$ls = & $this->Database->getOne("SELECT content FROM all_stuff WHERE member_id = '{$member_id}' AND group_id = '{$group_id}' AND stuff_type = 'business' ORDER BY modified_on DESC LIMIT 0,1 ");

		if (PEAR::isError($ls)) {
			die($ls->getMessage());
		}

		return $ls;

	}





	function getProfileX($mid) {

		global $people_allow_sexe, $people_allow_birthday, $people_allow_zodiac, 
			$people_allow_website, $people_allow_nation;
		
		$html = "";

		// from User.class.php
		$musername = getUsernameByMembershipID($this->Database,$mid);

		$user = new User($musername);

		$avat = $user->getAvatar();
		$name_surname = $user->getNameSurname();
		$age = $user->getAge();
		$zsign = $this->evaluateZSign($user->getZSign());
		$exact_birthday = date('m/d/Y',strtotime($user->getBirthday()));
		$birthday = date('F, j',strtotime($user->getBirthday()));
		$nationality_1 = $user->getNationality();
		$nationality_flag_1 = $user->getNationalityFlag($nationality_1);
		$sexe = $user->getSexe();
		$sexeicon = $user->getSexeIcon($sexe);
		$contact_icq = $user->getContactICQ();
		$contact_jabber = $user->getContactJabber();
		$contact_msn = $user->getContactMSN();
		$contact_yahoo = $user->getContactYahoo();
		$contact_aim = $user->getContactAIM();
		$contact_email = $user->getEmail();
		$website = $user->getWebSite();

		$html .= <<<EOS
	 <div style="position:absolute;visibility:hidden;">
	 
	 
	 <textarea id="inf_namesurname">{$name_surname}</textarea>
	 <textarea id="inf_sexe">{$sexe}</textarea>"
	 <textarea id="inf_birthday">{$exact_birthday}</textarea>
	 <textarea id="inf_nationality">{$nationality_1}</textarea>
	 <textarea id="inf_myspace">{$website}</textarea>
	 
	 <textarea id="inf_email"></textarea>
	 <textarea id="inf_aim">{$contact_aim}</textarea>"
	 <textarea id="inf_msn">{$contact_msn}</textarea>
	 <textarea id="inf_yahoo">{$contact_yahoo}</textarea>
	 <textarea id="inf_jabber">{$contact_jabber}</textarea>
	 <textarea id="inf_icq">{$contact_icq}</textarea>
	 
	 
	 </div>
	 
EOS;


		// and start the HTML

		$html .= "<div class=\"vcard\">";

		$html .= "<img class=\"photo\" src=\"{$avat}?".rand(0,1000)."\" width=\"80\" alt=\"{$name_surname}\" height=\"80\" border=\"0\" align=\"left\" style=\"margin-bottom: 5px;margin-left: 15px; margin-right: 25px;\" />";

		$html .= "<div style=\"font-size: 1.2em;\"><b><span class=\"fn\">{$name_surname}</span>";
		if($people_allow_birthday)
			$html .= " ({$age})";
		$html .= "</b> ";
		if($people_allow_nation)
			$html .= "<img src=\"{$nationality_flag_1}\" width=\"16\" height=\"11\" border=\"0\" />";
		$html .= "</div>";
		
		if($people_allow_website)
			$html .= "<div style=\"font-size: 1.1em;margin-bottom: 10px;\"><a class=\"url\" href=\"{$website}\">{$website}</a></div>";

		$html .= "<div style=\"margin-bottom: 10px;\">";
		if($people_allow_sexe)
			$html .= "<img align=\"bottom\" src=\"{$sexeicon}\" border=\"0\" alt=\"\" width=\"16\" height=\"16\" /> ";
		if($people_allow_zodiac)
			$html .= $zsign." (<span class=\"bday\">{$birthday}</span>)";
		$html .= "</div>";

		$html .= "";

		/*
		$html .= "<div>";
		$html .= "<a class=\"url\" type=\"application/x-icq\" href=\"http://www.icq.com/people/cmd.php?uin={$contact_icq}&action=message\"><img src=\"{$this->OnlineStatusIndicator}icq/{$contact_icq}\" alt=\"\" border=\"0\" style=\"margin-right: 5px; margin-right: 25px;\" /></a>";
		$html .= "<a class=\"url\" href=\"xmpp:{$contact_jabber}\"><img src=\"{$this->OnlineStatusIndicator}jabber/{$contact_jabber}\" alt=\"\" border=\"0\" style=\"margin-right: 5px; margin-right: 25px;\" /></a>";
		$html .= "<a class=\"url\" href=\"msnim:chat?contact={$contact_msn}\"><img src=\"{$this->OnlineStatusIndicator}msn/{$contact_msn}\" alt=\"\" border=\"0\" style=\"margin-right: 5px; margin-right: 25px;\" /></a>";
		$html .= "<a class=\"url\" href=\"ymsgr:sendIM?{$contact_yahoo}\"><img src=\"{$this->OnlineStatusIndicator}yahoo/{$contact_yahoo}\" alt=\"\" border=\"0\" style=\"margin-right: 5px; margin-right: 25px;\" /></a>";
		$html .= "<a class=\"url\" href=\"aim:goim?screenname={$contact_aim}\"><img src=\"{$this->OnlineStatusIndicator}aim/{$contact_aim}\" alt=\"\" border=\"0\" style=\"margin-right: 5px; margin-right: 25px;\" /></a>";
		$html .= "</div>";
		*/


		$html .= "</div>";
		
		
		if(isset($_SESSION['valid_user'])&&$musername==$_SESSION['valid_user']) {
			
		
		$html .= <<<EOS

        
        <script type="text/javascript" src="http://grou.ps/includes/wz_dragdrop.js"></script>
        <script type="text/javascript">
        <!--
        
        function crop_preloader() {
                
            SET_DHTML('theCrop');
                
        }
            
        crop_preloader();
            
            

        function my_DragFunc()
        {
            var z = xHeight('theImage')<=xWidth('theImage')?xHeight('theImage'):xWidth('theImage');
            
            dd.elements.theCrop.maxoffr = xWidth('theImage') - dd.elements.theCrop.w;
            dd.elements.theCrop.maxoffb = xHeight('theImage') - dd.elements.theCrop.h;
            dd.elements.theCrop.maxoffl = 0;
            dd.elements.theCrop.maxofft = 0;
            dd.elements.theCrop.minw = 80;
            dd.elements.theCrop.minh = 80;
            dd.elements.theCrop.maxw = (xWidth('theImage') + xPageX('theImage')) - dd.elements.theCrop.x;
            dd.elements.theCrop.maxh = (xHeight('theImage') + xPageY('theImage')) - dd.elements.theCrop.y;
        }

        function my_ResizeFunc()
        {
            var z = xHeight('theImage')<=xWidth('theImage')?xHeight('theImage'):xWidth('theImage');
            
            dd.elements.theCrop.maxoffr = xWidth('theImage') - dd.elements.theCrop.w;
            dd.elements.theCrop.maxoffb = xHeight('theImage') - dd.elements.theCrop.h;
            dd.elements.theCrop.maxoffl = 0;
            dd.elements.theCrop.maxofft = 0;
            dd.elements.theCrop.minw = 80;
            dd.elements.theCrop.minh = 80;
            dd.elements.theCrop.maxw = (xWidth('theImage') + xPageX('theImage')) - dd.elements.theCrop.x;
            dd.elements.theCrop.maxh = (xHeight('theImage') + xPageY('theImage')) - dd.elements.theCrop.y;
        }

   

        //-->
        </script>
        
        

		
EOS;

}

		return $html;


	}


	/**
	 * @param $mid membership_id
	 * @returns array of profile units
	 */
	function getProfile($mid) {

		_filter_var($mid);

		/**
		 * There are lots of fields in here
		 * and they should be explicitly written
		 * That's we'll do this part as follows:
		 */
		$fields = '';
		$fields .= 'favorite_songs,';
		$fields .= 'favorite_singers,';
		$fields .= 'favorite_movies,';
		$fields .= 'favorite_actors,';
		$fields .= 'favorite_books,';
		$fields .= 'favorite_authors,';
		$fields .= 'favorite_colors,';
		$fields .= 'favorite_sportsmen,';
		$fields .= 'favorite_artists,';
		$fields .= 'favorite_cities,';
		$fields .= 'motto,';
		$fields .= 'adored_people,';
		$fields .= 'birthday,';
		$fields .= 'religion,';
		$fields .= 'nationality,';
		$fields .= 'second_nationality,';
		$fields .= 'ethnic_race,';
		$fields .= 'marital_status,';
		$fields .= 'children,';
		$fields .= 'sexe,';
		$fields .= 'sexual_orientation,';
		$fields .= 'occupation,';
		$fields .= 'hobbies,';
		$fields .= 'fobbies';

		$member_id = membershipIDToMemberID($this->Database,$mid);
		$group_id = membershipIDToGroupID($this->Database,$mid);

		$q = & $this->Database->getRow("SELECT $fields FROM profiles WHERE member_id = '{$member_id}' AND group_id = '{$group_id}'  ORDER BY modified_on DESC LIMIT 0,1 ", array(), 2 /*ASSOC*/);

		if (PEAR::isError($q)) {
			die($q->getMessage());
		}

		if(sizeof($q)==0)
		return array(false,array());
		else
		return array(true,$q);

	}

	function getAge($birthday) {

		_filter_var($birthday);

		$birthyear = substr($birthday,0,4);
		$nowyear = date('Y');

		$age = $nowyear - $birthyear;

		return $age;


	}

	function getZSign($birthday_date) {

		_filter_var($birthday_date);

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

	function isOwner($mid,$membername) {

		_filter_var($mid);
		_filter_var($membername);



		// shorter method

		$member_id = _getMemberID($membername);
		$compare_mid = getMembershipID($this->Database, $member_id);

		if($compare_mid==$mid) {
			return true;
		}
		else {
			return false;
		}
	}






	function getDefaultMembershipID($memberselected=null) {

		global $access_isGroupMember;

		$membership_id = -1;
		$member_username = '';

		$g = null;


		if($memberselected!=null) {

			$g = new Group();
			$u = new User($memberselected);
			$memberselected_id = $u->getID(); // from Access.php

			if($g->hasMember($memberselected_id)) {
				$member_username = $memberselected;
				$membership_id = $u->getMembershipID();
			}

		}

		if($membership_id==-1) {

			if(isset($_SESSION['valid_user'])&&$access_isGroupMember) {
				$member_username = $_SESSION['valid_user'];
				$u = new User($member_username);
				$membership_id = $u->getMembershipID();
			}
			else {

				if($g==null)
				$g = new Group();

				$u = $g->getRandomMember();

				$member_username = _getMemberUsername($u['member_id']);
				$membership_id = $u['membership_id'];

			}
		}

		return array('id'=>$membership_id,'username'=>$member_username);

	}








	/**
     * This function is to evaluate stuff
     * and take out the people in the boxes
     * Quick and dirty hack!
     * Should be reviewed extensively!
     */
	function evaluateStuff($content) {

		_filter_var($content);


		$res_array = array(); // response
		$res_uids = array(); // response user ids
		$res_unames = array(); // response user names


		/**
         * quick and dirty hack!
         * implement a better solution!
         * TODO: implement a better solution!
         */
		/**
         * these are things like stopwords of search engines
         */
		$content = str_replace('"', '', $content);
		$content = str_replace('\'s', '', $content);
		$content = str_replace('\'', '', $content);
		$content = str_replace('\\', '', $content);
		$content = str_replace('of', '', $content);
		$content = str_replace('to', '', $content);
		$content = str_replace('from', '', $content);
		$content = str_replace('and', '', $content);
		$content = str_replace('after', '', $content);
		$content = str_replace('before', '', $content);
		$content = str_replace('with', '', $content);

		$words = explode(' ',$content);



		foreach($words as $word) {


			$q = & $this->Database->getAll("SELECT member_id, member_name FROM memberships WHERE gnippet_ids like '%{$gid_format}%' AND (member_name = '{$word}' OR member_name like '{$word} %' OR member_name = '% {$word} %' OR member_name = '% {$word}')", array(), 2 /* ASSOC */);

			if (PEAR::isError($q)) {
				die($q->getMessage());
			}


			if(sizeof($q)>0) {

				foreach($q as $qi) {


					if(!in_array($qi['member_id'],$res_uids)) {
						$res_uids[] = $qi['member_id'];
						$res_unames[] = $qi['member_name'];

					}

				}

			}


		}


		$reslength = sizeof($res_uids);

		if($reslength==0) {

		}
		else {
			$m=0;
			for($i=0;$i<$reslength;$i++) {

				$res_array[$m]['id'] = $res_uids[$i];
				$res_array[$m]['name'] = $res_unames[$i];
				$m++;
			}


			return array(true,$res_array);

		}
	}






	/**
     * This function gets the zodiac sign and returns a link
     * to the actual zodiac sign page.
     * We currently use Yahoo Horoscopes for this.
     * @param $zsign the zodiac sign
     * @returns string link to yahoo horoscopes for given zodiac sign
     */
	function evaluateZSign($zsign) {

		$final_res = '';
		$yahoo_zsign = '';

		$yahoo_zsign .= "http://astrology.yahoo.com/astrology/general/dailyoverview/";
		$yahoo_zsign .= strtolower($zsign);

		$final_res =  "<a href=\"{$yahoo_zsign}\">{$zsign}</a>";

		return $final_res;

	}



	function getAmazonMusicLink($keyword) {

		$type = 'music';
		$amazon_href = '';

		$amazon_href .= "http://www.amazon.com/exec/obidos/redirect?link_code=ur2&tag=";
		$amazon_href .= $this->AmazonKey;
		$amazon_href .= "&camp=1789&creative=9325&path=external-search%3Fsearch-type=ss%26keyword=";
		$amazon_href .= $keyword;
		$amazon_href .= "%26index=";
		$amazon_href .= $type;

		return $amazon_href;

	}






	function getAmazonBookLink($keyword) {

		$type = 'books';
		$amazon_href = '';

		$amazon_href .= "http://www.amazon.com/exec/obidos/redirect?link_code=ur2&tag=";
		$amazon_href .= $this->AmazonKey;
		$amazon_href .= "&camp=1789&creative=9325&path=external-search%3Fsearch-type=ss%26keyword=";
		$amazon_href .= $keyword;
		$amazon_href .= "%26index=";
		$amazon_href .= $type;


		return $amazon_href;

	}




	function getOperationsBlock() {
		
		global $people_allow_tags, $people_allow_favorites;

		if(!isset($_SESSION['valid_user']))
		die('Error No: 9435');

		$member_id = _getMemberID($_SESSION['valid_user']);
		$id = getMembershipID($this->Database, $member_id);

		$spacer = " &nbsp; ";

		$c_content = "";

		$c_content .= "<button onclick=\"people_changepicture({$id})\">".$this->getOperationIcon('picture_edit')." ".$this->_("Change Picture")."</button>";
		$c_content .= $spacer;
		$c_content .= "<button onclick=\"people_editprofilex({$id})\">".$this->getOperationIcon('vcard_edit')." ".$this->_("Edit Profile")."</button>";
		$c_content .= $spacer;
		// $c_content .= "<button onclick=\"people_editcontacts({$id})\">".$this->getOperationIcon('telephone_edit')." ".$this->_("Edit Contacts")."</button>";
		// $c_content .= $spacer;
		//$c_content .= "<button onclick=\"people_edittags({$id})\">".$this->getOperationIcon('pencil')." ".$this->_("Edit Tags")."</button>";
		if($people_allow_tags) {
			$c_content .= "<button onclick=\"people_edittags({$id})\">".$this->_("Edit Tags")."</button>";
			$c_content .= $spacer;
		}
		//$c_content .= "<button onclick=\"people_edit_favourites({$id})\">".$this->getOperationIcon('pencil')." ".$this->_("Edit Favorites")."</button>";
		if($people_allow_favorites) {
			$c_content .= "<button onclick=\"people_edit_favourites({$id})\">".$this->_("Edit Favourites")."</button>";
			$c_content .= $spacer;
		}
		//$c_content .= "<button onclick=\"people_editlove({$id})\">".$this->getOperationIcon('pencil')." ".$this->_("Edit Stuff")."</button>";
		//$c_content .= "<button onclick=\"people_editlove({$id})\">".$this->_("Edit Stuff")."</button>";


		return $c_content;

	}

	function getNotMeOperationsBlock($p) {

		global $group_name;

		$html = '<button onclick="popUp(\'http://grou.ps/msg.do?to='.addslashes($p).'&toname='.addslashes($p).'\')"><img src="http://grou.ps/images/emails.gif" alt="Email" width="16" height="11" /> Send a Private Message to this Person</button>';

		

		return $html;

	}



	function getAmazonMovieLink($keyword) {

		$type = 'dvd';
		$amazon_href = '';

		$amazon_href .= "http://www.amazon.com/exec/obidos/redirect?link_code=ur2&tag=";
		$amazon_href .= $this->AmazonKey;
		$amazon_href .= "&camp=1789&creative=9325&path=external-search%3Fsearch-type=ss%26keyword=";
		$amazon_href .= $keyword;
		$amazon_href .= "%26index=";
		$amazon_href .= $type;

		return $amazon_href;

	}



	function registerLoveStuff($mid,$content) {


		$mid = mysql_real_escape_string($mid);
		$content = mysql_real_escape_string($content);

		/**
         * check for authentication and whether
         * the user really edits its own content
         */
		if(!isset($_SESSION['valid_user'])) {
			return false;
		}

		$username = $_SESSION['valid_user'];
		$userid = _getMemberID($username);
		$membership_id = getMembershipID($this->Database,$userid);

		if($membership_id!=$mid) {
			return false;
		}




		$sql = "INSERT INTO all_stuff VALUES('','love','{$userid}','{$content}','',NOW())";


		$q = & $this->Database->query($sql);

		if (PEAR::isError($q)) {
			die($q->getMessage());
		}

		return $q; // bool

	}

	function registerHealthStuff($mid,$content) {

		_filter_var($mid);
		_filter_var($content);

		/**
         * check for authentication and whether
         * the user really edits its own content
         */
		if(!isset($_SESSION['valid_user'])) {
			return false;
		}
		$username = $_SESSION['valid_user'];
		$userid = _getMemberID($username);
		$membership_id = getMembershipID($this->Database,$userid);

		if($membership_id!=$mid) {
			return false;
		}


		$nowt = date('Y-m-d H:i:s');
		$q = & $this->Database->query("INSERT INTO all_stuff VALUES('','health','{$userid}','{$content}','','{$nowt}')");

		if (PEAR::isError($q)) {
			die($q->getMessage());
		}

		return $q; // bool

	}

	function registerBusinessStuff($mid,$content) {


		_filter_var($mid);
		_filter_var($content);


		/**
         * check for authentication and whether
         * the user really edits its own content
         */
		if(!isset($_SESSION['valid_user'])) {
			return false;
		}
		$username = $_SESSION['valid_user'];
		$userid = _getMemberID($username);
		$membership_id = getMembershipID($this->Database,$userid);

		if($membership_id!=$mid) {
			return false;
		}

		$nowt = date('Y-m-d H:i:s');
		$q = & $this->Database->query("INSERT INTO all_stuff VALUES('','business','{$userid}','{$content}','','{$nowt}')");

		if (PEAR::isError($q)) {
			die($q->getMessage());
		}

		return $q; // bool

	}





	/******
	* WARNING:
	* If there's a problem here
	* it may be trigerred by entering faulty values in
	* (especially) date field.
	*/
	function registerProfile($mid,$motto,$birthday,$sexe,$sorientation,$mstatus,$occupation,$nationality,$nationality2,$erace,$religion,$fsongs,$fsingers,$fmovies,$factors,$fbooks,$fauthors,$fsportsmen,$fartists,$fcities,$fcolors,$hobbies,$fobbies) {

		_filter_var($mid);
		_filter_var($motto);
		_filter_var($birthday);
		_filter_var($sexe);
		_filter_var($sorientation);
		_filter_var($mstatus);
		_filter_var($occupation);
		_filter_var($nationality);
		_filter_var($nationality2);
		_filter_var($erace);
		_filter_var($religion);
		_filter_var($fsongs);
		_filter_var($fsingers);
		_filter_var($fmovies);
		_filter_var($factors);
		_filter_var($fbooks);
		_filter_var($fauthors);
		_filter_var($fsportsmen);
		_filter_var($fartists);
		_filter_var($fcities);
		_filter_var($fcolors);
		_filter_var($hobbies);
		_filter_var($fobbies);



		/**
         * check for authentication and whether
         * the user really edits its own content
         */
		if(!isset($_SESSION['valid_user'])) {
			return false;
		}
		/**
         * TODO: review here, and add $mid == this check
         * can't implement now..
         * it will take too much effort and time
         * So we leave it with these precautions.
         */
		$username = $_SESSION['valid_user'];
		$userid = _getMemberID($username);
		$membership_id = getMembershipID($this->Database,$userid);

		if($membership_id!=$mid) {
			return false;
		}



		/**
         * first insert row
         * if there is no
         */

		$sql = "SELECT COUNT(`profile_id`) FROM `profiles` WHERE member_id='{$userid}';";
		$q = $this->Database->getOne($sql);
		if (PEAR::isError($q)) {
			die($q->getMessage());
		}

		if($q==0) {
			$sql = 'INSERT INTO `profiles` (`profile_id`, `group_id`, `member_id`, `modified_on`, `favorite_songs`, `favorite_singers`, `favorite_movies`, `favorite_actors`, `favorite_books`, `favorite_authors`, `favorite_sportsmen`, `favorite_artists`, `favorite_cities`, `favorite_colors`, `motto`, `adored_people`, `birthday`, `religion`, `nationality`, `second_nationality`, `ethnic_race`, `marital_status`, `children`, `sexe`, `sexual_orientation`, `occupation`, `hobbies`, `fobbies`) VALUES (NULL, \''.$userid.'\', \'0000-00-00 00:00:00\', \'\', \'\', \'\', \'\', \'\', \'\', \'\', \'\', \'\', \'\', \'\', \'\', \'0000-00-00\', \'\', \'\', \'\', \'\', \'\', \'0\', \'\', \'\', \'\', \'\', \'\');';
			$q = $this->Database->query($sql);
			if (PEAR::isError($q)) {
				die($q->getMessage());
			}

			if(!$q||$this->Database->affectedRows()==0)
			return false;
		}


		/**
         * and now let the database queries begin
         */
		$query = '';
		$query .= "UPDATE profiles SET ";
		$query .= "fobbies = '{$fobbies}', ";
		$query .= "hobbies = '{$hobbies}', ";
		$query .= "favorite_colors = '{$fcolors}', ";
		$query .= "favorite_cities = '{$fcities}', ";
		$query .= "favorite_artists = '{$fartists}', ";
		$query .= "favorite_sportsmen = '{$fsportsmen}', ";
		$query .= "favorite_authors = '{$fauthors}', ";
		$query .= "favorite_books = '{$fbooks}', ";
		$query .= "favorite_actors = '{$factors}', ";
		$query .= "favorite_movies = '{$fmovies}', ";
		$query .= "favorite_singers = '{$fsingers}', ";
		$query .= "favorite_songs = '{$fsongs}', ";
		$query .= "religion = '{$religion}', ";
		$query .= "ethnic_race = '{$erace}', ";
		$query .= "nationality = '{$nationality}', ";
		$query .= "second_nationality = '{$nationality2}', ";
		$query .= "occupation = '{$occupation}', ";



		// check marital status for possible values
		if($mstatus!='D') {
			if($mstatus=='married'||$mstatus=='never_married'||$mstatus=='divorced') {
				$query .= "marital_status = '{$mstatus}', ";
			}
			else {
				return false;
			}
		}

		// check sexual orientation for possible values
		if($sorientation!='D') {
			if($sorientation=='homosexual'||$sorientation=='heterosexual'||$sorientation=='no_answer'||$sorientation=='bisexual') {
				$query .= "sexual_orientation = '{$sorientation}', ";
			}
			else {
				return false;
			}
		}

		//  check sexe for allowed values
		if($sexe!='D') {
			if($sexe=='male'||$sexe=='female') {
				$query .= "sexe = '{$sexe}', ";
			}
			else {
				return false; // not allowed value
			}
		}



		// birthday check
		// erroneous
		/*include_once('cphplib/cphplib.inc');
		$cphplib = new cphplib();
		if($cphplib->checkBirthday($birthday)) {
		$query .= "birthday = '{$birthday}', ";
		}
		else {
		return false;
		}*/
		// cancel this and do it in your own
		// like...
		if($birthday != "YYYY-MM-DD" && !ereg("([0-9]{4})-([0-9]{2})-([0-9]{2})",$birthday) )
		return false;
		else
		$query .= " birthday = '{$birthday}', ";





		// lastly, motto
		$query .= "motto = '{$motto}' ";

		// and finish the query by the WHERE condition.
		$query .= " WHERE member_id = '{$userid}'";

		$q = & $this->Database->query($query);

		if (PEAR::isError($q)) {
			die($q->getMessage());
		}

		return $q;

	}



	function canShowFavourites($username) {

		$u = $this->getUserObject($username);

		return $u->canShowFavourites();

	}

	function canShowTags($username) {


		$u = $this->getUserObject($username);

		return $u->canShowTags();

	}

	function canShowLoveStuff($username) {

		$u = $this->getUserObject($username);

		return $u->canShowLoveStuff();
	}

	function canShowHealthStuff($username) {

		$u = $this->getUserObject($username);

		return $u->canShowHealthStuff();
	}

	function canShowBusinessStuff($username) {

		$u = $this->getUserObject($username);

		return $u->canShowBusinessStuff();

	}

	function showLoveStuff($username) {

		$u = $this->getUserObject($username);

		return $u->showLoveStuff();

	}

	function showBusinessStuff($username) {

		$u = $this->getUserObject($username);

		return $u->showBusinessStuff();

	}

	function showHealthStuff($username) {

		$u = $this->getUserObject($username);

		return $u->showHealthStuff();

	}

	function showQuotes($username) {

		$u = $this->getUserObject($username);

		return $u->showQuotes();

	}

	function showTags($username) {

		$u = $this->getUserObject($username);

		return $u->showTags();

	}

	function hideLoveStuff($username) {

		$u = $this->getUserObject($username);

		return $u->hideLoveStuff();

	}

	function hideBusinessStuff($username) {

		$u = $this->getUserObject($username);

		return $u->hideBusinessStuff();

	}

	function hideHealthStuff($username) {

		$u = $this->getUserObject($username);

		return $u->hideHelathStuff();

	}

	function hideQuotes($username) {

		$u = $this->getUserObject($username);

		return $u->hideQuotes();

	}

	function hideTags($username) {

		$u = $this->getUserObject($username);

		return $u->hideTags();

	}


	function getUserObject($username) {

		if(!isset($this->UserObjects[$username])) {

			$this->UserObjects[$username] = new User($username);

		}


		return $this->UserObjects[$username];
	}



	function getTags($username) {

		$u = $this->getUserObject($username);

		$tags = $u->getTags();

		// before everything, the field values
		// inf (internalfield)

		$html="";$html = "";

		$html .= <<<EOS
	
	 <div style="position:absolute;visibility:hidden;">
	 
	 
	 <textarea id="inf_tags">{$tags}</textarea>
	 
	 
	 </div>
	 
EOS;

		if(!empty($tags)) {
			$tags_exploded = explode(',',$tags);
			$w = makeCloud2($tags_exploded);
			_filter_res_var($w);
			$html .= "<div>".$w."</div>";
		}
		else {
			$html .= "<center><img src=\"http://grou.ps/images/nodata.png\" alt=\"No Data\" border=\"0\" /></center>";
		}

		return $html;

	}


	function getFavourites($username) {

		global $group_name;
		global $access_isGroupMember;


		_filter_var($username);

		$html = '';

		$options = array();
		$tmp_url = "";
		$tmp_width = "";
		$tmp_height = "";
		$spacer = " &nbsp;&nbsp; ";

		$section_start_1 = "<div style=\"margin-bottom:5px;\"><b>";
		$section_start_2 = "</b></div><div style=\"margin-bottom:15px;\">";
		$section_end = "</div>";

		$u = $this->getUserObject($username);

		$fav_songs = $u->getFavouriteSongs();
		$fav_singers = $u->getFavouriteSingers();
		$fav_movies = $u->getFavouriteMovies();
		$fav_actors = $u->getFavouriteActors();
		$fav_books = $u->getFavouriteBooks();
		$fav_authors = $u->getFavouriteAuthors();
		//$fav_colors = $u->getFavouriteColors();
		$fav_sportsmen = $u->getFavouriteSportsmen();
		$fav_artists = $u->getFavouriteArtists();
		$fav_cities = $u->getFavouriteCities();


		$inf_favsongs = $fav_songs!=null?implode(",", $fav_songs):'';
		$inf_favsingers = $fav_singers!=null?implode(",", $fav_singers):'';
		$inf_favmovies = $fav_movies!=null?implode(",", $fav_movies):'';
		$inf_favactors = $fav_actors!=null?implode(",", $fav_actors):'';
		$inf_favbooks = $fav_books!=null?implode(",", $fav_books):'';
		$inf_favauthors = $fav_authors!=null?implode(",", $fav_authors):'';
		$inf_favsportsmen = $fav_sportsmen!=null?implode(",", $fav_sportsmen):'';
		$inf_favartists = $fav_artists!=null?implode(",", $fav_artists):'';
		$inf_favcities = $fav_cities!=null?implode(",", $fav_cities):'';

		// before everything, the field values
		// inf (internalfield)

		$html .= '<div style="position:absolute;visibility:hidden;">';


		$html .= "<textarea id=\"inf_favsongs\">{$inf_favsongs}</textarea>";
		$html .= "<textarea id=\"inf_favsingers\">{$inf_favsingers}</textarea>";
		$html .= "<textarea id=\"inf_favmovies\">{$inf_favmovies}</textarea>";
		$html .= "<textarea id=\"inf_favactors\">{$inf_favactors}</textarea>";
		$html .= "<textarea id=\"inf_favbooks\">{$inf_favbooks}</textarea>";
		$html .= "<textarea id=\"inf_favauthors\">{$inf_favauthors}</textarea>";
		$html .= "<textarea id=\"inf_favsportsmen\">{$inf_favsportsmen}</textarea>";
		$html .= "<textarea id=\"inf_favartists\">{$inf_favartists}</textarea>";
		$html .= "<textarea id=\"inf_favcities\">{$inf_favcities}</textarea>";

		$html .= '</div>';

		// we'll compare this
		$html2 = $html;


		if($fav_songs!=null) {
			$html .= $section_start_1."Favourite Music".$section_start_2;
			foreach($fav_songs as $fav_song) {

				$options['Keywords'] = $fav_song;
				$options['ResponseGroup'] = 'Images';
				$options['Sort'] = 'psrank';
				$res = $this->Amazon->ItemSearch('Music', $options);

				if(!PEAR::isError($res)) {

					$tmp_url = $res['Item'][0]['SmallImage']['URL'];
					$tmp_height = $res['Item'][0]['SmallImage']['Height']['_content'];
					$tmp_width = $res['Item'][0]['SmallImage']['Width']['_content'];
					$tmp_link = $this->getAmazonMusicLink($fav_song);


					$html .= "<a href=\"{$tmp_link}\" onmouseover=\"showTooltip(event,'Favourite Music:<br />".ucwords($fav_song)."')\" onmouseout=\"hideTooltip()\" >";
					$html .= "<img onmouseover=\"this.style.borderColor='pink';\" onmouseout=\"this.style.borderColor='black';\" src=\"{$tmp_url}\" width=\"{$tmp_width}\" height=\"{$tmp_height}\" alt=\"\" border=\"1\" style=\"border-color:black;\" />";
					$html .= "</a>";
					$html .= $spacer;

				}
				else {

					$html .= "<a href=\"javascript:void()\" onmouseover=\"showTooltip(event,'Favourite Music:<br />".ucwords($fav_song)."')\" onmouseout=\"hideTooltip()\" >";
					$html .= "<img onmouseover=\"this.style.borderColor='pink';\" onmouseout=\"this.style.borderColor='black';\" src=\"http://grou.ps/images/blank.gif\" width=\"65\" height=\"65\" alt=\"\" border=\"1\" style=\"border-color:black;\" />";
					$html .= "</a>";
					$html .= $spacer;
				}

			}
			$html .= $section_end;
		}


		if($fav_singers!=null) {
			$options = null;
			$options = array();
			$html .= $section_start_1."Favourite Musicians".$section_start_2;
			foreach($fav_singers as $fav_singer) {

				$options['Artist'] = $fav_singer;
				$options['ResponseGroup'] = 'Images';
				$options['Sort'] = 'psrank';
				$res = $this->Amazon->ItemSearch('Music', $options);
				$tmp_link = $this->getAmazonMusicLink($fav_singer);

				if(!PEAR::isError($res)) {

					$tmp_url = $res['Item'][0]['SmallImage']['URL'];
					$tmp_height = $res['Item'][0]['SmallImage']['Height']['_content'];
					$tmp_width = $res['Item'][0]['SmallImage']['Width']['_content'];
					$tmp_link = $this->getAmazonMusicLink($fav_singer);

					$html .= "<a href=\"{$tmp_link}\" onmouseover=\"showTooltip(event,'Favourite Musician:<br />".ucwords($fav_singer)."')\" onmouseout=\"hideTooltip()\" >";
					$html .= "<img onmouseover=\"this.style.borderColor='pink';\" onmouseout=\"this.style.borderColor='black';\" src=\"{$tmp_url}\" width=\"{$tmp_width}\" height=\"{$tmp_height}\" alt=\"\" border=\"1\" style=\"border-color:black;\" />";
					$html .= "</a>";
					$html .= $spacer;

				}
				else {

					$html .= "<a href=\"javascript:void()\" onmouseover=\"showTooltip(event,'Favourite Musician:<br />".ucwords($fav_singer)."')\" onmouseout=\"hideTooltip()\" >";
					$html .= "<img onmouseover=\"this.style.borderColor='pink';\" onmouseout=\"this.style.borderColor='black';\" src=\"http://grou.ps/images/blank.gif\" width=\"65\" height=\"65\" alt=\"\" border=\"1\" style=\"border-color:black;\" />";
					$html .= "</a>";
					$html .= $spacer;
				}

			}
			$html .= $section_end;
		}


		if($fav_movies!=null) {
			$options = null;
			$options = array();
			$html .= $section_start_1."Favourite Movies".$section_start_2;
			foreach($fav_movies as $fav_movie) {

				$options['Title'] = $fav_movie;
				$options['ResponseGroup'] = 'Images';
				$options['Sort'] = 'relevancerank';
				$res = $this->Amazon->ItemSearch('DVD', $options);

				if(!PEAR::isError($res)) {

					$tmp_url = $res['Item'][0]['SmallImage']['URL'];
					$tmp_height = $res['Item'][0]['SmallImage']['Height']['_content'];
					$tmp_width = $res['Item'][0]['SmallImage']['Width']['_content'];
					$tmp_link = $this->getAmazonMovieLink($fav_movie);

					$html .= "<a href=\"{$tmp_link}\" onmouseover=\"showTooltip(event,'Favourite Movie:<br />".ucwords($fav_movie)."')\" onmouseout=\"hideTooltip()\" >";
					$html .= "<img onmouseover=\"this.style.borderColor='pink';\" onmouseout=\"this.style.borderColor='black';\" src=\"{$tmp_url}\" width=\"{$tmp_width}\" height=\"{$tmp_height}\" alt=\"\" border=\"1\" style=\"border-color:black;\" />";
					$html .= "</a>";
					$html .= $spacer;

				}
				else {

					$html .= "<a href=\"javascript:void()\" onmouseover=\"showTooltip(event,'Favourite Movie:<br />".ucwords($fav_movie)."')\" onmouseout=\"hideTooltip()\" >";
					$html .= "<img onmouseover=\"this.style.borderColor='pink';\" onmouseout=\"this.style.borderColor='black';\" src=\"http://grou.ps/images/blank.gif\" width=\"65\" height=\"65\" alt=\"\" border=\"1\" style=\"border-color:black;\" />";
					$html .= "</a>";
					$html .= $spacer;
				}

			}
			$html .= $section_end;
		}


		if($fav_actors!=null) {
			$options = null;
			$options = array();
			$html .= $section_start_1."Favourite Actors".$section_start_2;
			foreach($fav_actors as $fav_actor) {

				$options['Actor'] = $fav_actor;
				$options['ResponseGroup'] = 'Images';
				$options['Sort'] = 'relevancerank';
				$res = $this->Amazon->ItemSearch('DVD', $options);

				if(!PEAR::isError($res)) {

					$tmp_url = $res['Item'][0]['SmallImage']['URL'];
					$tmp_height = $res['Item'][0]['SmallImage']['Height']['_content'];
					$tmp_width = $res['Item'][0]['SmallImage']['Width']['_content'];
					$tmp_link = $this->getAmazonMovieLink($fav_actor);

					$html .= "<a href=\"{$tmp_link}\" onmouseover=\"showTooltip(event,'Favourite Actor:<br />".ucwords($fav_actor)."')\" onmouseout=\"hideTooltip()\" >";
					$html .= "<img onmouseover=\"this.style.borderColor='pink';\" onmouseout=\"this.style.borderColor='black';\" src=\"{$tmp_url}\" width=\"{$tmp_width}\" height=\"{$tmp_height}\" alt=\"\" border=\"1\" style=\"border-color:black;\" />";
					$html .= "</a>";
					$html .= $spacer;

				}
				else {

					$html .= "<a href=\"javascript:void()\" onmouseover=\"showTooltip(event,'Favourite Actor:<br />".ucwords($fav_actor)."')\" onmouseout=\"hideTooltip()\" >";
					$html .= "<img onmouseover=\"this.style.borderColor='pink';\" onmouseout=\"this.style.borderColor='black';\" src=\"http://grou.ps/images/blank.gif\" width=\"65\" height=\"65\" alt=\"\" border=\"1\" style=\"border-color:black;\" />";
					$html .= "</a>";
					$html .= $spacer;
				}

			}
			$html .= $section_end;
		}



		if($fav_books!=null) {
			$options = null;
			$options = array();
			$html .= $section_start_1."Favourite Books".$section_start_2;
			foreach($fav_books as $fav_book) {

				$options['Title'] = $fav_book;
				$options['ResponseGroup'] = 'Images';
				$options['Sort'] = 'relevancerank';
				$res = $this->Amazon->ItemSearch('Books', $options);

				if(!PEAR::isError($res)) {

					$tmp_url = $res['Item'][0]['SmallImage']['URL'];
					$tmp_height = $res['Item'][0]['SmallImage']['Height']['_content'];
					$tmp_width = $res['Item'][0]['SmallImage']['Width']['_content'];
					$tmp_link = $this->getAmazonBookLink($fav_book);

					$html .= "<a href=\"{$tmp_link}\" onmouseover=\"showTooltip(event,'Favourite Book:<br />".ucwords($fav_book)."')\" onmouseout=\"hideTooltip()\" >";
					$html .= "<img onmouseover=\"this.style.borderColor='pink';\" onmouseout=\"this.style.borderColor='black';\" src=\"{$tmp_url}\" width=\"{$tmp_width}\" height=\"{$tmp_height}\" alt=\"\" border=\"1\" style=\"border-color:black;\" />";
					$html .= "</a>";
					$html .= $spacer;

				}
				else {

					$html .= "<a href=\"javascript:void()\" onmouseover=\"showTooltip(event,'Favourite Book:<br />".ucwords($fav_book)."')\" onmouseout=\"hideTooltip()\" >";
					$html .= "<img onmouseover=\"this.style.borderColor='pink';\" onmouseout=\"this.style.borderColor='black';\" src=\"http://grou.ps/images/blank.gif\" width=\"65\" height=\"65\" alt=\"\" border=\"1\" style=\"border-color:black;\" />";
					$html .= "</a>";
					$html .= $spacer;
				}

			}
			$html .= $section_end;
		}



		if($fav_authors!=null) {
			$options = null;
			$options = array();
			$html .= $section_start_1."Favourite Authors".$section_start_2;
			foreach($fav_authors as $fav_author) {

				$options['Author'] = $fav_author;
				$options['ResponseGroup'] = 'Images';
				$options['Sort'] = 'relevancerank';
				$res = $this->Amazon->ItemSearch('Books', $options);

				if(!PEAR::isError($res)) {

					$tmp_url = $res['Item'][0]['SmallImage']['URL'];
					$tmp_height = $res['Item'][0]['SmallImage']['Height']['_content'];
					$tmp_width = $res['Item'][0]['SmallImage']['Width']['_content'];
					$tmp_link = $this->getAmazonBookLink($fav_book);

					$html .= "<a href=\"{$tmp_link}\" onmouseover=\"showTooltip(event,'Favourite Author:<br />".ucwords($fav_author)."')\" onmouseout=\"hideTooltip()\" >";
					$html .= "<img onmouseover=\"this.style.borderColor='pink';\" onmouseout=\"this.style.borderColor='black';\" src=\"{$tmp_url}\" width=\"{$tmp_width}\" height=\"{$tmp_height}\" alt=\"\" border=\"1\" style=\"border-color:black;\" />";
					$html .= "</a>";
					$html .= $spacer;

				}
				else {

					$html .= "<a href=\"javascript:void()\" onmouseover=\"showTooltip(event,'Favourite Author:<br />".ucwords($fav_author)."')\" onmouseout=\"hideTooltip()\" >";
					$html .= "<img onmouseover=\"this.style.borderColor='pink';\" onmouseout=\"this.style.borderColor='black';\" src=\"http://grou.ps/images/blank.gif\" width=\"65\" height=\"65\" alt=\"\" border=\"1\" style=\"border-color:black;\" />";
					$html .= "</a>";
					$html .= $spacer;
				}

			}
			$html .= $section_end;
		}



		if($fav_sportsmen!=null) {
			$options = null;
			$options = array();
			$html .= $section_start_1."Favourite Sportsmen".$section_start_2;
			foreach($fav_sportsmen as $fav_sportsman) {

				$options['Keywords'] = $fav_sportsman;
				$options['ResponseGroup'] = 'Images';
				$res = $this->Amazon->ItemSearch('Books', $options);

				if(!PEAR::isError($res)) {

					$tmp_url = $res['Item'][0]['SmallImage']['URL'];
					$tmp_height = $res['Item'][0]['SmallImage']['Height']['_content'];
					$tmp_width = $res['Item'][0]['SmallImage']['Width']['_content'];
					$tmp_link = $this->getAmazonBookLink($fav_sportsman);

					$html .= "<a href=\"{$tmp_link}\" onmouseover=\"showTooltip(event,'Favourite Sportsman:<br />".ucwords($fav_sportsman)."')\" onmouseout=\"hideTooltip()\" >";
					$html .= "<img onmouseover=\"this.style.borderColor='pink';\" onmouseout=\"this.style.borderColor='black';\" src=\"{$tmp_url}\" width=\"{$tmp_width}\" height=\"{$tmp_height}\" alt=\"\" border=\"1\" style=\"border-color:black;\" />";
					$html .= "</a>";
					$html .= $spacer;

				}
				else {

					$html .= "<a href=\"javascript:void()\" onmouseover=\"showTooltip(event,'Favourite Sportsmen:<br />".ucwords($fav_sportsman)."')\" onmouseout=\"hideTooltip()\" >";
					$html .= "<img onmouseover=\"this.style.borderColor='pink';\" onmouseout=\"this.style.borderColor='black';\" src=\"http://grou.ps/images/blank.gif\" width=\"65\" height=\"65\" alt=\"\" border=\"1\" style=\"border-color:black;\" />";
					$html .= "</a>";
					$html .= $spacer;
				}

			}
			$html .= $section_end;
		}




		if($fav_artists!=null) {
			$options = null;
			$options = array();
			$html .= $section_start_1."Favourite Artists".$section_start_2;
			foreach($fav_artists as $fav_artist) {

				$options['Keywords'] = $fav_artist;
				$options['ResponseGroup'] = 'Images';
				$res = $this->Amazon->ItemSearch('Books', $options);


				if(!PEAR::isError($res)) {

					$tmp_url = $res['Item'][0]['SmallImage']['URL'];
					$tmp_height = $res['Item'][0]['SmallImage']['Height']['_content'];
					$tmp_width = $res['Item'][0]['SmallImage']['Width']['_content'];
					$tmp_link = $this->getAmazonBookLink($fav_artist);

					$html .= "<a href=\"{$tmp_link}\" onmouseover=\"showTooltip(event,'Favourite Artist:<br />".ucwords($fav_artist)."')\" onmouseout=\"hideTooltip()\" >";
					$html .= "<img onmouseover=\"this.style.borderColor='pink';\" onmouseout=\"this.style.borderColor='black';\" src=\"{$tmp_url}\" width=\"{$tmp_width}\" height=\"{$tmp_height}\" alt=\"\" border=\"1\" style=\"border-color:black;\" />";
					$html .= "</a>";
					$html .= $spacer;

				}
				else {

					$html .= "<a href=\"javascript:void()\" onmouseover=\"showTooltip(event,'Favourite Artist:<br />".ucwords($fav_artist)."')\" onmouseout=\"hideTooltip()\" >";
					$html .= "<img onmouseover=\"this.style.borderColor='pink';\" onmouseout=\"this.style.borderColor='black';\" src=\"http://grou.ps/images/blank.gif\" width=\"65\" height=\"65\" alt=\"\" border=\"1\" style=\"border-color:black;\" />";
					$html .= "</a>";
					$html .= $spacer;
				}

			}
			$html .= $section_end;
		}










		if($fav_cities!=null) {
			$options = null;
			$options = array();
			$html .= $section_start_1."Favourite Cities".$section_start_2;
			foreach($fav_cities as $fav_city) {

				$options['Keywords'] = $fav_city;
				$options['ResponseGroup'] = 'Images';
				$res = $this->Amazon->ItemSearch('Books', $options);


				if(!PEAR::isError($res)) {

					$tmp_url = $res['Item'][0]['SmallImage']['URL'];
					$tmp_height = $res['Item'][0]['SmallImage']['Height']['_content'];
					$tmp_width = $res['Item'][0]['SmallImage']['Width']['_content'];
					$tmp_link = $this->getAmazonBookLink($fav_city);

					$html .= "<a href=\"{$tmp_link}\" onmouseover=\"showTooltip(event,'Favourite City:<br />".ucwords($fav_city)."')\" onmouseout=\"hideTooltip()\" >";
					$html .= "<img onmouseover=\"this.style.borderColor='pink';\" onmouseout=\"this.style.borderColor='black';\" src=\"{$tmp_url}\" width=\"{$tmp_width}\" height=\"{$tmp_height}\" alt=\"\" border=\"1\" style=\"border-color:black;\" />";
					$html .= "</a>";
					$html .= $spacer;

				}
				else {

					$html .= "<a href=\"javascript:void()\" onmouseover=\"showTooltip(event,'Favourite City:<br />".ucwords($fav_city)."')\" onmouseout=\"hideTooltip()\" >";
					$html .= "<img onmouseover=\"this.style.borderColor='pink';\" onmouseout=\"this.style.borderColor='black';\" src=\"http://grou.ps/images/blank.gif\" width=\"65\" height=\"65\" alt=\"\" border=\"1\" style=\"border-color:black;\" />";
					$html .= "</a>";
					$html .= $spacer;
				}

			}
			$html .= $section_end;
		}

		if(empty($html))
		$html = "<center><img src=\"http://grou.ps/images/nodata.png\" alt=\"No Data\" border=\"0\" /></center>";






		if($html==$html2) // nodata
		$html .= "<center><img src=\"http://grou.ps/images/nodata.png\" alt=\"No Data\" border=\"0\" /></center>";



		return $html;




	}




	function addQuote($quote) {

		_filter_var($quote);
		global $access_isGroupMember;

		if(!isset($_SESSION['valid_user'])||!$access_isGroupMember)
		return false;

		$uname = $_SESSION['valid_user'];
		$uid = _getMemberID($uname); // from Access.php

		$sql = 'INSERT INTO `quotes` (`quote_id`, `member_id`, `gnippet_id`, `quote`, `added_on`) VALUES (NULL, \''.$uid.'\', \''.$quote.'\', NOW());';

		$q = $this->Database->query($sql);

		if (PEAR::isError($q)) {
			die("Error No 100: ".$q->getMessage());
		}

		return $q;

	}


	function getComments($member_id) {

		_filter_var($member_id);

		// keeps track of duplicate entries
		// because we don't delete old entries, but show only
		// one of them, actually the last one
		$has_comment = array();
		$comments_res = array(); // we'll return this

		$comments = & $this->Database->getAll("SELECT author_id, comment, added_on,comment_id FROM comments WHERE member_id = '{$member_id}' ORDER BY added_on DESC",array(), 2 /*assoc*/);

		if (PEAR::isError($comments)) {
			die($comments->getMessage());
		}

		if(sizeof($comments)>0) {

			$_now = date('Y-m-d H:i:s');

			foreach($comments as $i=>$comment) {

				$user_id = $comment["author_id"];

				// multiple comments allowed now,
				// because this is wall now
				//if(!in_array($user_id,$has_comment)) {

					$has_comment[] = $user_id;

					$username = _getMemberUsername($user_id);
					$user = new User($username);
					$usersnamesurname = $user->getNameSurname();
					$usersicon = $user->getMiniIcon();

					// from GeneralFunctions.php

					$timediff = getStyledDateDiff($comment['added_on'],$_now);

					$comments_res[$i]["author"] = $usersnamesurname;
					$comments_res[$i]["author_username"] = $username;
					$comments_res[$i]["comment"] = $comment["comment"];
					$comments_res[$i]["icon"] = $usersicon;
					$comments_res[$i]["date"] = $timediff;
					$comments_res[$i]["id"] = $comment["comment_id"];

				//}


			}

		}

		return $comments_res;

	}





	function getWall($member_id) {

		global $access_isGroupAdmin;
		global $access_isGroupMember;

		_filter_var($member_id);

		// now filter member_id
		//  if it is set to be -1
		// then this means we'll use a random
		// value
		// FOR NOW: not random; the first user..
		if($member_id==-1) {
			$ms = $this->getPeople();
			if($ms==null||sizeof($ms)<1) {
				die('Error'); // should be impossible!
			}
			$member_id = $ms[0]['id'];
		}


		$html = ""; // we'll return this


		
		
		$comments = $this->getComments($member_id);

		if(sizeof($comments)>0) {
			
					if($access_isGroupMember) {
			$html .= <<<EOS
        	 <div style="text-align:center;">
			<form style="margin:0">
			<input type="hidden" id="wallid" value="{$member_id}" />
        	<textarea id="mycomment" style="width:95%;height:100px;" onfocus="empty_wall(this)">Write something on my wall...</textarea>
        	<br /><input type="button" value="Post" style="width:95%" onclick="make_wall_comment()" />
        	</form>
        	</div>
        	
        	<p>&nbsp;</p>
        	
EOS;
}

			foreach($comments as $comment) {

				$cmt = $comment['comment'];
				//parse_bbcode($cmt);

				_filter_res_var($cmt);

				$html .= "<div style=\"margin-bottom:20px;\">";
				$html .= "<div style=\"margin-bottom:5px;font-weight:bold;font-size:1.1em;background:#ddd;\">";
				$html .= "<img src=\"{$comment['icon']}\" border=\"0\" width=\"16\" height=\"16\" alt=\"\" /> ";
				$html .= "<a href=\"javascript:void(popUp('http://grou.ps/msg.do?to={$comment['author_username']}&group={}&toname={$comment['author']}'))\">{$comment['author']}</a> {$comment['date']}</div>";
				$html .= $cmt;
				
				if($access_isGroupAdmin||($access_isGroupMember&&($this->isCommentOwner($comment['id'])||($this->isCommentSubject($comment['id']))))) {
					$html .= "<p><button onclick=\"comment_delete({$comment['id']})\"><img src=\"http://grou.ps/images/delete.gif\" align=\"absbottom\"/> Delete</button></p>";
				}
				
				
				$html .= "</div>";
				//$html .= "<hr />";

			}

		}
		else {

					if($access_isGroupMember) {
			$html .= <<<EOS
        	
        	<script>
        	var initial_wall_value = 1;
        	function empty_wall(obj) {
        		if(initial_wall_value==1) {
        			obj.value="";
        			initial_wall_value = 0;	
        		}
        	}
        	</script>
        	
			<form style="margin:0">
			<input type="hidden" id="wallid" value="{$member_id}" />
        	<p><textarea id="mycomment" style="width:100%;height:100px;" onfocus="empty_wall(this)">Be the first to write something on my wall...</textarea></p>
        	<p><input type="button" value="Post" style="width:100%" onclick="make_wall_comment()" /></p>
        	</form>
        	
EOS;
}
else {
			$html .= "<center><img src=\"http://grou.ps/images/nodata.png\" alt=\"No Data\" border=\"0\" /></center>";
}

		}



return $html;

	}

	
	function getSearchBox($searched="") {
		
        $html = ""; // we'll return this..
        
        $html .= "<form style=\"display:inline;\" method=\"POST\" action=\"http://grou.ps/{}/people/search\">";
        //$html .= "<form style=\"display:inline;\" method=\"POST\" action=\"http://grou.ps/index42.php?group_name={}&function=people&obj1=search\">";
        $html .= "<input type=\"text\" name=\"psq\" onfocus=\"inputfocus(this)\" onblur=\"inputblur(this)\" value=\"{$searched}\" /> ";
        $html .= "&nbsp; <input type=\"submit\" value=\"Search\" />";       	
        $html .= "</form>";
        return $html;
		
		
	}



function pages($pagenum=1) {
    	
global $service_host;
    	
    	$sql = "SELECT COUNT(membership_id) FROM memberships";
    	$res = $this->Database->getOne($sql);
    	if(PEAR::isError($res))
    		$res = 1;
    	
    	$firstpage=1;
    	$lastpage = ceil($res/20);
    	
    	$html = '<table border="0" cellpadding="5" cellspacing="0" width="100%"><tr>';
    	
    	$html .= '<td width="50" align="left">';
    	
    	if($pagenum>$firstpage) {
    		if($catid=="")
    			$html .= '<button onclick="window.location.href=\''.$service_host.'people/page/'.($pagenum-1).'\'" style="width:50px;font-weight:bold;">&lt;</button>';
    		else 
    			$html .= '<button onclick="window.location.href=\''.$service_host.'\'people/page/'.($pagenum-1).'c'.$catid.'\'" style="width:50px;font-weight:bold;">&lt;</button>';
    	}
    	else {
    		$html .= '<button disabled style="width:50px;font-weight:bold;">&lt;</button>';
		}
    	
    	$html .= '</td>';
    	
    	$html .= '<td width="100%" align="center"><strong>Page '.$pagenum.'</strong></td>';
    	
    	$html .= '<td width="50" align="right">';
    	
    	if($lastpage>$pagenum) {
    		if($catid=="")
    			$html .= '<button onclick="window.location.href=\''.$service_host.'people/page/'.($pagenum+1).'\'" style="width:50px;font-weight:bold;">&gt;</button>';
    		else 
    			$html .= '<button onclick="window.location.href=\''.$service_host.'people/page/'.($pagenum+1).'c'.$catid.'\'" style="width:50px;font-weight:bold;">&gt;</button>';
    	}
    	else {
    		$html .= '<button disabled style="width:50px;font-weight:bold;">&gt;</button>';
    	}
    	
    	$html .= '</td>';
    	
    	$html .= '</tr></table>';
    	
    	return $html;
    		
    }
    
	
	

	function removeQuote($quote_id) {

		_filter_var($quote_id);
		global $access_isGroupMember;

		if(!isset($_SESSION['valid_user'])||!$access_isGroupMember)
		return false;

		$uname = $_SESSION['valid_user'];
		$uid = _getMemberID($uname); // from Access.php

		$sql= "DELETE FROM quotes WHERE quote_id='{$quote_id}' AND member_id='{$uid}';";

		$q = $this->Database->query($sql);

		if (PEAR::isError($q)) {
			die("Error No 105640: ".$q->getMessage());
		}

		return $q;

	}

	function insertComment($member_id,$comment) {

		global $access_isGroupMember;

		if(!$access_isGroupMember)
		return false;

		$author = "";

		if(isset($_SESSION['valid_user'])) {
			$author = _getMemberID($_SESSION['valid_user']);
		}
		else {
			return false;
		}

		$sql = 'INSERT INTO `comments` (`comment_id`, `author_id`, `member_id`, `comment`, `added_on`) VALUES (NULL, ?, ?, ?, ?, NOW());';

		$q = & $this->Database->query($sql,array($author,$member_id,$comment));

		if (PEAR::isError($q)) {
			return false;
		}

		return true;

	}


	function isCommentOwner($c_id) {

		if(isset($_SESSION['valid_user'])) {
			$author = _getMemberID($_SESSION['valid_user']);
		}
		else {
			return false;
		}

		$sql = 'SELECT COUNT(comment_id) FROM comments WHERE `comment_id`=? AND `author_id`=?';

		$q = & $this->Database->getOne($sql,array($c_id,$author));

		if (PEAR::isError($q)) {
			return false;
		}

		return $q==1;
	}

	function deleteComment($c_id) {

		global $access_isGroupAdmin, $access_isGroupMember;

		if($access_isGroupAdmin||($access_isGroupMember&&($this->isCommentOwner($c_id)||$this->isCommentSubject($c_id)))) {

			$sql = 'DELETE FROM comments WHERE `comment_id`=? ';

			$q = & $this->Database->query($sql,array($c_id));

			if (PEAR::isError($q)) {
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


	function isCommentSubject($c_id) {
		
		if(isset($_SESSION['valid_user'])) {
			$author = _getMemberID($_SESSION['valid_user']);
		}
		else {
			return false;
		}

		$sql = 'SELECT COUNT(comment_id) FROM comments WHERE `comment_id`=? AND `member_id`=?';

		$q = & $this->Database->getOne($sql,array($c_id,$author));

		if (PEAR::isError($q)) {
			return false;
		}

		return $q==1;
	}



	/**
     * returns the qupte of the user
     * if the quote_author is left null; returns any quote
     * from the group
     * otherwise returns a quote of the specified user.
     *
     * FIX: we have fixed this function
     * the problemous lines are stated with "//"
     *
     * @param quote_author author of the quote
     * @returns string the quote
     */
	function getQuotes($quote_author) {

		global $access_isGroupMember;

		if( !$access_isGroupMember || !isset($_SESSION['valid_user']) ) {
			return null;
		}

		$uid = _getMemberID($quote_author); // from Access.php

		$sql = 'SELECT quote,quote_id FROM `quotes` WHERE member_id=\''.$uid.'\';';

		$q = $this->Database->getAll($sql,array(),2/*ASSOC*/);

		if (PEAR::isError($q)) {
			die("Error No 160: ".$q->getMessage());
		}


		if(sizeof($q)==0)
		return null;
		else {
			return $q;
		}


		// }
		// else
		//    return "";


	}



	function getQuotesHTML($quote_author) {

		$empty_html = "<center><img src=\"http://grou.ps/images/nodata.png\" alt=\"No Data\" border=\"0\" /></center>";

		global $access_isGroupMember;


		if( !$access_isGroupMember || !isset($_SESSION['valid_user']) ) {
			return $empty_html;
		}

		$res = $this->getQuotes($quote_author);

		$html = "";

		if($res!=null) {

			$html .= "<div id=\"personal_quotes\">";
			$html .= "<input type=\"hidden\" id=\"noquoteyet\" value=\"0\" />";
			foreach($res as $r) {

				$html .= "<div id=\"personal_quote_{$r['quote_id']}\" style=\"margin-bottom:3px;\">";
				$html .= "\"{$r['quote']}\" &nbsp;&nbsp;&nbsp; [ ".$this->getOperationIcon('delete')."<a href=\"javascript:void(removeQuote({$r['quote_id']}))\">".$this->_("Remove")."</a> ]";
				$html .= "</div>";
			}
			$html .= "</div>";

		}
		else {
			$html .= "<div id=\"personal_quotes\">";
			$html .= "<input type=\"hidden\" id=\"noquoteyet\" value=\"1\" />";
			$html .= $empty_html;
			$html .= "</div>";

		}

		$html .= "<div class=\"box_mid_ops\" style=\"margin-top:25px;\"><div class=\"content\">";
		$html .= "[ ".$this->getOperationIcon('sound_add')."<a href=\"javascript:void(addQuote())\">".$this->_("Add Quote")."</a> ]";
		$html .= "</div></div>";

		return $html;

	}


	function getOperationIcon($icon,$id="") {

		if(empty($id))
		$html = "<img src=\"http://grou.ps/images/{$icon}.gif\" align=\"absbottom\" vspace=\"0\" alt=\"\" width=\"16\" height=\"16\" border=\"\" /> ";
		else
		$html = "<img src=\"http://grou.ps/images/{$icon}.gif\" align=\"absbottom\" vspace=\"0\" alt=\"\" width=\"16\" height=\"16\" border=\"\" id=\"{$id}\" /> ";

		return $html;

	}







	function getPeopleOptions($uname) {

		global $access_isGroupMember;

		_filter_var($uname);
		$res = "";

		if(isset($_SESSION['valid_user'])&&$access_isGroupMember) {

			if($uname!=$_SESSION['valid_user']) {

				$u = new User($_SESSION['valid_user']);
				$uid = _getMemberID($uname);


				if(!$u->isWatching($uid)) {
					$res .= "<img align=\"absbottom\" src=\"http://grou.ps/images/eye.gif\" alt=\"".$this->_("Add to Your Watchlist")."\" width=\"16\" height=\"16\" border=\"0\" /> ";
					$res .= "<a href=\"javascript:void(add_watchlist('{$uname}'))\">".$this->_("Add to Your Watchlist")."</a>";
				}
				else {
					$res .= "<img align=\"absbottom\" src=\"http://grou.ps/images/bell.gif\" alt=\"".$this->_("In Your Watchlist")."\" width=\"16\" height=\"16\" border=\"0\" /> ";
					$res .= $this->_("In Your Watchlist");

				}

			}
		}

		return $res;

	}



	function getAjaxFunctions() {

		global $access_isGroupMember;
		global $people_allow_sexe, $people_allow_birthday, $people_allow_website,
			$people_allow_nation;



		$functions = '';





		if(isset($_SESSION['valid_user'])&&$access_isGroupMember) {
			$u = new User($_SESSION['valid_user']);
			$mid = $u->getMembershipID();
			$functions .= "var current_user_membership_id = {$mid};\n\n";
		}
		else {
			$functions .= "var current_user_membership_id = -1;\n\n";
		}





		$functions .= <<<EOS
        
        
        function removeQuote(rid) {
            
            var txt = '';
EOS;

		$functions .= "txt += '".$this->_("Do you really want to remove this quote?")."';\n";



		$functions .= " txt += '<br /><br />';\n";
		$functions .= "txt += '<input type=\"button\" value=\"".$this->_("Yes")."\" onclick=\"removeQuoteYes('+rid+')\" /> ';\n";
		$functions .= "txt += '<input type=\"button\" value=\"".$this->_("No")."\" onclick=\"close_ajax_popup()\" /> ';\n";


		$functions .= <<<EOS
    
            popup_ajax(txt);
            
        }
        
        var removed_quote_id = -1;
        function removeQuoteYes(rid) {
            removed_quote_id = rid;
            make_busy();
            x_removeQuote(rid,removeQuoteRes);
            
             }
        
                function removeQuoteRes(res) {
                    if(res=='1') {
                        xDisplay("personal_quote_"+removed_quote_id,"none");
EOS;
		$functions .= "popup_ajax('".$this->_("Quote removed. <a href=\"javascript:void(close_ajax_popup())\">Click</a> to continue...")."');\n";
		$functions .= "}                    else {\n";
		$functions .= "popup_ajax('There was a problem while removing the quote. <a href=\"javascript:void(close_ajax_popup())\">Click</a> try again...');\n";

		$functions .= <<<EOS
    }
             }
        
        
        
EOS;


		$functions .= "function addQuote() {\n";
		$functions .= "\t var txt = '';\n";
		$functions .= "\t txt += '".$this->_("Your quotes will be displayed randomly on top of this group pages")."<br /><br />';\n";
		$functions .= "\t txt += '<form><textarea required=\"1\" realname=\"Quote\" onblur=\"inputblur(this)\" onfocus=\"inputfocus(this)\" id=\"your_quote\" style=\"width:250px;height:100px;\"></textarea><br /><br />';\n";
		$functions .= "\t txt += '<input type=\"button\" value=\"".$this->_("Submit")."\" onclick=\"addQuoteForSure(this.form)\" /></form>';\n";
		$functions .= "\t popup_ajax(txt);\n";
		$functions .= "}\n\n";

		$functions .= "var saved_quote = '';\n";
		$functions .= "function addQuoteForSure(formobj) {\n";
		$functions .= "\t if(!validateCompleteForm(formobj,'error')) return false;\n";
		$functions .= "\t var quote = xGetElementById('your_quote').value;\n";
		$functions .= "\t saved_quote = quote;\n";
		$functions .= "\t make_busy();\n";
		$functions .= "\t x_addQuoteX(quote,quoteAdded);\n";
		$functions .= "}\n\n";

		$functions .= "function quoteAdded(res) {\n";
		$functions .= "\t var txt = '';\n";
		$functions .= "\t if(res=='1') {\n";
		//$functions .= "xWinScrollTo(window,0,0,0);\n";
		$functions .= "if(xGetElementById('noquoteyet')&&xGetElementById('noquoteyet').value==1) xInnerHtml('personal_quotes','');\n";
		$functions .= "xInnerHtml('personal_quotes',xInnerHtml('personal_quotes')+'<div style=\"margin-bottom:3px;\">\"'+saved_quote+'\"</div>');";
		//$functions .= "\t\t txt += 'Your quote has been added.<br /><a href=\"javascript:void(location.reload())\">Click</a> to continue...';\n";
		// we don't reload.. no need to do it..
		$functions .= "\t\t txt += 'Your quote has been added.<br /><a href=\"javascript:void(close_ajax_popup())\">Click</a> to continue...';\n";
		$functions .= "\t } else\n";
		$functions .= "\t\t txt += 'Failure!<br /><a href=\"javascript:void(close_ajax_popup())\">Click</a> to try again...';\n";
		$functions .= "\t popup_ajax(txt)\n";
		$functions .= "}\n\n";













		$functions .= "function redecorate_peopleblock(res) {\n"
		. "\txGetElementById('block_title_no_2').innerHTML = 'People';\n"
		. "\txGetElementById('block_no_2').innerHTML = res;showAllBlocks();\n"
		. "}\n";

		$functions .= "function redecorate_lovestuff(res) {\n"
		//. "\t xGetElementById('block_title_no_6').innerHTML = 'Love Stuff';\n"

		."\t var isOpen = res.substring(0,2)=='++';\n"
		. "\t var obj;\n"
		. "\t if(xGetElementById('top_block_no_6')) obj = xGetElementById('top_block_no_6');\n"
		. "\t else obj = xGetElementById('hidden_block_6');\n"
		. "\t if(isOpen) obj.style.display='block';\n"
		. "\t else obj.style.display='none';\n"
		. "\t xGetElementById('block_no_6').innerHTML = res.substring(2);showAllBlocks();\n"
		. "}\n";

		$functions .= "function redecorate_healthstuff(res) {\n"
		."\t var isOpen = res.substring(0,2)=='++';\n"
		. "\t var obj;\n"
		. "\t if(xGetElementById('top_block_no_8')) obj = xGetElementById('top_block_no_8');\n"
		. "\t else obj = xGetElementById('hidden_block_8');\n"
		. "\t if(isOpen) obj.style.display='block';\n"
		. "\t else obj.style.display='none';\n"

		. "\txGetElementById('block_no_8').innerHTML = res.substring(2);showAllBlocks();\n"

		. "}\n";

		$functions .= "function redecorate_businessstuff(res) {\n"
		."\t var isOpen = res.substring(0,2)=='++';\n"
		. "\t var obj;\n"
		. "\t if(xGetElementById('top_block_no_7')) obj = xGetElementById('top_block_no_7');\n"
		. "\t else obj = xGetElementById('hidden_block_7');\n"
		. "\t if(isOpen) obj.style.display='block';\n"
		. "\t else obj.style.display='none';\n"

		. "\t xGetElementById('block_no_7').innerHTML = res.substring(2);showAllBlocks();\n"
		. "}\n";



		$functions .= "function redecorate_tags(res) {\n"
		."\t var isOpen = res.substring(0,2)=='++';\n"
		. "\t var obj;\n"
		. "\t if(xGetElementById('top_block_no_4')) obj = xGetElementById('top_block_no_4');\n"
		. "\t else obj = xGetElementById('hidden_block_4');\n"
		. "\t if(isOpen) obj.style.display='block';\n"
		. "\t else obj.style.display='none';\n"

		. "\t xGetElementById('block_no_4').innerHTML = res.substring(2);showAllBlocks();\n"
		. "}\n";


		$functions .= "function redecorate_favourites(res) {\n"
		."\t var isOpen = res.substring(0,2)=='++';\n"
		. "\t var obj;\n"
		. "\t if(xGetElementById('top_block_no_5')) obj = xGetElementById('top_block_no_5');\n"
		. "\t else obj = xGetElementById('hidden_block_5');\n"
		. "\t if(isOpen) obj.style.display='block';\n"
		. "\t else obj.style.display='none';\n"

		. "\t xGetElementById('block_no_5').innerHTML = res.substring(2);showAllBlocks();\n"
		."\t close_ajax_popup();\n"
		. "}\n";



		// always open
		$functions .= "function redecorate_profile(res) {\n"
		//. "\t//xDisplay('hidden_block_3','block');\n"
		. "\t xGetElementById('block_no_3').innerHTML = res;\n"
		//. "\tclose_ajax_popup(); // not anywhere else but here; because it is called lastly, and it is the biggest one; so we're sure, this will be the last to stop\n"
		. "}\n";





		$functions .= "function redecorate_lovestuff_step3(res) {\n"
		. "\tif(res=='1') {\n"
		. "\t\t//x_peopleGetLoveStuff(lovestuff_current_id,redecorate_lovestuff);\n"
		. "\t\tpeople_show(lovestuff_current_id);\n"
		. "\t\tpopup_ajax('".$this->_("Content Update was successful! <a href=\"javascript:void(close_ajax_popup())\">Click</a> to continue...")."');\n"
		. "\t}\n"
		. "\telse {\n"
		. "\t\tpopup_ajax('".$this->_("Could not update the content! <a href=\"javascript:void(close_ajax_popup())\">Click</a> to try again...")."');\n"
		. "\t}\n"
		. "}\n";





		$functions  .= "function empty_boxes() {\n"
		. "\tredecorate_lovestuff('');"
		. "\tredecorate_healthstuff('');\n"
		. "\tredecorate_businessstuff('');\n"
		. "\tredecorate_profile('');\n"
		. "}\n";

		$functions .= <<<EOS
	
	function redecorate_operationsblock(res) {
		// xGetElementById('block_title_no_2').innerHTML = 'People';
        	xGetElementById('block_no_1').innerHTML = res;
	}
    
    var section_transparencies = new Array();
    var last_me = true;
    function save_section_transparencies() {
        
 
        section_transparencies['tags'] = xInnerHtml('visopt_tags')=='Hide';
        section_transparencies['favourites'] = xInnerHtml('visopt_favourites')=='Hide';
        section_transparencies['lovestuff'] = xInnerHtml('visopt_love')=='Hide';
        section_transparencies['healthstuff'] = xInnerHtml('visopt_health')=='Hide';
        section_transparencies['businessstuff'] = xInnerHtml('visopt_business')=='Hide';
        

        
    }
    
    
    function restore_section_transparencies() {
        
                

        if(!section_transparencies['tags'])
            peopleHideSection(4,true);
        
        if(!section_transparencies['favourites'])
            peopleHideSection(5,true);
        
        if(!section_transparencies['lovestuff'])
            peopleHideSection(6,true);
        
        if(!section_transparencies['healthstuff'])
            peopleHideSection(8,true);
        
        if(!section_transparencies['businessstuff'])
            peopleHideSection(7,true);
        
        
    }
    
    
        function reset_section_transparencies() {

            peopleShowSection(4,true);        
            peopleShowSection(5,true); 
            peopleShowSection(6,true);       
            peopleShowSection(7,true);   
            peopleShowSection(8,true);
        
        
    }
        
        
        var zrv_id = -1;
        function showAllBlocks() {
    
            if(zrv_id==current_user_membership_id) {
        
                obj1 = xGetElementById('top_block_no_4')?'top_block_no_4':'hidden_block_4';
                obj2 = xGetElementById('top_block_no_5')?'top_block_no_5':'hidden_block_5';
                obj3 = xGetElementById('top_block_no_6')?'top_block_no_6':'hidden_block_6';
                obj4 = xGetElementById('top_block_no_7')?'top_block_no_7':'hidden_block_7';
                obj5 = xGetElementById('top_block_no_8')?'top_block_no_8':'hidden_block_8';
        
                xDisplay(obj1,'block');
                xDisplay(obj2,'block');
                xDisplay(obj3,'block');
                xDisplay(obj4,'block');
                xDisplay(obj5,'block');
   
            }
            
        }
        
        
    	// will be implemented
        function pageload(id) {
            history.go(0);
             
        }
	
EOS;








		$functions  .= "function people_show(id,username) {\n"
		. "\t location.assign('http://grou.ps/{}/people/'+username);\n"
		. "\t /*make_busy();\n";
		//. "\t empty_boxes();\n"
		//. "\t make_busy();\n"

		$functions .= <<<EOS
        zrv_id = id;
    differentiate_bg();
        
    if(id==current_user_membership_id) {
        
        xDisplay('top_block_no_9','block')
        
        // don't touch if this is the first
        if(!last_me)
            restore_section_transparencies();
        
        last_me = true;
    }
    else {
        
        xDisplay('top_block_no_9','none')
        
        if(last_me)
            save_section_transparencies();
        
        reset_section_transparencies();
        
        last_me = false;
        
    }
    
    
    
EOS;

		if($access_isGroupMember)
		$functions  .=  "\t x_getOperationsBlock(id,redecorate_operationsblock);\n";

		$functions  .= "\t x_getPeopleBlock(id,redecorate_peopleblock);\n"
		."\t x_peopleGetProfileX(id,redecorate_profile);\n"
		. "\t x_peopleGetLoveStuff(id,redecorate_lovestuff);\n"
		. "\t x_peopleGetBusinessStuff(id,redecorate_businessstuff);\n"
		. "\t x_peopleGetHealthStuff(id,redecorate_healthstuff);\n"
		. "\t x_peopleGetTags(id,username,redecorate_tags);\n"
		. "\t x_peopleGetFavourites(id,username,redecorate_favourites);*/\n"

		. "}\n";










		/*



		$functions  .= "var lovestuff_current_id;\n";
		$functions  .= "var lovestuff_saved_content;\n";
		$functions  .= "var healthstuff_current_id;\n";
		$functions  .= "var healthstuff_saved_content;\n";
		$functions  .= "var businessstuff_current_id;\n";
		$functions  .= "var businessstuff_saved_content;\n";


		$functions  .= "function people_editlove(id) {\n"
		. "\tmake_ajax_popup_bigger();\n"
		. "\tlovestuff_current_id = id;\n"
		. "\tvar a = xGetElementById('lovestuff_stripped_content').value;\n"
		. "\tvar con = '<b>".str_replace("'","\\'",$this->_("Edit Love Stuff"))."</b><br /><br />'\n"
		. "\tcon += '<textarea id=\"xxx_love\" rows=\"12\" cols=\"50\">'\n"
		. "\tcon += xGetElementById('lovestuff_stripped_content').value\n"
		. "\tcon += '</textarea>'\n"
		. "\tcon += '<br /><br />'\n"
		. "\tcon += '<input type=\"button\" value=\"".$this->_("Continue")."\" onclick=\"javascript:lovestuff_saved_content=xGetElementById(\'xxx_love\').value;make_busy(); x_registerLoveStuff(lovestuff_current_id,lovestuff_saved_content,redecorate_lovestuff_step3);\" /> '\n"
		. "\tcon += '<input type=\"button\" value=\"".$this->_("Cancel")."\" onclick=\"javascript:void(close_ajax_popup())\" /> '\n"
		. "\tpopup_ajax(con);\n"
		. "}\n";

		*/


		/******
		* now let's deal with profile stuff
		*************/


		/*


		$functions .= <<<EOS



		var people_showhealth_id = -1;
		function people_showhealth(pid) {
		people_showhealth_id = pid;
		make_busy();
		x_hideHealthStuff(pid, people_showhealth_res);

		}

		function people_showhealth_res(res) {

		var txt = '';

		if(res=='1') {
		xDisplay('people_health_options_'+people_showhealth_id,'none');
		xDisplay('people_health_options_'+people_showhealth_id,'block');
		txt += 'Health Stuff section is hidden now. You will continue seeing it for editing purposes.';
		txt += '<br /><a href="close_ajax_popup()">Click</a> to continue';
		}
		else {
		txt += 'There was a problem!';
		txt += '<br /><a href="close_ajax_popup()">Click</a> to try again';
		}

		popup_ajax(txt);
		}





		EOS;

		*/


		$functions .= <<<EOS
                function people_editprofilex(membership_id) { 
			
			var inf_namesurname = xGetElementById('inf_namesurname').value;
			var inf_sexe = xGetElementById('inf_sexe').value;
			var inf_birthday = xGetElementById('inf_birthday').value;
			var inf_nationality = xGetElementById('inf_nationality').value;
			var inf_myspace = xGetElementById('inf_myspace').value;
			if(inf_myspace=='') inf_myspace = 'http://';
			
            var txt = "";
            txt += "<form><table width=\"420\" border=\"0\" cellpadding=\"3\" cellspacing=\"0\">";
EOS;

		$functions .= 'txt += "<tr><td align=\\"left\\">'.$this->_("Name Surname").'</td><td align=\\"left\\" style=\\"padding-left:10px;\\"><input onfocus=\\"inputfocus(this)\\" onblur=\\"inputblur(this)\\" required=\\"1\\" minlength=\\"3\\" realname=\\"Name Surname\\" regexp=\\"/^[a-z A-Z]{3,}$/\\" type=\\"text\\" id=\\"namesurname\\" value=\\""+inf_namesurname+"\\" /></td></tr>";';


		if($people_allow_sexe)
			$functions .= 'txt += "<tr><td align=\\"left\\">'.$this->_("Sexe").'</td><td align=\\"left\\" style=\\"padding-left:10px;\\"><select id=\\"sexe\\" realname=\\"Sexe\\" required=\\"1\\"><option value=\\"Female\\">'.$this->_("Female").'</option><option value=\\"male\\">'.$this->_("Male").'</option></select></td></tr>";';
		else 
			$functions .= 'txt += "<input type=\\"hidden\\" id=\\"sexe\\" value=\\""+inf_sexe+"\\" />";';
		
		if($people_allow_birthday)
			$functions .= 'txt += "<tr><td align=\\"left\\">'.$this->_("Birthday").'</td><td align=\\"left\\" style=\\"padding-left:10px;\\"><input onfocus=\\"inputfocus(this)\\" onblur=\\"inputblur(this)\\" required=\\"1\\" regexp=\\"JSVAL_RX_DATE\\" realname=\\"Birthday\\" type=\\"text\\" id=\\"birthday\\" value=\\""+inf_birthday+"\\" /> '.$this->_("(mm/dd/yyyy)").'</td></tr>";';
		else 
			$functions .= 'txt += "<input type=\\"hidden\\" id=\\"birthday\\" value=\\""+inf_birthday+"\\" />";';
			
		if($people_allow_website)
			$functions .= 'txt += "<tr><td align=\\"left\\">'.$this->_("Web/MySpace Page").'</td><td align=\\"left\\" style=\\"padding-left:10px;\\"><input onfocus=\\"inputfocus(this)\\" onblur=\\"inputblur(this)\\" required=\\"0\\" realname=\\"Web/MySpace Page\\" type=\\"text\\" id=\\"myspace\\" value=\\""+inf_myspace+"\\" />";';
		else 
			$functions .= 'txt += "<input type=\\"hidden\\" id=\\"myspace\\" value=\\""+inf_myspace+"\\" />";';
			
		if($people_allow_nation) {
			$functions .= 'txt += "<tr><td align=\\"left\\">'.$this->_("Nationality").'</td><td align=\\"left\\" style=\\"padding-left:10px;\\"><select required=\\"1\\" realname=\\"Nationality\\" type=\\"text\\" id=\\"nationality\\" />";';
		


		$functions .= <<<EOS
            txt += '<option value="AF">Afghanistan</option>';
            txt += '<option value="AL">Albania</option>';
            txt += '<option value="DZ">Algeria</option>';
            txt += '<option value="AS">American Samoa</option>';
            txt += '<option value="AD">Andorra</option>';
            txt += '<option value="AO">Angola</option>';
            txt += '<option value="AI">Anguilla</option>';
            txt += '<option value="AQ">Antarctica</option>';
            txt += '<option value="AG">Antigua and Barbuda</option>';
            txt += '<option value="AR">Argentina</option>';
            txt += '<option value="AM">Armenia</option>';
            txt += '<option value="AW">Aruba</option>';
            txt += '<option value="AU">Australia</option>';
            txt += '<option value="AT">Austria</option>';
            txt += '<option value="AZ">Azerbaijan</option>';
            txt += '<option value="AP">Azores</option>';
            txt += '<option value="BS">Bahamas</option>';
            txt += '<option value="BH">Bahrain</option>';
            txt += '<option value="BD">Bangladesh</option>';
            txt += '<option value="BB">Barbados</option>';
            txt += '<option value="BY">Belarus</option>';
            txt += '<option value="BE">Belgium</option>';
            txt += '<option value="BZ">Belize</option>';
            txt += '<option value="BJ">Benin</option>';
            txt += '<option value="BM">Bermuda</option>';
            txt += '<option value="BT">Bhutan</option>';
            txt += '<option value="BO">Bolivia</option>';
            txt += '<option value="BA">Bosnia And Herzegowina</option>';
            txt += '<option value="XB">Bosnia-Herzegovina</option>';
            txt += '<option value="BW">Botswana</option>';
            txt += '<option value="BV">Bouvet Island</option>';
            txt += '<option value="BR">Brazil</option>';
            txt += '<option value="IO">British Indian Ocean Territory</option>';
            txt += '<option value="VG">British Virgin Islands</option>';
            txt += '<option value="BN">Brunei Darussalam</option>';
            txt += '<option value="BG">Bulgaria</option>';
            txt += '<option value="BF">Burkina Faso</option>';
            txt += '<option value="BI">Burundi</option>';
            txt += '<option value="KH">Cambodia</option>';
            txt += '<option value="CM">Cameroon</option>';
            txt += '<option value="CA">Canada</option>';
            txt += '<option value="CV">Cape Verde</option>';
            txt += '<option value="KY">Cayman Islands</option>';
            txt += '<option value="CF">Central African Republic</option>';
            txt += '<option value="TD">Chad</option>';
            txt += '<option value="CL">Chile</option>';
            txt += '<option value="CN">China</option>';
            txt += '<option value="CX">Christmas Island</option>';
            txt += '<option value="CC">Cocos (Keeling) Islands</option>';
            txt += '<option value="CO">Colombia</option>';
            txt += '<option value="KM">Comoros</option>';
            txt += '<option value="CG">Congo</option>';
            txt += '<option value="CD">Congo, The Democratic Republic</option>';
            txt += '<option value="CK">Cook Islands</option>';
            txt += '<option value="XE">Corsica</option>';
            txt += '<option value="CR">Costa Rica</option>';
            txt += '<option value="CI">Cote d\\'Ivoire (Ivory Coast)</option>';
            txt += '<option value="HR">Croatia</option>';
            txt += '<option value="CU">Cuba</option>';
            txt += '<option value="CY">Cyprus</option>';
            txt += '<option value="CZ">Czech Republic</option>';
            txt += '<option value="DK">Denmark</option>';
            txt += '<option value="DJ">Djibouti</option>';
            txt += '<option value="DM">Dominica</option>';
            txt += '<option value="DO">Dominican Republic</option>';
            txt += '<option value="TP">East Timor</option>';
            txt += '<option value="EC">Ecuador</option>';
            txt += '<option value="EG">Egypt</option>';
            txt += '<option value="SV">El Salvador</option>';
            txt += '<option value="GQ">Equatorial Guinea</option>';
            txt += '<option value="ER">Eritrea</option>';
            txt += '<option value="EE">Estonia</option>';
            txt += '<option value="ET">Ethiopia</option>';
            txt += '<option value="FK">Falkland Islands (Malvinas)</option>';
            txt += '<option value="FO">Faroe Islands</option>';
            txt += '<option value="FJ">Fiji</option>';
            txt += '<option value="FI">Finland</option>';
            txt += '<option value="FR">France (Includes Monaco)</option>';
            txt += '<option value="FX">France, Metropolitan</option>';
            txt += '<option value="GF">French Guiana</option>';
            txt += '<option value="PF">French Polynesia</option>';
            txt += '<option value="TA">French Polynesia (Tahiti)</option>';
            txt += '<option value="TF">French Southern Territories</option>';
            txt += '<option value="GA">Gabon</option>';
            txt += '<option value="GM">Gambia</option>';
            txt += '<option value="GE">Georgia</option>';
            txt += '<option value="DE">Germany</option>';
            txt += '<option value="GH">Ghana</option>';
            txt += '<option value="GI">Gibraltar</option>';
            txt += '<option value="GR">Greece</option>';
            txt += '<option value="GL">Greenland</option>';
            txt += '<option value="GD">Grenada</option>';
            txt += '<option value="GP">Guadeloupe</option>';
            txt += '<option value="GU">Guam</option>';
            txt += '<option value="GT">Guatemala</option>';
            txt += '<option value="GN">Guinea</option>';
            txt += '<option value="GW">Guinea-Bissau</option>';
            txt += '<option value="GY">Guyana</option>';
            txt += '<option value="HT">Haiti</option>';
            txt += '<option value="HM">Heard And Mc Donald Islands</option>';
            txt += '<option value="VA">Holy See (Vatican City State)</option>';
            txt += '<option value="HN">Honduras</option>';
            txt += '<option value="HK">Hong Kong</option>';
            txt += '<option value="HU">Hungary</option>';
            txt += '<option value="IS">Iceland</option>';
            txt += '<option value="IN">India</option>';
            txt += '<option value="ID">Indonesia</option>';
            txt += '<option value="IR">Iran</option>';
            txt += '<option value="IQ">Iraq</option>';
            txt += '<option value="IE">Ireland</option>';
            txt += '<option value="EI">Ireland (Eire)</option>';
            txt += '<option value="IL">Israel</option>';
            txt += '<option value="IT">Italy</option>';
            txt += '<option value="JM">Jamaica</option>';
            txt += '<option value="JP">Japan</option>';
            txt += '<option value="JO">Jordan</option>';
            txt += '<option value="KZ">Kazakhstan</option>';
            txt += '<option value="KE">Kenya</option>';
            txt += '<option value="KI">Kiribati</option>';
            txt += '<option value="KP">Korea, Democratic People Repub</option>';
            txt += '<option value="KW">Kuwait</option>';
            txt += '<option value="KG">Kyrgyzstan</option>';
            txt += '<option value="LA">Laos</option>';
            txt += '<option value="LV">Latvia</option>';
            txt += '<option value="LB">Lebanon</option>';
            txt += '<option value="LS">Lesotho</option>';
            txt += '<option value="LR">Liberia</option>';
            txt += '<option value="LY">Libya</option>';
            txt += '<option value="LI">Liechtenstein</option>';
            txt += '<option value="LT">Lithuania</option>';
            txt += '<option value="LU">Luxembourg</option>';
            txt += '<option value="MO">Macao</option>';
            txt += '<option value="MK">Macedonia</option>';
            txt += '<option value="MG">Madagascar</option>';
            txt += '<option value="ME">Madeira Islands</option>';
            txt += '<option value="MW">Malawi</option>';
            txt += '<option value="MY">Malaysia</option>';
            txt += '<option value="MV">Maldives</option>';
            txt += '<option value="ML">Mali</option>';
            txt += '<option value="MT">Malta</option>';
            txt += '<option value="MH">Marshall Islands</option>';
            txt += '<option value="MQ">Martinique</option>';
            txt += '<option value="MR">Mauritania</option>';
            txt += '<option value="MU">Mauritius</option>';
            txt += '<option value="YT">Mayotte</option>';
            txt += '<option value="MX">Mexico</option>';
            txt += '<option value="FM">Micronesia, Federated States Of</option>';
            txt += '<option value="MD">Moldova, Republic Of</option>';
            txt += '<option value="MC">Monaco</option>';
            txt += '<option value="MN">Mongolia</option>';
            txt += '<option value="MS">Montserrat</option>';
            txt += '<option value="MA">Morocco</option>';
            txt += '<option value="MZ">Mozambique</option>';
            txt += '<option value="MM">Myanmar (Burma)</option>';
            txt += '<option value="NA">Namibia</option>';
            txt += '<option value="NR">Nauru</option>';
            txt += '<option value="NP">Nepal</option>';
            txt += '<option value="NL">Netherlands</option>';
            txt += '<option value="AN">Netherlands Antilles</option>';
            txt += '<option value="NC">New Caledonia</option>';
            txt += '<option value="NZ">New Zealand</option>';
            txt += '<option value="NI">Nicaragua</option>';
            txt += '<option value="NE">Niger</option>';
            txt += '<option value="NG">Nigeria</option>';
            txt += '<option value="NU">Niue</option>';
            txt += '<option value="NF">Norfolk Island</option>';
            txt += '<option value="MP">Northern Mariana Islands</option>';
            txt += '<option value="NO">Norway</option>';
            txt += '<option value="OM">Oman</option>';
            txt += '<option value="PK">Pakistan</option>';
            txt += '<option value="PW">Palau</option>';
            txt += '<option value="PS">Palestinian Territory, Occupied</option>';
            txt += '<option value="PA">Panama</option>';
            txt += '<option value="PG">Papua New Guinea</option>';
            txt += '<option value="PY">Paraguay</option>';
            txt += '<option value="PE">Peru</option>';
            txt += '<option value="PH">Philippines</option>';
            txt += '<option value="PN">Pitcairn</option>';
            txt += '<option value="PL">Poland</option>';
            txt += '<option value="PT">Portugal</option>';
            txt += '<option value="PR">Puerto Rico</option>';
            txt += '<option value="QA">Qatar</option>';
            txt += '<option value="RE">Reunion</option>';
            txt += '<option value="RO">Romania</option>';
            txt += '<option value="RU">Russian Federation</option>';
            txt += '<option value="RW">Rwanda</option>';
            txt += '<option value="KN">Saint Kitts And Nevis</option>';
            txt += '<option value="SM">San Marino</option>';
            txt += '<option value="ST">Sao Tome and Principe</option>';
            txt += '<option value="SA">Saudi Arabia</option>';
            txt += '<option value="SN">Senegal</option>';
            txt += '<option value="XS">Serbia-Montenegro</option>';
            txt += '<option value="SC">Seychelles</option>';
            txt += '<option value="SL">Sierra Leone</option>';
            txt += '<option value="SG">Singapore</option>';
            txt += '<option value="SK">Slovak Republic</option>';
            txt += '<option value="SI">Slovenia</option>';
            txt += '<option value="SB">Solomon Islands</option>';
            txt += '<option value="SO">Somalia</option>';
            txt += '<option value="ZA">South Africa</option>';
            txt += '<option value="GS">South Georgia And The South Sand</option>';
            txt += '<option value="KR">South Korea</option>';
            txt += '<option value="ES">Spain</option>';
            txt += '<option value="LK">Sri Lanka</option>';
            txt += '<option value="NV">St. Christopher and Nevis</option>';
            txt += '<option value="SH">St. Helena</option>';
            txt += '<option value="LC">St. Lucia</option>';
            txt += '<option value="PM">St. Pierre and Miquelon</option>';
            txt += '<option value="VC">St. Vincent and the Grenadines</option>';
            txt += '<option value="SD">Sudan</option>';
            txt += '<option value="SR">Suriname</option>';
            txt += '<option value="SJ">Svalbard And Jan Mayen Islands</option>';
            txt += '<option value="SZ">Swaziland</option>';
            txt += '<option value="SE">Sweden</option>';
            txt += '<option value="CH">Switzerland</option>';
            txt += '<option value="SY">Syrian Arab Republic</option>';
            txt += '<option value="TW">Taiwan</option>';
            txt += '<option value="TJ">Tajikistan</option>';
            txt += '<option value="TZ">Tanzania</option>';
            txt += '<option value="TH">Thailand</option>';
            txt += '<option value="TG">Togo</option>';
            txt += '<option value="TK">Tokelau</option>';
            txt += '<option value="TO">Tonga</option>';
            txt += '<option value="TT">Trinidad and Tobago</option>';
            txt += '<option value="XU">Tristan da Cunha</option>';
            txt += '<option value="TN">Tunisia</option>';
            txt += '<option value="TR">Turkey</option>';
            txt += '<option value="TM">Turkmenistan</option>';
            txt += '<option value="TC">Turks and Caicos Islands</option>';
            txt += '<option value="TV">Tuvalu</option>';
            txt += '<option value="UG">Uganda</option>';
            txt += '<option value="UA">Ukraine</option>';
            txt += '<option value="AE">United Arab Emirates</option>';
            txt += '<option value="UK">United Kingdom</option>';
            txt += '<option value="GB">Great Britain</option>';
            txt += '<option value="US" selected>United States</option>';
            txt += '<option value="UM">United States Minor Outlying Isl</option>';
            txt += '<option value="UY">Uruguay</option>';
            txt += '<option value="UZ">Uzbekistan</option>';
            txt += '<option value="VU">Vanuatu</option>';
            txt += '<option value="XV">Vatican City</option>';
            txt += '<option value="VE">Venezuela</option>';
            txt += '<option value="VN">Vietnam</option>';
            txt += '<option value="VI">Virgin Islands (U.S.)</option>';
            txt += '<option value="WF">Wallis and Furuna Islands</option>';
            txt += '<option value="EH">Western Sahara</option>';
            txt += '<option value="WS">Western Samoa</option>';
            txt += '<option value="YE">Yemen</option>';
            txt += '<option value="YU">Yugoslavia</option>';
            txt += '<option value="ZR">Zaire</option>';
            txt += '<option value="ZM">Zambia</option>';
            txt += '<option value="ZW">Zimbabwe</option>';	
	
            
            
            txt += "</select></td></tr>";
EOS;
		}
		else 
			$functions .= 'txt += "<input type=\\"hidden\\" id=\\"nationality\\" value=\\""+inf_nationality+"\\" />";';

		$functions .= 'txt += "<tr><td>&nbsp;</td><td align=\"left\" style=\"padding-left:10px;\"><br /><input type=\"button\" value=\"'.$this->_("Save").'\" onclick=\"people_editprofilex_go(this.form,"+membership_id+")\" /></form></td></tr>";';

		$functions .= 'popup_ajax(txt);';
	    
		if($people_allow_sexe)
	    	$functions .= "selectBoxDefault('sexe',inf_sexe);";
	    	
	    if($people_allow_nation)
	    	$functions .= "selectBoxDefault('nationality',inf_nationality);";

		$functions .= "}";


		$functions .= <<<EOS
            
            var zmid = -1;
        function people_editprofilex_go(formobj,mid) { 
            
                
                
            if(!validateCompleteForm(formobj,'error'))
                return false;
            
            zmid = mid;
            var namesurname = xGetElementById('namesurname').value;
            var birthday = xGetElementById('birthday').value;
            var nationality = xGetElementById('nationality').value;
            var myspace = xGetElementById('myspace').value;
            
	    var sexe = xGetElementById('sexe').value;
            
            make_busy();
            x_registerProfileX(mid,namesurname,birthday,myspace,nationality,sexe,people_editprofilex_finish);
            
}
            
            
            
            
            
            function people_editprofilex_finish(res) {
                
                if(res=='1') {
         
                    popup_ajax('Profile successfully modified! <a href="javascript:void(pageload('+zmid+'))">Click</a> to continue...');
        }
            else {
                
                popup_ajax('Failure! <a href="javascript:void(pageload('+zmid+'))">Click</a> to try again...');
                
        }
                
        }
            
            
            
            
            
            
EOS;


		/*
		$functions .= <<<EOS

		function people_editcontacts(mid) {

		var email = xGetElementById('inf_email').value;
		var aim = xGetElementById('inf_aim').value;
		var icq = xGetElementById('inf_icq').value;
		var yahoo = xGetElementById('inf_yahoo').value;
		var msn = xGetElementById('inf_msn').value;
		var jabber = xGetElementById('inf_jabber').value;

		var txt = "";
		txt += "<form>";
		EOS;


		$functions .= 'txt += "<label style=\"width:100px;\">'.$this->_('Email').'</label><span style=\"width:250px;padding-left:10px;\"><input value=\""+email+"\" onfocus=\"inputfocus(this)\" onblur=\"inputblur(this)\" required=\"1\" realname=\"Email\" regexp=\"JSVAL_RX_EMAIL\" type=\"text\" id=\"email\" /></span><br /><br />";';


		$functions .= <<<EOS
		txt += "<label style=\"width:100px;\">AIM</label><span style=\"width:250px;padding-left:10px;\"><input value=\""+aim+"\" onfocus=\"inputfocus(this)\" onblur=\"inputblur(this)\" required=\"0\" realname=\"AIM\" type=\"text\" id=\"aim\" /></span><br /><br />";
		txt += "<label style=\"width:100px;\">ICQ</label><span style=\"width:250px;padding-left:10px;\"><input value=\""+icq+"\" onfocus=\"inputfocus(this)\" onblur=\"inputblur(this)\" required=\"0\" realname=\"ICQ\" type=\"text\" id=\"icq\" /></span><br /><br />";
		txt += "<label style=\"width:100px;\">Jabber</label><span style=\"width:250px;padding-left:10px;\"><input value=\""+jabber+"\" onfocus=\"inputfocus(this)\" onblur=\"inputblur(this)\" required=\"0\" regexp=\"JSVAL_RX_EMAIL\" realname=\"Jabber\" type=\"text\" id=\"jabber\" /></span><br /><br />";
		txt += "<label style=\"width:100px;\">MSN</label><span style=\"width:250px;padding-left:10px;\"><input value=\""+msn+"\" onfocus=\"inputfocus(this)\" onblur=\"inputblur(this)\" required=\"0\" realname=\"MSN\" regexp=\"JSVAL_RX_EMAIL\" type=\"text\" id=\"msn\" /></span><br /><br />";
		txt += "<label style=\"width:100px;\">Yahoo</label><span style=\"width:250px;padding-left:10px;\"><input value=\""+yahoo+"\" onfocus=\"inputfocus(this)\" onblur=\"inputblur(this)\" required=\"0\" realname=\"Yahoo\" type=\"text\" id=\"yahoo\" /></span><br /><br />";
		EOS;

		$functions .= 'txt += "<input type=\"button\" value=\"'.$this->_("Save").'\" onclick=\"people_editcontacts_go(this.form,"+mid+")\" /></form>";';

		$functions .= <<<EOS

		popup_ajax(txt);
		}
		EOS;



		$functions .= <<<EOS
		function people_editcontacts_go(formobj,mid) {

		if(!validateCompleteForm(formobj,'error'))
		return false;

		var email = xGetElementById('email').value;
		var aim = xGetElementById('aim').value;
		var icq = xGetElementById('icq').value;
		var msn = xGetElementById('msn').value;
		var jabber = xGetElementById('jabber').value;
		var yahoo = xGetElementById('yahoo').value;

		make_busy();
		x_registerContactsX(mid,email,aim,icq,msn,jabber,yahoo,people_editcontacts_finish);

		}



		function people_editcontacts_finish(res) {

		if(res=='1') {

		popup_ajax('Success! <a href="javascript:void(pageload('+zmid+'))">Click</a> to continue...');
		}
		else {

		popup_ajax('Failure! Make sure you entered valid contact data. <a href="javascript:void(pageload('+zmid+'))">Click</a> to try again...');

		}

		}




		*/


		$functions .= <<<EOS
	    
	    var isTagsEmpty = false;
	    function people_edittags(mid) {
		
		    var tags = xGetElementById('inf_tags').value;
		    
		    
		    if(tags=='') {
			    isTagsEmpty = true;
EOS;

		$functions .= "tags = '".$this->_("Stanford University, USA, MacOS X, easy going, GEEK, origin:israel, Starbucks, yellow, romantic, University Avenue, Palo Alto, Computer Sciences, beer, NY City, soft rock, BMW, desperate single, sunset, religion: jewish, girls, heterosexual, fun, Steve Jobs, sushi, Tel Aviv, BA, honesty")."';\n";

		$functions .= <<<EOS

		    }
		    else {
			    isTagsEmpty = false;
		    }
			    
		    var txt = '';
		    
		    txt += '<form>';
EOS;
		$functions .= "txt += '".$this->_("Describe yourself by tags. Separate them by <u>commas</u>. Sort by <u>descending</u> <u>priority</u> order. (e.g highest priority is at the first tag)")."<br /><br />';\n";

		$functions .= <<<EOS
		    		    
		    txt += '<textarea onclick="tagcheck(this)" id="yourtags" required="1" realname="Tags" style="width:300px;height:100px;">'+tags+'</textarea>';
		    txt += '<br /><br />';
EOS;

		$functions .= "txt += '<input type=\"button\" value=\"".$this->_("Submit")."\" onclick=\"people_edittags_go(this.form,'+mid+')\" />';\n";

		$functions .= <<<EOS
		    
		    txt += '</form>';
		    
		    popup_ajax(txt);
		    
		    if(isTagsEmpty)
			    xGetElementById('yourtags').style.color='red';
		    
		    
	    }
	    
	    function tagcheck(obj) {
		    
		    if(isTagsEmpty) {
			    obj.value='';
			    obj.style.color='black';
			    isTagsEmpty=false;
		    }
	    }
	    
	    
	    
	    
	   function people_edittags_go(formobj,mid) { 
            
            if(!validateCompleteForm(formobj,'error'))
                return false;
            
            var tags = xGetElementById('yourtags').value;
            
            make_busy();
            x_registerTagsX(mid,tags,people_edittags_finish);
            
	}



            function people_edittags_finish(res) {
                
                if(res=='1') {
         
			popup_ajax('Success! <a href="javascript:void(pageload('+zmid+'))">Click</a> to continue...');
		}
		else {
                
			popup_ajax('Failure! <a href="javascript:void(pageload('+zmid+'))">Click</a> to try again...');
                
		}
        	        
	    }

	    
	    function people_edit_favourites(mid) {
	    
		    var txt = '';
		    
		    var favsongs = xGetElementById('inf_favsongs').value;
		    var favsingers = xGetElementById('inf_favsingers').value;
		    var favmovies = xGetElementById('inf_favmovies').value;
		    var favactors = xGetElementById('inf_favactors').value;
		    var favbooks = xGetElementById('inf_favbooks').value;
		    var favauthors = xGetElementById('inf_favauthors').value;
		    var favsportsmen = xGetElementById('inf_favsportsmen').value;
		    var favartists = xGetElementById('inf_favartists').value;
		    var favcities = xGetElementById('inf_favcities').value;
		    
		    
		    txt += "<form><table width=\"300\" border=\"0\" cellpadding=\"3\" cellspacing=\"0\">";
		    
EOS;

		$functions .= 'txt += "<tr><td colspan=\"2\" align=\"left\">'.$this->_("Separate your favorites by <u>commas</u>").'</td></tr>";';



		$functions .= 'txt += "<tr><td align=\"left\">'.$this->_("Fav Songs").'</td><td align=\"left\" style=\"padding-left:10px;\"><input onfocus=\"inputfocus(this)\" onblur=\"inputblur(this)\" required=\"0\" type=\"text\" id=\"favsongs\" value=\""+favsongs+"\" /></td></tr>";';
		$functions .= 'txt += "<tr><td align=\"left\">'.$this->_("Fav Singers").'</td><td align=\"left\" style=\"padding-left:10px;\"><input onfocus=\"inputfocus(this)\" onblur=\"inputblur(this)\" required=\"0\" type=\"text\" id=\"favsingers\" value=\""+favsingers+"\" /></td></tr>";';
		$functions .= 'txt += "<tr><td align=\"left\">'.$this->_("Fav Movies").'</td><td align=\"left\" style=\"padding-left:10px;\"><input onfocus=\"inputfocus(this)\" onblur=\"inputblur(this)\" required=\"0\" type=\"text\" id=\"favmovies\" value=\""+favmovies+"\" /></td></tr>";';
		$functions .= 'txt += "<tr><td align=\"left\">'.$this->_("Fav Actors").'</td><td align=\"left\" style=\"padding-left:10px;\"><input onfocus=\"inputfocus(this)\" onblur=\"inputblur(this)\" required=\"0\" type=\"text\" id=\"favactors\" value=\""+favactors+"\" /></td></tr>";';
		$functions .= 'txt += "<tr><td align=\"left\">'.$this->_("Fav Books").'</td><td align=\"left\" style=\"padding-left:10px;\"><input onfocus=\"inputfocus(this)\" onblur=\"inputblur(this)\" required=\"0\" type=\"text\" id=\"favbooks\" value=\""+favbooks+"\" /></td></tr>";';
		$functions .= 'txt += "<tr><td align=\"left\">'.$this->_("Fav Authors").'</td><td align=\"left\" style=\"padding-left:10px;\"><input onfocus=\"inputfocus(this)\" onblur=\"inputblur(this)\" required=\"0\" type=\"text\" id=\"favauthors\" value=\""+favauthors+"\" /></td></tr>";';
		$functions .= 'txt += "<tr><td align=\"left\">'.$this->_("Fav Sportsmen").'</td><td align=\"left\" style=\"padding-left:10px;\"><input onfocus=\"inputfocus(this)\" onblur=\"inputblur(this)\" required=\"0\" type=\"text\" id=\"favsportsmen\" value=\""+favsportsmen+"\" /></td></tr>";';
		$functions .= 'txt += "<tr><td align=\"left\">'.$this->_("Fav Artists").'</td><td align=\"left\" style=\"padding-left:10px;\"><input onfocus=\"inputfocus(this)\" onblur=\"inputblur(this)\" required=\"0\" type=\"text\" id=\"favartists\" value=\""+favartists+"\" /></td></tr>";';
		$functions .= 'txt += "<tr><td align=\"left\">'.$this->_("Fav Cities").'</td><td align=\"left\" style=\"padding-left:10px;\"><input onfocus=\"inputfocus(this)\" onblur=\"inputblur(this)\" required=\"0\" type=\"text\" id=\"favcities\" value=\""+favcities+"\" /></td></tr>";';

		$functions .= "txt += '<tr><td align=\"left\">&nbsp;</td><td align=\"left\" style=\"padding-left:10px;\"><input type=\"button\" value=\"".$this->_("Submit")."\" onclick=\"people_edit_favourites_go('+mid+')\" /></td></tr>';";

		$functions .= <<<EOS
		    txt += '</form>';
		    
		    popup_ajax(txt);
	    
	    }
            
            
            
            /**
             * no form check
             */
                
                var rrmidr = -1;
        function people_edit_favourites_go(mid) {
	    
               rrmidr = mid;
            var txt = '';
		    
		    var favsongs = xGetElementById('favsongs').value;
		    var favsingers = xGetElementById('favsingers').value;
		    var favmovies = xGetElementById('favmovies').value;
		    var favactors = xGetElementById('favactors').value;
		    var favbooks = xGetElementById('favbooks').value;
		    var favauthors = xGetElementById('favauthors').value;
		    var favsportsmen = xGetElementById('favsportsmen').value;
		    var favartists = xGetElementById('favartists').value;
		    var favcities = xGetElementById('favcities').value;
		    
		    make_busy();
            x_registerFavourites(mid,favsongs,favsingers,favmovies,favactors,favbooks,favauthors,favsportsmen,favartists,favcities,people_edit_favourites_finish);
	    
	    }
	    
            
            
            
            function people_edit_favourites_finish(res) {    
     
                if(res=='1') {
EOS;

		$functions .= "popup_ajax('".$this->_("Success! <a href=\"javascript:void(pageload('+rrmidr+'))\">Click</a> to continue...")."');\n";
		$functions .= "}\n            else {\n";

		$functions .= "popup_ajax('".$this->_("Failure! <a href=\"javascript:void(pageload('+rrmidr+'))\">Click</a> to try again...")."');";

		$functions .= <<<EOS
        }                
            
        }
            
         
            function people_vishealth(mid) {
EOS;
		$functions .= "if(xInnerHtml('visopt_health')=='".$this->_("Hide")."') {\n";

		$functions .= <<<EOS
                	                	
                    peopleHideSection(8,false);
            make_busy();
EOS;
		$functions .= "xInnerHtml('visopt_health','".$this->_("Show")."');\n";

		$functions .= <<<EOS
            xGetElementById('view_visopt_health').src = 'http://grou.ps/images/show.gif';
            x_hideSectionHealth(mid,visres);
                    
        }
            else {
                
                peopleShowSection(8,false);
            make_busy();
EOS;
		$functions .= "xInnerHtml('visopt_health','".$this->_("Hide")."');\n";

		$functions .= <<<EOS
            xGetElementById('view_visopt_health').src = 'http://grou.ps/images/hide.gif';
            x_showSectionHealth(mid,visres);
        }
                
                
                
        }
            
            function people_visbusiness(mid) {
                
EOS;
		$functions .= "if(xInnerHtml('visopt_business')=='".$this->_("Hide")."') {\n";

		$functions .= <<<EOS
                    
                    peopleHideSection(7,false);
            make_busy();
EOS;
		$functions .= "xInnerHtml('visopt_business','".$this->_("Show")."');\n";

		$functions .= <<<EOS
            xGetElementById('view_visopt_business').src = 'http://grou.ps/images/show.gif';
            x_hideSectionBusiness(mid,visres);
                    
        }
            else {
                
                peopleShowSection(7,false);
            make_busy();
EOS;
		$functions .= "xInnerHtml('visopt_business','".$this->_("Hide")."');\n";

		$functions .= <<<EOS
            xGetElementById('view_visopt_business').src = 'http://grou.ps/images/hide.gif';
            x_showSectionBusiness(mid,visres);
        }
        
        }  
        
            function people_vislove(mid) {
                
EOS;
		$functions .= "if(xInnerHtml('visopt_love')=='".$this->_("Hide")."') {\n";

		$functions .= <<<EOS
                    
                    peopleHideSection(6,false);
            make_busy();
EOS;
		$functions .= "xInnerHtml('visopt_love','".$this->_("Show")."');\n";

		$functions .= <<<EOS
            xGetElementById('view_visopt_love').src = 'http://grou.ps/images/show.gif';
                    x_hideSectionLove(mid,visres);
        }
            else {
                
                peopleShowSection(6,false);
            make_busy();
EOS;
		$functions .= "xInnerHtml('visopt_love','".$this->_("Hide")."');\n";

		$functions .= <<<EOS
            xGetElementById('view_visopt_love').src = 'http://grou.ps/images/hide.gif';
                x_showSectionLove(mid,visres);
        }
        
                
        }
            
            function people_vistags(mid) {
                
EOS;
		$functions .= "if(xInnerHtml('visopt_tags')=='".$this->_("Hide")."') {\n";

		$functions .= <<<EOS
                                    
                    peopleHideSection(4,false);    
            make_busy();
EOS;
		$functions .= "xInnerHtml('visopt_tags','".$this->_("Show")."');\n";

		$functions .= <<<EOS
            xGetElementById('view_visopt_tags').src = 'http://grou.ps/images/show.gif';
                    x_hideSectionTags(mid,visres);
        }
            else {
                
                peopleShowSection(4,false);
            make_busy();
EOS;
		$functions .= "xInnerHtml('visopt_tags','".$this->_("Hide")."');\n";

		$functions .= <<<EOS
            xGetElementById('view_visopt_tags').src = 'http://grou.ps/images/hide.gif';
                x_showSectionTags(mid,visres);
        }
        
                
        }
	    
            function people_visfavourites(mid) {
                
EOS;
		$functions .= "if(xInnerHtml('visopt_favourites')=='".$this->_("Hide")."') {\n";

		$functions .= <<<EOS
                                    
                    peopleHideSection(5,false);
            make_busy();
EOS;
		$functions .= "xInnerHtml('visopt_favourites','".$this->_("Show")."');\n";

		$functions .= <<<EOS
            xGetElementById('view_visopt_favourites').src = 'http://grou.ps/images/show.gif';
                    x_hideSectionFavourites(mid,visres);
        }
            else {
                peopleShowSection(5,false);
            make_busy();
EOS;
		$functions .= "xInnerHtml('visopt_favourites','".$this->_("Hide")."');\n";

		$functions .= <<<EOS
            xGetElementById('view_visopt_favourites').src = 'http://grou.ps/images/hide.gif';
            x_showSectionFavourites(mid,visres);
        }
        
                
        }
            
            
            function visres(res) {
                
                if(res=='1')
                close_ajax_popup();
            else
EOS;
		$functions .= "popup_ajax('".$this->_("There was an error! <a href=\"javascript:void(history.go(0))\">Refresh</a> this page and try again...")."');\n";

		$functions .= <<<EOS
                
                
                
        }
	
	
	
	function add_watchlist(membername) {
		make_busy();
		x_addToWatchlist(membername,watchlist_res)
	}
            
	
	function watchlist_res(res) {
	
		if(res=='0') {
EOS;

		$functions .= "popup_ajax('".$this->_("There was an error! <a href=\"javascript:void(close_ajax_popup())\">Click</a> to try again...")."');\n";

		$functions .= "		}		else if(res=='1') {\n";

		$functions .= "popup_ajax('".$this->_("This person is already in your watchlist. <a href=\"javascript:void(close_ajax_popup())\">Click</a> to continue...")."');\n";
		$functions .= "}		else if(res=='2') { \n";

		$functions .= "xGetElementById('block_no_1').innerHTML = '<img align=\"absbottom\" src=\"http://grou.ps/images/eye.gif\" width=\"16\" height=\"16\" border=\"0\" /> ".$this->_("In Your Watchlist")."';\n";

		$functions .= "popup_ajax('".$this->_("Success! Please notice that this feature is currently experimental and you may not enjoy all of its benefits during the \"early adopters\" phase. <a href=\"javascript:void(close_ajax_popup())\">Click</a> to continue...")."');\n";

		$functions .= <<<EOS
		}
		
	}
            
EOS;





		$functions .= <<<EOS
        
        function avatar_chosen(pid) {
            
            for(i=1;i<=15;i++) {
            
                if(i==pid) {
                    xGetElementById('chosen_avatar').value = i;
                    xGetElementById('avatar_option_'+i).style.borderColor='pink';
                }
                else {
                    xGetElementById('avatar_option_'+i).style.borderColor='black';
                   
                }
            }
        }
        
        
        function no_chosen_avatar(obj) {
            
            if(obj.value!='') {
            
                for(i=1;i<=15;i++) {
                    
                    xGetElementById('avatar_option_'+i).style.borderColor='black';
                    
                }
                
            }
        }
        
        
        function avon(obj) {
         
            if(obj.style.borderLeftColor!='pink')
                obj.style.borderColor='yellow';
            
        }
        
        function avout(obj) {
            
            if(obj.style.borderLeftColor!='pink') {
                obj.style.borderColor='black';
            }
            
        }
        
        
        function people_changepicture(mid) { 
            
            alert("this function is not supported in this version!")
                
            
        }
        
        function people_changepicture_process(formobj,mid) {
         
            var ava = xGetElementById('chosen_avatar').value;
            var pic = xGetElementById('custom_avatar_file').value;
            
            if(pic!='') {
                formobj.submit();
                make_busy();
            }
            else {
                make_busy();
                x_setAvatar(ava,cropRes);
            }
            
        }
        
        
        function addPhotoSubmitRes(nnresult) {
            if(nnresult==4) {
                popup_ajax('There was an error.. <a href="javascript:void(close_ajax_popup())">Click</a> to try again.');
            }
            else if(nnresult==3) {
                popup_ajax('Image too big.. <a href="javascript:void(close_ajax_popup())">Click</a> to try again.');
            }
            else if(nnresult==1) {
                popup_ajax('Image type not valid.. <a href="javascript:void(close_ajax_popup())">Click</a> to try again.');
            }
            else  {
                
                people_changepicture_crop(nnresult);
            }
        }
             
        
        function people_changepicture_crop(res) {
           
            var s = res.split('-----::----bjsds__kdbasdf--'); 
            
            var src = s[0];
            var width = s[1];
            var height = s[2];
            
            
            if(width<=80||height<=80) {
                x_cropCustomPicture(src,0,0,width,height,cropRes);
                return;
            }

            txt = '';
           
EOS;

		$functions .= "txt += '".str_replace("'","\\'",$this->_("You can crop the image below. <u>Shift</u> key to resize. The cropped portion will be resized to 80x80"))."<br /><br />';\n";

		$functions .= <<<EOS

            txt += '<img src="'+src+'" id="theImage" style="width:'+width+'px;height:'+height+'px;" />';
EOS;

		$functions .= "txt += '<br /><br /><input type=\"button\" value=\"".$this->_("Continue")."\" onclick=\"people_changepicture_crop2(\''+src+'\')\" />';\n";

		$functions .= <<<EOS

            //txt += '<img src=\"http://grou.ps/wysiwyg_files/tmp/'+res+'\" id=\"theImage\">';
            
            popup_ajax(txt);
            
            xGetElementById('theImage').src = 'http://grou.ps/wysiwyg_files/tmp/'+src;
                        
            
            dd.elements.theCrop.setOpacity(0.5);
            dd.elements.theCrop.defx = xPageX('theImage');
            dd.elements.theCrop.defy = xPageY('theImage');
            dd.elements.theCrop.moveTo(xPageX('theImage'),xPageY('theImage'));
            dd.elements.theCrop.resizeTo(xWidth('theImage')>300?300:xWidth('theImage'),xHeight('theImage')>300?300:xHeight('theImage'));
            dd.elements.theCrop.setDraggable(true);
            dd.elements.theCrop.setScalable(true);
            
            var z = xHeight('theImage')<=xWidth('theImage')?xHeight('theImage'):xWidth('theImage');
            
            dd.elements.theCrop.maxoffr = xWidth('theImage') - dd.elements.theCrop.w;
            dd.elements.theCrop.maxoffb = xHeight('theImage') - dd.elements.theCrop.h;
            dd.elements.theCrop.maxoffl = 0;
            dd.elements.theCrop.maxofft = 0;
            dd.elements.theCrop.minw = 80;
            dd.elements.theCrop.minh = 80;
            dd.elements.theCrop.maxw = z;
            dd.elements.theCrop.maxh = z;
            
            
            dd.elements.theCrop.show();
            
           
        }
        
        
        function people_changepicture_crop2(src) {
            
         
            var x = dd.elements.theCrop.x-xPageX('theImage');
            var y = dd.elements.theCrop.y-xPageY('theImage');
            var w = dd.elements.theCrop.w;
            var h = dd.elements.theCrop.h;
            
            dd.elements.theCrop.hide();
            
            x_cropCustomPicture(src,x,y,w,h,cropRes);
            
        }
        
        
        function cropRes(res) {
            
            dd.elements.theCrop.hide();
            
            var txt = '';
            
            if(res=='1') {

EOS;

		$functions .= "txt += '".$this->_("Avatar successfully changed! <a href=\"javascript:void(history.go(0))\">Refresh</a> this page or <a href=\"javascript:void(close_ajax_popup())\">click</a> to continue...")."';\n";


		$functions .= <<<EOS
                
            }
            else {
                
EOS;

		$functions .= "txt += 'Failure! <a href=\"javascript:void(close_ajax_popup())\">Click</a> to continue...';\n";

		$functions .= <<<EOS

            }
            
            popup_ajax(txt);
            
        }

        
        function make_wall_comment() {
        	var mycomment = xGetElementById('mycomment').value;
        	if(mycomment=="") {
        		alert("It's empty!");
        		return;
        	}
        	var wallid = xGetElementById('wallid').value;
        	make_busy();
        	x_commentOn(wallid,mycomment,commenting_done);
        }
        
        function comment_delete(cid) {
        	if(confirm("Are you sure you want to delete this comment?")) {
        		make_busy();
        		x_commentDelete(cid,comment_deleted);
        	}
        }
        
        function comment_deleted(res) {
        	if(res==1)
        		history.go(0);
        	else
        		popup_ajax("Oops, there was a problem. <a href='javascript:void(0)' onclick='close_ajax_popup()'>Please try again!</a>");
        }

EOS;


		$functions .= "function comment_on(formobj,pid) {\n"
		."\t if(!validateCompleteForm(formobj,'error')) return false;\n"
		. "\t make_busy();\n"
		. "\t var my_comments = xGetElementById('my_comments').value;\n"
		. "\t last_subject = pid;\n"
		. "\t x_commentOn(pid,my_comments,commenting_done);\n"
		. "} \n";

		$functions .= "function commenting_done(res) {\n"
		. "\t var msg = '';\n"
		. "\t if(res=='1') { // true\n"
		. "\t\t history.go(0);\n"
		. "\t }\n"
		. "\t else { \n"
		. "\t\t msg = 'Failure! <a href=\"javascript:void(close_ajax_popup())\">Click</a> to try again...';\n"
		. "\t\t popup_ajax(msg);\n"
		. "\t }\n"
		. "} \n";

		return $functions;
	}

	/*
	* This is static
	* Should be entered as this class grows
	*/
	function getFunctionsToRegister() {

		$to_register = array();
		//$to_register[] = "peopleGetLoveStuff";
		//$to_register[] = "peopleGetHealthStuff";
		//$to_register[] = "peopleGetBusinessStuff";
		$to_register[] = "peopleGetProfile";
		//$to_register[] = "evaluateStuff";
		//$to_register[] = "registerLoveStuff";
		//$to_register[] = "registerHealthStuff";
		//$to_register[] = "registerBusinessStuff";
		$to_register[] = "registerProfile";
		$to_register[] = "getPeopleBlock";

		//$to_register[] = 'addQuoteX'; // not to confuse with JS one
		//$to_register[] = 'removeQuote'; // not to confuse with JS one

		$to_register[] = 'cropCustomPicture';
		$to_register[] = 'setAvatar';
		$to_register[] = "registerProfileX";
		$to_register[] = "registerContactsX";
		$to_register[] = "registerTagsX";
		$to_register[] = "registerFavourites";



		$to_register[] = 'showSectionFavourites';
		//$to_register[] = 'showSectionLove';
		$to_register[] = "showSectionTags";
		//$to_register[] = "showSectionBusiness";
		//$to_register[] = "showSectionHealth";
		$to_register[] = 'hideSectionFavourites';
		//$to_register[] = 'hideSectionLove';
		$to_register[] = "hideSectionTags";
		//$to_register[] = "hideSectionBusiness";
		//$to_register[] = "hideSectionHealth";

		//$to_register[] = "addToWatchlist";

		$to_register[] = "getOperationsBlock";
		$to_register[] = "peopleGetTags";
		$to_register[] = "peopleGetFavourites";
		$to_register[] = "peopleGetProfileX";


		$to_register[] = "commentOn";
		$to_register[] = "commentDelete";

		return $to_register;
	}





	function getExtraHead() {

		global $access_isGroupMember;
		global $rsslink_people;

		$res = "";



		// tooltip
		$res .= <<<EOS
		
		<script type="text/javascript" language="javascript">
		var initial_wall_value = 1;
        	function empty_wall(obj) {
        		if(initial_wall_value==1) {
        			obj.value="";
        			initial_wall_value = 0;	
        		}
        	}
        </script>
		
        
        <style type="text/css">

	
	
        #dhtmlgoodies_tooltip{
            background-color: lightyellow;
            border:1px solid black;
            position:absolute;
            display:none;
            z-index:30;
            padding:2px;
            font-family: Verdana, Arial, sans-serif;
            font-size: 11px;
            font-weight:bold;
		
        }
        #dhtmlgoodies_tooltipShadow{
            position:absolute;
            background-color:#999;
            display:none;
            z-index:29;
            opacity:0.5;
            filter:alpha(opacity=50);
            -khtml-opacity: 0.5;
            -moz-opacity: 0.5;
        }
        </style>
        <SCRIPT type="text/javascript">
        /************************************************************************************************************
        (C) www.dhtmlgoodies.com, October 2005
	
        This is a script from www.dhtmlgoodies.com. You will find this and a lot of other scripts at our website.	
	
        Updated:	
		March, 11th, 2006 - Fixed positioning of tooltip when displayed near the right edge of the browser.
	
        Terms of use:
        You are free to use this script as long as the copyright message is kept intact. However, you may not
        redistribute, sell or repost it without our permission.
	
        Thank you!
	
        www.dhtmlgoodies.com
        Alf Magne Kalleland
	
        ************************************************************************************************************/	
        var dhtmlgoodies_tooltip = false;
        var dhtmlgoodies_tooltipShadow = false;
        var dhtmlgoodies_shadowSize = 4;
        var dhtmlgoodies_tooltipMinWidth = 100;
        var dhtmlgoodies_tooltipMaxWidth = 200;
        function showTooltip(e,tooltipTxt)
        {
		
            var bodyWidth = Math.max(document.body.clientWidth,document.documentElement.clientWidth) - 20;
	
            if(!dhtmlgoodies_tooltip){
                dhtmlgoodies_tooltip = document.createElement('DIV');
                dhtmlgoodies_tooltip.id = 'dhtmlgoodies_tooltip';
                dhtmlgoodies_tooltipShadow = document.createElement('DIV');
                dhtmlgoodies_tooltipShadow.id = 'dhtmlgoodies_tooltipShadow';
			
                document.body.appendChild(dhtmlgoodies_tooltip);
                document.body.appendChild(dhtmlgoodies_tooltipShadow);	
            }
		
            dhtmlgoodies_tooltip.style.display='block';
            dhtmlgoodies_tooltipShadow.style.display='block';
		
            var st = Math.max(document.body.scrollTop,document.documentElement.scrollTop);
		
            var leftPos = e.clientX + 10;
		
            dhtmlgoodies_tooltip.style.width = null;	// Reset style width if it's set 
            dhtmlgoodies_tooltip.innerHTML = tooltipTxt;
            dhtmlgoodies_tooltip.style.left = leftPos + 'px';
            dhtmlgoodies_tooltip.style.top = e.clientY + 10 + st + 'px';
		
            dhtmlgoodies_tooltipShadow.style.left =  leftPos + dhtmlgoodies_shadowSize + 'px';
            dhtmlgoodies_tooltipShadow.style.top = e.clientY + 10 + st + dhtmlgoodies_shadowSize + 'px';
		
            if(dhtmlgoodies_tooltip.offsetWidth>dhtmlgoodies_tooltipMaxWidth){	/* Exceeding max width of tooltip ? */
                dhtmlgoodies_tooltip.style.width = dhtmlgoodies_tooltipMaxWidth + 'px';
            }
		
            var tooltipWidth = dhtmlgoodies_tooltip.offsetWidth;		
            if(tooltipWidth<dhtmlgoodies_tooltipMinWidth)tooltipWidth = dhtmlgoodies_tooltipMinWidth;
		
            dhtmlgoodies_tooltip.style.width = tooltipWidth + 'px';
            dhtmlgoodies_tooltipShadow.style.width = dhtmlgoodies_tooltip.offsetWidth + 'px';
            dhtmlgoodies_tooltipShadow.style.height = dhtmlgoodies_tooltip.offsetHeight + 'px';		
		
            if((leftPos + tooltipWidth)>bodyWidth){
                dhtmlgoodies_tooltip.style.left = (dhtmlgoodies_tooltipShadow.style.left.replace('px','') - ((leftPos + tooltipWidth)-bodyWidth)) + 'px';
                dhtmlgoodies_tooltipShadow.style.left = (dhtmlgoodies_tooltipShadow.style.left.replace('px','') - ((leftPos + tooltipWidth)-bodyWidth) + dhtmlgoodies_shadowSize) + 'px';
            }
        }
	
        function hideTooltip()
        {
            dhtmlgoodies_tooltip.style.display='none';
            dhtmlgoodies_tooltipShadow.style.display='none';		
        }
	
        </SCRIPT>	
        
        
        
EOS;



		$res .= <<<EOS
        
        <script>
        
        function peopleHideSection(id,fast) {
           
           var topobj = xGetElementById('top_block_no_'+id)?'top_block_no_'+id:'hidden_block_'+id;
           
           if(fast) {
		   if(!document.all)
			   changeOpac(25,topobj);
			   else {
				   changeOpac(25,'block_title_no_'+id);
				   changeOpac(25,'block_no_'+id);
			   }
           }
           else {
		    if(!document.all)
			    opacity(topobj,100,25,3000);
		    else {
				   changeOpac(25,'block_title_no_'+id);
				   changeOpac(25,'block_no_'+id);
			   }
           }
        
       }
       
        function peopleShowSection(id,fast) {
            
            var topobj = xGetElementById('top_block_no_'+id)?'top_block_no_'+id:'hidden_block_'+id;
            
            if(fast) {
		    if(!document.all)
                changeOpac(100,topobj);
		else {
				   changeOpac(100,'block_title_no_'+id);
				   changeOpac(100,'block_no_'+id);
			   }
            }
            else {
		     if(!document.all)
                opacity(topobj,25,100,3000);
	else {
				   changeOpac(100,'block_title_no_'+id);
				   changeOpac(100,'block_no_'+id);
			   }
            }
        
       }
        
        </script>
        
EOS;

		// if($access_isGroupMember) {
		$rsslink_people = true;
		$res .= "<link rel=\"alternate\" type=\"application/rss+xml\" title=\"Grou.p People\" href=\"http://grou.ps/rss/{}/people\" />";
		// }

		return $res;


	}


	function getRSS() {

		$data = $this->getPeople();
		$rss = array();


		$i=0;
		foreach($data as $d) {

			$rss[$i]['description'] = '';
			$rss[$i]['link'] = "http://grou.ps/{}/people/{$d['member_username']}";
			$rss[$i]['title'] = $d['name'];
			// TODO: make it better
			$rss[$i]["dc:date"] = date('Y-m-d')."T00:00:00Z";

			$i++;

		}


		return $rss;

	}


	function getHelpText() {


		$text = <<<EOS
        
<p>
You're currently visiting the <u>People</u> section of your grou.p<br />
This section is intended to give insight about grou.p members.<br /><br />
            The section is divided into 2 columns.
The right one consists of a member list block and an operational block designed for watchlist interactions.
The left one consists of numerous blocks; Some of them are required (so all users should have it), while some others 
are optional... If you are logged in and you are already a member of this grou.p, you can edit these blocks using 
the inline operational elements inside the blocks. Now let's see what blocks you may see in the left column:
<br /><br />
<span class="bigger">Profile (required)</span><br />
Here you can set an avatar, or optionally upload your photo. If you chose to upload, you can crop your image using
our embedded image editor. Profile block lets you share basic information about yourself. The information you enter
is automatically reformatted to make it as eye-candy as possible. You can also enter your contact information so that
your online presence can be shared with your friends.
<br /><br />
<span class="bigger">My Tags (optional)</span><br />
So you wanna share more information? My Tags block lets you share as much as you want.. And the good thing is that
you are completely free to decide what to share. As opposed to others, we don't ask you questions and expect you to
respond them in a limited set of answers. Your tags should be entered in importance order. Don't forget that
more important a tag, bigger its size is.
<br /><br />
<span class="bigger">Favourites (optional)</span><br />
Just enter your favourite movies, music, actors.. They'll be automatically reformatted with their album covers,
photos and shared with your friends.. 
<br /><br />
<span class="bigger">Love/Business/Health Stuff (optional)</span><br />
Let your friends know what's going on in your love/business/health life. Take special care not to confuse this section with Blogs;
this section is just to give a general overview of all.. 
<br /><br />
<!--
<span class="bigger">Quotes (personal)</span><br />
If you see this block then you are visiting your own People page. This block is only shown for editing purposes. It lets
you add/remove quotes so that they'll be randomly shown at the top of each grou.p pages.
<br /><br />
-->
Hope this helped. If you still face problems with one of the elements on this page just point your mouse cursor over it and
chances are you'll be shown an explanatory popup at the bottom right side of the screen.
<br /><br /><br />
<a href="http://support.grou.ps/list.php?6">Support Forums</a>
<br />
<a href="http://support.grou.ps/list.php?1">See FAQ</a>
</p>
        
EOS;


		return $text;


	}




}









?>