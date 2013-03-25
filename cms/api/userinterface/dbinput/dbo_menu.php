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
	 * Creates an input field with a list of the sitemap.
	 * So one Menu-Entry can be moved to another.
	 * require_onces existence of $oid. oid is of type GUID
	 * @package DatabaseConnectedInput
	 */
	class MenuDropdown extends DBO {

		/**
		  * standard constructor
		  * @param string Text that is to be shown as description or label with your object.
		  * @param string Table, you want to connect with the object.
		  * @param string column, you want to connect with the object.
		  * @param string $row_identifier Usually to be generated with form->setPK. Identifies the
		  * row in db, you want to affect with operation. Keep empty for insert. (Example: stdEDForm)
		  * @param string $params Allowed parameter is:
		  * param:<Name of form> needed for js-reasons.
		  * @param string $check Does checks on user input. Allowed are MANDATORY (=not null)|UNIQUE. Separate with &.
		  * @param string $db_datatype Datatype of the database, you want to use. Allowed is DATE only.
		  */
		function MenuDropdown($label, $table, $column, $row_identifier = "1", $params = "", $check = "MANDATORY", $db_datatype = "NUMBER") {
			DBO::DBO($label, $table, $column, $row_identifier, $params, $db_datatype, $check);

			$this->v_wuiobject = new Dropdown($this->name, null, $this->std_style, $this->value, 300, 1);
		}

		/**
		  * draw the object.
		  */
		function draw() {
			/** Create the value array **/
			global $db;

			$folders = null;
			$folders[0][0] = "&gt;";
			$folders[0][1] = 0;

			$this->createFolders($folders, "&gt;", 0);

			// set the values
			$this->v_wuiobject->value = $folders;
			// draw the object
			DBO::draw();
			return 2;
		}

		/**
		  * Used to create a directory tree of the category-table
		  * Recursive function. 
		  * Create a global variable $isFolder if you are moving folders, because there are special rules then.
		  * @param array array with name-value pairs of the folders
		  * @param string prefix, which to write in front of all foldernames. Leave blank, is internally used.
		  * @param integer node where to start indexing
		  */
		function createFolders(&$folder, $prefix, $node) {
			global $db, $oid;

			$isFolder = true;
			$sql = "SELECT MENU_ID, NAME from sitemap WHERE DELETED = 0 AND PARENT_ID=$node ORDER BY POSITION ASC";
			$query = new query($db, $sql);

			while ($query->getrow()) {
				$name = $query->field("NAME");

				$id = $query->field("MENU_ID");
				$nextId = count($folder);
				$nprefix = $prefix . "&nbsp;" . $name . "&nbsp;&gt;";

				if ($id != $oid) {
					$folder[$nextId][0] = $nprefix;

					$folder[$nextId][1] = $id;
				}

				if ((($id == $oid) && !isset($isFolder)) || ($id != $oid))
					$this->createFolders($folder, $nprefix, $id);
			}

			$query->free();
		}
	}
?>