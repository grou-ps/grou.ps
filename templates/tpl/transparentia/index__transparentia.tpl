{$ajax_handle_requests}
<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head profile="http://gmpg.org/xfn/11">
<title>{$group_title}</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta name="keywords" content="{$group_keywords}">
<meta name="Description" content="{$group_description}">
<link rel="SHORTCUT ICON" href="images/favicon.ico">
<link rel="stylesheet" type="text/css" href="templates/tpl/{$template_name}/transparentia.css" />
<link rel="stylesheet" type="text/css" href="includes/topbar.abs.css" />
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
<div id="bg_differentiation"></div>
<div id="theCrop"></div>
<div id="ajax_popup"><a href="javascript:void(close_ajax_popup())" class="closeButton"></a><br />
  <table width="100%" height="100%" cellspacing="0" cellpadding="0" border="0" style="background:transparent;">
    <tr>
      <td align="center" valign="middle" style="background:transparent;"><div id="ajax_popup_content"></div></td>
    </tr>
  </table>
</div>
<div id="busy_popup"></div>

{$topbar}


<div id="layout">
  <div id="header">
<div id="headerRight"></div>
    <div id="h1"> <a href="{$service_host}/">{$group_title}</a></div>
    <div id="h1_sub"><a href="{$service_host}">{$group_desc}</a></div>
    {if not $no_menu}
    <div id="menu"><div id="menuRight"></div>
      <div id="menuLeft"></div><ul id="nav">
        {foreach item=tab from=$tabs}
        {if $tab.module neq ''}
        {if $module eq $tab.module}
        {if $tab.rename == ""}
        <li class="selected"><a href="?function={$tab.module}">{translate}{$tab.title}{/translate}</a></li>
        {else}
        <li class="selected"><a href="?function={$tab.module}">{$tab.rename}</a></li>
        {/if}
          {else}
          {if $tab.rename == ""}
        <li><a href="?function={$tab.module}">{translate}{$tab.title}{/translate}</a></li>

        {else}
        <li><a href="?function={$tab.module}">{$tab.rename}</a></li>
        {/if}
          {/if}

          {else}
        <li><a href="{$tab.link}">{translate}{$tab.title}{/translate}</a></li>
        {/if}
          {/foreach}
      </ul>
    <div style="clear: both;"></div></div><div style="clear: both;"></div>
    {/if} </div><div style="clear: both;"></div>
  <div id="content"><div style="clear: both;"></div>
    <div id="contentCont"> {$page_content} </div>
  </div>
<div id="footer"> &copy; {$smarty.now|date_format:"%Y"} {$group_title}
    &bull; powered by <a href="http://grou.ps/">GROU.PS <!-- it's illegal to remove this notice --></a>
  </div>
  </div>
</div>
</body>
</html>