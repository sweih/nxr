<?
	/**********************************************************************
	 *	N/X - Web Content Management System
	 *	Copyright 2002 Sven Weih (sven@nxsystems.org), Fabian Koenig (fabian@nxsystems.org)
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
	* Create a select-box, for selecting one value. The Values are given in an array and will
	* be automatically processed.
	* @version 1.00
	* @package WebUserInterface
	*/
	class Select extends WUIObject {
		var $selectedValue;
		var $additionalAttribute = "";

		var $rows;
		var $params;
		var $iwidth;

		/**
		  * standard constructor
		  * @param string the name of the Input, used in the html name property
		  * @param string 2D-array with values and corresponding names. to be Created with db-funtions.
		  * @param string sets the styles, which will be used for drawing
		  * @param integer Value of values-array, that is to be preselected, 0 is standard.
		  * @param integer Height of the selectbox in lines
		  * @param string use to format the layout-item
		  * @param integer width in pixel
		  * @param integer Cellspan of the element in Table.
		  */
		function Select($name, $values, $style, $selectedValue = 0, $rows = 5, $params = "", $width = 300, $cells = 1) {
			WUIObject::WUIObject($name, "", $values, $style, $cells);

			$this->selectedValue = $selectedValue;
			$this->iwidth = $width;
			$this->rows = $rows;
			$this->params = $params;
		}

		/**
		  * Draws the layout-element
		  */
		function draw() {
			$output = WUIObject::std_header();

			$output .= "<select id=\"$this->name\" name=\"$this->name\" size=\"$this->rows\" style=\"width:$this->iwidth px;\" $this->params $this->additionalAttribute>\n";

			// note: values are in form: array( array(name, value) )
			for ($i = 0; $i < count($this->value); $i++) {
				$sel = "";

				if ($this->value[$i][1] == $this->selectedValue) {
					$sel = "selected";
				}

				$output .= "<option value=\"" . $this->value[$i][1] . "\" " . $sel . ">" . $this->value[$i][0] . "</option>\n";
			}

			$output .= "</select>";
			$output .= WUIObject::std_footer();
			echo $output;
			return $this->columns;
		}
	}
?>