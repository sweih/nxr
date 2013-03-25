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

	class DropdownSideMenu extends AbstractDesign {
		
		var $pathToRoot;
		var $themeName;
		/**
		 * Returns the name and the description of the DesignClass for Backoffice adjustments.
		 *
		 * @return string
		 */
		function getName() {
			return "Dropdown Menu with Side Menu";
		}


	/**
  	 * Draw the menu.
  	 */
  	function getHeader() {
  	  global $cds;
  	  // get the path of menues, e.g. if a thrid-level page is active you 
  	  // get the corresponding3rd/2nd/1st level menues
	   echo '<div id="maindiv">';  
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
  	  echo '</td></tr></table>';
	  
  	  $this->pathToRoot = $this->cds->menu->getPathToRoot();
  	  // get the actice toplevelmenu
	  // get the startpage
  	  $startMenu = $this->cds->menu->getMenuByPath("/");  	  
      // get the first menu level.
  	  $firstLevelMenues = $startMenu->sameLevel();

  	  $topMenu = array_pop($this->pathToRoot);
      if ($topMenu == null) $topMenu = $startMenu;
       
     echo'<div id="mainmenu"><div id="mainmenuleft"></div><div id="mainmenucenter">';
      echo  "
	  <SCRIPT type='text/javascript'>
		var dsMenu =
		[";
    
 	 
  	  for ($i=0; $i<count($firstLevelMenues); $i++) {
			echo $this->drawMenu($firstLevelMenues[$i]); 	
			if (($i+1) < count($firstLevelMenues))  	  	
			  echo ',';
  	  }
  	  echo ']; </SCRIPT><DIV ID="dsMenu"></DIV>

	<SCRIPT type="text/javascript">cmDraw ("dsMenu", dsMenu, "hbr", cmTheme'.$this->themeName.', "Theme'.$this->themeName.'");</SCRIPT>';
    echo  '</div><div id="mainmenuright"></div></div>';
  	echo '<div id="sidemenu">';
  	if ($cds->menu->hasLowerLevel()) {
  	  echo '<h4>'.$cds->content->getByAccessKey("submenutitle").'</h4>';  	  
  	  $menues = $cds->menu->lowerLevel();
  	  for ($i=0; $i < count($menues); $i++) {
  	  	echo $menues[$i]->getTag();
  	  	br();
  	  }  	  
  	}
  	echo '</div>';
    echo '<div id="content"><br>';
  	}
  	
  	function getFooter() {
  		global $cds;
  		echo '</div>';
  		echo '<div id="sidebar">';
  		include $cds->path.'inc/side1.php';
  		br(); br();
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
  		echo '</div></div>';
  	}
  	

  /**
   * Draw menustructure
   *
   * @param menuObject $menuObject
   */
  function drawMenu($menuObject) {
    $title = $menuObject->getTitle();
 	$link  = $menuObject->getLink();
 	$isPopup = $menuObject->isPopup();
	
 	if ($isPopup)  {
		$target = "'_blank'";
	} else {
		$target="null";
	}
  	echo  "['', '$title', '$link', $target, ''";
  	if ($menuObject->hasLowerLevel()) {
  	   echo ',';
  		$lowerLevels = $menuObject->lowerLevel();
  	   for ($i=0; $i<count($lowerLevels); $i++)	{  	   	
  	   	 echo $this->drawMenu($lowerLevels[$i]); 	
		 if (($i+1) < count($lowerLevels))  	  	
		   echo ',';
  	   }
  	}
  	
  	echo  "]";
	
    return $out;						   	
   }
  	
  	/**
  	 * Setup the CSS for the Tabmenu
  	 *
  	 * @param unknown_type $layout
  	 */
  	function setupPage(&$layout)	{ 
  		global $cds; 						
  		$this->themeName = 'NX';
  		echo  '<SCRIPT type="text/javascript" LANGUAGE="JavaScript" SRC="'.$this->docroot().'JSCookMenu.js"></SCRIPT>
<LINK REL="stylesheet" HREF="'.$this->docroot().'Theme'.$this->themeName.'/theme.css" TYPE="text/css"/>
<LINK REL="stylesheet" HREF="'.$this->docroot().'styles.css" TYPE="text/css"/>
<SCRIPT LANGUAGE="JavaScript" type="text/javascript" SRC="'.$this->docroot().'Theme'.$this->themeName.'/theme.js"></SCRIPT>  		
  		';
  		$layout->addToHeader($out);
  	}
  	
	}
?>