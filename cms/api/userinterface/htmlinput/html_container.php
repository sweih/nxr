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
	 * Container for any HTML-Code you like.
	 * @package WebUserInterface
	 */
	class HTMLContainer extends WUIObject {
		var $container;
		/**
		  * Standard constructor.
		  * @param string the name of the WUIObject, used in the html name property
			* @param string sets the styles, which will be used for drawing
			* @param integer $columns Cellspan of the element in Table
		  */
		function HTMLContainer($name, $style, $columns = 2) { WUIObject::WUIObject($name, "", "", $style, $columns); }

		/**
		  * add new Text to the container.
		  * @param string Text that is to be added to the Container.
		  */
		function add($text) { $this->container .= $text; }

		/**
		  * Draw the container 
		  */
		function draw() {
			echo WUIObject::std_header();
			echo $this->container;
			echo WUIObject::std_footer();
			return $this->columns;
		}
	}
?>