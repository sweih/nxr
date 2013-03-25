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
	 * Draws a to the database-connected DateInputbox.
	 * @tobedone Do Check, whether browser is js-enhanced or not an provide
	 * alternatively JS-Input functions!
	 * @package DatabaseConnectedInput
	 */
	class DateInput extends DBO {

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
		function DateInput($label, $table, $column, $row_identifier = "1", $params = "param:form1", $check = "", $db_datatype = "DATE") {
			DBO::DBO($label, $table, $column, $row_identifier, $params, $db_datatype, $check);

			// load the data of the field.
			global $page_state, $page_action;

			if ($page_state == "processing") {
				//$fieldname = $this->table."_".$this->column;

				$this->value = value($this->name, "NOSPACES", "");
				$this->value = str_replace("/", "-", $this->value);

				/** added 21.1.2002 */
				global $c_magic_quotes_gpc;

				if ($c_magic_quotes_gpc == 1)
					$this->value = str_replace("\\", "", $this->value);
				/** got rid of magic quotes! */
				$this->oldvalue = getDBCell($table, $column, $row_identifier);
			} else {
				if (($page_action == "UPDATE" || $page_action == "DELETE") && $this->row_identifier != "1") {
					$this->value = getDBCell($table, $column, $row_identifier);

					if ($this->value == "0000-00-00 00:00:00")
						$this->value = "";

					if ($this->value == "00:00:00")
						$this->value = "";
				}
			}

			$this->v_wuiobject = new Datebox($this->name, $this->value, $this->std_style, $this->parameter);
		}

		/**
		  * Does the checks, that are to be performed for this input.
		  * Including the check paramter given rules.
		  * Adds errors to the global error-string.
		  */
		function check() {
			global $lang, $page_state;

			if ($page_state == "processing") {
				// do the null check
				if ($this->mandatory == true) {
					if ($this->value == "") {
						$this->addError("MANDATORY", $lang->get("mandatory"));
					}
				}
				if (strlen($this->value) != 10 && strlen($this->value) > 0) {
					$this->addError("DATEFORMAT", $lang->get("DATEFORMAT"));
				} else if (strlen($this->value) == 10) {
					if (substr($this->value, 4, 1) != "-" || substr($this->value, 7, 1) != "-") {
						$this->addError("DATEFORMAT", $lang->get("DATEFORMAT"));
					} else {
						$year = substr($this->value, 0, 4);

						$month = substr($this->value, 5, 2);
						$day = substr($this->value, 8, 2);

						if (!is_numeric($year) || !is_numeric($month) || !is_numeric($day)) {
							$this->addError("DATEFORMAT", $lang->get("wrongdate"));
						} else {
							if (!checkdate($month, $day, $year))
								$this->addError("DATEFORMAT", $lang->get("wrongdate"));
						}
					}
				}
			}
		}
	}
?>