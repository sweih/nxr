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
   * Checkbox-Class for Form-Layout
   * @version 1.00
   */
	class CheckboxTxt extends WUIObject {
		var $checked;
		var $playload = "";

		/**
		  * standard constructor
		  * @param string the name of the Input, used in the html name property
		* @param string Value, that will be returned if box is checked.
		  * @param string sets the styles, which will be used for drawing
		  * @param boolean $checked true or false, check or don't
		  * @param integer $cells Cellspan of the element in Table.
		  */
		function CheckboxTxt($name, $value, $title, $style, $checked = false, $cells = 1) {
			WUIObject::WUIObject($name, $title, $value, $style, $cells);

			$this->checked = $checked;
		}

		/**
		 * Add a onclick or something else to the checkbox tag
		 * @param string complete attribute to add, e.g. onClick="alert('test');"
		 */
		function setJSPayload($payload="") {
			$this->payload.=" ".$payload." ";
		}
		
		/**
		  * Draws the layout-element
		  */
		function draw() {
			$output = WUIObject::std_header();

			$output .= "<input type=\"checkbox\" name=\"$this->name\" id=\"$this->name\" value=\"$this->value\"";

			if ($this->checked) {
				$output .= " checked";
			}
			$output.= $this->payload;

			$output .= ">";
			$output .= "&nbsp;" . $this->text;
			$output .= WUIObject::std_footer();
			echo $output;
			return $this->columns;
		}
	}
?>