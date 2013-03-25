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
	 * Saves and Loads a variable to the session
	 *
	 * @param $varname string Name of the variable to save
	 */
	function SessionVar($varname) {
		global $HTTP_SESSION_VARS;

		$result = $HTTP_SESSION_VARS["$varname"];
		session_register ($varname);
		return $result;
	}

	/**
	 * Saves a variable to the session
	 *
	 * @param $varname string Name of the variable to save
	 */
	function SaveToSession($varname) { session_register ($varname); }

	/**
	 * Gets a variable from the session
	 *
	 * @param $varname string Name of the variable to load
	 */
	function LoadFromSession($varname) {
		global $HTTP_SESSION_VARS;

		return $HTTP_SESSION_VARS["$varname"];
	}
?>