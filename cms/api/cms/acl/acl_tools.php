<?
	/**
	 * @module ACLTools
	 * @package ContentManagement
	 */

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
	 * ACL Factory
	 * @param integer GUID of the item 
	 * @param string TYPE of the ITEM. allowed are: PAGE
	 */
	function aclFactory($guid, $type) {
		$type = strtoupper($type);

		$classname = "ACL_" . $type;
		$res = &new $classname($guid);
		$res->guid = $guid;
		return $res;
	}

	/**
	 * Retrieves the name to a Accessor - GUID
	 * @ param string $guid GUID of an Accessor (Group)
	 */
	function getAccessorName($guid) { return getDBCell("groups", "GROUP_NAME", "GROUP_ID = " . $guid); }

	/**
   * Retrieves the name to a Role-GUID
   * @ param string $guid GUID of the role
   */
	function getRoleName($guid) {
		if ($guid == "1")
			return "Administrator";

		return getDBCell("roles", "ROLE_NAME", "ROLE_ID = " . $guid);
	}
?>