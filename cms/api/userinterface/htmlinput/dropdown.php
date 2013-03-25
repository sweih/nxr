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
	* Create a dropdown-box, for selecting one value. The Values are given in an array and will
	* be automatically processed.
	* @version 1.00
	* @package WebUserInterface
	*/
	class Dropdown extends Select {

		/**
		   * standard constructor
		   * @param string the name of the Input, used in the html name property
		   * @param string 2D-array with values and corresponding names. to be Created with db-funtions.
		   * @param string sets the styles, which will be used for drawing
		   * @param integer $selectedValue Value of values-array, that is to be preselected, 0 is standard.
		   * @param integer $width width in pixel
		   * @param integer $cells Cellspan of the element in Table.
		   */
		function Dropdown($name, $values, $style, $selectedValue = 0, $width = 300, $cells = 1) { Select::Select($name, $values, $style, $selectedValue, 1, "", $width, $cells); }
	}
?>