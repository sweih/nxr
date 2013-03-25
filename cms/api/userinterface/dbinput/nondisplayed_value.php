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
	* Writes a value to the database, which is not displayed in the form, also not as
	* hidden field. Use, if you want to update timestamps e.g.
	* If NonDisplayedValue shall write on Insert only, then use class NonDisplayedValueOnInsert.
	* Use with forms only.
	* @package DatabaseConnectedInput
	*/
	class NonDisplayedValue extends DBO {
		var $table;

		var $column;
		var $row_identifier;
		var $datatype;
		var $value;

		/**
		  * standard constructor
		  * @param string Table, you want to connect with the object.
		  * @param string column, you want to connect with the object.
		  * @param string $row_identifier Usually to be generated with form->setPK. Identifies the
		  * row in db, you want to affect with operation. Keep empty for insert. (Example: stdEDForm)
		  * @param string value, you want to insert.
		  * @param string $datatype Datatype of the database, you want to use. Allowed are CHAR|NUMBER|DATE|DATETIME|TIMESTAMP|PASSWORD
		  */
		function NonDisplayedValue($table, $column, $row_identifier = "1", $value, $datatype = "TIMESTAMP") {
			$this->table = $table;

			$this->column = $column;
			$this->row_identifier = $row_identifier;
			$this->datatype = strtoupper($datatype);
			$this->value = $value;
		}

		/**
		 * empty here. for compatibility only.
		 * @return integer Always returns 0.
		 */
		function draw() {
			// draw nothing
			return 2; }

		/**
		  * empty here. for compatibility only.
		  */
		function check() {
			// nothing to check
			}
	}
?>