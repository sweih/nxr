<?
	/**********************************************************************
	 *	N/X - Web Content Management System
	 *	Copyright 2002-2006 Sven Weih, FZI Research Center for Information Technologies
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
	 * List PlugIn
	 * Version 1.0
	 *
	 * @package Plugins
	 */
	class pgnList extends Plugin {

		// Name of the Management's Table Primary Key
		var $pk_name = "FKID";

		// Name of the Plugin's Management Table. All tables should start with pgn_
		var $management_table = "pgn_list";
		var $isSingleConfig = true;
		
		/**
		  * Creates the input fields for editing text
		  * @param integer &$form link to the form the input-fields are to be created in 
		  */
		function edit(&$form) {
			global $lang, $sid, $c;
      $valuesString = $this->getOption("TEXT1");
      $values = explode(',', $valuesString);
      
      // make name value array out of it.
      $nv = array();
      for ($i=0; $i < count ($values); $i++) {
      	 $val = trim($values[$i]);
      	 $nv[] = array($val, $val);
      }

      // add widget to the from 
			$condition = "FKID = $this->fkid";			
			$form->add(new SelectOneInputFixed($lang->get('please select', 'Please select'), 'pgn_list', 'CONTENT', $nv, $condition ,"type:dropdown", "", "TEXT"));
		}
		
				/**
		 * Set the configuration-widgets for a cluster-content item.
		 */
		function editConfig() {
			global $lang;
			$this->configWidgets[0] = new TextInput("Commaseprated Values", "pgn_config_store", "TEXT1", "CLTI_ID = ".$this->cltiid, "type:text,size:255,width:300");		
		}

		/** 
		  * Used, for painting a preview of the content in the cms. Note, that the data
		  * is to be drawn in a table cell. Therefore you may design any html output, but
		  * you must return it as return value!
		  */
		function preview() {
			global $lang;
			$content = getDBCell("pgn_list", "CONTENT", "FKID = $this->fkid");

			if ($content == "")
				$content = "&lt;".$lang->get("no_content", "No content entered yet.")."&gt;";			
			return $content;
		}

		/**
		   * This function is used for drawing the html-code out to the templates.
		   * It just returns the code
		   * @param 		string	Optional parameters for the draw-function. There are none supported.
		   * @return		string	HTML-CODE to be written into the template.
		   */
		function draw($param = "") {
			$content = getDBCell("pgn_list", "CONTENT", "FKID = $this->fkid");
			return $content;
		}
		


		/**
		 * Export the data
		 */
		function export() {
			$content = urlencode(getDBCell("pgn_list", "CONTENT", "FKID = $this->fkid"));
			$xmlOptions = array(XML_OPTION_CASE_FOLDING => TRUE, XML_OPTION_SKIP_WHITE => TRUE);
    		$xml =& new XPath(FALSE, $xmlOptions);
    		$xml->appendChild('', "<nx:content type=\"LIST\"/>");
    		$xml->appendData("/NX:CONTENT[1]", $content);
    		return $xml->exportAsXml('', '');
		}
		
		/**
		 * Import the data
		 * @param string XML, generated with export function.
		 */
		function import($data) {
			$xmlOptions = array(XML_OPTION_CASE_FOLDING => TRUE, XML_OPTION_SKIP_WHITE => TRUE);
    		$xml =& new XPath(FALSE, $xmlOptions);
    		$xml->importFromString($data);
    		$content = urldecode($xml->getData('/NX:CONTENT[@type="LIST"]'));
    		if ($content != false) {
    			$content = parseSQL($content);
    			$sql = "UPDATE $this->management_table SET CONTENT = '$content' WHERE $this->pk_name = $this->fkid";
    			global $db;
    			$query = new query($db, $sql);
    			$query->free();    			
    		}
		}
	
	
		/**
		  * Create a new Record with the given $this->fkid in the database.
		  * Initialize with standard values!
		  */
		function createRecord() {
			$createHandler = new ActionHandler("CREATE");
			$createHandler->addDBAction("INSERT INTO $this->management_table ($this->pk_name, CONTENT) VALUES ($this->fkid, '')");
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
		   * Specifies information for installation and deinstallation of the plugin.
		   */
		function registration() {
			global $auth;

			// Authentification is require_onced for changing system configuration. Do not change.
			if ($auth->checkPermission("ADMINISTRATOR")) {

				// parent registration function for initializing. Do not change.
				Plugin::registration();

				// Name of the Plugin. The name will be displayed in the WCMS for selection
				$this->name = "List";
				// A short description, what the Plugin is for.
				$this->description = "Dropdownbox for use in classes only";
				// Version of the plugin. Use integer numbers only. Is important for future releases.
				$this->version = 1;

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
				$this->installHandler->addDBAction("CREATE TABLE `pgn_list` (`FKID` bigint(20) NOT NULL default '0', `CONTENT` varchar(255),  PRIMARY KEY  (`FKID`),  UNIQUE KEY `FKID` (`FKID`)) TYPE=MyISAM;");

				// SQL for deleting the tables from the database. 
				$this->uninstallHandler->addDBAction("DROP TABLE `pgn_list`");

				/**** change nothing beyond this point ****/
				global $source, $classname; // the source path has to be provided by the calling template
				$modId = nextGUID();
				$this->installHandler->addDBAction("INSERT INTO modules (MODULE_ID, MODULE_NAME, DESCRIPTION, VERSION, MT_ID, CLASS, SOURCE) VALUES ($modId, '$this->name', '$this->description', $this->version, $mtid, '$classname', '$source')");
			}
		}
	}
?>