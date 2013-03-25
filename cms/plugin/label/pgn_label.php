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
	 * Label PlugIn
	 * Version 1.0
	 *
	 * @package Plugins
	 */
	class pgnLabel extends Plugin {

		// Name of the Management's Table Primary Key
		var $pk_name = "FKID";

		// Name of the Plugin's Management Table. All tables should start with pgn_
		var $management_table = "pgn_label";
		var $isSingleConfig = true;
		
		/**
		  * Creates the input fields for editing text
		  * @param integer &$form link to the form the input-fields are to be created in 
		  */
		function edit(&$form) {
			global $lang, $sid, $c;

			// add button for external editor.
			$condition = "FKID = $this->fkid";
			$form->add(new TextInput($lang->get("o_text"), "pgn_label", "CONTENT", $condition, "type:text,width:350,size:255", ""));
		}
		
				/**
		 * Set the configuration-widgets for a cluster-content item.
		 */
		function editConfig() {
			global $lang;
			$this->configWidgets[0] = new TextInput("CSS Class", "pgn_config_store", "TEXT1", "CLTI_ID = ".$this->cltiid);		
		}

		/** 
		  * Used, for painting a preview of the content in the cms. Note, that the data
		  * is to be drawn in a table cell. Therefore you may design any html output, but
		  * you must return it as return value!
		  */
		function preview() {
			global $lang;
			$content = getDBCell("pgn_label", "CONTENT", "FKID = $this->fkid");

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
			$content = nl2br(getDBCell("pgn_label", "CONTENT", "FKID = $this->fkid"));
			$css = $this->getOption("TEXT1");
			if ($css != "") {
			  $content = '<div class="'.$css.'">'.$content."</div>";	
			}
			return applyFilterPlugins($content);
		}
		
    	  /**
	   * This function is used for drawing the html-code out to the templates in a editable mode.
	   * It just returns the code
	   * @param 		string	Optional parameters for the draw-function. There are none supported.
	   * @return		string	HTML-CODE to be written into the template.
	   */
		function drawLiveAuthoring($param = "") {
			global $c, $lang;

			$this->drawSMAJS();
			$content = $this->draw();
			$newcontent = value("htxt_" . $this->fkid);
		
			if ($newcontent != "0") {
				$content = $newcontent;

				if (get_magic_quotes_runtime())
					$newcontent = str_replace("\\", "", $newcontent);

				//$newcontent = parseSQL($newcontent);
				global $db;
				$query = new query($db, "UPDATE pgn_label SET CONTENT = '$newcontent' WHERE FKID = $this->fkid");
				$query->free();
				$content = getDBCell("pgn_label", "CONTENT", "FKID =" . $this->fkid);
			}

			$div = "<div id='txt_" . $this->fkid . "' style='border: 1px dashed #999999;' contentEditable='true' onFocus='document.getElementById(\"txt_" . $this->fkid . "\").style.setAttribute(\"border\", \"1px solid #dd0033\", \"false\");'  onBlur='document.getElementById(\"txt_" . $this->fkid . "\").style.setAttribute(\"border\", \"1px dashed #999999\", \"false\");'>$content</div>";
			$div
				.= "<a href='#' onClick='saveTXT(); return false;'><img src='" . $c["docroot"] . "img/icons/sma_save.gif' alt='" . $lang->get("pgntxt_sma", "Save all edited texts on the page"). "' width='16' height='16' border='0'></a><input type='hidden' name='htxt_" . $this->fkid . "' value=''>";
			return $div;
		}
		
		/**
		 * Draw the JS-Script for saving changed texts...
		 */
		function drawSMAJS() {
			global $txtsaved;

			if (!$txtsaved) {
				$txtsaved = true;

				// writeout JS Function.
				$o = '<script language="JavaScript">';
				$o .= '  function saveTXT() {';
				$o .= '    var txts = document.getElementsByTagName("*");';
				$o .= '     for (var i=0; i < txts.length; i++) {';
				$o .= '        idname = txts[i].id;';
				$o .= '        if (idname.substr(0,4) =="txt_" && txts[i].contentEditable=="true") {';
				$o .= '          var content = txts[i].innerHTML;';
				$o .= '          while(content.search(/\|/)!=-1) {';
				$o .= '            content=content.replace(/\|/,"§%%§");';
				$o .= '          }';
				$o .= '          document.getElementById("h" + idname).value = content;';
				$o .= '        }';
				$o .= '     }';
				$o .= '     document.smaform.submit();';
				$o .= '  }';
				$o .= '</script>';
				echo $o;
			}
		}

	
		/**
		 * Export the data
		 */
		function export() {
			$content = urlencode(getDBCell("pgn_label", "CONTENT", "FKID = $this->fkid"));
			$xmlOptions = array(XML_OPTION_CASE_FOLDING => TRUE, XML_OPTION_SKIP_WHITE => TRUE);
    		$xml =& new XPath(FALSE, $xmlOptions);
    		$xml->appendChild('', "<nx:content type=\"LABEL\"/>");
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
    		$content = urldecode($xml->getData('/NX:CONTENT[@type="LABEL"]'));
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
	 * Searches for keywords in the plugin.
	 * Used for search method
	 * @param array strings in a 1dim array to search for.
	 * @param array options to concat  AND|OR
	 * @returns array 2d Array with search results and fkid or null if no result found.
	 */
		function search($searchStrings, $searchOptions = null) {
			global $db;

			$whereClause = "";

			for ($i = 0; $i < count($searchStrings); $i++) {
				if ($i == 0) {
					$whereClause = "UPPER(CONTENT) LIKE '%" . strtoupper($searchStrings[$i]). "%' ";
				} else {
					if (strtoupper($searchOptions[$i]) == "OR") {
						$whereClause .= "OR ";
					} else {
						$whereClause .= "AND ";
					}

					$whereClause .= "CONTENT LIKE '%" . $searchStrings[$i] . "%' ";
				}
			}

			$whereClause .= " LIMIT 0,30";
			$sql = "SELECT FKID, CONTENT FROM pgn_label WHERE " . $whereClause;
			$query = new query($db, $sql);

			$results = array ();

			while ($query->getrow()) {
				$result["FK"] = $query->field("FKID");

				$content = $query->field("CONTENT");
				$posi = array ();
				$area = 60;

				for ($i = 0; $i < count($searchStrings); $i++) {
					$positions = null;

					$sw = $searchStrings[$i];
					$stpos = strpos($content, $sw);

					if ($stpos > $area) {
						$positions["start"] = $stpos - $area;
					} else {
						$positions["start"] = 1;
					}

					if ($stpos < (strlen($content) - $area)) {
						$positions["end"] = $stpos + $area + strlen($sw);
					} else {
						$positions["end"] = strlen($content);
					}

					array_push($posi, $positions);
				}

				// calculate content...
				//slow but save implementation.
				// build grid, which chars to display.
				$res = array_fill(0, strlen($content), false);

				for ($i = 0; $i < count($posi); $i++) {
					for ($j = $posi[$i]["start"]; $j < $posi[$i]["end"]; $j++) {
						$res[$j] = true;
					}
				}

				// now compute grid.
				$cursor = 1;
				$len = strlen($content);
				$cntTrimmed = "";

				while ($cursor < $len - 1) {
					$start = null;

					$end = null;

					for ($i = $cursor; $i < $len; $i++) {
						if ($res[$i] == true) {
							$start = $i;

							$cursor = $i;
							$i = $len;
						}
					}

					if ($start != null) {
						for ($i = $start; $i < $len; $i++) {
							if ($res[$i] == false) {
								if ($res[$i + 10] != true) {
									$end = $i;

									$cursor = $end;
									$i = $len;
								}
							}
						}
					} else {
						$cursor = $len;
					}

					if (($start > 1 && $cntTrimmed == ""))
						$cntTrimmed .= "...";

					$cntTrimmed .= substr($content, $start - 1, $end);

					if ($end < $len)
						$cntTrimmed .= "...";

					if ($start == null)
						$cursor = $len; // security for avoiding endlesss loops.
				}

				$result["CONTENT"] = $cntTrimmed;
				array_push($results, $result);
			}

			return $results;
		}

		/**
		   * Create the sql-code for a version of the selected object
		   * @param integer ID of new Version.
		   * @returns string SQL Code for new Version.
		   */
		function createVersion($newid) {
			// query for content
			global $db;

			$querySQL = "SELECT CONTENT FROM $this->management_table WHERE $this->pk_name = $this->fkid";
			$query = new query($db, $querySQL);
			$query->getrow();
			$content = addslashes($query->field("CONTENT"));
			$query->free();

			$sql = "INSERT INTO $this->management_table ($this->pk_name, CONTENT) VALUES ($newid, '$content')";
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
				$this->name = "Label";
				// A short description, what the Plugin is for.
				$this->description = "Text-Content with one line for input.";
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
				$this->installHandler->addDBAction("CREATE TABLE `pgn_label` (`FKID` bigint(20) NOT NULL default '0', `CONTENT` varchar(255),  PRIMARY KEY  (`FKID`),  UNIQUE KEY `FKID` (`FKID`)) TYPE=MyISAM;");

				// SQL for deleting the tables from the database. 
				$this->uninstallHandler->addDBAction("DROP TABLE `pgn_label`");

				/**** change nothing beyond this point ****/
				global $source, $classname; // the source path has to be provided by the calling template
				$modId = nextGUID();
				$this->installHandler->addDBAction("INSERT INTO modules (MODULE_ID, MODULE_NAME, DESCRIPTION, VERSION, MT_ID, CLASS, SOURCE) VALUES ($modId, '$this->name', '$this->description', $this->version, $mtid, '$classname', '$source')");
			}
		}
	}
?>