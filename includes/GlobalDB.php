<?php		

if(!isset($GlobalDatabase)) {

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
		
		$GlobalDatabase =& DB::connect($dsn, $options);
		if (PEAR::isError($GlobalDatabase)) {
			die($GlobalDatabase->getMessage());
		}
        
		/**
		 * @todo speed optimization, make this default from the daemon
		 * /usr/local/bin/mysql --default-character-set=utf8
		 * my.cnf
		 * for speed optimization
		 */
        $q = & $GlobalDatabase->query("SET NAMES utf8;");
  
        if (PEAR::isError($q)) {
            die("Error No 3002: ".$q->getMessage());
        }
        
}
        
        ?>