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
	 * Create a TextInput which is specialized for entering HTML-Formatted Colors for form-layout.
	 * @version 1.00
	 */
	class ColorInput extends WUIObject {
		var $formName;

		/**
		   * standard Constructor
		   * @param string the name of the Input, used in the html name property
		   * @param string sets a value that will be displayed
		   * @param string sets the styles, which will be used for drawing
		   * @param string You must manually enter the name of the form here for JS reasons.
		   * @param integer Cellspan of the element in Table.
		   */
		function ColorInput($name, $value, $style, $formname, $cells = 1) {
			WUIObject::WUIObject($name, "", $value, $style, $cells);

			$this->formName = $formname;
		}

		/**
		  * Draws the layout-element
		  */
		function draw() {
			$output = WUIObject::std_header();

			$output .= "<table width=\"100%\" cellpadding=\"0\" cellspacing=\"0\" border=\"0\"><tr><td>";
			$output .= "<input type=\"text\" name=\"$this->name\" value=\"$this->value\" maxlength=\"7\" style=\"width:80px;\"";

			if (!isNetscape()) {
				$output .= " onChange=\"document.all.col_" . $this->name . ".bgColor=document.forms." . $this->formName . "." . $this->name . ".value\">&nbsp;\n";
			} else {
				$output .= ">&nbsp;";
			}

			$output .= "</td><td id=col_" . $this->name . " bgcolor=" . $this->value . " width=\"25\">&nbsp;</td><td width=\"300\">&nbsp;</td></tr></table>\n";
			$output .= WUIObject::std_footer();
			echo $output;
			return $this->columns;
		}
	}
?>