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
	 * Draws a to the database-connected Checkbox
	 * @package DatabaseConnectedInput
	 */
	class CheckboxTxtInput extends DBO {

		/**
		  * standard constructor
		  * @param string Text that is to be shown as description or label with your object.
		  * @param string Table, you want to connect with the object.
		  * @param string column, you want to connect with the object.
		  * @param string $row_identifier Usually to be generated with form->setPK. Identifies the
		  * row in db, you want to affect with operation. Keep empty for insert. (Example: stdEDForm)
		  * @param string $checkedValue Value that is to be used if field checkbox is submitted checked.
		  * @param string $uncheckedValue Value that is to be used if field checkbox is submitted unchecked.
		  * @param string Datatype of the database, you want to use. Allowed are CHAR|NUMBER|DATE|DATETIME|TIMESTAMP|PASSWORD
		  */
		function CheckboxTxtInput($label, $table, $column, $row_identifier = "1", $checkedValue = "1", $uncheckedValue = "0", $datatype = "NUMBER") {
			DBO::DBO($label, $table, $column, $row_identifier, "param:$uncheckedValue", $datatype);

			$checked = false;

			if ($this->value == $checkedValue) {
				$checked = true;
			} else {
				$this->value = $uncheckedValue;
			}

			$this->v_wuiobject = new CheckboxTxt($this->name, $checkedValue, $label, $this->std_style, $checked, 2);
		}

		/**
		 * Add a onclick or something else to the checkbox tag
		 * @param string complete attribute to add, e.g. onClick="alert('test');"
		 */
		function setJSPayload($payload="") {
			$this->v_wuiobject->setJSPayload($payload);
		}
		
		/**
		 * Draw the widget
		 */
		function draw() { return $this->v_wuiobject->draw(); }
	}
?>