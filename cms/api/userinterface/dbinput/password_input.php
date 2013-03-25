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
	* Provides all functionality to input to the database-connected passwords including matching of two input-fields
	* and minimal complexity.
	* @package DatabaseConnectedInput
	*/
	class PasswordInput extends DBO {
		var $pwd1;
		var $pwd2;

		/**
		  * standard constructor
		  * @param string Text that is to be shown as description or label with your object.
		  * @param string Table, you want to connect with the object.
		  * @param string column, you want to connect with the object.
		  * @param string $row_identifier Usually to be generated with form->setPK. Identifies the
		  * row in db, you want to affect with operation. Keep empty for insert. (Example: stdEDForm)
		  * @param string $param allowed is width:XX as paramter for giving the width of input fields in pixel.
		  */
		function PasswordInput($label, $table, $column, $row_identifier = "1", $param = "width:80") { DBO::DBO($label, $table, $column, $row_identifier, $param, "TEXT", "MANDATORY"); }

		/**
		 * Draws the HTML for this Input.
		 */
		function draw() {
			$this->v_label->draw();

			//set password empty for security reasons/browser cache!
			$this->value = "";

			echo "<td class=\"$this->std_style\" valign=\"top\">";
			echo "<table width=\"100%\" class=\"embedded\" cellpadding=\"0\" cellspacing=\"0\">";
			echo "<tr><td class=\"embedded\" style=\"padding:0 0 0 0\" width=\"100\">";
			echo "<input type=\"password\" value=\"$this->value\" name=\"$this->name" . "_1\" style=\"width:" . $this->width . "px;\">";
			echo "</td></tr>";
			echo "<tr><td class=\"embedded\" style=\"padding:0 0 0 0\" width=\"100\">";
			echo "<input type=\"password\" value=\"$this->value\" name=\"$this->name" . "_2\" style=\"width:" . $this->width . "px;\">";
			echo "</td></tr>";
			echo "</table>";
			echo "</td>";
			return 2;
		}
		/**
	  * Does the checks, that are to be performed for this input.
	  * Including the check paramter given rules.
	  * Adds errors to the global error-string.
	  */
		function check() {
			global $lang, $page_state, $page_action;

			if ($page_state == "processing") {
				$p1 = $this->name . "_1";

				$p2 = $this->name . "_2";
				$this->pwd1 = value("$p1", "NOSPACES", "");
				$this->pwd2 = value("$p2", "NOSPACES", "");
				$this->value = $this->pwd1;
				/**
				old one
				if ($page_action == "INSERT" && ( strlen($this->pwd1) < 6 || strlen($this->pwd2) < 6 ) ) $this->addError("PASSWORD", $lang->get("pwdtooshort"));
				*/
				if ((strlen($this->pwd1) < 12 && strlen($this->pwd1) > 0) || (strlen($this->pwd2) < 12 && strlen($this->pwd2) > 0))
					$this->addError("PASSWORD", $lang->get("pwdtooshort"));

				if ($this->pwd1 != $this->pwd2)
					$this->addError("PASSWORD", $lang->get("pwdnotmatch"));
			}
		}

		/**
		 * Adds the column and table to the Saveset. Automatically called by page processing.
		 */
		function process() {
			global $page_action;

			if ($page_action == "INSERT")
				addInsert($this->table, $this->column, $this->value, $this->datatype);

			if ($page_action == "UPDATE" && $this->value != "")
				addUpdate($this->table, $this->column, $this->value, $this->row_identifier, $this->datatype);
		}
	}
?>