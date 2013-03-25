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
	 * Creates an input field with a list of the Cluster-Templates in the
	 * cms. Use for selecting a cluster-template.
	 * require_onces existence of $cltid. oid is of type GUID
	 * @package DatabaseConnectedInput
	 */
	class CLTSelector extends DBO {
		var $clt;
		var $iscompound = false;
		/**
		  * standard constructor
		  * @param string Text that is to be shown as description or label with your object.
		  * @param string Table, you want to connect with the object.
		  * @param string column, you want to connect with the object.
		  * @param string $row_identifier Usually to be generated with form->setPK. Identifies the
		  * row in db, you want to affect with operation. Keep empty for insert. (Example: stdEDForm)
		  * @param string $thisCLT ID of the currently used Cluster-Template.
		  * @param string $params Additional parameters.
		  * @param string $check Does checks on user input. Allowed are MANDATORY (=not null)|UNIQUE. Separate with &.
		  * @param string $db_datatype Datatype of the database, you want to use. Allowed is DATE only.
		  * @param string select compounds only.
		  */
		function CLTSelector($label, $table, $column, $row_identifier = "1", $thisCLT, $params = "", $check = "MANDATORY", $db_datatype = "NUMBER", $iscompound=0) {
			DBO::DBO($label, $table, $column, $row_identifier, $params, $db_datatype, $check);

			$this->v_wuiobject = new Dropdown($this->name, null, $this->std_style, $this->value, 300, 1);
			$this->clt = $thisCLT;
			$this->iscompound = $iscompound;
		}

		/**
		  * draw the object.
		  */
		function draw() {
			/** Create the value array **/
			global $db, $lang;

			if ($this->iscompound == 1) {
			  $folders[0][0] = $lang->get("not_specified", "Any type");
			  $folders[0][1] = -1;
			} else {
			  $folders = null;	
			}
			
			$this->createCLT($folders, "/", 0);
			
			// set the values
			$this->v_wuiobject->value = $folders;
			// draw the object
			DBO::draw();
			return 2;
		}

		/**
		  * processing the CLTSelector. Do nothing! 
		  */
		function process() {
			//empty
			}

		/**
		  * processing in forms.
		  */
		function process2() { DBO::process(); }

		/** For Down-Grade-Compatibility only **/
		function proccess2() { $this->process2(); }

		/**
		  * Used to create a  tree of the CLuster-Templates.
		  * Recursive function. 
		  * Create a global variable $isFolder if you are moving folders, because there are special rules then.
		  * @param array array with name-value pairs of the folders
		  * @param string prefix, which to write in front of all foldernames. Leave blank, is internally used.
		  * @param integer node where to start indexing
		  */
		function createCLT(&$folder, $prefix, $node) {
			global $db, $oid, $isFolder;

			// find CLT in homenode first.
			($this->iscompound > 0)? $filter = " AND CLT_TYPE_ID=1 ": $filter="";
			$sql = "SELECT NAME, CLT_ID FROM cluster_templates WHERE DELETED=0 AND CATEGORY_ID = $node  AND VERSION=0 $filter ORDER BY NAME ASC";
			$query = new query($db, $sql);

			while ($query->getrow()) {
				$name = $query->field("NAME");

				$id = $query->field("CLT_ID");

				// do checks now.
				if ($id != $this->clt) {
					$nextID = count($folder);

					$folder[$nextID][0] = $prefix . "&nbsp;" . $name;
					$folder[$nextID][1] = $id;
				}
			}

			$query->free();

			$sql = "SELECT CATEGORY_ID, CATEGORY_NAME from categories WHERE DELETED = 0 AND PARENT_CATEGORY_ID=$node ORDER BY CATEGORY_NAME ASC";
			$query = new query($db, $sql);

			while ($query->getrow()) {
				$name = $query->field("CATEGORY_NAME");

				$id = $query->field("CATEGORY_ID");
				$nprefix = $prefix . "&nbsp;" . $name . "&nbsp;/";
				$this->createCLT($folder, $nprefix, $id);
			}

			$query->free();
		}
	}
	
	/**
	 * Class for selecting a compound in channels
	 */
	class CompoundSelector extends CLTSelector {
		
			/**
		  * standard constructor
		  * @param string Text that is to be shown as description or label with your object.
		  * @param string Table, you want to connect with the object.
		  * @param string column, you want to connect with the object.
		  * @param string $row_identifier Usually to be generated with form->setPK. Identifies the
		  * row in db, you want to affect with operation. Keep empty for insert. (Example: stdEDForm)
		  * @param string $thisCLT ID of the currently used Cluster-Template.
		  * @param string $params Additional parameters.
		  * @param string $check Does checks on user input. Allowed are MANDATORY (=not null)|UNIQUE. Separate with &.
		  * @param string $db_datatype Datatype of the database, you want to use. Allowed is DATE only.
		  */
		function CompoundSelector($label, $table, $column, $row_identifier = "1", $thisCLT, $params = "", $check = "MANDATORY", $db_datatype = "NUMBER") {
			CLTSelector::CLTSelector($label, $table, $column, $row_identifier, $thisCLT, $params, $check, $db_datatype,2);
		}
		
		/**
		 * standard processing
		 */
		function process() {
			DBO::process();	
		} 
		
				
	}
?>