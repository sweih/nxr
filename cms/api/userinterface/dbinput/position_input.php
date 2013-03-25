<?php
/**********************************************************************
	 *	N/X - Web Content Management System
	 *	Copyright 2002-2005 Sven Weih, FZI Research Center for Information Technologies
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
	* Draws a to the database-connected positioninput field.
	* @package DatabaseConnectedInput
	*/
	class PositionInput extends DBO {

	var $posFilter;
		
	/**
		  * standard constructor
		  * @param string Text that is to be shown as description or label with your object.
		  * @param string Table, you want to connect with the object.
		  * @param string column, you want to connect with the object.
		  * @param string $row_identifier Usually to be generated with form->setPK. Identifies the
		  * row in db, you want to affect with operation. Keep empty for insert. (Example: stdEDForm)
		  * @param string $posFilter Filter for determining the correct order
		  * @param string $params Allowed parameters are:
		  * size:XX Size of Input in chars.
		  * width:XX Size of Input in pixel.
		  */
		function PositionInput($label, $table, $column, $row_identifier = "1", $posFilter, $params = "size:10,width:100") {
			$this->posFilter = $posFilter;
			DBO::DBO($label, $table, $column, $row_identifier, $params, "NUMBER", "NUMBER&MANDATORY");
			$this->v_wuiobject = new Input($this->name, $this->value, $this->std_style, $this->size, $this->parameter, $this->width);
		}
		
		/**
		 * Checks, wheter a page is actually in INSERT or UPDATE mode an creates the corresponding
		 * Saveset.
		 */
		function process() {
		  global $page_action;
		  $this->posFilter = str_replace("<chcat>","CHID = ".getvar("chsel"), $this->posFilter);	
		  $pos = getDBCell($this->table, $this->column, $this->posFilter." AND ".$this->column."=".$this->value);
		  if ($pos != "") {
		  	freeRowPosition($this->table, $this->column, $this->value, $this->posFilter);
		  }	
			if ($page_action == "INSERT") {
				addInsert($this->table, $this->column, $this->value, $this->datatype);
			} else if ($page_action == "UPDATE") {
				addUpdate($this->table, $this->column, $this->value, $this->row_identifier, $this->datatype);
			}			
		}
		
	}
?>