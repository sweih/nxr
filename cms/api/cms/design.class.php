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


	/**
	 * Abstract base class for deriving layout-classes for the websites built with N/X WCMS.
	 * As a developer you must derive from from AbstractDesign and implement your own methods,
	 * at least getName(), getHeader() and getFooter().
	 * 
	 * After implementing your class, you can copy it to cms/design in the subfolder you have written
	 * the designclass for. 
	 */
	class AbstractDesign {
		
		var $cds;
		var $pageId;
		var $variation;
		var $articleId;				
		
	
		/**
		 * Get the standard variables of the page
		 *
		 * @return AbstractDesign
		 */
		function AbstractDesign()  {
			global $cds, $article;
			$this->cds = &$cds;
			$this->page = $cds->pageId;
			$this->variation = $cds->variation;
			$this->articleId = $articleId;						
		}
		
		
		/**
		 * Because you should not write your own constructor, the system will call the initialize
		 * method whenever an instance of this class is created;
		 *
		 */
		function initialize() {			
			// empty
		}
		
		/**
		 * Returns the ClassName and the FolderName of the extension.
		 */
		function getName() {
			return "AbstractDesign";
		}
		
		/**
		 * The display name which is shown in the backoffice to select a menu		 		
		 */
		function getDisplayName() {
			return $this->getName();
		}
		
		/**
		 * Returns the header code for the design-class. The layout methods of design-classes 
		 * for a menu-class are  called with the scheme:
		 *  getHeader();
		 * ... do something....
		 * getFooter(),
		 */
		function getHeader() {
		   // empty
		   $this->getBody();
		}
		
		/**
		 * Returns the footer code for the design-class. The layout methods of design-classes 
		 * for a menu-class are  called with the scheme:
		 *  getHeader();
		 * ... do something....
		 * getFooter(),
		 */
		function getFooter() {
			// empty
		}
		
		/**
		 * Document-Root of the design
		 *
		 * @return unknown
		 */
		function docroot() {
			global $c;
			return $c["basedocroot"].'designs/'.strtolower(get_class($this)).'/';
		}
		
		
		/**
		 * Returns code which should be created with the design class. 
		 * for a breadcrumb you do not need header and footer, so use getBody.		 
		 */
		function getBody() {
		   //empty			
		}
		
		/**
		 * The method is called wehen the object is created by the CDS.
		 * You get the $cds->layout Object as an reference and can modify the page headers.
		 *
		 * @param CDSLayout $layout object of $cds->layout.
		 */
		function setupPage($layout) {
			// add css or javascript or onLoad...
		}
		
		/**
		 * Allow custom configuration of the layout-class
		 * The configuration is based on the class SettingsForm which is passed by the 
		 * variable-reference $settingsform.
		 *
		 * @param SettingsForm $settingsForm
		 */
		function editConfiguration(&$settingsForm) {
		  //				
		}
		
		
	}
	
?>