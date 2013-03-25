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
	 * Bulk Image Plugin
	 * Version 1.0
	 *
	 * @package Plugins
	 */
	class pgnBulkImage extends Plugin {

		// Name of the Management's Table Primary Key
		
       	var $globalConfigPage = "plugin/bulkimage/import.php";
		// Name of the Plugin's Management Table. All tables should start with pgn_
		var $management_table = "pgn_cal_appointment";
        var $pluginType = 2; //CDS-API-Extension
        var $globalConfigRoles = "BULKIMAGE";

        /**
         * Define function tree that will be created...
         */
        function getSystemFunctions() {
           return array("PLUGINS_M" => array(array("BULKIMAGE", "Bulk Image", "Bulkimage Plugin")));
        }
                
           /**
		   * Specifies information for installation and deinstallation of the plugin.
		   */
		function registration() {
			global $auth;
            		$this->name = "BulkImage";
            		$this->description = "System-Extensions for importing images in archives.";
            		$this->version = 1;
			if ($auth->checkPermission("ADMINISTRATOR")) {
				Plugin::registration();
				global $source, $classname; // the source path has to be provided by the calling template
				$modId = nextGUID();
				$this->installHandler->addDBAction("INSERT INTO modules (MODULE_ID, MODULE_NAME, DESCRIPTION, VERSION, MT_ID, CLASS, SOURCE, MODULE_TYPE_ID) VALUES ($modId, '$this->name', '$this->description', $this->version, 0, '$classname', '$source', 3);");
			}
		}
	}	
?>