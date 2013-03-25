<?
	/**********************************************************************
	 *	N/X - Web Content Management System
	 *	Copyright 2006 Sven Weih
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
	 * Adsense PlugIn
	 * Version 1.0
	 *
	 * @package Plugins
	 */
	class pgnAdsense extends Plugin {

		// Name of the Management's Table Primary Key
		var $pk_name = "FKID";

		// Name of the Plugin's Management Table. All tables should start with pgn_
		var $management_table = "pgn_adsense";
		var $isSingleConfig = false;
		
		/**
		  * Creates the input fields for editing text
		  * @param integer &$form link to the form the input-fields are to be created in 
		  */
		function edit(&$form) {
			global $lang, $sid, $c;
			// add button for external editor.
			$condition = "FKID = $this->fkid";
			$form->add(new TextInput($lang->get("adtextas", "AD-Javascript (copy from Google Adsense Homepage)"), "pgn_adsense", "ADTEXT", $condition, "type:textarea,width:350,size:10", ""));
		}
		

		/** 
		  * Used, for painting a preview of the content in the cms. Note, that the data
		  * is to be drawn in a table cell. Therefore you may design any html output, but
		  * you must return it as return value!
		  */
		function preview() {
			global $lang;						
			$out= ' Impressions: '.getDBCell("hits", "HIT", "ID=".translateState($this->fkid, 10, false));			
			return $out;
		}

		/**
		   * This function is used for drawing the html-code out to the templates.
		   * It just returns the code
		   * @param 		string	Optional parameters for the draw-function. There are none supported.
		   * @return		string	HTML-CODE to be written into the template.
		   */
		function draw($param = "") {
			global $cds, $c;
			if ($cds->is_development) {
				$content = '<div style="border:1px solid black; background-color:#e0e0e0;align:center;vertical-align:middle;padding:10px;">Adsene Placeholder. <br>Avoids influences to your adsense statistics.</div>';
			} else {			  
			  $content = unhtmlspecialchars(getDBCell("pgn_adsense", "ADTEXT", "FKID = $this->fkid"));						  
			  $content.= '<script type="text/javascript">bug = new Image(); bug.src=\''.$c["livedocroot"]."sys/hit.php?id=".$this->fkid.'&scope=adsense\';</script>';
			}
			return $content;
		}
		
 		/**
		  * Create a new Record with the given $this->fkid in the database.
		  * Initialize with standard values!
		  */
		function createRecord() {
			$createHandler = new ActionHandler("CREATE");
			$createHandler->addDBAction("INSERT INTO $this->management_table ($this->pk_name, ADTEXT) VALUES ($this->fkid, '')");
			$createHandler->process("CREATE");
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
				$this->name = "Adsense";
				// A short description, what the Plugin is for.
				$this->description = "Google Adsense Ad-Management Plugin.";
				// Version of the plugin. Use integer numbers only. Is important for future releases.
				$this->version = 1;

				/**** do not change from this point ****/
				$mtid = nextGUID(); // getting next GUID.
				//del1

				// SQL for creating the tables in the database. Do not call, if you do not need any tables in the database 
				$this->installHandler->addDBAction("CREATE TABLE `pgn_adsense` (`FKID` BIGINT NOT NULL ,`ADTEXT` TEXT NULL ,`IMPRESSIONS` BIGINT NOT NULL DEFAULT '0',`CLICKS` BIGINT NOT NULL DEFAULT '0',PRIMARY KEY ( `FKID` ) );");
				

				// SQL for deleting the tables from the database. 
				$this->uninstallHandler->addDBAction("DROP TABLE `pgn_adsense`");

				/**** change nothing beyond this point ****/
				global $source, $classname; // the source path has to be provided by the calling template
				$modId = nextGUID();
				$this->installHandler->addDBAction("INSERT INTO modules (MODULE_ID, MODULE_NAME, DESCRIPTION, VERSION, MT_ID, CLASS, SOURCE) VALUES ($modId, '$this->name', '$this->description', $this->version, $mtid, '$classname', '$source')");
			}
		}
	}
?>