<?php
/**
 * cphplib is a PHP-functions library. Those could simply be integrated into existing PHP-Scripts and allow an easy use.
 * These functions are very flexible to use because they are kept very common.
 *
 * PHP versions 4 and 5
 *
 * @category	Utilities
 * @package		cphplib
 * @author		Alexander Meindl <am@meindlsoft.com>
 * @author		Sven Reul <heffer@quaddamage.de>
 * @copyright	(c) 2002-2005 meindlSOFT
 * @license		Released under v2 of the GNU LGPL
 * @version		Version 0.50 CVS: $Id: cphplib.inc,v 1.20 2005/11/11 09:12:25 alex Exp $
 * @link		http://www.meindlsoft.com/tools.php
 */


/**
 * Replacement for useless PHP empty function
 * Returns true, if string is empty
 *
 * @param	string	$str	string to validate
 * @param	bool	$mode	true for compatible with echo {default: false}
 * @return	bool
 */
function isvoid($str, $mode=false)
{
	$rc = false;

	if (is_array($str))
	{
		if (count($str)==0)
		{
			$rc = true;
		}
	}
	else
	{
		$str = trim($str);
		if (strlen($str)==0)
		{
			$rc = true;
		}
		else if (($mode) && ($str=="0"))
		{
			$rc = true;
		}
	}

	return $rc;
}

/**
 * cphplib class
 */
class cphplib
{
	/**
	 * cphplib version
	 *
	 * @var	float
	 */
	var $version = "0.50";

	/**
	 * Error level
	 *	0: show no error message
	 *  1: only print message
	 *  2: die, if error occurres
	 *  3: user defined function (callback) => error_user_function required
	 *
	 * @var	int
	 */
	var	$error_level = 2;

	/**
	 * User defined function
	 *
	 * Required for error_level=3
	 *
	 * @var	mixed
	 */
	var	$error_user_function;

	/**
	 * Date format
	 *
	 *	I	= International date identifier (MM/DD/YYYY)
	 *  S	= Science date identifier (YYYY-MM-DD)
	 *  C	= Date without seperators (YYYYMMDD)
	 *  L	= German date identifier (DD.MM.YYYY)
	 *
	 * @var	char
	 */
	var	$date_format;

	/**
	 * Time format
	 *
	 *	Y = 24 hours mode
	 *	N = 12 hours mode
	 *
	 * @var	char
	 */
	var	$time_format;

	/**
	 * locale to use (see "man locale")
	 *
	 * @var	string
	 */
	var	$locale;

	/**
	 * xhtml output
	 *
	 * @var	bool
	 */
	var	$xhtml = true;

	/**
	 * Max. entries on one page of a list
	 *
	 * @var	int
	 */
	var $page_entries = 20;

	/**
	 * Type of database
	 *
	 * @var	string
	 */
	var $db_type;

	/**
	 * Database pear object
	 * (mysql or pgsql)
	 *
	 * @var	resource
	 */
	var $db;

	/**
	 * like not case sensitive for SQL
	 *
	 * @var	resource
	 */
	var $sql_like;

	/**
	 * International seperator
	 *
	 * @var	char
	 */
	var $m_sep_i         = "/";

	/**
	 * Science seperator
	 *
	 * @var	char
	 */
	var $m_sep_s         = "-";

	/**
	 * German seperator
	 *
	 * @var	char
	 */
	var $m_sep_l         = ".";

	/**
	 * Image URL to all images
	 *
	 * @var	string
	 */
	var $image_url		= "images";

	// db session variables
	var $m_dbsession_handler      = false;              // must be true for handling session_id
	var $m_dbsession_id           = "";                 // dbsession ID
	var $m_dbsession_id_name      = "SID";              // dbsession ID name
	var $m_dbsession_table        = "dbsession";        // table name for dbsession use
	var $m_dbsession_detail_table = "dbsession_detail"; // table name for dbsession use
	var $m_dbsession_timeout      = 60;                 // dbsession timeout

	/**
	 * cookies can be used for dbsession
	 *
	 * @var bool
	 */
	var $m_dbsession_cookies      = false;

	/**
	 * take special care for mod_rewrite
	 *
	 * at the moment only url() is supported
	 *
	 * @var bool
	 */
	var $mod_rewrite = false;

	/**
	 * constructor
	 *
	 * @param	string		$locale		if no empty, locale and string will be initialised
	 * @return	cphplib
	 */
	function cphplib($locale="de_DE")
	{
		if (!empty($locale))
		{
			$this->locale = $locale;
			$this->set_locale();
			$this->set_strings();
		}
	}

	/**
	 * Set locale
	 *
	 */
	function set_locale()
	{
		if ($this->locale=="de_DE")
		{
			$this->date_format = "L";
			$this->time_format = "Y";

			if ($this->check_php_version("4.3.0"))
			{
				setlocale(LC_TIME, 'de_DE@euro', 'de_DE', 'german', 'deu');
				setlocale(LC_CTYPE, 'de_DE@euro', 'de_DE', 'german', 'deu');
			}
			else if ($this->os_type(true) == "w")
			{
				setlocale(LC_TIME, 'german');
				setlocale(LC_CTYPE, 'german');
			}
			else
			{
				setlocale(LC_TIME, 'de_DE');
				setlocale(LC_CTYPE, 'de_DE');
			}
		}
		else
		{
			$this->set_format("I", "N");

		    setlocale(LC_TIME, $this->locale);
       		setlocale(LC_CTYPE, $this->locale);
		}
	}

	/**
	 * Set date and time format to cphplib
	 *
	 * @param	char	$date_format	see $this->date_format for valid values
	 * @param	char	$time_format	see $this->time_format for valid values
	 */
	function set_format($date_format, $time_format)
	{
		$this->date_format = $date_format;
		$this->time_format = $time_format;
	}

	/**
	 * Set strings for pager and Date methods
	 *
	 */
	function set_strings()
	{
		if (substr($this->locale, 0, 2) =="de")
		{
			include_once("i18n/german.inc");
		}
		else
		{
			include_once("i18n/english.inc");
		}
	}

	/**
	 * Opens a connection to a database server and select database
	 * Furthermore it sets $this->db to the PEAR database object,
	 * which is required for dbsession
	 *
	 * @param	string	$dsn	Data Source Name ( for more information see PEAR documentation)
	 *							addon array key:
	 *			            		persistent	= true for persistent {default: false}
	 * @param 	bool	$set_db	if true, db object will be set to $this->db
	 *							(and db_tyle and sql_like, too)
	 * @return	object			PEAR database object
	 */
	function db_connect($dsn, $set_db=true)
	{
		if ($this->file_exists("DB.php"))
		{
			if ((!isvoid($dsn['phptype'])) && (!(extension_loaded($dsn['phptype']))))
			{
				$this->show_error("db_connect", "PHP database module &quot;".$dsn['phptype']."&quot; is not supported by your system.");
			}

			require_once("DB.php");
		}
		else
		{
			$this->show_error("db_connect", "Pear \"DB Package\" required.");
		}

		if ($dsn['persistent'])	$db =& DB::connect($dsn, true);
		else					$db =& DB::connect($dsn ,false);

		if (DB::isError($db))
		{
			$this->show_error("db_connect", $db->getMessage(), $db->getCode());
		}

		if ($set_db)
		{
			$this->db =& $db;

			if ($dsn['phptype']=="mysqli")	$this->db_type = "mysql";
			else							$this->db_type = $dsn['phptype'];

			if ($dsn['phptype']=="pgsql")	$this->sql_like = "ILIKE";
			else							$this->sql_like = "LIKE";
		}

		return $db;
	}

	/**
	 * disconnect current database connection
	 *             (change $this->db for select the right one, if you
	 *              use more the one connection)
	 *
	 * @return bool		true if no errors occurred, otherwise false
	 */
	function db_close()
	{
		if (DB::isError($this->db))
		{
			$this->show_error("db_close", $this->db->getMessage());
		}

		return @ $this->db->disconnect();
	}

	/**
	 * Get the id generated from the previous INSERT operation
	 *
	 * @return	int
	 */
	function db_insert_id()
	{
		if (DB::isError($this->db))
		{
			$this->show_error("db_insert_id", $this->db->getMessage());
		}

		$rc = $this->db->getOne("SELECT last_insert_id()");
		if (DB::isError($rc))
		{
			$this->show_error("db_insert_id", $rc->getMessage());
		}

		return $rc;
	}

	/**
	 * Get next available number
	 *
	 * @param	string	$table		database table to use
	 * @param	string	$column		database column to use
	 * @param	string	$where		SQL WHERE restriction, e.g. "thisvalue>0"
	 *								(without "WHERE" in string!)
	 * @return	int					highst number in column + 1
	 */
	function db_next_id($table, $column, $where=null)
	{
		if (DB::isError($this->db))
		{
			$this->show_error("db_next_id", $this->db->getMessage());
		}

		$sqlstr = "SELECT MAX($column)+1 FROM $table";
		if (isset($where)) $sqlstr .= " WHERE ".$where;

		$tmp_id = $this->db->getOne($sqlstr);
		if (DB::isError($tmp_id))
		{
			$this->show_error("db_next_id", $tmp_id->getMessage());
		}

		if (empty($tmp_id))
		{
			return 1;
		}
		else
		{
			return $tmp_id;
		}
	}

	/**
	 * Get next available number
	 *
	 * @param	string	$seq_name	name of sequence
	 * @return	int
	 */
	function db_seq_id($seq_name)
	{
		if (DB::isError($this->db))
		{
			$this->show_error("db_seq_id", $this->db->getMessage());
		}
		else if (isvoid($seq_name))
		{
			$this->show_error("db_seq_id", "seq_name is missing");
		}
		else if (!is_object($this->db))
		{
			$this->show_error("db_seq_id", "\$this-&gt;db is not an database object");
		}

		$tmp_id = $this->db->nextId($seq_name);

		if (DB::isError($tmp_id))
		{
			$this->show_error("db_seq_id", $tmp_id->getMessage());
		}

		if (empty($tmp_id))	return 1;
		else				return $tmp_id;
	}

	/**
    *  Get number of lowest unused number in a column)
    *
    * @param	string	$table			database table to use
    * @param	string	$column			database column to use
    * @param	string	$where_key		database column name for limitation {default: void}
    * @param	string	$where_value	limitation value {default: void}
    * @return	int						lowest unused number in a column
    */
   function db_free_id($table, $column, $where_key="", $where_value="")
   {
	  if (DB::isError($this->db))
	  {
			$this->show_error("db_free_id", $this->db->getMessage());
	  }

	  $rc=0;

	  if ((isvoid($where_key)) && (isvoid($where_value)))
		 $sqlstr = "SELECT $column FROM $table ORDER BY $column";
	  else if ((!isvoid($where_key)) && (!isvoid($where_value)))
		 $sqlstr = "SELECT $column FROM $table WHERE $where_key='$where_value' ORDER BY $column";

	  if (!isvoid($sqlstr))
	  {
		 $que = $this->db->query($sqlstr);
		 if ($que->numRows()>0)
		 {
			$count=1;
			while ($row = $que->fetchRow())
			{
			   $tmp_id = $row[0];
			   if ($count==$tmp_id)
				  $count++;
			   else
			   {
				  $rc = $count;
				  break;
			   }
			}
			if ($rc==0)
			   $rc=$count;
		 }
		 else
			$rc=1;
	  }
	  return $rc;
   }

	/**
	 * check right database version
	 *
	 * @param	float	$mav	major release number {default: 3.23}
	 * @param	float	$miv	minor release number {default: 6}
	 * @return	string			active database version, if false empty
	 */
	function db_version($mav="3.23", $miv="6")
	{
		if (DB::isError($this->db))
		{
			$this->show_error("db_version", $this->db->getMessage());
		}

		$rc = "unknown";

		if (($this->db_type)=="mysql")
		{
			if ($this->db->phptype=="mysqli")	$tmp_rc = mysqli_get_server_info($this->db->connection);
			else 								$tmp_rc = mysql_get_server_info();

			$t_mav1 = strtok($tmp_rc,'.');
			$t_mav2 = strtok('.');
			$t_miv  = strtok('.');
			$t_mav  = $t_mav1.".".$t_mav2;
			if ($t_mav > $mav) $rc = "MySQL ".$tmp_rc;
			else if (($t_mav == $mav) && (intval($t_miv) >= $miv)) $rc = "MySQL ".$tmp_rc;
			else
			{
				$this->show_error("db_version", "MySQL version to old (version $mav.$miv or higher required), <b>program aborted</b>.");
			}
		}
		else
		{
			$tmp_rc = $this->db->getOne("SELECT version()");
			if (!isvoid($tmp_rc))
			{
				$rc = substr($tmp_rc,0,strpos($tmp_rc," on"));
				if (isvoid($rc)) $rc = $tmp_rc; // just to be sure
			}
		}

		return $rc;
	}

	/**
	 * starts dbsession. Enables session fallback handling. This functions
	 * will be required, if you want to handle the session_id with
	 * url or formstart
	 * Required	: read README file
	 *
	 * @param	string	$session_id		session id to use, if empty new unique id
	 *  			                    will be generated
	 * @param	bool	$table_error	show error message, if dbsession tables doesn't exist
	 * @return	string					session_id
	 */
	function dbsession_start($session_id="", $table_error=true)
	{
		global $HTTP_COOKIE_VARS;

		$this->m_dbsession_handler=true;
		$IDpassed = false;

		if (DB::isError($this->db))
		{
			$this->show_error("dbsession_start", $this->db->getMessage());
		}
		else
		{
			$tdata = $this->db->getListOf("tables");
		  	if ((!in_array($this->m_dbsession_table,$tdata)) || (!in_array($this->m_dbsession_detail_table,$tdata)))
				$dbtables_found = false;
			else
				$dbtables_found = true;

			if ((!$dbtables_found) && ($table_error))
			{
				$this->show_error("dbsession_start", "missing dbsession database table");
			}
		}

		if (empty($session_id))	$SID = $this->get_user_var($this->m_dbsession_id_name,'POST,GET,COOKIE');
		else					$SID = $session_id;

		// generate session_is
		$session_is = $this->get_id_string(false);

		// validate session_id
		if ((!empty($SID)) && (strlen($SID)==32) && ($dbtables_found))
		{
			$t_sec   = date("s");
			$t_min   = date("i");
			$t_hours = date("H");
			$t_day   = date("d");
			$t_month = date("m");
			$t_year  = date("Y");
			$session_start = $t_year."-".$t_month."-".$t_day." ".$t_hours.":".$t_min.":".$t_sec;

			$stempel     = mktime($t_hours,($t_min+$this->m_dbsession_timeout),$t_sec,$t_month,$t_day,$t_year);
			$faellig     = getdate($stempel);
			$session_end = $faellig['year']."-".$faellig['mon']."-".$faellig['mday']." ".$faellig['hours'].":".$faellig['minutes'].":".$faellig['seconds'];

			$sqlstr = "SELECT COUNT(*) FROM ".$this->m_dbsession_table." WHERE session_id='$SID'";
			if ($this->db->getOne($sqlstr)>0)
			{
				$sqlstr = "SELECT session_is,session_end FROM ".$this->m_dbsession_table." WHERE session_id='$SID'";
				$sdata = $this->db->getRow($sqlstr,DB_FETCHMODE_ASSOC);
				if (!empty($sdata['session_end']))
				{
					$tmp_stamp_db  = $this->convtoTimestamp($sdata['session_end'],'datetime');
					$tmp_stamp_now = $this->convtoTimestamp();
					if (($sdata['session_is']==$session_is) && ($tmp_stamp_db > $tmp_stamp_now))
					{
						$sqlstr = "UPDATE ".$this->m_dbsession_table." SET session_end=".$this->sql_value($session_end);
						$sqlstr .= " WHERE session_id='$SID'";
						$this->db->query($sqlstr);
						$IDpassed = true;
					}
					else
						$this->dbsession_end($SID);
				}
			}
			else
			{
				$sqlstr = "INSERT INTO ".$this->m_dbsession_table." (session_id,session_start,session_end,session_is)";
				$sqlstr .= " VALUES ('$SID',";
				$sqlstr .= $this->sql_value($session_start).",";
				$sqlstr .= $this->sql_value($session_end).",";
				$sqlstr .= $this->sql_value($session_is) .")";
				$this->db->query($sqlstr);
				$IDpassed = true;
			}
		}

		// calculate new SID and session_is
		if (!$IDpassed) $SID = $this->get_id_string(true);

		$this->m_dbsession_id = $SID;
		setcookie($this->m_dbsession_id_name, $this->m_dbsession_id,0,"/");

		if ((!$IDpassed) && ($dbtables_found))  // no valid session_id in url found
		{
			$tmp_script_name  = $_SERVER['SCRIPT_NAME'];
			$tmp_server_name  = $_SERVER['SERVER_NAME'];
			$tmp_server_port  = $_SERVER['SERVER_PORT'];
			$tmp_query_string = $_SERVER['QUERY_STRING'];

			$query	= $tmp_query_string != ""
				? "?".$tmp_query_string
				: "";

			$url = $tmp_script_name.$query;

			// non-standard port?
			$portMatch = array();
			$port = !preg_match("/^(80|443)$/", $tmp_server_port, $portMatch)
				? ":".$tmp_server_port
				: "";

			$new_location  = (($portMatch[1] == 443) ? "https://" : "http://");
			$new_location .= $tmp_server_name.$port.$this->url($url,"",2);

			// redirect
			header("Location: $new_location");
			exit;
		}

		$this->m_dbsession_cookies = (isset($_COOKIE[$this->m_dbsession_id_name]) && @strlen($_COOKIE[$this->m_dbsession_id_name]) == 32);

		return $SID;
	}

	/**
	 * starts sub session within a session. You can use it to group the session
     * in different parts
     * Required   : read README file
	 *
	 * @param	string	$session_id		session id to use (main session)
	 * @param	string	$session_subid	session_subid.  If empty new unique id
	 *									will be generated if required
	 * @return	string					session_subid
	 */
	function dbsessionsub_start($session_id, $session_subid="")
	{
		if (DB::isError($this->db))
		{
			$this->show_error("dbsessionsub_start", $this->db->getMessage());
		}

		$sqlstr = "SELECT COUNT(*) FROM ".$this->m_dbsession_table." WHERE session_id='$session_id'";
		if ($this->db->getOne($sqlstr)==0)
		{
			$this->show_error("dbsessionsub_start", "invalid session_id");
		}

		// generate session_is
		$session_is = $this->get_id_string(false);

		// validate session_subid
		if ((empty($session_subid)) || (strlen($session_subid)!=32))
			$session_subid = $this->get_id_string(true);

		$t_sec   = date("s");
		$t_min   = date("i");
		$t_hours = date("H");
		$t_day   = date("d");
		$t_month = date("m");
		$t_year  = date("Y");
		$session_start = $t_year."-".$t_month."-".$t_day." ".$t_hours.":".$t_min.":".$t_sec;

		$stempel     = mktime($t_hours,($t_min+$this->m_dbsession_timeout),$t_sec,$t_month,$t_day,$t_year);
		$faellig     = getdate($stempel);
		$session_end = $faellig['year']."-".$faellig['mon']."-".$faellig['mday']." ".$faellig['hours'].":".$faellig['minutes'].":".$faellig['seconds'];

		$sqlstr = "SELECT COUNT(*) FROM ".$this->m_dbsession_table." WHERE session_subid='$session_subid'";
		if ($this->db->getOne($sqlstr)>0) // maybe update is overkill, but it's safer.
		{
			$sqlstr  = "UPDATE ".$this->m_dbsession_table." SET session_end=".$this->sql_value($session_end);
			$sqlstr .= " WHERE session_subid='$session_subid'";
			$this->db->query($sqlstr);
		}
		else
		{
			$sqlstr = "INSERT INTO ".$this->m_dbsession_table." (session_id,session_subid,session_start,session_end,session_is)";
			$sqlstr .= " VALUES ('$session_id','$session_subid',";
			$sqlstr .= $this->sql_value($session_start).",";
			$sqlstr .= $this->sql_value($session_end).",";
			$sqlstr .= $this->sql_value($session_is) .")";
			$this->db->query($sqlstr);
		}

		return $session_subid;
   }

	/**
	 * ends dbsession or dbsessionsub
	 * (Read the README file for requirements)
	 *
	 * @param	string	$session_id	session id which should end ( and expired dbsession will automatically be removed)
	 * @return	bool				true if successfully end dbsession
	 */
	function dbsession_end($session_id="")
	{
		if (DB::isError($this->db))
		{
			$this->show_error("dbsession_end", $this->db->getMessage());
		}

		$sqlstr = "SELECT session_id, session_subid FROM ".$this->m_dbsession_table." WHERE session_end<=NOW()";
		if (!isvoid($session_id))
			$sqlstr .= " OR session_id=".$this->sql_value($session_id)." OR session_subid=".$this->sql_value($session_id);
		@ $que = $this->db->query($sqlstr);
		@ $num = $que->numRows();
		if ($num>0)
		{
			while ($row = $que->fetchRow())
			{
				if (!empty($row[1])) // sub session
				{
					$sqlstr1 = "DELETE FROM ".$this->m_dbsession_table." WHERE session_subid=".$this->sql_value($row[1]);
					$sqlstr2 = "DELETE FROM ".$this->m_dbsession_detail_table." WHERE session_id=".$this->sql_value($row[1]);
				}
				else						// session with all sub sessions
				{
					$sqlstr1 = "DELETE FROM ".$this->m_dbsession_table." WHERE session_id=".$this->sql_value($row[0]);
					$sqlstr2 = "DELETE FROM ".$this->m_dbsession_detail_table." WHERE session_id=".$this->sql_value($row[0]);

					// take care of sub sessions /////////////
					$sqlstr = "SELECT session_subid FROM ".$this->m_dbsession_table." WHERE session_id=".$this->sql_value($row[0]);
					if (!isvoid($session_id))
					@ $sub_que = $this->db->query($sqlstr);
					@ $num = $sub_que->numRows();
					if ($num>0)
					{
						while ($sub_row = $sub_que->fetchRow())
						{
							$sqlstr = "DELETE FROM ".$this->m_dbsession_detail_table." WHERE session_id=".$this->sql_value($sub_row[0]);
							@ $this->db->query($sqlstr1);
						}
					}
					//////////////////////////////////////////
				}
				@ $this->db->query($sqlstr1);
				@ $this->db->query($sqlstr2);
			}
		}

		return true;
	}

	/**
	 * register variable in dbsession
	 * Required   : read README file
	 *
	 * @param	string	$session_id	session id to use
	 * @param	string	$var_name	variable to add
	 * @param	mixed	$var_value	value of variable
	 * @return	string				value of variable
	 */
	function dbsession_write($session_id, $var_name, $var_value)
	{
		$rc = "";

		if (DB::isError($this->db))
		{
			$this->show_error("dbsession_write", $this->db->getMessage());
		}

		if ((empty($session_id)) || (empty($var_name)))
		{
			$error_string = "dbsession_write error: var_name missing";
			trigger_error($error_string, E_USER_ERROR);
		}
		else if (empty($var_value))
		{
			$this->dbsession_delete($session_id, $var_name);
		}
		else
		{
			// if already stored, delete it
			$sqlstr = "SELECT COUNT(session_id) FROM ".$this->m_dbsession_detail_table." WHERE session_id=".$this->sql_value($session_id)." AND session_var=".$this->sql_value($var_name);
			if ($this->db->getOne($sqlstr)>0) // update
				$this->dbsession_delete($session_id,$var_name);

			if (is_array($var_value))
			{
				$sqlstr  = "INSERT INTO ".$this->m_dbsession_detail_table." (session_id,session_array,session_var,session_value) VALUES (".$this->sql_value($session_id).",'Y',";
				$sqlstr .= "'$var_name',".$this->sql_value(serialize($var_value)).")";
				$this->db->query($sqlstr);
			}
			else
			{
				$sqlstr  = "INSERT INTO ".$this->m_dbsession_detail_table." (session_id,session_array,session_var,session_value) VALUES (".$this->sql_value($session_id).",'N',";
				$sqlstr .= $this->sql_value($var_name) .",";
				if (!isvoid($var_value)) $sqlstr .= $this->sql_value($var_value) .")";
				else $sqlstr .= "NULL)";
				$this->db->query($sqlstr);
			}

			$rc = $var_value;
		}

		return $rc;
	}

	/**
	 * register variable in dbsession
	 *
	 * @param	string	$session_id		session id to use
	 * @param	string	$var_name		name of variable
	 * @return	string					value of variable
	 */
	function dbsession_read($session_id, $var_name)
	{
		$rc = "";

		if (DB::isError($this->db))
		{
			$this->show_error("dbsession_read", $this->db->getMessage());
		}

		if ((empty($session_id)) || (empty($var_name)))
		{
			$error_string = "dbsession_read error: var_name missing";
			trigger_error($error_string, E_USER_ERROR);
		}
		else
		{
			$sqlstr  = "SELECT session_array,session_value FROM ".$this->m_dbsession_detail_table;
			$sqlstr .= " WHERE session_id=".$this->sql_value($session_id)." AND session_var=".$this->sql_value($var_name);
			$row = $this->db->getRow($sqlstr);
			if (is_array($row))
			{
				if ($row[0]=="N")	$rc = $row[1];
				else				$rc = unserialize($row[1]);
			}
		}

		return $rc;
	}

	/**
	 * unregister variable in dbsession
	 * Required   : read README file
	 *
	 * @param	string	$session_id	session id which will be used for the variabel
	 * @param	string	$var_name	variable to remove
	 * @return	bool				true if successfully removed variable
	 */
	function dbsession_delete($session_id, $var_name)
	{
		if (DB::isError($this->db))
		{
			$this->show_error("dbsession_delete", $this->db->getMessage());
		}

		if ((empty($session_id)) || (empty($var_name)))
		{
			$error_string = "dbsession_delete error: var_name missing";
			trigger_error($error_string, E_USER_ERROR);

			return false;
		}
		else
		{
			$sqlstr = "DELETE FROM ".$this->m_dbsession_detail_table." WHERE session_id=".$this->sql_value($session_id)." AND session_var=".$this->sql_value($var_name);
			$this->db->query($sqlstr);

			return true;
		}
	}

	/**
	 * prints url as html tag
	 * (this function can only handle the session_id, if dbsession_start has
	 * been called immediately after creating the class object)
	 *
	 * @param	string	$url		url
	 * @param	string	$name		name of url {default: url}
	 * @param	int		$mode		0: no session_id
	 *								1: with session_id if required {default: 1}
	 *								2: just url with session_id (without TAG)
	 *								3: just url with session_id (without TAG), but with delimiter &amp; instead of &
	 *								4: with session_id (always)
	 * @param	string	$title		url title (hover text)
	 * @param	string	$customize	other parameters like target, style or class
	 * @return	string				created url
	 */
	function url($url, $name="", $mode=1, $title="", $customize="")
	{
		if ((($mode>0) && (!$this->m_dbsession_cookies) && (!$this->mod_rewrite))
			|| ($mode==4))
		{
			if ($this->m_dbsession_handler) // if cphplib handles session fallback
			{
				// only add session_id if its on same host as script was executed (server).
				if ((($this->url_on_scripthost($url)) || ($mode==4)) &&
					(substr_count($url,"mailto:")==0))
				{
					// Anchor-Fragment extrahieren
					$dummyArray = split("#",$url);
					$pathInfo = $dummyArray[0];

					// evtl. (defekte) Session-ID(s) aus dem Querystring entfernen
					$pathInfo = preg_replace("/[?|&]".$this->m_dbsession_id_name."=[^?|&]*/","",$pathInfo);

					// evtl. Query-Delimiter korrigieren
					if (preg_match("/&/",$pathInfo) && !preg_match("/\?/",$pathInfo))
						$pathInfo = preg_replace("/&/","?",$pathInfo,1);

					// clear trash
					$match = array();
					preg_match("/(.*)(?<!&|\?)/",$pathInfo,$match);
					$url = $match[0];

					// add new session name and session id
					if (($mode==1) || ($mode==3) || ($mode==4))	$url .= preg_match("/\?/",$url) ? "&amp;" : "?";
					else						 				$url .= preg_match("/\?/",$url) ? "&" : "?";
					$url .= $this->m_dbsession_id_name."=".$this->m_dbsession_id;

					// add anchor part again
					$url .= isset($dummyArray[1]) ? "#".$dummyArray[1] : "";
				}
			}
		}

		if (($mode==2) || ($mode==3))  return $url;
		else
		{
			if (isvoid($name)) $name = $url;
			else               $name = trim($name);

			$rc = "<a href=\"".$url."\"";
			if (!isvoid($title))		$rc .= " title=\"".$title."\"";
			if (!isvoid($customize))	$rc .= " $customize";
			$rc .= ">".$name."</a>";
			return $rc;
		}
	}

	/**
	 * removes magic quotes
	 *
	 * @param array $array
	 */
	function remove_magic_quotes(&$array)
	{
		if(!get_magic_quotes_gpc())	return;

		foreach($array as $key => $value)
		{
			if(is_array($value))	$this->remove_magic_quotes($value);
			else					$array[$key] = stripslashes($value);
		}
	}

	/**
	 * returns value of special variable type from user input
	 * (If register_globals is Off (default since 4.2.0), this function
	 * can be used, to handle user variables (insecure variables).
	 * You can use more than one type (seperated with ",")
     * DBSESSION only works if dbsession_start has been called before.
	 *
	 * @param	string	$var_name	name of variable
	 * @param	string	$var_type	type of variable (POST, GET, COOKIE, SCRIPT, SESSION or DBSESSION)
	 *					            (note: SCRIPT means, declared variable above the function)
	 * @return	mixed				value of variable or "", if invalid
	 */
	function get_user_var($var_name, $var_type)
	{
		global $HTTP_POST_VARS, $HTTP_GET_VARS, $HTTP_SESSION_VARS, $HTTP_COOKIE_VARS;

		$rc = "";

		$types = explode(",", $var_type);

		while (list(, $value) = each($types))
		{
			if (strtoupper($value) == "POST")
			{
				if (isset($_POST))
				{
					if ((isset($_POST[$var_name])) && (!isvoid($_POST[$var_name])))
					{
						if (get_magic_quotes_gpc())
						{
							if (is_array($_POST[$var_name]))
							{
								$this->remove_magic_quotes($_POST[$var_name]);
								return $_POST[$var_name];
							}
							else	return stripslashes($_POST[$var_name]);
						}
						else		return $_POST[$var_name];
					}
				}
				else
				{
					if ((isset($HTTP_POST_VARS[$var_name])) && (!isvoid($HTTP_POST_VARS[$var_name])))
					{
						if (get_magic_quotes_gpc())
						{
							if (is_array($HTTP_POST_VARS[$var_name]))
							{
								$this->remove_magic_quotes($HTTP_POST_VARS[$var_name]);
								return $HTTP_POST_VARS[$var_name];
							}
							else	return stripslashes($HTTP_POST_VARS[$var_name]);
						}
						else		return $HTTP_POST_VARS[$var_name];
					}
				}
			}
			else if (strtoupper($value) == "GET")
			{
				if (isset($_GET))
				{
					if ((isset($_GET[$var_name])) && (!isvoid($_GET[$var_name])))
					{
						if (get_magic_quotes_gpc())
						{
							if (is_array($_GET[$var_name]))
							{
								$this->remove_magic_quotes($_GET[$var_name]);
								return $_GET[$var_name];
							}
							else	return stripslashes($_GET[$var_name]);
						}
						else		return $_GET[$var_name];
					}
				}
				else
				{
					if ((isset($HTTP_GET_VARS[$var_name])) && (!isvoid($HTTP_GET_VARS[$var_name])))
					{
						if (get_magic_quotes_gpc())
						{
							if (is_array($HTTP_GET_VARS[$var_name]))
							{
								$this->remove_magic_quotes($HTTP_GET_VARS[$var_name]);
								return $HTTP_GET_VARS[$var_name];
							}
							else	return stripslashes($HTTP_GET_VARS[$var_name]);
						}
						else		return $HTTP_GET_VARS[$var_name];
					}
				}
			}
			else if (strtoupper($value) == "COOKIE")
			{
				if (isset($_COOKIE))
				{
					if ((isset($_COOKIE[$var_name])) && (!isvoid($_COOKIE[$var_name])))
					{
						if (get_magic_quotes_gpc())
						{
							if (is_array($_COOKIE[$var_name]))
							{
								$this->remove_magic_quotes($_COOKIE[$var_name]);
								return $_COOKIE[$var_name];
							}
							else	return stripslashes($_COOKIE[$var_name]);
						}
						else		return $_COOKIE[$var_name];
					}
				}
				else
				{
					if ((isset($HTTP_COOKIE_VARS[$var_name])) && (!isvoid($HTTP_COOKIE_VARS[$var_name])))
					{
						if (get_magic_quotes_gpc())
						{
							if (is_array($HTTP_COOKIE_VARS[$var_name]))
							{
								$this->remove_magic_quotes($HTTP_COOKIE_VARS[$var_name]);
								return $HTTP_COOKIE_VARS[$var_name];
							}
							else	return stripslashes($HTTP_COOKIE_VARS[$var_name]);
						}
						else		return $HTTP_COOKIE_VARS[$var_name];
					}
				}
			}
			else if (strtoupper($value) == "SESSION")
			{
				if (isset($_SESSION))
				{
				   if ((isset($_SESSION[$var_name])) && (!isvoid($_SESSION[$var_name])))
						return $_SESSION[$var_name];
				}
				else
				{
					if ((isset($HTTP_SESSION_VARS)) && (!isvoid($HTTP_SESSION_VARS)))
						return $HTTP_SESSION_VARS[$var_name];
				}
			}
			else if ((strtoupper($value) == "DBSESSION") && ($this->m_dbsession_handler))
			{
				$SID = $this->m_dbsession_id;
				if (empty($SID))
				{
					$this->show_error("get_user_var", "dbsession_id is missing (DBSESSION)");
				}

				$tmp_rc = $this->dbsession_read($SID,$var_name);
				if (isset($tmp_rc))	return $tmp_rc;
			}
		}
		return $rc;
	}

	/**
	 * search needle in multi array
	 *
	 * @param	string	$needle
	 * @param	array	$haystack
	 * @return	bool
	 */
	function in_multi_array($needle, $haystack)
	{
		$rc = false;

		if(is_array($haystack))
		{
			if(in_array($needle, $haystack))
			{
				$rc = true;
			}
			else
			{
				for($i = 0; $i<sizeof($haystack); $i++)
				{
					if(is_array($haystack[$i]))
					{
						if($this->in_multi_array($needle, $haystack[$i]))
						{
							$rc = true;
							break;
						}
					}
				}
			}
		}
		return $rc;
	}

   /**
    * Removes duplicate values from an array (recursive)
	*             (this function is much slower than the internal php function)
    *
    * @param	array	$thearray	array to unique
    * @return	array
    */
   function array_unique(&$thearray)
   {
	  sort($thearray);
	  reset($thearray);

	  $newarray = array();
	  $i = 0;
	  $element = current($thearray);
	  for ($n=0;$n<sizeof($thearray);$n++)
	  {
		 if (next($thearray) != $element)
		 {
			$newarray[$i] = $element;
			$element = current($thearray);
			$i++;
		 }
	  }
	  return $newarray;
   }

	/**
	 * sort array (subfunction for usort)
	 *
	 * @param	array	$a	keys
	 * @param	array	$b	values
	 * @return	array
	 */
	function array_sort($a, $b)
	{
		$as = strtoupper(trim($a[1]));
		$bs = strtoupper(trim($b[1]));

		$as = strtr($as, "ä", "A");
		$as = strtr($as, "ö", "O");
		$as = strtr($as, "ü", "U");
		$as = strtr($as, "ß", "S");
		$bs = strtr($bs, "Ä", "A");
		$bs = strtr($bs, "Õ", "O");
		$bs = strtr($bs, "Ü", "U");

		if ($as == $bs) return 0;
		else return ($as > $bs)?1:-1;
	}

	/**
	 *reverse sort array (subfunction for usort)
	 *
	 * @param	array	$a	keys
	 * @param	array	$b	values
	 * @return	array
	 */
	function array_rsort($a, $b)
	{
		$as = strtoupper(trim($a[1]));
		$bs = strtoupper(trim($b[1]));

		$as = strtr($as, "ä", "A");
		$as = strtr($as, "ö", "O");
		$as = strtr($as, "ü", "U");
		$as = strtr($as, "ß", "S");
		$bs = strtr($bs, "Ä", "A");
		$bs = strtr($bs, "Õ", "O");
		$bs = strtr($bs, "Ü", "U");

		if ($as == $bs) return 0;
		else return ($as < $bs)?1:-1;
	}

	/**
	 * the current date
	 *
	 * @param	char	$date_format	see member variables $this->date_format below
	 * @return	date
	 */
	function currentDate($date_format=null)
	{
		if ((!isset($date_format)) || (empty($date_format)))	$date_format = $this->date_format;

		$day   = date("d");
		$month = date("m");
		$year  = date("Y");

		if      ($date_format=="I") return $month.$this->m_sep_i.$day  .$this->m_sep_i.$year;
		else if ($date_format=="S") return $year .$this->m_sep_s.$month.$this->m_sep_s.$day;
		else if ($date_format=="C") return $year                .$month.               $day;
		else						return $day  .$this->m_sep_l.$month.$this->m_sep_l.$year;
	}

	/**
	 * the current time
	 *
	 * @param	char	$time_format	see member variable $this->time_format below
	 * @return	time
	 */
	function currentTime($time_format=null)
	{
		if ((!isset($time_format)) || (empty($time_format)))	$time_format = $this->time_format;

		if ($time_format=="Y")
			return date("H").":".date("i").":".date("s");
		else
			return date("h").":".date("i").":".date("s")." ".date("a");
	}

	/**
	 * Get date from db-date format
	 * (short form of convDate)
	 *
	 * @param	string	$dbdate
	 * @param	bool	$short_mode
	 * @return	string
	 */
	function show_dbdate($dbdate, $short_mode=true)
	{
		if ($short_mode==true)
		{
			return $this->convDate($dbdate, 'S', $this->date_format, array('short_mode'=>true));
		}
		else
		{
			return $this->convDate($dbdate, 'S', $this->date_format);
		}
	}

	/**
	 * Get Datetime from db-date format
	 * (short form of convDate)
	 *
	 * @param	string	$dbdate
	 * @param	bool	$with_seconds
	 * @param	bool	$short_mode
	 * @param	string	$at
	 * @return	string
	 */
	function show_dbdatetime($dbdate, $with_seconds=false, $short_mode=true, $at=null)
	{
		return $this->convDateTime($dbdate, $this->date_format, $this->time_format, $with_seconds, $short_mode, $at);
	}

	/**
	 * Removes all whitespaces in a string
	 *
	 * @param	string	$str
	 * @return	string
	 */
	function killSpace($str)
	{
		$rc = htmlentities($str);
		$rc = str_replace("&nbsp;","",$rc);
		$rc = str_replace("&#160;","",$rc);
		$rc = str_replace(" ","",$rc);
		return $rc;
	}

	/**
	 * validate given date
	 *
	 * @param	mixed	$timestamp	array of date for validating or timestamp
	 *									array keys:	timestamp['year']
	 *      							            timestamp['month']
 	 *					                	        timestamp['day']
	 *                  				    	 	timestamp['hour']
	 *                      				   		timestamp['minute']
	 *		                 	         			timestamp['second']
	 * @param	bool	$mode		if true, input date is a timestamp {default: false}
	 * @return	timestamp			timestamp with valid date, if invalid input datas, return timestamp will
	 *								be return the nearest valid date
	 */
	function validate_timestamp($timestamp,$mode=false)
	{
		if (!$mode)
		{
			$timestamp = mktime($timestamp['hour'],
							$timestamp['minute'],
							$timestamp['second'],
							$timestamp['month'],
							$timestamp['day'],
							$timestamp['year']);
		}

		if ($timestamp<mktime(2,0,0,1,1,1970))
			return mktime(2,0,0,1,1,1970);
		else if ($timestamp>mktime(2,0,0,12,31,2037))
			return mktime(2,0,0,12,31,2037);
		else
			return $timestamp;
	}

   	/**
   	 *	calculates days to a given date
   	 *
   	 * @param	date	$start_date		first date
   	 * @param	date	$end_date		last date
   	 * @param	char	$date_format	see member variables $this->date_format below
   	 * @return	int						number of days, if date is out of range -1
   	 */
   	function date2days($start_date, $end_date, $date_format="S")
	{
		if ($date_format!="S")
		{
			$start_date = $this->convDate($start_date, $date_format, 'S');
			$end_date   = $this->convDate($end_date, $date_format, 'S');
		}

   		$start_year  = strtok($start_date,"-");
   		$start_month = strtok("-");
   		$start_day   = strtok("-");

   		$end_year  = strtok($end_date,"-");
   		$end_month = strtok("-");
   		$end_day   = strtok("-");

		if ($this->file_exists("Date.php"))
			require_once("Date.php");
		else
		{
			$this->show_error("date2days", "Pear \"Date Package\" required.");
		}

		return Date_Calc::dateDiff($start_day,$start_month,$start_year,$end_day,$end_month,$end_year);
	}

	/**
	 * return file extension
	 *
	 * @param	string	$filename
	 * @return	string
	 */
	function fileExtension($filename)
	{
		$rc="";
		$max_length = strlen($filename);
		$counter=0;

		while($max_length>0)
		{
			$ch = $filename[$max_length];
			if ($ch == ".")
				break;
			$rc .= $ch;
			$max_length--;
			$counter++;
		}

		if ($rc!="") $rc = strrev($rc);

		return $rc;
	}

	/**
	 * convert string to number while converting old seperator with "."
	 *
	 * @param	string	$value		string to convert {default: ","}
	 * @param	string	$sep_old	old seperator
	 * @return	float
	 */
	function convToNum($value,$sep_old=",")
	{
	   $rc = "";
	   if (empty($sep_old))
	   {
			$this->show_error("convtoNum", "missing parameter sep_old");
	   }

	   $st      = trim($value);
	   $sep_new = ".";
	   if (!isvoid($st))
	   {
		  if ($this->checkNumber($st,$sep_old))
		  {
			 if ($sep_old != $sep_new)
			 {
				if ($this->checkNumber($st,$sep_old))
				{
				   $num_sep = substr_count($st,$sep_old);
				   if ($num_sep==1)
					  $rc = str_replace($sep_old,$sep_new,$st);
				   else
					  $rc = $st;
				}
			 }
			 else
				$rc = $st;
		  }
	   }
	   return $rc;
	}

	/**
	 * convert seperator in number and fill number to specified length
	 *
	 * @param	float	$st			number to convert
	 * @param	char	$sep_new	new seperator {default: "."}
	 * @param	int		$precision	numbers behind seperator  {default: 0 }
	 *								(filling with 0; this function doesn't cut or round numbers)
	 * @param	string	$group		if set, this group seperator will be used
	 * @return	string				converted string with number
	 */
	function convnumSep($st, $sep_new=".", $precision=0, $group=null)
	{
		$rc="";
		if (!isvoid($st))
		{
			if ($this->checkNumber($st))
			{
				$sep_old = ".";
				$num_sep = substr_count($st,$sep_old);
				if (($num_sep==0) && ($precision>0))	// no old seperator
				{
					$tmp_st = $st.$sep_new;
					for ($ix=0;$ix<$precision;$ix++)
					$tmp_st .= "0";
					$rc = $tmp_st;
				}
				else									// with old seperator
				{
					$ln     = strlen($st);
					$pos    = strrpos($st, $sep_old);
					$ln_ext = $ln-$pos-1;
					if ($ln_ext<$precision)
					{
						$ln_miss = $precision - $ln_ext;
						$tmp_st=$st;
						for ($ix=0;$ix<$ln_miss;$ix++)
							$tmp_st .= "0";

						$rc = str_replace($sep_old, $sep_new, $tmp_st);
					}
					else
					{
						$rc = str_replace($sep_old, $sep_new, $st);
					}
				}

				// fill group seperator, if defined
				if (isset($group))
				{
					if ($rc[0]=="-")	$g_rc = substr($rc, 1);
					else				$g_rc = $rc;
					$ln		= strlen($g_rc);	// get new length
					$pos    = strrpos($g_rc, $sep_new);
					if ($pos>3)
					{
						$suffix_ln	= $ln-$pos+1;
						$prefix_ln	= $ln-$suffix_ln+1;
						if ($prefix_ln>3)
						{
							$tmp_rc  = "";
							$prefix = strrev(substr($g_rc, 0, $prefix_ln));
							for ($ix=0; $ix<$prefix_ln; $ix++)
							{
								if (($ix>0) && ($ix%3==0))	$tmp_rc .= $group;
								$tmp_rc .= $prefix[$ix];
							}
							$g_rc = strrev($tmp_rc).substr($g_rc, $pos);
						}
					}
					if ($rc[0]=="-")	$rc = "-".$g_rc;
					else 				$rc = $g_rc;
				}
			}
		}
		else
			$rc = $st;

		return $rc;
	}

	/**
	 * validate time format and convert it into right format
	 *
	 * @param	string	$time	time (or date) to convert, if empty NOW will be used
	 * @param	char	$format	if empty or time, e.g. 19:45:59
	 *							datetime, e.g. 2002-07-20 21:02:55
	 * @return	string			converted time, if wrong input false
	 */
	function convToTimestamp($time="",$format="")
	{
	   $timestamp = "";

	   if ((isvoid($format)) || ($format=="time"))    // only time
	   {
		  if (isvoid($time)) $timestamp = strtotime("now");
		  else
		  {
			 $t_hours = strtok(trim($time),":");
			 $t_min   = strtok(":");
			 $t_sec   = strtok(":");
			 $t_day   = date("d");
			 $t_month = date("m");
			 $t_year  = date("Y");
			 $tmp_stamp = mktime($t_hours,$t_min,$t_sec,$t_month,$t_day,$t_year);
			 if ($tmp_stamp>0)
				$timestamp = $tmp_stamp;
		  }
	   }
	   if (($format=="datetime") && (!isvoid($time))) // datetime
	   {
		  $tdate = trim(strtok(trim($time)," "));
		  $ttime = trim(strtok(" "));

		  $t_year  = strtok($tdate,"-");
		  $t_month = strtok("-");
		  $t_day   = strtok("-");

		  $t_hours = strtok($ttime,":");
		  $t_min   = strtok(":");
		  $t_sec   = strtok(":");

		  $tmp_stamp = mktime($t_hours,$t_min,$t_sec,$t_month,$t_day,$t_year);
		  if ($tmp_stamp>0)
			 $timestamp = $tmp_stamp;
	   }
	   return $timestamp;
	}

	/**
	 * validate time format and convert it into right format
	 *
	 * @param	string	$time			time to convert
	 * @param	char	$time_format	see member variable $this->time_format above
	 * @param	bool	$with_seconds	if false, don't return seconds {default: true}
	 * @return	string					converted time, if wrong input false
	 */
	function convTime($time, $time_format=null, $with_seconds=true)
	{
	   $rc="";

	   if ((!isset($time_format)) || (empty($time_format)))	$time_format = $this->time_format;

	   $ln = strlen($time);
	   if ($ln == 5)
	   {
		  $part1 = strtok($time,":");
		  $part2 = strtok(":");
		  if ((strlen($part1)==2) && (strlen($part2)==2))
		  {
			 if (($part1>=0) && ($part1<25) && ($part2>=0) && ($part2<60))
			 {
				if ($time_format=="N") // 12 hours
				{
				   if ($part1>=12)
				   {
					  $part1 = $part1-12;
					  $end = "&nbsp;pm";
				   }
				   else
				   {
					  $part1 += 0;
					  $end = "&nbsp;am";
				   }
				   if ($part1==0)
					  $part1 = 12;
				   $rc = $part1 . ":" . $part2.$end;
				}
				else            // 24 hours
				{
				   $rc = $part1 . ":" . $part2;
				}
			 }
		  }
	   }
	   else if ($ln >= 8)
	   {
		  $part1 = strtok($time,":");
		  $part2 = strtok(":");
		  $part3 = strtok(":");
		  if (strlen($part3)>2) $part3 = substr($part3, 0, 2);

		  if ((strlen($part1)==2) && (strlen($part2)==2) && (strlen($part3)==2))
		  {
			 if (($part1>=0) && ($part1<25) && ($part2>=0) && ($part2<60) && ($part3>=0) && ($part3<60))
			 {
				if ($time_format=="N") // 12 hours
				{
				   if ($part1>12)
				   {
					  $part1 = $part1-12;
					  $end = "&nbsp;pm";
				   }
				   else
				   {
					  $part1 += 0;
					  $end = "&nbsp;am";
				   }
				   $rc = $part1 . ":" .$part2;
				   if ($with_seconds)	$rc .= ":".$part3;
				   $rc .= $end;
				}
				else            // 24 hours
				{
				   $rc = $part1 . ":" .$part2;
				   if ($with_seconds)	$rc .= ":".$part3;
				}
			 }
		  }
	   }

	   return $rc;
	}

	/**
	 * convert datetime format
	 *
	 * @param	string 	$datetime		db datetime field format
	 * @param	char	$date_format	see member variable m_date above
	 * @param	char	$time_format	see member variable m_time above
	 * @param	bool	$with_seconds	show seconds
	 * @param	bool	$short_mode		if today, yesterday or tomorrow
	 *									show string for date
	 * @param	string	$at
	 * @return	string					datetime in converted format
	 */
	function convDateTime($datetime, $date_format="", $time_format="", $with_seconds=false, $short_mode=true, $at=null)
	{
		$rc="";

		if (!isvoid($datetime))
		{
			if ((!isset($date_format)) || (empty($date_format)))	$date_format = $this->date_format;
			if ((!isset($time_format)) || (empty($time_format)))	$time_format = $this->time_format;

	   		$tmp_date		= strtok($datetime, " ");
			$tmp_time		= strtok(" ");

			if (($short_mode) && (!isvoid($tmp_time)))
				$rc = $this->convDate($tmp_date, 'S', $date_format, array('short_mode'=>true));
			else
				$rc = $this->convDate($tmp_date, 'S', $date_format);

			if (!isvoid($tmp_time))
			{
				if (isset($at))	$tmp_at = $at;
				else 			$tmp_at = STR_AT;
				if ($tmp_at!=",")	$rc .= "&nbsp;";
				$rc .= $tmp_at."&nbsp;";
				$rc .= $this->convTime($tmp_time, $time_format, $with_seconds);
			}
		}

		return $rc;
	}

	/**
	 * Convert date format
	 *
	 * @param	string	$date_str		date to convert
	 * @param	string	$src_format		see member variables $this->date_format above
	 * @param	string	$dest_format	see member variables $this->date_format above or 'short' or 'long'
	 * @param	array	$customize		year_format string = long : e.g. 2002 {default}
	 *											 			 short: e.g. 02
	 *													 	 void : e.g.
	 *						       		leading_zeros bool = true : e.g. 01 {default}
	 *													 	 false: e.g. 1
	 *									with_weekday bool	= true:  with weekday
	 *													  	  false: without weekdays
	 *																  (only available if dest_format is short or long)
	 *									locale string		= see member variables m_locale above
	 *									first_valid_year	= first valid year, if specified date is before, it will be signed
	 *													  	   as invalid {default: 1850}
	 *									short_mode			= if today, yesterday or tomorrow
	 *								 						  show string for date {default: false}
	 * @return	string					 date or {if wrong input) empty
	 */
	function convDate($date_str, $src_format, $dest_format, $customize=null)
	{
		$rc				= "";
		$sep_old		= "";
		$sep_new		= "";
		$date_package	= false;

		// all years are possible, but year 0 cannot be selected (does anyone need it?)
		if ((!isset($customize['first_valid_year'])) || (intval($customize['first_valid_year'])==0))
			$customize['first_valid_year'] = 1850;

		if ($this->file_exists("Date.php"))
		{
			require_once("Date.php");
			$date_package = true;
		}

		if ((isset($customize['locale'])) && (!setlocale(LC_TIME,$customize['locale'])))
		{
			$this->show_error("convDate", "locale not readable from system!");
		}

		if (strtoupper($src_format)=="L")					// DD.MM.YYYY
		{
			$sep_old = $this->m_sep_l;

			$day   = intval(strtok($date_str,$sep_old));
			$month = intval(strtok($sep_old));
			$year  = intval(strtok($sep_old));
			if ($year<20)		$year += 2000;
			else if ($year<100)	$year += 1900;
    	}
		else if (strtoupper($src_format)=="I")				// MM/DD/YYYY
		{
			$sep_old = $this->m_sep_i;

			$month = intval(strtok($date_str,$sep_old));
			$day   = intval(strtok($sep_old));
			$year  = intval(strtok($sep_old));
			if ($year<20)		$year += 2000;
			else if ($year<100)	$year += 1900;
		}
		else if (strtoupper($src_format)=="S")				// YYYY-MM-DD
		{
			$sep_old = $this->m_sep_s;

			$year  = intval(strtok($date_str,$sep_old));
			$month = intval(strtok($sep_old));
			$day   = intval(strtok($sep_old));
			if ($year<20)		$year += 2000;
			else if ($year<100)	$year += 1900;
		}
		else if (strtoupper($src_format)=="C")				// YYYYMMDD
		{
			if (strlen($date_str)==8)
			{
				$year  = substr($date_str,0,4);
				$month = substr($date_str,4,2);
				$day   = substr($date_str,6,2);
			}
			else
			{
				$year  = substr($date_str,0,2);
				$month = substr($date_str,2,2);
				$day   = substr($date_str,4,2);
			}
			if ($year<20)		$year += 2000;
			else if ($year<100)	$year += 1900;
		}

		if      (strtoupper($dest_format)=="I") $sep_new = $this->m_sep_i;
		else if (strtoupper($dest_format)=="S") $sep_new = $this->m_sep_s;
		else if (strtoupper($dest_format)=="L") $sep_new = $this->m_sep_l;
		else if (strtoupper($dest_format)=="C") $sep_new = "";
		else $sep_new = $this->m_sep_s;

		if ((checkdate($month,$day,$year)) && ($customize['first_valid_year']<=$year))
		{
			$short_mode = false;
			if ((isset($customize['short_mode'])) && ($customize['short_mode']))
			{
				if (date("Y-n-j")=="$year-$month-$day")
				{
					$rc = STR_TODAY;
					$short_mode=true;
				}
				else if (date("Y-n-j", mktime(date('H'),date('i'), 0,date('m'), date('d')+1,date('Y')))=="$year-$month-$day")
				{
					$rc = STR_TOMORROW;
					$short_mode=true;
				}
				else if (date("Y-n-j", mktime(date('H'),date('i'), 0,date('m'), date('d')-1,date('Y')))=="$year-$month-$day")
				{
					$rc = STR_YESTERDAY;
					$short_mode=true;
				}
			}

			if ($short_mode==false)
			{
				if (isset($customize['year_format']))
				{
					$y_form = $customize['year_format'];
					if ($y_form=="short")		$year = substr($year,strlen($year)-2,2);
					else if ($y_form=="void")	$year = "";
				}

				$day   = sprintf("%02d",$day);
				$month = sprintf("%02d",$month);

				if ((isset($customize['leading_zeros'])) && (!empty($sep_new)))
				{
					$l_zeros = $customize['leading_zeros'];
					if (!$l_zeros)
					{
						$day   = intval($day);
						$month = intval($month);
					}
				}
				else if (empty($sep_new))
				{
					if (strlen($day)<2)		$day   .= "0";
					if (strlen($month)<2)	$month .= "0";
				}

				if (($dest_format=="long") || ($dest_format=="short"))
				{
					if ($date_package)
					{
						$stamp = new Date();
						$stamp->setDate("$year-$month-$day",DATE_FORMAT_ISO);
					}

					$wday = "";
					if (isset($customize['with_weekday']))
					{
						$with_wday = $customize['with_weekday'];

						if ($with_wday)
						{
							if ($date_package)
							{
								if ($dest_format=="long")	$wday = $stamp->format("%A").", ";
								else						$wday = $stamp->format("%a").", ";
							}
							else
							{
								$this->show_error("convDate", "Pear \"Date Package\" required for specificed parameters.");
							}
						}
					}
					if ($date_package)
					{
						if ($dest_format=="long")	$month = $stamp->format("%B");
						else						$month = $stamp->format("%b");
					}
					else
					{
						$this->show_error("convDate", "Pear \"Date Package\" required for specificed parameters.");
					}

					if ($this->m_date=="I")	$rc = $wday.$month."/".$day."/".$year;
					else					$rc = $wday.$day.". ".$month." ".$year;
				}
				else
				{
					if ($sep_new==$this->m_sep_i)
					{
						$rc = $month.$this->m_sep_i.$day;
						if (!isvoid($year))
							$rc .= $this->m_sep_i.$year;
					}
					else if ($sep_new==$this->m_sep_s)
					{
						if (!isvoid($year))
							$rc .= $year.$this->m_sep_s;
						$rc .= $month.$this->m_sep_s.$day;
					}
					else if ($sep_new==$this->m_sep_l)
					{
						 $rc = $day.$this->m_sep_l.$month.$this->m_sep_l.$year;
					}
					else
					{
						 $rc = intval($year.$month.$day);
					}
				}
			} // !short mode end
		}

		return $rc;
	}

	/**
	 * Checks whether a file or directory exists (also checks the include_path in php.ini)
	 *
	 * @param	string	$filename
	 * @return	bool
	 */
	function file_exists($filename)
	{
		$os_type = $this->os_type(true);
		if ($os_type=="w")
		{
			if (substr($filename,1,3) == ":\\")
				return file_exists($filename);
			$sep	 = ";";
			$sep_dir = "\\";
		}
		else
		{
			if (substr($filename,0,1) == "/")
				return file_exists($filename);
			$sep     = ":";
			$sep_dir = "/";
		}

		$incpath = ini_get("include_path");
 		foreach (explode($sep,$incpath) as $path)
		{
   			if (file_exists($path.$sep_dir.$filename))
				return true;
 		}
 		return false;
	}

	/**
	 * validate number (positiv) with wildcards
	 *
	 * @param	string	$number		 number to validate
	 * @param	char	$sep		valid seperator {default: ","}
	 * @return	bool
	 */
	function checkNumWildcard($number, $sep=",")
	{
	   $needle = "/[^0-9\*".$sep."]/";
	   if (!preg_match($needle, trim($number)))
		  return true;
	   else
		  return false;
	}

	/**
	 * validate phone number
	 *
	 * @param	string	$phone	phone number to validate
	 * @param	bool	$mode
	 * @return	bool
	 */
	function checkPhone($phone, $mode=false)
	{
		if ($mode)	$needle = '/[^\sA-Za-z0-9\(\)\-\+\/]/';
		else		$needle = '/[^\s0-9\(\)\-\+\/]/';

		if (!preg_match($needle, $phone))
			return true;
		else
			return false;
	}

	/**
	 * validate post zip code
	 *
	 * @param	string	$zip	zip to validate
	 * @return	bool
	 */
	function checkZip($zip)
	{
		if (!preg_match('/[^\s\w\-]/', $zip))
			return true;
		else
			return false;
	}

	/**
	 * validate German Bankleitzahl
	 * ( allowed seperators: "-", "." or " ")
	 *
	 * @param	string	$bank_id	bank id (German BLZ) to validate
	 * @return	bool
	 */
	function checkBankId($bank_id)
	{
		$bank_id = str_replace("-", "", $bank_id);
		$bank_id = str_replace(".", "", $bank_id);
		$bank_id = str_replace(" ", "", $bank_id);

		if (strlen($bank_id)==8)	return true;
		else						return false;
	}

	/**
	 * Check birthday
	 *
	 * @param	date	$birthday
	 * @param	char	$date_format
	 * @return	bool
	 */
	function checkBirthday($birthday, $date_format="S")
	{
		$tmp_birthday	= $this->convDate($birthday, $date_format, 'S');
		$tmp_b			= $this->convDate($tmp_birthday, "S", "C");
		$tmp_now		= date("Ymd");

		if (!$tmp_birthday)			return false;
		else if ($tmp_b>$tmp_now)	return false;

		return true;
	}

	/**
	 * validate email address format
	 *
	 * @param	string	$email		email address
	 * @param	bool	$with_dns	true for dns check {default: false}
	 *								(only works on non-windows platforms)
	 * @return	bool
	 */
	function checkEmail($email, $with_dns=false)
	{
		$rc = false;

		$known_doms  = "ad|ae|aero|af|ag|ai|al|am|an|ao|aq|ar|arpa|as|at|au|aw|az|ba";
		$known_doms .= "|bb|bd|be|bf|bg|bh|bi|biz|bj|bm|bn|bo|br|bs|bt|bv|bw|by|bz|ca";
		$known_doms .= "|cc|cd|cf|cg|ch|ci|ck|cl|cm|cn|co|com|coop|cr|cs|cu|cv|cx|cy";
		$known_doms .= "|cz|de|dj|dk|dm|do|dz|ec|edu|ee|eg|eh|er|es|et|eu|fi|fj|fk|fm";
		$known_doms .= "|fo|fr|ga|gb|gd|ge|gf|gh|gi|gl|gm|gn|gov|gp|gq|gr|gs|gt|gu|gw";
		$known_doms .= "|gy|hk|hm|hn|hr|ht|hu|id|ie|il|in|info|int|io|iq|ir|is|it|jm";
		$known_doms .= "|jo|jobs|jp|ke|kg|kh|ki|km|kn|kp|kr|kw|ky|kz|la|lb|lc|li|lk|lr|ls";
		$known_doms .= "|lt|lu|lv|ly|ma|mc|md|mg|mh|mil|mk|ml|mm|mn|mo|mp|mq|mr|ms|mt";
		$known_doms .= "|mu|museum|mv|mw|mx|my|mz|na|name|nc|ne|net|nf|ng|ni|nl|no|np";
		$known_doms .= "|nr|nt|nu|nz|om|org|pa|pe|pf|pg|ph|pk|pl|pm|pn|pr|pro|ps|pt|pw";
		$known_doms .= "|py|qa|re|ro|ru|rw|sa|sb|sc|sd|se|sg|sh|si|sj|sk|sl|sm|sn|so";
		$known_doms .= "|sr|st|su|sv|sy|sz|tc|td|tf|tg|th|tj|tk|tm|tn|to|tp|tr|travel|tt|tv";
		$known_doms .= "|tw|tz|ua|ug|uk|um|us|uy|uz|va|vc|ve|vg|vi|vn|vu|wf|ws|ye|yt";
		$known_doms .= "|yu|za|zm|zw";

		$email = trim($email);
		if (preg_match("/^[-_.[:alnum:]]+@((([[:alnum:]]|[[:alnum:]][[:alnum:]-]*[[:alnum:]])\.)+($known_doms)$|(([0-9][0-9]?|[0-1][0-9][0-9]|[2][0-4][0-9]|[2][5][0-5])\.){3}([0-9][0-9]?|[0-1][0-9][0-9]|[2][0-4][0-9]|[2][5][0-5]))$/i", $email))
		{
			if (($with_dns) && ($this->os_type(true)!="w"))
			{
				$host = explode('@', $email);
				// Check for MX record
				if( checkdnsrr($host[1], 'MX') )    $rc = true;
				// Check for A record
				if( checkdnsrr($host[1], 'A') )     $rc = true;
				// Check for CNAME record
				if( checkdnsrr($host[1], 'CNAME') ) $rc = true;
			 }
			 else
				$rc = true;
		}
		return $rc;
	}

	/**
	 * validate domain name
	 *
	 * @param	string	$domain	domain name
	 * @param	string	$tld	top level domain
	 * @return	bool
	 */
	function checkDomainName($domain, $tld=null)
	{
		$rc = false;

		$len = strlen($domain);
		$dot_count = substr_count($domain, ".");

		if (($dot_count>0) &&
			($len>1) &&
			($len<=(63*$dot_count)) &&
			(!preg_match('/[^a-z0-9\._\-]/i', $domain)))
		{
			if (isset($tld))
			{
				if (substr($domain, ($len - strlen($tld) - 1)==$tld))
					$rc = true;
			}
			else
				$rc = true;
		}

		return $rc;
	}

	/**
	 * validate IP address
	 *
	 * @param	string		$addr	IP address to validate
	 * @return	bool				true if valid
	 */
	function checkIP($addr)
	{
		$rc = false;
		if ((strlen($addr)<16) && (!preg_match('/[^0-9\.]/', $addr)))
		{
			$ip = explode(".", $addr);
			if (count($ip)==4)
			{
				$rc = true;
				for ($i=0;$i<4;$i++)
				{
					if ((strlen($ip[$i])==0) ||
							($ip[$i]<0)      ||
							($ip[$i]>255))
					{
						$rc = false;
						break;
					}
				}
			}
		}
		return $rc;
	}

	/**
	 * validate MAC address
	 *
	 * @param	string	$addr	MAC address to validate
	 * @return	bool
	 */
	function checkMAC($addr)
	{
		$rc = false;
		if ((strlen($addr)==17) && (!preg_match('/[^A-Fa-f0-9\:]/',$addr)))
		{
			$mac = explode(":", $addr);
			if (count($mac)==6)
			{
				$rc = true;
				for ($i=0;$i<=5;$i++)
				{
					if ((strlen($mac[$i])!=2)		||
							(hexdec($mac[$i])<0)	||
							(hexdec($mac[$i]>255)))
					{
						$rc = false;
						break;
					}
				}
			}
		}
		return $rc;
	}

	/**
	 * check if a string has HTML tags
	 *
	 * @param	string	$str	string to validate
	 * @return	bool			 true if string has html tags
	 */
	function checkHtmlTag($str)
	{
		if (!preg_match('/[<]/',trim($str)))
			return true;
		else
			return false;
	}

	/**
	 * check if string is a valid color value for HTML and CSS
	 *
	 * @param	string	$str	string to validate
	 * @return	bool
	 */
	function checkColor($str)
	{
		$rc = false;

		$colors = array("aqua", "black", "blue", "fuchsia", "gray", "green", "lime",
					"maroon", "navy", "olive", "purple", "red", "silver", "teal", "white", "yellow");

		if (in_array($str, $colors))
			$rc = true;
		else if ((substr($str,0,1)=="#") && (strlen($str)<8))
		{
			if (!preg_match('/[^A-Fa-f0-9]/',substr($str,1)))
				$rc = true;
		}
		return $rc;
	}

	/**
	 * is string a valid number?
	 *
	 * @param	string	$number		number to validate
	 * @param	char	$sep		valid seperator {default: "."}
	 * @param	bool	$mode		if true, with negative numbers {default: true}
	 * @param	bool	$with_null	if true, number "0" is valid {default: true}
	 * @return	bool
	 */
	function checkNumber($number, $sep=".", $mode=true, $with_null=true)
	{
	   $rc  = false;
	   $num = trim($number);
	   if (!isvoid($sep))
	   {
		  $num_sep = substr_count($num, $sep);
		  if ($num_sep < 2)
		  {
			 if ($mode)
				$simple = "/[^0-9\-\\$sep]/";
			 else
				$simple = "/[^0-9\\$sep]/";
			 if (!preg_match($simple, $num))
			 {
				$pos = strpos($num, '-');
				if ($pos===false)	$rc = true;
				else if ($pos==0)	$rc = true;
			 }
		  }
		  else if (($mode) && (!preg_match('/[^0-9\-]/',$num))) $rc = true;
		  else if (!preg_match('/[^0-9]/',$num)) $rc = true;
	   }
	   else // only integer
	   {
		  if (($mode) && (!preg_match('/[^0-9\-]/',$num))) $rc = true;
		  else if (!preg_match('/[^0-9]/',$num)) $rc = true;
	   }

	   if ((!$with_null) && ($rc) && ($num=="0"))
		$rc = false;

	   return $rc;
	}

	/**
	 * validate user name
	 * (only alpha-numeric, '_' and '-' (but not as first sign,
	 * because some systems has problems with this) are allowed)
	 *
	 * @param	string	$user_name			user to validate
	 * @param	int		$min_length			minimal length of username {default: 3}
	 * @param	bool	$allow_upper_case	true	: case insensitive	{default: true}
	 *										false	: upper letters are not allowed
	 * @return	bool
	 */
	function checkUserName($user_name, $min_length=3, $allow_upper_case=true)
	{
		$rc		= false;
		$str	= trim($user_name);
		if ((strlen($str) >= $min_length) && ($str[0]!='-'))
		{
			if (!$allow_upper_case)	$val='/[^a-z0-9\_\-]/';
			else					$val='/[^A-Za-z0-9\_\-]/';

			if (!preg_match($val, $str))	$rc = true;
		}
		return $rc;
	}

	/**
	 * checks version of php and if pear is installed
	 *
	 * @param	string	$min_ver	oldest php version, which is allowed
	 * @param	bool	$with_pear	true: if pear must installed {default: false}
	 * @param	bool	$mode		true: die with message {default: false}
	 * @return	bool
	 */
	function check_php_version($min_ver, $with_pear=false, $mode=false)
	{
		$rc = false;

		$pear_installed = false;
		$curr_php = phpversion();
		$c_p1 = strtok($curr_php,'.');
		$c_p2 = strtok('.');
		$c_p3 = strtok('.');

		$m_p1 = strtok($min_ver,'.');
		$m_p2 = strtok('.');
		$m_p3 = strtok('.');

		if ($c_p1 > $m_p1) $rc=true;
		else if ($c_p1==$m_p1)
		{
			if ($c_p2 > $m_p2) $rc=true;
			else if ($c_p2==$m_p2)
			{
				// if c_p3 is no number (because it is a dev version, take 0)
				if (!cphplib::checkNumber($c_p3,""))
					$c_p3=0;
				if ($c_p3 >= $m_p3) $rc=true;
			}
		}

		// pear only works with php >= 4
		if (($with_pear) && ($c_p1>=4))
		{
			@ include_once("PEAR.php");
			$pear_installed = class_exists("PEAR");
		}

		if ($mode)
		{
			if (!$rc)
			{
				$this->show_error("check_php_version", "version >= $min_ver required (installed php version is $curr_php).");
			}
			else if (($with_pear) && (!$pear_installed))
			{
				$this->show_error("check_php_version", "php pear is missing (installed php version is $curr_php).");
			}
		}
		else
		{
			if (($with_pear) && (!$pear_installed))
				$rc = false;
		}

		return $rc;
	}

	/**
	 * validate password format
	 *
	 * @param	string		$password		password string
	 * @param	int			$min_length		minimal length of password {default: 5}
	 * @param	int			$strength		0: no special rules	{default}
	 *										1: at least one number and one char
	 *										2: same as 1 and at least one lowercase and and uppercase letter
	 *										3: same as 2 and at least one special character
	 * @return	bool						true for qualified password
	 */
	function checkPassword($password, $min_length=5, $strength=0)
	{
		$rc = false;
		$password = trim($password);

		$special	= "\.\,;\:\!\@\#\%\&\*\-\_\=\(\)\+§<>\$";
		$allowed	= "/[^[:alnum:]".$special."]/i";

		if ((strlen($password) >= $min_length) && (!preg_match($allowed, $password)))
		{
			switch($strength)
			{
				case 1:		// at least one number and one char
					if ((preg_match('/[A-Za-z]/i', $password)) &&
						(preg_match('/[0-9]/', $password)))
					   $rc = true;
					break;
				case 2:		// same as 1 and at least one lowercase and and uppercase letter
					if ((preg_match('/[A-Z]/', $password)) &&
						(preg_match('/[a-z]/', $password)) &&
						(preg_match('/[0-9]/', $password)))
					   $rc = true;
					break;
				case 3:		// same as 2 and at least one special character
					if ((preg_match('/[A-Z]/', $password)) &&
						(preg_match('/[a-z]/', $password)) &&
						(preg_match('/[0-9]/', $password)) &&
						(preg_match('/['.$special.']/', $password)))
					   $rc = true;
					break;
				default:	// no special rules
					$rc = true;
			}
		}

		return $rc;
	}

	/**
	 * validate string with letters only
	 *
	 * @param	string	$str	tring to validate
	 * @param	bool	$mode	true for German Umlaute include {default: false}
	 * @return	bool
	 */
	function checkString($str,$mode=false)
	{
	   $rc = false;
	   $str = trim($str);
	   if ($mode==true)
	   {
		  if (!preg_match('/[^A-Za-zöüäßÄÜÖ]/',$str))
			 $rc = true;
	   }
	   else
	   {
		  if (!preg_match('/[^A-Za-z]/',$str))
			 $rc = true;
	   }
	   return $rc;
	}

	/**
	 * convert German Umlaute to HTML and vice versa
	 *
	 * @param	string	$str	string to convert
	 * @param	bool	$mode	true:	convert to HTML
	 *							false:	from HTML
	 * @return	string
	 */
	function convUmlaute($str, $mode=true)
	{
		if ($mode)
		{
			$str = str_replace("ä", "&auml;",	$str);
			$str = str_replace("ö", "&ouml;",	$str);
			$str = str_replace("ü", "&uuml;",	$str);
			$str = str_replace("Ä", "&Auml;",	$str);
			$str = str_replace("Ö", "&Ouml;",	$str);
			$str = str_replace("Ü", "&Uuml;",	$str);
			$str = str_replace("ß", "&szlig;",	$str);
		}
		else
		{
			$str = str_replace("&auml;", "ä",	$str);
			$str = str_replace("&ouml;", "ö",	$str);
			$str = str_replace("&uuml;", "ü",	$str);
			$str = str_replace("&Auml;", "Ä",	$str);
			$str = str_replace("&Ouml;", "Ö",	$str);
			$str = str_replace("&Uuml;", "Ü",	$str);
			$str = str_replace("&szlig;", "ß",	$str);
		}
		return $str;
	}

	/**
	 * Envelops a string with quotes (for db INSERT,UPDATES or SELECT)
	 *
	 * @param	string	$str				string to convert
	 * @param	bool	$conv_special_char	convert special charactar to html convention
	 *										(great helper to secure SQL strings) {default: false}
	 * 										(conv_special_char is not recomended for an INSERT or UPDATE SQL string)
	 * @param	int		$active_null		0: deactive NULL value
	 *										1: active NULL value                     {default}
	 *										2: active NULL value, with 0 is invalid
	 * @return	string							 converted string
	 */
	function sql_value($str,$conv_special_char=false, $active_null=1)
	{
		if (!isvoid($str)) $str = trim($str);

		if ((($active_null==1) && (!isvoid($str))) ||
			(($active_null==2) && (!empty($str))))
		{
			if ($conv_special_char)
			{
				$str = $this->convHtml($str);
				$str = $this->convUmlaute($str, false);
			}
			return "'" . addslashes($str) . "'";
		}
		else if ($active_null>0)	return "NULL";
		else						return "''";
	}

	/**
	 * convert ascii string to HTML string
	 * (because of a bug, it is better to set magic_quotes_gpc=Off in your php.ini)
	 *
	 * @param	string	$str		string to convert
	 * @param	bool	$mode		true : html->ascii
	 *								false: ascii->html {default}
	 * @param	bool	$with_trim	true : cut off whitespaces before and after the string {default}
	 *								false: leave string with whitespaces
	 * @param	bool	$with_link	true : allow HTML links
	 *									   (you have to use [url=http://mydomain.com/]thebest[/url] syntax or
     *			                           [mail=me@mydomain.com]Me[/mail]
     *									   (at the moment it works only in ascii->html))
     *								false: disallow HTML links {default}
	 * @return	string
	 */
	function convHtml($str, $mode=false, $with_trim=true, $with_link=false)
	{
		if ($with_trim)	$str = trim($str);

		if (!$mode) // ascii -> html
		{
			if ((!isvoid($str)) && ($str != '*'))
			{
				if (ini_get("magic_quotes_gpc")!=1) // if "magic_quotes_gpc=off"
				{
					$str = str_replace("\\", "\\\\", $str);
					$str = stripslashes($str);
				}

				if (defined(STR_CHARACTER_SET))	$charset = STR_CHARACTER_SET;
				else 							$charset = "iso-8859-1";

				if (defined(STR_CHARACTER_SET))	$rc = htmlentities($str, ENT_QUOTES, $charset);
				else 							$rc = htmlentities($str, ENT_QUOTES);
			}
			else
				$rc = $str;

			if ($with_link)
			{
				// Set up the parameters for a URL search string
				$URLSearchString = " a-zA-Z0-9\:\/\-\?\&\.\=\_\~\#\'";
				// Set up the parameters for a MAIL search string
				$MAILSearchString = $URLSearchString . " a-zA-Z0-9\.@";

				// Perform URL Search
				$rc = preg_replace("/\[url\]([$URLSearchString]*)\[\/url\]/", '<a href="$1" target="_blank">$1</a>', $rc);
				$rc = preg_replace("(\[url\=([$URLSearchString]*)\](.*?)\[/url\])", '<a href="$1" target="_blank">$2</a>', $rc);

				// Perform same Window URL Search
				$rc = preg_replace("/\[surl\]([$URLSearchString]*)\[\/surl\]/", '<a href="$1">$1</a>', $rc);
				$rc = preg_replace("(\[surl\=([$URLSearchString]*)\](.*?)\[/surl\])", '<a href="$1">$2</a>', $rc);

				// Perform MAIL Search
				$rc = preg_replace("(\[mail\]([$MAILSearchString]*)\[/mail\])", '<a href="mailto:$1">$1</a>', $rc);
				$rc = preg_replace("(\[mail\=([$MAILSearchString]*)\](.*?)\[/mail\])", '<a href="mailto:$1">$2</a>', $rc);

				// Check for bold text
				$rc = preg_replace("(\[b\](.+?)\[\/b])is",'<span style="font-weight: bold;">$1</span>', $rc);

				// Check for italic text
				$rc = preg_replace("(\[i\](.+?)\[\/i\])is",'<span style="font-style: italic;">$1</span>', $rc);
			}
		}
		else              // html -> ascii
		{
			$trans = get_html_translation_table(HTML_ENTITIES);
			$trans = array_flip($trans);
			$rc    = strtr($str, $trans);
			$rc    = str_replace("&euro;","EUR",$rc);

			if (!$with_link)	// show url text, if available
			{
				$start_tag_1       = "[url=\"";
				$start_tag_1_count = substr_count($rc, $start_tag_1);
				$start_tag_2       = "\"]";
				$start_tag_2_count = substr_count($rc, $start_tag_2);
				$end_tag           = "[/url]";
				$end_tag_count     = substr_count($rc, $end_tag);

				if (($start_tag_1_count>0) &&
					($start_tag_1_count==$end_tag_count) &&
					($start_tag_2_count==$end_tag_count)) // clear links only, if we have the same sum of start and end tags
				{
					$rc = str_replace($end_tag,"", $rc);

					$pos1 = strpos($rc,$start_tag_1);
					while($pos1)
					{
						$pos2 = strpos($rc,$start_tag_2);

						$part1 = substr($rc,0,$pos1);
						$part2 = substr($rc,($pos2+strlen($start_tag_2)));

						$rc = $part1.$part2;

						$pos1 = strpos($rc,$start_tag_1);
					}
				}
			}
		}
		return $rc;
	}

	/**
	 * convert first letter to upper or lower
	 *
	 * @param	string	$str	string to convert
	 * @param	bool	$mode	true for upper, false for lower
	 * @return	string
	 */
	function convFirstLetter($str,$mode=false)
	{
		if (!$mode)
			return substr(strtolower($str),0,1).substr($str,1,(strlen($str)-1));
		else
			return substr(strtouppter($str),0,1).substr($str,1,(strlen($str)-1));
	}

	/**
	 * Return end tag for formular objects
	 * attending xhtml settings ($this->xhtml)
	 *
	 * @return	string
	 */
	function end_tag()
	{
		 if ($this->xhtml)	return " />";
		 else 				return ">";
	}

	/**
	 * prints a meta description and meta keywords line for xhtml
	 * Required    : every line in filename is a keyword, first line is the description
	 *               (first line with quotes!!!)
	 *
	 * @param	string	$filename	filename with keywords
	 * @param	bool	$mode		true: print it as meta tags {default}
	 *								false: return array with text and keywords
	 * @return	mixed				if mode is false, array with text and keywords
	 */
	function metatag_write($filename, $mode=true)
	{
		$rc = array();

		if (file_exists($filename))
		{
			$close_sign = $this->end_tag()."\n";

			$fcontents = file($filename);
			$meta = array();
			while (list ($line_num, $line) = each ($fcontents))
			{
				if ($line_num==0)
				{
					$ver_info  = strstr ($line, 'INFO');
					if (!empty($ver_info))
					{
						$ver_info = trim(substr($ver_info,6));
						$ver_len  = strlen($ver_info)-1;

						$rc['text'] = substr($ver_info,0,$ver_len);

						if (($mode) && (!isvoid($rc['text'])))
						{
							print "\t<meta name=\"description\" content=\"".$rc['text']."\"".$close_sign;
							print "\t<meta name=\"DC.Description\" content=\"".$rc['text']."\"".$close_sign;
						}
					}
				}
				else
				{
					$line = trim($line);
					if (!isvoid($line))
						$meta[] = $line;
				}
			}

			if (count($meta))
			{
				$count=0;
				$rc['keywords'] = "";
				while(list(, $values)=each($meta))
				{
					$count++;
					if ($count==1)		$rc['keywords'] .= $values;
					else				$rc['keywords'] .= ",".$values;
				}

				if ($mode)
				{
					print "\t<meta name=\"keywords\" content=\"".$rc['keywords']."\"".$close_sign;
				}
			}
		}

		if (!$mode)	return $rc;
		else 		return "";
	}

	/**
	 * hit counter for web pages
	 * Required    : every line in filename is a keyword, first line is the description
     *               (first line with quotes!!!)
	 *
	 * @param	string	$id			id string
	 * @param	bool	$r			true:  reload allowed
	 *								false: reload disallowed {default}
	 * @param	bool	$db_open	true:  database connection aready open
	 *								false: no database connection open, open one
	 * @param	array	$CONF		CONF database array, if empty use counterconfig.php
	 *								(have to be in same directory as cphplib.inc)
	 * @return	int
	 */
	function hitcounter($id, $r=false, $db_open=false, $CONF=null)
	{
		if (empty($id))
		{
			$this->show_error("hitcounter", "missing id string.");
		}

		$server_domain = $_SERVER['SERVER_NAME'];

	 	if ($CONF['storage_type']!="files") // db
	 	{
			if (!$db_open)
			{
				if (!is_array($CONF))
					require_once("hitconfig.php");

				$this->db =& $this->db_connect($CONF['db']);
			}

		 	if (empty($this->db))
		 	{
		 		$this->show_error("hitcounter", "PEAR database object missing");
		 	}
	 	}
		else if (!is_array($CONF)) // if storage is files, always open hitconfig.php, if no CONF array is given
			require_once("hitconfig.php");

	 	if (!is_array($CONF))
	 	{
	 		$this->show_error("hitcounter", "no CONF array found.");
	 	}

	 	$file_count = $CONF['storage_dir']."/hitcounter_".$id.".count";
	 	$file_id    = $CONF['storage_dir']."/hitcounter_".$id.".id";

	 	$last_num = 0;
		$rc_num   = 0;
		$current_id_string = $this->get_id_string(false);

		////////////////////////////////////////////////////////////////////////////
		// check if counter is allowed in current script
		if (!$this->net_access($CONF['allowed_domains'], $CONF['allowed_ips'], "server"))
		{
			die(STR_ACCESS_DENIED);
		}

	 	if ($CONF['storage_type']!="files") // db
	 	{
			// get old number
			$sqlstr  = "SELECT hits,id_string FROM ".$CONF['db']['table']." WHERE domain=".$this->sql_value($server_domain);
			$sqlstr .= " AND counter_name=".$this->sql_value($id);
			$row = $this->db->getRow($sqlstr);
			if (DB::isError($row))
			{
				$this->show_error("hitcounter", $row->getMessage());
			}

			if (is_array($row))
			{
				$last_num       = $row[0];
				$last_id_string = $row[1];
			}
	 	}
	 	else if (file_exists($file_count)) // files
	 	{
			$fd       = fopen($file_count,"r+" );
			$last_num = intval(fread($fd,filesize($file_count)));
			fclose($fd);

			if (file_exists($file_id)) // no get last_id_string, if file exits
	 		{
				$fd             = fopen($file_id,"r+");
				$last_id_string = fread($fd,filesize($file_id));
				fclose($fd);
			 }
 		}

		if ($last_num>0)
		{
		    ////////////////////////////////////////////////////////////////////////////
			// check if visistor should be ignored
			$ignore_hit = $this->net_access($CONF['ignored_domains'], $CONF['ignored_ips']);

			if ((!$ignore_hit) && (($last_id_string!=$current_id_string) || ($r)) )
			{
				$rc_num = $last_num+1;

  				if ($CONF['storage_type']=="files")
  				{
					$fd = fopen($file_count,"w+");
					fwrite($fd,$rc_num);
					fclose($fd);

					$fd = fopen($file_id,"w+");
					fwrite($fd,$current_id_string);
					fclose($fd);
	  			}
  				else
  				{
					$sqlstr  = "UPDATE ".$CONF['db']['table']." SET hits=$rc_num";
					$sqlstr .= ",id_string=".$this->sql_value($current_id_string);
					$sqlstr .= "WHERE domain=".$this->sql_value($server_domain);
					$sqlstr .= " AND counter_name=".$this->sql_value($id);

					$dbi = $this->db->query($sqlstr);
					if (DB::isError($dbi))
					{
						$this->show_error("hitcounter", $dbi->getMessage());
					}
  				}
			}
			else
			{
				$rc_num = $last_num;
			}
		}
		else // start with 1
		{
		 	$rc_num = 1;
  			if ($CONF['storage_type']=="files")
  			{
  				touch($file_count);
  				touch($file_id);
				chmod($file_count,0664);
				chmod($file_count,0664);

				$fd = fopen($file_count,"w+");
				fwrite($fd,$rc_num);
				fclose($fd);

				$fd = fopen($file_id,"w+");
				fwrite($fd,$current_id_string);
				fclose($fd);
  			}
  			else
  			{
				$sqlstr  = "INSERT INTO ".$CONF['db']['table']."(domain,counter_name,hits,id_string) ";
		 		$sqlstr .= "VALUES (".$this->sql_value($server_domain).",".$this->sql_value($id).",";
		 		$sqlstr .= $rc_num.",".$this->sql_value($current_id_string).")";
		 		$dbi = $this->db->query($sqlstr);
				if (DB::isError($dbi))
				{
					$this->show_error("hitcounter", $dbi->getMessage());
				}
  			}
		}

		return $rc_num;
	}

  	/**
	 * Returns type of browser
	 *
	 * @param	bool	$details	true	= return array with browser type and
	 *										  gecko engine version (only available for Mozilla based browsers)
	 *								 		  browser_type  = browser type
	 *								 		  gecko_version = gecko version integer {default: 0}
	 * 						 		false	= return letter for browser type {default: false}
	 * @return	char				array if details is true, or char:
  	 *								m = Mozilla (and all clones)
  	 *								i = Internet Explorer
  	 *								n = Netscape
  	 *								o = Opera
  	 *								k = Konqueror
  	 *								"" = unknown
	 */
	function browser_type($details=false)
	{

		$rc = "";

		$agent = strtolower($_SERVER['HTTP_USER_AGENT']);

		if      (substr_count($agent,"opera")>0)		$rc = "o";
		else if (substr_count($agent,"konqueror")>0)	$rc = "k";
		else if (substr_count($agent,"gecko")>0)		$rc = "m";
		else if (substr_count($agent,"msie")>0)			$rc = "i";
		else if (substr_count($agent,"mozilla")>0)		$rc = "n";
		if ($details)
		{
			$rc_array = array();
			$rc_array['browser_type']	= $rc;
			$needle = "gecko/";
			if (($rc=="m") && (substr_count($agent,$needle)>0))
			{
				$gecko = trim(substr($agent,(strpos($agent, $needle))+6));

	 			$ln = strlen($gecko);
	 			if ($ln>0)
	 			{
	 				$tmp_gecko = "";
	 				for($i=0;$i<$ln;$i++)
	 				{
	 					$chr = $gecko[$i];
	 					if (is_numeric($chr))	$tmp_gecko .= $chr;
	 					else					break;
	 				}
	 				$gecko = $tmp_gecko;
	 			}
				$rc_array['gecko_version']	= intval($gecko);
			}
			else
			{
				$rc_array['gecko_version']	= 0;
			}
			return $rc_array;
		}
		else
		{
			return $rc;
		}
	}

	/**
	 * returns type of operating system
	 *
	 * @param	bool	$mode	true for server os,
	 *							false for client os {default: false}
	 * @return	char	w = Windows
	 *					m = Mac
	 *					l = Linux
	 *					u = Unix
	 *					o = OS/2
	 *					"" = unknown
	 */
	function os_type($mode=false)
	{
		$rc="";
		if ($mode)
		{
			$agent = strtolower(PHP_OS);

			if      ((substr_count($agent,"win32")>0) ||
					 (substr_count($agent,"winnt")>0) ||
					 (substr_count($agent,"windows")>0)) $rc = "w";
			else if (substr_count($agent,"macos")>0) $rc = "m";
			else if (substr_count($agent,"linux")>0) $rc = "l";
			else if (substr_count($agent,"unix")>0)  $rc = "u";
			else if (substr_count($agent,"os/2")>0)  $rc = "o";

			if (isvoid($rc))
			{
				if	(substr_count($_SERVER['WINDIR'],":\\")>0)
				{
					$rc = "w";
				}
			}
		}
		else
		{
			$agent = strtolower($_SERVER['HTTP_USER_AGENT']);

			if      (substr_count($agent,"win")>0)   $rc = "w";
			else if (substr_count($agent,"mac")>0)	 $rc = "m";
			else if (substr_count($agent,"linux")>0) $rc = "l";
			else if (substr_count($agent,"unix")>0)  $rc = "u";
			else if (substr_count($agent,"os/2")>0)  $rc = "o";
		}
		return $rc;
	}

	/**
	 * calculates CSS font sizes. This is a small solution, that the appearance looks similar
	 * with different browser types and operating systems
	 *
	 * @param	char	$os_type		type of operating system {default: 'user agent os'}
	 * @param	char	$browser_type	type of browser          {default: 'user agent os'}
	 * @return	array					array with css font sizes
	 */
	function get_fontsizes($os_type="",$browser_type="")
	{
		if (isvoid($os_type))      $os_type      = $this->os_type();
		if (isvoid($browser_type)) $browser_type = $this->browser_type();

		$fdata = array();

		// font_size_smallest
		if (($os_type == "w") && ($browser_type=="o"))
			$fdata['font_size_smallest'] = "8px";
		else if (($os_type == "m") && ($browser_type!="i") && ($browser_type!="m"))
			$fdata['font_size_smallest'] = "x-small";
		else
			$fdata['font_size_smallest'] = "xx-small";

		// font_size_small
		if (($os_type == "w") && ($browser_type=="i"))
			$fdata['font_size_small'] = "75%";
		else if (($os_type == "w") && ($browser_type=="o"))
			$fdata['font_size_small'] = "xx-small";
		else if (($os_type == "m") && ($browser_type!="i")&& ($browser_type!="m"))
			$fdata['font_size_small'] = "small";
		else if (($os_type != "w") && ($browser_type=="o"))
			$fdata['font_size_small'] = "95%";
		else if ($browser_type=="k")
			$fdata['font_size_small'] = "xx-small";
		else
			$fdata['font_size_small'] = "x-small";

		// font_size
		if (($os_type == "m") && ($browser_type!="i") && ($browser_type!="m"))
			$fdata['font_size'] = "medium";
		else if ((($os_type == "w") && ($browser_type=="i")) || ($browser_type=="o"))
			$fdata['font_size'] = "x-small";
		else if ($browser_type=="k")
			$fdata['font_size'] = "x-small";
		else
			$fdata['font_size'] = "small";

		// font_size_big
		if ((($os_type == "w") && ($browser_type=="i")) || ($browser_type=="o"))
			$fdata['font_size_big'] = "medium";
		else
			$fdata['font_size_big'] = "large";

		// font_size_biggest
		if ((($os_type == "w") && ($browser_type=="i")) || ($browser_type=="o"))
			$fdata['font_size_biggest'] = "large";
		else
			$fdata['font_size_biggest'] = "x-large";

		return $fdata;
	}

	/**
	 * checks if specified url is on script server
	 * (on the host where your scripts are)
	 *
	 * @param	string	$url
	 * @return	bool			true if it is on host
	 */
	function url_on_scripthost($url)
	{
		$rc = false;

		$tmp_array    = parse_url($url);

		if ((!isset($tmp_array['host'])) || ($tmp_array['host'] == $_SERVER['HTTP_HOST']))
			$rc = true;

		return $rc;
  	}

	/**
	 * Return cross sum of a number
	 *
	 * @param	int	$number
	 * @return	int
	 */
	function cross_sum($number)
	{
		$rc = 0;
		$i_max  = strlen($number);
		for ($i=0;$i<$i_max;$i++)
			$rc = $rc+$number[$i];
		return $rc;
	}

	/**
	 * checks for protocol, if missing http will be used
	 *
	 * @param	string	$url
	 * @return	string
	 */
	function convUrl($url)
	{
		if (substr_count($url,"://")>0)
			$rc = $url;
		else
			$rc = "http://".$url;

		if (substr_count($rc,".")<2)
		{
			$pos = strpos($rc, ":");
			$rc = substr_replace($rc,"www.",$pos+3,0);
		}
		return $rc;
	}

	/**
	 * counts days till specified day
 	 *
	 * @param	date	$enddate
	 * @return	int					days till specified date
	 */
	function countdown($enddate)
	{
		$ln = strlen($enddate);
		$ix = 0;
		$jahr=$monat=$tag="";
		while ($ix < $ln)
		{
			$ch = $enddate[$ix];
			$ix++;
			if ($ix<= 4)
			{
				$jahr=$jahr . $ch;
			}
			else if (($ix> 5) && ($ix<8))
			{
				$monat=$monat . $ch;
			}
			else if ($ix> 8)
			{
				$tag=$tag . $ch;
			}
		}
		$date1 = mktime(0, 0, 0, $monat,$tag,$jahr);
		$date2 = strtotime ("now");

		if ($date1 > $date2)
		{
			$rc = ($date1 - $date2) / 86400; // 24 * 60 *60
		}
		else if ($date1 < $date2)
		{
			$rc = ($date2 - $date1) / 86400; // 24 * 60 *60
		}
		else
		{
			$rc = 0;
		}

		return ceil($rc);
	}

	/**
	 * computes the exact age (considering leap years!) of the person born on
	 * the specified birthday measured from today or a specified date
	 *
	 * @param string	$date			Date on which the person was born
	 * @param string	$thisdate		Date from which the age is measured (default: today)
	 * @param char		$date_format	Format of the date and thisdate strings (default: "S"cience format)
	 * @param bool		$fixed			if true, age of current year will be returned {default: false}
	 * @return int						age of person, returns "" if the person is not born yet or the date is wrong!
	 *									(returns "born"-string, if the age is exactly 0)
	 */
	function age_from_birthday($date, $thisdate="", $date_format="S", $fixed=false)
	{
		$date  = $this->convDate($date, $date_format,"S");
		$year  = intval(strtok($date,"-"));
		$month = intval(strtok("-"));
		$day   = intval(strtok("-"));

		if ($thisdate=="")
		{
			$thisyear  = date("Y");
			$thismonth = date("n");
			$thisday   = date("j");
		}
		else
		{
			$thisdate  = $this->convDate($thisdate, $date_format, "S");

			$thisyear  = intval(strtok($thisdate,"-"));
			$thismonth = intval(strtok("-"));
			$thisday   = intval(strtok("-"));
		}

		$age = $thisyear-$year;

		if (!$fixed)
		{
			if (($thismonth<$month) ||
		 		(($thismonth==$month)&&($thisday<$day)))
				$age = $age-1;
		}

		if (($age<0)||(!checkdate($month,$day,$year)))
			$age = "";

		return $age;
	}

	/**
	 * generate rand string/number with specified length
	 *
	 * @param	int		$length	length of chars/numbers for return
	 * @param	string	$values	string with all allowed characters/numbers
	 * @return 	string			generated rand string, on error ""
	 */
	function rand_value($length=1, $values="")
	{
		$rc = "";
		if (isvoid($values))
		{
			$values = "1234567890ABCDEFGHIJKLMNOPQRSTUVWXYZ";
		}

		for ($i=0;$i<intval($length);$i++)
		{
			srand((double) microtime() * 1000000);
			$rc .= $values[mt_rand(0,strlen($values)-1)];
		}

		return $rc;
	}

	/**
	 * generate password with specified characteristics
	 *
	 * @param	array	$options	options array:
	 *				 					min_length		= min. length of password {default: 8}
	 *								 	max_length		= max. length of password {default: 8}
	 *				 					exclude_signs	= string with forbiddenen signs
	 *								 	include_signs	= string with signs, which have to been used
	 *				 					exclude_pwd		= this password is forbidden
	 *								 	with_numbers	= password has to use at least one number {true}
	 *								 	with_lowers		= password has to use at least one lower letter {true}
	 *								 	with_uppers		= password has to use at least one upper letter {true}
	 *				 					with_signs		= password has to use at least one special char {true}
	 * @return	string					generated password string
	 *									(if an error occurred, false)
	 */
	function generate_password($options=null)
	{
		// defaults
		$min_length		= 8;
		$max_length		= 8;
		$with_numbers	= true;
		$with_lowers	= true;
		$with_uppers	= true;
		$with_signs		= true;

		if (is_array($options))
		{
			if (array_key_exists("min_length", $options))
			{
				if (intval($options['min_length'])<3)
					return false;
				else
					$min_length = $options['min_length'];
			}
			if (array_key_exists("max_length", $options))
			{
				if (intval($options['max_length'])<$min_length)
					return false;
				else
					$max_length = $options['max_length'];
			}
			else if ($min_length>$max_length)
				$max_length = $min_length;

			if ((array_key_exists("with_numbers", $options)) && (!$options['with_numbers']))
				$with_numbers = false;
			if ((array_key_exists("with_lowers", $options)) && (!$options['with_lowers']))
				$with_lowers = false;
			if ((array_key_exists("with_uppers", $options)) && (!$options['with_uppers']))
				$with_uppers = false;
			if ((array_key_exists("with_signs", $options)) && (!$options['with_signs']))
				$with_signs = false;

			if ((!$with_numbers) && (!$with_lowers) && (!$with_uppers) && (!$with_signs))
				return false;
			else if (($with_numbers) && ($with_lowers) && ($with_uppers) && ($with_signs) && ($min_length<4))
				return false;
		}

		$numbers	= range(0, 9);
		$lowers		= range('a', 'z');
		$uppers		= range('A', 'Z');
		$signs		= str_split("!@#%&*-_=.,;:^()[]{}/\\");

		$haystack = array();
		if ($with_numbers)
			$haystack = $numbers;
		if ($with_lowers)
			$haystack = array_merge($haystack, $lowers);
		if ($with_uppers)
			$haystack = array_merge($haystack, $uppers);
		if ($with_signs)
			$haystack = array_merge($haystack, $signs);

		// exclude forbitten signs
		$ex_sum = 0;
		if (!isvoid($options['exclude_signs']))
		{
			$ex = str_split($options['exclude_signs']);
			$ex = array_unique($ex);

			$ex_sum = count($ex);
			if ($ex_sum>0)
			{
				// looking for a better solution, kind of array_unmerge
				for ($i=0; $i<$ex_sum; $i++)
				{
					reset($haystack);
					while(list($key, $value)=each($haystack))
					{
						$tmp_ex = $ex[$i];

						// need "", type conversion?
						if ("$value"==$tmp_ex)
						{
							unset($haystack[$key]);
							break;
						}
					}
				}
			}
		}

		// build include array
		$in_sum = 0;
		if (!isvoid($options['include_signs']))
		{
			$in = str_split($options['include_signs']);
			$in = array_unique($in);

			$in_sum = count($in);
			if ($in_sum>$min_length)
				return false;
			else if ($ex_sum>0)
			{
				// check exclude list for include signs
				$needle = str_replace("/", "\\/",	$options['include_signs']);
				$needle = str_replace("(", "\\(", 	$needle);
				$needle = str_replace(")", "\\)",	$needle);
				$needle = str_replace("-", "\\-",	$needle);
				$needle = str_replace("-", "\\-",	$needle);
				$needle = str_replace("\\", "\\\\",	$needle);
				$needle = str_replace(";", "\\;",	$needle);
				$needle = str_replace(".", "\\.",	$needle);
				$needle = str_replace(",", "\\,", 	$needle);
				$needle = str_replace("[", "\\,", 	$needle);
				$needle = str_replace("]", "\\,", 	$needle);
				$needle = str_replace("{", "\\{", 	$needle);
				$needle = str_replace("}", "\\}", 	$needle);

				if (preg_match('/['.$needle.']/', $options['exclude_signs']))
					return false;
			}
		}

		$max_cnt	= 1000; // if 1000 tries find no password, return false!
		$cnt		= 0;
		$rc			= false;

		while(1)
		{
			$cnt++;
			if ($cnt>$max_cnt)	break;

			// length of password
			if ($min_length==$max_length)
			{
				$pwd_length = $min_length;
			}
			else
			{
				$range = range($min_length, $max_length);
				$pwd_length = $range[array_rand($range, 1)];
			}

			$tmp_passwd = "";
			for ($i=0; $i<$pwd_length; $i++)
			{
				$key = array_rand($haystack, 1);
				$tmp_passwd .= $haystack[$key];
			}

			// include signs
			if ($in_sum>0)
			{
				$in_range = range(0, $pwd_length-1);
				for ($i=0; $i<$in_sum; $i++)
				{
					$in_pos = array_rand($in_range, 1);
					$tmp_passwd[$in_pos] = $in[$i];

					// remove from range
					unset($in_range[$in_pos]);
				}
			}

			// count how many lowercase, uppercase, and digits are in the password
			$uc = 0; $lc = 0; $num = 0; $sc = 0;
			for ($i = 0, $j = $pwd_length; $i < $j; $i++)
			{
				$c = substr($tmp_passwd, $i, 1);
				if (preg_match('/^[[:upper:]]$/',$c))
					$uc++;
				else if (preg_match('/^[[:lower:]]$/',$c))
					$lc++;
				else if (preg_match('/^[[:digit:]]$/',$c))
					$num++;
				else
					$sc++;
			}

			// currenty not supported with include chars
			if ($in_sum==0)
			{
				if (($with_lowers) && ($lc==0))
					continue;
				else if (($with_uppers) && ($uc==0))
					continue;
				else if (($with_numbers) && ($uc==0))
					continue;
				else if (($with_signs) && ($sc==0))
					continue;
			}

			if ((!isvoid($options['exclude_pwd'])) && ($options['exclude_pwd']==$tmp_passwd))
				continue;

			$rc = $tmp_passwd;
			break;
		}

		return $rc;
	}

	/**
	 * create unique identification string
	 *
	 * @param	bool	$with_time	true : with timestamp {default}
	 *								false: without considering time and date
	 * @return	string				unique identification string
	 */
	function get_id_string($with_time=true)
	{
		$id_string = $_SERVER['REMOTE_ADDR'].$_SERVER['HTTP_USER_AGENT'];

		if ($with_time)
		{
		 	mt_srand( (double) microtime() * 1000000);
		 	$tmp_time      = md5(mt_rand());
		  	return md5($id_string.$tmp_time);
		}
		else
			return md5($id_string);
	}

	/**
	 * get the ip address from the client, even if the client is befind a proxy
	 * (it doesn't work with transparent proxy!)
	 *
	 * @return	string	ip address of the client
	 */
	function get_client_ip()
	{
		global $REMOTE_ADDR;

		$client_ip = ( !empty($_SERVER['REMOTE_ADDR']) ) ? $_SERVER['REMOTE_ADDR'] : ( ( !empty($_ENV['REMOTE_ADDR']) ) ? $_ENV['REMOTE_ADDR'] : $REMOTE_ADDR );

		// Check for headers used by proxy servers to send the Client IP.
		// We should look for HTTP_CLIENT_IP before HTTP_X_FORWARDED_FOR.
		if ($_SERVER['HTTP_CLIENT_IP'])				$proxy_ip = $_SERVER['HTTP_CLIENT_IP'];
		else if ($_SERVER['HTTP_X_FORWARDED_FOR'])	$proxy_ip = $_SERVER['HTTP_X_FORWARDED_FOR'];

		// Proxy is used, see if the specified Client IP is valid.
		// Sometimes it's 10.x.x.x or 127.x.x.x... Just making sure.
		if ($proxy_ip)
		{
			$ip_list = array();
			if ( preg_match("/^([0-9]+\.[0-9]+\.[0-9]+\.[0-9]+)/", $proxy_ip, $ip_list) )
			{
				$private_ip	= array('/^0\./', '/^127\.0\.0\.1/', '/^192\.168\..*/', '/^172\.16\..*/', '/^10.\.*/', '/^224.\.*/', '/^240.\.*/');
				$client_ip	= preg_replace($private_ip, $client_ip, $ip_list[1]);
			}
		}

		return $client_ip;
	}

	/**
	 * Verify EAN
	 *
	 * @param	string	$ean
	 * @param	string	$type	type of EAN: {default: EAN13}
	 *							EAN8,EAN13,EAN14,UPC12,ILN,NVE18
	 * @return	bool
	 */
	function checkEAN($ean,$type=null)
	{
		$rc = false;

		if ($this->checkNumber($ean,"",false)) // only positive, integer numbers
		{
			$len  = strlen($ean);
			$factor		= 3;
			$sum		= 0;
			$run_mode	= false;
			if (!empty($type)) $type = strtoupper($type);

			if (($type=="EAN8") && ($len==8))								$run_mode = true;
			else if ((($type=="EAN13") || ($type=="ILN")) && ($len==13))	$run_mode = true;
			else if (($type=="EAN14") && ($len==14))						$run_mode = true;
			else if (($type=="UPC12") && ($len==12))						$run_mode = true;
			else if (($type=="NVE18") && ($len==18))						$run_mode = true;

			if ($run_mode)
			{
				$rest = substr($ean,-1);
				$wert = substr($ean,0,$len-1);

				for ($i=($len-1);$i>0;--$i)
				{
     				$sum	= $sum + substr($wert,$i-1,1) * $factor;
     				$factor	= 4 - $factor;
   				}
   				if ($rest==((1000-$sum)%10))	$rc = true;
			}
		}
		return $rc;
	}

	/**
	 * Return size of image file
	 *
	 * @param	string	$filename
	 * @return	int
	 */
	function get_image_size($filename)
	{
		$rc = "";
		if (file_exists($filename))
		{
			$img_info = getimagesize($filename);
			$rc = " ".$img_info[3];
		}
		return $rc;
	}

	/**
	 * shorten text to given length for display
	 *
	 * @param	string	$text		text string
	 * @param	int		$max_length	maximum lenght of text
	 * @param	string	$dots		hars for dots {default: ...}
	 * @return	string
	 */
	function text_shorten($text, $max_length, $dots="...")
	{
		$text_l = strlen($text);
		$dots_l = strlen($dots);

		if (($text_l>$dots_l) && ($text_l>$max_length))
		{
			$rc = substr($text, 0, $max_length).$dots;
		}
		else
		{
			$rc = $text;
		}
		return $rc;
	}

	/**
	 * convert rgb to decimal color code
	 *
	 * @param	string	$rgbstr	rgb string
	 * @return	array			decimal color code
	 */
	function rgb2dec($rgbstr)
	{
		$c = array();

        ereg("([0-9A-F]{2})([0-9A-F]{2})([0-9A-F]{2})", strtoupper($rgbstr), $c);
        if ($c[0])
        {
            $arr['r']= hexdec($c[1]);
            $arr['g']= hexdec($c[2]);
            $arr['b']= hexdec($c[3]);
        }
        return $arr;
	}

	/**
	 * check, if client IP or hostname is in access list
	 *
	 * @param	array	$hosts	array with hostnames, which have access
	 * @param	array	$ips	array with ips, which have access
	 * @param	bool	$mode	client, client ips (REMOTE_ADDR) {default}
	 *							server, client ips (SERVER_ADDR)
	 *							referrer (HTTP_REFERER)
	 * @return	bool			true	: access
	 *							false	: no access
	 */
	function net_access($hosts=null, $ips=null, $mode="client")
	{
		$rc = false;

		if ($mode=="server")
		{
			$test_ip	= $_SERVER['SERVER_ADDR'];
			$test_host	= $_SERVER['SERVER_NAME'];
		}
		else if ($mode=="referer")
		{
			$referer = $_SERVER['HTTP_REFERER'];
			if (empty($referer))
			{
				return false;
			}
			else
			{
				$uinfo = parse_url($referer);
				$test_host	= $uinfo['host'];
				$test_ip	= gethostbyname($test_host);
			}
		}
		else // client
		{
			$test_ip	= $_SERVER['REMOTE_ADDR'];
			$test_host	= gethostbyaddr($test_ip);
		}

		if ((is_array($ips)) && (!empty($test_ip)))
		{
			foreach ($ips as $tmp_ip)
			{
				if (!$tmp_ip) continue;
				$regexp = preg_quote($tmp_ip);
				$regexp = "/$regexp/i";
				if ( preg_match($regexp, $test_ip) == true )
					$rc = true;
			}
		}

		// if no valid ip was found, check host names
		if ((!$rc) && (is_array($hosts)) && (!empty($test_host)))
		{
			foreach ($hosts as $tmp_host)
			{
				if (!$tmp_host) continue;
				$regexp = preg_quote($tmp_host);
				$regexp = "/$regexp/i";
				if ( preg_match($regexp, $test_host) == true )
					$rc = true;
			}
		}

		return $rc;
	}

	/**
	 * pager (navigation) for list
	 *
	 * @param	string	$url			link for urls
	 * @param	int		$current_entry	current page number for navigation
	 * @param	int		$max_entries	highest page number in navigation
	 * @param	int		$page_entries	entries per page {default: $this->page_entries}
	 * @param	int		$image_url		overwrite global image url
	 * @return	string					html string for paper
	 */
	function pager($url, $current_entry=null, $max_entries=null, $page_entries=null, $image_url=null)
	{
		if (!isset($image_url))	$image_url = $this->image_url;

		$rc = "";

		if (empty($page_entries)) $page_entries = $this->page_entries;
		if (empty($page_entries))
		{
			$this->show_error("pager", "page_entries variable not defined!", $page_entries);
		}

		$current_page = intval($current_entry);
		$max_pages	  = intval($max_entries);

		if ($current_page==0)	$current_page = 1; // 0 is not valid!

		if ($max_entries % $page_entries==0) // no rest
			$last_page = (($max_entries/$page_entries)-1)*$page_entries+1;
		else
			$last_page = floor($max_entries/$page_entries)*$page_entries+1;

		if ($current_page==-1) $current_page = $current_entry = $last_page;

		if ((empty($max_entries)) || ($max_entries<$current_entry)) $max_entries = $current_entry;

		$current_page = floor($current_entry/$page_entries)+1;
		$max_pages    = ceil($max_entries/$page_entries);

		if ($max_pages>1)
		{
			// Zeige mindestens 5 und wachse bis auf 10

			if ($current_page==1)      $show_max = 5;
			else if ($current_page==2) $show_max = 6;
			else if ($current_page==3) $show_max = 7;
			else if ($current_page==4) $show_max = 8;
			else if ($current_page==5) $show_max = 9;
			else 					   $show_max = 10;

			$prev_page = $current_page-1;
			$next_page = $current_page+1;

			$prev_entry = ($prev_page*$page_entries)-$page_entries+1;
			$next_entry = ($next_page*$page_entries)-$page_entries+1;

			if ($max_pages<$show_max)  $show_max = $max_pages;

			if ($current_page>1)
			{
				$rc .= $this->url($url."&amp;spage=1","<img src=\"".$image_url."/arrow_small_prev.gif\" alt=\"".STR_PAGE_FIRST."\" style=\"border: none; vertical-align: bottom;\"".$this->end_tag(), 1, "","onmouseover=\"window.status='".STR_PAGE_FIRST."';return true\" onmouseout=\"window.status=''\"")."&nbsp;";
				$rc .= $this->url($url."&amp;spage=$prev_entry","<img src=\"".$image_url."/arrow_small_prev2.gif\" alt=\"".STR_PAGE_PREV."\" style=\"border: none; vertical-align: bottom;\"".$this->end_tag(), 1, "","onmouseover=\"window.status='".STR_PAGE_PREV."';return true\" onmouseout=\"window.status=''\"")."&nbsp;";
			}
			$rc .= "[&nbsp;&nbsp;";

			if ($current_page>6)
			{
				if ($current_page==$max_pages)			$seite=$current_page-9;
				else if ($current_page==($max_pages-1))	$seite=$current_page-8;
				else if ($current_page==($max_pages-2))	$seite=$current_page-7;
				else if ($current_page==($max_pages-3))	$seite=$current_page-6;
				else if ($current_page==($max_pages-4))	$seite=$current_page-5;
				else 									$seite=$current_page-4;

				$show_max_real=$seite+9;
				if ($show_max_real>$max_pages) 	$show_max_real = $max_pages; // error correction
				if ($seite<1)					$seite=1;					 // error correction
			}
			else
			{
				$seite=1;
				$show_max_real=$show_max;
			}

			for($seite;($seite<=$show_max_real);$seite++)
			{
				$site_no = ($seite*$page_entries)-$page_entries+1;
				$tmp_url = $url."&amp;spage=$site_no";
				if ($seite==$current_page)	$rc .= $seite;
				else						$rc .= $this->url($tmp_url,$seite,1,"","onmouseover=\"window.status='".STR_PAGE." $seite';return true\" onmouseout=\"window.status=''\" style=\"color: black;\"");
				$rc .= "&nbsp;&nbsp;";
			}

			$rc .= "]";
			if ($current_page!=$max_pages)
			{
				$rc .= "&nbsp;".$this->url($url."&amp;spage=$next_entry","<img src=\"".$image_url."/arrow_small_next2.gif\" alt=\"".STR_PAGE_NEXT."\" style=\"border: none; vertical-align: bottom;\"".$this->end_tag(), 1, "","onmouseover=\"window.status='".STR_PAGE_NEXT."';return true\" onmouseout=\"window.status=''\"");
				$rc .= "&nbsp;".$this->url($url."&amp;spage=$last_page","<img src=\"".$image_url."/arrow_small_next.gif\" alt=\"".STR_PAGE_LAST."\" style=\"border: none; vertical-align: bottom;\"".$this->end_tag(), 1, "","onmouseover=\"window.status='".STR_PAGE_LAST."';return true\" onmouseout=\"window.status=''\"");
			}

			$rc .= "<br".$this->end_tag()."\n";
		}

		return $rc;
	}

	/**
	 * Setup HTML header
	 *
	 * @param	array	$customize	charset		set charset
	 *								no_cache	if true, page will not be cachable
	 *								gzip		if true, gzip compression will be used
	 */
	function html_header($customize=null)
	{
		// default values
		$charset  = STR_CHARACTER_SET;
		$no_cache = true;
		$gzip     = true;

		if (isset($customize['charset']))  $charset  = $customize['charset'];
		if (isset($customize['no_cache'])) $no_cache = $customize['no_cache'];
		if (isset($customize['gzip']))     $gzip     = $customize['gzip'];
		/////////////////////////////////////////////////////////////////////////////

		if ($gzip) ob_start('ob_gzhandler');

		if (!$no_cache)
		{
			$now = gmdate('D, d M Y H:i:s').' GMT';
            header('Expires: '.$now);                                                                            // rfc2616 - Section 14.21
		    header('Last-Modified: '.$now);
		    header('Cache-Control: no-store, no-cache, must-revalidate, pre-check=0, post-check=0, max-age=0');  // HTTP/1.1
		    header('Pragma: no-cache');                                                                          // HTTP/1.0
		}
		if (!isvoid($charset))  header('Content-Type: text/html; charset="'.$charset.'"');
	}

	/**
	 * Write HTML head
	 *
	 * @param	array	$customize	title
	 *								charset
	 *								no_cache
	 *								lang
	 *								seo
	 *								with_header
	 *								print
	 * @return	string
	 */
	function html_head($customize)
	{
		// default values
		$title			= "no title";
		$charset		= STR_CHARACTER_SET;
		$no_cache		= true;
		$lang			= "de";
		$seo			= true;
		$with_header	= true;
		$print			= true;

		if ($this->xhtml)	$doc_mode		= "xhtml";
		else 				$doc_mode		= "html";

		if ($doc_mode=="frame")
		{
			$end_sign = "";
			$doctype = "<!DOCTYPE html\n\tPUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\"\n\t\"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">\n";
		}
		else if ($doc_mode=="html")
		{
			$end_sign = "";
			$doctype = "<!DOCTYPE html\n\tPUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\"\n\t\"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">\n";
		}
		else
		{
			$end_sign = " /";
			$doc_mode = "xhtml";
			$doctype = "<!DOCTYPE html\n\tPUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\"\n\t\"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">\n";
		}

		if (isset($customize['title']))			$title			= $customize['title'];
		if (isset($customize['charset']))		$charset		= $customize['charset'];
		if (isset($customize['no_cache']))		$no_cache		= $customize['no_cache'];
		if (isset($customize['lang']))			$lang			= $customize['lang'];
		if (isset($customize['seo']))			$seo			= $customize['seo'];
		if (isset($customize['print']))			$print			= $customize['print'];
		if (isset($customize['with_header']))	$with_header	= $customize['with_header'];

		/////////////////////////////////////////////////////////////////////////////

		if ($with_header)	$this->html_header($customize);

		$rc = "";
		if ($doc_mode=="xhtml")
			$rc .= "<?xml version=\"1.0\" encoding=\"$charset\"?>\n";
		$rc .= $doctype;

		if ($doc_mode=="xhtml")
			$rc .= "<html xmlns=\"http://www.w3.org/1999/xhtml\" xml:lang=\"$lang\" lang=\"$lang\">\n";

		$rc .= "<head>\n";
		$rc .= "\t<title>$title</title>\n";
		$rc .= "\t<meta name=\"content-language\" content=\"".$lang."\"".$end_sign.">\n";

		if ($seo)
			$rc .= "\t<meta name=\"robots\" content=\"index,follow\"".$end_sign.">\n";
		else
			$rc .= "\t<meta name=\"robots\" content=\"noindex,nofollow\"".$end_sign.">\n";

		if ($no_cache)
			$rc .= "\t<meta http-equiv=\"expires\" content=\"0\"".$end_sign.">\n";

		if (isset($customize['addon']))	$rc .= "\n".$customize['addon'];

		//$rc .= "</head>\n";

		if ($print)	print $rc;

		return $rc;
	}

	/**
	 * Calculate active URL of an array for validation
	 * (can be used for multi URL sites (e.g. dyndns)
	 *
	 * @param 	mixed	$urls	an url or an array of urls
	 * @return 	string			validated base url
	 */
	function get_base_url($urls)
	{
		if (is_array($urls))
		{
			$rc = false;

			if ($_SERVER['SERVER_PORT']=="443")	$tmp_prot = "https";
			else								$tmp_prot = "http";
			$tmp_url = $tmp_prot."://".$_SERVER['HTTP_HOST'];
			while (list(, $url) = each($urls))
			{
				if (substr_count($url, $tmp_url)>0)
				{
					$rc = $url;
					break;
				}
			}

			if ($rc===false)
			{
				die(STR_ACCESS_DENIED);
			}
		}
		else
		{
			$rc = $urls;
		}

		return $rc;
	}

	/**
	 * Convert string to javascript compatible output
	 *
	 * @param string $string
	 * @return string
	 */
	function js_conv($string)
	{
		$new_string = str_replace("'","\\'", $string);
		$new_string = str_replace("\"","&quot;", $new_string);

		return $new_string;
	}

	/**
	 * Show cphplib error
	 *
	 * like error_level is defined
	 *
	 * @param	string	$function	cphplib function name
	 * @param	string	$message	error message
	 * @param	mixed	$rc			return code
	 */
	function show_error($function, $message, $rc=null)
	{
		if ($this->error_level>0)
		{
			$message = $function." error: ".$message;
			if (isset($rc))
			{
				$message .= " (rc=$rc)";
			}

			if ($this->error_level==3)
			{
				call_user_func($this->error_user_function, $message);
			}
			else if ($this->error_level==1)
			{
				echo $message;
			}
			else
			{
				die($message);
			}
		}
	}

	/**
	 * Get url for mod_rewrite
	 *
	 * @param	string	$prefix			prefix of URL
	 * @param	int		$id
	 * @param	string	$name
	 * @param	bool	$invert_prefix	if true, use prefix and id as suffix
	 * @param	string	$extension		file extention
	 * @return	string
	 */
	function get_mod_rewrite_url($prefix, $id, $name="", $invert_prefix=false, $extension=".html")
	{
		// configuration for name
		$max_length = 50;
		$max_hyphen = 5;

		if ($invert_prefix)	$rc = "";
		else 				$rc = $prefix.$id;

		if (!empty($name))
		{
			if ((!$invert_prefix) && (!empty($rc)))	$rc .= "_";

			$name = $this->get_filename_from_text($name);

			$pieces	= explode("_", $name);

			// get allowed length and hyphen
			if ((strlen($name)>$max_length) || ($pieces>0))
			{
				$tmp_name	= "";
				$rc_name	= "";

				$cnt=0;
				while(list(, $word)=each($pieces))
				{
					if ($cnt>0)	$tmp_name .= "_";
					$tmp_name .= $word;

					if (strlen($tmp_name)<=$max_length)
					{
						$rc_name = $tmp_name;
					}
					else
					{
						break;
					}

					$cnt++;

					if ($cnt==$max_hyphen) break;
				}

				$name = $rc_name;
			}

			$rc .= $name;

			if ($invert_prefix)	$rc .= "_";
		}

		if ($invert_prefix)	$rc .= $prefix.$id;

		$rc .= $extension;

		return $rc;
	}

	/**
	 * Cleanup text from invalid characters for filename
	 *
	 * @param	string	$name
	 * @return	string
	 */
	function get_filename_from_text($name)
	{
		$name = strtolower($name);
		$name = str_replace("- ", " ", $name);
		$name = str_replace(" ", "_", $name);
		$name = str_replace("__", "_", $name);

		// Umlaute
		$trans = get_html_translation_table(HTML_ENTITIES);
		$trans = array_flip($trans);
		$name = strtr($name, $trans);

		$name = str_replace("ä", "ae", $name);
		$name = str_replace("ö", "oe", $name);
		$name = str_replace("ü", "ue", $name);
		$name = str_replace("ß", "ss", $name);
		$name = str_replace("Ä", "Ae", $name);
		$name = str_replace("Ö", "Oe", $name);
		$name = str_replace("Ü", "Ue", $name);

		// valid characters
		$valid = range('A', 'Z');
		$valid = array_merge(range('a', 'z'), $valid);
		$valid = array_merge(range('0', '9'), $valid);
		$valid[] = "-";
		$valid[] = "_";
		$valid[] = " ";

		$valid = array_flip($valid);

		$max		= strlen($name);
		$rc_name	= "";
		for($i=0; $i<$max; $i++)
		{
			$chr = $name[$i];
			if (array_key_exists($chr, $valid))
			{
				$rc_name .= $chr;
			}
		}

		$rc_name = str_replace("-_", "_", $rc_name);

		// twice!!!
		$rc_name = str_replace("__", "_", $rc_name);
		$rc_name = str_replace("__", "_", $rc_name);

		return $rc_name;
	}

	/**
	 * table header for all tables with optional sort support
	 *
	 * @param	array	$cols			colomns array
	 * @param	int		$default_col	default colomn
	 * @param	string	$sort			sort order
	 * @param	string	$base_url		base url for all links
	 * @param	array	$customize		bool	$static			true:	don't use sort header
	 *					  			    						false:	sort header						{default}
	 * 									bool	$use_th			true: th for table vill be used			{default}
	 * 															false: td for table will be used
	 *									int		$max_entries	if > 0: static mode, if max_entries < 2 {default: 2}
	 *									string	$mask			if exists, it will be added on the end of the URL, e.g #mask
	 * 									string	$tr_class		CSS class for TR
	 * 									string	$th_class		CSS class for TD/TH
	 * @return	string					html output
	 */
	function table_header($cols, $default_col, $sort, $base_url, $customize=null)
	{
		$static			= false;
		$use_th			= true;
		$max_entries	= 2;
		$mask			= "";
		$tr_class		= "";
		$td_class		= "";

		if (is_array($customize))
		{
			if ($customize['static'])				$static 		= true;
			if (!$customize['use_th'])				$use_th 		= false;
			if (isset($customize['max_entries']))	$max_entries	= $customize['max_entries'];
			if (isset($customize['mask']))			$mask			= $customize['mask'];
			if (isset($customize['tr_class']))		$tr_class		= $customize['tr_class'];
			if (isset($customize['td_class']))		$td_class		= $customize['td_class'];
		}

		if ($use_th)	$th = "th";
		else 			$th = "td";

		$rc = "\n<!-- ############################## table header ############################ -->\n";

		$rc .= "<tr align=\"left\"";
		if (!empty($tr_class))	$rc .= " class=\"".$tr_class."\"";
		$rc .= ">\n";

		$cols_sum = count($cols);

		if ($cols_sum>0)
		{
			while(list($key, $value)=each($cols))
			{
				if ((isset($value['turn'])) && ($value['turn']==1))
				{
					$sort_suffix1	= "_asc";
					$sort_suffix2	= "_desc";
					$sort_img1		= "<img src=\"".$this->image_url."/order_desc.gif\" id=\"order_desc\" alt=\"\" />&nbsp;&nbsp;";
					$sort_img2 		= "<img src=\"".$this->image_url."/order_asc.gif\" id=\"order_asc\" alt=\"\" />&nbsp;&nbsp;";
					$url_addon2		= "style=\"background: none;\" onmouseover=\"window.status='".STR_SORT_DESC."';return true\" onmouseout=\"window.status=''\"";
					$url_img_addon1 = "style=\"background: none;\" onmouseover=\"window.status='".STR_SORT_ASC."';document.getElementById('order_desc').src='".$this->image_url."/order_asc.gif';return true\" onmouseout=\"window.status='';document.getElementById('order_desc').src='".$this->image_url."/order_desc.gif';\"";
					$url_img_addon2 = "style=\"background: none;\" onmouseover=\"window.status='".STR_SORT_DESC."';document.getElementById('order_asc').src='".$this->image_url."/order_desc.gif';return true\" onmouseout=\"window.status='';document.getElementById('order_asc').src='".$this->image_url."/order_asc.gif';\"";
				}
				else
				{
					$sort_suffix1	= "_desc";
					$sort_suffix2	= "_asc";
					$sort_img1	 	= "<img src=\"".$this->image_url."/order_asc.gif\" id=\"order_asc\" alt=\"\" />&nbsp;&nbsp;";
					$sort_img2	  	= "<img src=\"".$this->image_url."/order_desc.gif\" id=\"order_desc\" alt=\"\" />&nbsp;&nbsp;";
					$url_addon2 	= "style=\"background: none;\" onmouseover=\"window.status='".STR_SORT_ASC."';return true\" onmouseout=\"window.status='';\"";
					$url_img_addon1 = "style=\"background: none;\" onmouseover=\"window.status='".STR_SORT_DESC."';document.getElementById('order_asc').src='".$this->image_url."/order_desc.gif';return true\" onmouseout=\"window.status='';document.getElementById('order_asc').src='".$this->image_url."/order_asc.gif';\"";
					$url_img_addon2 = "style=\"background: none;\" onmouseover=\"window.status='".STR_SORT_ASC."';document.getElementById('order_desc').src='".$this->image_url."/order_asc.gif';return true\" onmouseout=\"window.status='';document.getElementById('order_desc').src='".$this->image_url."/order_desc.gif';\"";
				}

				$rc .= "\t<".$th;
				if (isset($td_class))											$rc .= " class=\"".$td_class."\"";
				if ((isset($value['width'])) && (!isvoid($value['width'])))
				{
					$rc .= " style=\"width: ".($value['width']*1.2)."px\"";
				}
				if ((isset($value['colspan'])) && (!isvoid($value['colspan'])))	$rc .= " colspan=\"".$value['colspan']."\"";

				if ((isset($value['align'])) && (!isvoid($value['align'])))		$rc .= " align=\"".$value['align']."\"";
				else 															$rc .= " align=\"left\"";	// required for konqueror

				if ((isvoid($value['value'])) || ($max_entries<2))
				{
					$rc .= ">".$value['name']."&nbsp;</".$th."\n";
				}
				else
				{
					if (($sort==$value['value'].$sort_suffix2) || ((isvoid($sort)) && ($key==$default_col)))
					{
						if ($static==true)
							$rc .= ">".$value['name']."&nbsp;".$sort_img1."</".$th.">\n";
						else
							$rc .= ">".$this->url($base_url."&amp;sort=".$value['value'].$sort_suffix1.$mask, $value['name'], 1,"",$url_img_addon1)."&nbsp;".$sort_img1."</".$th.">\n";
					}
					else if (($sort==$value['value'].$sort_suffix1) || ((isvoid($sort)) && ($key==$default_col)))
					{
						if ($static==true)
							$rc .= ">".$value['name']."&nbsp;".$sort_img2."</".$th.">\n";
						else
							$rc .= ">".$this->url($base_url."&amp;sort=".$value['value'].$sort_suffix2.$mask, $value['name'], 1,"",$url_img_addon2)."&nbsp;".$sort_img2."</".$th.">\n";
					}
					else
					{
						if ($static==true)
							$rc .= ">".$value['name']."&nbsp;</".$th.">\n";
						else
							$rc .= ">".$this->url($base_url."&amp;sort=".$value['value'].$sort_suffix2.$mask, $value['name'], 1,"",$url_addon2)."&nbsp;</".$th.">\n";
					}
				}
			}
		}
		else
		{
			$rc .= "\t<".$th.">&nbsp;</".$th.">\n";
		}

		$rc .= "</tr>\n";
		$rc .= "<!-- ############################## table header end ############################ -->\n\n";

		return $rc;
	}
}

?>