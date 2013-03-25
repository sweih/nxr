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
	* Draws a to the database-connected textinput field.
	* @package DatabaseConnectedInput
	*/
	class TextInput extends DBO {

		/**
		  * standard constructor
		  * @param string Text that is to be shown as description or label with your object.
		  * @param string Table, you want to connect with the object.
		  * @param string column, you want to connect with the object.
		  * @param string $row_identifier Usually to be generated with form->setPK. Identifies the
		  * row in db, you want to affect with operation. Keep empty for insert. (Example: stdEDForm)
		  * @param string $params Allowed parameters are:
		  * type:TEXT|TEXTAREA|COLOR|RICH|URL;
		  * size:XX Size of Input in chars.
		  * width:XX Size of Input in pixel.
		  * @param string $check Does checks on user input. Allowed are MANDATORY (=not null)|UNIQUE. Separate with &.
		  * @param string $db_datatype Datatype of the database, you want to use. Allowed are CHAR|NUMBER|PASSWORD
		  */
		function TextInput($label, $table, $column, $row_identifier = "1", $params = "type:text,size:10,width:100", $check = "", $db_datatype = "TEXT") {
			DBO::DBO($label, $table, $column, $row_identifier, $params, $db_datatype, $check);
			switch ($this->type):
				case "TEXT":
					$this->v_wuiobject = new Input($this->name, $this->value, $this->std_style, $this->size, $this->parameter, $this->width);

					break;

				case "TEXTAREA":
					$this->v_wuiobject = new Textarea($this->name, $this->value, $this->std_style, $this->size, $this->parameter, $this->width);

					break;

				case "COLOR":
					$this->v_wuiobject = new ColorInput($this->name, $this->value, $this->std_style, $this->parameter);

					break;

				case "RICH":
					$this->v_wuiobject = new Richedit($this->name, $this->value, $this->std_style, $this->size, $this->parameter, $this->width);
					break;
				case "URL":
					$this->v_wuiobject = new InputURL($this->name, $this->value, $this->std_style, $this->size, $this->parameter, $this->width);

					break;
			endswitch;
		}
		
	}
?>