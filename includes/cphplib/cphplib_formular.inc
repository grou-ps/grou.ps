<?php
/**
 * Formular extention for cphplib
 *
 * PHP versions 4 and 5
 *
 * @category	Utilities
 * @package		cphplib
 * @author		Alexander Meindl <am@meindlsoft.com>
 * @copyright	(c) 2005 meindlSOFT
 * @license		Released under v2 of the GNU LGPL
 * @version		CVS: $Id: cphplib_formular.inc,v 1.10 2005/10/21 06:27:54 alex Exp $
 * @link		http://www.meindlsoft.com/tools.php
 */

/**
 * cphplib formular extention
 */
class cphplib_formular extends cphplib
{
	/**
	 * Show error messages with class
	 *
	 * @var	bool	true:	show error fields with class
	 *				false:	show non-error fields with class
	 */
	var $err_mode = false;

	/**
	 * Formular error messages
	 *
	 * @var array
	 */
	var $form_errors;

	/**
	 * Current client operating system
	 *
	 * @var	char
	 */
	var $os_type;

	/**
	 * Current browser type
	 *
	 * @var	char
	 */
	var $browser_type;

	/**
	 * Set name parameter of formular object
	 *
	 * @var	bool
	 */
	var $set_formular_name = true;

	/**
	 * Set id parameter of formular object
	 *
	 * @var	bool
	 */
	var $set_formular_id = false;

	/**
	 * Current formular name
	 *
	 * @var	string
	 */
	var $formular_name;

	/**
	 * CSS class for buttons in formular
	 *
	 * @var	string
	 */
	var $class_button	= "button";

	/**
	 * CSS class for buttons in formular (for error displaying)
	 *
	 * @var	string
	 */
	var $class_shadow		= "shadow";

	/**
	 * CSS class for valid input fileds (non-error displaying)
	 *
	 * @var	string
	 */
	var $class_non_error	= "";

	/**
	 * CSS class for error messages (HEADER)
	 *
	 * @var	string
	 */
	var $class_error_header	= "err_header";

	/**
	 * CSS class for error messages (BODY)
	 *
	 * @var	string
	 */
	var $class_error_body	= "err";

	/**
	 * Default date view mode:	M = month
	 *							Q = quarter
	 *							Y = year
	 *
	 * @var	string
	 */
	var $default_date_period	= "M";

	/**
	 * First year in date select box
	 *
	 * @var	int
	 */
	var $first_date_year		= 1970;

	/**
	 * Color for error displaying (only used for opera!)
	 *
	 * @deprecated
	 *
	 * @var	string
	 */
	var $error_color	= "grey";	//	$STYLE['form2_bordercolor'];

	function cphplib_formular($locale="de_DE")
	{
		parent::cphplib($locale);

		$this->os_type      = $this->os_type();
		$this->browser_type	= $this->browser_type();
	}

	/**
	 * begining for formular
	 * (this function can only handle the session_id, if dbsession_start has
	 * been called immediately after creating the class object)
	 *
	 * @param	string	$action		action					{default: PHP_SELF}
	 * @param	string	$name		name for formular		{default: void}
	 * @param	string	$method		post or get				{default: post}
	 * @param	bool	$mode		false: no session_id, true: with session_id if required {default: true}
	 * @param	string	$customize	for addon parameters like enctype
	 * @return string				formular head
	 */
	function fm_start($action="", $name="form1", $method="post", $mode=true, $customize="")
	{
		$with_hidden = false;

		if (($mode) && ($this->m_dbsession_handler))
		{
			if (!$this->m_dbsession_cookies)
				$with_hidden = true;
		}

		if (isvoid($action))	$action = $_SERVER['PHP_SELF'];
		if (isvoid($method))	$method = "post";

		$rc  = "<form action=\"".$action."\"";
		if (!empty($name))
		{
			// save name for other objects
			$this->formular_name = $name;
			if ($this->set_formular_name)	$rc .= " name=\"".$name."\"";
			if ($this->set_formular_id)		$rc .= " id=\"".$name."\"";
		}
		$rc .= " method=\"".$method."\"";
		if (!empty($customize))	$rc .= " $customize";
		$rc .= ">\n";

		if ($with_hidden)
		{
			$rc .= $this->fm_hidden($this->m_dbsession_id_name, $this->m_dbsession_id);
		}

		return $rc;
	}

	/**
	 * close tag for HTML form
	 *
	 * @return	string
	 */
	function fm_end()
	{
		$rc = "</form>\n";
		return $rc;
	}

	/**
	 * Add formular error message to stack ($this->form_errors)
	 *
	 * @param	string	$message	error message
	 * @param	string	$field		name of formular object	(use , if you want more than one fields)
	 */
	function add_form_error($message, $field=null)
	{
		if (!empty($message))
		{
			if ((!isset($field)) || (empty($field)))
			{
				$field = "global_errors";
			}

			$this->form_errors[$field][] = $message;
		}
	}

	/**
	 * Remove formular error message from error stack ($this->form_errors)
	 *
	 * @param	string 	name of formular object	(use , if you want more than one fields)
	 *					if no field name is defined, all error messages will be deleted
	 */
	function remove_form_error($field=null)
	{
		if (isset($field))
		{
			$fields = array();

			if (substr_count($field, ","))
			{
				$fields = explode(",", $field);
			}
			else
			{
				$fields[] = $field;
			}

			while(list(, $key)=each($fields))
			{
				if (array_key_exists($key, $this->form_errors))
				{
					unset($this->form_errors[$key]);
				}
			}

			if (count($this->form_errors)==0)
			{
				unset($this->form_errors);
			}
		}
		else
		{
			unset($this->form_errors);
		}
	}

	/**
	 * Exists a formular error
	 *
	 * @param	string	only check this field (use , if you want more than one fields)
	 *					if empty all fields
	 * @return	bool 	true, if error exists
	 */
	function is_form_error($field=null)
	{
		$rc = false;

		if ((is_array($this->form_errors)) && (count($this->form_errors)>0))
		{
			if (isset($field))
			{
				$fields = array();

				if (substr_count($field, ","))
				{
					$fields = explode(",", $field);
				}
				else
				{
					$fields[] = $field;
				}

				while(list(, $key)=each($fields))
				{
					if (array_key_exists($key, $this->form_errors))
					{
						$rc = true;
						break;
					}
				}
			}
			else
			{
				$rc = true;
			}
		}

		return $rc;
	}

	/**
	 * Get formular error messages
	 *
	 * @return	bool 	true, if error exists
	 */
	function get_form_errors()
	{
		$rc = array();

		reset($this->form_errors);
		while(list(, $enote)=each($this->form_errors))
		{
			if (is_array($enote))
			{
				while(list(, $d_enote)=each($enote))
				{
					$rc[] = $d_enote;
				}
			}
			else
			{
				$rc[] = $enote;
			}
		}

		return $rc;
	}

	/**
	 * show all formular errors
	 *
	 * @return	string 		html list of errors
	 */
	function show_form_errors()
	{
		$output = "";

		if ($this->is_form_error())
		{
			$err_count = 0;
			reset($this->form_errors);

			$message_block = "";

			while(list(, $messages)=each($this->form_errors))
			{
				while(list(, $message)=each($messages))
				{
					if (!empty($message))
					{
						$err_count++;
						$message_block .= "<li  class=\"".$this->class_error_body."\">$message</li>\n";
					}
				}
			}

			$output .= "\n<div class=\"".$this->class_error_header."\">";
			$output .= "<img src=\"".$this->image_url."/form_error.gif\" style=\"border: 0;\" alt=\"\"".$this->end_tag()."&nbsp;";
			$output .= "<span style=\"font-weight: bold;\">".STR_FORM_ERROR_HEADER."&nbsp;(".$err_count."):</span>";
			$output .= "<br".$this->end_tag()."<ul>\n";

			if ($err_count>0)
			{
				$output .= $message_block;
			}
			else
			{
				$output .="<li class=\"".$this->class_error_body."\">error message is missing. Please call for support.</li>\n";
			}

			$output .= "</ul></div>\n";
		}
		else
		{
			$this->show_error("show_form_errors", "error message is missing");
		}

		return $output;
	}

	/**
	 * Get error CSS class
	 *
	 * If err_mode = false and "global_errors" are defined, no shadow class is used! (inactive)
	 *
	 * @param	string	$name
	 * @param	bool	$with_opera_color
	 * @return	string
	 */
	function get_error_class($name, $with_opera_color=false)
	{
		$rc = "";

		if ($this->is_form_error())
		{
			// temp array
			$form_errors = array();

			reset($this->form_errors);
			while(list($keys, $messages)=each($this->form_errors))
			{
				if (substr_count($keys, ","))
				{

					$sub_keys = explode(",", $keys);
					while(list(, $key)=each($sub_keys))
					{
						$form_errors[$key] = $messages;
					}
				}
				else
				{
					$form_errors[$keys] = $messages;
				}
			}

			if ($this->err_mode)
			{
				if ((array_key_exists($name, $form_errors)) &&
					(is_array($form_errors[$name])))
				{
					if ($with_opera_color)
					{
						$rc .= " style=\"color: ".$this->error_color.";\"";
					}
					else
					{
						$rc .= " class=\"".$this->class_shadow."\"";
					}
				}
			}
			else if (!array_key_exists("global_errors", $form_errors))
			{
				if ((!array_key_exists($name, $form_errors)) ||
					(array_key_exists($name, $form_errors)) && (!is_array($form_errors[$name])))
				{
					if ($with_opera_color)
					{
						$rc .= " style=\"color: ".$this->error_color.";\"";
					}
					else
					{
						$rc .= " class=\"".$this->class_shadow."\"";
					}
				}
			}
		}

		if ((empty($rc)) && (!empty($this->class_non_error)))	$rc = " class=\"".$this->class_non_error."\"";

		return $rc;
	}

	/**
	 * calculates select start for formular
	 *
	 * @param	string	$name		name of select tag
	 * @param	array	$fmdata		array with options
	 *									 value:
	 *									 desc:
	 *									 default: if true, this entry will be selected {default: false}
	 *									 convert: if true, convert to html {default: true}
	 *									 customize: other values like styles or ids
	 * @param	string	$active		if actuve is value a valid, this entry is selected {default: first in data array}
	 * @param	string	$options	other tag parameters
	 * @param	string	$onchange	onchange event
	 * @return	string
	 */
	function fm_select($name, $fmdata, $active=null, $options="", $onchange="")
	{
		if (!is_array($fmdata))
		{
			$this->show_error("fm_select", "missing fmdata array", $fmdata);
		}

		// check if valid value will be found
		if (!empty($active))
		{
			$active_found=false;
			reset($fmdata);
			while(list(, $specs)=each($fmdata))
			{
				if ($active==$specs['value'])
				{
					$active_found=true;
					break;
				}
			}
			// if not, set active to default value
			if (!$active_found)
			{
				reset($fmdata);
				while(list(, $specs)=each($fmdata))
				{
					if ($specs['default']==true)
					{
						$active = $specs['value'];
						break;
					}
				}
			}
		}
		///////////////////////////////////////////////////////

		$rc  =  "<select name=\"$name\"";
		$rc .= $this->get_error_class($name);

		if (!empty($options))	$rc .= " $options";
		if (!empty($onchange))	$rc .= " onchange=\"$onchange;\"";
		$rc .= ">\n";

		reset($fmdata);
		$select_set = false;
		while(list(, $specs)=each($fmdata))
		{
			$rc .=  "\t<option value=\"".$specs['value']."\"";
			if (($active==$specs['value']) && (!$select_set))
			{
				$select_set = true;
				$rc .= " selected=\"selected\"";
			}
			else if ((isset($specs['default'])) &&
					($specs['default']==true) &&
					(!$select_set) &&
					(isvoid($active, true)))
			{
				$select_set = true;
				$rc .= " selected=\"selected\"";
			}
			if (isset($specs['customize']))	$rc .= " ".$specs['customize'];
			$rc .= ">";

			if (isset($specs['convert']))
			{
				if ($specs['convert'])	$rc .= $this->convHtml($specs['desc']);
				else					$rc .= $specs['desc'];
			}
			else						$rc .= $this->convHtml($specs['desc']);
			$rc .= "</option>\n";
		}
		$rc .=	"</select>\n";

		return $rc;
	}

	/**
	 * calculates submit button string
	 *
	 * @param	string	$name			name of the button
	 * @param	string	$value			value of the button
	 * @param	string	$customize		other formular parameters, e.g. CLASS
	 * @return	string					submit button string for formular
	 */
	function fm_submit($name,$value,$customize="")
	{
		if (strlen($value)<10)
			$value = "&nbsp;&nbsp;".$value."&nbsp;&nbsp;";

		$rc = "<input type=\"submit\" class=\"".$this->class_button."\" name=\"$name\" value=\"$value\"";
		if (!empty($customize))
			$rc .= " $customize";
		$rc .= $this->end_tag();
		return $rc;
	}

	/**
	 * Calculates button string
	 *
	 * @param	string	$name			name of the button
	 * @param	string	$value			value of the button
	 * @param	string	$onclick		onclick event (e.g. javascript:...)
	 * @param	string	$customize		other tag parameter options like CLASS
	 * @return	string
	 */
	function fm_button($name, $value, $onclick, $customize="")
	{
		if (strlen($value)<10)
			$value = "&nbsp;&nbsp;".$value."&nbsp;&nbsp;";

		$rc = "<input type=\"button\" class=\"".$this->class_button."\" name=\"$name\" value=\"$value\" onclick=\"$onclick;\"";
		if (!empty($customize))
		{
			$rc .= " $customize";
		}

		$rc .= $this->end_tag();

		return $rc;
	}

	/**
	 * calculates image button
	 *
	 * @param	string	$name			name of the button
	 * @param	string	$image_name		image name
	 * @param	string	$alt_name		alt text for image
	 * @param	string	$onclick		onclick event (e.g. javascript:...)
	 * @param	string	$customize		other tag parameter options like CLASS
	 * @return	string					button string for formular
	 */
	function fm_image($name, $image_name, $alt_name, $onclick="", $customize="")
	{
		$rc = "<input type=\"image\" class=\"".$this->class_button."\"";
		if (!isvoid($name))	$rc .= " name=\"$name\"";
		$rc .= " src=\"$image_name\" alt=\"$alt_name\" style=\"border: none; background-color: transparent;\"";

		if (!empty($onclick))   $rc .= " onclick=\"$onclick;\"";
		if (!empty($customize))	$rc .= " $customize";
		$rc .= $this->end_tag();

		return $rc;
	}

	/**
	 * calculates file input field
	 *
	 * @param	string	$name
	 * @param	int		$size
	 * @param	string	$onchange
	 * @param	string	$customize
	 * @return	string
	 */
	function fm_file($name, $size, $onchange="",$customize="")
	{
		$rc = "<input type=\"file\" name=\"$name\" size=\"$size\"";

		$rc .= $this->get_error_class($name);

		if (!empty($onchange))		$rc .= " onchange=\"$onchange;\"";
		if (!empty($customize))		$rc .= " $customize";
		$rc .= $this->end_tag();

		return $rc;
	}

	/**
	 * calculates text field for formular
	 *
	 * @param	string	$name				name of text tag
	 * @param	string	$value				content of text tag
	 * @param	string	$cols				number of colomns
	 * @param	string	$rows				number of rows
	 * @param	string	$options			other formular parameters like CLASS
	 * @param	int		$text_mode			0: no edit options
	 *										1: no edit options, with mce
	 *										2: edit options, text active
	 *										3: edit options, mce small active
	 *										4: edit options, mce large active
	 * @param	bool	$with_hidden_field
	 * @return	string						text field string for formular
	 */
	function fm_text($name, $value="", $cols="", $rows="", $options="", $text_mode=0, $with_hidden_field=true)
	{
		if      ($cols=="smallest")                    $cols_value = 20;
		else if ($cols=="small")                       $cols_value = 30;
		else if ($cols=="medium")                      $cols_value = 38;
		else if ((empty($cols)) || ($cols=="default")) $cols_value = 45;
		else if ($cols=="big")                         $cols_value = 50;
		else if ($cols=="biggest")                     $cols_value = 65;
		else if ($cols=="s10")                         $cols_value = 76;
		else                                           $cols_value = $cols;

		if      ($rows=="smallest")                    $rows_value = 3;
		else if ($rows=="small")                       $rows_value = 4;
		else if ($rows=="medium")                      $rows_value = 5;
		else if ((empty($rows)) || ($rows=="default")) $rows_value = 6;
		else if ($rows=="big")                         $rows_value = 8;
		else if ($rows=="biggest")                     $rows_value = 12;
		else if ($rows=="s10")                         $rows_value = 15;
		else                                           $rows_value = $rows;

		if ($this->browser_type=="i")
			$cols_value += 3;
		else if ($this->os_type=="w")
			$cols_value -= 1;
		else if ($this->browser_type=="o")
			$cols_value += 2;

		$output = "";

		if ($text_mode>1)
		{
			$output .= "<table cellpadding=\"0\" style=\"width: 100%\">
						<tr><td align=\"right\" style=\"background-color: #f0f0ee;\">";

			if ($with_hidden_field)
			{
				$output .= $this->fm_hidden("run_text_mode", 0, true);
			}

			$formdef = "document.mask";
			$start_url	= "javascript:".$formdef.".text_mode.value=";
			$mode_url	= $formdef.".run_text_mode.value=1;".$formdef.".submit()";

			if ($text_mode==2)	$output .= "<img src=\"".IMAGE_URL."/button_text_active.gif\" alt=\"".STR_TEXT_VIEW."\" title=\"".STR_TEXT_VIEW."\" style=\"border: 0;\"".$this->end_tag();
			else 				$output .= $this->url($start_url."2;".$mode_url, "<img src=\"".IMAGE_URL."/button_text.gif\" alt=\"".STR_TEXT_VIEW."\" title=\"".STR_TEXT_VIEW."\" style=\"border: 0;\"".$this->end_tag(), 1, STR_TEXT_VIEW);
			$output .= "&nbsp;";
			if ($text_mode==3)	$output .= "<img src=\"".IMAGE_URL."/button_mce_active.gif\" alt=\"".STR_MCE_VIEW."\" title=\"".STR_MCE_VIEW."\" style=\"border: 0;\"".$this->end_tag();
			else 				$output .= $this->url($start_url."3;".$mode_url, "<img src=\"".IMAGE_URL."/button_mce.gif\" alt=\"".STR_MCE_VIEW."\" title=\"".STR_MCE_VIEW."\" style=\"border: 0;\"".$this->end_tag(), 1, STR_MCE_VIEW);
			$output .= "&nbsp;";
			if ($text_mode==4)	$output .= "<img src=\"".IMAGE_URL."/button_mce_large_active.gif\" alt=\"".STR_MCE_VIEW_LARGE."\" title=\"".STR_MCE_VIEW_LARGE."\" style=\"border: 0;\"".$this->end_tag();
			else 				$output .= $this->url($start_url."4;".$mode_url, "<img src=\"".IMAGE_URL."/button_mce_large.gif\" alt=\"".STR_MCE_VIEW_LARGE."\" title=\"".STR_MCE_VIEW_LARGE."\" style=\"border: 0;\"".$this->end_tag(), 1, STR_MCE_VIEW_LARGE);

			$output .= "</td></tr><tr><td>";
		}

		$output .=  "<textarea name=\"$name\"";
		$output .= " cols=\"$cols_value\"";

		if ($text_mode==4)	$output .=" rows=\"32\"";
		else 				$output .=" rows=\"$rows_value\"";

		$output .= $this->get_error_class($name);

		if (!empty($options))	$output .= " $options";
		if (($text_mode!=0) && ($text_mode!=2))		$output .= " mce_editable=\"true\"";
		$output .= ">";
		if (!isvoid($value))
		{
			if ($text_mode>1)	$output .= nl2br($value);
			else				$output .= $this->convHtml($value);
		}
		$output .= "</textarea>";

		if ($text_mode>1)
		{
			$output .= "</td></tr></table>";
		}

		return $output;
	}

	/**
	 * calculates Date select string
	 *
	 * @param	array	$customize	array with the following keys:
	 *					                 	name_year		= select box name for year
	 *				                        name_month		= select box name for month
	 *				                        name_day		= select box name for day
	 *				                        name_hour		= select box name for hour
	 *				                        name_minute		= select box name for minute
	 *				                        value_year		= selected year                {default: current year}
	 *				                        value_month		= selected month               {default: current month}
	 *				                        value_day		= selectd day                  {default: current day}
	 *				                        value_hour		= selected hour                {default: current hour}
	 *			                        	value_minute	= selected_minute              {default: current minute}
	 *			                         	with_time		= it true, with time and title {default: false}
	 *			                        	with_alltime	= it true, with alltime event  {default: false}
	 *			                         	name_alltime	= name of alltime checkbox
	 *			                        	value_alltime	= if "Y", it is selected       {default: "N"}
	 *			                        	minute_int		= minute interval 1, 5, 10 or 15			   {default: 5 }
	 *			                        	with_run		= true, if with run button     {default: false}
	 *			                        	prefix			= if defined, it will be print
	 *		                             		               in front of the first select box
	 *			                         	start_year		= set this, if the selection shall not start from year 1970
	 *		                        		end_year		= set this, if the selection shall not end after year 2037
	 * @param	string	$options	other tag parameters like readonly or class
	 * @return	string				date select string for formular
	 */
	function fm_date($customize, $options="")
	{
		if (empty($customize['start_year']))
			$customize['start_year'] = 1901;
		if ((empty($customize['end_year'])) || (intval($customize['end_year'])>2037))
			$customize['end_year'] = 2037;

		if (empty($customize['name_year']))   $this->show_error("fm_date", "name_year is missing");
		if (empty($customize['name_month']))  $this->show_error("fm_date", "name_month is missing");
		if (empty($customize['name_day']))    $this->show_error("fm_date", "name_day is missing");
		if ($customize['with_time']!=true)    $customize['with_time']   = false;
		if ($customize['with_run']!=true)     $customize['with_run']    = false;

		if ((isvoid($customize['value_year'])) || ($customize['value_year']<$customize['start_year']) || ($customize['value_year']>$customize['end_year']))
			$customize['value_year']  = date('Y');

		if ((empty($customize['value_month'])) || ($customize['value_month']>12))
			$customize['value_month'] = date('m');

		if ((empty($customize['value_day'])) || ($customize['value_day']>31))
			$customize['value_day'] = date('d');

		if ($customize['with_time'])
		{
			if (empty($customize['name_hour']))
			{
				$this->show_error("fm_date", "name_hour is missing");
			}
			else if (empty($customize['name_minute']))
			{
				$this->show_error("fm_date", "name_minute is missing");
			}

			if ((isvoid($customize['value_hour'])) || ($customize['value_hour']>23))
				$customize['value_hour']   = date('H');
			if ((isvoid($customize['value_minute'])) || ($customize['value_minute']>59))
				$customize['value_minute'] = date('i');

			if (($customize['with_alltime']) && (empty($customize['name_alltime'])))
			{
				$this->show_error("fm_date", "name_alltime is missing");
			}
		}

		$rc  = "<table cellpadding=\"0\" cellspacing=\"0\" style=\"border: 0;\"><tr>";
		if (!isvoid($customize['prefix'])) $rc .= "<td>".$customize['prefix'].":&nbsp;</td>";
		$rc .= "<td>\n";

		if ($customize['with_time'])	$rc .= STR_DATE.":<br".$this->end_tag();

		// day
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$fmdata = array();
		for ($i=1;$i<32;$i++)
		{
			if (strlen($i)<2)	$show_i = "0".$i;
			else				$show_i = $i;
			$fmdata[] = array('value'=>$i, 'desc'=>$show_i."&nbsp;", 'convert'=>false);
		}
		$rc_day = $this->fm_select($customize['name_day'], $fmdata, $customize['value_day'], $options);

		// month
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////
		unset($fmdata);
		$fmdata = array();
		for ($i=1;$i<13;$i++)
		{
			$fmdata[] = array('value'=>$i, 'desc'=>Date_Calc::getMonthFullname($i));
		}
		$rc_month = $this->fm_select($customize['name_month'], $fmdata, $customize['value_month'], $options);

		// year
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////
		unset($fmdata);
		$fmdata = array();
		for ($i=$customize['start_year'];$i<=$customize['end_year'];$i++)
		{
			$fmdata[] = array('value'=>$i, 'desc'=>$i."&nbsp;", 'convert'=>false);
		}
		$rc_year = $this->fm_select($customize['name_year'], $fmdata, $customize['value_year'], $options);

		if ($this->date_format=="I")		$rc .= $rc_month.$rc_day.$rc_year;
		else if ($this->date_format=="S")	$rc .= $rc_year.$rc_month.$rc_day;
		else								$rc .= $rc_day.$rc_month.$rc_year;

		if ($customize['with_time'])
		{
			$rc .= "</td><td>";
			$rc .= "&nbsp;&nbsp;".STR_TIME.":<br".$this->end_tag()."&nbsp;&nbsp;";

			// minute
			//////////////////////////////////////////////////////////////////////////////////////////////////////////////
			$fmdata_m = array();

			if ((!isvoid($customize['minute_int'])) && (!isvoid(array_search($customize['minute_int'], array(1, 5, 10, 15)))))
				$minute_int = $customize['minute_int'];
			else
				$minute_int = 5; // default

			for ($i=0; $i<60; $i=$i+$minute_int)
			{
				if (($customize['value_minute']!=$i) && ($minute_int>1) &&
						(($i<$customize['value_minute']) && (($customize['value_minute']+$minute_int*2)>59))  ||
						(($i>$customize['value_minute']) &&
						($i<($customize['value_minute']+$minute_int)) &&
						($customize['value_minute']+$minute_int)<=59))
					$default = true;
				else
					$default = false;

				if (strlen($i)<2)	$show_i = "0".$i;
				else				$show_i = $i;

				$fmdata_m[] = array('value'=>$i, 'desc'=>$show_i."&nbsp;", 'convert'=>false, 'default'=>$default);
			}

			// hour
			//////////////////////////////////////////////////////////////////////////////////////////////////////////////
			$fmdata_h = array();
			for ($i=0;$i<24;$i++)
			{
				if (($this->time_format=="Y") || ($i>0))
				{
					if ($this->time_format=="Y")
					{
						if (strlen($i)<2)	$curr_time = "0".$i;
						else				$curr_time = $i;
					}
					else
					{
						if ($i>12)	$curr_time = ($i-12)." pm";
						else		$curr_time = $i." am";
					}
					$fmdata_h[] = array('value'=>$i, 'desc'=>$curr_time."&nbsp;", 'convert'=>false);
				}
			}
			if ($this->time_format!="Y")
				$fmdata_h[] = array('value'=>0, 'desc'=>"12 pm&nbsp;", 'convert'=>false);

			if ((($customize['value_minute']+$minute_int)>59) && ($customize['value_hour']<=23))
			{
				$customize['value_hour']++;
			}

			// show time
			$rc .= $this->fm_select($customize['name_hour'], $fmdata_h, $customize['value_hour'], "style=\"text-align: right;\" ".$options);

			$rc .= $this->fm_select($customize['name_minute'], $fmdata_m, $customize['value_minute'], $options);

			if ($customize['with_alltime'])
			{
				$rc .= "&nbsp;&nbsp;&nbsp;&nbsp;";
				$rc .= $this->fm_checkbox($customize['name_alltime'], "Y", STR_APPOINTMENT_ALLDAY, $customize['value_alltime'], $options);
			}
		}

		$rc .= "</td>\n";
		if ($customize['with_run'])
			$rc .= "<td>&nbsp;".$this->fm_image("", $this->image_url."/goto.gif", STR_CALENDAR_JUMPTO, "", $customize="", $options)."</td>\n";
		$rc .= "</tr></table>\n";

		return $rc;
	}

	/**
	 * Hidden field
	 *
	 * @param	string	$field_name
	 * @param	string	$field_value
	 * @param	bool	$mode			false: if empty, don't print it
	 * @return	string
	 */
	function fm_hidden($field_name, $field_value="", $mode=false)
	{
		$output = "";

		if ((!isvoid($field_value)) || ((isvoid($field_value)) && ($mode)))
		{
		 	$output .= "\t<input type=\"hidden\" name=\"$field_name\" value=\"";
		 	if (is_int($field_value)) $output .= $field_value;
		 	else 					  $output .= $this->convHtml($field_value);
		 	$output .= "\"".$this->end_tag()."\n";
		}

		return $output;
	}

	/**
	 * checkbox for formular
	 *
	 * @param	string	$name
	 * @param	string	$value
	 * @param	string	$desc			description behind checkbox
	 * @param	string	$active_value	active checkbox
	 * @param	string	$options		other optional tag parameters
	 * @return	string
	 */
	function fm_checkbox($name, $value, $desc, $active_value="", $options="")
	{
		$rc = "<input type=\"checkbox\" name=\"$name\"";

		$rc .= " value=\"";
		if (is_int($value))	$rc .= $value;
		else 				$rc .= $this->convHtml($value);
		$rc .= "\"";

		if ($active_value==$value)	$rc .= " checked=\"checked\"";

		if ($this->browser_type=="i") // fix ie style fault
			$rc .= " style=\"background-color: transparent; border-style: none;\"";

		if (!empty($options))		$rc .= " $options";

		if (!isvoid($desc))
		{
			$id_name = str_replace("[", "-", $name);
			$id_name = str_replace("]", "-", $id_name);

			$rc .= " id=\"".$id_name."\"".$this->end_tag();
			$rc .= "<label for=\"".$id_name."\">".$desc."</label>";
		}
		else
		{
			$rc .= $this->end_tag();
		}

		return $rc;
	}

	/**
	 * checkbox for formular
	 *
	 * @param	string		$name
	 * @param	string		$value
	 * @param	string		$desc			description behind radio button
	 * @param	string		$active_value	active radio
	 * @param	string		$alt_values		alternate active values for other radio buttons. If no other radio button is correct,
	 *										that one with optional alt_values will be checked
	 * @param	string		$options		other optional tag parameters
	 * @return	string						string with checkbox for formular
	 */
	function fm_radio($name, $value, $desc, $active_value="", $alt_values="", $options="")
	{
		$rc = "\t<input type=\"radio\" name=\"$name\"";
		if ($this->browser_type=="i") // fix ie style fault
			$rc .= "style=\"background-color: transparent; border-style: none;\"";
		else if ($this->browser_type=="o") // only works with opera
		{
			$rc .= $this->get_error_class($name, true);
		}

		$rc .= " value=\"";
		if (is_int($value))	$rc .= $value;
		else 				$rc .= $this->convHtml($value);
		$rc .= "\"";

		if ($active_value==$value)	$rc .= " checked=\"checked\"";
		else if (!isvoid($alt_values))
		{
			$found=false;
			// if alt_value exists and active_value is empty, mark it as checked
			if (!isvoid($active_value))
			{
				$ed = explode(",", $alt_values);
				while(list(, $val)=each($ed))
				{
					if ($val==$active_value)
					{
						$found=true;
						break;
					}
				}
			}
			if (!$found)	$rc .= " checked=\"checked\"";
		}
		if (!empty($options))	$rc .= " $options";

		if (!isvoid($desc))
		{
			$id_name = $name."-".$value;
			$id_name = str_replace("[", "-", $id_name);
			$id_name = str_replace("]", "-", $id_name);

			$rc .= " id=\"".$id_name."\"".$this->end_tag();
			$rc .= "<label for=\"".$id_name."\">".$desc."</label>";
		}
		else
		{
			$rc .= $this->end_tag();
		}
		$rc .= "\n";

		return $rc;
	}

	/**
	 * calculats input tag for formular
	 *
	 * @param	string	$name
	 * @param	string	$value
	 * @param	mixed	$size			smallest,small,medium (2xmedium=normal), default or big
	 * @param	int		$maxlength		maximum input length
	 * @param	string	$options		other optional tag parameters
	 * @param	array	$customize
	 * @return	string					string with input tag for formular
	 */
	function fm_input($name, $value="", $size="", $maxlength="", $options="", $customize=null)
	{
		$is_password_field			= false;
		$password_generate_button	= false;
		$password_show_button		= false;
		$url_button					= false;
		$email_button				= false;
		$date_button				= false;
		$button_space				= false;

		if (isset($customize))
		{
			if (array_key_exists("is_password_field", $customize))			$is_password_field			= $customize['is_password_field'];
			if (array_key_exists("password_generate_button", $customize))	$password_generate_button	= $customize['password_generate_button'];
			if (array_key_exists("password_show_button", $customize))		$password_show_button		= $customize['password_show_button'];
			if (array_key_exists("url_button", $customize))					$url_button 				= $customize['url_button'];
			if (array_key_exists("email_button", $customize))				$email_button				= $customize['email_button'];
			if (array_key_exists("date_button", $customize))				$date_button				= $customize['date_button'];
			if (array_key_exists("button_space", $customize))				$button_space				= $customize['button_space'];
		}
		/////////////////////////////

		$ptype=0;

		if      ($size=="s1")                     $size_value = 8;
		else if ($size=="s2")                     $size_value = 11;
		else if ($size=="s3")                     $size_value = 15; // 15
		else if ($size=="s4")                     $size_value = 20; // 20
		else if ($size=="s5")                     $size_value = 25; // 27
		else if ((empty($size)) || ($size=="s6")) $size_value = 30; // 32
		else if ($size=="s7")                     $size_value = 36; // 36
		else if ($size=="s8")                     $size_value = 41;
		else if ($size=="s9")                     $size_value = 48;
		else if ($size=="s10")                    $size_value = 58;
		else                                      $size_value = $size;

		if ($this->browser_type=="i")
			$size_value -= 2;
		else if (($this->os_type=="w") && ($size_value<25) && ($size_value>11))
			$size_value -= 3;
		else if (($this->browser_type=="o") && ($size_value<25) && ($size_value>11))
			$size_value += 1;

		// new_passwd = same as new_pwd
		if ((substr($name,0,7)=="new_pwd") || (substr($name,0,10)=="new_passwd"))
			$ptype=1; // because of an Opera bug, I turned password fields to 0 (default:1)
		else if ($name=="upwd")
			$ptype=2;

		$rc = "<input type=\"";
		if ($ptype==1)	$rc .= "password";
		else			$rc .= "text";

		$rc .= "\" name=\"$name\"";

		if (!isvoid($value))	$rc .= " value=\"".$this->convHtml($value)."\"";

		$rc .= " size=\"$size_value\"";
		if (!empty($maxlength))	$rc .= " maxlength=\"$maxlength\"";

		$rc .= $this->get_error_class($name);

		if (!empty($options))	$rc .= " $options";

		$rc .= $this->end_tag();

		if ((($name=="new_email") || (substr($name, 0, 5)=="email")) && (!isvoid($value)) && (!$this->is_form_error($name)))
		{
			$rc .= "&nbsp;<a style=\"background: none;\" href=\"mailto:$value\"><img src=\"".$this->image_url."/mail.gif\" alt=\"".STR_SHOW_EMAIL."\" title=\"".STR_SHOW_EMAIL."\" style=\"border: 0;\"".$this->end_tag()."</a>";
		}
		else if ((((substr($name,0,7))=="new_url") || ((substr($name,0,3))=="url") || ($url_button)) && (!isvoid($value)) && (!$this->is_form_error($name)))
		{
			$rc .= "&nbsp;".$this->url($this->convHtml($this->convURL($value)),
										 "<img src=\"".$this->image_url."/url.gif\" alt=\"".STR_SHOW_URL."\" title=\"".STR_SHOW_URL."\" style=\"border: 0;\"".$this->end_tag(),
										 1,
										 "",
										 "target=\"_blank\" style=\"background: none;\"");
		}
		if (($name=="new_pic") && (!isvoid($value)) && (!$this->is_form_error($name)))
		{
			$rc .= "&nbsp;".$this->url($this->convHtml($this->convUrl($value)),
										 "<img src=\"".$this->image_url."/url_pic.gif\" alt=\"".STR_SHOW_PIC."\" title=\"".STR_SHOW_PIC."\" style=\"border: 0;\"".$this->end_tag(),
										 1,
										 "",
										 "style=\"background: none;\"");
		}
		else if (($name=="new_spec") && (!isvoid($value)) && (!$this->is_form_error($name)))
		{
			$rc .= "&nbsp;".$this->url($this->convHtml($this->convUrl($value)),
										 "<img src=\"".$this->image_url."/url_spec.gif\" alt=\"".STR_SHOW_SPEC."\" title=\"".STR_SHOW_SPEC."\" style=\"border: 0;\"".$this->end_tag(),
										 1,
										 "",
										 "style=\"background: none;\"");
		}
		else if (($name=="new_cdate") || ($name=="cdate"))	// with date
		{
			if ((substr_count($options,"readonly")==0) && (substr_count($options,"disabled")==0))
			{
				$rc .= "&nbsp;<a href=\"javascript:getcdate();\" onmouseover=\"window.status='".STR_DATE_SETTODAY."';return true\" onmouseout=\"window.status=''\" style=\"background: none;\">";
				$rc .= "<img src=\"".$this->image_url."/last_contact.gif\" alt=\"".STR_DATE_SETTODAY."\" title=\"".STR_DATE_SETTODAY."\" style=\"border: 0;\"".$this->end_tag();
				$rc .= "</a>";
			}
		}
		else if (($name=="new_ddate") || ($name=="ddate")) // with date
		{
			if ((substr_count($options,"readonly")==0) && (substr_count($options,"disabled")==0))
			{
				$rc .= "&nbsp;<a href=\"javascript:getddate();\" onmouseover=\"window.status='".STR_DATE_SETTODAY."';return true\" onmouseout=\"window.status=''\" style=\"background: none;\">";
				$rc .= "<img src=\"".$this->image_url."/last_contact.gif\" alt=\"".STR_DATE_SETTODAY."\" title=\"".STR_DATE_SETTODAY."\" style=\"border: 0;\"".$this->end_tag();
				$rc .= "</a>";
			}
		}
		else if ($ptype==2)
		{
			if ((substr_count($options,"readonly")==0) && (substr_count($options,"disabled")==0))
			{
				$rc .= "&nbsp;<a href=\"javascript:GeneratePassword();\" onmouseover=\"window.status='".STR_PASSWORD_GENERATE."';return true\" onmouseout=\"window.status=''\" style=\"background: none;\">";
				$rc .= "<img src=\"".$this->image_url."/generator.gif\" alt=\"".STR_PASSWORD_GENERATE."\" title=\"".STR_PASSWORD_GENERATE."\" style=\"border: 0;\"".$this->end_tag();
				$rc .= "</a>";
			}

			if ((!isvoid($value)) && (!$this->is_form_error($name)))
			{
				$rc .= "&nbsp;<a href=\"javascript:ShowPassword();\" onmouseover=\"window.status='".STR_PASSWORD_SHOW."';return true\" onmouseout=\"window.status=''\" style=\"background: none;\">";
				$rc .= "<img src=\"".$this->image_url."/glasses.gif\" alt=\"".STR_PASSWORD_SHOW."\" title=\"".STR_PASSWORD_SHOW."\" style=\"border: 0;\"".$this->end_tag();
				$rc .= "</a>";
			}
			else
				$rc .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
		}
		else
		{
			// have to be integrated
			// new schema!

			if ($button_space)
			{
				$rc .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
			}
			else
			{
				if ($password_generate_button)
				{
					$rc .= "&nbsp;<a href=\"javascript:GeneratePassword();\" onmouseover=\"window.status='".STR_PASSWORD_GENERATE."';return true\" onmouseout=\"window.status=''\" style=\"background: none;\">";
					$rc .= "<img src=\"".$this->image_url."/generator.gif\" alt=\"".STR_PASSWORD_GENERATE."\" title=\"".STR_PASSWORD_GENERATE."\" style=\"border: 0;\"".$this->end_tag();
					$rc .= "</a>";
				}
				if ($password_show_button)
				{
					$rc .= "&nbsp;<a href=\"javascript:ShowPassword();\" onmouseover=\"window.status='".STR_PASSWORD_SHOW."';return true\" onmouseout=\"window.status=''\" style=\"background: none;\">";
					$rc .= "<img src=\"".$this->image_url."/glasses.gif\" alt=\"".STR_PASSWORD_SHOW."\" title=\"".STR_PASSWORD_SHOW."\" style=\"border: 0;\"".$this->end_tag();
					$rc .= "</a>";
				}
				if ($date_button)
				{
					$rc .= "&nbsp;<a href=\"javascript:ShowPassword();\" onmouseover=\"window.status='".STR_PASSWORD_SHOW."';return true\" onmouseout=\"window.status=''\" style=\"background: none;\">";
					$rc .= "<img src=\"".$this->image_url."/glasses.gif\" alt=\"".STR_PASSWORD_SHOW."\" title=\"".STR_PASSWORD_SHOW."\" style=\"border: 0;\"".$this->end_tag();
					$rc .= "</a>";
				}
			}
		}

		return $rc;
	}

	/**
	 * draw period form dialog
	 *
	 * @param	array	$uvar
	 * @param	bool	$with_userdef	true: with user defined period
	 *				 				    false: without user defined period {default}
	 * @param	int		$first_year
	 * @return	string					period form dialog
	 */
	function fm_select_period(&$uvar, $with_userdef=false, $first_year="")
	{
		if (!empty($first_year))	$fm_year = $this->fm_year_options($first_year);
		else						$fm_year = $this->fm_year_options();

		if (empty($uvar['period']))
		{
			$uvar['period']	= $this->default_date_period;
			$uvar['year_m']	= date("Y");
			$uvar['month']	= date("m");
		}

		$rc  = "<tr><td>".STR_PERIOD.":</td><td>".$this->fm_radio("period", "M", STR_MONTH, $uvar['period'])."&nbsp;</td>";

		$fmdata = $this->fm_month_options();
		$rc .= "<td>".$this->fm_select("month", $fmdata, $uvar['month'])."</td>\n";

		$rc .= "<td align=\"right\">".STR_YEAR.": ";
		$rc .= $this->fm_select("year_m", $fm_year, $uvar['year_m'])."</td></tr>\n";

		$rc .= "<tr><td>&nbsp;</td><td>".$this->fm_radio("period", "Q", STR_QUARTERLY, $uvar['period'])."&nbsp;</td>";
		unset($fmdata);
		$fmdata = $this->fm_quarter_options();
		$rc .= "<td>".$this->fm_select("quarter", $fmdata, $uvar['quarter'])."</td>\n";

		$rc .= "<td align=\"right\">".STR_YEAR.": ";
		$rc .= $this->fm_select("year_q", $fm_year, $uvar['year_q'])."</td></tr>\n";

		$rc .= "<tr><td>&nbsp;</td><td colspan=\"2\">".$this->fm_radio("period", "Y", STR_YEAR, $uvar['period'])."&nbsp;</td>";
		$rc .= "<td align=\"right\">".STR_YEAR.": ";
		$rc .= $this->fm_select("year_y", $fm_year, $uvar['year_y'])."</td></tr>\n";

		if ($with_userdef)
		{
			$rc .= "<tr><td>&nbsp;</td><td>".$this->fm_radio("period", "U", STR_USER_DEFINIED, $uvar['period'])."&nbsp;</td>";
			$rc .= "<td>".STR_PERIOD_FROM.": ".$this->fm_input("user_start",$uvar['user_start'],"s2",10)."</td>";
			$rc .= "<td align=\"right\">".STR_PERIOD_TILL.": ".$this->fm_input("user_end",$uvar['user_end'],"s2",10)."</td>";
			$rc .= "</tr>\n";
		}

		return $rc;
	}

	/**
	 * options with quarters
	 *
	 * @return	array
	 */
	function fm_quarter_options()
	{
		$fmdata = array();

		$curr_quarter = ceil(date("n")/3);

		for($i=0;$i<4;$i++)
		{
			$curr = $i+1;

			if ($curr_quarter==$curr)	$fmdata[] = array('value'=>$curr, 'desc'=>"$curr. ".STR_QUARTER, 'convert'=>false, 'default'=>true);
			else						$fmdata[] = array('value'=>$curr, 'desc'=>"$curr. ".STR_QUARTER, 'convert'=>false);
		}

		return $fmdata;
	}

	/**
	 * options with months
	 *
	 * @return	array
	 */
	function fm_month_options()
	{
		$fmdata = array();

		$curr_month = date("n");

		for($i=0;$i<12;$i++)
		{
			$curr = $i+1;

			if ($curr_month==$curr)	$fmdata[] = array('value'=>$curr, 'desc'=>Date_Calc::getMonthFullname($curr), 'convert'=>true, 'default'=>true);
			else					$fmdata[] = array('value'=>$curr, 'desc'=>Date_Calc::getMonthFullname($curr), 'convert'=>true);
		}

		return $fmdata;
	}

	/**
	 * options with years
	 *
	 * @param	int		$first		first available year
	 * @param 	int		$last
	 * @return	array
	 */
	function fm_year_options($first="", $last="")
	{
		if (isvoid($first))
		{
			$first = $this->first_date_year;
		}

		$fmdata = array();

		$curr_year = date("Y");

		if (!isvoid($last))
		{
			if ($last<$curr_year)	$i_max = $curr_year+1;
			else					$i_max = $last+1;
		}
		else
		{
			$i_max = $curr_year+1;
		}

		for($i=$first;$i<$i_max;$i++)
		{
			if ($curr_year==$i)	$fmdata[] = array('value'=>$i, 'desc'=>$i."&nbsp;&nbsp;", 'convert'=>false, 'default'=>true);
			else				$fmdata[] = array('value'=>$i, 'desc'=>$i."&nbsp;&nbsp;", 'convert'=>false);
		}

		return $fmdata;
	}

	/**
	 * Get day of month
	 *
	 * @param	int		$month
	 * @param	int		$year
	 * @return	string
	 */
	function fm_month_days($month,$year)
	{
	   if ($month==12)
		  $tmp_month=1;
	   else
		  $tmp_month = $month+1;
	   return date("t",mktime (0,0,0,$tmp_month,0,$year));
	}
}
?>