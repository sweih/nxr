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

	class Plain extends AbstractDesign {
		
		var $pathToRoot;
		var $prefix;
		var $mainmenu;
		
		/**
		 * Returns the name and the description of the DesignClass for Backoffice adjustments.
		 *
		 * @return string
		 */
		function getName() {
			return "Plain";
		}
		
		/**
		 *  Draw the Footer
		 *
		 */
		function getFooter() {
		  global $cds, $c, $db;

		  br();
		  br();
		  include $cds->path.'inc/foot1.php';
		  br();
		  echo '</div>';	      		  
		  echo '</div>';
		  echo '<div id="footer">';
		  		  
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
		  echo $cds->content->getByAccessKey("footermessage");
		  br();
          echo 'Page designed by <a href="http://www.jameskoster.co.uk" target="_blank">James Koster</a>  &nbsp; Powered by <a href="http://www.nxsystems.org" target="_blank">N/X CMS</a>.' ;
          echo '</div>';
		  echo '</center>';
		  /*
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

		  echo $cds->content->getByAccessKey('footermessage');
		  br();
		  echo 'Design by <a href="http://www.nodethirtythree.com/" target="_blank">NodeThirtyThree Design</a>. &nbsp; Powered by <a href="http://www.nxsystems.org" target="_blank">N/X CMS</a>.';
		  
		  echo  '</div>';
		 */
		}

	/**
  	 * Draw the Header
  	 */
  	function getHeader() {
  	  global $cds;
  	  echo '<a name="top" id="top"></a>';
	  echo '<center>';
	  echo '<div id="menu">';
	  
	  $this->pathToRoot = $this->cds->menu->getPathToRoot();
  	  $startMenu = $this->cds->menu->getMenuByPath("/");  	  
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
 			echo '<a href="'.$link.'" '.$add.'>'.$title.'</a> ';
       }	 	
	  	  
     echo $cds->layout->spacer(100,1);
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
	  
	  echo '<div id="header">';
	  echo '<h1>'.$cds->content->getByAccessKey("title").'</h1>';
	  echo '<h2>'.$cds->content->getByAccessKey("subtitle").'</h2>';
	  echo '</div>';
	  
	  echo '<div id="content">';
      echo '<a href="'.$cds->docroot.'">'.$cds->content->getByAccessKey('logo', array('class' => 'logo')).'</a>';
	  echo '<p class="introduction">';
	  include $cds->path.'inc/side1.php';					
	  echo '</p>';
	  echo '<div id="sidebar"><h1>'.$cds->content->getByAccessKey('submenutitle').'</h1>';
	  echo '<div class="submenu">';
      $this->drawSubMenu();								 
	  echo '</div>';					
	  br();
	  br();
	  include $cds->path.'inc/side2.php';				
	  echo '</div><div id="mainbar">';
	  
	  
  	  /*
  	  echo '<div id="header">';
  	  echo '<div id="header_inner" class="fluid">';
	    echo '<div id="headerlinks">';
	 
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
  		
  	  */
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
  	     	      
  	      $out.= '<a '.$add.' href="' . $href . '"';
  	      if ($isPopup)
  	    	  $out.=' target="_blank"';
  	      $out.=' '.$add.'>';
  	      $out.= $this->prefix.$title;
  	      $out.= '</a>';  	   
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
  			$tag = '<link href="'.$this->docroot().'css/1.css" rel="stylesheet" type="text/css" media="screen, projection, print">'; 			  			
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