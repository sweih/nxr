<?php
/**
 * Status control Object
 * @package Userinterface
 * @subpackage Form
 */


/**********************************************************************
 *	N/X - Web Content Management System
 *	Copyright 2004 Sven Weih
 *
 *      $Id: pagestate.php,v 1.1 2004/09/21 10:45:36 sven_weih Exp $ *
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
  * Simple class, which holds the state of form-handling to get rid of global
  * variables.
  */
  class PageState {
  
	var $env;
	var $processing = false;
	var $action = "";
	var $insert = false;
	var $update = false;
	var $delete = false;	
	
  	/**
  	 * Standard constructor
  	 */
  	 function PageState() {
  	 	$this->env = array();
  	 	$this->initialize();
  	 	
  	 }	
  	
  	 /**
  	  * Initialize the PageState-Object. Is automatically called in constructor
  	  */
  	 function initialize() {
		$this->processing = (value("processing") == "yes");
  	 	
		$go = value("go");
		$goon = value("goon");

		if ($goon != "0")
			$go = $goon;

		if ($go != "0") {
			if (strtoupper($go) == "CREATE") {
				$this->action = "INSERT";
				$this->insert = true;
			} else if (strtoupper($go) == "UPDATE") {
				$this->action = "UPDATE";
				$this->update = true;
			}
		}

		if (value("delete") != "0") {
	  		$this->action = "DELETE";	
	  		$this->delete = true;
		}
  	 }
  	 
  	 /**
  	  * Add a variable to state
  	  * @param string name of the variable
  	  * @param mixed value
  	  */
  	  function add($name, $value) {
  	  	$this->env[strtoupper($name)] = $value;
  	  }
  	  
  	  /**
  	   * Returns a variable
  	   * @param string name of the variable
  	   */
  	   function get($name) {
  	   	  return $this->env[strtoupper($name)];
  	   }
  	
  }
 
 
 ?>