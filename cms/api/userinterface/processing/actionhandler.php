<?
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
 * stores actions, that shall be performed, when
 * a form is successfully processed.
 * Known types for ActionHandler are insert, update and delete.
 * @package Database
 */
 class ActionHandler {

 	var $type;
	var $dbactions = null;
	var $fncactions = null;

	/**
	* standard constructor
	*
	* @param string When shall the handler be processed? ON INSERT|UPDATE|DELETE
	*/
	function ActionHandler($type) {
		$this->type = strtoupper($type);
	}

	/**
	* Add a SQL String to the ActionHandler, that will be executed, when the type action
	* you specified in the constructor is performed. You can use <oid> on Insert-Handlers,
	* to make use of a oid, that is created by the insert operation!
	*
	* @param string The SQL-String that is to be executed
	*/
	function addDbAction($sql) {
		$this->dbactions[count($this->dbactions)] = $sql;
	}
	
	/**
	 * Add a Function to the ActionHandler, that will be called when the action you specified
	 * in the constructor is performed. Please assure, that the function is in an namespace,
	 * that is accessible for the Handler.
	 * @param string Name of the function to call.
	 */
	function addFncAction($fname) {
		$this->fncactions[count($this->fncactions)] = $fname;
	}

	/**
	* This function will be automatically called by your form, when the action_type,
	* you specified in the constuctor is performed
	*
	* @param string The action that is currently performed by the form. Does not need to
	* 		 the same as the action in the constructor. Therefore it will not be
	*		 executed. (INSERT|UPDATE|DELETE)
	*/
	function process($type) {
		global $db, $oid;
		if (strtoupper($type) == $this->type) {
			for ( $i = 0; $i < count($this->dbactions); $i++ ) {
				$this->dbactions[$i] = ereg_replace("<oid>", $oid, $this->dbactions[$i]);
				$query = new query($db, $this->dbactions[$i]);				
			}
			//  use pconnect, connection will be closed automatically.
			for ( $j = 0; $j < count($this->fncactions); $j++) {
				$res = $this->fncactions[$j]();
			}
		}
	}
	/** For Down-Grade-Compatibility only **/
	function proccess($type) {
 		$this->process($type);
 	}

 }


 ?>
