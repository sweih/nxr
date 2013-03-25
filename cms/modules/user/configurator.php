<?
	/**********************************************************************
	 *	N/X - Web Content Management System
	 *	Copyright 2002 Sven Weih
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
	 * Draws a to the database-connected RoleConfigurator
	 * @package DatabaseConnectedInput
	 */
	class Configurator {
		var $configData = null;

		/**
		  * standard constructor
		  */
		function Configurator() {

			// load the data of the field.
			global $page_state, $page_action, $oid;

			if ($page_state == "processing") {
				$keys = createDBCArray("sys_functions", "FUNCTION_ID", "1");

				$this->configData = array ();

				for ($i = 0; $i < count($keys); $i++) {
					if (value($keys[$i]) != "0")
						array_push($this->configData, $keys[$i]);
				}
			//$this->resolveParents();
			} else {
				// fetch values from the database...
				$this->configData = createDBCArray("role_sys_functions", "FUNCTION_ID", "ROLE_ID = $oid");

				if (!is_array($this->configData))
					$this->configData = array ();
			}
		}

		/** 
		 * Draw the control
		 */
		function draw() {
			echo "<tr><td colspan=\"2\">";

			echo "<table width=\"100%\" cellpadding=\"0\" cellspacing=\"0\" border=\"0\">";
			echo "<tr>";
			echo "<td class=\"standard\">";
			$this->draw_body();
			echo "</td></tr>";
			echo "</table>";
			echo "</td></tr>";
		}

		/** 
		 * Draw the control body
		 */
		function draw_body($parent = "0", $depth = 0) {
			$data = $this->getFunctionData($parent);

			for ($i = 0; $i < count($data); $i++) {
				echo "<table width=\"100%\" cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"embedded\">";

				echo "<tr>";
				echo "<td width=\"25\">" . drawSpacer(1, 1). "</td>";
				echo "<td width=\"" . (250 - $depth * 25) . "\">" . drawSpacer(1, 1). "</td>";
				echo "<td width=\"300\">" . drawSpacer(1, 1). "</td>";
				echo "</tr>";

				if ($depth == 0) {
					echo "<tr>";

					echo "<td width=\"25\" style=\"border-bottom:1px solid black;\">" . drawSpacer(1, 1). "</td>";
					echo "<td width=\"250\" style=\"border-bottom:1px solid black;\">" . drawSpacer(1, 1). "</td>";
					echo "<td width=\"300\" style=\"border-bottom:1px solid black;\">" . drawSpacer(1, 1). "</td>";
					echo "</tr>";
				}

				echo "<tr>";

				if (in_array($data[$i]["ID"], $this->configData)) {
					$checked = true;
				} else {
					$checked = false;
				}

				$chkbox = new Checkbox($data[$i]["ID"], "1", "embedded", $checked);
				$chkbox->draw();
				$lbl = new Label("lbl", $data[$i]["NAME"], "standardlight");
				$lbl->draw();
				$lbl = new Label("lbl", $data[$i]["DESC"], "standardlight");
				$lbl->draw();
				echo "</tr>";

				echo "<tr>";
				echo "<td width=\"15\">" . drawSpacer(1, 1). "</td>";
				echo "<td colspan=\"2\">" . drawSpacer(1, 1);
				$this->draw_body($data[$i]["ID"], ($depth + 1));
				echo "</td>";
				echo "</tr>";
				echo "</table>";
			}
		}

		/**
		 *save the values to the db...
		 */
		function process() {
			global $db, $oid;

			$sql = "DELETE FROM role_sys_functions WHERE ROLE_ID = $oid";
			$query = new query($db, $sql);

			for ($i = 0; $i < count($this->configData); $i++) {
				$sql = "INSERT INTO role_sys_functions (ROLE_ID, FUNCTION_ID) VALUES ($oid, '" . $this->configData[$i] . "')";

				$query = new query($db, $sql);
			}

			$query->free();
		}
		/** For Down-Grade-Compatibility only **/
		function proccess() { $this->process(); }

		/**
		 * Check for correctness
		 */
		function check() { }

		/** 
		 * Assures, all parents are checked in array config-data...
		 */
		function resolveParents() {
			$resolvedParents = array ();

			for ($i = 0; $i < count($this->configData); $i++) {
				$parent = $this->configData[$i];

				while ($parent != "0") {
					array_push($resolvedParents, $parent);

					$parent = getDBCell("sys_functions", "PARENT_ID", "FUNCTION_ID = '$parent'");
				}
			}

			$this->configData = array_values(array_unique($resolvedParents));
		}

		/**
		 * Retrieves function Data from DB
		 * @param string Identifier of the parent or 0 if no parent
		 */
		function getFunctionData($parent = "0") {
			global $db;

			$resultset = array ();
			$sql = "SELECT * FROM sys_functions WHERE PARENT_ID = '$parent' ORDER BY NAME ASC";
			$query = new query($db, $sql);

			while ($query->getrow()) {
				$ar["ID"] = $query->field("FUNCTION_ID");

				$ar["NAME"] = $query->field("NAME");
				$ar["DESC"] = $query->field("DESCRIPTION");
				$ar["PARENT"] = $query->field("PARENT_ID");
				array_push($resultset, $ar);
			}

			$query->free();
			return $resultset;
		}
	}
?>