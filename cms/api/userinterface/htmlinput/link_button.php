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
	 * Button-Class for Form-Layout.
	 * The Button will not be placed within table's td-tags, as other layout elements do!
	 * @version 1.00
	 */
	class LinkButton extends LinkButtonInline {

		/**
		  * standard constuctor
		  * @param string the name of the Input, used in the html name property
		  * @param string Text that will be placed on the button and will be submitted.
		  * @param string sets the styles, which will be used for drawing
		* @param string $type type of input. Allowed are submit|button|reset
		* @param string $action JS action, that is to be performed, when button is clicked
		* @param string Name of the form, the button is in.
		  * @param integer $cells Cellspan of the element in Table.
		  */
		function LinkButton($name, $value, $style, $type = "button", $action = "", $formname = "form1", $cells = 1) { LinkButtonInline::LinkButtonInline($name, $value, $style, $type, $action, $formname, $cells); }

		/**
		  * Draws the layout-element
		  */
		function draw() { echo LinkButtonInline::draw(); }
	}
?>