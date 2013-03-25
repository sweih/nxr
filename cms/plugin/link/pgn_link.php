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
	 * Link PlugIn
	 * Version 1.0
	 *
	 * @package Plugins
	 */
	class pgnLink extends Plugin {

		// Name of the Management's Table Primary Key
		var $pk_name = "FKID";

		// Name of the Plugin's Management Table. All tables should start with pgn_
		var $management_table = "pgn_link";

		/**
		  * Creates the input fields for editing text
		  * @param integer &$form link to the form the input-fields are to be created in 
		  */
		function edit(&$form) {
			global $lang, $sid, $c;
			
			$condition = "FKID = $this->fkid";
			$form->add(new TextInput("Label", "pgn_link", "LABEL", $condition, "type:text,width:350,size:128"));
			$form->add(new SitepageSelector("Internal Address", "pgn_link", "SPID", $condition));
			$form->add(new TextInput("External Address (overrides internal)", "pgn_link", "HREF", $condition, "type:text,width:200,size:64", ""));
			$form->add(new TextInput("Target", "pgn_link", "TARGET", $condition, "type:text,width:200,size:32", ""));
			$form->add(new NonDisplayedValueOnInsert("pgn_link", "FKID", $condition, $this->fkid, "NUMBER"));
		}

		/** 
		  * Used, for painting a preview of the content in the cms. Note, that the data
		  * is to be drawn in a table cell. Therefore you may design any html output, but
		  * you must return it as return value!
		  */
		function preview() {
			global $variation, $c;

			$link_text = getDBCell("pgn_link", "LABEL", "FKID = $this->fkid");

			if ($link_text == "") {
				$content = "&lt;this item is empty&gt;";
			} else {
				$href = getDBCell("pgn_link", "HREF", "FKID = $this->fkid");

				$spid = getDBCell("pgn_link", "SPID", "FKID = $this->fkid");

				if ($href == "") {
					$spm = getDBCell("sitepage", "SPM_ID", "SPID = " . $spid);

					$path = getDBCell("sitepage_master", "TEMPLATE_PATH", "SPM_ID = $spm");
					$href = $c["devdocroot"] . $path . "?page=$spid&amp;v=$variation";
				}

				$content = "<a href=\"$href\" target=\"_blank\">$link_text</a>";
			}

			return $content;
		}

		/**
		   * This function is used for drawing the html-code out to the templates.
		   * It just returns the code
		   * @param 		string	Optional parameters for the draw-function. There are none supported.
		   * @return		string	HTML-CODE to be written into the template.
		   */
		function draw($param = "") {
			global $db, $cds;
			$v = value("v", "NUMERIC");
			
			$content = "";
			$querySQL = "SELECT * FROM $this->management_table WHERE $this->pk_name = $this->fkid";
			$query = new query($db, $querySQL);
			$query->getrow();
			$label = addslashes($query->field("LABEL"));
			$href = $query->field("HREF");
			$spid = $query->field("SPID");
			$target = $query->field("TARGET");
			$query->free();

			//if (is_object($cds)) {
			//	if ($cds->level == _LIVE) {
			//		$tmp = getDBCell("state_translation", "OUT_ID", "IN_ID = ".$spid." AND LEVEL = 10");	
			//		if ($tmp != "") $spid = $tmp;
			//	}	
			//}
			
			if ($href == "") {
				if (!isSPExpired($spid, $v) || $cds->is_development) {					
					if (function_exists("getMenuLink")) {
						$href = getMenuLink($spid, $v);
					} else if (is_object($cds)) {
						$obj = $cds->menu->getMenuById($spid);
						if (is_object($obj))
						  $href = $obj->getLink();
						unset($obj);	
					}
				}
			}

			if ($href != "") {
				if (strtoupper($param) == "ALL") {
					$cn["HREF"] = $href;

					$cn["LABEL"] = $label;
					$cn["TARGET"] = $target;
					$cn["SPID"] = $spid;
					return $cn;
				} else {
					$tg = "";

					if ($target != "")
						$tg = " target=\"" . $target . "\" ";

					$content = "<a href=\"$href\" $param $tg>$label</a>";
				}
			}

			return $content;
		}

		/**
		  * Create a new Record with the given $this->fkid in the database.
		  * Initialize with standard values!
		  */
		function createRecord() {
			$createHandler = new ActionHandler("CREATE");

			$createHandler->addDBAction("INSERT INTO $this->management_table ($this->pk_name, LABEL) VALUES ($this->fkid, '')");
			$createHandler->process("CREATE");
		}

		/**
		 * This Function provides all actions for deleting a complete recordset
		 * of a plugin. It shoul use the $this->fkid for identifying the record.
		 */
		function deleteRecord() { Plugin::deleteRecord();
			// does not need to be canged as long working on one table only!
			}

		/**
		   * Create the sql-code for a version of the selected object
		   * @param integer ID of new Version.
		   * @returns string SQL Code for new Version.
		   */
		function createVersion($newid) {
			// query for content
			global $db;

			$querySQL = "SELECT * FROM $this->management_table WHERE $this->pk_name = $this->fkid";
			$query = new query($db, $querySQL);
			$query->getrow();
			$label = addslashes($query->field("LABEL"));
			$href = $query->field("HREF");
			$spid = $query->field("SPID");
			$spidTrans = translateState($spid, 10, false);
			$target = $query->field("TARGET");
			$query->free();
			$sql = "INSERT INTO $this->management_table ($this->pk_name, LABEL, HREF, SPID, TARGET) VALUES ($newid, '$label', '$href', $spidTrans,'$target')";
			return $sql;
		}

		function copyRecord($newId) {
			global $db;

			$querySQL = "SELECT * FROM $this->management_table WHERE $this->pk_name = $this->fkid";
			$query = new query($db, $querySQL);
			$query->getrow();
			$label = addslashes($query->field("LABEL"));
			$href = $query->field("HREF");
			$spid = $query->field("SPID");
			$target = $query->field("TARGET");
			$query->free();
			$sql = "INSERT INTO $this->management_table ($this->pk_name, LABEL, HREF, SPID, TARGET) VALUES ($newid, '$label', '$href', $spid,'$target')";
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
				$this->name = "Link";
				// A short description, what the Plugin is for.
				$this->description = "Link to pages";
				// Version of the plugin. Use integer numbers only. Is important for future releases.
				$this->version = 1;

				// Every module can have its own and individual META-Data in NX. The following Handler is
				// for creating a META-Data-Template and for assigning it to the Plugin.
				// IF you do not want to declare an individual META-Scheme, then set $mtid=0 and delete
				// everything between del1 and /del1!

				/**** do not change from this point ****/
				$mtid = nextGUID(); // getting next GUID.

				// SQL for creating the tables in the database. Do not call, if you do not need any tables in the database 
				$this->installHandler->addDBAction("CREATE TABLE `pgn_link` ( `FKID` BIGINT NOT NULL, `LABEL` VARCHAR(32), `EXTERNAL` TINYINT DEFAULT '0' NOT NULL, `HREF` VARCHAR(128), `SPID` BIGINT, `TARGET` VARCHAR(32), PRIMARY KEY (`FKID`) )");

				// SQL for deleting the tables from the database. 
				$this->uninstallHandler->addDBAction("DROP TABLE `pgn_link`");

				/**** change nothing beyond this point ****/
				global $source, $classname; // the source path has to be provided by the calling template
				$modId = nextGUID();
				$this->installHandler->addDBAction("INSERT INTO modules (MODULE_ID, MODULE_NAME, DESCRIPTION, VERSION, MT_ID, CLASS, SOURCE) VALUES ($modId, '$this->name', '$this->description', $this->version, $mtid, '$classname', '$source')");
			}
		}
	}
?>