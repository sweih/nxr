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
	class pgnTeaser extends Plugin {

		// Name of the Management's Table Primary Key
		var $pk_name = "FKID";

		// Name of the Plugin's Management Table. All tables should start with pgn_
		var $management_table = "pgn_teaser";

		/**
		  * Creates the input fields for editing text
		  * @param integer &$form link to the form the input-fields are to be created in 
		  */
		function edit(&$form) {
			global $lang, $sid, $c;
			
			$cond = "FKID = $this->fkid";
			$form->add(new TextInput($lang->get("title", "Title"), "pgn_teaser", "HEADLINE", $cond, "type:text,width:320,size:256"));
			$form->add(new RichEditInput($lang->get("body", "Body"), "pgn_teaser", "BODY", $cond, "type:rich,width:320,size:6", ""));               
			$form->add(new LibrarySelect("pgn_teaser", "IMAGEID", $cond, "IMAGE"));
			$form->add(new CheckboxTxtInput($lang->get("imageteaser", "Only display Image"), "pgn_teaser", "ISIMAGETEASER", $cond, "1", "0"));
			$form->add(new SitepageSelector($lang->get("intpage", "Internal Page"), "pgn_teaser", "SPID", $cond));
			$form->add(new CheckboxTxtInput($lang->get("resolvecp", "Display all childs"), "pgn_teaser", "RESOLVECHILDS", $cond, "1", "0"));
			$form->add(new TextInput($lang->get("hreftoor", "WWW (overrides internal)"), "pgn_teaser", "HREF", $cond, "type:text,width:320,size:256"));
			$form->add(new TextInput($lang->get("linktext", "Link Text (optional)"), "pgn_teaser", "LINKTEXT", $cond, "type:text,width:320,size:256"));
			$form->add(new TextInput($lang->get("configstring", "Configuration String (optional)"), "pgn_teaser", "CONFIG", $cond, "type:text,width:320,size:256"));
			$form->add(new NonDisplayedValueOnInsert("pgn_teaser", "FKID", $cond, $this->fkid, "NUMBER"));
		}

		/** 
		  * Used, for painting a preview of the content in the cms. Note, that the data
		  * is to be drawn in a table cell. Therefore you may design any html output, but
		  * you must return it as return value!
		  */
		function preview() {
			global $variation, $c;
			$content = getDBCell("pgn_teaser", "HEADLINE", "FKID=".$this->fkid);
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
      require_once $cds->path."sys/draw_teaser.php";      
      return drawPGNTeaser($this->fkid);
		}

		/**
		  * Create a new Record with the given $this->fkid in the database.
		  * Initialize with standard values!
		  */
		function createRecord() {
			$createHandler = new ActionHandler("CREATE");
			$createHandler->addDBAction("INSERT INTO $this->management_table ($this->pk_name) VALUES ($this->fkid)");
			$createHandler->process("CREATE");
		}
		
		function getInstallationFiles() {
			return array("draw_teaser.php");
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
			$change['FKID'] = $newid;
			$change['SPID'] = 'translate';
			$change['IMAGEID'] = 'translate';
			copyRow($this->management_table, "$this->pk_name = $this->fkid", $change);			
			return '';
		}

		function copyRecord($newId) {
			global $db;
			$change['FKID'] = $newid;
			copyRow($this->management_table, "$this->pk_name = $this->fkid", $change);								
			return '';
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
				$this->name = "Teaser";
				// A short description, what the Plugin is for.
				$this->description = "Build teaser boxes";
				// Version of the plugin. Use integer numbers only. Is important for future releases.
				$this->version = 1;

				// Every module can have its own and individual META-Data in NX. The following Handler is
				// for creating a META-Data-Template and for assigning it to the Plugin.
				// IF you do not want to declare an individual META-Scheme, then set $mtid=0 and delete
				// everything between del1 and /del1!

				/**** do not change from this point ****/
				$mtid = nextGUID(); // getting next GUID.

				// SQL for creating the tables in the database. Do not call, if you do not need any tables in the database 
				$this->installHandler->addDBAction("CREATE TABLE `pgn_teaser` (`FKID` BIGINT NOT NULL ,`HEADLINE` VARCHAR( 256 ) NULL ,`BODY` TEXT NULL ,`IMAGEID` BIGINT NULL ,`HREF` VARCHAR( 256 ) NULL ,`SPID` BIGINT NULL ,`LINKTEXT` VARCHAR( 256 ) NULL ,`ISIMAGETEASER` TINYINT( 1 ) NOT NULL DEFAULT '0',`RESOLVECHILDS` TINYINT( 1 ) NOT NULL DEFAULT '0',`CONFIG` VARCHAR( 256 ) NULL ,PRIMARY KEY ( `FKID` )) ENGINE = MYISAM ");

				// SQL for deleting the tables from the database. 
				$this->uninstallHandler->addDBAction("DROP TABLE `pgn_teaser`");

				/**** change nothing beyond this point ****/
				global $source, $classname; // the source path has to be provided by the calling template
				$modId = nextGUID();
				$this->installHandler->addDBAction("INSERT INTO modules (MODULE_ID, MODULE_NAME, DESCRIPTION, VERSION, MT_ID, CLASS, SOURCE) VALUES ($modId, '$this->name', '$this->description', $this->version, $mtid, '$classname', '$source')");
			}
		}
	}
?>