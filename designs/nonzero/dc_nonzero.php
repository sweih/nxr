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

	class NonZero extends AbstractDesign {
		
		var $pathToRoot;
		var $prefix;
		var $mainmenu;
		
		/**
		 * Returns the name and the description of the DesignClass for Backoffice adjustments.
		 *
		 * @return string
		 */
		function getName() {
			return "NonZero";
		}
		
		/**
		 *  Draw the Footer
		 *
		 */
		function getFooter() {
		  global $cds, $c, $db;
		  br();
		  include $cds->path.'inc/foot1.php';
		  echo '</div></div>';
		  echo '<div id="secondaryContent_3columns"><div id="columnB_3columns">';
		  // menu drawing.
		  echo '<h2><span>'.$cds->content->getByAccessKey('submenutitle').'</span></h2>
				<ul class="links">';
		  $this->drawSubMenu();				
		  echo  '	</ul>';
		  echo  '<br>';
		  include($cds->path.'inc/side1.php');
		  echo  '</div>';
		  echo  '<div id="columnC_3columns">';
		  include ($cds->path.'inc/side2.php');
		  echo  '</div>';		      
		  echo '<br class="clear" /> ';		  
		  echo  '</div>';		  		  
		  echo '</div>';
		  
		  echo  '<div id="footer" class="fluid">';
		  
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
		  echo $cds->content->getByAccessKey('footermessage');
		  br();
		  echo 'Design by <a href="http://www.nodethirtythree.com/" target="_blank">NodeThirtyThree Design</a>. &nbsp; Powered by <a href="http://www.nxsystems.org" target="_blank">N/X CMS</a>.';
		  
		  echo  '</div>';
		 
		}

	/**
  	 * Draw the Header
  	 */
  	function getHeader() {
  	  global $cds;
  	  echo '<div id="header">';
  	  echo '<div id="header_inner" class="fluid">';
	    echo '<div id="headerlinks">';
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
	  echo '<div id="logo">';
	  include $cds->path.'inc/head1.php';
	  echo '</div><div id="menu"><ul>';
	  
	  // get the path of menues, e.g. if a thrid-level page is active you 
  	  // get the corresponding3rd/2nd/1st level menues
  	    	  
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
 				  $add = ' class="active" ';
 				  $this->mainmenu = $firstLevelMenues[$i];
 			}
 			
 			if ($isPopup) {
 				$add.= ' target="_blank" ';
 			}
 				
 			// build a-tag
 			echo '<li><a href="'.$link.'" '.$add.'>'.$title.'</a></li>';
       }	 				
       
       
       
	   echo  '</ul></div></div></div>';
  	   echo  '<div id="main">';
  	   echo  '<div id="main_inner" class="fluid"><div id="primaryContent_3columns"><div id="columnA_3columns">';	
  		
  	  return $out;
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