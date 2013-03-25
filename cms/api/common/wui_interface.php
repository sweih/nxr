<?
	/**
	 * @module Basic Interface
	 * @package WebUserInterface
	 */

	/**********************************************************************
	 *	N/X - Web Content Management System
	 *	Copyright 2002 Sven Weih, FZI Research Center for Information Technologies
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
	 * Basic interface all classes should implement who are drawing with the containers.
	 */
	class WUIInterface {
		
		var $parentForm = null;
		
		function WUIInterface() { }

		/**
		 * Write HTML of the Object out to Browser
		 */
		function draw() { }

		/**
		   * Perform checks of input here
		   */
		function check() { }

		/**
		   * Process inputs here
		   */
		function process() { }

		/** 
		   * Call process. For compatibility only
		   */
		function proccess() { $this->proccess(); }
		
		
		/**
		 * Will be called before process is called and after check
		 */
		function beforeProcess() { }
		
		/**
		 * Will be called after process is called and the data is written!
		 */
		function afterProcess()   { }
		
		/**
		 * Set a backlink to the parent container form
		 * @param object $par Parent Container Object
		 */
		 function setParent(&$par) {
		   $this->parentForm = $par;			 	
		 }
		 
		 /**
		  * Initialize is called by the form directly after adding the
		  * a WUIInterface based object to the form
		  */
		 function initialize() {}
	}
?>