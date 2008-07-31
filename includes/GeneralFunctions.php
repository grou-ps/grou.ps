<?php

//if(!class_exists('DB'))
//	include_once dirname(__FILE__).'/DB/DB.php';
require_once 'DB.php';
include_once('configs/globals.php');



/**
* Handles multidimentional array sorting by a key (not recursive)
*
* @author Oliwier Ptak <aleczapka at gmx dot net>
*/
class array_sorter
{
    var $skey = false;
    var $sarray = false;
    var $sasc = true;

    /**
    * Constructor
    *
    * @access public
    * @param mixed $array array to sort
    * @param string $key array key to sort by
    * @param boolean $asc sort order (ascending or descending)
    */
    function array_sorter(&$array, $key, $asc=true)
    {
        $this->sarray = $array;
        $this->skey = $key;
        $this->sasc = $asc;
    }

    /**
    * Sort method
    *
    * @access public
    * @param boolean $remap if true reindex the array to rewrite indexes
    */
    function sortit($remap=true)
    {
        $array = &$this->sarray;
        uksort($array, array($this, "_as_cmp"));
        if ($remap)
        {
            $tmp = array();
            while (list($id, $data) = each($array))
                $tmp[] = $data;
            return $tmp;
        }
        return $array;
    }

    /**
    * Custom sort function
    *
    * @access private
    * @param mixed $a an array entry
    * @param mixed $b an array entry
    */
    function _as_cmp($a, $b)
    {
        //since uksort will pass here only indexes get real values from our array
        if (!is_array($a) && !is_array($b))
        {
            $a = $this->sarray[$a][$this->skey];
            $b = $this->sarray[$b][$this->skey];
        }

        //if string - use string comparision
                if (!ereg('^[0-9\-]+$',$a) || !ereg('^[0-9\-]+$',$b))
        {
            if ($this->sasc)
                return strcasecmp($a, $b);
            else 
                return strcasecmp($b, $a);
        }
        else
        {
            if (intval($a) == intval($b)) 
                return 0;

            if ($this->sasc)
                return (intval($a) > intval($b)) ? -1 : 1;
            else
                return (intval($a) > intval($b)) ? 1 : -1;
        }
    }

}//end of class
// http://tr2.php.net/manual/en/function.uksort.php#47936
    function multi_sort_2(&$array, $key, $asc=true)
    {
        $sorter = new array_sorter($array, $key, $asc);
        return $sorter->sortit();
    }





/**
 * function to sort multi dimensional arrays
 * according to a specified key.
 * Very useful!
 * WARNING about $key
 * see http://us2.php.net/manual/en/function.array-multisort.php, RWC
 * Used in:
 * Page.Links.class.ph getLinks()
 *
 */
$key = '';
function multi_sort($array, $akey)
{
  function compare($a, $b)
  {
     global $key;
     if ($a[$key]>$b[$key]){
         $varcmp = "1";
         return $varcmp;
     }
     elseif ($a[$key]<$b[$key]){
         $varcmp = "-1";
         return $varcmp;
     }
     elseif ($a[$key]==$b[$key]){
         $varcmp = "0";
         return $varcmp;
     }
  }
  usort($array, "compare");
  return $array;
}




/*
// in case we need once again;
// otherwise redeclaration errors
$key2 = '';
function multi_sort2($array, $akey)
{
  function compare2($a, $b)
  {
     global $key2;
     if ($a[$key2]>$b[$key2]){
         $varcmp = "1";
         return $varcmp;
     }
     elseif ($a[$key2]<$b[$key2]){
         $varcmp = "-1";
         return $varcmp;
     }
     elseif ($a[$key2]==$b[$key2]){
         $varcmp = "0";
         return $varcmp;
     }
  }
  usort($array, "compare2");
  return $array;
}
*/

/**
 * looks if the array ($arr) has the specified
 * key ($skey)
 * @param $skey key that we search (needle)
 * @param $arr array that we search in (haystack)
 * @return bool
 */
function array_has_key($skey, $arr) {

	$arr_keys = array_keys($arr);
	
	if(in_array($skey,$arr)) {
		return true;
	}
	else {
		return false;
	}
}


function getMembershipID($db_conn, $member_id, $group_id) {
    
    $gid = ','.$group_id.',';
    $membership_id = &$db_conn->getOne("SELECT membership_id FROM memberships WHERE member_id = '{$member_id}' AND gnippet_ids LIKE '%{$gid}%'");    
    
    if (PEAR::isError($membership_id)) {
		die($membership_id->getMessage());
	}
    
    return $membership_id;
    
}

function getMembershipName($db_conn, $member_id, $group_id) {
    
    
    $gid = ','.$group_id.',';
    $member_name = &$db_conn->getOne("SELECT member_name FROM memberships WHERE member_id = '{$member_id}' AND gnippet_ids LIKE '%{$gid}%'");    
    
    if (PEAR::isError($member_name)) {
		die($member_name->getMessage());
	}
    
    return $member_name;
    
    
    
}


// instead of shuffle() which removes keys
// http://tr2.php.net/manual/en/function.shuffle.php
function ass_array_shuffle ($array) {
   while (count($array) > 0) {
       $val = array_rand($array);
       $new_arr[$val] = $array[$val];
       unset($array[$val]);
   }
   return $new_arr;
}



function getPopularTags($max=110) {
    
		global $GlobalDatabase;
	
    	global $db_type;
		global $db_username;
		global $db_password;
		global $db_host;
		global $db_name;
		
		
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
			
			$Database =& DB::connect($dsn, $options);
			if (PEAR::isError($Database)) {
				die("Error No 12313: ".$Database->getMessage());
			}
	        
	        $q = & $Database->query("SET NAMES utf8;");
	  
	        if (PEAR::isError($q)) {
	            die("Error No 132413: ".$q->getMessage());
	        }
		}
		else {
			$Database = $GlobalDatabase;
		}
		
		
        $res = &$Database->getAll("SELECT tag FROM gnippet_tags", array(), 2/*assoc*/);
        
        if (PEAR::isError($res)) {
            die("Error No 12353: ".$res->getMessage());
        }
        
        $restags = array();
        
        /**
         * get rid of Test and empty tags
         * make everything lower case
         */
        foreach($res as $r) {
            $r["tag"] = strtolower($r["tag"]);
            
            // should be longer than 2 words, otherwise search engine does
            // not look for..
            // should not be empty
            // should be an allowed value (for now we just prohibt "text" word)
            if(!empty($r["tag"])&&$r["tag"]!="test"&&strlen($r["tag"])>2
            
            &&$r["tag"]!="linux"&&$r["tag"]!="opensource"&&$r["tag"]!="programming"&&$r["tag"]!="geek"
            )
                $restags[] = $r["tag"];
        }        
        
        $tags = array_count_values($restags);
        
        // key reverse sort
        array_multisort($tags,SORT_DESC,SORT_NUMERIC);
        
        $ret = array_slice($tags,0,$max);
    
        return $ret;
}


function makeCloud($arr) {
    
    $html = ""; // we'll return this
    
    $maxfontsize = 24;
    $minfontsize = 10;
    $offset = 100; // used for color
    
    $t_max = max($arr); 
    $t_min = min($arr);
    
    
    
    // we want random order
    //$arr = ass_array_shuffle($arr);
    arsort($arr);
    
    $array_keys = array_keys($arr);

    
    foreach($arr as $key=>$u) {
        
        $fontsize = ($maxfontsize-$minfontsize)*(sqrt($u/$t_max))+$minfontsize;
        $fontcolor = 255-ceil((255-$offset)*(sqrt($u/$t_max))+$offset);
        
        $html .= "<span><a onmouseover=\"this.style.backgroundColor='black';this.style.color='white';this.style.textDecoration='none';\" onmouseout=\"this.style.backgroundColor='';this.style.color='rgb({$fontcolor},0,{$fontcolor})';this.style.textDecoration='underline';\" style=\"font-size:".$fontsize."px; color: rgb({$fontcolor},0,{$fontcolor});\" href=\"search_groups.do?q=".urlencode($key)."\">".$key."</a> &nbsp; </span>";
    }
    
    return $html;
    
}


function makeCloud2($arr) {
    
    $html = ""; // we'll return this
    
    $maxfontsize = 24;
    $minfontsize = 9;
    $offset = 100; // used for color
    
    $t_max = sizeof($arr); 
    //$t_min = min($arr);
    
    
    
    // we want random order
    $arr = ass_array_shuffle($arr);
    //sort($arr);
    
    $array_keys = array_keys($arr);

    
    foreach($arr as $i=>$u) {
        
        $fontsize = ($maxfontsize-$minfontsize)*(sqrt(($t_max-$i-1)/$t_max))+$minfontsize;
        $fontcolor = 'black';
        
        $html .= "<span><a onmouseover=\"this.style.backgroundColor='black';this.style.color='white';this.style.textDecoration='none';\" onmouseout=\"this.style.backgroundColor='';this.style.color='black';this.style.textDecoration='underline';\" style=\"font-size:".$fontsize."px; color: {$fontcolor};\" href=\"javascript:void())\">".$u."</a> &nbsp; </span>";
    }
    
    return $html;
    
}


// This function converts from Unicode that is
// used in Javascript to UTF8 used in HTML
// @author Emre Sokullu

// declared in index.php and sajax.php
// so no need to redeclare
// even creates code to break
/*function unicode2utf8($val){



    $new_string = "";
    $char = "";
    $string_length = strlen($val);

    for($i=0;$i<$string_length;$i++) {

        $char = $val[$i];

        if($char=="%") {

            if( $i<=($string_length-6) && $val[$i+1]=="u" ) { // \u0045 format

                $num_hexpart = substr($val, $i+2, 4);
                $num_dec = hexdec($num_hexpart);

                $new_string .= "&#{$num_dec};";
                $i+=6;

            }
            else {
                $new_string .= $char;
            }
        }
        else {

            $new_string .= $char;
        }
    }
       return $new_string;
}*/



// echo datediff('w', '9 July 2003', '4 March 2004', false);
// http://www.ilovejackdaniels.com/php/php-datediff-function/
function datediff($interval, $datefrom, $dateto, $using_timestamps = false) {
    /*
    $interval can be:
    yyyy - Number of full years
    q - Number of full quarters
    m - Number of full months
    y - Difference between day numbers
    (eg 1st Jan 2004 is "1", the first day. 2nd Feb 2003 is "33". The datediff is "-32".)
    d - Number of full days
    w - Number of full weekdays
    ww - Number of full weeks
    h - Number of full hours
    n - Number of full minutes
    s - Number of full seconds (default)
    */
  
    if (!$using_timestamps) {
        $datefrom = strtotime($datefrom, 0);
        $dateto = strtotime($dateto, 0);
    }
    $difference = $dateto - $datefrom; // Difference in seconds
  
    switch($interval) {
  
        case 'yyyy': // Number of full years

            $years_difference = floor($difference / 31536000);
            if (mktime(date("H", $datefrom), date("i", $datefrom), date("s", $datefrom), date("n", $datefrom), date("j", $datefrom), date("Y", $datefrom)+$years_difference) > $dateto) {
                $years_difference--;
            }
            if (mktime(date("H", $dateto), date("i", $dateto), date("s", $dateto), date("n", $dateto), date("j", $dateto), date("Y", $dateto)-($years_difference+1)) > $datefrom) {
                $years_difference++;
            }
            $datediff = $years_difference;
            break;

        case "q": // Number of full quarters

            $quarters_difference = floor($difference / 8035200);
            while (mktime(date("H", $datefrom), date("i", $datefrom), date("s", $datefrom), date("n", $datefrom)+($quarters_difference*3), date("j", $dateto), date("Y", $datefrom)) < $dateto) {
                $months_difference++;
            }
            $quarters_difference--;
            $datediff = $quarters_difference;
            break;

        case "m": // Number of full months

            $months_difference = floor($difference / 2678400);
            while (mktime(date("H", $datefrom), date("i", $datefrom), date("s", $datefrom), date("n", $datefrom)+($months_difference), date("j", $dateto), date("Y", $datefrom)) < $dateto) {
                $months_difference++;
            }
            $months_difference--;
            $datediff = $months_difference;
            break;

        case 'y': // Difference between day numbers

            $datediff = date("z", $dateto) - date("z", $datefrom);
            break;

        case "d": // Number of full days

            $datediff = floor($difference / 86400);
            break;

        case "w": // Number of full weekdays

            $days_difference = floor($difference / 86400);
            $weeks_difference = floor($days_difference / 7); // Complete weeks
            $first_day = date("w", $datefrom);
            $days_remainder = floor($days_difference % 7);
            $odd_days = $first_day + $days_remainder; // Do we have a Saturday or Sunday in the remainder?
            if ($odd_days > 7) { // Sunday
                $days_remainder--;
            }
            if ($odd_days > 6) { // Saturday
                $days_remainder--;
            }
            $datediff = ($weeks_difference * 5) + $days_remainder;
            break;

        case "ww": // Number of full weeks

            $datediff = floor($difference / 604800);
            break;

        case "h": // Number of full hours

            $datediff = floor($difference / 3600);
            break;

        case "n": // Number of full minutes

            $datediff = floor($difference / 60);
            break;

        default: // Number of full seconds (default)

            $datediff = $difference;
            break;
    }    

    return $datediff;

}



// echo datediff('w', '9 July 2003', '4 March 2004', false);
// http://www.ilovejackdaniels.com/php/php-datediff-function/
function getStyledDateDiff($datefrom, $dateto, $using_timestamps = false) {
    
    global $treng;
    
    $mindiff = datediff('n',$datefrom, $dateto, $using_timestamps);
    
    $hourdiff = floor($mindiff/60);
    $mindiff %= 60;
    
    $daydiff = floor($hourdiff/24);
    $hourdiff %= 24;
    
    $monthdiff = floor($daydiff/30);
    $daydiff %= 30;
    
    $yeardiff = floor($monthdiff/12);
    $yeardiff %= 12;
    
    
    
    
    if($yeardiff>2) {
        return !isset($treng)?$yeardiff." years ago":sprintf($treng->_("%s years ago","genfuns"),$yeardiff);
    }
    elseif($yeardiff>0) {
        
        
        
        if((($yeardiff*12)+$monthdiff) == 1)
            return !isset($treng)?(($yeardiff*12)+$monthdiff)." month ago":sprintf($treng->_("%s month ago","genfuns"),(($yeardiff*12)+$monthdiff));
        else
            return !isset($treng)?(($yeardiff*12)+$monthdiff)." months ago":sprintf($treng->_("%s months ago","genfuns"),(($yeardiff*12)+$monthdiff));
        
    }
    elseif($monthdiff>2) {
        
        if($monthdiff == 1)
            return !isset($treng)?$monthdiff." month ago":sprintf($treng->_("%s month ago","genfuns"),$monthdiff);
        else
            return !isset($treng)?$monthdiff." months ago":sprintf($treng->_("%s months ago","genfuns"),$monthdiff);
        
    }
    elseif($monthdiff!=0||$daydiff!=0) {       
        if((($monthdiff*30)+$daydiff) == 1)
            return !isset($treng)?(($monthdiff*30)+$daydiff)." day ago":sprintf($treng->_("%s days ago","genfuns"),(($monthdiff*30)+$daydiff));
        else
            return !isset($treng)?(($monthdiff*30)+$daydiff)." days ago":sprintf($treng->_("%s days ago","genfuns"),(($monthdiff*30)+$daydiff));
            
    }
    elseif($hourdiff!=0) {
        
        if($hourdiff == 1)
            return !isset($treng)?$hourdiff." hour ago":sprintf($treng->_("%s hour ago","genfuns"),$hourdiff);
        else
            return !isset($treng)?$hourdiff." hours ago":sprintf($treng->_("%s hours ago","genfuns"),$hourdiff);
        
    }
    else {
        
        if($mindiff == 1)
            return !isset($treng)?$mindiff." minute ago":sprintf($treng->_("%s minute ago","genfuns"),$mindiff);
        else
            return !isset($treng)?$mindiff." minutes ago":sprintf($treng->_("%s minutes ago","genfuns"),$mindiff);
        
    }
    

}

// http://tr.php.net/manual/en/function.urldecode.php#64676
function unicode_urldecode($url)
{
   preg_match_all('/%u([[:alnum:]]{4})/', $url, $a);
  
   foreach ($a[1] as $uniord)
   {
       $dec = hexdec($uniord);
       $utf = '';
      
       if ($dec < 128)
       {
           $utf = chr($dec);
       }
       else if ($dec < 2048)
       {
           $utf = chr(192 + (($dec - ($dec % 64)) / 64));
           $utf .= chr(128 + ($dec % 64));
       }
       else
       {
           $utf = chr(224 + (($dec - ($dec % 4096)) / 4096));
           $utf .= chr(128 + ((($dec % 4096) - ($dec % 64)) / 64));
           $utf .= chr(128 + ($dec % 64));
       }
      
       $url = str_replace('%u'.$uniord, $utf, $url);
   }
  
   return urldecode($url);
}



/**
 * http://tr.php.net/manual/en/ref.strings.php#62307
 * returns true if $str begins with $sub
 * @param string $str
 * @param string $sub
 * @return boolean
 */
function beginsWith( $str, $sub ) {
   return ( substr( $str, 0, strlen( $sub ) ) == $sub );
}



/**
 * http://tr.php.net/manual/en/ref.strings.php#62307
 * return tru if $str ends with $sub
 * @param string $str
 * @param string $sub
 * @return boolean
 */
function endsWith( $str, $sub ) {
   return ( substr( $str, strlen( $str ) - strlen( $sub ) ) == $sub );
}


// http://us2.php.net/manual/en/function.copy.php#70238
function dircpy($basePath, $source, $dest, $overwrite = false){
    if(!is_dir($basePath . $dest)) //Lets just make sure our new folder is already created. Alright so its not efficient to check each time... bite me
    mkdir($basePath . $dest);
    if($handle = opendir($basePath . $source)){        // if the folder exploration is sucsessful, continue
        while(false !== ($file = readdir($handle))){ // as long as storing the next file to $file is successful, continue
            if($file != '.' && $file != '..'){
                $path = $source . '/' . $file;
                if(is_file($basePath . $path)){
                    if(!is_file($basePath . $dest . '/' . $file) || $overwrite)
                    if(!@copy($basePath . $path, $basePath . $dest . '/' . $file)){
                        // echo '<font color="red">File ('.$path.') could not be copied, likely a permissions problem.</font>';
                    }
                } elseif(is_dir($basePath . $path)){
                    if(!is_dir($basePath . $dest . '/' . $file))
                    mkdir($basePath . $dest . '/' . $file); // make subdirectory before subdirectory is copied
                    dircpy($basePath, $path, $dest . '/' . $file, $overwrite); //recurse!
                }
            }
        }
        closedir($handle);
    }
}


function is_ie_less_than_7(){   
	if(isset($_SERVER['HTTP_USER_AGENT'])) {
	if( eregi("(msie) ([0-9]{1,2}.[0-9]{1,3})",$_SERVER['HTTP_USER_AGENT'],$regs) )
{
	$ver = floor($regs[2]);
	return $ver<7;
}
else return false;
	}
	else return false;

}


// http://www.php.net/manual/en/function.array-slice.php#70913
/**
 * array_slice with preserve_keys for every php version
 *
 * @param array $array Input array
 * @param int $offset Start offset
 * @param int $length Length
 * @return array
 */
function array_slice_preserve_keys($array, $offset, $length = null)
{
    // prepare input variables
    $result = array();
    $i = 0;
    if($offset < 0)
        $offset = count($array) + $offset;
    if($length > 0)
        $endOffset = $offset + $length;
    else if($length < 0)
        $endOffset = count($array) + $length;
    else
        $endOffset = count($array);
    
    // collect elements
    foreach($array as $key=>$value)
    {
        if($i >= $offset && $i < $endOffset)
            $result[$key] = $value;
        $i++;
    }
    
    // return
    return($result);
}


#http://us.php.net/manual/en/ref.array.php#68280
//  Get a key position in array
    function array_kpos(&$array,$key) {
        $x=0;
        foreach($array as $i=>$v) {
            if($key===$i) return $x;
            $x++;
        }
        return false;
    }
    
    #http://us.php.net/manual/en/ref.array.php#68280
    // Return key by position
    function array_kbypos(&$array,$pos) {
        $x=0;
        foreach($array as $i=>$v) {
            if($pos==$x++) return $i;
        }
        return false;
    }
    
    
    // http://us2.php.net/manual/en/function.base64-encode.php#82506
function base64_url_encode($input)
{
	return strtr(base64_encode($input), '+/=', '-_,');
}

function base64_url_decode($input)
{
	return base64_decode(strtr($input, '-_,', '+/='));
}



# http://us.php.net/manual/en/function.image-type-to-extension.php#79688
if ( !function_exists('image_type_to_extension') ) {

    function image_type_to_extension ($type, $dot = true)
    {
        $e = array ( 1 => 'gif', 'jpeg', 'png', 'swf', 'psd', 'bmp', 
            'tiff', 'tiff', 'jpc', 'jp2', 'jpf', 'jb2', 'swc',
            'aiff', 'wbmp', 'xbm');

        // We are expecting an integer.
        $type = (int)$type;
        if (!$type) {
            trigger_error( '...come up with an error here...', E_USER_NOTICE );
            return null;
        }

        if ( !isset($e[$type]) ) {
            trigger_error( '...come up with an error here...', E_USER_NOTICE );
            return null;
        }

        return ($dot ? '.' : '') . $e[$type];
    }
    
}

if ( !function_exists('image_type_to_mime_type') ) {

    function image_type_to_mime_type ($type)
    {
        $m = array ( 1 => 'image/gif', 'image/jpeg', 'image/png',
            'application/x-shockwave-flash', 'image/psd', 'image/bmp',
            'image/tiff', 'image/tiff', 'application/octet-stream',
            'image/jp2', 'application/octet-stream', 'application/octet-stream',
            'application/x-shockwave-flash', 'image/iff', 'image/vnd.wap.wbmp', 'image/xbm');

        // We are expecting an integer.
        $type = (int)$type;
        if (!$type) {
            trigger_error( '...come up with an error here...', E_USER_NOTICE );
            return null;
        }

        if ( !isset($m[$type]) ) {
            trigger_error( '...come up with an error here...', E_USER_NOTICE );
            return null;
        }

        return $m[$type];
    }

}

#http://us.php.net/manual/en/function.filesize.php#81906
function urlfilesize($url,$thereturn=false) {
if (substr($url,0,4)=='http') { 
$x = array_change_key_case(get_headers($url, 1),CASE_LOWER);
$x = $x['content-length'];
            }
else { $x = @filesize($url); }
if($thereturn == 'mb') { return round($x / (1024*1024),2) ; }
elseif($thereturn == 'kb') { return round($x / (1024),2) ; }
else  { return $x ; }
}

#http://us2.php.net/manual/en/function.get-headers.php#57131
if(!function_exists('get_headers'))
{
function get_headers($url,$format=1) {
	ob_start();
       $url_info=parse_url($url);
       $port = isset($url_info['port']) ? $url_info['port'] : 80;
       $fp=fsockopen($url_info['host'], $port, $errno, $errstr, 30);
       if($fp) {
           if(!$url_info['path']){
                         $url_info['path'] = "/";
                     }
                     if($url_info['path'] && !$url_info['host']){
                        $url_info['host'] = $url_info['path'];
                        $url_info['path'] = "/";
                     }
                     if( $url_info['host'][(strlen($url_info['host'])-1)] == "/" ){
                        $url_info['host'][(strlen($url_info['host'])-1)] = "";
                     }
                     if(!$url_array[scheme]){
                         $url_array[scheme] = "http"; //we always use http links
                        }
                     $head = "HEAD ".@$url_info['path'];
                     if( $url_info['query'] ){
                         $head .= "?".@$url_info['query'];
                        }
                        print_r($url_info);
           $head .= " HTTP/1.0\r\nHost: ".@$url_info['host']."\r\n\r\n";
           
                     fputs($fp, $head);
           while(!feof($fp)) {
               if($header=trim(fgets($fp, 1024))) {
                   if($format == 1) {
                       $h2 = explode(':',$header);
                       // the first element is the http header type, such as HTTP/1.1 200 OK,
                       // it doesn't have a separate name, so we have to check for it.
                       if($h2[0] == $header) {
                           $headers['status'] = $header;
                       }
                       else {
                           $headers[strtolower($h2[0])] = trim($h2[1]);
                       }
                   }
                   else {
                       $headers[] = $header;
                   }
               }
           }
           ob_end_clean();
           return $headers;
       }
       else {
       	ob_end_clean();
           return false;
       }
   }
}

?>
