<?php

/**
 * This file contains bbcode that will be frquently used throughout
 * our operations. Instead of letting members to enter html
 * we limit them by bbcode
 * This is a security precaution.
 */


 function parse_bbcode(&$message) {   

       

        $message = nl2br($message);


        $message = str_replace("[b]", "<b>", $message);



        $message = str_replace("[/b]", "</b>", $message);



        $message = str_replace("[i]", "<i>", $message);



        $message = str_replace("[/i]", "</i>", $message);



        $message = str_replace("[u]", "<u>", $message);



        $message = str_replace("[/u]", "</u>", $message);


        $message = str_replace("[center]", "<div align=\"center\">", $message);



        $message = str_replace("[/center]", "</div>", $message);



        $message = str_replace("[left]", "<div align=\"left\">", $message);



        $message = str_replace("[/left]", "</div>", $message);



        $message = str_replace("[right]", "<div align=\"right\">", $message);



        $message = str_replace("[/right]", "</div>", $message);



        $message = str_replace("[ol]", "<ol>", $message);

	$message = str_replace("[ul]", "<ul>", $message);

        $message = str_replace("[li]", "<li>", $message);



        $message = str_replace("[/ol]", "</ol>", $message);

	$message = str_replace("[/ul]", "</ul>", $message);

        $message = str_replace("[br]", "<br>", $message);


        $message = eregi_replace("\[img\]([^\\[]*)\[/img\]", "<img src=\"\\1\" border=\"0\">", $message);


        

        

        $message = eregi_replace("\[url\]http://([^\\[]*)\[/url\]", "<a href=\"http://\\1\">\\1</a>", $message);




}


function return_parsed_bbcode($message,$nowrap=false) {   

	
	// never strip_tags here, see Page.Talks for details
       
        $message = str_replace("[b]", "<b>", $message);



        $message = str_replace("[/b]", "</b>", $message);



        $message = str_replace("[i]", "<i>", $message);



        $message = str_replace("[/i]", "</i>", $message);



        $message = str_replace("[u]", "<u>", $message);



        $message = str_replace("[/u]", "</u>", $message);


        $message = str_replace("[center]", "<div align=\"center\">", $message);



        $message = str_replace("[/center]", "</div>", $message);



        $message = str_replace("[left]", "<div align=\"left\">", $message);



        $message = str_replace("[/left]", "</div>", $message);



        $message = str_replace("[right]", "<div align=\"right\">", $message);



        $message = str_replace("[/right]", "</div>", $message);



        $message = str_replace("[ol]", "<ol>", $message);

	$message = str_replace("[ul]", "<ul>", $message);

        $message = str_replace("[li]", "<li>", $message);



        $message = str_replace("[/ol]", "</ol>", $message);

	$message = str_replace("[/ul]", "</ul>", $message);

        $message = str_replace("[br]", "<br>", $message);

        $message = eregi_replace("\[img\]([^\\[]*)\[/img\]", "<img src=\"\\1\" border=\"0\">", $message);


        

        

        $message = eregi_replace("\[url\](https?://[^\\[]*)\[/url\]", "<a href=\"\\1\">\\1</a>", $message);

        

        if(function_exists("tidy_get_output")) {
        	if(!$nowrap) {
						$config = array('indent'=> FALSE,
						                'output-xhtml' => TRUE,
						                'show-body-only' => TRUE,
						                'wrap' => 80);
        	}
        	else {
        		$config = array('indent'=> FALSE,
						                'output-xhtml' => TRUE,
						                'show-body-only' => TRUE);
        	}
						tidy_set_encoding('UTF8');
						foreach ($config as $key => $value) {
						   tidy_setopt($key,$value);
						}
						tidy_parse_string($message);
						tidy_clean_repair();
						$message = tidy_get_output();
					}

        return $message;

}



?>