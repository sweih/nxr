<?php

  /**
   * Creates a tabmenu
   */
  class CDSTabBar {
  	
 	
  	var $menucontainer = null;
		var $submenucontainer = null;		
	
		var $selectedMenu = 0;
		var $selectedSubmenu = 0;
		var $menucounter = 0;
		var $postfix;
		var $doc = "";
		var $id;
		
  
  	/**
		 * standard constructor.
		 */
		function CDSTabBar() {			
			$this->id = 'menu';
			$this->selectedMenu = 0;
			$this->selectedSubmenu = 0;			
			$this->doc = $_SERVER['REQUEST_URI'];
		}	
		
		/**
		 * Add a new tab
		 *
		 * @param string $name Title of the tab.
		 * @param string $link URL where to link to.
		 */
		function addMainTab($name, $link='') {
		  $this->menucounter++;
		  $this->menucontainer[$this->menucounter][0] = $name;	  		  
		  $this->menucontainer[$this->menucounter][1] = $link;
		  return $this->menucounter;
		} 	
		

		/**
		 * Add a new submenu tab
		 *
		 * @param integer $maintab ID of the maintab.
		 * @param string $name Title of the tab.
		 * @param string $link URL where to link to.
		 */
		function addSubTab($maintab, $name, $link='') {
		  $miniarray[0] = $name;
		  $miniarray[1] = $link;
		  $this->submenucontainer[$maintab][] = $miniarray;
		}
  	
  	/**
  	 * Draw the tabbar.
  	 */
  	function draw() {
  	  $out = '';
  	  $out.= '<table cellpadding="0" width="100%" cellspacing="0" border="0">';
  	  $out.= '<tr>';
  	  for ($i=1; $i <= $this->menucounter; $i++) {
  	  	$this->drawTab($i, $out);
  	  }
  	  $out.= '<td width="100%">&nbsp;</td>';
  	  $out.= '</tr>';
  	  $out.='</table>';	
  	  $out.= '<table border="0" align="center" width="100%" cellpadding="0" cellspacing="0" id="subtabs">';
  	  $out.= '<tr><td>';
  	  $this->drawSubTabs($out);  
      $out.='</td></tr></table>';
  
  	  return $out;
  	}
  	
  	/**
  	 * Draw a single tab element
  	 *
  	 * @param integer $position Position of the tab in the menuarray
  	 * @param string $out String to add the tab.
  	 */
  	function drawTab($position, &$out) {
  	  $onclick = $this->getHref($position);
  	  if (stristr($this->getHref($position), 'javascript:') === false) {
  	  		$onclick = "document.location.href='".$onclick."';";
  	  }  
  		
  	  if ($position == $this->selectedMenu) {
  	    // active tab	
  	    $out.= '<td nowrap id="'.$this->id.$position.'Head" class="activeTab">';
  	    $out.= '<div class="active1" id="'.$this->id.$position.'p1"><div class="active2" id="'.$this->id.$position.'p2"><div class="active3" id="'.$this->id.$position.'line"></div></div></div>';
  	    $out.= '<div class="activeTabText" id="'.$this->id.$position.'text">&nbsp;&nbsp;<a href="#" onclick="'.$onclick.';">';
  	    $out.=$this->menucontainer[$position][0];
  	    $out.= '</a>&nbsp;&nbsp;</div>';
  	    $out.= '</td>';
  	  } else {
  	  	// inactive tab  	  	
  	  	$out.= '<td nowrap class="inactiveTab" id="'.$this->id.$position.'Head" onclick="'.$onclick.'">';
  	  	$out.= '<div class="inactive1" id="'.$this->id.$position.'p1"><div class="inactive2" id="'.$this->id.$position.'p2"><div class="inactive3" id="'.$this->id.$position.'line"></div></div></div>';
  	    $out.= '<div class="inactiveTabText" id="'.$this->id.$position.'text">&nbsp;&nbsp;<a href="#" onclick="' . $onclick . '">';
  	    $out.=$this->menucontainer[$position][0];
  	    $out.= '</a>&nbsp;&nbsp;</div>';
  	    $out.= '</td>';  	    
  	  }  	  
  	  $out.= '<td>&nbsp;&nbsp;</td>';
  	}
  	
  	/**
  	 * Draw the submenu
  	 *
  	 * @param string $out String where to add the menu
  	 */
  	function drawSubTabs(&$out) {  	   
  	  for ($i=0; $i < count($this->submenucontainer[$this->selectedMenu]); $i++) {  	 
  	    if ($this->selectedSubmenu == $i) {
  	    	// aktives Submenu
  	    	$out.= '<span class="activeText">';
  	    	$out.= $this->submenucontainer[$this->selectedMenu][$i][0];
  	    	$out.= '</span>';
  	    } else {
  	    	// inaktives Submenu
  	    	$out.= '<span class="inactiveText">';
  	    	$out.= '<a href="' . $this->getHref($this->selectedMenu, $i) . '">';
  	    	$out.= $this->submenucontainer[$this->selectedMenu][$i][0];
  	    	$out.= '</a>';
  	    	$out.= '</span>';	    	
  	    }
  	    if ($i < count($this->submenucontainer[$this->selectedMenu])-1) 
  	      $out.= ' | ';
  	  }    	
  	} 

  	
  	/**
  	 * Evaluate the link to url.
  	 */
  	function getHref($mainmenu=-1, $submenu=-1) {
  		$href = $this->doc;
  		if (count($this->submenucontainer[$mainmenu])>0  && $submenu==-1)
  		  $submenu = 0;
  		if ($mainmenu > 0)  {  			  		
  			if ($submenu > -1) { 				
  			  if ($this->submenucontainer[$mainmenu][$submenu][1] != '')
  					$href = $this->submenucontainer[$mainmenu][$submenu][1];				
  			} else {
  				//MainMenutab.
  				if ($this->menucontainer[$mainmenu][1] != '') {  				
  				  $href = $this->menucontainer[$mainmenu][1];
  				} else if ($this->submenucontainer[$mainmenu][$submenu][1] != '') {
  					$href = $this->submenucontainer[$mainmenu][$submenu][1];
  				}
  			}
  		$pos =strpos($href, 'javascript'); 
  		if ( $pos === false ) {	  		
        	if (! strpos($href, '?')) {
  	  	    $href.='?';
    		  } else {
  			   $href.='&';
  		    }
  		    $href = $href . $this->postfix;
  		    $href = $this->addToQuery($this->id, $mainmenu.'x'.$submenu, $href);  				    			
  		  }
  		}
  		return $href;
  	} 
  	
  	/**
   * @return string
   * @param string $varName
   * @param string $varVal
   * @param string $uri
   * @desc Returns the a string that is either
   *        $uri if you pass it or the current
   *        uri with the variable name $varName
   *        equal to the value urlencode($varVal)
   *        It replaces a current value if it find
   *        it or adds the variable and value pair
   *        if they are new.
  */
  function addToQuery($varName, $varVal, $uri=null) {
   $result = '';
   $beginning = '';
   $ending = '';
   
   if (is_null($uri)) {//Piece together uri string
       $beginning = $_SERVER['PHP_SELF'];
       $ending = ( isset($_SERVER['QUERY_STRING']) ) ? $_SERVER['QUERY_STRING'] : '';
   } else {
       $qstart = strpos($uri, '?');
       if ($qstart === false) {
           $beginning = $uri; //$ending is '' anyway
       } else {
           $beginning = substr($uri, 0, $qstart);
           $ending = substr($uri, $qstart);
       }
   }
   
   if (strlen($ending) > 0) {
       $vals = array();
       $ending = str_replace('?','', $ending);
       parse_str($ending, $vals);
       $vals[$varName] = $varVal;
       $ending = '';
       $count = 0;
       foreach($vals as $k => $v) {
           if ($count > 0) { $ending .= '&'; }
           else { $count++; }
           $ending .= "$k=" . urlencode($v);
       }
   } else {
       $ending = $varName . '=' . urlencode($varVal);
   }
   
   $result = $beginning . '?' . $ending;
   
   return $result;
  } 	
  }

?>