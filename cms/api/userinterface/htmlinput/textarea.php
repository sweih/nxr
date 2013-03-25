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
	* Textareabox for form-layout.
	* @version 1.00
	* @package WebUserInterface
	*/
	class Textarea extends WUIObject {
		var $action;

		var $iwidth;
		var $params;
		var $rows;

		/**
		   * standard constructor
		   * @param string the name of the Input, used in the html name property
		   * @param string sets a value that will be displayed
		   * @param string sets the styles, which will be used for drawing
		   * @param integer $rows Height of the textbox in lines
		   * @param string $params use to format the layout-item. the text will be entered into the textarea html-tag
		   * @param integer $width width in pixel
		   * @param string $action Javascript-action, that ist to be done when changing text.
		   * @param integer $cells Cellspan of the element in Table.
		   */
		function TextArea($name, $value, $style, $rows = 5, $params = "", $width = 300, $action = "", $cells = 1) {
			WUIObject::WUIObject($name, "", $value, $style, $cells);

			$this->action = $action;
			$this->iwidth = $width;
			$this->params = $params;
			$this->rows = $rows;
		}
		/**
	  * Draws the layout-element
	  */
		function draw() {
			$output = WUIObject::std_header();

			$output .= "<textarea name=\"$this->name\" id=\"$this->name\" style=\"width:" . $this->iwidth . "px;\" $this->params rows=\"$this->rows\"";

			if ($this->action != "") {
				$output .= " onChange=\"$this->action\"";
			}

			$output .= ">";
			$output .= $this->value;
			$output .= "</textarea>";
			$output .= WUIObject::std_footer();
			echo $output;
			return $this->columns;
		}
	}
?>