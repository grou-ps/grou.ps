<?php

/**
 * This file includes global functions
 * for variable security.
 * The functions represented in here are
 * for cleaning variables in request and response
 */ 


/**
 * IMPORTANT NOTICE ABOUT CODING
 *
 * Considered making is_string() and is_array()
 * checks. But observed that there's no problem
 * without them.
 * So decided that it is safer to leave them as is
 */ 
 
 
 
 
	/**
	 * This class heavily uses variables coming directly from
	 * internet. Moreover, most of them come from Ajax enabled
	 * browsers. So the server does not directly deal with them.
	 * We should filter them before using.
	 * @param $vars array of variables, should be passed by reference (&)
	 * @returns if nothing went wrong, true; else false
	 */
function _filter_vars(&$vars) {
	
   

    if (get_magic_quotes_gpc()==1) {
        
		foreach($vars as $i=>$var) {	
            
            // no more escapeshellcmd, because it fucks &
            // and we don't need it..
            // do we make any system call?
			$vars[$i] = trim(strip_tags($var));	
            //$vars[$i] = trim(escapeshellcmd(addslashes(stripslashes($var))));	
		}

    } else {

		foreach($vars as $i=>$var) {	
            
            // no more escapeshellcmd, because it fucks &
            // and we don't need it..
            // do we make any system call?
			$vars[$i] = addslashes(stripslashes(trim(strip_tags($var))));	
            //$vars[$i] = trim(escapeshellcmd(addslashes(stripslashes($var))));	
            
		}

}


		
		return true;
	}






	
	function _filter_var(&$var) {
	
		/** htmlspecialchars instead pf strip_tags
		 * because this prevents tags to be completely removed
		 * user may have accidentally enter tags; and this way
		 * he will remember them
		 */
        if (get_magic_quotes_gpc()==1) {
            
            // no more escapeshellcmd, because it fucks &
            // and we don't need it..
            // do we make any system call?
            $var = trim(strip_tags($var));	
            
        }
        else {
            
            // no more escapeshellcmd, because it fucks &
            // and we don't need it..
            // do we make any system call?
            $var = addslashes(stripslashes(trim(strip_tags($var))));	
        }
		//$var = trim(escapeshellcmd(addslashes(stripslashes($var))));
		return true;
		
	}
	
    
    
    
    
	function _filter_res_vars(&$vars) {
		
		foreach($vars as $i=>$var) {
		
		 $vars[$i] = stripslashes($var);
            $vars[$i] = $var;
		}
	
		return true;
	}
	
	function _filter_res_var(&$var) {
	
		$var = stripslashes($var);
        $var = $var;
        
		return true;
	}





?>