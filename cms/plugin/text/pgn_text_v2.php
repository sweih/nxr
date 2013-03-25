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
	 * Text PlugIn
	 * Version 2.0
	 * Extensions to Version 1.0:
	 *    - added Checkbox to specify if LineFeeds or CarriageReturns are 
	 *		 replaced with <br>'s (standard as it used to be in Version 1.0) or not.
	 *
	 * @package Plugins
	 */
	class pgnText extends Plugin {

		// Name of the Management's Table Primary Key
		var $pk_name = "FKID";

		// Name of the Plugin's Management Table. All tables should start with pgn_
		var $management_table = "pgn_text";

		/**
		  * Creates the input fields for editing text
		  * @param integer &$form link to the form the input-fields are to be created in 
		  */
		function edit(&$form) {
			global $lang, $sid, $c;

			// add button for external editor.
			$form->add(new Cell("clc1", "informationheader", 1, 50));
			$form->add(
				new ButtonInCell("launcher", "Launch External Editor (IE Only)", "informationheader", "BUTTON", "editor = window.open('" . $c["docroot"] . "plugin/text/rte/editor.php?oid=$this->fkid&sid=$sid', '','dialogWidth:640px;dialogHeight:480px;help:no;status:no;scroll:no;resizable:yes;');"));
			$condition = "FKID = $this->fkid";
			$form->add(new TextInput($lang->get("o_text"), "pgn_text", "CONTENT", $condition, "type:textarea,width:350, size:6", ""));
			$form->add(new CheckBoxInput($lang->get("o_text_nobreak", "Ignore New-Lines"), "pgn_text", "NOBREAK", $condition, "1", "0", "NUMBER"));
		}

		/** 
		  * Used, for painting a preview of the content in the cms. Note, that the data
		  * is to be drawn in a table cell. Therefore you may design any html output, but
		  * you must return it as return value!
		  */
		function preview() {
			$nobreak = getDBCell("pgn_text", "NOBREAK", "FKID = $this->fkid");

			$content = getDBCell("pgn_text", "CONTENT", "FKID = $this->fkid");
			$content = strip_tags($content);

			if ($nobreak == 0) {
				$content = nl2br($content);
			}

			if (strlen($content) > 150)
				$content = substr($content, 0, 150). " ...";

			if ($content == "")
				$content = "&lt;this item is empty&gt;";

			return $content;
		}

		/**
		   * This function is used for drawing the html-code out to the templates.
		   * It just returns the code
		   * @param 		string	Optional parameters for the draw-function. There are none supported.
		   * @return		string	HTML-CODE to be written into the template.
		   */
		function draw($param = "") {
			$nobreak = getDBCell("pgn_text", "NOBREAK", "FKID = $this->fkid");

			$content = getDBCell("pgn_text", "CONTENT", "FKID = $this->fkid");

			if ($nobreak == 0) {
				$content = nl2br($content);
			}

			return $content;
		}

		/**
		  * Create a new Record with the given $this->fkid in the database.
		  * Initialize with standard values!
		  */
		function createRecord() {
			$createHandler = new ActionHandler("CREATE");

			$createHandler->addDBAction("INSERT INTO $this->management_table ($this->pk_name, CONTENT, NOBREAK) VALUES ($this->fkid, '', 0)");
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
			$content = addslashes($query->field("CONTENT"));
			$nobreak = $query->field("NOBREAK");
			$query->free();

			$sql = "INSERT INTO $this->management_table ($this->pk_name, CONTENT, NOBREAK) VALUES ($newid, '$content', $nobreak)";
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
				$this->name = "Text";
				// A short description, what the Plugin is for.
				$this->description = "Version 2 of Text-Content with any length and format.";
				// Version of the plugin. Use integer numbers only. Is important for future releases.
				$this->version = 2;

				// Every module can have its own and individual META-Data in NX. The following Handler is
				// for creating a META-Data-Template and for assigning it to the Plugin.
				// IF you do not want to declare an individual META-Scheme, then set $mtid=0 and delete
				// everything between del1 and /del1!

				/**** do not change from this point ****/
				$mtid = nextGUID(); // getting next GUID.
				//del1
				$this->metaInstallHandler->addDBAction("INSERT INTO meta_templates (MT_ID, NAME, DESCRIPTION, INTERNAL) VALUES ($mtid, '$this->name PlugIn-Scheme', 'internally used for assigning $this->name plugin meta data', 1)");

				define("_TEXT", 1);
				define("_TEXTAREA", 2);
				define("_COLOR", 3);

				/**** add META-Data now ****/
				$guid = nextGUID();
				$this->metaInstallHandler->addDBAction("INSERT INTO meta_template_items (MTI_ID, MT_ID, NAME, POSITION, MTYPE_ID) VALUES($guid, $mtid, 'Text-Color', 1, " . _COLOR . ")");

				/**** end adding META-Data ****/
				// /del1

				// SQL for creating the tables in the database. Do not call, if you do not need any tables in the database 
				$this->installHandler->addDBAction("CREATE TABLE `pgn_text` (`FKID` bigint(20) NOT NULL default '0', `CONTENT` longtext, `NOBREAK` tinyint(4), PRIMARY KEY  (`FKID`),  UNIQUE KEY `FKID` (`FKID`),  FULLTEXT KEY `CONTENT` (`CONTENT`)) TYPE=MyISAM;");

				// SQL for deleting the tables from the database. 
				$this->uninstallHandler->addDBAction("DROP TABLE `pgn_text`");

				// SQL for upgrading from Version 1 to version 2.
				$this->upgradeHandler->addDBAction("ALTER TABLE `pgn_text` ADD `NOBREAK` tinyint(4) AFTER `CONTENT`");

				/**** change nothing beyond this point ****/
				global $source, $classname; // the source path has to be provided by the calling template
				$modId = nextGUID();
				$this->installHandler->addDBAction("INSERT INTO modules (MODULE_ID, MODULE_NAME, DESCRIPTION, VERSION, MT_ID, CLASS, SOURCE) VALUES ($modId, '$this->name', '$this->description', $this->version, $mtid, '$classname', '$source')");
			}
		}
	}
?>