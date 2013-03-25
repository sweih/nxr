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
	 * Auth PlugIn
	 * Version 1.0
	 *
	 * @package AUTH
	 */
	class pgnAuth extends Plugin {

		// Name of the Management's Table Primary Key
		var $pk_name = "FKID";

		// Name of the Plugin's Management Table. All tables should start with pgn_
		var $management_table = "pgn_auth";

		/**
		  * Creates the input fields for editing text
		  * @param integer &$form link to the form the input-fields are to be created in 
		  */
		function edit(&$form) {
			global $lang;
			$groups = createNameValueArray("groups", "GROUP_NAME", "GROUP_ID", "1 ORDER BY GROUP_NAME ASC");
			$groups[0][0] = $lang->get("all");
			$groups[0][1] = "0";
			$condition = "FKID = $this->fkid";
			$form->add(new SelectOneInputFixed($lang->get("auth_group", "Authentification"), "pgn_auth", "GROUP_ID", $groups, $condition, "type:dropdown,width:200", ""));
		}

		/** 
		  * Used, for painting a preview of the content in the cms. Note, that the data
		  * is to be drawn in a table cell. Therefore you may design any html output, but
		  * you must return it as return value!
		  */
		function preview() {
			global $lang;
			$groupName = getDBCell("groups", "GROUP_NAME", "GROUP_ID=".getDBCell("pgn_auth", "GROUP_ID", "FKID=".$this->fkid));
			if ($groupName == "") $groupName = $lang->get("all");
			$result = "<b>".$lang->get("auth_group").":</b>&nbsp;".$groupName;
			return $result;
		}

		/**
		   * This function is used for drawing the html-code out to the templates.
		   * It just returns the code
		   * @param 		string	Optional parameters for the draw-function. There are none supported.
		   * @return		string	HTML-CODE to be written into the template.
		   */
		function draw($param = "") {
			$groupId = getDBCell("pgn_auth", "GROUP_ID", "FKID=".$this->fkid);
			$result = new authCommunity($groupId);
			if (value("logout") != "0") {
				$result->logout();
				$result = new authCommunity($groupId);				
			}
			return $result;
		}

		
		/**
		  * Create a new Record with the given $this->fkid in the database.
		  * Initialize with standard values!
		  */
		function createRecord() {
			$createHandler = new ActionHandler("CREATE");
			$createHandler->addDBAction("INSERT INTO $this->management_table ($this->pk_name, GROUP_ID) VALUES ($this->fkid, 0)");
			$createHandler->process("CREATE");
		}


		/**
		   * Create the sql-code for a version of the selected object
		   * @param integer ID of new Version.
		   * @returns string SQL Code for new Version.
		   */
		function createVersion($newid) {
			// query for content
			global $db;

			$querySQL = "SELECT GROUP_ID FROM $this->management_table WHERE $this->pk_name = $this->fkid";
			$query = new query($db, $querySQL);
			$query->getrow();
			$content = $query->field("GROUP_ID");
			$query->free();

			$sql = "INSERT INTO $this->management_table ($this->pk_name, GROUP_ID) VALUES ($newid, $content)";
			return $sql;
		}

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
				$this->name = "Auth";
				// A short description, what the Plugin is for.
				$this->description = "Community Authentification";
				// Version of the plugin. Use integer numbers only. Is important for future releases.
				$this->version = 1;

				// Every module can have its own and individual META-Data in NX. The following Handler is
				// for creating a META-Data-Template and for assigning it to the Plugin.
				// IF you do not want to declare an individual META-Scheme, then set $mtid=0 and delete
				// everything between del1 and /del1!

				/**** do not change from this point ****/
				$mtid = 1; // getting next GUID.
				
				// SQL for creating the tables in the database. Do not call, if you do not need any tables in the database 
				$this->installHandler->addDBAction("CREATE TABLE `pgn_auth` (`FKID` BIGINT NOT NULL ,`GROUP_ID` BIGINT NOT NULL ,PRIMARY KEY ( `FKID` ));");

				// SQL for deleting the tables from the database. 
				$this->uninstallHandler->addDBAction("DROP TABLE `pgn_auth`");

				/**** change nothing beyond this point ****/
				global $source, $classname; // the source path has to be provided by the calling template
				$modId = nextGUID();
				$this->installHandler->addDBAction("INSERT INTO modules (MODULE_ID, MODULE_NAME, DESCRIPTION, VERSION, MT_ID, CLASS, SOURCE) VALUES ($modId, '$this->name', '$this->description', $this->version, $mtid, '$classname', '$source')");
			}
		}
	}
?>