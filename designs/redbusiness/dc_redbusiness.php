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

	class RedBusiness extends AbstractDesign {
		
		var $pathToRoot;
		var $prefix;
		var $mainmenu;
		
		/**
		 * Returns the name and the description of the DesignClass for Backoffice adjustments.
		 *
		 * @return string
		 */
		function getName() {
			return "RedBusiness";
		}
		
		/**
		 *  Draw the Footer
		 *
		 */
		function getFooter() {
		  global $cds, $c, $db;
		  echo '</div></div>';
		  include $cds->path.'inc/foot1.php';
		  br();
		  echo '<div id="footer">';
	    echo '<div class="right">'.$cds->content->getByAccessKey('footermessage').'<br />Design: <a href="http://www.free-css-templates.com">David Herreman</a></div>';
			
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
		  echo '</div></div>';		  
		}

	/**
  	 * Draw the Header
  	 */
  	function getHeader() {
  	  global $cds;
  	 
  	  echo '<div class="content">';
  	  echo '<div id="top">';
  	  echo '<div id="icons">';
  	  $links = $cds->menu->getMenuByPath('/HeaderLinks');
  	  if (is_object($links)) {  	  	
  	  	$links = $links->lowerLevel();
  	  	if (is_array($links)) {
  	  		for ($i=0; $i<count($links); $i++) {
  	  			echo $links[$i]->getTag().'&nbsp;&nbsp;';
  	  		}
  	  	}
  	  }  	  
  	  echo '&nbsp;&nbsp;&nbsp;&nbsp;';
  	  echo '</div>';
			include $cds->path.'inc/head1.php';					
  	  echo '</div>';	
  	  echo '</div>';

      echo '<div id="prec"><div id="wrap"><div id="pic"><div id="slogan">';
			include $cds->path.'inc/side1.php';
      echo '</div></div>';
      
      // menü zeichnen
		  echo '<div id="menu"><ul>';
		  
		  $this->pathToRoot = $this->cds->menu->getPathToRoot();
  	  // get the actice toplevelmenu
	  // get the startpage
  	  $startMenu = $this->cds->menu->getMenuByPath("/");  	  
      // get the first menu level.
  	  $firstLevelMenues = $startMenu->sameLevel();

  	  $topMenu = array_pop($this->pathToRoot);
      if ($topMenu == null) $topMenu = $startMenu;
		
      for ($i=0; $i < count($firstLevelMenues); $i++) {			
 			$add = "";
      	    $title = $firstLevelMenues[$i]->getTitle();
 			$link  = $firstLevelMenues[$i]->getLink();
 			$isPopup = $firstLevelMenues[$i]->isPopup();
 			
 			if ($firstLevelMenues[$i]->pageId == $topMenu->pageId)  { 			
 				  $this->mainmenu = $firstLevelMenues[$i];
 			}
 			
 			if ($isPopup) {
 				$add.= ' target="_blank" ';
 			}
 				
 			// build a-tag
 			echo '<li><a href="'.$link.'" '.$add.'>'.$title.'</a></li>';
       }	 						  		  
		  echo '</ul></div></div></div>';
  	  echo '<div class="content">';
  	  echo '<div id="main">';   	  
  	  echo '<div id="right_side_plc">';
  	  br();
  	  echo '<div id="right_side">';
  	  echo '<h2>'.$cds->content->getByAccessKey('submenutitle').'</h2>';
  	  echo $this->drawSubMenu();
  	  echo '</div><br><div id="right_side">';
  	  include $cds->path.'inc/side2.php';
  	  
  	  echo '</div></div>';
  	  echo '<div id="left_side">';
  
  	}
  	

		/**
		 * Draw a submenu		 	 
		 */
	function drawSubMenu() {
	  if (is_object($this->mainmenu)) {
	  	
	    $menues = $this->mainmenu->lowerLevel();  	 
  	    for ($i=0; $i < count($menues); $i++) {  	 
  	      $href    = $menues[$i]->getLink();
  	      $title   = $menues[$i]->getTitle();
  	      $isPopup = $menues[$i]->isPopup();
  	      $add = "";
  	   
  	      $out.= '<li>';
  	      $out.= '<a '.$add.' href="' . $href . '"';
  	      if ($isPopup)
  	    	  $out.=' target="_blank"';
  	      $out.=' '.$add.'>';
  	      $out.= $this->prefix.$title;
  	      $out.= '</a>';
  	      $out.= '</li>';	    	  	  
  	    }
	  }	    		
	  echo $out;
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