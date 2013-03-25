<?php
	/**********************************************************************
	 *	N/X - Web Content Management System
	 *	Copyright 2002- 2006 Sven Weih, FZI Research Center for Information Technologies
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

	class Tabmenu extends AbstractDesign {
		
		var $pathToRoot;
		
		/**
		 * Returns the name and the description of the DesignClass for Backoffice adjustments.
		 *
		 * @return string
		 */
		function getName() {
			return "tabmenu";
		}
		
		function getTitle() {
			 return "Tabbed Menu";
		}

	
	/**
  	 * Draw the tabbar.
  	 */
  	function getHeader() {
  	  global $cds;
  	  echo '<div id="maindiv">';
  	  // get the path of menues, e.g. if a thrid-level page is active you 
  	  // get the corresponding3rd/2nd/1st level menues
  	   
  	  echo '<table width="100%" border="0" cellpadding="0" cellspacing="0">';
  	  echo '<tr><td width="60%" valign="top">';
  	  include $cds->path.'inc/head1.php';
  	  echo '</td><td valign="top" align="right" width="40%">';
  	  // headerlinks
  	  $links = $cds->menu->getMenuByPath('/HeaderLinks');
  	  if (is_object($links)) {  	  	
  	  	$links = $links->lowerLevel();
  	  	if (is_array($links)) {
  	  		for ($i=0; $i<count($links); $i++) {
  	  			echo $links[$i]->getTag().'&nbsp;&nbsp;';
  	  		}
  	  	}
  	  }
  	  echo '</td></tr></table><br>';
  	  $this->pathToRoot = $this->cds->menu->getPathToRoot();
  	  // get the actice toplevelmenu
	  // get the startpage
  	  $startMenu = $this->cds->menu->getMenuByPath("/");  	  
      // get the first menu level.
  	  $firstLevelMenues = $startMenu->sameLevel();

  	  $topMenu = array_pop($this->pathToRoot);
      if ($topMenu == null) $topMenu = $startMenu;
  	  
  	  echo  '<table cellpadding="0" width="100%" cellspacing="0" border="0">';
  	  echo  '<tr>';
  	   for ($i=0; $i < count($firstLevelMenues); $i++) {
  	  	 $this->drawTab($firstLevelMenues[$i], $firstLevelMenues[$i]->pageId == $topMenu->pageId, $out);
  	  }
  	  echo  '<td width="100%">&nbsp;</td>';
  	  echo  '</tr>';
  	  echo '</table>';	
  	  echo  '<table border="0" align="center" width="100%" cellpadding="0" cellspacing="0" id="subTabs">';
  	  echo  '<tr><td class=>';	  
  	  $this->drawSubTabs($topMenu, $out);  
      echo '</td></tr></table><br/>';
      echo '<div id="content">';
  
  	  return $out;
  	}
  	
  	function getFooter() {
  		global $cds;
  		echo '</div>';
  		echo '<div id="sidebar">';
  		include $cds->path.'inc/side1.php';
  		br();
  		include $cds->path.'inc/side2.php';  		
  		echo '</div>';
  		echo '<div id="footerbox">';
  		br(); br();
  		include $cds->path.'inc/foot1.php';  		
  		echo '</div>';
  		br();br();
  		echo '<div id="footerline">';
  		echo '<table width="100%" border="0" cellpadding="0" cellspacing="0">';
  		echo '<tr><td width="30%">';
  		 // footerlinks
			$links = $cds->menu->getMenuByPath('/FooterLinks');
  	  if (is_object($links)) {  	  	
  	  	$links = $links->lowerLevel();
  	  	if (is_array($links)) {
  	  		for ($i=0; $i<count($links); $i++) {  	  			
  	  			echo $links[$i]->getTag().'&nbsp;';
  	  			if ($i < (count($links)-1)) echo '-&nbsp;';
  	  		}
  	  	}
  	  }  	  	  
  	  echo '</td><td width="40%" align="center">'.$cds->content->getByAccessKey("footermessage");
  	  echo '</td><td width="30%" align="right">Powered by <a href="http://www.nxsystems.org" target="_blank">N/X CMS</a></td></tr></table>';
  		echo '</div>';
  		
  	}
  	
  	/**
  	 * Draw a single tab element
  	 *
  	 * @param integer $position Position of the tab in the menuarray
  	 * @param string $out String to add the tab.
  	 */
  	function drawTab($mainmenu, $active, &$out) {  	  
  	  $href = $mainmenu->getLink();
  	  $title   = $mainmenu->getTitle();  	  
  	  $isPopup = $mainmenu->isPopup(); 
  	  
  	  $onclick = "document.location.href='$href'"; 		
  	  
  	  if ($active) {
  	    // active tab	
  	    echo  '<td nowrap class="activeTab">';
  	    echo  '<div class="active1"><div class="active2"><div class="active3"></div></div></div>';
  	    echo  '<div class="activeTabText">&nbsp;&nbsp;<a href="'.$href.'">';
  	    echo  $title;
  	    echo  '</a>&nbsp;&nbsp;</div>';
  	    echo  '</td>';
  	  } else {
  	  	// inactive tab  	  	
  	  	echo  '<td nowrap class="inactiveTab" onclick="'.$onclick.'">';
  	  	echo  '<div class="inactive1"><div class="inactive2"><div class="inactive3"></div></div></div>';
  	    echo  '<div class="inactiveTabText">&nbsp;&nbsp;<a href="'.$href.'" >';
  	    echo  $title;
  	    echo  '</a>&nbsp;&nbsp;</div>';
  	    echo  '</td>';  	    
  	  }  	  
  	  echo  '<td>&nbsp;&nbsp;</td>';
  	}
  	
  	/**
  	 * Draw the submenu
  	 *
  	 * @param string $out String where to add the menu
  	 */
  	function drawSubTabs($mainmenu, &$out) {  	   
  	  $menues = $mainmenu->lowerLevel();  	 
  	  if (is_array($this->pathToRoot))
  	    $submenu = array_pop($this->pathToRoot);
  	  $activeSubmenu = $submenu->pageId;
  	  
  	  for ($i=0; $i < count($menues); $i++) {  	 
  	    $href    = $menues[$i]->getLink();
  	    $title   = $menues[$i]->getTitle();
  	    $isPopup = $menues[$i]->isPopup();
  	  	
  	    if ( $activeSubmenu == $menues[$i]->pageId) {
  	    	// aktives Submenu
  	    	echo  '<span class="activeText">';
  	    	echo  $title;
  	    	echo  '</span>';
  	    } else {
  	    	// inaktives Submenu
  	    	echo  '<span class="inactiveText">';
  	    	echo  '<a href="' . $href . '"';
  	    	if ($isPopup)
  	    	  echo ' target="_blank"';
  	    	echo '>';
  	    	echo  $title;
  	    	echo  '</a>';
  	    	echo  '</span>';	    	
  	    }
  	    if ($i < count($menues)-1) 
  	      echo  ' | ';
  	  }    	
  	} 
  	
  	
  	/**
  	 * Setup the CSS for the Tabmenu
  	 *
  	 * @param unknown_type $layout
  	 */
  	function setupPage(&$layout)	{
  			global $c;  		
			  $tag = '<link href="'.$this->docroot().'style.css" rel="stylesheet" type="text/css" media="screen, projection, print">'; 			  			
			  $layout->addToHeader($tag);
  	}  	  	
	}
?>