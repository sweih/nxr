<?
	/**
	 * @module ACL
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
	 * Proxy Object for a group or user for accessing another object
	 * Works for users only at the momennt!
	 */
	class Accessor {
		var $guid;

		var $isGroup;
		var $isUser;
		var $isAdmin = false;
		var $memberGroups = null;

		/**
		 * Standard constructor
		 * @param integer GUID of the user or group that wants to access the object
		 */
		function Accessor($guid) {
			$this->guid = $guid;

			if (countRows("users", "USER_ID", "USER_ID = $guid") > 0) {
				$this->isUser = true;

				$this->isGroup = false;

				if (countRows("user_permissions", "USER_ID", "ROLE_ID= 1 AND GROUP_ID =1 AND USER_ID = $guid") > 0)
					$this->isAdmin = true;
			} else {
				$this->isUser = false;

				$this->isGroup = true;
				$this->isAdmin = false;
			}
		}

		/**
		 * returns an array with all GUIDs of Groups, the user is in.
		 * At array pos 0 the userGUID itself is returned!
		 */
		function isInGroups() {
			if ($this->memberGroups == null) {
				$this->memberGroups = array ();

				if ($this->isUser) {
					array_push($this->memberGroups, $this->guid);

					$groups = createDBCArray("user_permissions", "GROUP_ID", "USER_ID = " . $this->guid);

					if (is_array($groups)) {
						$this->memberGroups = array_merge($this->memberGroups, $groups);
					}
				}
			}

			return $this->memberGroups;
		}

		/**
		 * Checks, whether the user is administrator in system group
		 */
		function isSysAdmin() { return $this->isAdmin; }

		/**
		   * Checks, whether an user is allowed to perform the action according to his given groups.
		   * @param string SHORT-TAG of the Action
		   * @param string ID of the group
		   */
		function canDoInGroup($action, $group) {
			if ($this->isAdmin)
				return true;
			global $db;

			$myGroups = $this->isInGroups();
			if (in_array($group, $myGroups)) {
				$accessorRoles = createDBCArray("user_permissions", "ROLE_ID", "GROUP_ID = $group AND USER_ID = $this->guid");
				if (in_array(1, $accessorRoles))
				  return true;
				if (is_array($accessorRoles)) {
					$accessorRolesStr = implode(",", $accessorRoles);
					$sql = "SELECT r.FUNCTION_ID FROM role_sys_functions r WHERE r.FUNCTION_ID = UPPER('$action') AND r.ROLE_ID IN ($accessorRolesStr)";
					$query = new query($db, $sql);
					if ($query->getrow())
					  return true;
				}
				
			}              
			return false;          			
		}

		/**
		   * Checks, whether a user is allowed to perform an action generally in any group.
		   * @param string SHORT-TAG fo the Action
		   */
		function canDo($action) {
			if ($this->isUser) {
				global $db;

				if ($this->isAdmin)
					return true;

				$sql = "SELECT rsf.FUNCTION_ID FROM role_sys_functions rsf, user_permissions u WHERE u.ROLE_ID = rsf.ROLE_ID AND UPPER(rsf.FUNCTION_ID) = UPPER('$action') AND u.USER_ID=" . $this->guid;
				$query = new query($db, $sql);

				if ($query->getrow()) {
					$query->free();

					return true;
				} else {
					$query->free();

					return false;
				}
			} else {
				return false;
			}
		}
	}
?>