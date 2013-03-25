<?
	/**********************************************************************
	 *	N/X - Web Content Management System
	 *	Copyright 2002 Fabian Koenig
	 *
	 *	This file is part of N/X.
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
	 * draws a to the database-connected advanced select-box, from which you can select only one value.
	 * @package DatabaseConnectedInput
	 */
	class IconSelectInput extends DBO {
		function IconSelectInput($label, $table, $column, $foreign_table, $foreign_name, $foreign_value, $foreign_description, $foreign_iconpath, $foreign_icon, $foreign_data_identifier = "1", $row_identifier = "1", $param = "", $check = "MANDATORY", $datatype = "NUMBER") {
			DBO::DBO($label, $table, $column, $row_identifier, $param, $datatype, $check);

			// build name-value-description-imgpath-image - Array
			global $db;
			$values = null;
			$counter = 0;
			$sql = "SELECT DISTINCT $foreign_name, $foreign_value, $foreign_description, $foreign_icon FROM $foreign_table WHERE $foreign_data_identifier ORDER BY $foreign_name";
			$query = new query($db, $sql);

			while ($query->getrow()) {
				$values[$counter][0] = $query->field($foreign_name);

				$values[$counter]["name"] = $values[$counter][0];
				$values[$counter][1] = $query->field($foreign_value);
				$values[$counter]["value"] = $values[$counter][1];
				$values[$counter][2] = $query->field($foreign_description);
				$values[$counter]["description"] = $values[$counter][2];
				$values[$counter][3] = $foreign_iconpath;
				$values[$counter]["iconpath"] = $values[$counter][3];
				$values[$counter][4] = $query->field($foreign_icon);
				$values[$counter]["icon"] = $values[$counter][4];
				$counter++;
			}
			$query->free();
			// end of Array-build
			$this->v_wuiobject = new IconSelect($this->name, $values, "", $this->value, $this->size, $this->std_style);
		}
	}
?>