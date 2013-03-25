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
	 * Create a Filebox for selecting a File for upload.
	 * @version 1.00
	 */
	class Filebox extends WUIObject {
		var $size;

		var $iwidth;
		var $allow;
		var $additionalAttribute = "";

		/**
		  * standard constructor
		  * @param string the name of the Input, used in the html name property
		  * @param string sets the styles, which will be used for drawing
		  * @param string mime types, that are allowed to upload. e.g. text or images
		  * @param integer $size Number of chars, that can be entered.
		  * @param integer $width width in pixel
		  * @param integer $cells Cellspan of the element in Table.
		  */
		function Filebox($name, $style, $allow, $size = 255, $width = 300, $cells = 1) {
			WUIObject::WUIObject($name, "", "", $style, $cells);

			$this->size = $size;
			$this->iwidth = $width;
			$this->allow = $allow;
		}

		
		/**
		  * Draws the layout-element
		  */
		function draw() {
			$output = WUIObject::std_header();

			$output .= "<input type=\"file\" name=\"$this->name\" id=\"$this->name\"  accept=\"$this->allow\" style=\"width:" . $this->iwidth . "px;\" enctype=\"multipart/form-data\" ".$this->additionalAttribute.">";
			$output .= WUIObject::std_footer();
			echo $output;
			return $this->columns;
		}
	}
?>