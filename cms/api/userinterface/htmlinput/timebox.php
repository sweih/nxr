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
	 * Create a dropdown-Box for entering a time
	 * @version 1.00
	 * @package WebUserInterface
	 */
	class Timebox extends WUIObject {

		/**
		   * standard constructor
		   * @param string the name of the Input, used in the html name property
		   * @param string sets a value that will be displayed
		   * @param string sets the styles, which will be used for drawing
		   * @param integer $cells Cellspan of the element in Table.
		   */
		function Timebox($name, $style, $values = "", $selectedvalue = "", $cells = 1, $interval = 10, $min = 0, $max = 1439) {
			$this->name = $name;

			$this->style = $style;
			$this->selectedvalue = $selectedvalue;
			$this->values = $values;
			$this->cells = $cells;
			$this->interval = $interval;
			$this->min = $min;
			$this->max = $max;

			if ($this->vaules == "") {
				$i = 0;

				$count = 0;

				if ($min > 0)
					$i = $min;

				for ($i = 0; $i <= ($max); $i += $interval) {
					$h = floor(($i) / 60);

					if ($h < 10)
						$h = "0" . $h;

					$m = ($i % 60);

					if ($m < 10)
						$m = "0" . $m;

					$this->values[$count] = $h . ":" . $m;
					$count++;
				}
			}
		}

		/**
		  * Draws the layout-element
		  */
		function draw() {
			$output = WUIObject::std_header();

			$output .= "<select name=\"" . $this->name . "\" id=\"" . $this->name . "\" style=\"width:70px;\">\n";

			for ($i = 0; $i < count($this->values); $i++) {
				if ($this->values[$i] == $this->selectedvalue) {
					$output .= "<option value=\"" . $this->values[$i] . "\" selected>" . $this->values[$i] . "</option>\n";
				} else {
					$output .= "<option value=\"" . $this->values[$i] . "\">" . $this->values[$i] . "</option>\n";
				}
			}

			$output .= "</select>\n";

			$output .= WUIObject::std_footer();
			echo $output;
			return 1;
		}
	}

// $mytime = new Timebox("test", "style");
// $mytime->draw();
?>