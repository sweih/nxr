<?
	/**********************************************************************
	 *	N/X - Web Content Management System
	 *	Copyright 2002 Fabian Koenig
	 *
	 *	This file is part of N/X.
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

	//	require_once "../../../settings.inc.php";
	//	require_once "../../../config.inc.php";

	/**
	* Create an advanced select-box, for selecting one value. The Values and corresponding icons are given in an array and will
	* be automatically processed.
	* @version 1.00
	* @package WebUserInterface
	*/
	class IconSelect extends WUIObject {
		var $selectedValue;

		var $rows;
		var $params;
		var $iwidth;

		/**
		  * standard constructor
		  * @param string the name of the Input, used in the html name property
		  * @param string 5D-array with names, values, descriptions and icon-paths and icon-filenames.
		  * @param string sets the styles, which will be used for drawing
		  * @param integer Value of values-array, that is to be preselected, 0 is standard.
		  * @param integer Height of the selectbox in lines
		  * @param string use to format the layout-item
		  * @param integer width in pixel
		* @param integer height in pixel
		  * @param integer Cellspan of the element in Table.
		  */
		function IconSelect($name, $values, $style, $selectedValue = 0, $rows = 5, $params = "", $width = 0, $height = 0, $cells = 1) {
			global $c_theme;

			WUIObject::WUIObject($name, "", $values, $style, $cells);
			$this->selectedValue = $selectedValue;

			if ($width == 0) {
				$this->width = 350;
			} else {
				$this->width = $width;
			}

			if ($height == 0) {
				$this->height = 250;
			} else {
				$this->height = $height;
			}

			$this->rows = $rows;
			$this->params = $params;
		}

		/**
		  * Draws the layout-element
		  */
		function draw() {
			global $c_theme;

			$output = WUIObject::std_header();

			$output .= "<div align=\"left\" id=\"iconselect\" style=\"position:static; width:" . $this->width . "; height:" . $this->height . "; z-index:1; overflow: auto; border: 1px solid #999999; padding:2px;\">\n";
			$output .= "<table width=\"100%\" border=\"0\" class=\"iconselect_table\" cellspacing=\"10\" cellpadding=\"0\">\n";

			for ($i = 0; $i < count($this->value); $i++) {
				$sel = "";

				if ($this->value[$i]["value"] == $this->selectedValue) {
					$sel = " checked";
				}

				if ($this->value[$i]["icon"] == "")
					$this->value[$i]["icon"] = "default.png";

				$output .= "<tr><td valign=\"middle\" class=\"iconselect_table\">";
				$output .= "<input type=\"radio\" name=\"" . $this->name . "\" value=\"" . $this->value[$i][1] . "\"" . $sel . ">";
				$output .= "</td><td>";
				$output .= "<img src=\"" . $this->value[$i]["iconpath"] . $this->value[$i]["icon"] . "\" border=\"0\">";
				$output .= "</td><td valign=\"top\">";
				$output .= "<span style='font-size:12px;font-weight:strong;'>" . $this->value[$i]["name"] . "</span><br><span style='font-size:11px;color:#666;'>" . $this->value[$i]["description"] . "</span>";
				$output .= "</td></tr>\n";
			}

			$output .= "</table>\n";
			$output .= "</div>\n";

			$output .= WUIObject::std_footer();
			echo $output;
			return $this->columns;
		}
	}
?>