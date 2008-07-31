<?php

/**
 * No filtering in here!
 * otherwise pages get useless
 * the variables must have been cleaned before coming at this point!
 */


class PageGenerator {
	
	var $html = '';

	var $layout = '';
	var $blocks = array();
	
	/**
	 * Predefined layout types
	 */
	var $EqualColumns = 1;
	var $WithSidebar = 2;
	var $FullColumn = 3;
	
	/**
	 * Predefined block types
	 */
	var $LeftColumn = 1;
	var $RightColumn = 2;
	var $Sidebar = 3;
	var $MainColumn = 4;
	var $Column = 5;
	var $InvisibleArea = 6;
	
	function PageGenerator() {
	
	}
	
	function setLayout($layout) {
	
		_filter_var($layout);
		
		$this->layout = $layout;
	
	}
    
    function getBlockNo() {
        
        $i = 1;
        
        foreach($this->blocks as $b) {
         
            if(!$b['is_promotional'])
                $i++;
            
        }
        
        return $i;
        
    }
	
    
    /**
     * adds a block to the page
     * @param $block_location location, should be a predefined word
     * @param $block_title title of the block, keep it blank for titleless blocks
     * @param $block_content the content
     * @param $is_hidden ..
     */
	function addBlock($block_location, $block_title, $block_content, $help_text="", $is_hidden=false, $is_operations=false, $is_promotional=false, $is_userdependent=false, $subtitle_operations="", $subtitle_info="") {
	
			
		$thisblock = array();
		
		if($this->_isLocatedCorrectly($block_location)) {
			$thisblock['location']=$block_location;
		}
		else {
			die('Error');
		}
        
		
		$thisblock['title'] = $block_title;
		
		$thisblock['content'] = $block_content;
		
		$thisblock['hidden'] = $is_hidden;
		
		$thisblock['subtitle_info'] = $subtitle_info;
		$thisblock['subtitle_operations'] = $subtitle_operations;
		
		$block_no = $this->getBlockNo();
        
        if(!$is_promotional) {
            
            $thisblock['id'] = 'block_no_'.$block_no;
            $thisblock['title_id'] = 'block_title_no_'.$block_no;
            if($is_hidden) {
                $thisblock['hidden_id'] = 'hidden_block_'.$block_no;
            }
            else {
                $thisblock['hidden_id'] = '';
            }
         
            $thisblock['is_operations'] = $is_operations;
            $thisblock['is_promotional'] = false;
        }
        else {
         
            $thisblock['id'] = 'promotional_block';
            $thisblock['title_id'] = 'promotional_block_title';
            $thisblock['hidden_id'] = '';
         
            $thisblock['is_operations'] = false;
            $thisblock['is_promotional'] = true;
            
        }
        
        
	$thisblock['is_userdependent'] = $is_userdependent;
	
	$thisblock['help_text'] = $help_text;
		
		$this->blocks[] = $thisblock;
		
	
	}
	
	/**
	 * Removes the last block added
	 * This should be operated before the generateHTML
	 * function
	 *
	 */
	function removeLastBlock() {
		$this->blocks = array_pop($this->blocks);
	}
	
	function _isLocatedCorrectly($block_location) {
	
	
	
		if($block_location==$this->InvisibleArea||$block_location==$this->Column) {
			
			return true;
			
		}
		else {
				
			switch($this->layout) {
				
				case $this->EqualColumns:
					
					if($block_location==$this->LeftColumn||$block_location==$this->RightColumn) {
						return true;
					} 
					else {
						return false;
					}
					
					break;
					
				case $this->WithSidebar:
				
					if($block_location==$this->Sidebar||$block_location==$this->MainColumn) {
						return true;
					} 
					else {
						return false;
					}
					
					break;
					
				default:
				
					return false;
			
			}
		}
	}
	
	function generateHTML() {
	
		if(empty($this->layout)||sizeof($this->blocks)==0) {
			return false;
		}
		else {
	
			$tmp_html = '';
			$block_html = array();
		
			foreach($this->blocks as $block) {
			
				// very important!!
				// clears the buffer in each turn
				$tmp_html = '';
			
				if($block['location']==$this->InvisibleArea) {
					$tmp_html .= "<div id=\"{$block['id']}\" style=\"position:absolute;visibility:hidden;\">\n";
					$tmp_html .= "\t{$block['content']}\n";
					$tmp_html .= "</div>\n";
				}
				else {
					if($block['hidden']) {
						$tmp_html .= "<div id=\"{$block['hidden_id']}\"  style=\"display:none\" thehelp=\"{$block['help_text']}\">";
					}
                    else {
                        $tmp_html .= "<div id=\"top_{$block['id']}\" thehelp=\"{$block['help_text']}\">";
                    }
                    
                    if(!$block['is_operations']&&!$block['is_promotional']) {
                        $tmp_html .= "<div class=\"box\">";
                    }
                    
                    if(!empty($block['title'])) {
                        $tmp_html .= "<div class=\"box_top\" id=\"{$block['title_id']}\">";
                        $tmp_html .= "{$block['title']}<br />";
                        $tmp_html .= "</div>";
                    }
                    
                    if(!empty($block['subtitle_info'])||!empty($block['subtitle_operations'])) {
                    	$tmp_html .= "<div class=\"box_subtitle\">";
                    	$tmp_html .= "<div id=\"subtitle_info_{$block['id']}\" class=\"box_subtitle_info\">{$block['subtitle_info']}</div>";
                    	$tmp_html .= "<div id=\"subtitle_operations_{$block['id']}\" class=\"box_subtitle_operations\">{$block['subtitle_operations']}</div>";
                    	$tmp_html .= "</div>";
                    }
                    
                    if(!$block['is_operations']&&!$block['is_promotional']) {
                    	
                    	// IE bug fix
                    	if($block['location']==$this->Sidebar) 
                        	$tmp_html .= "<div class=\"box_mid box_mid_sidebar_ie_width_fix\">";
                        else 
                        	$tmp_html .= "<div class=\"box_mid\">";
					
                        if(!$block['is_userdependent']) 
							$tmp_html .= "<div class=\"content\" id=\"{$block['id']}\">";
						else
							$tmp_html .= "<div class=\"content userdependent\" id=\"{$block['id']}\">";
                        
						$tmp_html .= $block['content'];
                        $tmp_html .= "<br /></div>";
                        $tmp_html .= "</div>";
                        $tmp_html .= "</div>";
                        $tmp_html .= "<div class=\"box_bottom\">";
                        $tmp_html .= "<span class=\"box_bottom_right\"></span>";
                        $tmp_html .= "<span class=\"box_bottom_left\"></span>";
                        $tmp_html .= "</div>";
                    }
                    else {
                    	
                        if($block['is_operations']) {
                        	
                        	// IE bug fix
                    		if($block['location']==$this->Sidebar) 
                            	$tmp_html .= "<div class=\"box_mid_ops box_mid_sidebar_ie_width_fix\">";
                            else 
                            	$tmp_html .= "<div class=\"box_mid_ops\">";
                            	
                        }
                        elseif($block['is_promotional']) {
                        	
                        	// IE bug fix
                    		if($block['location']==$this->Sidebar) 
                            	$tmp_html .= "<div class=\"box_mid_promo box_mid_sidebar_ie_width_fix\">";
                            else 
                            	$tmp_html .= "<div class=\"box_mid_promo\">";
                            
                        }
                            
                        $tmp_html .= "<div class=\"content\" id=\"{$block['id']}\">";
                        $tmp_html .= "{$block['content']}";
                        $tmp_html .= "<br /></div>";
                        $tmp_html .= "</div>";
						$tmp_html .= "<div class=\"box_bottom_ops\"> </div>";
						$tmp_html .= "</div>";
                        
                    }
					
                    if(!$block['is_operations']&&!$block['is_promotional']) {
						$tmp_html .= "</div>\n";
					}
				}	
				
				if(!isset($block_html[$block['location']])) {
					$block_html[$block['location']] = '';
				}
					
				$block_html[$block['location']] .= $tmp_html;
				
				
			}
				
			$tmp_html = '';
			
			if(isset($block_html[$this->Column])&&!empty($block_html[$this->Column])) {
				$tmp_html .= "<div class=\"col_full\">\n";
				$tmp_html .= $block_html[$this->Column];
				$tmp_html .= "</div>\n";
			}
			
			switch($this->layout) {
			
				case $this->EqualColumns:
					$tmp_html .= "<div class=\"col_left\">\n";
					$tmp_html .= $block_html[$this->LeftColumn];
					$tmp_html .= "</div>\n";
					$tmp_html .= "<div class=\"col_right\">\n";
					$tmp_html .= $block_html[$this->RightColumn];
					$tmp_html .= "</div>\n";
					break;
				case $this->WithSidebar:
					$tmp_html .= "<div class=\"col_main\">\n";
					$tmp_html .= $block_html[$this->MainColumn];
					$tmp_html .= "</div>\n";
					$tmp_html .= "<div class=\"col_side\">\n";
					$tmp_html .= $block_html[$this->Sidebar];
					$tmp_html .= "</div>\n";		
					break;

			}
			
			if(isset($block_html[$this->InvisibleArea])) {
				$tmp_html .= $block_html[$this->InvisibleArea];
			}
			
			/**
			 * should we make _filter_res_var($tmp_html);
			 * here..
			 * this is thw whole html!
			 * so we don't touch it for now..
			 */			
			
			$this->html = $tmp_html;
			
			return true;
		}
		
	}
	
	function getHTML() {
		
		/**
		 * no _filter_res_var($tmp_html); here
		 * 
		 */			
			
		return $this->html;
	}

}

?>