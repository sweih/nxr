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
	* Draws a database-connected Special-Input for durations
	* @package DatabaseConnectedInput
	*/
	class TimeInput extends DBO {

		/**
		 * standard constructor
		 * @param string Text that is to be shown as description or label with your object.
		 * @param string Table, you want to connect with the object.
		 * @param string column, you want to connect with the object.
		 * @param string first $row_identifier Usually to be generated with form->setPK. Identifies the
		 * row in db, you want to affect with operation. Keep empty for insert. (Example: stdEDForm)
		 * @param string $params Allowed parameter is:
		 * param:<Name of form> needed for js-reasons.
		 * @param string $check Does checks on user input. Allowed are MANDATORY (=not null)|UNIQUE. Separate with &.
		 * @param string $db_datatype Datatype of the database, you want to use. TIME is allowed only
		 */
		function TimeInput($label, $table, $column, $row_identifier = "1", $style = "", $params = "", $check = "", $db_datatype = "TIME") {
			DBO::DBO($label, $table, $column, $row_identifier, $params, $db_datatype, $check);

			global $page_state;

			if ($page_state == "processing") {
				$fieldname = $this->name;

				if (value($fieldname) != "0")
					$this->value = value($fieldname);
			} else {
				$this->value = getDBCell($table, $column, $row_identifier);
			}

			$this->v_wuiobject = new Timebox($this->name, $this->std_style, "", substr($this->value, 0, 5));
		}

		/**
		  * Does the checks, that are to be performed for this input.
		  * Including the check paramter given rules.
		  * Adds errors to the global error-string.
		  */
		function check() { }
	}
?>