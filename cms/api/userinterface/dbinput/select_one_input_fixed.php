<?php
	/**********************************************************************
	 *	N/X - Web Content Management System
	 *	Copyright 2002 Sven Weih, Thomas Gauweiler
	 *	FZI Research Center for Information Technologies
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
	* @package DatabaseConnectedInput
	*/

	/**
	 * draws a to the database-connected dropdown or select field, which you can select only one value.
	 * @package DatabaseConnectedInput
	 */
	class SelectOneInputFixed extends DBO {

		/**
		 * standard constructor
		 * @param string Text that is to be shown as description or label with your object.
		 * @param string Table, you want to connect with the object.
		 * @param string column, you want to connect with the object.
		 * @param array values, the list of input-values.
		 * @param string $row_identifier Usually to be generated with form->setPK. Identifies the
		 * row in db, you want to affect with operation. Keep empty for insert. (Example: stdEDForm)
		 * @param string allowed is type:DROPDOWN|SELECT as paramter for choosing a dropdown or selectbox. For select
		 * you may also use size:XX for number of rows.
		 * @param string $check Does checks on user input. Allowed are MANDATORY (=not null)|UNIQUE. Unique will not make sense in most cases!
		 * @param string $datatype Datatype of the database, you want to use. Allowed are CHAR|NUMBER|DATE|DATETIME|TIMESTAMP|PASSWORD
		 */
		function SelectOneInputFixed($label, $table, $column, $values, $row_identifier = "1", $param = "type:dropdown", $check = "MANDATORY", $datatype = "NUMBER") {
			DBO::DBO($label, $table, $column, $row_identifier, $param, $datatype, $check);

			$dsvalues = array ();

			if (is_array($values[0])) {
				$dsvalues = $values;	
			} else {
				foreach ($values as $key => $value) {
					$dsvalues[] = array (
						$value,
						$key
					);
				}
			}

			switch ($this->type):
				case "DROPDOWN":
					$this->v_wuiobject = new Dropdown($this->name, $dsvalues, $this->std_style, $this->value, $this->width);

					break;

				case "SELECT":
					$this->v_wuiobject = new Select($this->name, $dsvalues, "", $this->value, $this->size, $this->std_style, $this->width);

					break;

				default: echo ("SelectOneInputFixed: ERROR! type $this->type is invalid<br>\n");
			endswitch;
		} // function SelectOneInputFixed(..)
	}     // class SelectOneInputFixed extends DBO
?>