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

	class Transparentia extends AbstractDesign {
		
		var $pathToRoot;
		var $prefix;
		
		/**
		 * Returns the name and the description of the DesignClass for Backoffice adjustments.
		 *
		 * @return string
		 */
		function getName() {
			return "Transparentia";
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
		  br();
		  echo '</div></div><div class="sidenav">';

	     $this->pathToRoot = $this->cds->menu->getPathToRoot();
  	     $startMenu = $this->cds->menu->getMenuByPath("/");  	  
         $firstLevelMenues = $startMenu->sameLevel();

  	     $topMenu = array_pop($this->pathToRoot);
         if ($topMenu == null) $topMenu = $startMenu;
					 
 	     for ($i=0; $i < count($firstLevelMenues); $i++) {			
 				$title = $firstLevelMenues[$i]->getTitle();
 				$link  = $firstLevelMenues[$i]->getLink();
 				$isPopup = $firstLevelMenues[$i]->isPopup();
 				 			
 				// build a-tag 		
 				$tag = '<a '.$add.'href="'.$link.'" ';
 				if ($isPopup) {
 				  $tag.= ' target="_blank"'; 				  
 				} 				
 				$tag.='>'.$title.'</a>'; 				
 				echo '<h1>'.$tag.'</h1>';
				if ($firstLevelMenues[$i]->hasLowerLevel()) { 				 				 				   				
					echo  $this->drawSubMenu($firstLevelMenues[$i]);
 				 }
 				
 		}			
  						  
		  
		echo '</div>';		
		echo '<div class="clearer"><span></span></div></div>';		
		echo '<div class="footer">';
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
	br();	
		echo $cds->content->getByAccessKey('footermessage').' Template design by <a href="http://templates.arcsin.se">Arcsin</a>.
	Powered by <a href="http://www.nxsystems.org" target="_blank">N/X CMS</a>
	</div></div>';  	   	 
		}

	/**
  	 * Draw the Header
  	 */
  	function getHeader() {
  	  global $cds;
  	  
      echo '<div class="container">';
	  echo '<div class="main">';
	  echo '<div class="header">';
	  echo '<div class="headerlinks">';
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
	  echo '</div>'; 
	  echo '<div class="title">';
      include $cds->path.'inc/head1.php';				
      echo '</div></div>';	
      echo '<div class="sidebar">';
		include $cds->path.'inc/side1.php';
		br();
		br();
		include $cds->path.'inc/side2.php';
	  echo '</div>';						
	  echo '<div class="content">';
	  echo '<div class="item">';
  	}
  	

		/**
		 * Draw a submenu
		 *
		 * @param object $startPage Menu-Object
		 * @param integer $level depth of menu
		 */
	function drawSubMenu($mainmenu) {
      echo '<ul>';
	  $menues = $mainmenu->lowerLevel();  	 
  	  if (is_array($this->pathToRoot))
  	    $submenu = array_pop($this->pathToRoot);
  	    $activeSubmenu = $submenu->pageId;
		  
		$max = count($menues)-1;
  	    for ($i=0; $i < count($menues); $i++) {  	 
  	      $href    = $menues[$i]->getLink();
  	      $title   = $menues[$i]->getTitle();
  	      $isPopup = $menues[$i]->isPopup();   

  	      echo  '<li '.$add2.'>';
  	      echo  '<a href="' . $href . '"';
  	      if ($isPopup)
  	    	  echo ' target="_blank"';
  	      echo ' '.$add.'>'.$title.'</a>';
  	      if ($activeSubmenu == $menues[$i]->pageId) {  	    		
  	    	echo  $this->drawSubMenu($menues[$i]);  	    		  	    		
  	      }
  	      echo  '</li>';	    	
  	    
		}
		echo '</ul>';		
	 }
  	
  	
  	/**
  	 * Setup the CSS for the Tabmenu
  	 *
  	 * @param unknown_type $layout
  	 */
  	function setupPage(&$layout)	{
  			global $c; 
  			$tag = '<link href="'.$this->docroot().'default.css" rel="stylesheet" type="text/css" media="screen, projection, print">'; 			  			
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