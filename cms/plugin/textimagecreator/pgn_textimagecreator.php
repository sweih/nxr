<?
	/**********************************************************************
	 *	N/X - Web Content Management System
	 *	Copyright 2004 Sven Weih
	 *
	 *	This file is part of N/X.
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
	 * TextimageCreatpr PlugIn
	 * Version 1.0
	 *
	 * @package Plugins
	 */
	class pgnTextImageCreator extends Plugin {

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
			$form->add(new TextInput($lang->get("gr_text", "Graphical Text"), "pgn_label", "CONTENT", $condition, "type:text,width:350,size:255", ""));
		}

				
		/**
		 * Set the configuration-widgets for a cluster-content item.
		 */
		function editConfig() {
			global $lang;
			$gravity = array("Center" => "Center", "North"=>"North", "NorthEast"=>"NorthEast", "East" =>"East", "SouthEast" => "SouthEast", "South" => "South", "SouthWest" => "SouthWest", "West" => "West", "NorthWest" => "NorthWest");
			$fontstyle = array(" " => "Normal", "OBLIQUE" => "Bold", "ITALIC" => "Italic");
			$this->configWidgets[] = new TextInput($lang->get("font", "Font name"), "pgn_config_store", "TEXT4", "CLTI_ID=".$this->cltiid, "type:text", "MANDATORY", "TEXT");
			$this->configWidgets[] = new TextInput($lang->get("fontsize", "Font size"), "pgn_config_store", "NUMBER1", "CLTI_ID=".$this->cltiid, "type:text,width:30", "MANDATORY&NUMBER", "NUMBER");
            $this->configWidgets[] = new SelectOneInputFixed($lang->get("text_style", "Text Style"), "pgn_config_store", "TEXT5", $fontstyle, "CLTI_ID = ".$this->cltiid, "type:dropdown,width:200", "MANDATORY", "TEXT");
			$this->configWidgets[] = new SelectOneInputFixed($lang->get("text_align", "Text Align"), "pgn_config_store", "TEXT1", $gravity, "CLTI_ID = ".$this->cltiid, "type:dropdown,width:200", "MANDATORY", "TEXT");
            $this->configWidgets[] = new TextInput($lang->get("text_color", "Text Color"), "pgn_config_store", "TEXT2", "CLTI_ID=".$this->cltiid, "type:color", "MANDATORY", "TEXT");
			$this->configWidgets[] = new TextInput($lang->get("bg_color", "Background Color"), "pgn_config_store", "TEXT3", "CLTI_ID=".$this->cltiid, "type:color", "MANDATORY", "TEXT");			
			$this->configWidgets[] = new TextInput($lang->get("width", "Width"), "pgn_config_store", "NUMBER2", "CLTI_ID=".$this->cltiid, "type:text,width:30", "MANDATORY&NUMBER", "NUMBER");
			$this->configWidgets[] = new TextInput($lang->get("height", "Height"), "pgn_config_store", "NUMBER3", "CLTI_ID=".$this->cltiid, "type:text,width:30", "MANDATORY&NUMBER", "NUMBER");			
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
		 * create the image
		 * @param string Name and path of the image to create
		 */
		function createImage($imagename) {
		    global $panic, $debug;
			$content = getDBCell("pgn_label", "CONTENT", "FKID = $this->fkid");
			$image = new NXImageApi("", $imagename, false);
			$image->createCanvas($this->getOption("NUMBER2"),$this->getOption("NUMBER3"), $this->getOption("TEXT3"));
			$image->font = $this->getOption("TEXT4");
  			$image->fontsize = $this->getOption("NUMBER1");
  			$image->fontstyle = $this->getOption("TEXT5");
  			$image->gravity = $this->getOption("TEXT1");  			
  			$image->fillcolor = $this->getOption("TEXT2");
  			$image->drawText($content, 0, 0);  			
  			$image->save();	
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
				$content = "[".nl2br(getDBCell("pgn_label", "CONTENT", "FKID = $this->fkid"))."]";
			} else {
			  $im["path"] = $c["livefilesdocroot"] . $this->fkid.".png";

			  if (stristr($param, "ALL"))
				return $im;
			  $content = "<img src=\"" . $im["path"] . "\" border=\"0\" >";			
			}	   
			return $content;
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
			$content = nl2br(getDBCell("pgn_label", "CONTENT", "FKID = $this->fkid"));
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
		   * Create the sql-code for a version of the selected object
		   * @param integer ID of new Version.
		   * @returns string SQL Code for new Version.
		   */
		function createVersion($newid) {
			// query for content
			global $db, $c;

			$querySQL = "SELECT CONTENT FROM $this->management_table WHERE $this->pk_name = $this->fkid";
			$query = new query($db, $querySQL);
			$query->getrow();
			$content = addslashes($query->field("CONTENT"));
			$query->free();
			$this->createImage($c["livefilespath"] . $newid.".png");
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
				$this->name = "TextImageCreator";
				// A short description, what the Plugin is for.
				$this->description = "Text-Content which creates graphical images.";
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
				$this->installHandler->addDBAction("CREATE TABLE IF NOT EXISTS `pgn_label` (`FKID` bigint(20) NOT NULL default '0', `CONTENT` varchar(255),  PRIMARY KEY  (`FKID`),  UNIQUE KEY `FKID` (`FKID`)) TYPE=MyISAM;");

				/**** change nothing beyond this point ****/
				global $source, $classname; // the source path has to be provided by the calling template
				$modId = nextGUID();
				$this->installHandler->addDBAction("INSERT INTO modules (MODULE_ID, MODULE_NAME, DESCRIPTION, VERSION, MT_ID, CLASS, SOURCE) VALUES ($modId, '$this->name', '$this->description', $this->version, $mtid, '$classname', '$source')");
			}
		}
	}
?>