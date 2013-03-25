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
	 * Create a new Set for inserting a record to the database.
	 * Do not call directly but use the addInsert method!
	 * @package DatabaseOperations
	 */
	class InsertSet extends SaveSet {
		var $pk = "";
		var $pkval = "";
		var $pkdatatype = "NUMBER";

		/**
		 * standard constructor
		 * @param string Name of the table you want to modify
		 */
		function InsertSet($table) { SaveSet::SaveSet($table, ""); }

		/**
		 * Set the Name of Primary Key column for autogenerating the next value.
		 * The system NX works with is based on artificial keys. So no combined
		 * primary keys are allowed in InsertSet-Methods. Nevertheless you can
		 * insert to db data with combined keys by using the Action-Handler-Class.
		 * This makes sense for creating n:n-Connections on a table.
		 * @param string Name of primary key in table.
		 * @param mixed optional key of the PK
		 * @param string optional datatype of the PK
		 */
		function setPK($pk, $value="", $datatype) { 
			$this->pk = $pk; 
			$this->pkval = $value;
			$this->pkdatatype = $datatype;
		}

		/**
		 * generates and executes the Insert-Statement towards the database.
		 */
		function execute() {
			global $db, $errors, $temp_oid, $form;
			

			if ($this->pkval == "") {
			  $nextId = nextGUID();
			  $temp_oid = $nextId;			
			  $keys = "($this->pk";
			  $vals = "($nextId";
			  $commaflag = true;
			} else {
			  addInsert($this->table, $this->pk, $this->pkval, $this->pkdatatype);
			  $keys = "(";
			  $vals = "(";	
			  $commaflag = false;
			}
			
			$str = "INSERT INTO $this->table ";
			

			for ($i = 0; $i < $this->counter; $i++) {
				if ($commaflag) {
					$keys .= ", ";
					$vals .= ", ";
				}

				$keys .= $this->columns[$i];
				$vals .= $this->values[$i];
				$commaflag = true;
			}

			$str .= $keys . ") VALUES " . $vals . ")";

			global $debug;

			if ($debug == true)
				echo "SQL: $str <br>";

			$query = new query($db, $str);

			if (trim($db->error()) != "0:")
				$errors .= "-DBInsert";

			return $nextId;
		}
	}
?>