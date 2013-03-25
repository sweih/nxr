<?php
	/**********************************************************************
	 *	N/X - Web Content Management System
	 *	Copyright 2002 Thomas Gauweiler, FZI Research Center for Information Technologies
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
	* @package DatabaseConnectedInput
	*/

	/**
	 * Draw information from the database
	 * @package DatabaseConnectedInput
	 */
	class DisplayedValue extends DBO {

		/**
		 * standard constructor
		 * @param string Text that is to be shown as description or label with your object.
		 * @param string Table, you want to connect with the object.
		 * @param string column, you want to connect with the object.
		 * @param string $row_identifier Usually to be generated with form->setPK. Identifies the
		 * row in db, you want to affect with operation. Keep empty for insert. (Example: stdEDForm)
		 * @param string allowed is type:TEXT as paramter
		 * @param string $datatype Datatype of the database, you want to use. Allowed are CHAR|NUMBER|DATE|DATETIME|TIMESTAMP|PASSWORD
		 */
		function DisplayedValue($label, $table, $column, $row_identifier = "1", $param = "type:text", $datatype = "TIMESTAMP") {
			DBO::DBO($label, $table, $column, $row_identifier, $param, $datatype, "");
			$this->std_style = "standardlight";			
			$this->v_wuiobject = new Label($this->name, $this->value, $this->std_style, $this->width);
		} // function DisplayedValue(..)
		
		/**
		 * Dummy function
		 */
		function process() {}
		
		/**
		 * Dummy function
		 */
		function check() {}
	}     // class DisplayedValue extends DBO
?>