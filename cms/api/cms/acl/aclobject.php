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

	$fi_accessor = 0;
	$fi_role = 0;

	/**
	 * Base-Class for Managing Access to all CMS-Objects
	 * To be used with ACL-Factory.
	 */
	class ACLObject {
		var $guid;

		var $owner = 1;
		var $lockedBy;
		var $lockedAt;
		var $isInternal = 0;
		var $isSystem = 0;
		var $inherit = 1;
		var $isDisabled = 0;
		var $typeId = 0;
		var $accessors = null;
		var $parentACL = 0;
		var $rootNodeId = "0";
		var $effectiveACLGuid = 0;
		
		/**
		   * Standard constructor. Overwrite with own one and call this one.
		   * @param integer GUID of the ACLObject
		   */
		function ACLObject($guid) { $this->guid = $guid;
			// set type here also!	
			}

		/**
		   * Check, whether the given GUID has general Access to this object
		   * @param integer GUID of an accessor that wants to access this object.
		   */
		function hasAccess($guid) {
			// checkOwner...
			if ($this->owner == $guid)
				return true;
			
			// get all the groups the accessor is in.
			$accessor = new Accessor($guid);

			if ($accessor->isSysAdmin())
				return true; // Administrator

			$groups = $accessor->isInGroups();

			// get all the groups, the object is in.
			$myaccessors = array ();
			array_push($myaccessors, $this->owner);
			
			for ($i = 0; $i < count($this->accessors); $i++) {
				array_push($myaccessors, $this->accessors[$i]["ACCESSOR"]);
			}

			if (count(array_intersect($myaccessors, $groups)) > 0) {
				return true;
			} else {
				return false;		
			}
		}

		/**
		 * Checks, whether the currently logged in user is allowed to perform a action on
		 * this object.
		 * @param string Title of the action to check for.
		 */
		 function checkAccessToFunction($action) {
		   global $auth;		   
		   return $this->checkAccessToFunction2($auth->userId, $action);			 	
		 }
		
		/**
		   * Check, whether an accessor is allowed to perform a special operation on the Object.
		   * @param integer GUID of the user or group
		   * @param string Title of the action to check for.
		   */
		function checkAccessToFunction2($guid, $action) {
			global $db, $auth;		
			if ($auth->user == "Administrator") 
			  return true;

			$action = strtoupper($action);
			$result = false;
			$accessor = new Accessor($guid);
			
			// determine accessors the user is in.
			$accessorGroups = $accessor->isInGroups();			
			$accessorGroupsStr = implode(',', $accessorGroups);
						
			// check for owner
			if (in_array($this->owner, $accessorGroups)) {				
				if ($accessor->canDoInGroup($action, $this->owner))				
					return true;
			}
				
			// determine roles, the function is in.			
			$actionRoles = createDBCArray("role_sys_functions", "ROLE_ID", "UPPER(FUNCTION_ID) = '$action'");
			if (! is_array($actionRoles)) {
			  $actionRoles = array();
			 }
			array_push($actionRoles, 1); // Administrator Role
			$actionRolesStr = implode(",", $actionRoles);	
			
			$resourceGroups = array();
			for ($i=0; $i < count($this->accessors); $i++) {
			  if (in_array($this->accessors[$i]["ROLE"], $actionRoles)) {
			    array_push($resourceGroups, $this->accessors[$i]["ACCESSOR"]);	
			  }	
			}
			
								
			$bothGroups = array();
			if (is_array($resourceGroups)) 
			  $bothGroups = array_intersect($resourceGroups, $accessorGroups);
			  if (is_array($bothGroups)) {
			  foreach ($bothGroups as $group) {				
				$sql = "SELECT up.USER_ID FROM user_permissions up, acl_relations r WHERE up.USER_ID = $guid AND up.GROUP_ID = ".$group." AND up.ROLE_ID IN ($actionRolesStr) AND r.ROLE_ID IN ($actionRolesStr) AND r.ACCESSOR_GUID IN ($accessorGroupsStr) AND r.GUID= ".$this->effectiveACLGuid;			
				$query = new query($db, $sql);				
				if ($query->getrow()) {
				  return true;				   
				 } else {				   
				}
				$query->free();
			  }
			}			
			/**
			
			// check for foreign relations....
			if ($accessorGroupsStr != "") {		
				$accessorRolesOnResource = createDBCArray("acl_relations", "ROLE_ID", "GUID = $this->guid AND ACCESSOR_GUID IN ($accessorGroupsStr)");				
				if (in_array(1, $accessorRolesOnResource))  // Admin Rechte
				  $result = true;
				if (is_array($accessorRolesOnResource)) {
					$accessorRolesOnResourceStr = implode(',', $accessorRolesOnResource);										
					$accessorFunctionsOnResource = createDBCArray("role_sys_functions", "FUNCTION_ID", "ROLE_ID IN ($accessorRolesOnResourceStr)");															
					if (is_array($accessorFunctionsOnResource)) {
						if (in_array(strtoupper($action), $accessorFunctionsOnResource)) 
					  		$result = true;				
					}
				}
			}*/
			return false;
		}

		/**
		   * Find the node, which gives an object all its permissions
		   */
		function getRootACLGUID() {
			if ($this->inherit != "0") {
				$inherits = 1;
			} else {
				$inherits = 0;
			}

			$parent = $this->guid;

			while ($inherits == 1 && $parent != "1" && $parent != 0 && $parent != "0" && $parent != "") {
				$parent = $this->getParentACLGUID($parent);

				$inherits = getDBCell("acl_management", "INHERIT", "GUID = " . $parent);

				if ($inherits == "" && $parent != "" && $parent != "0")
					$inherits = 1;
			}

			if ($parent == "")
				$parent = "0";

			return $parent;
		}

		/**
		  * Get the name of the parent object if ACL inheritance...
		  */
		function getParentName() { return "Parent"; }

		/**
		   * Get the Parent ACL-GUID of the object. Used for inherited permissions
		   * MUST BE overwritten!!
		   */
		function getParentACLGUID($guid) {
			echo "overwrite getParentACLGUID!!";

			return 0;
		}

		/**
		   * Remove a certain Accessor form the ACL
		   * @param integer GUID of the Accessor to remove.
		   */
		function removeAccessor($guid) {
			global $fi_accessor;

			$fi_accessor = $guid;

			if (is_array($this->accessors)) {
				$this->accessors = array_filter($this->accessors, "_filterAccessor");
			}
		}

		/**
		   * Removes a role from an Accessor from the ACL
		   * @param integer GUID of the Accesssor
		   * @param integer GUID of the Role to remove
		   */
		function removeAccessorRole($accessor, $role) {
			global $fi_accessor, $fi_role;

			$fi_accessor = $accessor;
			$fi_role = $role;

			if (is_array($this->accessors)) {
				$this->accessors = array_filter($this->accessors, "_filterAccessorRoles");
			}
		}

		/**
		   * Load the settings of the Object from database
		   * Must be called if wanted to work with it!
		   */
		function load() {
			global $db;

			$sql = "SELECT * FROM acl_management WHERE GUID=" . $this->guid;
			$query = new query($db, $sql);

			if ($query->getrow()) {
				$this->inherit = $query->field("INHERIT");

				if ($this->inherit != "0" && $this->guid != "0" && $this->guid != "1") {
					$guid = $this->guid;

					$this->guid = $this->getRootACLGUID();
					$this->load();
					$this->parentACL = $this->guid;
					$this->guid = $guid;
					$this->inherit = "1";
				} else {
					$this->effectiveACLGuid = $this->guid;
					$this->isInternal = $query->field("INTERNAL");
					$this->isDisabled = $query->field("DISABLED");
					$this->isSystem = $query->field("SYSTEM");
					$this->owner = $query->field("OWNER_GUID");
					$this->lockedBy = $query->field("LOCKED_BY");
					$this->lockedAt = $query->field("LOCKED_AT");

					$this->accessors[0] = array("ROLE" => 1, "ACCESSOR" => $this->owner);
					
					// now get roles and groups for this acl-object
					$sql = "Select ACCESSOR_GUID, ROLE_ID from acl_relations WHERE GUID=" . $this->guid;
					$query = new query($db, $sql);
					
					$counter = 1;

					while ($query->getrow()) {
						$ar["ROLE"] = $query->field("ROLE_ID");
						$ar["ACCESSOR"] = $query->field("ACCESSOR_GUID");
						$this->accessors[$counter] = $ar;
						$counter++;
					}
				}
			} else {
				$this->setSystemDefaults();
			}

			$query->free();			
		}

		/**
		   * Saves the current state of the ACL-Object. The Objects needs to exist already.
		   * If you are not sure, if the object exists, use the sync-function
		   */
		function save() {
			global $db;
			$this->saveEx();
			// save Accessors
			// - clear old permissions first.
			deleteRow("acl_relations", "GUID = " . $this->guid);
		
			// - write new permissions
			for ($i = 0; $i < count($this->accessors); $i++) {
				$sql = "INSERT INTO acl_relations (GUID, ROLE_ID, ACCESSOR_GUID) VALUES (" . $this->guid . ", " . $this->accessors[$i]["ROLE"] . ", " . $this->accessors[$i]["ACCESSOR"] . ")";

				$query = new query($db, $sql);
				$query->free();
			}
		}

		/**
		   * Save the ACLObject. Does not save the accessors.
		   */
		function saveEx() {
			// save general settings
			$uds = new UpdateSet("acl_management", "GUID = " . $this->guid);
			$uds->add("INTERNAL", $this->isInternal, "NUMBER");
			$uds->add("DISABLED", $this->isDisabled, "NUMBER");
			$uds->add("SYSTEM", $this->isSystem, "NUMBER");
			$uds->add("INHERIT", $this->inherit, "NUMBER");
			$uds->add("OWNER_GUID", $this->owner, "NUMBER");
			$uds->execute();
		}

		/**
		   * Syncs an ACLObject with the database. Creates the object, if it does not exist.
		   */
		function sync() {
			if (countRows("acl_management", "GUID", "GUID = " . $this->guid) < 1) {
				$crs = new CreateSet("acl_management");

				$crs->add("GUID", $this->guid, "NUMBER");
				$crs->add("TYPE_ID", $this->typeId, "NUMBER");
				$crs->execute();
			}

			// now save the data...
			$this->save();
		}

		/**
		   * Syncs an ACLObject. Compare to sync, but does not write accessors.
		   */
		function syncEx() {
			if (countRows("acl_management", "GUID", "GUID = " . $this->guid) < 1) {
				$crs = new CreateSet("acl_management");

				$crs->add("GUID", $this->guid, "NUMBER");
				$crs->add("TYPE_ID", $this->typeId, "NUMBER");
				$crs->execute();
			}

			// now save the data...
			$this->saveEx();
		}

		/**
		   * Use, to set the ACL of an object to the systems default.
		   */
		function setSystemDefaults() {
			global $db;			
			$sql = "SELECT * FROM acl_management WHERE GUID=".$this->rootNodeId;
			$query = new query($db, $sql);
			if ($query->getrow()) {
				$this->isInternal = $query->field("INTERNAL");
					$this->isDisabled = $query->field("DISABLED");
					$this->isSystem = $query->field("SYSTEM");
					$this->owner = $query->field("OWNER_GUID");
					$this->lockedBy = $query->field("LOCKED_BY");
					$this->lockedAt = $query->field("LOCKED_AT");

					// now get roles and groups for this acl-object
					$sql = "Select ACCESSOR_GUID, ROLE_ID from acl_relations WHERE GUID=" .$this->rootNodeId;
					$query = new query($db, $sql);
					$counter = 0;

					while ($query->getrow()) {
						$ar["ROLE"] = $query->field("ROLE_ID");
						$ar["ACCESSOR"] = $query->field("ACCESSOR_GUID");
						$this->accessors[$counter] = $ar;
						$counter++;
					}	
			} else {
			
				$this->isSystem = 0;
				$this->isDisabled = 0;
				$this->isInternal = 0;
				$this->inherit = 1;
				$this->accessors = null;
				$this->lockedBy = "";
				$this->lockedAt = "";
				$this->owner = 1;			
			}
			
			if ($this->guid=="0" || $this->guid == "1")
			  $this->inherit = 0;
		}

		/**
		   * Destroy the ACL-Object permanently
		   */
		function destroy() {
			deleteRow("acl_management", "GUID = " . $this->guid);

			deleteRow("acl_relations", "GUID = " . $this->guid);
		}
	}

	/**
	 * Help-Function
	 * @param array Array
	 */
	function _filterAccessor($var) {
		global $fi_accessor;

		if ($var["ACCESSOR"] != $fi_accessor)
			return $var;
	}

	/**
	 * Help-Function
	 * @param array Array
	 */
	function _filterAccessorRoles($var) {
		global $fi_role, $fi_accessor;

		if (($var["ACCESSOR"] != $fi_accessor) || ($var["ROLE"] != $fi_role))
			return $var;
	}
?>