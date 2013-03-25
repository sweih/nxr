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
	 * Version 1.0
	 *
	 * @package Plugins
	 */
	class pgnMouseoverImage extends Plugin {

		// Name of the Management's Table Primary Key
		var $pk_name = "FKID";

		// Name of the Plugin's Management Table. All tables should start with pgn_
		var $management_table = "pgn_mouseoverimage";

		var $width;
		var $height;		

		/**
		  * Creates the input fields for editing images
		  * @param integer &$form link to the form the input-fields are to be created in 
		  */
		function edit(&$form) {
			global $lang, $c;
	  		require_once $c["path"] . "plugin/mouseoverimage/imageupload.inc.php";
	  		$condition = "FKID = $this->fkid";
	  		$form->add(new ImageUpload3($lang->get("ChooseLowLight", 'Choose lowlight image'), 'FILENAME1', $this->fkid, $this));								
	  		$form->add(new ImageUpload3($lang->get("ChooseHighLight", 'Choose highlight image'), 'FILENAME2', $this->fkid, $this));								
			$form->add(new TextInput($lang->get("o_alt"), "pgn_mouseoverimage", "ALT", $condition, "type:text,width:300,size:64", ""));
			$form->add(new TextInput($lang->get("o_copyright"), "pgn_mouseoverimage", "COPYRIGHT", $condition, "type:text,width:300,size:64", ""));
		}
		
		/**
		 * Checks if the record exists
		 */		 
		function exists() {
		  $result = (getDBCell($this->management_table, "HEIGHT", $this->pk_name." = ".$this->fkid) != "0");		    
		  return $result;
		}

		/**
		 * Set the configuration-widgets for a cluster-content item.
		 */
		function editConfig() {
			global $lang;
			$this->configWidgets[0] = new TextInput("maximum width", "pgn_config_store", "TEXT1", "CLTI_ID = ".$this->cltiid);
			$this->configWidgets[1] = new TextInput("maximum height", "pgn_config_store", "TEXT2", "CLTI_ID = ".$this->cltiid);
		}		
		
		/** 
		  * Used, for painting a preview of the content in the cms. Note, that the data
		  * is to be drawn in a table cell. Therefore you may design any html output, but
		  * you must return it as return value!
		  */
		function preview() {
			global $c;

			$width = getDBCell("pgn_mouseoverimage", "WIDTH", "FKID = $this->fkid");
			$height = getDBCell("pgn_mouseoverimage", "HEIGHT", "FKID = $this->fkid");

			if ($width == 0 && $height == 0)
				return "<div align=\"center\">No image uploaded yet.</div>";

			// Scaling down image.
			$scale_to = 120; //scale to 200px.
			$scale = 1;
			$dwidth = $width;
			$dheight = $height;

			if ($width > $scale_to || $height > $scale_to) {
				$scale_w = $width / $scale_to;

				$scale_h = $height / $scale_to;
				$scale = max($scale_w, $scale_h);
				//scale down
				$dwidth = $width / $scale;
				$dheight = $height / $scale;
			}

			$alt = getDBCell("pgn_mouseoverimage", "ALT", "FKID = $this->fkid");
			$filename = getDBCell("pgn_mouseoverimage", "FILENAME1", "FKID = $this->fkid");
			

			// painting preview.
			mt_srand((double)microtime() * 1000000);
			$randval = mt_rand();
			$output = "<table width=\"150\" cellpadding=\"0\" cellspacing=\"0\" border=\"0\">";
			$output .= "<tr><td><a href=\"#\" border=\"0\" onClick=\"preview = window.open('" . $c["devfilesdocroot"] . $filename . "', '', 'width=".($width + 20).",height=".($height + 20).",scrollbars=no,status=no,menubar=no');\">";
			if (file_exists($c["devfilespath"]."t".$filename)) {
				$output .= "<img src=\"" . $c["devfilesdocroot"] . "t" . $filename . "?$randval\" alt=\"$alt\" border=\"0\">";				
			} else {
				$output .= "<img src=\"" . $c["devfilesdocroot"] . $filename . "?$randval\" width=\"$dwidth\" height=\"$dheight\" alt=\"$alt\" border=\"0\">";
			}
			$output .= "</a></td></tr>";
			$output.= "<tr><td><b>Width:</b> $width<br><b>Height:</b> $height<br><b>ALT-Tag:</b> $alt</td></tr>";
			$output .= "</table>";
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

			if (is_object($cds)) {
			  $splevel = $cds->level;	
			}
			$sql = "SELECT * FROM $this->management_table WHERE $this->pk_name = $this->fkid";
			$query = new query($db, $sql);
			$query->getrow();
			$filename1 = $query->field("FILENAME1");
			$filename2 = $query->field("FILENAME2");
			

			if ($splevel == 10) {
				$path = $c["livefilesdocroot"];				
			} else 	if ($splevel == 0) {
				$path = $c["devfilesdocroot"];				
			}

			$width = $query->field("WIDTH");
			$height = $query->field("HEIGHT");
			$alt = $query->field("ALT");
			
			if ($width != 0) {
				$tag = "<img src=\"" . $path.$filename1 . "\" width=\"" . $width . "\" height=\"" . $height . "\" alt=\"" . $alt . "\" border=\"0\"";
				$tag.= " onMouseOver=\"this.src='".$path.$filename2."';\"";
				$tag.= " onMouseOut=\"this.src='".$path.$filename1."';\"";
				$tag.= ">";
			} else {
				$tag = "";
			}

			$query->free();
			return $tag;
		}

		

		/**
		  * Create a new Record with the given $this->fkid in the database.
		  * Initialize with standard values!
		  */
		function createRecord() {
			$createHandler = new ActionHandler("CREATE");
			$createHandler->addDBAction("INSERT INTO $this->management_table ($this->pk_name, FILENAME1, FILENAME2, ALT, WIDTH, HEIGHT, COPYRIGHT) VALUES ($this->fkid, '', '', '', 0,0,'')");
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
		function createVersion($newid) {
			// query for content
			global $db, $c;
			$destinationPath = $c["livefilespath"];
			$columns = $this->_getColumns($newid);
		
			nxDelete($destinationPath,  $columns["newfile"]);			
			nxDelete($destinationPath, "t" . $columns["newfile"]);
			nxDelete($destinationPath,  $columns["newfile2"]);
			nxDelete($destinationPath, "t" . $columns["newfile2"]);
			if ($columns["suffix"] != "") {
				nxCopy($c["devfilespath"] . $columns["filename"], $destinationPath , $columns["newfile"]);
				nxCopy($c["devfilespath"] . $columns["filename2"], $destinationPath , $columns["newfile2"]);
				if (file_exists($c["devfilespath"] . "t" . $columns["filename"])) 
				  nxCopy($c["devfilespath"] . "t" . $columns["filename"], $destinationPath , "t" . $columns["newfile"]);
				if (file_exists($c["devfilespath"] . "t" . $columns["filename2"])) 
				  nxCopy($c["devfilespath"] . "t" . $columns["filename2"], $destinationPath , "t" . $columns["newfile2"]);
			}

			return $this->_getCreateSQL($columns);
		}
		   
		/**
		 * Get recordset from pgnImage as associative array. Internally used only.
		 * @param integer id for new recordset.
		 * @returns 2d-array of name-value-pairs.
		 */
		function _getColumns($newid) {
			global $db;
			
			$querySQL = "SELECT * FROM $this->management_table WHERE $this->pk_name = $this->fkid";
			$query = new query($db, $querySQL);
			$query->getrow();
			$width = addslashes($query->field("WIDTH"));
			$height = addslashes($query->field("HEIGHT"));
			$alt = addslashes($query->field("ALT"));
			$copyright = addslashes($query->field("COPYRIGHT"));
			$filename = $query->field("FILENAME1");
			$filename2 = $query->field("FILENAME2");;
			$query->free();
			// copy image to new version
			$fileparts = explode(".", $filename);
			$suffix = strtolower($fileparts[(count($fileparts) - 1)]);
			$newfile = $newid . "FILENAME1." . $suffix;
			$newfile2 = $newid.'FILENAME2.'.$suffix;
			
			return array("newid" => $newid, "suffix" => $suffix, "filename" => $filename, "filename2" => $filename2, "newfile" => $newfile,"newfile2"=>$newfile2 ,"alt" => $alt, "height" => $height, "width" => $width, "copyright" => $copyright);
		}

		/**
		 * generates SQL for copying a record.
		 * @param array 2d-array retrieved with _getColumns
		 * @returns string SQL
		 */
		function _getCreateSQL($columns) {
			return "INSERT INTO $this->management_table ($this->pk_name, FILENAME1, FILENAME2, ALT, WIDTH, HEIGHT, COPYRIGHT) VALUES (".$columns["newid"].", '".$columns["newfile"]."','".$columns["newfile2"]."' ,'".$columns["alt"]."', ".$columns["width"].", ".$columns["height"].", '".$columns["copyright"]."')";
		}		

		function copyRecord($newid) { 
			// query for content
			global $db, $c;
			$destinationPath = $c["devfilespath"];
			$columns = $this->_getColumns($newid);

			if ($columns["suffix"] != "") {
				nxCopy($c["devfilespath"] . $columns["filename"], $destinationPath , $columns["newfile"]);
				if (file_exists($c["devfilespath"] . "t" . $columns["filename"])			)
				  nxCopy($c["devfilespath"] . "t" . $columns["filename"], $destinationPath , "t" . $columns["newfile"]);
				
				nxCopy($c["devfilespath"] . $columns["filename2"], $destinationPath , $columns["newfile2"]);
				if (file_exists($c["devfilespath"] . "t" . $columns["filename2"])			)
				  nxCopy($c["devfilespath"] . "t" . $columns["filename2"], $destinationPath , "t" . $columns["newfile2"]);
			}

			return $this->_getCreateSQL($columns);			
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
				$this->name = "MouseoverImage";
				// A short description, what the Plugin is for.
				$this->description = "MouseOverImage. Allowed formats are GIF, JPEG and PNG.";
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
				$this->installHandler->addDBAction("CREATE TABLE pgn_mouseoverimage (FKID bigint(20) NOT NULL default '0', FILENAME1 varchar(32) default NULL, FILENAME2 varchar(32) default NULL, ALT varchar(64) default NULL, WIDTH smallint(6) default NULL, HEIGHT smallint(6) default NULL, COPYRIGHT varchar(64) default NULL, PRIMARY KEY  (FKID), UNIQUE KEY FKID (FKID)) TYPE=MyISAM;");

				// SQL for deleting the tables from the database. 
				$this->uninstallHandler->addDBAction("DROP TABLE `pgn_mouseoverimage`");

				/**** change nothing beyond this point ****/
				global $source, $classname; // the source path has to be provided by the calling template
				$modId = nextGUID();
				$this->installHandler->addDBAction("INSERT INTO modules (MODULE_ID, MODULE_NAME, DESCRIPTION, VERSION, MT_ID, CLASS, SOURCE) VALUES ($modId, '$this->name', '$this->description', $this->version, $mtid, '$classname','$source')");
			}
		}
	}
?>