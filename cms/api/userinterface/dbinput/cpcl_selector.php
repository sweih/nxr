<?
	/**********************************************************************
	 *	N/X - Web Content Management System
	 *	Copyright 2002 Sven Weih (sven@nxsystems.org), Fabian Koenig (fabian@nxsystems.org)
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
	 * Creates an input field with a list of the Instances of an CLuster-Template in the
	 * cms. Use for selecting a compound-cluster-instance. Can handly also "ANY" for creating a
	 * list of all compounds of the system.
	 * @package DatabaseConnectedInput
	 */
	class CPCLSelector extends CLSelector {
		var $clt;
		var $additionalAttribute = "";

		/**
		  * standard constructor
		  * @param string Text that is to be shown as description or label with your object.
		  * @param string Table, you want to connect with the object.
		  * @param string column, you want to connect with the object.
		  * @param string $row_identifier Usually to be generated with form->setPK. Identifies the
		  * row in db, you want to affect with operation. Keep empty for insert. (Example: stdEDForm)
		  * @param string $cltype ID of the currently used Cluster-Template.
		  * @param string $params Additional parameters.
		  * @param string $check Does checks on user input. Allowed are MANDATORY (=not null)|UNIQUE. Separate with &.
		  * @param string $db_datatype Datatype of the database, you want to use. Allowed is DATE only.
		  */
		function CPCLSelector($label, $table, $column, $row_identifier = "1", $cltype, $params = "", $check = "MANDATORY", $db_datatype = "NUMBER") {
			DBO::DBO($label, $table, $column, $row_identifier, $params, $db_datatype, $check);

			$this->v_wuiobject = new Dropdown($this->name, null, $this->std_style, $this->value, 300, 1);
			$this->clt = $cltype;
			if ($this->value=="") $this->value="NULL";
			
		}

		/**
		  * draw the object.
		  */
		function draw() {
			/** Create the value array **/
			global $db;
			$this->v_wuiobject->additionalAttribute = $this->additionalAttribute;

			$instances = null;

			if ($this->clt == "-1") {
				$this->createALLCPCL($instances);
			} else {
				$this->createCLT($instances);
			}
			// set the values
			$this->v_wuiobject->value = $instances;
			// draw the object
			DBO::draw();
			return 2;
		}

		/**
		  * Used to create a  tree of the CLuster-Templates.
		  * Recursive function. 
		  * Create a global variable $isFolder if you are moving folders, because there are special rules then.
		  * @param array array with name-value pairs of the folders
		  */
		function createAllCPCL(&$instances) {
			global $db, $lang;

			$sql = "SELECT ct.NAME AS CTNAME, cn.NAME AS CNNAME, cn.CLNID FROM cluster_node cn, cluster_templates ct WHERE cn.CLT_ID = ct.CLT_ID AND ct.CLT_TYPE_ID = 1 AND cn.DELETED=0  AND cn.VERSION=0 ORDER BY ct.NAME, cn.NAME ASC";

			$instances[0][0] = "-";
			$instances[0][1] = 0;

			$myquery = new query($db, $sql);

			while ($myquery->getrow()) {
				$ni = count($instances);
				$instances[$ni][1] = $myquery->field("CLNID");
				$instances[$ni][0] = $myquery->field("CTNAME").'/'.$myquery->field("CNNAME");
			}
			$myquery->free();
		}
	}
?>