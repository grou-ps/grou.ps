<?php

include_once("includes/sajax.php");

require_once 'includes/VariableSecurity.php';
require_once 'includes/GeneralFunctions.php';
include_once('includes/Smarty/Smarty.class.php'); // global templating engine, Smarty
require_once('includes/Group.class.php');
include_once('includes/Access.php');
require_once('includes/bbcode.php');
require_once("includes/Mailer.class.php");
require_once("includes/Analytics.class.php");
require_once("includes/TranslationEngine.class.php");
include_once('includes/cphplib/cphplib.inc');
include_once('configs/globals.php');
include_once('includes/GlobalDB.php');



/**
 * The first thing to do can be to call the Analytics
 * class which will output nothing; but only keep
 * statistical information.
 */
$analytics = new Analytics();

/**
 * this can be done on any page
 * we use it without making any check; but it is
 * safe; because the function itself makes the necessary
 * checks
 */
$analytics->registerRefererURL();


$tpl = new Smarty;
$tpl->compile_dir = 'templates/templates_c';
$tpl->config_dir = 'templates/configs';
$tpl->cache_dir = 'templates/cache';



/**
 * Is this an authenticated user
 */
$access_isAuthenticated = isAuthenticated();
$access_name = ''; // name and surname of the user
$access_isGroupMember = false; // is he/she a group member
$access_isGroupAdmin = false; // is he/she admin of the group







/**
 * Create a new Group object with a single parameter;
 * $group_name
 */
$group = new Group();




// turn on the translation engine
$grouplang = $group_language;
$treng = new TranslationEngine($grouplang);



/* Get the group template
* and set the templating engine
*/
$group_template = $group_template;


/*
* assign the group keywords
*/
$tpl->assign('group_keywords',$group_keywords);

error_log("istanbul guzeldir - 1");


/*
* Set the authentication variables if this is an
* authenticated user
*/
if($access_isAuthenticated) {
	// $access_name = $access->getName(); // put user name if he/she is auth'd
	if(isGroupAdmin()) {
		$access_isGroupMember = true;
		$access_isGroupAdmin = true;
	}
	else {
		$access_isGroupMember = isGroupMember();
	}
}

$defmod = "people";
$function = "people";
if(isset($_GET['function']))
	$function = strtolower($_GET['function']);

$num_of_modules = 0;

$num_of_modules += intval($allow_wiki);

$num_of_modules += intval($allow_talks);

$no_menu = false;
if($num_of_modules==1) {
	$no_menu = true;
}

$tpl->assign('no_menu',$no_menu);

$tpl->assign('allow_wiki',$allow_wiki);
$tpl->assign('allow_talks',$allow_talks);
$tpl->assign('allow_people',$allow_people);



$tabs = array();

$tabs[] = array('module'=>'home','title'=>'Home','weight'=>-2,'help'=>'','link'=>'','rename'=>'');
if($access_isGroupMember) {
	$tabs[] = array('module'=>'','title'=>'My Page','weight'=>-1,'help'=>'','link'=>"{$service_host}people/{$_SESSION['valid_user']}",'rename'=>'');
}
else {
	$tabs[] = array('module'=>'','title'=>'My Page','weight'=>-1,'help'=>'','link'=>"{$service_host}join",'rename'=>'');
}

if($allow_wiki)
$tabs[] = array('module'=>'wiki','title'=>'Wiki','weight'=>$weight_wiki,'help'=>'','link'=>'','rename'=>'');

if($allow_talks)
$tabs[] = array('module'=>'talks','title'=>'Talks','weight'=>$weight_talks,'help'=>'','link'=>'','rename'=>'');

if($allow_people)
$tabs[] = array('module'=>'people','title'=>'People','weight'=>$weight_people,'help'=>'','link'=>'','rename'=>'');

$tabs = multi_sort_2($tabs,'weight',false);

$tpl->assign('tabs',$tabs);


$user_pending_membership_request = false;
if($access_isAuthenticated&&!$access_isGroupMember)
$user_pending_membership_request = $group->pendingMembershipRequest($_SESSION['valid_user']);

$tpl->assign('service_host', $service_host);

$tpl->assign('access_isGroupAdmin', $access_isGroupAdmin);
$tpl->assign('access_isGroupMember', $access_isGroupMember);
$tpl->assign('access_isAuthenticated', $access_isAuthenticated);
$tpl->assign('user_pending_membership_request', $user_pending_membership_request);



/**
 * Firstly, let's define the header
 */
$tpl->assign('group_title', $group_title);

$tpl->assign('group_desc', $short_group_desc);


sajax_init();

$ajax_functions = '';



/**
 * now it is time to generate 
 * page contents
 */

include('includes/PageGenerator.class.php');

/**
 * these ones should be filled by blogs
 * they'll be appended to template file
 */
$page_content = '';
$extra_head = '';
$thehelp_text = '';

$pg = new PageGenerator();
/**
 * layouts are
 * EqualColumns (LeftColumn, RightColumn)
 * WithSidebar (MainColumn, Sidebar)
 * FullColumn (Column)
 */



function populateJoinStep() {

	header('location:'.$service_host.'?function=join');
	exit;

}



function warnPrivatePage($from="") {

	global $service_host;

	if(isAuthenticated()) {
		header("location:{$service_host}?function=join&obj1=warn");
	}
	elseif(!empty($from))
		header("location:{$service_host}?function=signin&obj1=warn&obj2={$from}");
	else
		header("location:{$service_host}?function=signin&obj1=warn");

	exit;

}

function warnWelcomeSection() {

	global $pg;
	global $page_content;
	global $treng;

	$text = $treng->_("This is a new section. If you are the administrator of this group, you can visit Group Control Panel to enable/disable this section.","grouppage");

	$pg->setLayout($pg->FullColumn);

	$pg->addBlock($pg->Column, $treng->_('New Section',"grouppage"), $text);

	if($pg->generateHTML()) {
		$page_content .= $pg->getHTML();
	}
	else {
		die('Could not generate HTML');
	}

}



$_nodata_warning = "<center><img src=\"{$service_host}images/nodata.png\" alt=\"No Data\" border=\"0\" /></center>";

if($function==""||$function=="/") {
	$function = $defmod;
}


switch($function) {

	case 'join':

		if(!$access_isGroupMember) {

			if(!isAuthenticated()) {
				
				$pg->setLayout($pg->EqualColumns);

				$signin = "<form method=\"POST\" action=\"{$service_host}?function=signin\">";

				$temp_translation_string_1 = $treng->_("Email","groupjoin");
				$temp_translation_string_2 = $treng->_("Password","groupjoin");
				$temp_translation_string_3 = $treng->_("Keep me signed in unless I sign out","groupjoin");
				$temp_translation_string_4 = $treng->_("Sign In","groupjoin");
				$trengvar = array($service_host,$group_name);
				$temp_translation_string_5 = $treng->_("Forgot your username and/or password? <a href=\"%TRENGVAR_1%%TRENGVAR_2%/recover\">Recover</a>!<br />Alternatively, you can sign in via your <a href=\"%TRENGVAR_1%%TRENGVAR_2%/openid\">OpenID</a>","authentication",$trengvar);
				$temp_translation_string_6 = $treng->_("Sign Up","groupjoin");

				$signin .= <<<EOS
     
		                
		                <input type="hidden" name="join" value="1" />
		                
		                <div style="height:30px;width:100%;padding:5px;">
		                <div style="float:left;width:100px;font-weight:bold;">{$temp_translation_string_1}</div>
		                <div style="float:left;width:auto;"><input type="text" name="username"></div>
		                </div>
		                
		                
		                <div style="height:30px;width:100%;padding:5px;">
		                <div style="float:left;width:100px;font-weight:bold;">{$temp_translation_string_2}</div>
		                <div style="float:left;width:auto;"><input type="password" name="password"></div>
		                </div>
		             
		                <div style="height:30px;width:100%;padding:5px;">
		                <input type="checkbox" name="rememberme" value="1" checked /> {$temp_translation_string_3}
		                </div>
		                   
		                <div style="padding:5px;">
		                <input type="submit" value="{$temp_translation_string_4}" />
		                </div>
		                
		                
		                <div style="padding:5px; margin: 5px 0 0 0;">
		                {$temp_translation_string_5}
		                </div>
		                
		                
		                </form>
		                
                
EOS;


				$signup .= "<form method=\"POST\" action=\"{$service_host}?function=signup\">";

				$signup .= <<<EOS
						                
		                <input type="hidden" name="join" value="1" />
		                
		                
		                <div style="height:30px;width:100%;padding:5px;">
		                <div style="float:left;width:100px;font-weight:bold;">{$temp_translation_string_1}</div>
		                <div style="float:left;width:auto;"><input type="text" name="email" value="@"></div>
		                </div>
		                
		                <div style="height:30px;width:100%;padding:5px;">
		                <div style="float:left;width:100px;font-weight:bold;">{$temp_translation_string_2}</div>
		                <div style="float:left;width:auto;"><input type="password" name="password"></div>
		                </div>
		                
		                <div style="height:30px;width:100%;padding:5px;">
		                <input type="checkbox" name="rememberme" value="1" checked /> {$temp_translation_string_3}
		                </div>
		                
		                <div style="padding:5px;">
		                <input type="submit" value="{$temp_translation_string_6}" />
		                </div>
		                
	
		                
		                </form>
		                
                
EOS;



				$pg->addBlock($pg->Column, "", $treng->_("In order to join, you should register first. Sign in if you already have an account, or sign up to get one.","groupjoin"), "", false, true);
				$pg->addBlock($pg->LeftColumn, $temp_translation_string_4,  $signin, "");
				$pg->addBlock($pg->RightColumn, $temp_translation_string_6,  $signup, "");



			}
			else {


				$pg->setLayout($pg->FullColumn);

				if(isset($_GET['obj1'])&&$_GET['obj1']=="warn") {
					$text = "<p style=\"text-align:center;font-size:x-large;\">".$treng->_("Only group members can view this section.","")."</p><p align=\"center\"><a href=\"{$service_host}{$group_name}/join\">".$treng->_("Click here to become a member instantly","")."</a></p><p align=\"center\">".$treng->_("you are already logged in","").".</p>";
					$pg->addBlock($pg->Column, "", $text, "");
				}
				else {


					if($can_anyone_join) {

						$mem_req = $group->addNewMember($_SESSION['valid_user']);

						if(!$mem_req)
						die('Try again later...');
						else {
							// we should notify the admins
							$mailr = new Mailer($_SESSION['valid_user'],$group_name);
							$m = $mailr->notifyJoining($group_title);

						}



						populateJoinStep();



					}
					else {


						if(isset($_POST['message'])) {

							$mem_req = $group->addMembershipRequest($_POST['message']);

							if(!$mem_req)
							die('Try again later...');
							else {
								// we should notify the admins
								$mailr = new Mailer($_SESSION['valid_user'],$group_name,true);
								$m = $mailr->notifyJoiningRequest($group_title);

								$text = "<p>".$treng->_("We have sent your request, you should receive a reply shortly, thanks for your interest.","groupjoin")."</p>";

								$pg->addBlock($pg->Column, "", $text, "");
							}
						}
						else {

							$temp_translation_string_1 = $treng->_("<strong>This group requires admin authorization for memberships.</strong><br />Please send a message to the admin and explain why you want to join, the admin will evaluate your request, and you will receive a confirmation email when (s)he accepts.","groupjoin");
							$temp_translation_string_2 = $treng->_("Send","groupjoin");


							$text = <<<EOS

		<p>
		$temp_translation_string_1;
		</p>
EOS;


							if(!isset($forfacebook)||$forfacebook!=2)
							$text .= "<form method=\"POST\" action=\"{$service_host}{$group_name}/join\">";
							else
							$text .= '<form method="post" action="http://_grou.ps/forfacebook.php?group_name='.$group_name.'&function=join">';


							$text .= <<<EOS
		<p>
		<textarea name="message"></textarea>
		</p>
		
		<p>
		<input type="submit" value="{$temp_translation_string_2}" />
		</p>
		</form>
		
EOS;

							$pg->addBlock($pg->Column, $treng->_("Join This Group","groupjoin"), $text, "");

						}

					}

				}


			}







			if($pg->generateHTML()) {
				$page_content .= $pg->getHTML();
			}
			else {
				die('Could not generate HTML');
			}
			break;





		}
		elseif(@$_POST['joinform']==1) {


			ob_start();


			$u = new User($_SESSION['valid_user']);

			if(!isset($_POST['name']) || empty($_POST['name'])) {
				$u->setNameSurname($group_name,$_SESSION['valid_user']);
			}
			else {
				@$u->setNameSurname($group_name,$_POST['name']);
			}



			$a = @$u->setBirthday($group_name,$_POST['birthday']);


			$a = @$u->setNationality($group_name,$_POST['nationality']);
			$a = @$u->setSexe($group_name,$_POST['sexe']);


			if(@!empty($_POST['blog'])) {
				include_once('includes/Page.BlogsYYYY.class.php');
				$b = new BlogsPage($group_name);
				@$b->setUserBlog($_POST['blog']);
			}


			include_once('includes/Page.LinksZZZZ.class.php');
			$l = new LinksPage($group_name);

			if(@!empty($_POST['digg_username'])) {
				$l->addMember($_POST['digg_username'],"all","digg");
			}

			if(@!empty($_POST['delicious_username'])) {
				$l->addMember($_POST['delicious_username'],"","delicious");
			}


			if(@!empty($_POST['reddit_username'])) {
				$l->addMember($_POST['reddit_username'],"","reddit");
			}

			if(@!empty($_POST['flickr_username'])) {
				include_once('includes/Page.PhotosYYY.class.php');
				$p= new PhotosPage($group_name);
				@$p->changePersonalFlickrAccount($u->getID(),$_POST['flickr_username']);
			}


			if(isset($_FILES['avatar'])) {



				include_once('includes/phpThumb/phpthumb.class.php');
				include_once('includes/custom_avatar.inc.php');


				$phpThumb = new phpThumb();
				$phpThumb->setParameter('config_allow_src_above_docroot', true); // very important

				$phpThumb->setSourceFilename($_FILES['avatar']['tmp_name']);

				$phpThumb->setParameter('w', 80);
				$phpThumb->setParameter('h', 80);
				$phpThumb->setParameter('zc', 1);

				$phpThumb->setParameter('config_output_format', 'png');

				if ($phpThumb->GenerateThumbnail()) {


					include_once('configs/globals.php');
					$membership_id = $u->getMembershipID($group_name);

					$bigfile = $app_base."avatars/{$membership_id}/80.png";
					$smallfile = $app_base."avatars/{$membership_id}/16.png";
					$target = $app_base."avatars/{$membership_id}/";

					@mkdir($target);

					$phpThumb->RenderToFile($bigfile);
					makeSmallAvatar($bigfile,$smallfile);
					makeFlags($smallfile,$target);


					$u->setCustomAvatar($group_name);



				}


			}
			else {



				$membership_id = $u->getMembershipID($group_name);
				$u->copyAvatar($group_name, $u->getAvatarNo($group_name), $membership_id);

			}



			ob_end_clean();

			//header("location:{$service_host}{$group_name}/");
			//exit;


			$pg->setLayout($pg->FullColumn);

			$temp_translation_string_1 = $treng->_("<big><strong>You have successfully joined this group! <a href=\"%TRENGVAR_1%%TRENGVAR_2%/\">Continue...</a></strong></big><br /><br />To edit the content, go to the related page (to change your blog address, go to Blogs section) and click one of the control buttons as shown:<br /><br /><img src=\"images/controlshow.jpg\" alt=\"Example Control\" border=\"0\" width=\"600\" height=\"389\" />","groupjoin",array($service_host,$group_name));


			$xhtml = "<div align=\"center\">
					{$temp_translation_string_1}
					</div>";

			$pg->addBlock($pg->Column, "",  $xhtml, "");


			if($pg->generateHTML()) {
				$page_content .= $pg->getHTML();
			}
			else {
				die('Could not generate HTML');
			}
			break;


		}
		else {


			$pg->setLayout($pg->FullColumn);

			//populateJoinStep();
			header("location:{$service_host}".$group_name);
			exit;


			if($pg->generateHTML()) {
				$page_content .= $pg->getHTML();
			}
			else {
				die('Could not generate HTML');
			}
			break;

		}





	case 'signup':

		if(!isAuthenticated()) {

			$form = "";
			$signin_error_msg = false;
			$rememberme = false;

			if(isset($_POST['email'])&&isset($_POST['password'])&&isset($_POST['join'])) {

				if(@$_POST['rememberme']==1)
				$rememberme = true;

				$res = signup('',$_POST['password'],$_POST['email'],true,-1,false,$rememberme);

				$res_success = $res[0];
				$res_failure_reason = $res[1];

				if($res_success) {

					// no conditional anymore
					// what's the logic of signing up otherwise!
					header("location:{$service_host}{$group_name}/join");
					exit;

					if($_POST['join']==1) {
						header("location:{$service_host}{$group_name}/join");
						exit;
					}
					else {
						$temp_translation_string_1 = $treng->_("<big><strong>You have successfully joined this group! <a href=\"%TRENGVAR_1%%TRENGVAR_2%/\">Continue...</a></strong></big><br /><br />To edit the content, go to the related page (to change your blog address, go to Blogs section) and click one of the control buttons as shown:<br /><br /><img src=\"images/controlshow.jpg\" alt=\"Example Control\" border=\"0\" width=\"600\" height=\"389\" />","groupjoin",array($service_host,$group_name));
						$pg->setLayout($pg->FullColumn);
						$xhtml = "<div align=\"center\">{$temp_translation_string_1}</div>";

						$pg->addBlock($pg->Column, "",  $xhtml, "");


						if($pg->generateHTML()) {
							$page_content .= $pg->getHTML();
						}
						else {
							die('Could not generate HTML');
						}





						break;

					}
				}
				else {
					$signin_error_msg = true;
				}
			}



			$pg->setLayout($pg->FullColumn);

			$temp_translation_string_1 = $treng->_("Ooops... There were some errors!","signup");
			$temp_translation_string_2 = $treng->_("Email","signup");
			$temp_translation_string_3 = $treng->_("Password","signup");
			$temp_translation_string_4 = $treng->_("Keep me signed in unless I sign out - <i>uncheck if on a shared computer</i>","signup");
			$temp_translation_string_5 = $treng->_("Sign Up","signup");
			$temp_translation_string_6 = $treng->_("Got an account? <a href=\"%TRENGVAR_1%%TRENGVAR_2%/signin\">Sign In</a>!","signup",array($service_host,$group_name));
			$temp_translation_string_7 = $treng->_("About %TRENGVAR%","authentication",$group_title);

			if($signin_error_msg) {
				$form .= <<<EOS
		                	
		                	<p><img src="{$service_host}images/warning_obj.gif" width="16" height="16" hspace="0" vspace="0" alt="" border="0" align="absbottom" />
		                	<strong>{$temp_translation_string_1} {$res_failure_reason}.</strong></p>
		                	<p>&nbsp;</p>
EOS;
}

$group_description = $group->getDescription();
$form .= '<table cellspacing="0" cellpadding="0" border="0" width="100%"><tr><td width="70%" valign="top">';

if(!isset($forfacebook)||$forfacebook!=2)
$form .= "<form method=\"post\" action=\"{$service_host}{$group_name}/signup\">";
else
$form .= '<form method="post" action="http://_grou.ps/forfacebook.php?group_name='.$group_name.'&function=signup">';

$form .= <<<EOS
		                <input type="hidden" name="join" value="0" />
		                
		                
		                <div style="height:30px;width:100%;padding:5px;">
		                <div style="float:left;width:100px;font-weight:bold;">{$temp_translation_string_2}</div>
		                <div style="float:left;width:auto;"><input type="text" name="email" value="@"></div>
		                </div>
		                
		                <div style="height:30px;width:100%;padding:5px;">
		                <div style="float:left;width:100px;font-weight:bold;">{$temp_translation_string_3}</div>
		                <div style="float:left;width:auto;"><input type="password" name="password"></div>
		                </div>
		                
		                <div style="height:30px;width:100%;padding:5px;">
		                <input type="checkbox" name="rememberme" value="1" checked /> {$temp_translation_string_4}
		                </div>
		                
		                <div style="padding:5px;">
		                <input type="submit" value="{$temp_translation_string_5}" />
		                </div>
		                
		                
		                <div style="padding:5px; margin: 5px 0 0 0;">
		                {$temp_translation_string_6}
		                </div>
		                
		                
		                </form>
		         		
		                </td><td width="30%" valign="top" style="border-left:1px #000 dotted; padding-left:10px;">
		                <p><strong>{$temp_translation_string_7}</strong></p>
		                {$group_description}		                
		                </td></tr>
		                </table>
                
EOS;


$pg->addBlock($pg->Column, $treng->_('Sign Up',""),  $form, "");



if($pg->generateHTML()) {
	$page_content .= $pg->getHTML();
}
else {
	die('Could not generate HTML');
}





break;
		}


	case 'recover':

		if(!isAuthenticated()) {


			$pg->setLayout($pg->FullColumn);
			$res = "";

			if(isset($_POST['email'])) {
				$res = recoverLoginInfo($email);
				if($res=="TRUE") {
					$recover = "<p>".$treng->_("We have sent you an email with your login information. Please check your inbox and follow the directions.","recover")."</p>";
				}
				else {
					$recover = <<<EOS
							<p><img src="{$service_host}images/warning_obj.gif" width="16" height="16" hspace="0" vspace="0" alt="" border="0" align="absbottom" /> {$res}</p>
EOS;
}
}


if($res!="TRUE") {

	if(!isset($forfacebook)||$forfacebook!=2)
	$recover .= "<form method=\"POST\" action=\"{$service_host}{$group_name}/recover\">";
	else
	$recover .= '<form method="post" action="http://_grou.ps/forfacebook.php?group_name='.$group_name.'&function=signup">';

	$temp_translation_string_1 = $treng->_("Email","recover");
	$temp_translation_string_2 = $treng->_("Recover","recover");

	$recover .= <<<EOS

					<p>
					<strong>{$temp_translation_string_1}</strong>
					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					<input type="text" name="email" value="@" />
					
					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					<input type="submit" value="{$temp_translation_string_2}" />
					</p>
					</form>
					
EOS;
}

$pg->addBlock($pg->Column, $treng->_("Recover your username & password","recover"), $recover, "");



if($pg->generateHTML()) {
	$page_content .= $pg->getHTML();
}
else {
	die('Could not generate HTML');
}



break;


		}

	case 'logout':
	case 'signout':

		if(isAuthenticated()) {
			logout();
			header("location: {$service_host}{$group_name}");
			exit;
			break;
		}


	case 'settings':
	case 'dashboard':

		if(isAuthenticated()) {



			$page = $_GET['obj1'];

			switch($page) {


				case "password":


					if(isOpenIDAccount())
					die("Since this is an OpenID account, you cannot Change Credentials, try to visit your OpenID provider's web site.");

					$error = "";
					$success = false;


					if(@$_GET['obj2']=="change_password" && isset($_POST["oldpassword"])) {
						if(@empty($_POST["oldpassword"])||@empty($_POST["newpassword1"])||@empty($_POST["newpassword2"])) {

							$error = $treng->_("Inputs missing","dashboard");

						}
						else {

							if($_POST["newpassword1"]!=$_POST["newpassword2"]) {

								$error = $treng->_("New passwords don't match","dashboard");

							}

							else {

								$res = changePassword($_POST["oldpassword"],$_POST["newpassword1"]);

								$success = $res[0];

								if(!$success) {

									$error = $res[1];

								}


							}

						}
					}

					if(@$_GET['obj2']=="change_email" && isset($_POST["oldpassword"])) {
						if(@empty($_POST["oldpassword"])||@empty($_POST["newemail"])) {

							$error = $treng->_("Inputs missing","dashboard");

						}
						else {



							$res = changeEmail($_POST["oldpassword"],$_POST["newemail"]);

							$success = $res[0];

							if(!$success) {

								$error = $res[1];
							}

						}
					}

					if($success) {
						$html = "<span style=\"color:green;\">".$treng->_("Email and/or Password changed successfully!","dashboard")."</span>";
					}
					elseif(!empty($error)) {
						$html = "<span style=\"color:red;\">{$error}</span>";
					}


					$pg->setLayout($pg->FullColumn);

					if(!isset($forfacebook)||$forfacebook!=2)
					$html .= "<form method=\"POST\" action=\"{$service_host}{$group_name}/dashboard/password/change_password\">";
					else
					$html .= '<form method="post" action="http://_grou.ps/forfacebook.php?group_name='.$group_name.'&function=dashboard&obj1=password&obj2=change_password">';

					$temp_translation_string_1 = $treng->_("Old Password","dashboard");
					$temp_translation_string_2 = $treng->_("New Password","dashboard");
					$temp_translation_string_3 = $treng->_("Confirm New Password","dashboard");
					$temp_translation_string_4 = $treng->_("Change My Password","dashboard");



					$html .= <<<EOS

<table border="0" cellpadding="5" cellspacing="0" width="100%">
    


<tr>
	<td>{$temp_translation_string_1}</td>
	<td><input onfocus="inputfocus(this)" onblur="inputblur(this)" type="password" name="oldpassword" size="40" />
	
</td></tr>

<tr>
<td>{$temp_translation_string_2}</td>
<td><input onfocus="inputfocus(this)" onblur="inputblur(this)" type="password" name="newpassword1" size="40" /></td>
</tr>

<tr>
	<td>{$temp_translation_string_3}</td>
	<td><input onfocus="inputfocus(this)" onblur="inputblur(this)" type="password" name="newpassword2" size="40" /></td>
</tr>

<tr>
<td>&nbsp;</td>
<td><input type="submit" value="{$temp_translation_string_4}" /></td>
</tr>

</table>
    </form>
				
EOS;

					if(!isset($forfacebook)||$forfacebook!=2)
					$html_email .= "<form method=\"POST\" action=\"{$service_host}{$group_name}/dashboard/password/change_email\">";
					else
					$html_email .= '<form method="post" action="http://_grou.ps/forfacebook.php?group_name='.$group_name.'&function=dashboard&obj1=password&obj2=change_email">';

					$temp_translation_string_1 = $treng->_("Password","dashboard");
					$temp_translation_string_2 = $treng->_("New Email","dashboard");
					$temp_translation_string_3 = $treng->_("Change My Email","dashboard");

					$html_email .= <<<EOS

				    
<table border="0" cellpadding="5" cellspacing="0" width="100%">
    

<tr>
	<td>{$temp_translation_string_1}</td>
	<td><input onfocus="inputfocus(this)" onblur="inputblur(this)" type="password" name="oldpassword" size="40" />
	
</td></tr>



<tr>
<td>{$temp_translation_string_2}</td>
<td><input onfocus="inputfocus(this)" onblur="inputblur(this)" type="text" name="newemail" size="40" /></td>
</tr>


<tr>
<td>&nbsp;</td>
<td><input type="submit" value="{$temp_translation_string_3}" /></td>
</tr>

</table>
    </form>
				
EOS;


					if(!isOpenIDAccount())
					$welcome_html = $treng->_("DASHBOARD &nbsp; - &nbsp; [ <a href=\"%TRENGVAR_1%%TRENGVAR_2%/dashboard/password\">Change Credentials</a> ] &middot; <a href=\"%TRENGVAR_1%%TRENGVAR_2%/dashboard/messages\">My Incoming Messages</a> &middot; <a href=\"%TRENGVAR_1%%TRENGVAR_2%/dashboard/spread\">Invite People</a> &middot; <a href=\"%TRENGVAR_1%%TRENGVAR_2%/dashboard/signout\">Sign Out</a>","dashboard",array($service_host,$group_name));
					else
					$welcome_html = $treng->_("DASHBOARD &nbsp; - &nbsp; <a href=\"%TRENGVAR_1%%TRENGVAR_2%/dashboard/messages\">My Incoming Messages</a> &middot; <a href=\"%TRENGVAR_1%%TRENGVAR_2%/dashboard/spread\">Invite People</a> &middot; <a href=\"%TRENGVAR_1%%TRENGVAR_2%/dashboard/signout\">Sign Out</a>","dashboard",array($service_host,$group_name));


					$pg->addBlock($pg->Column, "", $welcome_html, "",false, true);
					$pg->addBlock($pg->Column, $treng->_("Change My Password","dashboard"), $html, "");
					$pg->addBlock($pg->Column, $treng->_("or Change My Email","dashboard"), $html_email, "");

					break;






				case "messages":


					include_once("includes/InternalMessages.class.php");

					$temp_translation_string_1 = $treng->_("To","dashboard");
					$temp_translation_string_2 = $treng->_("Message","dashboard");


					$html = <<<EOS
		
					<table width="100%" border="0" cellpadding="10" cellspacing="0">
<col style="width:200px;" />
<col style="width:auto;" />
<tr>
<th align="left">{$temp_translation_string_1}</th>
<th align="left">{$temp_translation_string_2}</th>
</tr>
EOS;

					$userid = _getMemberID($_SESSION['valid_user']);
					$im = new InternalMessages();
					$msgs = $im->getOutgoingMessages($userid);
					$i = 0;
					$r = "";
					foreach($msgs as $msg) {

						$res_msg = $msg['msg'];
						_filter_res_var($res_msg);
						$res_msg = nl2br($res_msg);
						$res_counter = "";

						$subj_username = _getMemberUsername($msg['to']);
						$subj = new User($subj_username);
						$res_counter = $subj->getEmail();
						$res_date = getStyledDateDiff($msg['date'],date('Y-m-d'));

						$i++;



						if($i%2==0) {
							$r = " style=\"background-color:#eee;\"";
						}
						else {
							$r = "";
						}

						$html .= <<<EOS

<tr{$r}>
<td valign="top">
<a href="javascript:void(popUp('msg.do?to={$subj_username}&toname={$res_counter}'))">{$subj_username}</a><br />{$res_date}
</td>
<td>
{$res_msg}
</td>
</tr>

EOS;

}


$html .= "</table>";


if(!isOpenIDAccount())
$welcome_html = $treng->_("DASHBOARD &nbsp; - &nbsp; <a href=\"%TRENGVAR_1%%TRENGVAR_2%/dashboard/password\">Change Credentials</a> &middot; [ <a href=\"%TRENGVAR_1%%TRENGVAR_2%/dashboard/messages\">My Incoming Messages</a> ] &middot; <a href=\"%TRENGVAR_1%%TRENGVAR_2%/dashboard/spread\">Invite People</a> &middot; <a href=\"%TRENGVAR_1%%TRENGVAR_2%/dashboard/signout\">Sign Out</a>","dashboard",array($service_host,$group_name));
else
$welcome_html = $treng->_("DASHBOARD &nbsp; - &nbsp; [ <a href=\"%TRENGVAR_1%%TRENGVAR_2%/dashboard/messages\">My Incoming Messages</a> ] &middot; <a href=\"%TRENGVAR_1%%TRENGVAR_2%/dashboard/spread\">Invite People</a> &middot; <a href=\"%TRENGVAR_1%%TRENGVAR_2%/dashboard/signout\">Sign Out</a>","dashboard",array($service_host,$group_name));

$pg->setLayout($pg->FullColumn);
$pg->addBlock($pg->Column, "", $welcome_html, "",false, true);
$pg->addBlock($pg->Column, "", $html, "");

break;






case "spread":

	include_once("includes/GroupAdmin.php");

	$html = "";


	if(@!empty($_POST['gnippet_invite'])) {
		@GA_inviteViaEmail($_POST['gnippet_invite'],$_POST['gnippet_invitation_text']);
		$html .= "<p><span style=\"color:green\">Invitations sent!</span></p>";
	}


	$html .= GA_getPromoteEmail(false);


	if(!isOpenIDAccount())
	$welcome_html = $treng->_("DASHBOARD &nbsp; - &nbsp; <a href=\"%TRENGVAR_1%%TRENGVAR_2%/dashboard/password\">Change Credentials</a> &middot; <a href=\"%TRENGVAR_1%%TRENGVAR_2%/dashboard/messages\">My Incoming Messages</a> &middot; [ <a href=\"%TRENGVAR_1%%TRENGVAR_2%/dashboard/spread\">Invite People</a> ] &middot; <a href=\"%TRENGVAR_1%%TRENGVAR_2%/dashboard/signout\">Sign Out</a>","dashboard",array($service_host,$group_name));
	else
	$welcome_html = $treng->_("DASHBOARD &nbsp; - &nbsp; <a href=\"%TRENGVAR_1%%TRENGVAR_2%/dashboard/messages\">My Incoming Messages</a> &middot; [ <a href=\"%TRENGVAR_1%%TRENGVAR_2%/dashboard/spread\">Invite People</a> ] &middot; <a href=\"%TRENGVAR_1%%TRENGVAR_2%/dashboard/signout\">Sign Out</a>","dashboard",array($service_host,$group_name));

	$pg->setLayout($pg->FullColumn);
	$pg->addBlock($pg->Column, "", $welcome_html, "",false, true);
	$pg->addBlock($pg->Column, "", $html, "");


	break;










default:


	$posted = false;

	if(isset($_POST['groupname'])) {

		$g = new Group($_POST['groupname']);

		$res = $g->deleteMember($_POST['mid']);

		if($res) {

			$msg = "<span style=\"color:green;\">".$treng->_("You have successfully left the group","dashboard")."</span>";
		}
		else {
			$msg = "<span style=\"color:red;\">".$treng->_("Error! Please try again","dashboard")."</span>";
		}

		$posted = true;
	}


	$welcome_html = $treng->_("DASHBOARD &nbsp; - &nbsp; <a href=\"%TRENGVAR_1%?function=dashboard&obj1=password\">Change Credentials</a> &middot; <a href=\"%TRENGVAR_1%?function=dashboard&obj1=messages\">My Incoming Messages</a> &middot; <a href=\"%TRENGVAR_1%?function=dashboard&obj1=spread\">Invite People</a> &middot; <a href=\"%TRENGVAR_1%?function=dashboard&obj1=signout\">Sign Out</a>","dashboard",array($service_host));





			}


			if($pg->generateHTML()) {
				$page_content .= $pg->getHTML();
			}
			else {
				die('Could not generate HTML');
			}



			break;



		}

	case 'login':
	case 'signin':


		if(!isAuthenticated()) {

			$form = "";
			$signin_error_msg = false;
			$rememberme = false;

			if(isset($_POST['username'])&&isset($_POST['password'])&&isset($_POST['join'])) {
				if(@$_POST['rememberme']==1)
				$rememberme = true;
				$signin_res = login($_POST['username'], $_POST['password'], $rememberme);
				if($signin_res) {
					if($_POST['join']==1) {
						header("location:{$service_host}{$group_name}/join");
						exit;
					}
					else {
						if(isset($_POST['redirect_section'])&&!empty($_POST['redirect_section'])) {
							header("location:{$service_host}{$group_name}/{$_POST['redirect_section']}");
							exit;
						}
						$tpl->assign('linkto_login', false);
						$pg->setLayout($pg->FullColumn);
						$xhtml = "<div align=\"center\">".$treng->_("<big><strong>You have successfully signed in! <a href=\"%TRENGVAR_1%/\">Continue...</a></strong></big><br /><br />To edit the content, go to the related page (to change your blog address, go to Blogs section) and click one of the control buttons as shown:<br /><br /><img src=\"images/controlshow.jpg\" alt=\"Example Control\" border=\"0\" width=\"600\" height=\"389\" />","dashboard",array($service_host,$group_name))."</div>";

						$pg->addBlock($pg->Column, "",  $xhtml, "");



						if($pg->generateHTML()) {
							$page_content .= $pg->getHTML();
						}
						else {
							die('Could not generate HTML');
						}






						break;

					}
				}
				else {
					$signin_error_msg = true;
				}
			}


			$pg->setLayout($pg->FullColumn);


			$warn_redirect = "";
			if(isset($_GET['obj1'])&&$_GET['obj1']=="warn") {
				$form .= "<p>".$treng->_("YOU MUST BE SIGNED IN TO REACH THIS SECTION!","authentication")."</p><p>&nbsp;</p>";
				if(isset($_GET['obj2'])&&!empty($_GET['obj2'])) {
					$warn_redirect = $_GET['obj2'];
				}
			}
			elseif($signin_error_msg) {
				$temp_translation_string_1 = $treng->_("Ooops... There were some errors! Please try again.","authentication");
				$form .= <<<EOS
		                	
		                	<p><img src="{$service_host}images/warning_obj.gif" width="16" height="16" hspace="0" vspace="0" alt="" border="0" align="absbottom" />
		                	<strong>{$temp_translation_string_1}</strong></p>
		                	<p>&nbsp;</p>
EOS;
}

$form .= '<table cellspacing="0" cellpadding="0" border="0" width="100%"><tr><td width="70%" valign="top">';

$form .= '<form method="post" action="'.$service_host.'?function=signin">';


$trengvar = array($service_host);
$temp_translation_string_1 = $treng->_("Forgot your username and/or password? <a href=\"%TRENGVAR_1%?function=recover\">Recover</a>!","authentication",$trengvar);
$temp_translation_string_2 = $treng->_("About %TRENGVAR%","authentication",$group_title);
$temp_translation_string_3 = $treng->_("Don't have an account? <a href=\"%TRENGVAR_1%?function=signup\">Sign Up</a>!","authentication",$trengvar);
$temp_translation_string_4 = $treng->_("Email","groupjoin");
$temp_translation_string_5 = $treng->_("Password","groupjoin");
$temp_translation_string_6 = $treng->_("Keep me signed in unless I sign out - <i>uncheck if on a shared computer</i>","signup");
$temp_translation_string_7 = $treng->_("Sign In","groupjoin");

$form .= <<<EOS
		                <input type="hidden" name="redirect_section" value="{$warn_redirect}" />
		                <input type="hidden" name="join" value="0" />
		                
		                <div style="height:30px;width:100%;padding:5px;">
		                <div style="float:left;width:100px;font-weight:bold;">{$temp_translation_string_4}</div>
		                <div style="float:left;width:auto;"><input type="text" name="username"></div>
		                </div>
		                
		                
		                <div style="height:30px;width:100%;padding:5px;">
		                <div style="float:left;width:100px;font-weight:bold;">{$temp_translation_string_5}</div>
		                <div style="float:left;width:auto;"><input type="password" name="password"></div>
		                </div>
		                
		                
		                <div style="height:30px;width:100%;padding:5px;">
		                <input type="checkbox" name="rememberme" value="1" checked /> {$temp_translation_string_6}
		                </div>
		                
		                <div style="padding:5px;">
		                <input type="submit" value="{$temp_translation_string_7}" />
		                </div>
		                
		                
		                <div style="padding:5px; margin: 5px 0 0 0;">
		                {$temp_translation_string_3}
		                <br />
		                {$temp_translation_string_1}
		                </div>
		                
		                
		                </form>
		                
		                </td><td width="30%" valign="top" style="border-left:1px #000 dotted; padding-left:10px;">
		                <p><strong>{$temp_translation_string_2}</strong></p>
		                {$group_description}		                
		                </td></tr>
		                </table>
                
EOS;


$pg->addBlock($pg->Column, $treng->_('Sign In',""),  $form, "");




if($pg->generateHTML()) {
	$page_content .= $pg->getHTML();
}
else {
	die('Could not generate HTML');
}






break;

		}


	case 'wiki':

error_log("emre");
		$analytics->groupAccessed($function);
error_log("emel");

		// yes the user may select wiki page; but maybe
		// wiki is not open for this group,
		// we include break; also in this block
		// because we want to fall to default option
		if($allow_wiki) {

			if(!$open_wiki&&!$access_isGroupMember) {

				warnPrivatePage("wiki");
				break;

			}

			else {


				include_once('includes/Page.Wiki.class.php');


				$wikipage = new WikiPage($group_name);
				$pg->setLayout($pg->WithSidebar);


				if(!isset($_GET['obj1'])) {
					$first_id = $wikipage->getDefaultPageID();
				}
				else {
					// from GeneralFunctions.php
					$first_id = unicode_urldecode($_GET['obj1']);
				}

				$first_content = wikiGetDefaultContent($first_id);






				$pg->addBlock($pg->Sidebar, $treng->_('Wiki Index',"modulewiki"), $wikipage->getLinks($first_id), $treng->_("List of Wiki Pages; you can browse the wiki in here","modulewiki"));

				$pg->addBlock($pg->MainColumn, $wikipage->getTitle($first_id), $first_content, $treng->_("A Wiki Page; rich content created and edited by grou.p members","modulewiki"), false, false, false, true, "", $service_host.$group_name."/wiki/".$first_id);


				// START
				// We show Operations block to everyone
				// even we consider its probability of
				// being empty.
				// Because always, there may be
				// Revisions option
				//
				// if($access_isGroupMember) {
				// operations format
				$ops_area_html = $wikipage->getOperations($access_isGroupMember, $access_isGroupAdmin, $first_id);
				$is_editable = $group->canMembersEditWiki();
				if(!$is_editable&&!$access_isGroupAdmin) {
					$ops_area_html = $treng->_("This section can be edited only by administrators.","")
					.$wikipage->hideOperationsBlock();
				}
				elseif(empty($ops_area_html)) {
					$ops_area_html .= $wikipage->hideOperationsBlock();
				}
				$pg->addBlock($pg->Column, '', $ops_area_html, $treng->_("Operations Block; here you can edit/lock/unlock this page, create new pages, view page revisions","modulewiki"), false, true);
				//}
				// END

				// hidden
				// because all others are dynamic, we need a static script includer
				// that will never disappear with JS insertHTML calls.
				$pg->addBlock($pg->Column,'',$wikipage->getWikiEditArea(),"",true,true);
				//$pg->addBlock($pg->MainColumn,'',$wikipage->getWikiCreateArea(),true,true);

				$wiki_top_contributors = $wikipage->getTopContributorsAsHTML();
				if(!empty($wiki_top_contributors))
				$pg->addBlock($pg->Sidebar, $treng->_("Top Contributors",""), $wiki_top_contributors, "");

				$thehelp_text .= $wikipage->getHelpText();

				if($pg->generateHTML()) {
					$page_content .= $pg->getHTML();
				}
				else {
					die('Could not generate HTML');
				}
				$ajax_functions .= $wikipage->getAjaxFunctions();


				$to_register = $wikipage->getFunctionsToRegister();
				foreach($to_register as $r)
				sajax_export($r);

				/**
                 * and also we have extra head here
                 */
				$extra_head .= $wikipage->getExtraHead();

				break;
			}
		}









	case 'people':



		$analytics->groupAccessed($function);

		//// no allow_people, always existent
		if($allow_people) {

			if(!$open_people&&!$access_isGroupMember) {

				warnPrivatePage("people");
				break;

			}

			else {


				include('includes/Page.People.class.php');
				$peoplepage = new PeoplePage();
				$people_allow_tags = $modules_people_allowTags;
				$people_allow_favorites = $modules_people_allowFavorites;
				
				/**
                 * comes from Access.php
                 * but there is a very similar function named getMembershipID
                 * also, in GeneralFunctions.php
                 */



				if(!$access_isGroupMember) {
					$pg->addBlock($pg->Column, '', $treng->_("You could have your profile here if you were a member. <a href=\"%TRENGVAR_1%join\">Click</a> to join this group!","pagepeople",array($service_host)), "", false, true);
				}
				else {
					
					if(!isset($_GET['obj1'])||$_GET['obj1']=="search"||$_GET['obj1']=="page") {

					}
					elseif($_GET['obj1']==$_SESSION['valid_user']||($_GET['obj1']=="person"&&$_GET['obj2']==$_SESSION['valid_user'])) {
						$pg->addBlock($pg->Column, '', $peoplepage->getOperationsBlock(), '', false, true);
					}
					else {
						if(isset($_GET['obj1'])&&$_GET['obj1']=="person"&&isset($_GET['obj2']))
						$pg->addBlock($pg->Column, '', $peoplepage->getNotMeOperationsBlock($_GET['obj2']), '',false, true);
						else
						$pg->addBlock($pg->Column, '', $peoplepage->getNotMeOperationsBlock($_GET['obj1']), '',false, true);
					}




				}




				if(!isset($_GET['obj1'])||($_GET['obj1']=="page"||$_GET['obj1']=="search")) {

					$searched = "";
					$page_starts = 0;
					$page_ends = 20;
					$page = 1;
					if(isset($_GET['obj1'])&&$_GET['obj1']=="page"&&isset($_GET['obj2'])&&$_GET['obj2']>=2) {
						$page = $_GET['obj2'];
						$page_starts = 20*($_GET['obj2']-1);
					}

					$pg->setLayout($pg->FullColumn);

					if(isset($_GET['obj1'])&&$_GET['obj1']=="search"&&isset($_POST['psq'])) {
						$searched = $_POST['psq'];
						$members = $group->searchMembers($searched);
					}
					else {
						$members = $group->getMembers(false,false,$page_starts,$page_ends,"activity");
					}

					$html = "";

					$sm = sizeof($members);
					$maxsm = floor(($sm-1)/2);

					include_once("includes/User.class.php");
					foreach ($members as $i=>$m) {

						$member_name = _getMemberUsername($m['member_id']);
						$u = new User($member_name);
						$uuname = $u->getNameSurname();

						
						$a = $u->getAvatarAsHTML( 0, false);

						if(!isset($_SESSION['valid_user'])||$member_name!=$_SESSION['valid_user']) {
							if ($group->isAdmin($m['member_id'])) {
								$bg = "background:lightyellow;";
							}
							else {
								$bg = "";
							}
						}
						else {
							$bg = "background:#eee;";
						}

						$borderright = "";
						$borderbottom = "border-bottom:1px #ccc dotted;";

						$avatar = "<a href=\"{$service_host}people/person/{$member_name}\">{$a}</a>";

						$wallnum = $u->getWallPostsNum($m['member_id']);


						if($i%2==0) {
							$borderright = "border-right:1px #ccc dotted;";
						}

						if(floor($i/2)==$maxsm) {
							$borderbottom = "";
						}


						$html .= "
							<div style=\"width:48%;float:left;height:100px;{$borderbottom}{$borderright}{$bg}\"
							><div style=\"margin:10px;\"
							><table cellpadding=\"0\" border=\"0\" cellspacing=\"0\" width=\"100%\" style=\"background:transparent;\"
							><tr><td width=\"100\">{$avatar}</td><td valign=\"top\"
							><strong><a href=\"{$service_host}/people/person/{$member_name}\">{$uuname}</a></strong><br
							 /><small>has {$wallnum[0]} wall messages, {$wallnum[1]} new";

						$html .= "<br />posted ~ ";

					

						$html = substr($html,0,-2);


						
					}

					$pg->addBlock($pg->Column, '', $peoplepage->getSearchBox($searched), "", false, true);
					$pg->addBlock($pg->Column, '', $html, '');
					if(empty($searched))
					$pg->addBlock($pg->Column, '', $peoplepage->pages($page), '');


				}

				else {

					$people_allow_sexe = $group->moduleOptions_getPeopleAllowSexe();
					$people_allow_birthday = $group->moduleOptions_getPeopleAllowBirthday();
					$people_allow_zodiac = $group->moduleOptions_getPeopleAllowZodiac();
					$people_allow_website = $group->moduleOptions_getPeopleAllowWebsite();
					$people_allow_nation = $group->moduleOptions_getPeopleAllowNation();



					$person = "";

					if($_GET['obj1']=="person") {
						$person = $_GET['obj2'];
					}
					else {
						$person = $_GET['obj1'];
					}


					$zz = array();

					$pg->setLayout($pg->EqualColumns);

					// from GeneralFunctions.php
					$zz = $peoplepage->getDefaultMembershipID($person);



					$membershipid = $zz['id'];
					$member_username = $zz['username'];



					$pg->addBlock($pg->RightColumn, $treng->_('Wall',"modulepeople"), $peoplepage->getWall(_getMemberID($member_username)), "");



					$pg->addBlock($pg->LeftColumn, $treng->_('Profile',"modulepeople"), peopleGetProfileX($membershipid), $treng->_('An overview of this person',"modulepeople"));




					//if((isset($_SESSION['valid_user'])&&$member_username==$_SESSION['valid_user'])||$peoplepage->canShowTags($member_username))
					if($people_allow_tags)
					$pg->addBlock($pg->LeftColumn, $treng->_('My Tags',"modulepeople"), substr(peopleGetTags($membershipid,$member_username),2), $treng->_('These are the tags describing this person. Bigger a tags is, more it defines this person.',"modulepeople")); // will be used as Love Stuff
					//else
					//  $pg->addBlock($pg->MainColumn, $treng->_('My Tags',"modulepeople"), substr(peopleGetTags($membershipid,$member_username),2), $treng->_('These are the tags describing this person. Bigger a tags is, more it defines this person.',"modulepeople"), true);  // to store input stuff




					//if((isset($_SESSION['valid_user'])&&$member_username==$_SESSION['valid_user'])||$peoplepage->canShowFavourites($member_username))
					if($people_allow_favorites)
					$pg->addBlock($pg->LeftColumn, $treng->_('Favourites',"modulepeople"), $peoplepage->getFavourites($member_username), $treng->_('Favourites of this person. Shown by a book/CD cover for aesthetic purposes. Point your mouse over it to see more information in a popup box.',"modulepeople"),false); // will be used as Love Stuff
					//else
					//  $pg->addBlock($pg->MainColumn, $treng->_('Favourites',"modulepeople"), $peoplepage->getFavourites($member_username), $treng->_('Favourites of this person. Shown by a book/CD cover for aesthetic purposes. Point your mouse over it to see more information in a popup box.',"modulepeople"), true);  // to store input stuff





					//if((isset($_SESSION['valid_user'])&&$member_username==$_SESSION['valid_user'])||($peoplepage->canShowLoveStuff($member_username)||$peoplepage->canShowBusinessStuff($member_username)||$peoplepage->canShowHealthStuff($member_username)))

					// $pg->addBlock($pg->MainColumn, $treng->_('Life Overview',"modulepeople"), substr(peopleGetLoveStuff($membershipid,$member_username),2), $treng->_("An overview of this person's love life, business situation and health conditions.","modulepeople")); // will be used as Love Stuff
					//else
					//  $pg->addBlock($pg->MainColumn, $treng->_('Love, Business, Health',"modulepeople"), substr(peopleGetLoveStuff($membershipid,$member_username),2), $treng->_("An overview of this person's love life, business situation and health conditions.","modulepeople"), true);  // to store input stuff


					// hidden content goes here

					$pg->addBlock($pg->LeftColumn, '', '',"", true);
					$pg->addBlock($pg->LeftColumn, '', '',"", true);
					$pg->addBlock($pg->LeftColumn, '', '',"", true);
					$pg->addBlock($pg->LeftColumn, '', '',"", true);
					$pg->addBlock($pg->LeftColumn, '', $peoplepage->getSubContent(),"", true);


				}


				$thehelp_text .= $peoplepage->getHelpText();

				$extra_head .= $peoplepage->getExtraHead();

				if($pg->generateHTML()) {
					$page_content .= $pg->getHTML();
				}
				else {
					die('Could not generate HTML');
				}
				$ajax_functions .= $peoplepage->getAjaxFunctions();


				$to_register = $peoplepage->getFunctionsToRegister();
				foreach($to_register as $r)
				sajax_export($r);

				break;


			}


		}


	case 'talks':



		$analytics->groupAccessed($function);

		if($group->welcomeTalks()) {

			warnWelcomeSection();
			break;

		}
		else {
			if($allow_talks) {

				if(!$open_talks&&!$access_isGroupMember) {

					warnPrivatePage("talks");
					break;

				}

				else {

					include('includes/Page.TalksZZZZZ.class.php');
					$talkspage = new TalksPage($group_name);
					$talks_strict_maillist = $group->moduleOptions_getTalksStrictMaillist();

					$pg->setLayout($pg->FullColumn);


					$is_editable = $group->canMembersEditTalks();

					if(isset($_GET['obj1'])&&ereg("^[0-9]+$",$_GET['obj1'])) {
						$o1 = $_GET['obj1'];
						// temporary solution,
						// do it more properly
						$pg->addBlock($pg->Column, '', $talkspage->getThreadOptions($o1), "",false, true);

						// TODO: ATTENTION: WARNING
						// now you can pass a message id that does not belong to your group
						// we should take care of this
						$pg->addBlock($pg->Column, $talkspage->getMessageTitle($o1), $talkspage->getMessage($o1,true), "");

					}
					else {

						$pagenum = 0;
						$catid = "";
						if(isset($_GET['obj1'])) {
							if(ereg("^p[0-9]+$",$_GET['obj1'])) {
								$pagenum = substr($_GET['obj1'],1);
							}
							elseif(ereg("^c[0-9]+$",$_GET['obj1'])) {
								$catid = substr($_GET['obj1'],1);
							}
							elseif(ereg("^c([0-9]+)p([0-9]+)$",$_GET['obj1'],$tm)) {
								$pagenum = $tm[2];
								$catid = $tm[1];
							}
							elseif(ereg("^p([0-9]+)c([0-9]+)$",$_GET['obj1'],$tm)) {
								$pagenum = $tm[1];
								$catid = $tm[2];
							}
						}


						$pg->addBlock($pg->Column, '', $talkspage->getMemberOperations(), "",false, true);


						// Search Box
						// as an operation
						$pg->addBlock($pg->Column, '', $talkspage->getSearchOperation(), $treng->_("Search message archives","moduletalks"), true, true);


						// returns an array
						// which consists of 2 identical elements
						// the only difference is the ids
						$allmsgs = $talkspage->getAllMessages($pagenum,$catid);

						if($allmsgs!=null)
						$pg->addBlock($pg->Column, $treng->_('All Messages',"moduletalks"), $allmsgs[0], $treng->_('Newer Messages',"moduletalks"));
						else
						$pg->addBlock($pg->Column, $treng->_('All Messages',"moduletalks"), $_nodata_warning, '');

						// hidden for now
						// will be actual thread when clicked
						$pg->addBlock($pg->Column, "&nbsp;", "", $treng->_('Current Thread; sorted by descending date',"modulemap"), true,false,false,false,"","{$service_host}{$group_name}/talks/");


						// hidden for now
						// will be previous posts when clicked
						//
						// we put $allmsgs in here too
						// because other javascript method does not work
						// we have to play with its CSS Display properties
						if($allmsgs!=null)
						$pg->addBlock($pg->Column, $treng->_('Previous Posts',"modulemap"), $allmsgs[1], $treng->_('Previous Messages',"map"), true);
						else
						$pg->addBlock($pg->Column, $treng->_('Previous Posts',"modulemap"), '', $treng->_('Previous Messages',"map"), true);



						$topcontributors = $talkspage->getTopContributorsAsHTML();
						if(!empty($topcontributors)) {
							$pg->addBlock($pg->Column, $treng->_("Top Contributors",""), $topcontributors,"");
						}

						$pg->addBlock($pg->Column, "", $talkspage->pages($pagenum,$catid),"");


					}



					$thehelp_text .= $talkspage->getHelpText();

					$extra_head .= $talkspage->getExtraHead();


					if($pg->generateHTML()) {
						$page_content .= $pg->getHTML();
					}
					else {
						die('Could not generate HTML');
					}


					$ajax_functions .= $talkspage->getAjaxFunctions();

					$to_register = $talkspage->getFunctionsToRegister();

					foreach($to_register as $r)
					sajax_export($r);



					break;
				}
			}
		}


	case 'home':
	default:

			$function = 'home';

			$analytics->groupAccessed($group_name, $function);


			include_once("includes/Page.HomeZZZ.class.php");

			
			if(isset($_GET['obj1'])&&$_GET['obj1']=='new_modules'&&isset($_POST['newm'])&&is_array($_POST['newm'])) {
				foreach ($_POST['newm'] as $nm) {
					homeAddNewModule($nm);
				}
				header("location:{$service_host}{$group_name}/home");
				exit;
			}
			
			$defmod = "everything"; // dirty hack

			$isok = false;
			$isok_content = array();

			switch ($defmod) {

				case "everything":
					$isok = false;
					break;

				case "photos":



					include_once('includes/Page.PhotosZZZZZ.class.php');
					$photospage = new PhotosPage($group_name);
					$photos_allow_comments = $group->moduleOptions_getPhotosAllowComments();
					$photos_allow_rating = $group->moduleOptions_getPhotosAllowRating();
					$photos_allow_flickr = $group->moduleOptions_getPhotosAllowFlickr();

					if($allow_photos&&($access_isGroupMember||$open_photos)) {
						$isok = true;
						$isok_content[] = array("Photos",$photospage->getPhotosAsHTML(1,'date',null,false,5));
						$extra_head .= $photospage->getExtraHead();
						$ajax_functions .= $photospage->getAjaxFunctions();
						$to_register = $photospage->getFunctionsToRegister();
						foreach($to_register as $r)
						sajax_export($r);


					}

					break;

				case "links":


					include_once('includes/Page.LinksZZZZ.class.php');
					$linkspage = new LinksPage($group_name);
					$links_allow_rating = $group->moduleOptions_getLinksAllowRating();
					$links_allow_comments = $group->moduleOptions_getLinksAllowComments();
					$links_allow_digg = $group->moduleOptions_getLinksAllowDigg();
					$links_allow_reddit = $group->moduleOptions_getLinksAllowReddit();
					$links_allow_delicious = $group->moduleOptions_getLinksAllowDelicious();


					if($allow_photos&&($access_isGroupMember||$open_photos)) {

						$isok = true;
						$isok_content[] = array("Links",$linkspage->getLinks());



						$extra_head .= $linkspage->getExtraHead();


						$ajax_functions .= $linkspage->getAjaxFunctions();


						$to_register = $linkspage->getFunctionsToRegister();
						foreach($to_register as $r)
						sajax_export($r);

					}


					break;

				case "wiki":

					include_once('includes/Page.WikiZZZZZZZ.class.php');


					/**
                 * ATTENTION::::::::::
                 * Notice that we call this function first, instead of
                 * putting it inside addBlock function
                 * This is because wjhen there exists no page;ÃÂ this function
                 * produces first content...
                 * So if we don't call it first, then getLinks
                 * will return wrong value..
                 */
					$isok = true;
					$isok_content[] = array("Wiki",wikiGetDefaultContent());

					break;

				case "talks":




					include('includes/Page.TalksZZZZZ.class.php');
					$talkspage = new TalksPage($group_name);
					$talks_strict_maillist = $group->moduleOptions_getTalksStrictMaillist();
					$allmsgs = $talkspage->getAllMessagesCompact();

					if($allow_talks&&($access_isGroupMember||$open_talks)&&!empty($allmsgs)) {



						$isok = true;
						$isok_content[] = array("Latest Topics",$allmsgs);


					}







					break;

				case "people":



					$members = $group->getMembers();

					$html = "";

					foreach ($members as $m) {

						$member_name = _getMemberUsername($m['member_id']);
						$u = new User($member_name);

						if(isset($_SESSION['valid_user'])&&$member_name!=$_SESSION['valid_user'])
						$a = $u->getAvatarAsHTML($group_name, 15, false);
						else
						$a = $u->getAvatarAsHTML($group_name, 15, true);

						$html .= "<a href=\"{$service_host}{$group_name}/people/{$member_name}\">{$a}</a>";

					}

					$isok = true;
					$isok_content[] = array("Members",$html);


					break;



				case "blogs":
				default:
					include_once('includes/Page.BlogsZZZZZ.class.php');
					$blogspage = new BlogsPage($group_name);
					$blog_allow_external_entries = $group->moduleOptions_getBlogAllowExternalEntries();
					$blog_shorten_entries = $group->moduleOptions_getBlogShortenEntries();
					$entries = $blogspage->getBlogEntries(10);
					$isok = ($allow_blogs&&($access_isGroupMember||$open_blogs)&&sizeof($entries)>=3);
					if($isok) {

						// safe, continue...
						foreach($entries as $entry) {

							$title = "";
							$title .= "<img src=\"{$entry['icon']}\" alt=\"{$entry['author']}\" height=\"16\" width=\"16\" border=\"0\" align=\"absbottom\" /> ";
							$title .= "<a href=\"{$entry['link']}\">{$entry['title']}</a>";

							$content = $entry['content'];

							$isok_content[] = array($title,$content);

						}

					}
					break;
			}



			// check first
			// otherwise the HTML structure gets damaged
			if($isok) {

				$mapspage = new MapPage($group_name);
				$map_allow_souvenirs = $group->moduleOptions_getMapAllowSouvenirs();

				$pg->setLayout($pg->WithSidebar);	
				
				$home_opblock = getHomeOperationsBlock();

				if(!empty($home_opblock)) {
					$pg->addBlock($pg->Sidebar, '', '<div style="text-align:center;padding:10px;line-height:1.2em;">'.$home_opblock.'</div>','',false,true);
					$oblocks++;
				}

				$mapcontent = "";
				if($allow_map&&($access_isGroupMember||$open_map)&&$mapspage->hasEntry()) {
					$mapcontent = $mapspage->getSmallestGroupMap();
				}

				$photoscontent = "";
				if($allow_photos&&($access_isGroupMember||$open_photos)) {
					include_once("includes/Page.PhotosZZZZZ.class.php");
					$photospage = new PhotosPage($group_name);
					$photos_allow_comments = $group->moduleOptions_getPhotosAllowComments();
					$photos_allow_rating = $group->moduleOptions_getPhotosAllowRating();
					$photos_allow_flickr = $group->moduleOptions_getPhotosAllowFlickr();

					$photoscontent = $photospage->getPhotosAsHTML_Home(3);
					if($photoscontent==null)
					$photoscontent = "";
				}

				$pg->addBlock($pg->Sidebar, '', getMiniAboutUsBlock($mapcontent,$photoscontent), $treng->_('About Us',"map"));

				// safe, continue...
				foreach($isok_content as $isokc) {
					// user dependent
					$pg->addBlock($pg->MainColumn, $isokc[0], $isokc[1], $treng->_("This is a blog entry","pageblogs"), false, false, false, true);
				}




			}





			else {

				include_once('includes/Page.MapZZZZ.class.php');
				include_once('includes/Page.TalksZZZZZ.class.php');
				include_once("includes/Page.PhotosZZZZZ.class.php");
				include_once("includes/Page.LinksZZZZ.class.php");
				include_once("includes/Page.WikiZZZZZZZ.class.php");
				include_once("includes/Page.CalendarXX.class.php");

				$mapspage = new MapPage($group_name);
				$map_allow_souvenirs = $group->moduleOptions_getMapAllowSouvenirs();

				$talkspage = new TalksPage($group_name);
				$talks_strict_maillist = $group->moduleOptions_getTalksStrictMaillist();

				$photospage = new PhotosPage($group_name);

				$linkspage = new LinksPage($group_name);
				$links_allow_rating = $group->moduleOptions_getLinksAllowRating();
				$links_allow_comments = $group->moduleOptions_getLinksAllowComments();
				$links_allow_digg = $group->moduleOptions_getLinksAllowDigg();
				$links_allow_reddit = $group->moduleOptions_getLinksAllowReddit();
				$links_allow_delicious = $group->moduleOptions_getLinksAllowDelicious();

				$wikipage = new WikiPage($group_name);


				$pg->setLayout($pg->EqualColumns);
				
								$taborder = null;
								$can_members_change_tab_order_home = $group->canMembersChangeTabOrderHome();
				if($can_members_change_tab_order_home) {
					$taborder = getMyHomeTabOrder();
				}
				else {
					$taborder = getGroupHomeTabOrder();
				}
				
				
				$oblocks = 0;
				
							$groupdesc = getAboutUsBlock();
							$pg->addBlock($pg->LeftColumn, "", $groupdesc, "");
					
							
							$home_opblock = getHomeOperationsBlock();
							if(!empty($home_opblock)) {
								$pg->addBlock($pg->RightColumn, '', '<div style="text-align:center;padding:10px;line-height:1.2em;">'.$home_opblock.'</div>','',false,true);
								$oblocks++;
							}
				
							$taborder_size = sizeof($taborder)-1;
						
							$n = 0;	
							
								
							
				foreach ($taborder as $i=>$ct) {
					
					
					$op="";
					if(isset($_SESSION['valid_user'])&&($access_isGroupAdmin||($access_isGroupMember&&$can_members_change_tab_order_home))) {
					if($n==0) {
						$op = "<a href=\"javascript:void(take_block_right('{$ct}'))\" style=\"text-decoration:none;\">&rarr;</a>";
					}
					elseif($n==$taborder_size) {
						$op = "<a href=\"javascript:void(take_block_left('{$ct}'))\" style=\"text-decoration:none;\">&larr;</a>";
					}
					else {
						$op = "<a href=\"javascript:void(take_block_left('{$ct}'))\" style=\"text-decoration:none;\">&larr;</a> <a href=\"javascript:void(take_block_right('{$ct}'))\" style=\"text-decoration:none;\">&rarr;</a>";
					}
					$op .= " <a href=\"javascript:void(close_block('{$ct}'))\" style=\"text-decoration:none;\">x</a>";
					}
					
					$n++;
					
					switch($ct) {
						/*
						case "tab_description":
							$groupdesc = getAboutUsBlock();
							$pg->addBlock($pg->LeftColumn, "", $groupdesc, "");
							break;
							
						case "tab_operations":
							$home_opblock = getHomeOperationsBlock();
							if(!empty($home_opblock)) {
								$pg->addBlock($pg->RightColumn, '', '<div style="text-align:center;padding:10px;line-height:1.2em;">'.$home_opblock.'</div>','',false,true);
								$oblocks++;
							}
							break;
							*/
							
						case "tab_admins":
							if($access_isGroupMember||$can_anyone_join||$open_people) {
								$adminsb = getAdminsBlock();
								if($oblocks%2==1)
								$pg->addBlock($pg->RightColumn, $treng->_('Admins',"modulehome"), $adminsb, "",false,false,false,false,$op);
								else
								$pg->addBlock($pg->LeftColumn, $treng->_('Admins',"modulehome"), $adminsb, "",false,false,false,false,$op);
								$oblocks++;
							}
							break;
							
						case "tab_map":
							if($allow_map&&($access_isGroupMember||$open_map)&&$mapspage->hasEntry()) {
			
								if($oblocks%2==1)
								$pg->addBlock($pg->RightColumn, $treng->_('Map',"modulemap"), $mapspage->getMiniGroupMap(), $treng->_('Map',"modulemap"),false,false,false,false,$op);
								else
								$pg->addBlock($pg->LeftColumn, $treng->_('Map',"modulemap"), $mapspage->getMiniGroupMap(), $treng->_('Map',"modulemap"),false,false,false,false,$op);
			
								$oblocks++;
							}
							break;
							
						case "tab_online_members":
							if($allow_people&&($access_isGroupMember||$open_people)) {
			
			
								$onlinemembers = getOnlineMembersBlock();
								if(!empty($onlinemembers)) {
									if($oblocks%2==1)
										$pg->addBlock($pg->RightColumn, $treng->_('Online Members',"modulehome"), $onlinemembers, $treng->_('Online Members',"modulehome"),false,false,false,false,$op);
									else
										$pg->addBlock($pg->LeftColumn, $treng->_('Online Members',"modulehome"), $onlinemembers, $treng->_('Online Members',"modulehome"),false,false,false,false,$op);
									$oblocks++;
			
								}
							}
							break;
							 
						case "tab_top_contributors":
							if($allow_people&&($access_isGroupMember||$open_people)) {
			
			
								
			
								$global_topcontributors = getTopContributorsBlock();
								if(!empty($global_topcontributors)) {
									if($oblocks%2==1)
									$pg->addBlock($pg->RightColumn, $treng->_('Top 5 Contributors',"modulehome"), $global_topcontributors, $treng->_('Top 5 Contributors',"modulehome"),false,false,false,false,$op);
									else
									$pg->addBlock($pg->LeftColumn, $treng->_('Top 5 Contributors',"modulehome"), $global_topcontributors, $treng->_('Top 5 Contributors',"modulehome"),false,false,false,false,$op);
									
									// we don't them to be aligned vertically
								$oblocks++;
			
								}
			
								
			
								
			
							}
							break;
							
						case "tab_latest_members":
							if($allow_people&&($access_isGroupMember||$open_people)) {
								$latest_members = getLatestMembersBlock();
								if(!empty($latest_members)) {
									if($oblocks%2==1)
										$pg->addBlock($pg->RightColumn, $treng->_('Latest 5 Members',"modulehome"), $latest_members, $treng->_('Latest 5 Members',"modulehome"),false,false,false,false,$op);
									else
										$pg->addBlock($pg->LeftColumn, $treng->_('Latest 5 Members',"modulehome"), $latest_members, $treng->_('Latest 5 Members',"modulehome"),false,false,false,false,$op);
									// we don't them to be aligned vertically
								$oblocks++;
			
								}
			
								
			
							}
							break;
							
						case "tab_talks":
											if($allow_talks&&($access_isGroupMember||$open_talks)) {

					if($oblocks%2==1)
					$pg->addBlock($pg->RightColumn, $treng->_('Talks',"moduletalks"), $talkspage->getAllMessages_Home(), $treng->_('Talks',"moduletalks"),false,false,false,false,$op);
					else
					$pg->addBlock($pg->LeftColumn, $treng->_('Talks',"moduletalks"), $talkspage->getAllMessages_Home(), $treng->_('Talks',"moduletalks"),false,false,false,false,$op);

					$oblocks++;

				}
							break;
							
						case "tab_blogs":
											include_once("includes/Page.BlogsZZZZZ.class.php");
				$blogspage = new BlogsPage($group_name);
				$blog_allow_external_entries = $group->moduleOptions_getBlogAllowExternalEntries();
				$blog_shorten_entries = $group->moduleOptions_getBlogShortenEntries();
				$blogentries = $blogspage->getBlogEntries_Home();

				if($allow_blogs&&($access_isGroupMember||$open_blogs)&&!empty($blogentries)) {

					if($oblocks%2==1)
					$pg->addBlock($pg->RightColumn, $treng->_('Blogs',"moduleblogs"), $blogentries, $treng->_('Blogs',"moduleblogs"),false,false,false,false,$op);
					else
					$pg->addBlock($pg->LeftColumn, $treng->_('Blogs',"moduleblogs"), $blogentries, $treng->_('Blogs',"moduleblogs"),false,false,false,false,$op);

					$oblocks++;

				}
							break;
							
						case "tab_photos":
											$photolist = $photospage->getPhotosAsHTML_Home();

				if($allow_photos&&($access_isGroupMember||$open_blogs)&&$photolist!=null) {

					if($oblocks%2==1)
					$pg->addBlock($pg->RightColumn, $treng->_('Photos',"modulephotos"), $photolist, $treng->_('Blogs',"modulephotos"),false,false,false,false,$op);
					else
					$pg->addBlock($pg->LeftColumn, $treng->_('Photos',"modulephotos"), $photolist, $treng->_('Blogs',"modulephotos"),false,false,false,false,$op);

					$oblocks++;

				}
							break;
							
						case "tab_links":
							$linkslist = $linkspage->getLinks_Home();

				if($allow_links&&($access_isGroupMember||$open_links)&&$linkslist!=null) {

					if($oblocks%2==1)
					$pg->addBlock($pg->RightColumn, $treng->_('Links',"modulelinks"), $linkslist, $treng->_('Links',"modulelinks"),false,false,false,false,$op);
					else
					$pg->addBlock($pg->LeftColumn, $treng->_('Links',"modulelinks"), $linkslist, $treng->_('Links',"modulelinks"),false,false,false,false,$op);

					$oblocks++;

				}
							break;
							
						case "tab_wiki":
							$wikilist = $wikipage->getLinks_Home();

				if($allow_wiki&&($access_isGroupMember||$open_wiki)&&!empty($wikilist)) {

					if($oblocks%2==1)
					$pg->addBlock($pg->RightColumn, $treng->_('Wiki',"modulewiki"), $wikilist, $treng->_('Wiki',"modulewiki"),false,false,false,false,$op);
					else
					$pg->addBlock($pg->LeftColumn, $treng->_('Wiki',"modulewiki"), $wikilist, $treng->_('Wiki',"modulewiki"),false,false,false,false,$op);

					$oblocks++;

				}
							break;
							
						case "tab_calendar":
							$cal = new CalendarPage($group_name);
				if($allow_calendar) {

					if($oblocks%2==1)
					$pg->addBlock($pg->RightColumn, $treng->_('Calendar',"modulewcalendar"), $cal->getHomeBlock(), "",false,false,false,false,$op);
					else
					$pg->addBlock($pg->LeftColumn, $treng->_('Calendar',"modulecalendar"), $cal->getHomeBlock(), "",false,false,false,false,$op);

					$oblocks++;

				}
							break;
					}
					
				}
				

				

				

				

























				



				

				


				// We only have About Us block
				// make it wider
				if($oblocks==1) {

					$pg->removeLastBlock();

					$pg->setLayout($pg->FullColumn);

					$pg->addBlock($pg->Column, $treng->_('About Us',"modulemap"), $groupdesc, $treng->_('About Us',"map"));

				}
				
				
				// operational box
				if($access_isGroupAdmin||($access_isGroupMember&&$can_members_change_tab_order_home)) {
						if($oblocks%2==1)
							$pg->addBlock($pg->LeftColumn, "", homeAddNewModuleBlock(), "", false,true);
						else
							$pg->addBlock($pg->RightColumn, "", homeAddNewModuleBlock(), "",false,true);
					}
			}

			$ajax_functions .= home_getAjaxFunctions();
				$to_register = home_getFunctionsToRegister();
				foreach($to_register as $r)
				sajax_export($r);




			if($pg->generateHTML()) {
				$page_content .= $pg->getHTML();
			}
			else {
				die('Could not generate HTML');
			}


			break;


		


}


$tpl->assign('module', $function);



$tpl->assign('page_content',$page_content);
$tpl->assign('ajax_functions',$ajax_functions);

$tpl->assign('thehelp_text',$thehelp_text);


ob_start();
sajax_handle_client_request();
$ajax_handle_requests = ob_get_contents();
ob_end_clean();
$tpl->assign('ajax_handle_requests',$ajax_handle_requests);

$ajax_javascript = sajax_get_javascript();
$tpl->assign('ajax_javascript',$ajax_javascript);
$ajax_functions = '';
// $sajax_debug_mode = 1;

/**
 * extra headers
 */
$tpl->assign('extra_head_content',$extra_head);


// get topbar

include_once("includes/topbar.php");
$tpl->assign('topbar',getGroupTopBar($group_title, $function));




/**
 * SSL Check
 */
if((array_key_exists("HTTPS", $_SERVER) && $_SERVER["HTTPS"] == "on"))
$tpl->assign('ssl_check', true);
else
$tpl->assign('ssl_check', false);





/**
 * Just before displaying Smarty template
 * we register a Smarty function
 * to translate blocks of texts
 */
$tpl->register_block('translate', 'do_smarty_translation');

function do_smarty_translation ($params, $content, &$tpl, &$repeat) {

	global $treng;

	$content = trim($content);

	$res = $treng->_($content,"grouppage");

	return $res;

}



// old-
// hacks

if($group_template[0]=='m'&&is_ie_less_than_7()) {
	switch($group_template) {
		case 'minimalistic-red':
		case 'minimalistic-rww':
			$group_template = 'igloored';
			break;
		case 'minimalistic-blue-dark':
		case 'minimalistic-blue-light':
		case 'minimalistic-green-dark':
		case 'minimalistic-green-light':
			$group_template = 'iglooblue';
			break;
		case 'minimalistic-gray-dark':
		case 'minimalistic-gray-light':
		case 'minimalistic-black':
			$group_template = 'iglooblack';
			break;
		case 'minimalistic-pink':
			$group_template = 'default';
			break;
		case 'minimalistic-orange':
			$group_template = 'iglooorange';
			break;
	}
}


	$tpl->security = false;

	$tpl->template_dir = 'templates/tpl/'.$group_template;
	$tpl->assign('template_name',$group_template);

	$tpl->display('index__'.$group_template.'.tpl');


?>