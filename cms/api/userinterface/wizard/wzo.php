<?php
/**
 * Wizard API Library
 * @package Userinterface
 * @subpackage Wizard
 */


/**********************************************************************
 *	N/X - Web Content Management System
 *	Copyright 2004 Sven Weih
 *
 *      $Id: wzo.php,v 1.1 2004/03/31 15:11:11 fabian_koenig Exp $ *
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
	 * WZO (Wizard Object) is comparable to WUI, except that the data, filled in
	 * the form is automatically stored in a session.
	 */
	class WZO extends DBO {

		var $error=false;
		var $errortext="";
		var $css="standardlight";
		
		/**
		  * standard constructor
		  * @param string Name of the inputfield in the session.
		  * @param string Name of a validator.
		  */
		function WZO($name, $validator="") {
			global $c_magic_quotes_gpc;
			$this->name = $name;
			$this->validatorFactory($validator);
			session_register($name);
			
			if (value($this->name) != "0") {
				$this->value = value($this->name);
				$_SESSION[$this->name] = $this->value;
			} else {
				$this->value = $_SESSION[$this->name];
			}
			
			if ($c_magic_quotes_gpc == 1)	$this->value = str_replace("\\", "", $this->value);
			if ($this->value == "0000-00-00 00:00:00") $this->value = "";
			if ($this->value == "00:00:00") $this->value = "";
		}
				
		/**
		  * Writes the HTML for the Label and the input-element to the page.
		  * @return integer number of Columns, that were used by draw operation.
		  * Note: NX works with tables and the forms do automatic row-processing.
		  * So they need to know, how many columns the last draw-operation needed!
		  */
		function draw() {
		}

		/**
		 * checks the user input for rules defined in constructor.
		 * @return boolean true if okay, false if error occured.
		 */
		function check() {
			global $errors, $page_state, $page_action, $lang;

			if ($page_state == "processing" && value("changevariation") == "0") {
				for ($i = 0; $i < count($this->validators); $i++) {
					$validation = $this->validators[$i]->validate($this->table, $this->column, $this->filter, $this->value, $this->oldvalue, $this->datatype);

					if ($validation != "") {
						$this->addError("", $validation);
					}
				}
			}
		}	
	}
 
 ?>