<?php

if (!isset($SAJAX_INCLUDED)) {

	/*
	* GLOBALS AND DEFAULTS
	*
	*/
	$sajax_debug_mode = 0;
	$sajax_export_list = array();
	$sajax_request_type = "POST";
	$sajax_remote_uri = "";
	$sajax_is_loaded=strstr($_SERVER['REQUEST_URI'],'sajax_is_loaded');

	/*
	* CODE
	*
	*/

	//
	// Initialize the Sajax library.
	//
	function sajax_init() {
	}

	//
	// Helper function to return the script's own URI.
	//
	function sajax_get_my_uri() {
		return $_SERVER['REQUEST_URI'];
	}
	$sajax_remote_uri = sajax_get_my_uri();

	//
	// Helper function to return an eval()-usable representation
	// of an object in JavaScript.
	//
	function sajax_get_js_repr($value='') {
		$type = gettype($value);

		//if ($type == "boolean" ||
		//$type == "integer") {
		if ($type == "boolean") {
			return ($value) ? "Boolean(true)" : "Boolean(false)";
		}
		elseif ($type == "integer") {
			return "parseInt($value)";
		}
		elseif ($type == "double") {
			return "parseFloat($value)";
		}
		elseif ($type == "array" || $type == "object" ) {
			//
			// XXX Arrays with non-numeric indices are not
			// permitted according to ECMAScript, yet everyone
			// uses them.. We'll use an object.
			//

			# check that array is fully numeric
			if($type=='array'){
				$isNumeric=1;
				foreach(array_keys($value) as $key)if((int)$key != $key)$isNumeric=0;
			}
			# use an ordinary array if the keys are numeric
			if($type=='array' && $isNumeric){
				$arr=array();
				foreach($value as $k=>$v)$arr[]=sajax_get_js_repr($v);
				return '['.join(',',$arr).']';
			}

			$s = "{ ";
			if ($type == "object") {
				$value = get_object_vars($value);
			}
			foreach ($value as $k=>$v) {
				$esc_key = sajax_esc($k);
				if (is_numeric($k))
				$s .= "$k: " . sajax_get_js_repr($v) . ", ";
				else
				$s .= "\"$esc_key\": " . sajax_get_js_repr($v) . ", ";
			}
			//return substr($s, 0, -2) . " }";
			if (count($value))
			$s = substr($s, 0, -2);
			return $s . " }";

		}
		else {
			$esc_val = sajax_esc($value);
			$s = "'$esc_val'";
			return $s;
		}
	}
	function sajax_handle_client_request() {
		global $sajax_export_list;
		$mode="";
		if(!empty($_GET["rs"]))$mode = "get";
		if(!empty($_POST["rs"]))$mode = "post";
		if(empty($mode))return;
		$target='';
		if ($mode == "get") {
			// Bust cache in the head
			header ("Expires: Mon, 26 Jul 1997 05:00:00 GMT");    // Date in the past
			header ("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
			// always modified
			header ("Cache-Control: no-cache, must-revalidate");  // HTTP/1.1
			header ("Pragma: no-cache");                          // HTTP/1.0
			$func_name = $_GET["rs"];
			if (! empty($_GET["rsargs"]))$args = $_GET["rsargs"];
			else $args=array();
		}
		else {
			$func_name = $_POST["rs"];
			if (! empty($_POST["rsargs"]))
			$args = $_POST["rsargs"];
			else
			$args = array();
		}
		echo(in_array($func_name,$sajax_export_list))?
		'+:var res='.trim(sajax_get_js_repr(call_user_func_array($func_name,$args))).';res;':
		"-:$func_name not callable";
		exit;
	}
	function sajax_get_common_js() {
		global $sajax_debug_mode,$sajax_request_type;
		global $sajax_remote_uri;

		$t = strtoupper($sajax_request_type);
		if ($t != "" && $t != "GET" && $t != "POST")
		return "// Invalid type: $t.. \n\n";
		ob_start();
			?>
		// remote scripting library
		// (c) copyright 2005 modernmethod, inc
		var sajax_debug_mode = <?php echo $sajax_debug_mode ? "true" : "false"; ?>;
		var sajax_request_type = "<?php echo $t; ?>";
		var sajax_target_id = "";
		window.sajax_debug=function(text) {
			if (sajax_debug_mode)
				alert("RSD: " + text)
		}
 		window.sajax_init_object=function() {
 			sajax_debug("sajax_init_object() called..")
 			var A=null;
			if(navigator.userAgent.indexOf('IE')>-1 && navigator.userAgent.indexOf('Opera')==-1){
				try {
					A=new ActiveXObject("Msxml2.XMLHTTP");
				} catch (e) {
					try {
						A=new ActiveXObject("Microsoft.XMLHTTP");
					} catch (oc) {
						XMLHttpRequest=function(){
							var i=0;
							var url='';
							var responseText='';
							var iframe='';
							this.onreadystatechange=function(){
								return false;
							}
							this.open=function(method,url){
								//TODO: POST methods
								this.i=++kXHR_instances; // id number of this request
								this.url=url;
								document.body.appendChild(document.createElement('<iframe id="kXHR_iframe_'+this.i+'" style="display:none" src="/"></iframe>'));
							}
							this.send=function(postdata){
								//TODO: use the postdata
								document.getElementById('kXHR_iframe_'+this.i).src=this.url;
								kXHR_objs[this.i]=this;
								setTimeout('XMLHttpRequest_checkState('+this.i+',2)',2);
							}
							return true;
						}
						XMLHttpRequest_checkState=function(inst,delay){
							var el=document.getElementById('kXHR_iframe_'+inst);
							if(el.readyState=='complete'){
								var responseText=document.frames['kXHR_iframe_'+inst].document.body.innerText;
								kXHR_objs[inst].responseText=responseText;
								kXHR_objs[inst].readyState=4;
								kXHR_objs[inst].status=200;
								kXHR_objs[inst].onreadystatechange();
								el.parentNode.removeChild(el);
							}else{
								delay*=1.5;
								setTimeout('XMLHttpRequest_checkState('+inst+','+delay+')',delay);
							}
						}
					}
				}
			}
			if(!A && typeof XMLHttpRequest != "undefined")
				A = new XMLHttpRequest();
			if (!A)
				sajax_debug("Could not create connection object.");
			return A;
		}
		window.sajax_do_call=function(func_name, args) {
			var i, x, n;
			var uri;
			var post_data;
			var target_id;
			
			sajax_debug("in sajax_do_call().." + sajax_request_type + "/" + sajax_target_id);
			target_id = sajax_target_id;
			if (sajax_request_type == "") 
				sajax_request_type = "GET";
			
			uri = function_urls[func_name];

			if (sajax_request_type == "GET") {
			
				if (uri.indexOf("?") == -1) 
					uri += "?rs=" + escape(func_name);
				else
					uri += "&rs=" + escape(func_name);
				uri += "&rst=" + escape(sajax_target_id);
				uri += "&rsrnd=" + new Date().getTime();
				
                var tmpstring = "";
				for (i = 0; i < args.length-1; i++)  {
                    tmpstring = new String(args[i]);
                    // TODO: implement &
                    //tmpstring = tmpstring.replace(/&/g,"and");
					uri += "&rsargs[]=" + encodeURIComponent(tmpstring);
                }

				post_data = null;
			} 
			else if (sajax_request_type == "POST") {
				post_data = "rs=" + escape(func_name);
				post_data += "&rst=" + escape(sajax_target_id);
				post_data += "&rsrnd=" + new Date().getTime();
				
                var tmpstring = "";
				for (i = 0; i < args.length-1; i++) {
                    tmpstring = new String(args[i]);
                    //tmpstring = tmpstring.replace(/&/g,"and");
					post_data = post_data + "&rsargs[]=" + encodeURIComponent(tmpstring);
                }
			}
			else {
				alert("Illegal request type: " + sajax_request_type);
			}
			
			x = sajax_init_object();
			x.open(sajax_request_type, uri, true);
			
			if (sajax_request_type == "POST") {
				x.setRequestHeader("Method", "POST " + uri + " HTTP/1.1");
				x.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
			}
			
			x.onreadystatechange = function() {
				if (x.readyState != 4) 
					return;

				sajax_debug("received " + x.responseText);
				
				var status;
				var data;
				status = x.responseText.charAt(0);
				data = x.responseText.substring(2);
				if (status == "-") 
					alert("Error: " + data);
				else {
					if (target_id != "") 
						document.getElementById(target_id).innerHTML = eval(data);
					else {
						// args[args.length-1](eval(data));
						try {
                            var callback;
                            var extra_data = false;
                            if (typeof args[args.length-1] == "object") {
                                callback = args[args.length-1].callback;
                                extra_data = args[args.length-1].extra_data;
                            } else {
                                callback = args[args.length-1];
                            }
                            callback(eval(data), extra_data);
						} catch (e) {
                            // soyle donduruyor: :var res='1';res;
                            // iki nokta fazla
                            data = x.responseText.substring(3);
			    var tmp__data = data.substring(0,3);
			    if(tmp__data=='ar ')
				    data = x.responseText.substring(2);
                            if(tmp__data==':va')
                                data = x.responseText.substring(4);
                            args[args.length-1](eval(data));
								//sajax_debug("Caught error " + e + ": Could not eval " + data );
                        }
                    }
				}
				if(!(--active_sajax_requests))window.status='';
			}
			sajax_debug(func_name + " uri = " + uri + "/post = " + post_data);
			window.status='Retrieving data from server : '+uri;
			active_sajax_requests++;
			x.send(post_data);
			sajax_debug(func_name + " waiting..");
			delete x;
			return true;
		}
		
	try{
		if(function_urls)window.blah=1/0;
	}catch(e){
		window.function_urls=[];
		window.kXHR_instances=0;
		window.kXHR_objs=[];
		window.active_sajax_requests=0;
	} 
		<?php
		$html = ob_get_contents();
		ob_end_clean();
		return $html;
	}

	function sajax_show_common_js() {
		echo sajax_get_common_js();
	}
	function sajax_esc($val){
		$val = str_replace("\\", "\\\\", $val);
		$val = str_replace("\r", "\\r", $val);
		$val = str_replace("\n", "\\n", $val);
		$val = str_replace("'", "\\'", $val);
		return str_replace('"', '\\"', $val);
		//return /*unicode2utf8(utf8_encode(*/$val/*))*/;
	}
	function sajax_get_one_stub($func_name) {
		global $sajax_is_loaded;
		ob_start();
		?>
function x_<?php echo $func_name; ?>(){sajax_do_call("<?php echo $func_name; ?>",x_<?php echo $func_name; ?>.arguments);}
function_urls['<?=$func_name;?>']='<?=sajax_get_my_uri();?>';
<?php
if(!$sajax_is_loaded)echo 'sajax_is_loaded=1;';
$html = ob_get_contents();
ob_end_clean();
return $html;
	}

	function sajax_show_one_stub($func_name) {
		echo sajax_get_one_stub($func_name);
	}
	function sajax_export() {
		global $sajax_export_list;

		$n = func_num_args();
		for ($i = 0; $i < $n; $i++) {
			$sajax_export_list[] = func_get_arg($i);
		}
	}
	$sajax_js_has_been_shown = 0;
	function sajax_get_javascript()
	{
		global $sajax_js_has_been_shown,$sajax_is_loaded;
		global $sajax_export_list;

		$html = "";
		if (! $sajax_js_has_been_shown && !$sajax_is_loaded) {
			$html .= sajax_get_common_js();
			$sajax_js_has_been_shown = 1;
		}
		foreach ($sajax_export_list as $func) {
			$html .= sajax_get_one_stub($func);
		}
		return $html;
	}

	function sajax_show_javascript()
	{
		echo sajax_get_javascript();
	}


	$SAJAX_INCLUDED = 1;
}

?>