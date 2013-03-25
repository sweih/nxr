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
	 * Create a new Set for updating one or several records to the database.
	 * Do not call directly but use the addUpdate method!
	 * @package Database
	 */
	class UpdateSet extends SaveSet {

		/**
		* standard constructor
		* @param string Name of the table you want to modify
		* @param string Where-Condition for SQL-Clause
		*/
		function UpdateSet($table, $row_identifier) { SaveSet::SaveSet($table, $row_identifier); }

		/**
		 * generates and executes the Updatestatement towards the database.
		 */
		function execute() {
			global $db, $errors;
			if ($this->counter > 0) {
				$str = "UPDATE $this->table SET ";
				$commaflag = false;
				
				for ($i = 0; $i < $this->counter; $i++) {
					if ($commaflag)
					$str .= ", ";
					
					$str .= $this->columns[$i] . " = " . $this->values[$i];
					$commaflag = true;
				}
				
				$str .= " WHERE $this->row_identifier";
				
				global $debug;
				
				if ($debug)
				echo "UPDATE: $str <br>";
				
				$query = new query($db, $str);
				
				if (trim($db->error()) != "0:")
				$errors .= "-DBUpdate";
			}
		}
	}
?>