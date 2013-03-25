<?php

/**********************************************************************
 *	N/X - Web Content Management System
 *	Copyright 2004 Sven Weih
 *
 *      $Id: plugin.php,v 1.8 2004/09/21 10:45:35 sven_weih Exp $ *
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

  require_once($c["path"].'api/parser/objectparser.php');
  
  /**
   * Apply the filter-plugins on a given input
   * @param string Text to filter
   */
   function applyFilterPlugins($text) {
   	 $objectParser = new ObjectParser();
   	 $text = $objectParser->parse($text);
   	 $plugins = createDBCArray("modules", "MODULE_ID", "MODULE_TYPE_ID=4");   
   	 for ($i=0; $i<count($plugins); $i++) {
   	   includePGNSources();
   	   $plugin = createPGNRef($plugins[$i], 0);   	   
   	   $text = $plugin->parseText($text);
   	 }
   	 return $text;
   }
 
 	/**
	* Launch a plugin.
	* @param integer FKID of the plugin to launch.
	* @param integer Module-ID of the plugin.
	* @param integer ID of the level to launch to
	* @param integer ID of the Cluster-Content-Item
	* @returns integer Translated ID after launch.
	*/
	function launchPlugin($in, $plugin, $level, $clti=0) {
		global $db;

		$out = translateState($in, $level);
		
		// reference the Plugin.
		$sql = "SELECT CLASS FROM modules WHERE MODULE_ID = $plugin";
		$query = new query($db, $sql);
		$query->getrow();
		$classname = $query->field("CLASS");

		$ref = new $classname($in, $clti);
		$delSQL = "DELETE FROM $ref->management_table WHERE $ref->pk_name = $out";
		$query = new query($db, $delSQL);
		// *old* note: when versioning is being implemented, createVersion must be called with parameter $applyProperties=false if the original version level is higher than 0 !!!
		// *old* this is to make sure automatic modifications are only applied when first increasing the version level of a content.
		// note: the new concept is to apply automatic modifications immediately when uploading new plugin data while the original uploaded data will remain as an unchanged version.
		// this way the automatic changes can be updated if changed later on by issuing a corresponding command on the singleConfig page
		$sql = $ref->createVersion($out);
		$query = new query($db, $sql);
		$query->free();
		unset ($ref);
		return $out;
	}
	
    /**
     * Returns the ID of a Plugin
     * @param string name of the plugin
     */
    function getPluginId($name) {
    	return getDBCell("modules", "MODULE_ID", "UPPER(MODULE_NAME) = '".strtoupper($name)."'");
    }
    
    /**
     * delete the content of a plugin.
     * @param integer id of the record to delete
     * @param integer id of the plugin to delete.
     */
    function deletePlugin($fkid, $module) {
        $ref = createPGNRef($module, $fkid);

        if ($ref != null)
            $ref->deleteRecord();
    }
	
?>