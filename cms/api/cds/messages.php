<?
	/**
	 * @package CDS
	 */

	/**********************************************************************
	 *	N/X - Web Content Management System
	 *	Copyright 2002 Sven Weih
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
	  * This class is used to display messages in the website. Customize it, if you need to.
	  * Access this class with $cds->messages
	  */
	 class Messages {
		var $parent;

		/**
		* Standard constructor.
		*/
		function Messages(&$parent) { $this->parent = &$parent; }
		
				
		/**
		 * Call this function whenever you want to display a message
		 * @param string Name of the message you want to display, e.g. pageExpired.
		 */
		 function draw($messageName) {
		 	$messageName = strtoupper($messageName);
		 	$this->htmlHeader();
		 	$this->$messageName();
		 	$this->htmlFooter();	
		 }
		
		/**
		 * page, which is drawn, when the Sitepage is expired
		 */
		 function PAGEEXPIRED() {
		 	global $c;
		 	echo "<h1>This page is no longer available</h1>\n";
		 	echo "<a href=\"".$c["livedocroot"]."\">Go back to the website by klicking this link</a><br>\n";
		 	echo "<!-- this message was generated at ".time()." -->";
		 }
		 
		 /**
		  * Draw a header for the HTML-Page
		  */
		 function htmlHeader() {
		 	echo "<html><head><title>N/X WCMS</title></head><body>";
		 }
		
		 /**
		  * Draw a footer for the HTML-Page
		  */
		 function htmlFooter() {
		 	echo "</html></body>";
		 }
	}