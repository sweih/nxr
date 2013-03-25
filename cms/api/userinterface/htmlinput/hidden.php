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
	 * HTML: Adds a Hidden-Field. Alternatively one may use the retain(key, value)
	 * function, which is not automatically processed in forms.
	 * @package WebUserInterface
	 */
	class Hidden extends WUIObject {

		/**
		 * standard constructor
		 * @param string Name of the Hidden Field
		 * @param string Value of the Hidden Field.
		 */
		function Hidden($name, $value) { WUIObject::WUIObject($name, "", $value, "", 0); }

		/**
		 * Write HTML for the WUI-Object.
		 */
		function draw() {
			$output = "<input type=\"hidden\" name=\"$this->name\" id=\"$this->name\" value=\"$this->value\">";

			echo $output;
			return 0;
		}
	}
?>