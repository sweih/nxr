<?
	/**********************************************************************
	 *	N/X - Web Content Management System
	 *	Copyright 2004 Fabian Koenig, fabian@nxsystems.org
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
	 * HTML: Adds a Hidden-Field and takes care that only one field with the same name is added
	 * uses lastest value handed over for output
	 * function, which is not automatically processed in forms.
	 * @package WebUserInterface
	 */
	class SingleHidden extends WUIObject {
		
		var $form;

		/**
		 * standard constructor
		 * @param string Name of the Hidden Field
		 * @param string Value of the Hidden Field.
		 */
		function SingleHidden($name, $value, $form="form1") { 
			global $hidden_fields;
			$this->form = $form;
			if (!isset($hidden_fields)) {
				$hidden_fields = array();
			}
			WUIObject::WUIObject($name, "", $value, "", 0); 
		}

		/**
		 * Generate HTML for the WUI-Object.
		 */
		function get() {
			global $hidden_fields;
			if (!isset($hidden_fields[$this->form][$this->name])) {
				$hidden_fields[$this->form][$this->name] = $this->value;		
				$output = "<input type=\"hidden\" name=\"$this->name\" id=\"$this->name\" value=\"".$hidden_fields[$this->form][$this->name]."\">";
				return $output;
			} else {
				return "";
			}
		}

		/**
		 * Write HTML for the WUI-Object.
		 */		
		function draw() {
			global $hidden_fields;
			echo $this->get();
			return 0;
		}
		
	}
?>