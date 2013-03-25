<?php
/**********************************************************************
 *	N/X - Web Content Management System
 *	Copyright 2003 Sven Weih
 *
 *	This file is part of N/X.
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
  * API interface for accessing Type-3 Plugins.
  */
  class CDSPlugins {
  	
    /**
     * Standard constructor
     */
    function CDSPlugins() {}
  
    /**
     * Get the API-Object of a type 3 Plugin
     * @param string name of the Type-3 Plugin
     * @param mixed parameters as assosiative array ("id" => 12);
     */
    function getAPI($name, $parameters=null) {
       $pluginId = getDBCell("modules", "MODULE_ID", "UPPER(MODULE_NAME) = '".strtoupper($name)."' AND MODULE_TYPE_ID = 3");      						
       includePGNSource($pluginId);
       $ref = createPGNRef($pluginId, 0);
       if (is_object($ref)) {
		  return $ref->draw($parameters);
	   } else {
	     return null;
	   }
    }
    
  }
 
 
?>