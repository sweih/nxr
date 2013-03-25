<?
/**********************************************************************
 *	N/X - Web Content Management System
 *	Copyright 2002 Sven Weih and Fabian Koenig
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
   * Base class for the oo CDS-API
   */
 class SMA_CDSApi extends CDSApi  {
	
	
 	/**
 	 * Constructor for creating the CDS API interface
 	 * @param boolean Flag, if the CDS should get content from live-server or not
 	 */
	function SMA_CDSApi($is_development, $pageId=0, $variation=0) {
 		CDSApi::CDSApi($is_development, $pageId, $variation);
		$this->menu = new SMA_Menu($this);
 		$this->content = new SMA_Content($this);
 		$this->layout = new SMA_LAyout($this);
 	}	
 	
 	 	/**
	 	 * Return a CDSApi-Object
 	 	 * @param boolean Flag, if the CDS should get content from live-server or not
		 * @param integer pageId of a page to set as start node
		 * @param integer Id of the variation to set as default
		 */
		 function createInstance($is_development, $pageId, $variation) {
		 		return new SMA_CDSApi($is_development, $pageId, $variation);
		 }	
 }
 
?>