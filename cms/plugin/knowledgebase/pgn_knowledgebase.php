<?
	/**********************************************************************
	 *	N/X - Web Content Management System
	 *	Copyright 2007 Sven Weih, FZI Research Center for Information Technologies
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
	 * Knowledgebase of the PlugIn
	 * Version 1.0
	 * by Sven Weih
	 *
	 * @package Plugins
	 */
	class pgnKnowledgeBase extends Plugin {

		// Name of the Management's Table Primary Key
		var $pk_name = "ID";

		// Name of the Plugin's Management Table. All tables should start with pgn_
		var $management_table = "pgn_knowledgebase";
		// configuration Variables
		var $isSingleConfig = false;
		var $pluginType = 2; // 1= Content Plugin, 2= System Extension

		var $globalConfigPage = "plugin/knowledgebase/overview.php";
		var $globalConfigRoles = "ADMINISTRATOR";
		var $name = "Knowledgebase";


		/**
		   * This function is used for drawing the html-code out to the templates.
		   * It just returns the code
		   * @param 		string	Optional parameters for the draw-function. There are none supported.
		   * @return		string	HTML-CODE to be written into the template.
		   */
		function draw($param = "") { }

		/**
		  * Create a new Record with the given $this->fkid in the database.
		  * Initialize with standard values!
		  */
		function createRecord() { }

		/**
		 * This Function provides all actions for deleting a complete recordset
		 * of a plugin. It shoul use the $this->fkid for identifying the record.
		 */
		function deleteRecord() { }

		/**
		   * Create the sql-code for a version of the selected object
		   * @param integer ID of new Version.
		   * @returns string SQL Code for new Version.
		   */
		function createVersion($newid) { }

		/**
		   * Specifies information for installation and deinstallation of the plugin.
		   */
		function registration() {
			global $auth;

			// Authentification is require_onced for changing system configuration. Do not change.
			if ($auth->checkPermission("ADMINISTRATOR")) {

				// parent registration function for initializing. Do not change.
				Plugin::registration();

				// Name of the Plugin. The name will be displayed in the WCMS for selection
				$this->name = "Knowledgebase";
				// A short description, what the Plugin is for.
				$this->description = "Knowledgebase for your website";
				// Version of the plugin. Use integer numbers only. Is important for future releases.
				$this->version = 1;

				// Every module can have its own and individual META-Data in NX. The following Handler is
				// for creating a META-Data-Template and for assigning it to the Plugin.
				// IF you do not want to declare an individual META-Scheme, then set $mtid=0 and delete
				// everything between del1 and /del1!

				/**** do not change from this point ****/
				$mtid = nextGUID(); // getting next GUID.

				// SQL for creating the tables in the database. Do not call, if you do not need any tables in the database 
				$sql1 = "CREATE TABLE `pgn_knowledgebase` (`ID` BIGINT NOT NULL ,`TITLE` VARCHAR( 255 ) NOT NULL ,`DESCRIPTION` TEXT NULL ,`UPDATEUSER` VARCHAR( 64 ) NULL ,`UPDATETIMESTAMP` DATETIME NULL ,`ENABLED` TINYINT( 1 ) NULL DEFAULT '0',PRIMARY KEY ( `ID` ))";							
				$sql2 = "CREATE TABLE `pgn_knowledgebase_tags` (`TAG_ID` BIGINT NOT NULL ,`TAG` CHAR( 32 ) NOT NULL ,`TAG_CAT` INT( 3 ) NOT NULL ,PRIMARY KEY ( `TAG_ID` ) ,INDEX ( `TAG` ) ) TYPE = MYISAM ;";
				$sql3 = "CREATE TABLE `pgn_knowledgebase_tag_relation` (`TAG_ID` BIGINT NOT NULL ,`FK_ID` BIGINT NOT NULL ,`POSITION` INT( 3 ) NULL ,INDEX ( `TAG_ID` , `FK_ID` ) ) TYPE = MYISAM ;";

				$this->installHandler->addDBAction($sql1);
				$this->installHandler->addDBAction($sql2);
				$this->installHandler->addDBAction($sql3);

				// SQL for deleting the tables from the database. 
				$this->uninstallHandler->addDBAction("DROP TABLE `pgn_knowledgebase`");
				$this->uninstallHandler->addDBAction("DROP TABLE `pgn_knowledgebase_tags`");
				$this->uninstallHandler->addDBAction("DROP TABLE `pgn_knowledgebase_tag_relation`");

				/**** change nothing beyond this point ****/
				global $source, $classname; // the source path has to be provided by the calling template
				$modId = nextGUID();
				$this->installHandler->addDBAction("INSERT INTO modules (MODULE_ID, MODULE_NAME, DESCRIPTION, VERSION, MT_ID, CLASS, SOURCE, MODULE_TYPE_ID) VALUES ($modId, '$this->name', '$this->description', $this->version, $mtid, '$classname', '$source', $this->pluginType)");
			}
		}
	}
?>