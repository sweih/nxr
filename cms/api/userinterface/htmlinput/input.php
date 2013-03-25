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
	 * Textinput-field for Form-Layout. Alternatively one may also create hidden fields with it.
	 * @version 1.00
	 */
	class Input extends WUIObject {
		var $type;

		var $action;
		var $size;
		var $iwidth;
		var $params;
		var $additionalParameters;

		/**
		  * standard constructor
		  * @param string the name of the Input, used in the html name property
		  * @param string Value that will be filled in box.
		  * @param string sets the styles, which will be used for drawing
		  * @param integer $size Max. number of chars to be entered.
		  * @param string $params use to format the layout-item
		  * @param integer $width width in pixel
		* @param string $type type of input. Allowed are text|hidden|password
		* @param string $action JS action, that is to be performed, when text changed.
		  * @param integer $cells Cellspan of the element in Table.
		  */
		function Input($name, $value, $style, $size = 255, $params = "", $width = 300, $type = "text", $action = "", $cells = 1) {
			WUIObject::WUIObject($name, "", $value, $style, $cells);

			$this->action = $action;
			$this->type = $type;
			$this->size = $size;
			$this->iwidth = $width;
			$this->params = $params;
			$this->additionalParameters= "";
		}

		/**
		  * Draws the layout-element
		  */
		function draw() {
			$output = WUIObject::std_header();

			$output .= "<input type=\"$this->type\" name=\"$this->name\" id=\"$this->name\" value=\"$this->value\" maxlength=$this->size style=\"width:" . $this->iwidth . "px;\"".$this->additionalParameters." ";

			if ($this->action != "") {
				$output .= " onChange=\"$this->action\"";
			}

			$output .= " $this->params>";
			$output .= WUIObject::std_footer();
			echo $output;
			return $this->columns;
		}
	}
	
	class InputURL extends Input {
				/**
		  * Draws the layout-element
		  */
		function draw() {
			$output = WUIObject::std_header();

			$output .= "<input type=\"$this->type\" name=\"$this->name\" id=\"$this->name\" value=\"$this->value\" maxlength=$this->size style=\"width:" . ($this->iwidth-60) . "px;\"".$this->additionalParameters." ";

			if ($this->action != "") {
				$output .= " onChange=\"$this->action\"";
			}

			$output .= " $this->params>";
			$output.= '<input style="width:60px;" type="button" name="churl" value="Check" onClick="window.open(document.getElementById(\''.$this->name.'\').value, \'check\', \'\')">';
			$output .= WUIObject::std_footer();
			echo $output;
			return $this->columns;
		}
	}
?>