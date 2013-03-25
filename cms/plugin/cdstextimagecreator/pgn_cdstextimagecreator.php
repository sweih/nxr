<?
	/**********************************************************************
	 *	N/X - Web Content Management System
	 *	Copyright 2002-2004 Sven Weih
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
	 * CDSTextImageCreator PlugIn
	 * Version 1.0
	 *
	 * @package Plugins
	 */
	class pgnCDSTextImageCreator extends Plugin {

		        
		var $pluginType = 3; //CDS-API-Extension
        
		/**
		   * This function is used for drawing the html-code out to the templates.
		   * It just returns the code
		   * @param 		string	Optional parameters for the draw-function. There are none supported.
		   * @return		string	HTML-CODE to be written into the template.
		   */
		function draw($param = "") {
			return new CDSTextImageCreator_API();
		}
	

		/**
		   * Specifies information for installation and deinstallation of the plugin.
		   */
		function registration() {
			global $auth;	
			if ($auth->checkPermission("ADMINISTRATOR")) {
				Plugin::registration();
				$this->name = "CDSTextImageCreator";
				$this->description = "Class-3-Plugin for extending the CDS with a function for painting text-images on the fly.";
				$this->version = 1;
				$mtid = nextGUID(); // getting next GUID.
				global $source, $classname; // the source path has to be provided by the calling template
				$modId = nextGUID();
				$this->installHandler->addDBAction("INSERT INTO modules (MODULE_ID, MODULE_NAME, DESCRIPTION, VERSION, MT_ID, CLASS, SOURCE, MODULE_TYPE_ID) VALUES ($modId, '$this->name', '$this->description', $this->version, $mtid, '$classname', '$source', 3)");
			}
		}
	}
	
	/**
	 * Class for painting Text-Images
	 */
	class CDSTextImageCreator_API{
		
	  var $width=200;
	  var $height=30;
	  var $bgcolor="#ffffff";
	  var $font = "Arial";
	  var $fontsize = "28";
	  var $gravity = "West";
	  var $textcolor = "#000000";
	  var $fontstyle = "Normal";
	  var $prefix = "";
	  var $debug = false;
	  
	  /**
	   * Standard constructor
	   */
	  function CDSTextImageCreator_API() { }
		
	  /**
	   * Create a unique Image Name out of the text and check if the file exists
	   * @param string text out of which a unique filename is to be created.
	   */
	  function getImageName($text) {	    
	  	global $cds;
	  	$name = $this->prefix.md5($text).".png";
	  	if ($this->debug) return $name;
	  	if (file_exists($cds->path."images/".$name)) {
	  	  return "";
	  	} else {
	  	  return $name;	
	  	}
	  }
	  
	  
	  /**
	   * Create a unique Image Name out of the text.
	   * @param string text out of which a unique filename is to be created.
	   */
	  function getImageName2($text) {	    	  	
	  	return $this->prefix.md5($text).".png";	  	
	  }
	  
	  /**
	   * Paints a text as image.
	   * All configuration variables must be set before!
	   * @param string TExt to paint
	   * @param integer Offset on X-Axis
	   * @param integer Offset on Y-Axis
	   */
	  function paint($text, $x=0, $y=0) {
	  		global $cds;
	  		$name = $this->getImageName($text);
	  		if ($name != "") {
	  			$image = new NXImageApi("", $cds->path."images/".$name, false);
				$image->createCanvas($this->width, $this->height, $this->bgcolor);
				$image->font = $this->font;
  				$image->fontsize = $this->fontsize;
  				$image->gravity = $this->gravity;  			
  				$image->fillcolor = $this->textcolor;
  				$image->fontstyle = $this->fontstyle;
  				$image->drawText($text, $x, $y);  			
  				$image->save();	  				
	  		} 
			return '<img src="'.$cds->docroot.'images/'.$this->getImageName2($text).'" width="'.$this->width.'" height="'.$this->height.'" alt="'.$text.'" border="0">';
	  }
		
	}
?>