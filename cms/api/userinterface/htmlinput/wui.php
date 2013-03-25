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
	 * WUIObject (WebUserInterface) is the base class for all form-items
	 * in the nx framework. It provides basic functions for drawing elements.
	 * @version 1.00
	 * @package WebUserInterface
	 */
	class WUIObject extends WUIInterface {
		var $name;

		var $text;
		var $value;
		var $style;
		var $columns = 1;
		var $width = 0;
		var $height = 0;

		/**
		  * standard constructor
		  * @param string the name of the WUIObject, used in the html name property
		   * @param string Additional Description of the item. Used for labels.
		  * @param string Value that will be filled in WUIO
		   * @param string sets the styles, which will be used for drawing
		   * @param integer $columns Cellspan of the element in Table
		   * @param integer $width width in pixel
		 * @param integer $height height in pixel.
		   */
		function WUIObject($name, $text, $value, $style, $columns = 1, $width = 0, $height = 0) {
			$this->name = $name;

			$this->text = $text;
			$this->value = $value;
			$this->style = $style;
			$this->columns = $columns;
			$this->width = $width;
			$this->height = $height;
		}

		/**
		 * check, whether the name of the Object is like $name
		 * @param string Name, which shall be checked for equal.
		 */
		function isName($name) { return ($this->name == $name) ? true : false; }

		/**
		 * internal user only. used for writing cell-tags.
		 */
		function std_header() {
			if ($this->width != 0) {
				return "<td width=\"$this->width\" colspan=\"$this->columns\" class=\"$this->style\" valign=\"top\">";
			} else {
				return "<td colspan=\"$this->columns\" class=\"$this->style\" valign=\"top\">";
			}
		}

		/**
		  * internal use only. used for writing cell-tags
		  */
		function std_footer() { return "</td>"; }
	}
?>