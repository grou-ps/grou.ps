<?php


//if(!class_exists('DB'))
//	include_once dirname(__FILE__).'/DB/DB.php';
require_once 'DB.php';

class TranslationEngine {
	
	var $Database = null;
	var $Language = "";
	var $LanguageTable = "";
	
	
	function TranslationEngine($lang="english") {
		
		global $GlobalDatabase;
		
		_filter_var($lang);
		
		if(is_language_supported($lang)) {
			$this->Language = $lang;
			$this->LanguageTable = "lang_{$this->Language}";
		}
		else {
			$this->Language = "english";
			$this->LanguageTable = "";
		}
		
		
		/**
		 * no need to create database connection for English
		 * because it won't be used...
		 */
		if($this->Language!='english') {

			if(!isset($GlobalDatabase)) {
				
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
			else {
				$this->Database = $GlobalDatabase;
			}
		}
		
	}
	
	
	
	// This function makes the translation and returns the translated
	// string.. The function is named as "_" to keep it short; because
	// it will be used very frequently and it is the only meaningful and 
	// public function of this class.
	function _($text="",$page="",$vars="") {
		
		// no translation for english
		// the default language
		if($this->Language=="english") {
			return $this->_compileVars($text,$vars);
		}
		else {
			
			return $this->getTranslation($text,$page,$vars);
			
		}
		
	}
	
	
	// used internally; should have been private
	function getTranslation($text="",$page="",$vars="") {
		
		
		$text_s = mysql_real_escape_string($text);
		$page = mysql_real_escape_string($page);
		
		$sql = "";
		$res = "";
		
		if($text=="") {
			return "";
		}
		else {
			
			if($page==""||!is_string($page)) {
				
				$sql = "SELECT word_to FROM {$this->LanguageTable} WHERE word_from='{$text_s}' LIMIT 1;";
				$res = $this->Database->getOne($sql);

				
				if (PEAR::isError($res)) {
	              die($res->getMessage());
	        	}
				
			}
			else {
				
				$sql = "SELECT word_to FROM {$this->LanguageTable} WHERE word_from='{$text_s}' AND page='{$page}' LIMIT 1;";
				$res = $this->Database->getOne($sql);
				
		        if (PEAR::isError($res)) {
	              die($res->getMessage());
		        }
		        
		        if(empty($res)) {
			        	
					$sql = "SELECT word_to FROM {$this->LanguageTable} WHERE word_from='{$text_s}' LIMIT 1;";
					$res = $this->Database->getOne($sql);
	
					
					if (PEAR::isError($res)) {
		              die($res->getMessage());
		        	}
		        	
		        	
		        }
		        
				
				
			}
			
			
			if(empty($res))
				return $this->_compileVars($text,$vars);
			else {
				// If there are variables, replace %TRENGVAR_?% with variables
				return $this->_compileVars($res,$vars);
			}
				
		}
		
	}
	
	
	function _compileVars($res,$vars) {
		if(is_array($vars)) {
					$size = count($vars);
					for($i=1;$i<=$size;$i++) {
						// Replace %TRENGVAR_($i)% with $vars[$i]
						$res = str_replace('%TRENGVAR_'.$i.'%',$vars[($i-1)],$res);
					}
				} elseif ($vars!="") {
					// Replace %TRENGVAR% with $vars
					$res = str_replace('%TRENGVAR%',$vars,$res);
				}
				return $res;
	}
	
	
}



?>