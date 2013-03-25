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
	 * Code Sniplet PlugIn
	 * Version 1.0
	 *
	 * @package Plugins
	 */	
	class pgnSniplet extends Plugin {

		// Name of the Management's Table Primary Key
		var $pk_name = "FKID";

		// Name of the Plugin's Management Table. All tables should start with pgn_
		var $management_table = "pgn_sniplet";

		// configuration Variables
		var $isSingleConfig = false;
		var $pluginType = 1; // 1= Content Plugin, 2= System Extension


		/**
		  * Creates the input fields for editing text
		  * @param integer &$form link to the form the input-fields are to be created in 
		  */
		function edit(&$form) {
			global $lang, $sid, $c, $sma, $auth;
  			if ($auth->checkPermission("DEVELOPER|ADMINISTRATOR")) {
			  $sniptype[0][0] = "PHP";
			  $sniptype[0][1] = "0";
			  $sniptype[1][0] = "HTML, Javascript, CSS";
			  $sniptype[1][1] = "1";
			  $form->add(new SelectOneInputFixed($lang->get("snip_type", "Sniplet Type"), "pgn_sniplet", "SNIPLETTYPE", $sniptype, "FKID = $this->fkid"));
  			  $form->add(new TextInput($lang->get("snip_code", "Sniplet-Code"), "pgn_sniplet", "SNIPLET", "FKID = $this->fkid", "type:textarea,size:10,width:400".$italic.$underline, ""));
			  $form->add(new Label("lbl", $lang->get('secedit', 'Please note: Due to security concerns, the comination \\" will always be replaced with ".'), 'standardlight', 2));
			  $sniptype = array();
  			} else {
  				$form->add(new label("lbl",'Only developers and admins can edit sniplets.', 'standardlight', 2));
  			}
		}

		
		/** 
		  * Used, for painting a preview of the content in the cms. Note, that the data
		  * is to be drawn in a table cell. Therefore you may design any html output, but
		  * you must return it as return value!
		  */
		function preview() {
			$content = getDBCell("pgn_sniplet", "SNIPLET", "FKID = $this->fkid");

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
			global $cds, $page, $v, $db, $c;
			$sniplet = html_entity_decode(getDBCell("pgn_sniplet", "SNIPLET", "FKID = $this->fkid"));							
			$sniplet = str_replace('&#039;', "'", $sniplet);						
			$sniptype = getDBCell("pgn_sniplet", "SNIPLETTYPE", "FKID = $this->fkid");
			if ($sniptype == 0 ) {
			  ob_start();
		 	  $result = eval($sniplet);
		 	  $result = ob_get_contents();
		 	  ob_end_clean();						
			} else {				
				$result = $sniplet;
			}
			return $result;
		}

		

		/**
		 * Export the data
		 */
		function export() {
			$content = urlencode(getDBCell("pgn_sniplet", "SNIPLET", "FKID = $this->fkid"));
			$xmlOptions = array(XML_OPTION_CASE_FOLDING => TRUE, XML_OPTION_SKIP_WHITE => TRUE);
    		$xml =& new XPath(FALSE, $xmlOptions);
    		$xml->appendChild('', "<nx:sniplet type=\"TEXT\"/>");
    		$xml->appendData("/NX:SNIPLET[1]", $content);
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
    		$content = urldecode($xml->getData('/NX:SNIPLET[@type="TEXT"]'));
    		if ($content != false) {
    			$content = parseSQL($content);
    			$sql = "UPDATE $this->management_table SET SNIPLET = '$content' WHERE $this->pk_name = $this->fkid";
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
			$createHandler->addDBAction("INSERT INTO $this->management_table ($this->pk_name, SNIPLET) VALUES ($this->fkid, '')");
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
			$content = $query->field("SNIPLET");
			$snt = $query->field("SNIPLETTYPE");
			$parser = new LaunchText(variation(), false);
			$content = addslashes($parser->parseText($content));			
			$query->free();

			$sql = "INSERT INTO $this->management_table ($this->pk_name, SNIPLET, SNIPLETTYPE ) VALUES ($newid, '$content', $snt)";
			return $sql;
		}
		
		

		/**
		 * Copy this record and all its data to new id.
		 * @param integer id which is used as PK for new record.
		 */
		function copyRecord($newid) {
			// query for content
			global $db;

			$querySQL = "SELECT SNIPLET FROM $this->management_table WHERE $this->pk_name = $this->fkid";
			$query = new query($db, $querySQL);
			$query->getrow();
			$content = $query->field("SNIPLET");
			$content = addslashes($content);			
			$snt = $query->field("SNIPLETTYPE");
			$query->free();
			$sql = "INSERT INTO $this->management_table ($this->pk_name, SNIPLET, SNIPLETTYPE) VALUES ($newid, '$content', $snt)";
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
				$this->name = "Code Sniplets";
				// A short description, what the Plugin is for.
				$this->description = "PHP, HTML or Javascript sniplets.";
				// Version of the plugin. Use integer numbers only. Is important for future releases.
				$this->version = 1;

				// Every module can have its own and individual META-Data in NX. The following Handler is
				// for creating a META-Data-Template and for assigning it to the Plugin.
				// IF you do not want to declare an individual META-Scheme, then set $mtid=0 and delete
				// everything between del1 and /del1!

				/**** do not change from this point ****/
				$mtid = nextGUID(); // getting next GUID.
				//del1


				/**** add META-Data now ****/
				$guid = nextGUID();
				

				/**** end adding META-Data ****/
				// /del1

				// SQL for creating the tables in the database. Do not call, if you do not need any tables in the database 
				$this->installHandler->addDBAction("CREATE TABLE `pgn_sniplet` (`FKID` BIGINT NOT NULL ,`SNIPLET` TEXT NULL, `SNIPLETTYPE` TINYINT NULL ) TYPE = MYISAM");

				// SQL for deleting the tables from the database. 
				$this->uninstallHandler->addDBAction("DROP TABLE `pgn_sniplet`");

				/**** change nothing beyond this point ****/
				global $source, $classname; // the source path has to be provided by the calling template
				$modId = nextGUID();
				$this->installHandler->addDBAction("INSERT INTO modules (MODULE_ID, MODULE_NAME, DESCRIPTION, VERSION, MT_ID, CLASS, SOURCE, MODULE_TYPE_ID) VALUES ($modId, '$this->name', '$this->description', $this->version, $mtid, '$classname', '$source', $this->pluginType)");
			}
		}
	}
?>