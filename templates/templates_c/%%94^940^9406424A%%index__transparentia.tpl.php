<?php /* Smarty version 2.6.19, created on 2008-06-26 20:29:40
         compiled from index__transparentia.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('block', 'translate', 'index__transparentia.tpl', 57, false),array('modifier', 'date_format', 'index__transparentia.tpl', 80, false),)), $this); ?>
<?php echo $this->_tpl_vars['ajax_handle_requests']; ?>

<?php echo '<?xml'; ?>
 version="1.0" encoding="UTF-8"<?php echo '?>'; ?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head profile="http://gmpg.org/xfn/11">
<title><?php echo $this->_tpl_vars['group_title']; ?>
</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta name="keywords" content="<?php echo $this->_tpl_vars['group_keywords']; ?>
">
<meta name="Description" content="<?php echo $this->_tpl_vars['group_description']; ?>
">
<link rel="SHORTCUT ICON" href="images/favicon.ico">
<link rel="stylesheet" type="text/css" href="templates/tpl/<?php echo $this->_tpl_vars['template_name']; ?>
/transparentia.css" />
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
<?php echo $this->_tpl_vars['ajax_javascript']; ?>

<?php echo $this->_tpl_vars['ajax_functions']; ?>

</script>
<?php echo $this->_tpl_vars['extra_head_content']; ?>

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

<?php echo $this->_tpl_vars['topbar']; ?>



<div id="layout">
  <div id="header">
<div id="headerRight"></div>
    <div id="h1"> <a href="<?php echo $this->_tpl_vars['service_host']; ?>
/"><?php echo $this->_tpl_vars['group_title']; ?>
</a></div>
    <div id="h1_sub"><a href="<?php echo $this->_tpl_vars['service_host']; ?>
"><?php echo $this->_tpl_vars['group_desc']; ?>
</a></div>
    <?php if (! $this->_tpl_vars['no_menu']): ?>
    <div id="menu"><div id="menuRight"></div>
      <div id="menuLeft"></div><ul id="nav">
        <?php $_from = $this->_tpl_vars['tabs']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['tab']):
?>
        <?php if ($this->_tpl_vars['tab']['module'] != ''): ?>
        <?php if ($this->_tpl_vars['module'] == $this->_tpl_vars['tab']['module']): ?>
        <?php if ($this->_tpl_vars['tab']['rename'] == ""): ?>
        <li class="selected"><a href="?function=<?php echo $this->_tpl_vars['tab']['module']; ?>
"><?php $this->_tag_stack[] = array('translate', array()); $_block_repeat=true;do_smarty_translation($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start(); ?><?php echo $this->_tpl_vars['tab']['title']; ?>
<?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_repeat=false;echo do_smarty_translation($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat); }  array_pop($this->_tag_stack); ?></a></li>
        <?php else: ?>
        <li class="selected"><a href="?function=<?php echo $this->_tpl_vars['tab']['module']; ?>
"><?php echo $this->_tpl_vars['tab']['rename']; ?>
</a></li>
        <?php endif; ?>
          <?php else: ?>
          <?php if ($this->_tpl_vars['tab']['rename'] == ""): ?>
        <li><a href="?function=<?php echo $this->_tpl_vars['tab']['module']; ?>
"><?php $this->_tag_stack[] = array('translate', array()); $_block_repeat=true;do_smarty_translation($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start(); ?><?php echo $this->_tpl_vars['tab']['title']; ?>
<?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_repeat=false;echo do_smarty_translation($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat); }  array_pop($this->_tag_stack); ?></a></li>

        <?php else: ?>
        <li><a href="?function=<?php echo $this->_tpl_vars['tab']['module']; ?>
"><?php echo $this->_tpl_vars['tab']['rename']; ?>
</a></li>
        <?php endif; ?>
          <?php endif; ?>

          <?php else: ?>
        <li><a href="<?php echo $this->_tpl_vars['tab']['link']; ?>
"><?php $this->_tag_stack[] = array('translate', array()); $_block_repeat=true;do_smarty_translation($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start(); ?><?php echo $this->_tpl_vars['tab']['title']; ?>
<?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_repeat=false;echo do_smarty_translation($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat); }  array_pop($this->_tag_stack); ?></a></li>
        <?php endif; ?>
          <?php endforeach; endif; unset($_from); ?>
      </ul>
    <div style="clear: both;"></div></div><div style="clear: both;"></div>
    <?php endif; ?> </div><div style="clear: both;"></div>
  <div id="content"><div style="clear: both;"></div>
    <div id="contentCont"> <?php echo $this->_tpl_vars['page_content']; ?>
 </div>
  </div>
<div id="footer"> &copy; <?php echo ((is_array($_tmp=time())) ? $this->_run_mod_handler('date_format', true, $_tmp, "%Y") : smarty_modifier_date_format($_tmp, "%Y")); ?>
 <?php echo $this->_tpl_vars['group_title']; ?>

    &bull; powered by <a href="http://grou.ps/">GROU.PS <!-- it's illegal to remove this notice --></a>
  </div>
  </div>
</div>
</body>
</html>