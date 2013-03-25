<?
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

	/**
	 * File PlugIn
	 * Version 1.0
	 *
	 * @package Plugins
	 */
	class pgnFile extends Plugin {

		// Name of the Management's Table Primary Key
		var $pk_name = "FKID";

		// Name of the Plugin's Management Table. All tables should start with pgn_
		var $management_table = "pgn_file";

		/**
		  * Creates the input fields for editing images
		  * @param integer &$form link to the form the input-fields are to be created in 
		  */
		function edit(&$form) {
			global $lang, $c;

			require_once $c["path"] . "plugin/file/fileupload.inc.php";
			$condition = "FKID = $this->fkid";
			$form->add(new TextInput($lang->get("NAME"), "pgn_file", "NAME", $condition, "type:text,width:300,size:64", "MANDATORY"));
			$form->add(new TextInput($lang->get("DESC", "Description"), "pgn_file", "DESCRIPTION", $condition, "type:textarea,width:300,size:3", ""));
			$form->add(new TextInput($lang->get("LOCATION", "Location, if external"), "pgn_file", "LOCATION", $condition, "type:text,width:300,size:64", ""));

			$form->add(new FileUpload($lang->get("choosefile"), $this->fkid));
			$form->add(new Label("lbl", $lang->get("CLEAR_MEDIA", "Remove file from database"), "standard"));
			$form->add(new Checkbox("REMOVE", "1", "standard"));
		}

		/** 
		  * Used, for painting a preview of the content in the cms. Note, that the data
		  * is to be drawn in a table cell. Therefore you may design any html output, but
		  * you must return it as return value!
		  */
		function preview() {
			global $c, $lang;
	
	  	$filename = getDBCell("pgn_file", "FILENAME", "FKID = $this->fkid");
			$copyright = getDBCell("pgn_file", "COPYRIGHT", "FKID = $this->fkid");
			$name	  = getDBCell("pgn_file", "NAME", "FKID=$this->fkid");
      if ($filename=="") {
        return "<div align=\"center\">".$lang->get('nofile', 'No file uploaded yet.')."</div>";
      } else {			
			  return "<div align=\"center\"><a href=\"".$c['devfilesdocroot'].$filename."\" target=\"blank\">".$name."</a></div>";	
      }
			return $output;
		}

	

		/**
		   * This function is used for drawing the html-code out to the templates.
		   * @param 		string  Parameters of Image. Allowed are ALL and TAG.
								   If ALL is set, an array of following form is returned: 
								   array["path"], array["width"], array["height"], array["alt"], array["copyright"] 
		   * @returns		string	HTML-CODE to be written into the template.
		   */
		function draw($param = "") {
			global $c, $db, $splevel, $cds;

			$sql = "SELECT * FROM $this->management_table WHERE $this->pk_name = $this->fkid";
			$query = new query($db, $sql);
			$query->getrow();
			$filename = $query->field("FILENAME");
			
			$im["name"] = $query->field("NAME");
			$im["filename"] = $filename;
			$im["description"] = $query->fields("DESCRIPTION");
			$im["location"] = $query->fields("LOCATION");
			
			if (is_object($cds)) {
			  $splevel = $cds->level;
			}
			
			if ($splevel == 10) {
				$im["path"] = $c["livefilesdocroot"] . $filename; // splevel=10 is for live-site, 0 for development.
			}

			if ($splevel == 0) {
				$im["path"] = $c["devfilesdocroot"] . $filename;
			}
		
			$query->free();
			return $im;
		}

		/**
		  * Create a new Record with the given $this->fkid in the database.
		  * Initialize with standard values!
		  */
		function createRecord() {
			$createHandler = new ActionHandler("CREATE");
			$createHandler->addDBAction("INSERT INTO $this->management_table ($this->pk_name, FILENAME) VALUES ($this->fkid, '')");
			$createHandler->process("CREATE");
		}

		/**
		 * This Function provides all actions for deleting a complete recordset
		 * of a plugin. It shoul use the $this->fkid for identifying the record.
		 */
		function deleteRecord() { Plugin::deleteRecord();
			// does not need to be changed as long working on one table only!
			}


		/**
		   * Create the sql-code for a version of the selected object
		   * @param integer ID of new Version.
		   * @returns string SQL Code for new Version.
		   */
		function createVersion($newid, $copy = false) {
			// query for content
			global $db, $c;

			$destinationPath = $c["livefilespath"];

			if ($copy)
				$destinationPath = $c["devfilespath"];

			$querySQL = "SELECT * FROM $this->management_table WHERE $this->pk_name = $this->fkid";
			$query = new query($db, $querySQL);
			$query->getrow();
			      
			$filename = $query->field("FILENAME");
			$description = addslashes($query->field("DESCRIPTION"));
			$filetype = $query->field("FILETYPE");
			$location = $query->field("LOCATION");
			$name     = $query->field("NAME");
			
			$query->free();
			// copy image to new version
			$fileparts = explode(".", $filename);
			$suffix = strtolower($fileparts[(count($fileparts) - 1)]);
			$newfile = $newid . "." . $suffix;

			if (!$copy) {
				nxDelete ($destinationPath , $newfile);
			}
			
			if ($suffix != "") {				
				nxCopy($c["devfilespath"] . $filename, $destinationPath , $newfile);
			}
		
			$sql = "INSERT INTO $this->management_table ($this->pk_name, NAME,FILENAME, FILETYPE, LOCATION, DESCRIPTION) VALUES ($newid, '$name', '$newfile', '$filetype', '$location', '$description')";
			return $sql;
		}

		function copyRecord($newid) { return $this->createVersion($newid, true); }

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
				$this->name = "File";
				// A short description, what the Plugin is for.
				$this->description = "File Uploads. Accepts all type of files.";
				// Version of the plugin. Use integer numbers only. Is important for future releases.
				$this->version = 1;

				// Every module can have its own and individual META-Data in NX. The following Handler is
				// for creating a META-Data-Template and for assigning it to the Plugin.
				// IF you do not want to declare an individual META-Scheme, then set $mtid=0 and delete
				// everything between del1 and /del1!
				$mtid = nextGUID();

				// SQL for creating the tables in the database. Do not call, if you do not need any tables in the database 
				$this->installHandler->addDBAction("CREATE TABLE `pgn_file` (`FKID` BIGINT NOT NULL ,`NAME` VARCHAR( 64 ) NOT NULL ,`DESCRIPTION` VARCHAR( 255 ) ,`FILENAME` VARCHAR( 255 ) ,`FILETYPE` VARCHAR( 32 ) ,`LOCATION` VARCHAR( 128 ), `DOWNLOADS` BIGINT ,PRIMARY KEY ( `FKID` ))");

				// SQL for deleting the tables from the database. 
				$this->uninstallHandler->addDBAction("DROP TABLE `pgn_file`");

				/**** change nothing beyond this point ****/
				global $source, $classname; // the source path has to be provided by the calling template
				$modId = nextGUID();
				$this->installHandler->addDBAction("INSERT INTO modules (MODULE_ID, MODULE_NAME, DESCRIPTION, VERSION, MT_ID, CLASS, SOURCE) VALUES ($modId, '$this->name', '$this->description', $this->version, $mtid, '$classname','$source')");
			}
		}
	}
?>