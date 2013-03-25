<?php

/**********************************************************************
 *	N/X - Web Content Management System
 *	Copyright 2003 Sven Weih
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
  * with optional parameter group, that is neccessary to access the page. For an example
  * take just any page in the modules section.
  * @package ContentDeliverySystem
  */

class authCommunity extends authSMA {
	
    	/**
	 * standard constructor
	 * @param string Group, a user must have to access the page. Note that Administrators
	 * can access just any page and that a page wirh no permissions set may be accessed
	 * by every user of the CMS. 
	 */
	function authCommunity($group="") {
		global $cds, $sma;
			if ($group != 0 || $group == "ANY" || $group==null || strlen($_COOKIE[$this->myCookie]) > 0) {
				$forceLogin = ($group != 0);
				authSMA::authSMA("COMMUNITY_LOGIN", false, $forceLogin);	
				if ($forceLogin && !$this->isInGroup2($group) && $group != "ANY" && ! $group==0) {
					header ("Location: ".$this->forwardToOnAuthFail());
					exit;	
				}
			} 											
	}
	
	/**
	 * Returns the URL where the system shall forward to when auth fails.
	 */
	function forwardToOnAuthFail() {
		global $cds, $db;
		$loginpage = $cds->getPageByPath("/login");
		$result =  $loginpage->menu->getLink();
		$db->close();
		
		$forwardto = "forwardto=".urlencode($_SERVER["REQUEST_URI"]);
		if (stristr($_SERVER["QUERY_STRING"], "forwardto=")) {
			$forwardto = substr($_SERVER["QUERY_STRING"], strpos($_SERVER["QUERY_STRING"], "forwardto="));
		}
		return $result."&".$forwardto;			
	}
	
	/**
	 * Checks, whether a user is in a group or not.
	 * @param string ID of the group.
	 * @returns boolean true|false
	 */
	function isInGroup2($groupId) {
	 	if (countRows("user_permissions", "GROUP_ID", "GROUP_ID = $groupId AND USER_ID = ".$this->userId)>0) {
	  	  return true;
	  	} else {
	  	  return false;	
	  	}
	  
	}
}

?>