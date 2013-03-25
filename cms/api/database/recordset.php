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
	 * Used for querying and storing of a Record from the database.
	 * This class is for working with one record only! Use the getDBCell
	 * method instead of directly using Recordset for caching issues.
	 * @package Database
	 */
	class Recordset {
		var $table;

		var $columns;
		var $row_identifier;

		var $rs;

		/**
		  * standard constructor
		  * @param string Name of the table you want to work with
		  * @param string Name of the columns you want to select
		  * @param string Where-Statement of the SQL-Clause.
		  */
		function Recordset($table, $columns, $row_identifier = "1") {
			$this->table = $table;

			$this->columns = $columns;
			$this->row_identifier = $row_identifier;

			$sql = "SELECT $columns FROM $table WHERE $row_identifier";
			global $db, $errors;

			$this->rs = new query($db, $sql);

			if (trim($db->error()) != "0:") {
				$errors .= "-DBSelect(" . $sql . ")";
			}

			if (!$this->rs->getrow()) {
				// do nothing, just look if can be deleted.
				//$errors.= "-DBSelect(".$sql.")";
				}
		}

		/**
		  * Returns the Value of the column you selected.
		  * @param string Name of column, you want to get the name from.
		  * @return string Content of the selected column
		  */
		function getValue($column) { return $this->rs->field($column); }

		/**
		  * This function is internally used by the getDBCell method for checking,
		  * whether a table and row_identifier is matching to the current class.
		  * @param string table-string to match
		  * @param string row_identifier-string to match
		  * @return boolean Result of matching operation
		  */
		function match($table, $row_identifier) {
			if ($table == $this->table && $row_identifier == $this->row_identifier) {
				return true;
			} else {
				return false;
			}
		}
	}
?>