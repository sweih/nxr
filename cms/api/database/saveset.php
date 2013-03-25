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
	 * Baseclass for all storing Classes. Provides standard constructor and
	 * functionality for adding column-value pairs to the Insert or Updatestatements.
	 * @package Database
	 */
	class SaveSet {
		var $table;
		var $row_identifier;
		var $columns;
		var $values;
		var $counter;

		/**
		 * standard constructor
		 * @param string Name of the table you want to modify
		 * @param string Where-Condition for SQL-Clause
		 */
		function SaveSet($table, $row_identifier) {
			$this->table = $table;
			$this->row_identifier = $row_identifier;
			$this->columns = array();
			$this->values = array();
			$this->counter = 0;
		}

		/**
		 * Add a column-value-pair to the Set. The value is automatically
		 * formatted to match the given datatype.
		 * @param string Name of the column, you want to insert the value
		 * @param string Value of the column, you want to insert.
		 * @param string Kind of Datatype you want to insert.
		 * Allowed ar CHAR|NUMBER|DATE|DATETIME|TIMESTAMP|PASSWORD
		 */
		function add($column, $value, $datatype) {
			global $c;

			$datatype = strtoupper($datatype);
			if ($datatype == "TEXT") {
				$this->values[$this->counter] = "'" . parseSQL($value). "'";

				$this->columns[$this->counter] = $column;
				$this->counter++;
			} else if ($datatype == "BLOB") {
				$this->values[$this->counter] = "'" . parseSQL($value). "'";

				$this->columns[$this->counter] = $column;
				$this->counter++;
			} else if ($datatype == "NUMBER") {
                if (count($value) == 0) $value = 'NULL';
                $this->values[$this->counter] = $value;

				$this->columns[$this->counter] = $column;
				$this->counter++;
			} else if ($datatype == "DATE") {
				$this->values[$this->counter] = "'$value'";

				$this->columns[$this->counter] = $column;
				$this->counter++;
			} else if ($datatype == "TIME") {
				$this->values[$this->counter] = "'$value'";

				$this->columns[$this->counter] = $column;
				$this->counter++;
			} else if ($datatype == "DATETIME") {
                if (stristr($value, '()')) {
                   $this->values[$this->counter] = " $value ";
                } else if ($value == "" || $value==0) {
                   $this->values[$this->counter] = " NULL ";
                } else {
                   $this->values[$this->counter] = "'$value'";
                }

				$this->columns[$this->counter] = $column;
				$this->counter++;
			} else if ($datatype == "TIMESTAMP") {
				$this->values[$this->counter] = "NOW()";

				$this->columns[$this->counter] = $column;
				$this->counter++;
			} else if ($datatype == "PASSWORD") {
				$this->values[$this->counter] = "encode('$value', '" . $c["dbcode"] . "')";

				$this->columns[$this->counter] = $column;
				$this->counter++;
			}
		}
	}
?>