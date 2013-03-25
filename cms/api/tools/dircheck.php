<?php
/**********************************************************************
 *	N/X - Web Content Management System
 *	Copyright 2004 Sven Weih
 *
 *      $Id: dircheck.php,v 1.1 2004/10/13 15:00:05 sven_weih Exp $ *
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
   * Class checks directories for
   *  - existance
   *  - correct rights
   */
   class DirCheck {
   	
   	var $path = "";
   	var $permissions = "777";
   	
   	
   	/**
   	 * Standard constructor
   	 * @param string path-variable to check
   	 * @param string permissiions to check, e.g. 777
   	 */
   	function DirCheck($path, $permissions="777") {
   	  $this->path = $path;   	  
   	  $this->permissions = $permissions;
   	  $this->execute();
   	}
   	
   	/**
   	 * perform the check
   	 */
   	function execute() {
   		echo "<br/>";
   		if (! file_exists($this->path)) {
  			echo '<font color="red">Directory <b>'.$this->path.'</b> does not exist!</font><br/>';
   		} else {
   			echo '<font color="green">Directory <b>'.$this->path.'</b> exists. </font>';
   			if (! stristr($_ENV["OS"], "Win")) {
   			  clearstatcache();
   			  $info = stat($this->path);
   			  $permissions = $info[3];
   			  $permission= "100666";
   			  $user = $info[6];
   			  $permission = substr($permission,3,3);
   			  echo '<br/>';
   			  if ($permission != $this->permissions) {
   			  	echo '<font color="red">';
   			  } else {
   			  	echo '<font color="green">';
   			  }
   			  echo "Protection Mode: ".$permission." (Recommended: $this->permissions) ";
   			  echo " - UserId: ".$user;
   			  echo "</font>";   			  
   			}
   			echo "<br/>";
   		}
   		echo '<br/>';
   	}
   }
?>
