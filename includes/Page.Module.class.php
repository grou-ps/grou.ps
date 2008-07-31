<?php

require_once('TranslationEngine.class.php');
require_once('DB.php');

/**
 * This is the generic Module class
 * All modules should extend this one
 * @author Emre Sokullu
 */
class ModulePage {
	
	/**
	 * The database object
	 * @access private
	 * @var PEAR::DB object
	 */
	var $Database;
	
	
	/**
	 * The TranslationEngine object
	 * @access private
	 * @var TranslationEngine object
	 */
	var $TrEng;
	
	/**
	 * the short name of the module like: pagefunds
	 * To be used in function _ for example...
	 * @access private
	 * @var string
	 */
	var $ModuleCode;
	
	
	/**
	 * The constructor of the generic module
	 * class, ModulePage (Page.Module.class.php)
	 * <br>
	 * Takes global variables like TrEng and GlobalDatabase
	 * and sets the variables that will be used throughout
	 * the class
	 * @access public
	 * @param string $group
	 * @param string $modulecode
	 * @return ModulePage
	 */
	function ModulePage( $modulecode) {

		global $GlobalDatabase;
		global $treng;

		_filter_var($group);

		if(isset($GlobalDatabase))
			$this->Database = $GlobalDatabase;
		else {
			include('configs/globals.php');	
			$dsn = array(
	            'phptype'  => $db_type,
	   			'username' => $db_username,
	   			'password' => $db_password,
	            'hostspec' => $db_host,
	   			'database' => $db_name,
	            );
	
			$options = array(
	            'debug'       => 2,
	            'portability' => DB_PORTABILITY_ALL,
	            );
			
			$this->Database =& DB::connect($dsn, $options);
			if (PEAR::isError($this->Database)) {
				die($this->Database->getMessage());
			}
	        
	        $q = & $this->Database->query("SET NAMES utf8;");
	  
	        if (PEAR::isError($q)) {
	            die($q->getMessage());
	        }			
		}

		if(isset($treng))
			$this->TrEng = $treng;
		else 
			$this->TrEng = new TranslationEngine();
		
		$this->ModuleCode = $modulecode;

	}
	
	

	/**
	 * The translation function.<br />
	 * Any text block that will be output
	 * to the web page should be localized.
	 * This function takes care of that
	 * @access protected
	 * @param string text to translate (in english)
	 * @return translated text
	 */
	function _($text) {
		return $this->TrEng->_($text, $this->ModuleCode);
	}
	
	/**
	 * This function returns the javascript code that
	 * will be added to the module page.
	 * These javascript function should be the <u>presentation</u>
	 * part of the ajax hooks that the module can use.
	 * @access public
	 * @return javascript code
	 */
	function getAjaxFunctions() {
		return "";
	}
	
	/**
	 * The names of the functions that will be
	 * registered as AJAX functions
	 * These functions will be outside the scope
	 * of that class but included in the same
	 * file at the top of it.
	 * @access public
	 * @return an array of ajax functions to register
	 */
	function getFunctionsToRegister() {
		return array();
	}
	
	/**
	 * General Help Text that will help
	 * visitors to learn more about this
	 * specific module
	 * @access public
	 * @return help text
	 *
	 */
	function getHelpText() {
		return "";
	}
	
	/**
	 * Returns RSS output of this module
	 * @access public
	 * @return RSS output of this module
	 */
	function getRSS() {
		return "";
	}
	
	
}