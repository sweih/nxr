<?
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
	  * CDS Class for drawing menus in Live Authoring Mode
	  */
	 class SMA_Menu extends Menu {
	 	
	 	/**
	 	 * Standard constructor. Set $parent=null, if you want to initialize manually with
	 	 * pageId and variation.
	 	 * @param object Reference to the parent container
		 * @param integer pageId of a page to set as menu start node
		 * @param integer Id of the variation to set as default
		 * @param integer Id of the current level 
	 	 */
	 	function SMA_Menu($parent, $pageId=0, $variation=0, $level=0) {  	
	 	     Menu::Menu($parent, $pageId, $variation, $level);
	 	}
	 	
	 	/**
	 	 * Return a Menu-Object
	 	 * @param object Reference to the parent container
		 * @param integer pageId of a page to set as menu start node
		 * @param integer Id of the variation to set as default
		 * @param integer Id of the current level 
		 */
		 function createInstance($parent, $pageId, $variation, $level) {
		 		return new SMA_Menu($parent, $pageId, $variation, $level);	
		 }
		 
	/**
	 * returns the uri for a menu-item
	 * @param array additional parameters, use: array('varname1'=>'value1', 'varname2'=>'value2'), the routine encodes the values with rawurlencode (RFC 1738), using array('value1','value2') results in '&var0=value1&var1=value2'
	 * @return string uri for menu-item.
	 */
	function getLink($addparas = null) {
		global $c, $c_sessionidname;
		
		
		$sessionid = $addparas[$c_sessionidname];
		$sessionid = ($sessionid != "") ? $sessionid : value($c_sessionidname, "NOSPACES");
		$linkadd = ($sessionid != "") ? ('&' . $c_sessionidname . '=' . $sessionid) : "";

		if (is_array($addparas))
			foreach ($addparas as $key => $value) {
				if ($key != $c_sessionidname)
					$linkadd .= '&' . ((is_int($key)) ? 'var' . $key : $key) . '=' . rawurlencode($value);
			}

		$link_uri = $c["docroot"] . "modules/sma/sma.php?page=" . $this->pageId . "&amp;v=" . $this->variation;
		return ($link_uri . $linkadd);
	}
 }
?>