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

	class NXMenu extends AbstractDesign {
		
		var $pathToRoot;
		var $prefix;
		
		/**
		 * Returns the name and the description of the DesignClass for Backoffice adjustments.
		 *
		 * @return string
		 */
		function getName() {
			return "N/X-Menu";
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
		  echo  "</td>";
		  echo '<td width="20">&nbsp;</td>';
		  echo '<td valign="top" width=200"><br><br><br><br>';
		  include $cdsp->path.'inc/side1.php';
		  br();
		  echo "</td></tr></table>";
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
  		echo '</div></div>';
  	
		}		

	/**
  	 * Draw the Header
  	 */
  	function getHeader() {
  	  	global $cds;
  		echo  '<div id="main"><table width="100%" border="0" cellpadding="0" cellspacing="0">';
  	  	echo  '<tr><td rowspan="2" width="318" valign="middle" align="left" height="180" style="padding-left:20px; background-repeat:no-repeat;background-image:url('.$this->docroot().'logoplaceholder.jpg)">';
  	  	echo  $cds->content->getByAccessKey("logo");  	  	
  	  	echo '</td>';
  	  	echo  '<td height = "30" valign="middle" align="right">';
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
  	  	echo '</td></tr>'; 	
  	  	echo '<tr><td valign="middle" align="center">';
  	  	include $cds->path.'inc/side2.php';
  	  	echo '</td></tr>';
  	  	echo  '</table>';
  	  	
		
  	  	// draw mainmenu
  	  	echo  '<table width="990" border="0" cellpadding="0" cellspacing="0">';
  	  	echo  '<tr>';
  	  	echo  '<td width="200">'.drawSpacer(200,1).'</td>';
  	  	
  	  	// get the first menu level.
  	  	$startMenu = $this->cds->menu->getMenuByPath("/");  	  
        $firstLevelMenues = $startMenu->sameLevel();
        for ($i=0; $i< count($firstLevelMenues); $i++) {
          echo  '<td class="mainmenu" align="center">';
          $title = $firstLevelMenues[$i]->getTitle();
 		  $link  = $firstLevelMenues[$i]->getLink();
 		  $isPopup = $firstLevelMenues[$i]->isPopup();
          echo  '<a class="mainmenu" href="'.$link.'" ';
          if ($isPopup) echo  'target="_blank"';
          echo  '><nobr>'.strtoupper($title).'</nobr></a>';                 
          echo  '</td>'; 	       	
        } 	  
  	  	echo  '<td width="40%" class="mainmenu">&nbsp;</td>';
        echo  '</tr>';
  	  	echo  '</table>';
  	  	echo  '<br>';
  	  	
  	  	// submenu
  	  	$this->pathToRoot = $this->cds->menu->getPathToRoot();
  	  	$topMenu = array_pop($this->pathToRoot);
        if ($topMenu == null) $topMenu = $startMenu; 
  	  	echo  '<table width="" cellpadding="0" cellspacing="0" border="0">';
  	  	echo  '<tr><td width="180" height="400" class="cellsubmenu" valign="top">'; 	  	  	  	
  	  	echo  '<table width="180" border="0" cellpadding="0" cellspacing="1">';
  	  	// submenu malen.
  	  	echo  $this->drawSubMenu($topMenu);
  	  	echo  '</table>';
  	  	echo  '</td><td width="30">&nbsp;&nbsp;</td><td valign="top" width="600">';
  	  			
  	  	 
  	
    	}
  	

		/**
		 * Draw a submenu
		 *
		 * @param object $startPage Menu-Object
		 * @param integer $level depth of menu
		 */
	function drawSubMenu($mainmenu, $level=1) {
		
	  $menues = $mainmenu->lowerLevel();  	 
  	  if (is_array($this->pathToRoot))
  	    $submenu = array_pop($this->pathToRoot);
  	    $activeSubmenu = $submenu->pageId;
		  
		$max = count($menues)-1;
  	    for ($i=0; $i < count($menues); $i++) {  	 
  	      $href    = $menues[$i]->getLink();
  	      $title   = $menues[$i]->getTitle();
  	      $isPopup = $menues[$i]->isPopup();


		  echo  '<tr><td class="submenu'.$level.'">';  	      
  	      echo  '<a '.$add.' href="' . $href . '"';
  	      if ($isPopup)
  	    	  echo ' target="_blank"';
  	      echo ' '.$add.' class="submenu'.$level.'">';
  	      echo  $this->prefix.$title;
  	      echo  '</a>';

  	      if ($activeSubmenu == $menues[$i]->pageId && $level < 3) {	
  	    	 echo  $this->drawSubMenu($menues[$i], $level+1);  	    		
  	      }
  	      echo  '</td></tr>';	    	
  	    
		}						
	 }
  	
  	
  	/**
  	 * Setup the CSS for the Tabmenu
  	 *
  	 * @param unknown_type $layout
  	 */
  	function setupPage(&$layout)	{
  			global $c; 
  			$tag = '<link href="'.$this->docroot().'menu.css" rel="stylesheet" type="text/css" media="screen, projection, print">'; 			  			
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