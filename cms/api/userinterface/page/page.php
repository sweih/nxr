<?
	/**********************************************************************
	 *	N/X - Web Content Management System
	 *	Copyright 2002 Sven Weih, FZI Research Center for Information Technologies
	 *	www.fzi.de
	 *
	 *	This file is part of N/X.
	 *	The initial has been setup as a small diploma thesis (Studienarbeit) at the FZI.
	 *	It was be coached by Prof. Werner Zorn and Dipl.-Inform Thomas Gauweiler.
	 *
	 *	N/X is free software; you can redistribute it and/or modify
	 *	it under the terms of the GNU General Public License as published by
	 *	the Free Software Foundation; either version 2 of the License, or
	 *	(at your option) any later version.
	 *
	 *	N/X is distributed in the hope that it will be useful,
	 *	but WITHOUT ANY WARRANTY; without even the implied warranty of
	 *	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	 *	GNU General Public License for more details.
	 *
	 *	You should have received a copy of the GNU General Public License
	 *	along with N/X; if not, write to the Free Software
	 *	Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
	 **********************************************************************/

	/**
	 * The page class is used for drawing pages. It contains a containter, in which one can
	 * put Forms or other objects, providing a draw, a check and a process function.
	 * @version 1.00
	 * @package WebUserInterface
	 */
		
	class page {
		var $title;

		var $base_target;
		var $js_tag = "";
		var $js_date = false;
		var $js_win = false;
		var $js_select = false;
		var $js_tree = false;
		var $js_panel = false;
		var $js_clselect = false;
		var $js_md5 = false;
		var $js_tools = false;
		var $js_toggletd = false;
		var $container = null;
		var $menu = null;
		var $bodyAttributes="";
		var $onLoad="";
		var $tipp="";
		var $jsContainer="";
		var $headerPayload = "";
		
		/**
		   * standard constructor
		   * @param string Text, to be displayed in title-bar of the form
		   * @param string $base_target set, if you want the base target=".." tag to be used
		   */
		function page($title, $base_target = "") {
			$this->title = $title;
			$this->base_target = $base_target;
			$this->setJS("DATE");
			$this->setJS("SELECT");
			$this->setJS("WIN");
			$this->setJS("TOOLS");
			$this->onLoad="self.focus();";	
		}

		
		/**
		 * Add a string to the body tag
		 * @param string Attributestring you want to add
		 */
		function addBodyAttributes($attributes) {
			$this->bodyAttributes.= $attributes;
		}
		
		/**
		   * Use to add new Forms to the Page
		   * @param Form form-object you want to add for processing
		   */
		function add(&$item) {
			$next = count($this->container);

			$this->container[$next] = &$item;
		}
		
		/**
		 * Display the page with given url as iframe on the page.
		 * @param string $url URL to display.
		 */
		function setJSContainer($url) {
		  $this->jsContainer	 = $url;
		}

		/**
		   * Set the Menu to be displayed on the page.
		   * @param string Menu the menu object, you want to set for processing.
		   */
		function addMenu(&$item) {
			$next = count($this->menu);
			$this->menu[$next] = &$item;
		}
		

		/** 
		 * Creates the HTML-Output of the page for display. Calls all process and
		 * check procedures.
		   **/
		function draw() {
			global $c, $auth, $lang, $pagemenu;
			global $disableMenu, $sid;
			//load menu definitions
	        if (!$disableMenu) {
				include $c["path"] . "api/userinterface/page/menudef.php";	
		
   				if (strlen($c['smarttranslate']) > 0 ) {
					$this->add($lang->createSmartTranslateForm());
				}
	        }
	        
	        if (doc() == "index.php") {        	
	        	$forward=$pagemenu->tabbar->submenucontainer[$pagemenu->tabbar->selectedMenu][$pagemenu->tabbar->selectedSubmenu][1];	        	
	        	if ($forward != "") 
	        	  $forward = addParam($forward, "sid=$sid");
	        	  header("Location:".$c["host"].$forward);	        	
	        	  //header("Location:".$c["docroot"].'modules/nxtoday/nxtoday.php?sid='.$sid);
	        }
	        $this->process();
	        $this->draw_header();
			if (!$disableMenu) {
	          $this->draw_info_header();
			  $pagemenu->draw();
			}
			$this->_drawBody();	
			$this->draw_footer();
		}

		
		/**
		 * Process the data
		 */
		 function process() {
		 	for ($i = 0; $i < count($this->container); $i++) {				
		 		$this->container[$i]->process();
			}	
		 }
		 
		 
		 /**
		  * Draw the logoutbox, the searchbox .....
		  *
		  */
		 function draw_info_header() {
		 	global $c, $lang, $auth, $sid;		 	
		 	echo '<table width="100%" cellpadding="0" cellspacing="0" border="0">';
		 	echo '<tr>';
		 	echo '<td height="50">';
		 	echo '<img src="' .$c["docroot"]. 'img/logo.gif" alt="N/X WCMS" border="0"/>';
		 	echo '</td>';
		 	echo '<td width="70%" valign="top" style="padding-top:2px;" align="right">';
		 	echo '<a href="'.$c["livedocroot"].'" target="live">'.$lang->get("website", "Website").'</a>&nbsp;&nbsp;-&nbsp;&nbsp;';
		 	echo '<a href="'.$c["devdocroot"].'" target="dev">'.$lang->get("websiteprv", "Website preview").'</a>';
		 	
		 	echo '</td><td>'.drawSpacer(30,1).'</td>';
		 	echo '<td width="300" align="right">';
			echo '<table width="300" border="0" cellpadding="2" cellspacing="0">';
			echo '<tr>';
			echo '<td>';
			echo '<b>' . $auth->userName . '</b>&nbsp;&nbsp;-&nbsp;&nbsp;';
			echo '<a href="'.$c["docroot"] . "api/auth/logout.php?sid=$sid".'">';
			echo $lang->get("m_logout");
			echo '</a>&nbsp;&nbsp;-&nbsp;&nbsp;';
			echo '<a href="#" onClick="window.open(&quot;'.$c["docroot"].'help/help.php&quot;, &quot;gs&quot;, &quot;locationbar=no,statusbar=no,height=500,width=640&quot;);return false;">'.$lang->get("help", "Help").'</a>';			
			echo '</td>';
			echo '</tr>';
			echo '<tr>';
			echo '<td>';
			if ($auth->checkAccessToFunction("EXPLORE_SITE_S")) {
				echo '<form name="idsearch" method="post" action="'.$c["docroot"].'modules/sitepages/sitepagebrowser.php">';
				echo '<input type="text" name="oid" style="width:70px;color:#666666;" value="'.$lang->get("page_id", "Page ID").'" maxlength="7" onFocus="this.value=\'\';"/>';
				echo '&nbsp;&nbsp;';
				echo '<input type="hidden" name="sid" value="'.$sid.'"/>';
				echo '<input type="hidden" name="jump" value="1"/>';
				echo '<input type="submit" name="submit" value="'. $lang->get('search website', 'Search Website') .'"/>';
				echo '</form>';
			}
			echo '</td>';
			echo '</tr>';
			echo '</table>';
	 	
		 	echo '</td>';
		 	echo '</tr>';
		 	echo '</table>';	 			 	
		 }
		 
		 /**
		 * Draw Menu and page body
		 */
		function _drawBody() {
		   global $lang, $c;			   
		   if ($this->jsContainer == "") {
  		   echo '<div class="contentcenter"><br/><br/></div>';
  	    	if (count($this->menu) > 0) {
  		  		echo '<div class="contentleft">';	
  		   		for ($i = 0; $i < count($this->menu); $i++) {		   			
  		    		  $this->menu[$i]->draw();			
  		  		}			
  		  		echo '</div>';
  	    	}
  			
  			echo '<div id="Content" style="width:600px;">';
  			for ($i = 0; $i < count($this->container); $i++) {				
  				$this->container[$i]->draw();				
  			}					
  			echo "</div>";				
  
  			/**if (($this->tipp != "") && (count($this->container)>0)) {
  			  echo '<div class="contentright">';
  			  echo getFormHeadline($lang->get('help', 'Help'));
  			  echo $this->tipp;
  			  echo '</div>';
  			}*/
  		} else {
  			br();
  			echo '<iframe src="'.$this->jsContainer.'" name="contentset" id="contentset" frameborder="0" style="width:98%;height:800px;border:0px;"></iframe>';  			
  		}
		}
		
		
		/** 
		 * Creates the HTML-Output of the page for display. Calls all process and
		 * check procedures. If process succeeds, a forward to $forwardurl is made.
		 * @param string $forwardurl URL to forward to in success
		   **/
		function drawAndForward($forwardurl) {
			global $db, $errors, $oids, $c, $go, $page_state, $lang, $auth, $disableMenu;

			if (strlen($c['smarttranslate']) > 0 ) {
				$this->add($lang->createSmartTranslateForm());
			}
			$this->process();
			
			if ($errors == "" && strtoupper($go) == "CREATE" && $page_state == "processing") {
				$db->close();

				$forwardurl = str_replace("<oid>", $oids[0], $forwardurl);
				header ("Location: " . $c["docroot"] . $forwardurl);
				exit;
			} else {				
				//	load menu definitions
	        	include $c["path"] . "api/userinterface/page/menudef.php";	
				       
	    	    $this->draw_header();
				if (!$disableMenu) {
	    	    	$this->draw_info_header();
					$pagemenu->draw();
				}
				$this->_drawBody();	
				$this->draw_footer();			  	
			}
		}
		
		/**
		 * Draw the css definitions for tabs.
		 *
		 */
		function draw_tab_css() {
			global $c;
			$css = '
			div.active1 {
				height: 4px;
				background: url(' . $c["docroot"] . '/img/alb.gif) no-repeat top left;
			}

			div.active2 {
				height: 4px;
				background: url(' . $c["docroot"] . '/img/arb.gif) no-repeat top right;
				padding: 1px 6px;
			}
			
			div.active4 {
				height: 4px;
				background: url(' . $c["docroot"] . '/img/ulb.gif) no-repeat bottom left;
			}

			div.active5 {
				height: 4px;
				background: url(' . $c["docroot"] . '/img/urb.gif) no-repeat bottom right;
				padding: 1px 6px;
			}
			
			div.inactive1 {
				height: 4px;
				background: url(' . $c["docroot"] . '/img/ilb.gif) no-repeat top left;
			}

			div.inactive2 {
				height: 4px;
				background: url(' . $c["docroot"] . '/img/irb.gif) no-repeat top right;
				padding: 0 4px;
			}
			';
			echo '<style type="text/css">';
			echo $css;
			echo '</style>';
		}

		/**
		   * Used, to include JS-Files in HTML-Header.
		   * @param String Class of JS-File you want to include. Allowed are DATE|WIN
		   */
		function setJS($js) {
			global $c;

			switch (strtoupper($js)):
				case "DATE":
					if (!$this->js_date) {
						$this->js_tag.="<link rel=\"stylesheet\" type=\"text/css\" href=\"".$c["docroot"]."ext/jscalendar/cal.css\">\n";
						$this->js_tag.="<script type=\"text/javascript\"  type=\"text/javascript\" src=\"".$c["host"].$c["docroot"]."ext/jscalendar/calendar.js\"></script>\n";
						$this->js_tag.="<script type=\"text/javascript\"  type=\"text/javascript\" src=\"".$c["host"].$c["docroot"]."ext/jscalendar/lang/calendar-en.js\"></script>\n";
						$this->js_tag.="<script type=\"text/javascript\"  type=\"text/javascript\" src=\"".$c["host"].$c["docroot"]."ext/jscalendar/calendar-setup.js\"></script>\n";			
						$this->js_date = true;
					}

					break;

				case "WIN":
					if (!$this->js_win) {
						$this->js_tag .= "<script language=\"JavaScript\" type=\"text/javascript\"  src=\"" . $c["host"].$c["docroot"] . "api/js/windows.js\"></script>\n";

						$this->js_win = true;
					}

					break;

				case "CLSELECT":
					if (!$this->js_clselect) {
						$this->js_tag .= "<script language=\"JavaScript\" type=\"text/javascript\" src=\"" .$c["host"]. $c["docroot"] . "api/js/clselect.js\"></script>\n";

						$this->js_clselect = true;
					}

					break;

				case "TREE":
					if (!$this->js_tree) {
						$this->js_tag .= "<script language=\"JavaScript\"  type=\"text/javascript\" src=\"" . $c["host"].$c["docroot"] . "api/js/treemenu.js\"></script>\n";

						$this->js_select = true;
					}

					break;

				case "SELECT":
					if (!$this->js_select) {
						$this->js_tag .= "<script language=\"JavaScript\"  type=\"text/javascript\" src=\"" .$c["host"]. $c["docroot"] . "api/js/selectbox.js\"></script>\n";

						$this->js_select = true;
					}

					break;
				case "TOOLS":
					if (!$this->js_tools) {
						$this->js_tag .= "<script language=\"JavaScript\"  type=\"text/javascript\" src=\"" .$c["host"]. $c["docroot"] . "api/js/tools.js\"></script>\n";
						$this->js_tools = true;
					}

					break;

				case "PANELFORM":
					if (!$this->js_panel) {
						$this->js_tag .= "<script language=\"JavaScript\"  type=\"text/javascript\" src=\"" . $c["host"].$c["docroot"] . "api/js/panelform.js\"></script>\n";

						$this->js_select = true;
					}

					break;

				case "FCKLIB":
					if (!$this->js_fcklib) {
						$this->js_tag .= "<script language=\"JavaScript\"  type=\"text/javascript\" src=\"" .$c["host"]. $c["docroot"] . "api/js/fckeditor.js\"></script>\n";

						$this->js_fcklib = true;
					}

					break;

				case "MD5":
					if (!$this->js_md5) {
						$this->js_tag .= "<script language=\"JavaScript\"  type=\"text/javascript\" src=\"" . $c["host"].$c["docroot"] . "api/js/md5.js\"></script>\n";

						$this->js_md5 = true;
					}

					break;
				case "TOGGLETD":
					if (!$this->js_toggletd) {
						$this->js_tag .= "<script language=\"JavaScript\"  type=\"text/javascript\" src=\"" . $c["host"].$c["docroot"] . "api/js/toggletd.js\"></script>\n";
						
						$this->js_toggletd = true;	
					}
			endswitch;
		}

		/**
		  * do not call manually.
		  */
		function draw_header() {
			global $c, $auth, $nx_version;			
			echo "<html>";
			echo "<head>";
			echo "<title>N/X WCMS Backoffice v".$nx_version." - $this->title (".$c["database"].")</title>";	
			echo "<meta http-equiv=\"Pragma\" content=\"no-cache\">\n";
			echo "<meta http-equiv=\"Cache-Control\" content=\"no-cache, must-revalidate\">";
			echo "<meta http-equiv=\"Expires\" content=\"0\">\n";
			echo '<meta http-equiv="Content-Type" content="'.$c["standardencoding"].'">'."";		
			$this->draw_tab_css();
			echo '<link rel="stylesheet" type="text/css" href="' . $c["docroot"] . 'css/styles.css">';					
			echo $this->js_tag;			
			echo $this->headerPayload;
			echo "</head>\n";
			echo "<body leftmargin=\"5\" topmargin=\"5\" marginheight=\"0\" marginwidth=\"0\" onLoad=\"$this->onLoad\" $this->bodyAttributes>\n";

			if ($this->base_target != "")
				echo "<base target = \"$this->base_target\">"; 			 
		}

		/**
		   * do not call manually.
		   */
		function draw_footer() {	
			if ($this->base_target != "")
			  echo '</base>';
			  echo $errors;
			echo "</body></html>";
		}
	}
?>