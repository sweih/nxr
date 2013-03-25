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
	* @package DatabaseConnectedInput
	*/

	/**
	* Writes the specified value in an Insert-Statment to the db.
	* Use with forms only.
	* @package DatabaseConnectedInput
	*/
	class NonDisplayedValueOnInsert extends NonDisplayedValue {

		/**
		  * standard constructor
		  * @param string Table, you want to connect with the object.
		  * @param string column, you want to connect with the object.
		  * @param string $row_identifier Usually to be generated with form->setPK. Identifies the
		  * row in db, you want to affect with operation. Keep empty. For compatibility only.
		  * @param string value, you want to insert.
		  * @param string $datatype Datatype of the database, you want to use. Allowed are CHAR|NUMBER|DATE|DATETIME|TIMESTAMP|PASSWORD
		  */
		function NonDisplayedValueOnInsert($table, $column, $row_identifier = "1", $value, $datatype = "TIMESTAMP") { NonDisplayedValue::NonDisplayedValue($table, $column, $row_identifier, $value, $datatype); }

		/**
		 * Adds the column and table to the Insertset. Automatically called by page processing.
		 */
		function process() {
			global $page_action;

			if ($page_action == "INSERT")
				addInsert($this->table, $this->column, $this->value, $this->datatype);
		}
	}
?>