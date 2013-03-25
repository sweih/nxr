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
	 * DBO (=DataBase Object) is comparable to WUI, except that the data, filled in
	 * the form is automatically retrieved and stored in the database. So you do not need to
	 * write you own error-checking and database-processing methods, as DBO will
	 * do that for you.
	 * DBO is using WUI for drawing the Form-Objects. Usually a Label-Object and a
	 * Form-Object are generated to display an input. DBO is the baseclass for all
	 * database-connected input fields!
	 * @package DatabaseConnectedInput
	  */
	class DBO extends WUIInterface {
		var $table;

		var $column;
		var $datatype;
		var $row_identifier;
		var $filter = "1";
		var $label;

		var $v_label;
		var $v_wuiobject;

		var $unique;
		var $mandatory;

		var $std_style = "standard";
		var $err_style = "error";

		var $type;
		var $value = "";
		var $width = "300";
		var $size = "16";
		var $parameter = "";
		var $name = "";
		var $oldvalue = "";

		var $validators = null;

		/**
		  * standard constructor
		  * @param string Text that is to be shown as description or label with your object.
		  * @param string Table, you want to connect with the object.
		  * @param string column, you want to connect with the object.
		  * @param string $row_identifier Usually to be generated with form->setPK. Identifies the
		  * row in db, you want to affect with operation. Keep empty for insert. (Example: stdEDForm)
		  * @param string Additional parameters to the DBO class such as size, width etc.
		  * @param string Datatype of the database, you want to use. Allowed are CHAR|NUMBER|DATE|DATETIME|TIMESTAMP|PASSWORD
		  * @param string Does checks on user input. Allowed are MANDATORY (=not null)|UNIQUE. Separate with &.
		  */
		function DBO($label, $table, $column, $row_identifier = "1", $param, $db_datatype = "CHAR", $check = "") {
			global $specialID, $forceLoadFromDB;

			$this->table = $table;
			$this->column = $column;
			$this->datatype = $db_datatype;
			$this->row_identifier = $row_identifier;
			$this->datatype = strtoupper($db_datatype);
			$this->name = $table . "_" . $column . $specialID;
			$this->label = $label;
			$this->parseParam($param);

			// initialize validators...
			$this->ValidatorFactory($check);

			// old school
			/**if (stristr(strtoupper($check), "UNIQUE")) {
				 $this->unique = true;
			 } else {
				 $this->unique = false;
			 }*/
			if (stristr(strtoupper($check), "MANDATORY")) {
				$this->mandatory = true;
			} else {
				$this->mandatory = false;
			}
			$this->v_label = new Label("lbl_" . $column, $label, $this->std_style);

			if (!$this->mandatory) {
				$this->v_label->text = "<span style=\"font-weight:normal;\">$this->label</span>";
			}

			// load the data of the field.
			global $page_state, $page_action;

			if ($page_state == "processing" && (value("changevariation") == "0") && ! ($forceLoadFromDB=="yes")) {
				//$fieldname = $this->table."_".$this->column;
				$fieldname = $this->name;

				if (value($fieldname) != "0") {
					$this->value = value($fieldname);
                } else {
                    $this->value = $_POST[$fieldname];
                }
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

		}

		/**
		 * Register a validator that is used for cheking input
		 * @param object references to validator
		 */
		function registerValidator(&$validator) { $this->validators[count($this->validators)] = $validator; }

		/**
		 * Filtering in SQL-Where-Clause, for doing checks for uniqueness only on certain
		 * recordsets.
		 * @param string SQL Where-Clause to add without where.
		 */
		function setFilter($filter) { $this->filter = $filter; }

		/**
		 * Checks, wheter a page is actually in INSERT or UPDATE mode an creates the corresponding
		 * Saveset.
		 */
		function process() {
			global $page_action;			
			if ($page_action == "INSERT")
				addInsert($this->table, $this->column, $this->value, $this->datatype);

			if ($page_action == "UPDATE") {
				addUpdate($this->table, $this->column, $this->value, $this->row_identifier, $this->datatype);				
			}
		}

		/**
		  * Writes the HTML for the Label and the input-element to the page.
		  * @return integer number of Columns, that were used by draw operation.
		  * Note: NX works with tables and the forms do automatic row-processing.
		  * So they need to know, how many columns the last draw-operation needed!
		  */
		function draw() {
			$cols = 0;
			$cols += $this->v_label->draw();
			$cols += $this->v_wuiobject->draw();
			return $cols;
		}

		/**
		 * checks the user input for rules defined in constructor.
		 * @return boolean true if okay, false if error occured.
		 */
		function check() {
			global $errors, $page_state, $page_action, $lang;
			if ($page_state == "processing" && value("changevariation") == "0" && value("changetranslation") == "0") {
				for ($i = 0; $i < count($this->validators); $i++) {
					$validation = $this->validators[$i]->validate($this->table, $this->column, $this->filter, $this->value, $this->oldvalue, $this->datatype);
					if ($validation != "") {
						$this->addError("", $validation);
					}
				}

				//check, if number
				if ($this->datatype == "NUMBER") {
					if ($this->value != "0" && $this->value != 0 && !is_numeric($this->value))
						$this->addError("NOTNUMBER", $lang->get("NOTNUMBER"));
				}
			}
		}

		/**
		 * Add an errormessage to the global error-String.
		 * Changes also the colors of the DBO so that users can find the wrong input easier.
		 * @param string errortext, to be added to the global error-string. for debugging only.
		 * @param string text, to be displayed in the DBO for telling the user, what he did wrong.
		 */
		function addError($error, $text) {
			global $errors;

			$errors .= "-" . $error;
			$this->v_label = new Label("lbl_" . $this->column, $this->label . "<br/><span style=\"font-weight:normal\">$text</span>", $this->err_style);
			$this->v_wuiobject->style = $this->err_style;
			$this->std_style = $this->err_style;
		}

		/**
		 * Creates the Validators for this item. internal use only.
		 * @param string Validators to create, e.g. MANDATORY|UNIQUE
		 */
		function ValidatorFactory($check) {
			if ($check == "")
				return false;

			$check = strtoupper($check);
			$validators = explode("&", $check);

			for ($i = 0; $i < count($validators); $i++) {
				$classname = "VA_" . $validators[$i];

				$tempValidator[$i] = &new $classname;

				if ($tempValidator[$i] != null)
					$this->registerValidator($tempValidator[$i]);
			}

			return true;
		}

		/**
		  * parses a paramterstring for parameters. Scans for
		  * parameters TYPE, SIZE, WIDTH and PARAM.
		  * @param string parameter-string to parse.
		  */
		function parseParam($param) {
			$params = explode(",", $param);

			for ($i = 0; $i < count($params); $i++) {
				$vals = explode(":", $params[$i]);

				switch (strtoupper($vals[0])):
					case "TYPE":
						$this->type = strtoupper($vals[1]);

						break;

					case "SIZE":
						$this->size = $vals[1];

						break;

					case "WIDTH":
						$this->width = $vals[1];

						break;

					case "PARAM": $this->parameter = $vals[1];
				endswitch;
			}
		}
	}
?>