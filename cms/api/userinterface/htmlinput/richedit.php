<?
	/**********************************************************************
	 *	N/X - Web Content Management System
	 *	Copyright 2003 Fabian König 
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
	* Richedit for use in form-layout.
	* @version 1.00
	* @package WebUserInterface
	*/
	require_once $c["path"] . "ext/fckeditor24/fckeditor.php";

	class Richedit extends WUIObject {
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
		function Richedit($name, $value, $style, $rows = 5, $params = "", $width = 300, $action = "", $cells = 2) {
			WUIObject::WUIObject($name, "", $value, $style, $cells);
			$this->value = stripslashes($this->value);
			$this->action = $action;
			$this->iwidth = $width;
			$this->params = $params;
			$this->rows = $rows;
		}
		/**
	  * Draws the layout-element
	  */
		function draw() {
			global $c;
			$output = WUIObject::std_header();

			echo $output;
			$oFCKeditor = new FCKeditor($this->name);
			$oFCKeditor->Value = $this->value;
			$oFCKeditor->Width = '100%';
			$oFCKeditor->Height = 300;
			$oFCKeditor->BasePath = $c["host"].$c["docroot"]."ext/fckeditor24/";
			$oFCKeditor->Create();
			$output = WUIObject::std_footer();
			echo $output;
			return $this->columns;
		}
	}
?>