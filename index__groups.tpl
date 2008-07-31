{$ajax_handle_requests}
<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head profile="http://gmpg.org/xfn/11">{if $ssl_check eq true}
<base href="https://grou.ps/" />
{else}
<base href="http://grou.ps/" />
{/if}
<title>{$group_title}</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta name="keywords" content="{$group_keywords}">
<meta name="Description" content="{$group_description}">
<link rel="SHORTCUT ICON" href="images/favicon.ico">
<link rel="stylesheet" type="text/css" href="templates/tpl/groups/groups.css" />
<link rel="stylesheet" type="text/css" href="templates/tpl/popup.css" />
<script type="text/javascript" src="includes/xm.js"></script>
<script type="text/javascript" src="includes/generals.js"></script>
<script type="text/javascript" src="includes/groupmode.js"></script>
<script type="text/javascript" src="includes/blendtrans.js"></script>
<script type="text/javascript" src="includes/ajaxpopup.js"></script>
<script type="text/javascript" src="includes/title2note.js"></script>
<script type="text/javascript" src="includes/jsval/jsval.js"></script>
<script type="text/javascript" src="includes/scriptaculous/lib/prototype.js"></script>
<script type="text/javascript" src="includes/scriptaculous/src/scriptaculous.js?load=effects,dragdrop"></script>
<script language="javascript">
{$ajax_javascript}
{$ajax_functions}
</script>
{$extra_head_content}
</head>
<body>
{include file="../modals.tpl"}

{if $linkto_chat eq true}
{include file="../chat.tpl"}
{/if}

{if $firsttime eq "yes"}
{include file="../welcome.tpl"}
{/if}


	{if $google_ad_code <> ""}
<div style="position:absolute;left:250px;top:35px;width:728px;height:15px;padding:0;z-index:3;">
  <script type="text/javascript">
		<!--
		google_ad_client = "pub-{$google_ad_code}";
		google_ad_slot = "{$google_ad_code_2}";
		google_ad_width = 728;
		google_ad_height = 15;
		//-->
		</script>
  <script type="text/javascript"
		src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
	</script>
</div>
{/if}
<div id="layout">
  <div id="header">
      <div id="infoBar">
        <div id="myAccount">{$user_status}</div>
        <div id="myTwitter"><div>{$twitter}</div></div>
      </div>
    <div id="h1"> <a href="http://grou.ps/{$group_name}/">{$group_title}</a></div>
    <div id="h1_sub"><a href="http://grou.ps/{$group_name}/home">{$group_desc}</a></div>
    {if not $no_menu}
    <div class="nav">
      <ul>
        <li><a id="nav_left" href="javascript:void(0)" style="padding:0;width:20px;background: transparent url(images/nav_left_off.png) no-repeat top left;"> </a></li>
        <li><a href="introduction.php">Home</a></li>
        <li><a href="take_tour.php">Take the Tour</a></li>
        <li><a href="create_group_wizard.php">Start your own group</a></li>
      </ul>
      <div class="nav nav_right"> </div>
    </div>
    <div id="menu">
      <ul id="nav">
        {foreach item=tab from=$tabs}
        {if $tab.module neq ''}
        {if $module eq $tab.module}
        {if $tab.rename == ""}
        <li class="selected"><a href="{$group_name}/{$tab.module}" thehelp="{translate}{$tab.help}{/translate}">{translate}{$tab.title}{/translate}</a></li>
        {else}
        <li class="selected"><a href="{$group_name}/{$tab.module}" thehelp="{translate}{$tab.help}{/translate}">{$tab.rename}</a></li>
        {/if}
        {else}
        {if $tab.rename == ""}
        <li><a href="{$group_name}/{$tab.module}" thehelp="{translate}{$tab.help}{/translate}">{translate}{$tab.title}{/translate}</a></li>
        {else}
        <li><a href="{$group_name}/{$tab.module}" thehelp="{translate}{$tab.help}{/translate}">{$tab.rename}</a></li>
        {/if}
        {/if}
        
        {else}
        <li><a href="{$tab.link}" thehelp="{translate}{$tab.help}{/translate}">{translate}{$tab.title}{/translate}</a></li>
        {/if}
        {/foreach}
      </ul>
      <div style="clear: both;"></div>
    </div>
    <div style="clear: both;"></div>
    {/if} </div>
  <div style="clear: both;"></div>
  <div id="content">
    <div style="clear: both;"></div>
    <div id="contentCont"> {$page_content}
      <div style="clear: both;"></div>
    </div>
    <div style="clear: both;"></div>
  </div>
  <div id="footer"> © 2006-2008 
    - <a href="http://www.emresokullu.com/">Emre Sokullu</a> - <a href="http://grou.ps/groudotps/wiki/12">About Us</a> - <a href="http://grou.ps/groudotps/wiki/12">Open Source</a> - <a href="http://grou.ps/groudotps/">Our Group</a> - <a href="http://grou.ps/groudotps/talks">Support</a> - <a href="mailto:jobs@grou.ps">Jobs</a><br />
    <!--Social Cartography Inc, 211 Sutter, Suite 801, San Francisco, CA 94108<br />-->
    We proudly support <a href="http://www.openid.org">OpenID</a> and <a href="http://www.microformats.org/">microformats</a> -
    
    {if $module eq 'home'} <a href="http://grou.ps/rss/{$group_name}/home">{translate}OPML Feeds{/translate}</a> {/if}
    
    {if $module eq 'talks'} <a href="http://grou.ps/rss/{$group_name}/talks">{translate}RSS Feeds{/translate}</a> {/if}
    
    {if $module eq 'people'} <a href="http://grou.ps/rss/{$group_name}/people">{translate}RSS Feeds{/translate}</a> {/if}
    
    
    {if $module eq 'blogs'} <a href="http://grou.ps/rss/{$group_name}/blogs">{translate}RSS Feeds{/translate}</a> {/if}
    
    {if $module eq 'photos'} <a href="http://grou.ps/rss/{$group_name}/photos">{translate}RSS Feeds{/translate}</a> {/if}
    
    {if $module eq 'links'} <a href="http://grou.ps/rss/{$group_name}/links">{translate}RSS Feeds{/translate}</a> {/if}
    
    {if $module eq 'wiki'} <a href="http://grou.ps/rss/{$group_name}/wiki">{translate}RSS Feeds{/translate}</a> {/if}
    
    • <a href="http://grou.ps/{$group_name}/etiquette">Etiquette</a> • <a href="javascript:void(reportIssue())">Report an Issue</a> </div>
</div>
</div>
{include file="../final_scripts.tpl"}
</body>
</html>
