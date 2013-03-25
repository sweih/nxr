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

	class Sinorca extends AbstractDesign {
		
		var $pathToRoot;
		var $mainmenu;
		
		/**
		 * Returns the name and the description of the DesignClass for Backoffice adjustments.
		 *
		 * @return string
		 */
		function getName() {
			return "Sinorca";
		}
		
		/**
		 *  Draw the Footer
		 *
		 */
		function getFooter() {
		  global $cds;
		  br();
		  br();
		  include $cds->path.'inc/foot1.php';  
		  echo '</div>';
		  echo '<div id="footer"><div class="left">'.$cds->content->getByAccessKey("footermessage");
	      echo '</div>';
		  echo '<br class="doNotDisplay doNotPrint" />';
		  echo '<div class="right">';
 		   // footerlinks
		  $links = $cds->menu->getMenuByPath('/FooterLinks');
  	      if (is_object($links)) {  	  	
  	  	     $links = $links->lowerLevel();
  	  	     if (is_array($links)) {
  	  		    for ($i=0; $i<count($links); $i++) {  	  			
  	  			  echo $links[$i]->getTag().'&nbsp;';
  	  			  if ($i < (count($links)-1)) echo '|&nbsp;';
  	  		    }
  	  	     }
  	      }  	  	
		  echo '</div></div>';		 		  		
		}

	/**
  	 * Draw the Header
  	 */
  	function getHeader() {
  	  global $cds;
      echo '<div id="header"><div class="superHeader"><div class="right">';
      // headerlinks
  	  $links = $cds->menu->getMenuByPath('/HeaderLinks');
  	  if (is_object($links)) {  	  	
  	  	$links = $links->lowerLevel();
  	  	if (is_array($links)) {
  	  		for ($i=0; $i<count($links); $i++) {
  	  			echo $links[$i]->getTag();
  	  			if ($i < (count($links)-1)) echo '&nbsp;|&nbsp;';
  	  		}
  	  	}
  	  }    
      echo '</div></div>';
	  echo '<div class="midHeader">';
      include $cds->path.'inc/head1.php'; 
      echo '</div>';

      // Main Navigation
      echo '<div class="subHeader">';
     
     $this->pathToRoot = $this->cds->menu->getPathToRoot();
     $startMenu = $this->cds->menu->getMenuByPath("/");  	  
     $firstLevelMenues = $startMenu->sameLevel();
     $topMenu = array_pop($this->pathToRoot);
     if ($topMenu == null) $topMenu = $startMenu;
		for ($i=0; $i < count($firstLevelMenues); $i++) {			
 			$title = $firstLevelMenues[$i]->getTitle();
 			$link  = $firstLevelMenues[$i]->getLink();
 			$isPopup = $firstLevelMenues[$i]->isPopup();
 				
 			// setup formating for active menu
 			$add="";
 			if ($firstLevelMenues[$i]->pageId == $topMenu->pageId) {
 				  $add = ' class="highlight" ';
 				  $this->mainmenu = $firstLevelMenues[$i];
 			}
			$tag = '<a '.$add.'href="'.$link.'" ';
 			if ($isPopup) {
 			  $tag.= ' target="_blank"'; 				  
 			} 				
 			$tag.='>'.$this->prefix.$title.'</a>'; 				
			echo $tag; 				
			if ($i < (count($firstLevelMenues)-1)) echo '&nbsp;|&nbsp;';
 		}
        
      echo '</div>';
      echo '<div id="side-bar"><div>';
        
      echo '<p class="sideBarTitle">'.$cds->content->getByAccessKey("submenutitle").'</p>';
      echo '<ul>';
      $this->drawSubMenu($this->mainmenu,1);
      echo '</ul>';
      echo '</div>';
      echo '<div class="lighterBackground">';
      include $cds->path.'inc/side1.php';
      echo '</div></div>';
      echo '<div id="main-copy">';
      echo '<div id="side-right">';
	  include $cds->path.'inc/side2.php'; 
	  echo '</div>';
		       
  	}
  	

		/**
		 * Draw a submenu
		 *
		 * @param object $startPage Menu-Object
		 * @param integer $level depth of menu
		 */
	function drawSubMenu($mainmenu, $level) {

	  if (is_object($mainmenu)) {
	  $menues = $mainmenu->lowerLevel();  	 
  	  if (is_array($this->pathToRoot))
  	    $submenu = array_pop($this->pathToRoot);
  	    $activeSubmenu = $submenu->pageId;
		  
		$max = count($menues)-1;
  	    for ($i=0; $i < count($menues); $i++) {  	 
  	      $href    = $menues[$i]->getLink();
  	      $title   = $menues[$i]->getTitle();
  	      $isPopup = $menues[$i]->isPopup();
  	      $add = "";

  	      if ($i == $max) {
  	      	$add2 = 'class="bottom"';
  	      }
  	      if ( $activeSubmenu == $menues[$i]->pageId)
  	    	$add = 'class="menuactive"';
  	    	// inaktives Submenu

  	      echo  '<li '.$add2.'>';
  	      echo  '<a '.$add.' href="' . $href . '"';
  	      if ($isPopup)
  	    	  echo ' target="_blank"';
  	      echo ' '.$add.'>';
  	      echo  $this->prefix.$title;
  	      echo  '</a>';
  	      if ($activeSubmenu == $menues[$i]->pageId && $level < 3) {
  	    		if ($menues[$i]->hasLowerLevel()) {
  	      	    	echo  '<ul>';
  	    			echo  $this->drawSubMenu($menues[$i], $level+1);  	    		
  	    			echo  '</ul>';
  	    		}
  	      }
  	      echo  '</li>';	    	
  	    
		}		
	  }
	 }
  	
  	
  	/**
  	 * Setup the CSS for the Tabmenu
  	 *
  	 * @param unknown_type $layout
  	 */
  	function setupPage(&$layout)	{
  			global $c; 
  			$tag='    <link rel="stylesheet" type="text/css" href="'.$this->docroot().'sinorca-screen.css" media="screen" title="Sinorca (screen)" />
    <link rel="stylesheet alternative" type="text/css" href="'.$this->docroot().'sinorca-screen-alt.css" media="screen" title="Sinorca (alternative)" />
    <link rel="stylesheet" type="text/css" href="'.$this->docroot().'sinorca-print.css" media="print" />';
			$layout->addToHeader($tag);
  	}
  	
  	
  	/**
  	 * Edit-Configuration for the Tab-Menu, esp. Colors.
  	 *
  	 * @param SettingsForm $settingsForm
  	 */
  	function editConfiguration(&$settingsForm) {
  	  global $lang;
		// @todo: Add hint to edit colors and with in css-files  
  	}
	}
?>