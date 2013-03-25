<?
	/**
     * @package CDS
     */

	/**********************************************************************
	 *	N/X - Web Content Management System
	 *	Copyright 2003 Sven Weih
	 *
	 *	This file is part of N/X.
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
	  * CDS Class for drawing menus
	  * Access this class with $cds->menu
	  */
	 class Menu {
		
	 	var $pageId = 0;
	 	var $variation = 0;
	 	var $level = 0;
	 	var $management = null;
	 	var $menuId = 0;
	 	
	 	/**
	 	 * Standard constructor. Set $parent=null, if you want to initialize manually with
	 	 * pageId and variation.
	 	 * @param object Reference to the parent container
		 * @param integer pageId of a page to set as menu start node
		 * @param integer Id of the variation to set as default
		 * @param integer Id of the current level 
	 	 */
	 	function Menu($parent, $pageId=0, $variation=0, $level=0) { 
			if ($parent != null) {
	 		   $this->pageId = $parent->pageId;
	 		   $this->variation = $parent->variation;
	 		   $this->level = $parent->level;
	 		   $this->management = $parent->management;
			} else {
				$this->pageId = $pageId;
				$this->variation = $variation;		
				$this->level = $level;
				$this->management = new Management($this);
			}
			$this->menuId = getDBCell("sitepage", "MENU_ID", "SPID = ".$this->pageId);
	 	}
	 	
	 		 	
	 	/**
	 	 * Return a Menu-Object
	 	 * @param object Reference to the parent container
		 * @param integer pageId of a page to set as menu start node
		 * @param integer Id of the variation to set as default
		 * @param integer Id of the current level 
		 */
		 function createInstance($parent, $pageId, $variation, $level) {
		 	return new Menu($parent, $pageId, $variation, $level);	
		 }
		
	 	
	/**
	 * Returns the parent menu-object of this page
	 */
	function upperLevel() {		
		$parentMenuId = getDBCell("sitemap", "PARENT_ID", "MENU_ID = $this->menuId");
		$parSP = getDBCell("sitepage", "SPID", "MENU_ID = $parentMenuId");
			
		if ($parSP == "") {
			$management = new Management($this);
			$parSP = $management->getStartPage($this->variation);
		}	
			
		return $this->createInstance(null, $parSP, $this->variation, $this->level);
	}
	
	
	/**
	 * checks, whether the current level has an upper Level
	 */
	 function hasUpperLevel() {
	    return (($this->management->getStartPage($this->variation)==$this->pageId) ? true: false);
	 }
	 	

	/**
	 * checks, whether the current level has an lower Level
	 */
	 function hasLowerLevel() {	 	
		$childs = countRows("sitepage sp, sitemap sm", "SPID", "sm.PARENT_ID = $this->menuId AND sp.MENU_ID = sm.MENU_ID AND sm.DELETED=0 AND sm.IS_DISPLAYED=1");
		if ($childs >0) {
			return true;
		} else {
			return false;
		}		
	 }
	 
	 /**
	  * checks whether there are pages on the same level of this page
	  */
	  function hasSameLevel() {
		$parentMenuId = getDBCell("sitemap", "PARENT_ID", "MENU_ID = $this->menuId");
		$childs = countRows("sitemap", "MENU_ID", "IS_DISPLAYED=1 AND DELETED=0 AND PARENT_ID = $parentMenuId");
	  	if ($childs >0) {
			return true;
		} else {
			return false;
		}
	  }
	  
	  	 
	 /**
	  * checks whether there are pages on the same level of this page
	  */
	  function hasSameLevelMultipage() {		
		$childs = countRows("sitepage", "SPID", "MENU_ID = $this->menuId AND DELETED=0");		
	  	if ($childs >0) {
			return true;
		} else {
			return false;
		}
	  }
	 
	 
	/**
	 * Returns all Pages beeing on the lower level, i.e. having the given
	 * path or SPID as parent.
	 */
	function lowerLevel() {		
		$childs = createDBCArray("sitepage sp, sitemap sm", "SPID", "sm.PARENT_ID = $this->menuId AND sp.DELETED=0 AND sp.MENU_ID = sm.MENU_ID AND sm.DELETED=0 AND sm.IS_DISPLAYED=1 ORDER BY sm.POSITION, sp.POSITION");		
		$checked = array ();
		for ($i = 0; $i < count($childs); $i++) {
			if ((($this->level == 10) &&(!$this->management->isSPExpired($childs[$i]))) || ($this->level==0)) 
				array_push($checked, $this->createInstance(null, $childs[$i], $this->variation, $this->level));
		}
		return $checked;		
	}
	
	
	/**
	 * Returns all instances of a multipage.
	 * path or SPID as parent.
	 */
	function sameLevelMultipage() {
		$res = createDBCArray("sitepage", "SPID", "MENU_ID = $this->menuId AND DELETED=0 ORDER BY POSITION");
		$twins = array ();

		for ($i = 0; $i < count($res); $i++) {
			if ((!$this->management->isSPExpired($res[$i]) && ($this->level==10)) || ($this->level==0))
				array_push($twins, $this->createInstance(null, $res[$i], $this->variation, $this->level));
		}
		return $twins;	
	}
	
	/**
	 * retrieves The next singlepage in a menu-level
	 */
	function getNextPage() {
	  $position = getDBCell("sitemap", "POSITION", "MENU_ID=".$this->menuId);
	  $parent = getDBCell("sitemap", "PARENT_ID", "MENU_ID=".$this->menuId);
	  $menuId = getDBCell("sitemap", "MENU_ID", "POSITION = ".($position+1)." AND PARENT_ID = ".$parent);
	  $nextPage = getDBCell("sitepage", "SPID", "MENU_ID = $menuId");
	  if ($nextPage != "") {	  	
	  	return $this->createInstance(null, $nextPage, $this->variation, $this->level);
	  }
	}
	
	/**
	 * retrieves The next singlepage in a menu-level
	 */
	function getPreviousPage() {
	  $position = getDBCell("sitemap", "POSITION", "MENU_ID=".$this->menuId);
	  $parent = getDBCell("sitemap", "PARENT_ID", "MENU_ID=".$this->menuId);
	  $menuId = getDBCell("sitemap", "MENU_ID", "POSITION = ".($position-1)." AND PARENT_ID = ".$parent);
	  $nextPage = getDBCell("sitepage", "SPID", "MENU_ID = $menuId");
	  if ($nextPage != "") {	  	
	  	return $this->createInstance(null, $nextPage, $this->variation, $this->level);
	  }
	}
	
	
	/**
	 * Returns all Pages beeing on the same level, i.e. having the same
	 * parent page.
	 */
	function sameLevel() {		
		$parentMenuId = getDBCell("sitemap", "PARENT_ID", "MENU_ID = $this->menuId");
		$menues = createDBCArray("sitemap", "MENU_ID", "IS_DISPLAYED=1 AND PARENT_ID = $parentMenuId AND DELETED=0 ORDER BY POSITION ASC");
		$res = array ();
		for ($i = 0; $i < count($menues); $i++) {
			$spid = getDBCell("sitepage", "SPID", "MENU_ID = ".$menues[$i]." ORDER BY POSITION ASC");
			if ((($this->level == 10) && (!$this->management->isSPExpired($spid))) || ($this->level==0))
				array_push($res, $this->createInstance(null, $spid, $this->variation, $this->level));
			
		}
		return $res;
	}
	
   /**
	* Returns array containing all pages an the path from current page to root
	*/
	function getPathToRoot() {
		global $cds;
		$path = array();
		if ($this->pageId != $cds->management->getStartPage()) {
			$currentMenuId = $this->menuId;
			$currentSPID = $this->pageId;
			if (! $this->isSinglepage()) 
				$currentSPID = getDBCell("sitepage", "SPID", "MENU_ID = $currentMenuId ORDER BY POSITION");
			while ($currentMenuId != 0) {
				if (getDBCell("sitemap", "IS_DISPLAYED", "MENU_ID = $currentMenuId")) array_push($path, $this->createInstance(null, $currentSPID, $this->variation, $this->level));
				$currentMenuId = getDBCell("sitemap", "PARENT_ID", "MENU_ID = $currentMenuId");
				$currentSPID = getDBCell("sitepage", "SPID", "MENU_ID = ".$currentMenuId);
			}
		}	
		return $path;
	}
	
	/**
	 * Sets the menuobject of the startpage
	 *
	 */
	function getStartPage() {
		return $this->getMenuByPath("/");
	}
	 	
	 	/**
		 * Returns a menu-object with the menu corresponding to the pageId
		 * @param integer PageID, you want to set as menu start node.
		 */
		function getMenuById($pageId) {
			// check, if ID is a page.
			if (countRows("sitepage", "SPID", "SPID=$pageId") < 1) {
				return null;
			} else {
			  return $this->createInstance(null, $pageId, $this->variation, $this->level); 
			}  
		}
		
		
		/**
		 * Returns a menu-object with the menu corresponding to the path
		 * @param string Path to query for a new menu-object. format "/page1/page2/page3"
		 */
		function getMenuByPath($path="/") {
			$liste = explode("/", strtoupper($path));
			if ($path == "/" || $path=="") {
				$management = new Management($this);
				$page = $management->getStartPage($this->variation);	
			} else {
				if ($this->level != 0) {
			    	$page = getDBCell("state_translation", "OUT_ID", "IN_ID=0 AND LEVEL=".$this->level);
		    	} else {
				    $page = 0;
			    }
			
		    	for ($i = 1; $i < count($liste); $i++) {
			    	if ($page == "")
				    	$page = 0;

			    	if ($liste[$i] != "") 
				    	$page = getDBCell("sitemap", "MENU_ID", "PARENT_ID=$page AND UPPER(NAME)='" . $liste[$i] . "'");
				    }   
			    if ($page != "") 
				    $page = getDBCell("sitepage", "SPID", "MENU_ID=$page AND DELETED=0");
			}
	
		    if ($page == "") $page = getDBCell("state_translation", "OUT_ID", "IN_ID=0 AND LEVEL=".$this->level);	
		    return $this->createInstance(null, $page, $this->variation, $this->level);		
		}
		
	/**
	 * Use this function for directly referencing a menu you know the page-name of.
	 * Works relative to current menu only!
	 * @param string		Name of the Sitepage
	 */
	function getMenuRelative($pagename) {
		$pagename = strtoupper($pagename);
		$menuId = getDBCell("sitepage", "MENU_ID", "SPID=" . $this->pageId);
		$id = getDBCell("sitemap", "MENU_ID", "PARENT_ID = $menuId AND UPPER(NAME) = '" . $pagename . "'");
		$pageId = getDBCell("sitepage", "SPID", "MENU_ID = $id");
		return $this->createInstance(null, $pageId, $this->variation, $this->level);
	}

	/**
	 * returns the uri for this menu-item
	 * @param array additional parameters, use: array('varname1'=>'value1', 'varname2'=>'value2'), the routine encodes the values with rawurlencode (RFC 1738), using array('value1','value2') results in '&var0=value1&var1=value2'
	 * @return string uri for menu-item.
	 */
	function getLink($addparas = null) {
		global $c;

		$sessionid = $addparas[$c["sessionidname"]];
		$sessionid = ($sessionid != "") ? $sessionid : value($c["sessionidname"], "NOSPACES");
		$linkadd = ($sessionid != "" && $sessionid != "0") ? ('&' . $c["sessionidname"] . '=' . $sessionid) : "";

		if (is_array($addparas)) {
			foreach ($addparas as $key => $value) {
				if ($key != $c["sessionidname"])
					$linkadd .= '&' . ((is_int($key)) ? 'var' . $key : $key) . '=' . rawurlencode($value);
			}
		}
				
		
		if ($this->level == 10) { // Live-Version			
			$cached = getDBCell("sitemap", "IS_CACHED", "MENU_ID = $this->menuId");

			if (!$c["classicurls"]) {
			  return $c["docroothtml"].getPageURL($this->menuId, $this->variation);
			} else {
				if ($c["renderstatichtml"] && ($cached == 1)) {
					$spname = getSPNameUrlSafe($this->pageId);
					$link_uri = $c["cachedocroot"] . $spname . "_" . $this->pageId . "_v" . $this->variation . ".html";

					return ($link_uri);
				} else {
					$template = $this->getTemplate();
					$link_uri = $c["docroothtml"] . $template . "?page=" . $this->pageId . "&v=" . $this->variation;
					return ($link_uri . $linkadd);
				}
			}
		} else { // Development- (Template-) Version
			$template = $this->getTemplate();
			$link_uri = $c["devdocroot"] . $template . "?page=" . $this->pageId . "&v=" . $this->variation;
			return ($link_uri . $linkadd);
		}
	}

	/**
	 * Check, whether the menu page shall open in a new window or not
	 */
	function isPopup() {
		return (getDBCell("sitemap", "IS_POPUP", "MENU_ID = $this->menuId") == 1);
	}	
		
		
	/**
	 * Gets the Title of a Sitepage. You must make a difference between Multipages
	 * ans Singlepages. For getting Menu-Titles of singlepage and Multipagefolder no parameter is needed.
	 * For getting the title of a Multipage-Member, you need to set the parameter to true!
	 * @param boolean true =>retrieve the category names of multipages
	 */
	function getTitle($isMultipageName = false) {
	   $menuInformation = getMenu($this->pageId, $this->variation, $isMultipageName);
	   return $menuInformation[0];	
	}
	
	/**
	 * Creates an a-tag with the complete  link to the page
	 * @param string $addParams Additional payload (e.g. attributes) which will be passed in the a-tag
	 */
	function getTag($addParams="") {
		$result = '<a href="'.$this->getLink().'"';
		if ($this->isPopup()) $result.= ' target="_blank"';
		if ($addParams != "") $result.= ' '.$addparams;
		$result.='>'.$this->getTitle().'</a>';
		return $result;
	}
	
	/**
	 * Gets the Title of a Multipage
	 */
	 function getTitleMultipage() {
	 		return $this->getTitle(true);	
	 }
	
	
	/**
	 * Gets the Descrition of a Sitepage. You must make a difference between Multipages
	 * ans Singlepages. For getting Menu-descriptions of singlepage and Multipagefolder no parameter is needed.
	 * For getting the description of a Multipage-Member, you need to set the parameter to true!
	 * @param boolean true =>retrieve the category names of multipages
	 */
	function getDescription($isMultipageName = false) {
	   $menuInformation = getMenu($this->pageId, $this->variation, $isMultipageName);
	   return $menuInformation[1];	
	}
	
	/**
	 * Gets the description of a multipage
	 */
	 function getDescriptionMultipage() {
	   	return $this->getDescription(true)	;
	 }
	
	
	/**
	 * Determines, whether the page is a singlepage
	 */
	 function isSinglepage() {
	   return ($this->getSPType()==1);	
	 }
	 
	 
	/**
	 * Determines, whether the page is a singlepage
	 */
	 function isMultipage() {
	   return ($this->getSPType()==2);	
	 }
	
	/**
	 * Help function
	 * Determines, whether the sitepage is a single-page or a multi-page
	 * Use isSinglepage and isMullowetipage instead.
	 * @returns 	integer		1=Singlepage, 2=Multipage
	 */
	function getSPType() {
		$spm = getDBCell("sitepage", "SPM_ID", "SPID = ".$this->pageId);
		return getDBCell("sitepage_master", "SPMTYPE_ID", "SPM_ID = $spm");	
	}
	
	/**
	 * returns the string entered for direct-url
	 * @return string name of direct-url
	 */
	function getDirectUrl() {
		return getDBCell("sitepage_names", "DIRECT_URL", "SPID = ".$this->pageId." AND VARIATION_ID = ".$this->variation);
	}
	
	/**
	 * gets the template of this menupage
	 * @returns	string		template of the sitepage.
	 */
	function getTemplate() {
		$spm = getDBCell("sitepage", "SPM_ID", "SPID = ".$this->pageId);
		if ($spm != "") {
			return getDBCell("sitepage_master", "TEMPLATE_PATH", "SPM_ID=$spm");
		} else {
			return "";
		}
	}
	
 	/**
 	 * Set the variation with the short tag
 	 * @param string Shorttag of the variation
 	 */
 	 function setVariation($shortTag) {
	 	$id = getDBCell("variations", "VARIATION_ID", "UPPER(SHORTTEXT) = UPPER('$shortTag')");
 	 	if ($id != "") $this->variation = $id;
 	 }
	
}