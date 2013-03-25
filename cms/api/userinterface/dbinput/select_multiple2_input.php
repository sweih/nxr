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
	 * Adds an input for selecting Multiple values from a checkbox-button-group.
	 * Standard for nx is using this box for n:n-table connections on a table named
	 * table with foreign key column column. The matching key is s_value in s_table.
	 * For selecting s_name will be presented at the WUI.
	 * You may also use Combination-Editor.
	 * @package DatabaseConnectedInput
	 */
	class SelectMultiple2Input extends DBO {
		var $s_table;

		var $s_name;
		var $s_value;
		var $s_row_identifier;

		/**
		  * standard constructor
		  * @param string Text that is to be shown as description or label with your object.
		  * @param string Table, you want to connect with the object.
		  * @param string column, you want to connect with the object.
		  * @param string $row_identifier Usually to be generated with form->setPK. Identifies the
		  * row in db, you want to affect with operation. Keep empty for insert. (Example: stdEDForm)
		  * @param string name of the table, you want to join to the input-value.
		  * @param string name of the column, you want to use as diplay in dropdown for value.
		  * @param string name of the column, you want to retrieve the values from for the table and column.
		  * foreign key to table->column.
		  * @param string where-condition in SQL-Clause for selections of input-values.
		  * @param string Datatype of the database, you want to use. Allowed are CHAR|NUMBER
		  */
		function SelectMultiple2Input($label, $table, $column, $row_identifier, $s_table, $s_name, $s_value, $s_row_identifier, $datatype = "NUMBER") {
			DBO::DBO($label, $table, $column, $row_identifier, "", $datatype);

			$this->s_table = $s_table;
			$this->s_name = $s_name;
			$this->s_value = $s_value;
			$this->s_row_identifier = $s_row_identifier;
		}

		/**
		 * Draws the HTML for this Input.
		 */
		function draw() {
			global $page_state;

			$this->v_label->draw();
			$boxes = createNameValueArray($this->s_table, $this->s_name, $this->s_value, $this->s_row_identifier);

			if ($page_state != "processing")
				$checked_values = createDBCArray($this->table, $this->column, $this->row_identifier);

			// draw own table
			$cell = 1;
			echo "<td class=\"$this->std_style\" valign=\"top\">";
			echo "<table width=\"100%\" class=\"$this->std_style\" cellpadding=\"0\" cellspacing=\"0\">";

			for ($i = 1; $i < count($boxes); $i++) {
				$checked = "";

				for ($j = 0; $j < count($checked_values); $j++) {
					if ($boxes[$i][1] == $checked_values[$j]) {
						$checked = "checked";
					}
				}

				if (value($this->name . "_" . $boxes[$i][1]) != "0")
					$checked = "checked";

				if ($cell == 1)
					echo "<tr>";

				echo "<td class=\"embeddedlight\" width=\"150\">";
				echo "<input type=\"checkbox\" value=\"1\" name=\"$this->name" . "_" . $boxes[$i][1] . "\" $checked class=\"$this->std_style\"> &nbsp;";
				echo $boxes[$i][0];
				echo "</td>";

				if ($cell == 3) {
					echo "</tr>";

					$cell = 0;
				}

				$cell++;
			}

			if ($cell != 1) {
				$rest = 4 - $cell;

				echo "<td colspan=\"$rest\" style=\"embedded\">&nbsp;</td>";
			}

			echo "</table>";
			echo "</td>";
			return 2;
		}

		/**
		 * Does nothing here. process must be done my developer.
		 */
		function process() { }

		/**
		 * empty at the moment.
		 */
		function check() { }
	}
?>