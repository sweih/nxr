<?php
	/**********************************************************************
	 *	N/X - Web Content Management System
	 *	Copyright 2003 Sven Weih, FZI Research Center for Information Technologies
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

require_once POT_INCLUDE_PATH . 'LoggingEngine/Plugin.php';

/**
* Stores additional information for statistics
*
* @author   Sven Weih <sven@nxsystems.org>
*/

class phpOpenTracker_LoggingEngine_Plugin_nxlog extends phpOpenTracker_LoggingEngine_Plugin {
  
  /**
  * @return array
  * @access public
  */
  function post() {
   if ($this->container['first_request']) {
     $this->db->query(
       sprintf(
         "INSERT INTO %s
         		(accesslog_id, weekday, hour, starttime, endtime, pi, duration)
         	VALUES  (%d, %d, %d, %d, %d, %d, %d)",
         	"pot_nxlog",
         	$this->container["accesslog_id"],
         	date("w"),
         	date("H"),
         	$this->container["timestamp"],
         	$this->container["timestamp"],
         	1,
         	0));
         		
    } else {
     $this->db->query(
       sprintf(
         "UPDATE %s SET endtime = %d, hour=%d, pi=pi+1, duration = endtime-starttime WHERE accesslog_id = %d",
         		
         	"pot_nxlog",
         	$this->container["timestamp"],
         	date("H
         	"),
         	$this->container["accesslog_id"]));   	
    }  	
    return array();
   }
   
   
  
  /**
   * @return boolean
   * @access public
   */
  function pre() {
    return true;
  }
}

?>