<?php

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
  * This is the base class on which all security and user-checking-functions work
  * with. For making a page secure, you only need to create an instance of this class
  * with optional parameter role, that is neccessary to access the page. For an example
  * take just any page in the modules section.
  * @package Management
  */

class auth {

	var $loggedIn = false;
	var $session = "";
	var $remote = "";
	var $myCookie="nxcms";
	var $userId;
	var $user;
	var $userName;
	var $sys;
	var $group = 1;
	var $isJS = false;
	var $useAgent = false;
	var $lang = "EN";
	var $roles=array();
	var $allowedFunctions=array();
	
    /**
	 * standard constructor
	 * @param string Role, a user must have to access the page. Note that Administrators
	 * can access just any page and that a page wirh no permissions set may be accessed
	 * by every user of the CMS. A page can be accessible by several user-roles, just
	 * use '&' to combine roles.
	 * @param boolean Flag, that says if the user must be in systemgroup (when true.)
	 */
	function auth($permission="ANY", $sys = false) {
		global $c, $db;		
		
		$this->sys = $sys;
		// collect data
		if ($_SERVER['REMOTE_ADDR'] != "") {
			$this->remote = $_SERVER['REMOTE_ADDR'];;
		}
        
		if (value("passwd")!="0" && value("login")!="0") {
			//try to login user
			if ($this->checkLogin(value("login"), value("passwd"))) {
				$this->createSession($this->userId);
			} else {
				$log1 = new DBLog("AUTH");
				$log1->log("Login failed for user ".value("login")." with IP ".$_SERVER["REMOTE_ADDR"]);
				$db->close();
				header ("Location: ".$c["docroot"]."api/auth/loginform.php?status=failed");
				exit;
			}
		} else if (value("sid", "NOSPACES")!="0") {
    		   $this->session = value("sid", "NOSPACES");//$GLOBALS["sid"];
		}
		
		
		// verify data
		if (!$this->validateSession()) {
				$db->close();
				header ("Location: ".$c["docroot"]."api/auth/loginform.php");
			exit;
		} else {
			$this->loggedIn = true;
		}
		if (!$this->loggedIn) {
			// forward to login page
			$db->close();
			header ("Location: ".$c["docroot"]."api/auth/loginform.php");
			exit;
		}

		
		if ($this->loggedIn) {			
			$sql = "SELECT LANGID, USE_JAVASCRIPT, USE_AGENT FROM users WHERE USER_ID = ".$this->userId;
			$query = new query($db, $sql);
			$query->getrow();
			$this->lang = $query->field("LANGID");			
			$this->isJS = $query->field("USE_JAVASCRIPT");
			if (PMA_USR_BROWSER_AGENT == 'IE')
				$this->useAgent = $query->field("USE_AGENT");
			if ($this->lang !="") {
				global $c, $lang;
				$lang->language = $this->lang;
				
			}
			  			//get list of roles this user has
		
			// preselect system group, if system flag is not deselected.
			$additum = "";
			if ($this->sys) $additum = " AND (p.GROUP_ID = $this->group OR p.GROUP_ID=1)";
			// get all roleIDs the user has
			$sql = "SELECT DISTINCT r.ROLE_NAME, p.ROLE_ID FROM roles r, user_permissions p WHERE p.ROLE_ID = r.ROLE_ID AND p.USER_ID = $this->userId".$additum;
		
			$query = new query($db, $sql);
			while ($query->getrow()) {
				$this->roles[$query->field("ROLE_ID")] = strtoupper($query->field("ROLE_NAME"));
			}
			$query->free();
		
				
			//get list of functions allowed for this user
			$rolesString = "";
			$tmpRoleIDs = array_keys($this->roles);
			for($i=0;$i < count($tmpRoleIDs);$i++) $rolesString.=",".$tmpRoleIDs[$i];
			$rolesString = substr($rolesString,1);
		
			$sql="SELECT DISTINCT rsf.FUNCTION_ID FROM role_sys_functions as rsf WHERE rsf.ROLE_ID in ( ".$rolesString." )";
			$query = new query($db, $sql);
			$counter=0;
			while ($query->getrow()) {
				$this->allowedFunctions[$counter] = $query->field("FUNCTION_ID");
				$counter++;
			}
			$query->free();
			
			
	        if (($permission!="ANY" || ! $this->checkAccessToFunction("ALLOW_CMS_LOGIN")) && $this->userName != "Administrator") {
		       // check permission.
			if (!$this->checkPermission($permission, false) || ! $this->checkAccessToFunction("ALLOW_CMS_LOGIN")) {
				// no permission
				$db->close();
				header ("Location: ".$c["docroot"]."api/auth/login.php");
				exit;
			}
		}
		}
	}

	/**
	 * Returns the Security Id of an user.
	 * @return string Security ID (SID)
	 */
	function getSid() {
		return "sid=".$this->session;
	}

	
	/**
	 * This method checks, if a user has rights to access the page.
	 * In detail this means, that the method just checks, if the user has one of the specified roles
	 * on just any group he belongs to.
	 * @param string Roles, the user must have.
	 * @param boolean if true, the user must have the permission in the system-group, if false
	 * the permission must be in just any group. The system-group can be overwritten by setGroup-Function.
	 * @return boolean true, if user has role, fales else.
	 */
 	 function checkPermission($perm, $sys = true) {
        $perm = strtoupper($perm);
		$returns = false;
		if ($perm=="ANY") {
			$returns = true;
		} else {

        if ($this->checkAccessToFunction($perm)) return true;
        global $db;
		
		// preselect system group, if system flag is not deselected.
		$additum = "";
		if ($this->sys || $sys) $additum = " AND (p.GROUP_ID = $this->group OR p.GROUP_ID=1)";
		// get all roles the user has
		$sql = "SELECT DISTINCT r.ROLE_NAME FROM roles r, user_permissions p WHERE p.ROLE_ID = r.ROLE_ID AND p.USER_ID = $this->userId".$additum;
		
		$query = new query($db, $sql);
		while ($query->getrow()) {
			$usergroup=strtoupper($query->field("ROLE_NAME"));
			if (stristr($perm, $usergroup)) $returns = true;
			if ($usergroup == "ADMINISTRATOR") $returns = true;
		}
		$query->free();
		
		}
		return $returns;
	}


    /**
     * Checks, whether a user is allowed to call this function or not.
     *
     * @param string Name of the function to call.
     */
	function checkAccessToFunction($function_ID){
        if ($function_ID=="ANY" || in_array("ADMINISTRATOR", $this->roles)) {
            return true;
		} else {
			$functionArray = explode("|",$function_ID);
			for($i=0;$i < count($functionArray);$i++) {
				if(in_array($functionArray[$i],$this->allowedFunctions)) {
                     return true;
				}
			}
		}
		return false;
	}

	
	
	

	/**
	 * Sets a default group for doing the permission-checks.
	 * @param integer ID of the Group, the permission-Checks shall be done. Use 1 to set system-Group. 
	 */
	 function setGroup($groupId=1) {
	 	$this->group = $groupId;
	 }
	 
	/**
	 * Checks, if a session is still correct. If so, returns true, else false.
	 * The procedure checks, if the timeout is not reached yet and if the given sid
	 * is correct. Also the remote address of the user is checked. The maximum login time
	 * that is allowed is 4 hours at the moment. After that time, a new login is necessary.
	 * @return boolean true if Session valid, else false.
	 */
	function validateSession() {
		global $db, $c;
		if ($this->session !="") {
			if($c["disalbehostchecking"]) {
               $sql = "SELECT U.USER_ID, U.USER_NAME, U.FULL_NAME FROM user_session S, users U WHERE U.USER_ID = S.USER_ID AND S.SESSION_ID='$this->session' AND U.ACTIVE=1 AND NOW() <= DATE_ADD(S.LAST_LOGIN, INTERVAL 4 HOUR)";
			} else {
               $sql = "SELECT U.USER_ID, U.USER_NAME, U.FULL_NAME FROM user_session S, users U WHERE U.USER_ID = S.USER_ID AND S.SESSION_ID='$this->session' AND S.REMOTE_ADDRESS = '$this->remote' AND U.ACTIVE=1 AND NOW() <= DATE_ADD(S.LAST_LOGIN, INTERVAL 4 HOUR)";
			}
			
			$query = new query($db, $sql);
			if 	($query->count()==1) {
				// login successfull
				$query->getrow();
				$this->userId = $query->field("USER_ID");
				$this->user = $query->field("USER_NAME");
				$this->userName = $query->field("FULL_NAME");
				
				if ((reg_load("SYSTEM/MAINTENANCE/BB") != "1") || (sameText("Administrator", $this->user)))
				  return true;
			} else {
				return false;
			}
		}
		return false;
	}

	/**
	 * Deletes a session for security reasons and logs the user out
	 * of the cms system.
	 */
	function logout() {
		global $db;
		$sql = "UPDATE user_session SET SESSION_ID='' WHERE SESSION_ID = '$this->session'";
		
		$query = new query($db, $sql);
		
	}
	
	/**
	 * Creates an array with all group-ids, the user is in.
	 * @return integer scalar array with all group-ids.
	 */
	function getGroups() {
		global $db;
		return createDBCArray("user_permissions", "GROUP_ID", "USER_ID = $this->userId");
	}
	
	/**
	 * Checks, whether a user is in a group or not.
	 * @param string Name of the group.
	 * @returns boolean true|false
	 */
	function isInGroup($groupName) {
	  $groupName=strtoupper($groupName);
	  $groupId = getDBCell("groups", "GROUP_ID", "UPPER(GROUP_NAME) = '$groupName'");
	  if ($groupId != "") {
	  	if (countRows("user_permissions", "GROUP_ID", "GROUP_ID = $groupId AND USER_ID = ".$this->userId)>0) {
	  	  return true;
	  	} else {
	  	  return false;	
	  	}
	  } else {
	  	return false;
	  }
	}
	
	/**
	 * This method checks, if a user of a group has rights to access the page.
	 * In detail this means, that the method just checks, if the user has one of the specified roles
	 * on the group he belongs to.
	 * @param integer ID of the group, the user is in and the role is to be checked.
	 * @param string Roles, the user must have.
	 * @return boolean true, if user has role, fales else.
	 */
	function checkPermissionInGroup($groupId, $perm) {
		$perm = strtoupper($perm);
		$returns = false;
		if ($perm=="ANY") { 
			$returns = true;
		} else {
		  global $db;
		  // get all groupnames the user is in
		  $sql = "SELECT DISTINCT r.ROLE_NAME FROM roles r, user_permissions p WHERE p.ROLE_ID = r.ROLE_ID AND p.USER_ID = $this->userId AND p.GROUP_ID = $groupId";
		
	  	  // check for role
          $query = new query($db, $sql);
		  while ($query->getrow()) {
			  $usergroup=strtoupper($query->field("ROLE_NAME"));
			  if (stristr($perm, $usergroup)) $returns = true;
			  if ($usergroup == "ADMINISTRATOR") $returns = true;
		  }
		  $query->free();
	
          // check for function
          if ($returns == false) {
            $sql = "SELECT r.FUNCTION_ID FROM role_sys_functions r, user_permissions u WHERE u.USER_ID = ".$this->userId." AND u.GROUP_ID = $groupId AND u.ROLE_ID = r.ROLE_ID AND r.FUNCTION_ID = UPPER('$perm')";
            $query = new query($db, $sql);
            if ($query->getrow()) $returns = true;
            $query->free();
          }
          
          // check if admin....
          if ($returns == false) {
            $sql = "SELECT ROLE_ID FROM user_permissions WHERE (ROLE_ID=1 AND USER_ID=".$this->userId." AND GROUP_ID = $groupId) OR (USER_ID=".$this->userId." AND ROLE_ID=1 AND GROUP_ID=1)";
            $query = new query($db, $sql);
            if ($query->getrow()) $returns = true;
            $query->free();
          }

        }
		
		return $returns;
	}
	
	
	/**
	 * Creates a new Security-Id and Session for a user
	 * @param integer Object-Id of the user.
	 */
	function createSession($userId) {
		//create session
		mt_srand ((double)microtime()*1000000);
      $this->session = md5(uniqid(mt_rand()));

		// write to db
		global $db, $c;
		$sql = "UPDATE user_session SET SESSION_ID = '$this->session', LAST_LOGIN = ".$c["dbnow"].", REMOTE_ADDRESS = '$this->remote' WHERE USER_ID = $userId";
		
		$query = new query($db, $sql);
		$query->free();
		
		global $sid;
		$sid = $this->session;
	}

	/**
	 * Check the login data of a user and return true if data is correct or false if incorrect.
	 * @param string Username
	 * @param string User's pasword.
	 * @return boolean true if check was successful or false if not.
	 */
	function checkLogin($login, $passwd) {
		global $db, $c;		
		$sql = "SELECT USER_ID FROM users WHERE USER_NAME='$login' AND PASSWORD = '$passwd'";
		$query = new query($db, $sql);
		if ($query->count()==1) {
			// login successfull
			$query->getrow();
			$this->userId = $query->field("USER_ID");
			return true;
		} else {
			return false;
		}
		$query->free();
		
	}

}
