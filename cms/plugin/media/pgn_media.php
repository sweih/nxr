<?
/**********************************************************************
 *	N/X - Web Content Management System
 *	Media Plugin
 *
 *	Copyright 2002-2004 by	 
 *	  Tim Haedke (info@timhaedke.de)
 * 	  Fabian König (fabian@nxsystems.org)
 *	  Sven Weih (sven@nxsystems.org)
 *	
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
  * Media PlugIn
  * Version 2.0
  * Currently supports: 
  *   - Macromedia Flash Films (.swf)
  *   - Portable Document Format (.pdf)
  *   - Windows Media (.avi)
  *   - Real Player (.rm)
  *   - Apple Quicktime (.mov)
  *   - MP3 over Windows Media (.mp3)
  *
  * @package Plugins
  */
  
  class pgnMedia extends Plugin {
  	
  	// Name of the Management's Table Primary Key
  	var $pk_name = "FKID";
	// Name of the Plugin's Management Table. All tables should start with pgn_
	var $management_table = "pgn_media";
    
	var $width;
	var $height;
    
  	/**
 	 * Creates the input fields for editing media contents
 	 * @param integer &$form link to the form the input-fields are to be created in 
 	 */
  	function edit(&$form) {
 		global $lang, $c;
        	require_once $c["path"]."plugin/media/mediaupload.inc.php";
        	$condition = "FKID = ".$this->fkid;
        	
   		$form->add(new Label("lbl", $lang->get("MEDIA_SUPPORTED", "The Media plugin supports the following file-types:<br>Macromedia Flash Films (.swf), Windows Media (.avi), Real Player (.rm), Apple Quicktime (.mov), MP3 over Windows Media (.mp3), PDF (.pdf)"), "informationheader",2));
  		$form->add(new MediaUpload($lang->get("choosefile"), $this->fkid));
  		$form->add(new Label("lbl", $lang->get("CLEAR_MEDIA", "Remove file from database"), "standardlight"));
   		$form->add(new Checkbox("REMOVE", "1", "standard"));
  		$form->add(new TextInput($lang->get("o_copyright"), "pgn_media", "COPYRIGHT", $condition, "type:text,width:300,size:64", ""));
  		$form->add(new TextInput($lang->get("WIDTH","Width"), "pgn_media", "WIDTH", $condition, "type:text,width:50,size:4", ""));
  		$form->add(new TextInput($lang->get("HEIGHT","Height"), "pgn_media", "HEIGHT", $condition, "type:text,width:50,size:4", ""));	
   		
	}
	 
 	
 	
 	/** 
 	 * Used, for painting a preview of the content in the cms. Note, that the data
 	 * is to be drawn in a table cell. Therefore you may design any html output, but
 	 * you must return it as return value!
 	 */
 	 function preview() {
 	 	global $c;

 	 	$filename =  getDBCell("pgn_media", "FILENAME", "FKID = $this->fkid");
		$fileparts = explode(".", $filename);
		$suffix = strtolower($fileparts[(count($fileparts)-1)]);
 	 	if ($filename == "") return "<div align=\"center\">No media file uploaded yet.</div>";

 	 	$copyright =  getDBCell("pgn_media", "COPYRIGHT", "FKID = $this->fkid");
 	 	$width     =  getDBCell("pgn_media", "WIDTH", "FKID = $this->fkid");
 	 	$height    =  getDBCell("pgn_media", "HEIGHT", "FKID = $this->fkid");
 	 	
		switch ($suffix) {

			case "swf":
				// Scaling down media file.
				$scale_to = 200; //scale to 200px.
				$scale=1;
				$dwidth = $width;
				$dheight = $height;
				if ($width > $scale_to || $height>$scale_to) {
					$scale_w = $width / $scale_to;
					$scale_h = $height / $scale_to;
					$scale = max($scale_w, $scale_h);
					//scale down
					$dwidth = $width / $scale;
					$dheight = $height / $scale;
				}

				// produce special preview-source
				$preview_source = "Macromedia Flash<br>";
				$preview_source .= "<object classid=\"clsid:D27CDB6E-AE6D-11cf-96B8-444553540000\" codebase=\"http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,29,0\" width=\"$dwidth\" height=\"$dheight\">";
				$preview_source .=  "<param name=\"movie\" value=\"".$c["devfilesdocroot"].$filename."\">";
				$preview_source .=  "<param name=\"quality\" value=\"high\">";
				$preview_source .=  "<embed src=\"".$c["devfilesdocroot"].$filename."\" width=\"$dwidth\" height=\"$dheight\" quality=\"high\" pluginspage=\"http://www.macromedia.com/go/getflashplayer\" type=\"application/x-shockwave-flash\"></embed>";
				$preview_source .=  "</object>";				
		
				return $this->draw_standard_preview($preview_source, $width, $height, $copyright);

				break;

			case "pdf":
				$preview_source = "<a href=\"".$c["devfilesdocroot"].$filename."\" target=\"_blank\" alt=\"open in new window\"><img src=\"".$c["docroot"]."plugin/media/icons/icon_pdf.gif\" border=0></a>";
				
				return $this->draw_standard_preview($preview_source, "n.a.", "n.a.", $copyright);
				break;

			case "avi":
				return "<div align=\"center\">Windows Media Video</div>";
			case "mp3":
				// Scaling down media file.
				$scale_to = 200; //scale to 200px.
				$scale=1;
				$dwidth = $width;
				$dheight = $height;
				if ($width > $scale_to || $height>$scale_to) {
					$scale_w = $width / $scale_to;
					$scale_h = $height / $scale_to;
					$scale = max($scale_w, $scale_h);
					//scale down
					$dwidth = $width / $scale;
					$dheight = $height / $scale;
				}

				// EMBED CODE FOR WINDOWS MEDIA
				$preview_source = "MP3<br>";
				$preview_source .= "<OBJECT ID=\"player_wm\" BORDER=0 width=$dwidth height=$dheight classid=\"CLSID:22D6F312-B0F6-11D0-94AB-0080C74C7E95\" ";
				$preview_source .= "codebase=\"http://activex.microsoft.com/activex/controls/mplayer/en/nsmp2inf.cab#Version=6,4,5,715\" ";
				$preview_source .= "standby=\"Loading Microsoft® Windows® Media Player components...\" type=\"application/x-oleobject\"> ";	
				$preview_source .= "<PARAM NAME=\"FileName\" VALUE=\"".$c["devfilesdocroot"].$filename."\">";
				$preview_source .= "<PARAM NAME=\"AutoStart\" VALUE=\"false\">";
				$preview_source .= "<PARAM NAME=\"ShowControls\" VALUE=\"true\">";
				$preview_source .= "<PARAM NAME=\"ShowDisplay\" VALUE=\"false\">";
				$preview_source .= "<PARAM NAME=\"AnimationatStart\" VALUE=\"false\">";
				$preview_source .= "<EMBED BORDER=0 type=\"application/x-mplayer2\" ";
				$preview_source .= "pluginspage=\"http://www.microsoft.com/Windows/Downloads/Contents/Products/MediaPlayer/\" ";
				$preview_source .= "SRC=\"".$c["devfilesdocroot"].$filename."\" ";
				$preview_source .= "name=\"player_wm\" width=$dwidth height=$dheight AutoStart=false ShowControls=1 ShowDisplay=0 ShowStatusBar=0 animationAtStart=0></EMBED></OBJECT>";

				return $this->draw_standard_preview($preview_source, $width, $height, $copyright);

				break;
				
				
			case "rm":

				// Scaling down media file.
				$scale_to = 200; //scale to 200px.
				$scale=1;
				$dwidth = $width;
				$dheight = $height;
				if ($width > $scale_to || $height>$scale_to) {
					$scale_w = $width / $scale_to;
					$scale_h = $height / $scale_to;
					$scale = max($scale_w, $scale_h);
					//scale down
					$dwidth = $width / $scale;
					$dheight = $height / $scale;
				}

				// EMBED CODE FOR REAL MEDIA
				$preview_source = "Real Media<br>";
				$preview_source .= "<OBJECT ID=\"player_rm\" CLASSID=\"clsid:CFCDAA03-8BE4-11cf-B84B-0020AFBBCCFA\" HEIGHT=$dheight WIDTH=$dwidth>";
				$preview_source .= "<PARAM NAME=\"controls\" VALUE=\"ImageWindow\">";
				$preview_source .= "<PARAM NAME=\"console\" VALUE=\"Clip1\">";
				$preview_source .= "<PARAM NAME=\"autostart\" VALUE=\"false\">";
				$preview_source .= "<PARAM NAME=\"src\" VALUE=\"".$c["devfilesdocroot"].$filename."\">";
				$preview_source .= "<EMBED NAME=\"player_rm\" SRC=\"".$c["devfilesdocroot"].$filename."\" BORDER=0 autostart=\"false\" type=\"audio/x-pn-realaudio-plugin\" ";
				$preview_source .= "pluginspage=\"http://www.real.com\" CONSOLE=\"Clip1\" CONTROLS=\"ImageWindow\" HEIGHT=$dheight WIDTH=$dwidth></OBJECT>";
				$preview_source .= "<br>";
				$preview_source .= "<OBJECT ID=\"player_rm\" CLASSID=\"clsid:CFCDAA03-8BE4-11cf-B84B-0020AFBBCCFA\" HEIGHT=40 WIDTH=$dwidth>";
				$preview_source .= "<PARAM NAME=\"controls\" VALUE=\"ControlPanel\">";
				$preview_source .= "<PARAM NAME=\"console\" VALUE=\"Clip1\">";
				$preview_source .= "<PARAM NAME=\"autostart\" VALUE=\"false\">";
				$preview_source .= "<PARAM NAME=\"src\" VALUE=\"".$c["devfilesdocroot"].$filename."\">";
				$preview_source .= "<EMBED NAME=\"player_rm\" SRC=\"".$c["devfilesdocroot"].$filename."\" BORDER=0 autostart=\"false\" type=\"audio/x-pn-realaudio-plugin\" ";
				$preview_source .= "pluginspage=\"http://www.real.com\" CONSOLE=\"Clip1\" CONTROLS=\"ControlPanel\" HEIGHT=40 WIDTH=$dwidth></OBJECT>";

				return $this->draw_standard_preview($preview_source, $width, $height, $copyright);

				break;

			case "mov":

				// mov can't be scaled because it's cropped

				$dwidth = $width;
				$dheight = $height + 15; // Make view bigger for controller
				$preview_source = "Apple Quicktime<br>";
				$preview_source .= "<object classid=\"clsid:02BF25D5-8C17-4B23-BC80-D3488ABDDC6B\" width=\"$width\" height=\"$dheight\" codebase=\"http://www.apple.com/qtactivex/qtplugin.cab\">";
				$preview_source .= "<param name=\"src\" value=\"".$c["devfilesdocroot"].$filename."\" />";
				$preview_source .= "<param name=\"autoplay\" value=\"false\" />";
				$preview_source .= "<param name=\"controller\" value=\"true\" />";
				$preview_source .= "<embed src=\"".$c["devfilesdocroot"].$filename."\" width=\"$width\" height=\"$dheight\" autoplay=\"false\" controller=\"true\" pluginspage=\"http://www.apple.com/quicktime/download/\" /></object>";

				return $this->draw_standard_preview($preview_source, $width, $height, $copyright);

				break;

			default:
				return "unknown filetype !";
		}
		

	}

	/**
  	 * This function finally draws the html-code for a preview of the media object.
  	 * @preview_source 	string  HTML-Code to be included in the standard-code
	 * @width			integer	width of the media object
	 * @height 			integer	height of the media object
	 * @copyright		string	Copyright-String of the media object.	
  	 * @returns			string	HTML-CODE to be written into the template.
  	 */
	function draw_standard_preview($preview_source, $width, $height, $copyright) {
		// painting preview.
		mt_srand((double)microtime()*1000000);
		$randval = mt_rand();
		$output =  "<table width=\"100%\" cellpadding=\"0\" cellspacing=\"0\" border=\"0\">\n";
		$output .= "<tr>";
		$output .=  "<td class=\"standardlight\">";
		if ($width != 0) $output .= "<b>Width:</b> $width<br>";
		if ($height != 0) $output .= "<b>Height:</b> $height<br>";
		$output .=  "<br><b>Copyright:</b> $copyright</td>";
		$output .=  "<td class=\"standard\">".$preview_source."</td>";
		$output .= "</tr>";
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
  	function draw($param="") {
  		global $c, $db, $splevel,$cds;
 	 	$sql = "SELECT * FROM $this->management_table WHERE $this->pk_name = $this->fkid";
 	 	$query = new query($db, $sql);
 	 	$query->getrow();
 	 	$filename = $query->field("FILENAME");
		$fileparts = explode(".", $filename);
		$suffix = strtolower($fileparts[(count($fileparts)-1)]);

		if (is_object($cds) && ! isset($splevel)) {
		  $splevel = $cds->level;	
		}

 	 	if ($splevel==10) {
 	 	  $im["path"] = $c["livefilesdocroot"].$filename; // splevel=10 is for live-site, 0 for development.
 	 	//  $im["tpath"] = $c["livefilesdocroot"]."t".$filename;
 	 	//  $im["gtpath"] = $c["livefilesdocroot"]."gt".$filename;
 	 	}
 	 	if ($splevel==0) {
  		  $im["path"] = $c["devfilesdocroot"].$filename;
  		//  $im["tpath"] = $c["devfilesdocroot"]."t".$filename;
  		//  $im["gtpath"] = $c["devfilesdocroot"]."gt".$filename;
  		}
  	  		
  		$im["width"] = $query->field("WIDTH");
  		$im["height"] = $query->field("HEIGHT");
   		$im["copyright"] = $query->field("COPYRIGHT");
 
 		switch ($suffix) {

			case "swf":
				if (stristr($param, "ALL")) return $im;
				if ($im["width"] != 0) {
					$tag = "<object classid=\"clsid:D27CDB6E-AE6D-11cf-96B8-444553540000\" codebase=\"http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,29,0\" width=\"".$im["width"]."\" height=\"".$im["height"]."\">\n";
					$tag .= "  <param name=\"movie\" value=\"".$im["path"]."\">\n";
					$tag .= "  <param name=\"quality\" value=\"high\">\n";
					$tag .= "  <embed src=\"".$im["path"]."\" width=\"".$im["width"]."\" height=\"".$im["height"]."\" quality=\"high\" pluginspage=\"http://www.macromedia.com/go/getflashplayer\" type=\"application/x-shockwave-flash\"></embed></object>\n";
				} else {
					$tag = "";
				}
				break;

			case "pdf":
				if (stristr($param, "ALL")) return $im;
				$tag = "<a href=\"".$im["path"]."\" ";
				if (stristr($param, "SELF")) {
					$tag .= "target=\"_self\" ";
				} else {
					$tag .= "target=\"_blank\" ";
				}
				$tag .= "><img src=\"".$c["docroot"]."plugin/media/icons/icon_pdf.gif\" border=0></a>";
				break;

			case "avi":
			case "mp3":
				if (stristr($param, "ALL")) return $im;
				if ($im["width"] != 0) {
					$tag  = "<OBJECT ID=\"player_wm\" BORDER=0 width=".$im["width"]." height=".$im["height"]." classid=\"CLSID:22D6F312-B0F6-11D0-94AB-0080C74C7E95\" ";
					$tag .= "codebase=\"http://activex.microsoft.com/activex/controls/mplayer/en/nsmp2inf.cab#Version=6,4,5,715\" ";
					$tag .= "standby=\"Loading Microsoft® Windows® Media Player components...\" type=\"application/x-oleobject\"> ";	
					$tag .= "<PARAM NAME=\"FileName\" VALUE=\"".$im["path"]."\">";
					$tag .= "<PARAM NAME=\"AutoStart\" VALUE=\"true\">";
					$tag .= "<PARAM NAME=\"ShowControls\" VALUE=\"false\">";
					$tag .= "<PARAM NAME=\"ShowDisplay\" VALUE=\"false\">";
					$tag .= "<PARAM NAME=\"AnimationatStart\" VALUE=\"false\">";
					$tag .= "<EMBED BORDER=0 type=\"application/x-mplayer2\" ";
					$tag .= "pluginspage=\"http://www.microsoft.com/Windows/Downloads/Contents/Products/MediaPlayer/\" ";
					$tag .= "SRC=\"".$im["path"]."\" ";
					$tag .= "name=\"player_wm\" width=".$im["width"]." height=".$im["height"]." AutoStart=true ShowControls=0 ShowDisplay=0 ShowStatusBar=0 animationAtStart=0></EMBED></OBJECT>";
				} else {
					$tag = "";
				}
				break;

			case "rm":
				if (stristr($param, "ALL")) return $im;
				if ($im["width"] != 0) {
					$tag  = "<OBJECT ID=\"player_rm\" CLASSID=\"clsid:CFCDAA03-8BE4-11cf-B84B-0020AFBBCCFA\" HEIGHT=".$im["height"]." WIDTH=".$im["width"].">";
					$tag .= "<PARAM NAME=\"controls\" VALUE=\"ImageWindow\">";
					$tag .= "<PARAM NAME=\"console\" VALUE=\"Clip1\">";
					$tag .= "<PARAM NAME=\"autostart\" VALUE=\"true\">";
					$tag .= "<PARAM NAME=\"src\" VALUE=\"".$im["path"]."\">";
					$tag .= "<EMBED NAME=\"player_rm\" SRC=\"".$im["path"]."\" BORDER=0 autostart=\"true\" type=\"audio/x-pn-realaudio-plugin\" ";
					$tag .= "pluginspage=\"http://www.real.com\" CONSOLE=\"Clip1\" CONTROLS=\"ImageWindow\"  HEIGHT=".$im["height"]." WIDTH=".$im["width"]."></OBJECT>";
				} else {
					$tag = "";
				}
				break;

			case "mov":
				if (stristr($param, "ALL")) return $im;
				if ($im["width"] != 0) {
					$tag  = "<object classid=\"clsid:02BF25D5-8C17-4B23-BC80-D3488ABDDC6B\" width=\"".$im["width"]."\" height=\"".$im["height"]."\" codebase=\"http://www.apple.com/qtactivex/qtplugin.cab\">";
					$tag .= "<param name=\"src\" value=\"".$im["path"]."\" />";
					$tag .= "<param name=\"autoplay\" value=\"true\" />";
					$tag .= "<param name=\"controller\" value=\"false\" />";
					$tag .= "<embed src=\"".$im["path"]."\" width=\"".$im["width"]."\" height=\"".$im["height"]."\" autoplay=\"true\" controller=\"false\" pluginspage=\"http://www.apple.com/quicktime/download/\" /></object>";
				} else {
					$tag = "";
				}
				break;
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
  		$createHandler->addDBAction("INSERT INTO $this->management_table ($this->pk_name, FILENAME, WIDTH, HEIGHT, COPYRIGHT) VALUES ($this->fkid, '', 0,0,'')");
  		$createHandler->proccess("CREATE");
  	}
  
    /**
     * This Function provides all actions for deleting a complete recordset
     * of a plugin. It shoul use the $this->fkid for identifying the record.
     */
  	function deleteRecord() {
		Plugin::deleteRecord();
		// does not need to be changed as long working on one table only!
  	}
  	
  	/**
  	 * Create the sql-code for a version of the selected object
  	 * @param integer ID of new Version.
  	 * @returns string SQL Code for new Version.
  	 */
  	function createVersion($newid, $copy=false) {
  	 	// query for content
  	 	global $db, $c;
  	    	$destinationPath = $c["livefilespath"];
		if ($copy) 
		  $destinationPath = $c["devfilespath"];	
		$querySQL = "SELECT * FROM $this->management_table WHERE $this->pk_name = $this->fkid";
  	 	$query = new query($db, $querySQL);
  	 	$query->getrow();
  	 	$width = addslashes($query->field("WIDTH"));
  	 	$height = addslashes($query->field("HEIGHT"));

  	 	$copyright = addslashes($query->field("COPYRIGHT"));
  	 	$filename = $query->field("FILENAME");
  	 	$query->free();
  	 	// copy image to new version
  	 	$fileparts = explode(".", $filename);
		$suffix = strtolower($fileparts[(count($fileparts)-1)]);
  	 	$newfile = $newid.".".$suffix;
  	    if (!$copy) {	
  		  if (file_exists($destinationPath.$newfile) && $filename != "") unlink($destinationPath.$newfile);
  	    }	
		if ($suffix != "") {
		  copy($c["devfilespath"].$filename, $destinationPath.$newfile);
		}
  	 	
  	 	$sql = "INSERT INTO $this->management_table ($this->pk_name, FILENAME, WIDTH, HEIGHT, COPYRIGHT) VALUES ($newid, '$newfile',  $width, $height, '$copyright')";
   	 	return $sql;
   	 }
  	
    function copyRecord($newid) {
	  return $this->createVersion($newid, true);
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
  			$this->name = "Media";
  			// A short description, what the Plugin is for.
  			$this->description = "Media Files. Allowed formats are GIF, JPEG, PNG and SWF (Flash Films).";
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
			$this->metaInstallHandler->addDBAction("INSERT INTO meta_template_items (MTI_ID, MT_ID, NAME, POSITION, MTYPE_ID) VALUES($guid, $mtid, 'Image Description', 1, "._TEXTAREA.")"); 
			
			/**** end adding META-Data ****/
			// /del1
  				
  			// SQL for creating the tables in the database. Do not call, if you do not need any tables in the database 
  			$this->installHandler->addDBAction("CREATE TABLE $this->management_table (FKID bigint(20) NOT NULL default '0', FILENAME varchar(32) default NULL, WIDTH smallint(6) default NULL, HEIGHT smallint(6) default NULL, COPYRIGHT varchar(64) default NULL, PRIMARY KEY  (FKID), UNIQUE KEY FKID (FKID)) TYPE=MyISAM;"); 
  	
  			// SQL for deleting the tables from the database. 
  			$this->uninstallHandler->addDBAction("DROP TABLE `$this->management_table`");
  			
  			/**** change nothing beyond this point ****/
  			global $source, $classname; // the source path has to be provided by the calling template
  			$modId = nextGUID();
  			$this->installHandler->addDBAction("INSERT INTO modules (MODULE_ID, MODULE_NAME, DESCRIPTION, VERSION, MT_ID, CLASS, SOURCE) VALUES ($modId, '$this->name', '$this->description', $this->version, $mtid, '$classname','$source')"); 
  			
  		}
  	}
  	
  }

?>
