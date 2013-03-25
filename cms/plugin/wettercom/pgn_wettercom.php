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
	 * WetterCom PlugIn
	 * Version 1.0
	 *
	 * @package Plugins
	 */
	class pgnWetterCom extends Plugin {

		
        
		var $pluginType = 3; //CDS-API-Extension
        
		/**
		   * This function is used for drawing the html-code out to the templates.
		   * It just returns the code
		   * @param 		string	Optional parameters for the draw-function. There are none supported.
		   * @return		string	HTML-CODE to be written into the template.
		   */
		function draw($param = "") {
			return new CDS_WetterCom();
		}
	

		/**
		   * Specifies information for installation and deinstallation of the plugin.
		   */
		function registration() {
			global $auth;	
			if ($auth->checkPermission("ADMINISTRATOR")) {
				Plugin::registration();
				$this->name = "WetterCom";
				$this->description = "CDS-API-Extension for including weather forecast.";
				$this->version = 1;
				$mtid = nextGUID(); // getting next GUID.
				global $source, $classname; // the source path has to be provided by the calling template
				$modId = nextGUID();
				$this->installHandler->addDBAction("INSERT INTO modules (MODULE_ID, MODULE_NAME, DESCRIPTION, VERSION, MT_ID, CLASS, SOURCE, MODULE_TYPE_ID) VALUES ($modId, '$this->name', '$this->description', $this->version, $mtid, '$classname', '$source', 3)");
			}
		}
	}
	
	/**
	 * Paints weather buttons of germany from wetter.com
	 */
	class CDS_WetterCom {
	
		/**
		 * Standard constructor
		 */
		 function CDS_WetterCom() {}
		 
		 
		 /**
		  * Draws current weather of a town.
		  * @param string ZIP of the town (PLZ)
		  * @param string design of the button (Numeric 0-9)
		  */
		 function drawCurrentWeather($zip, $design, $region="DEPLZ") {
		 	return $this->_draw('C', $zip, $this->_getDesign($design), $region);
		 }
		 
		 /**
		  * Draws forecast weather of a town.
		  * @param string ZIP of the town (PLZ)
		  * @param string design of the button (Numeric 0-9)
		  */
		 function drawForecastWeather($zip, $design, $region="DEPLZ") {
		   return $this->_draw('F', $zip, $this->_getDesign($design), $region);	
		 }
		 
		 /**
		  * Translate numeric-values into wetter.com-design
		  * @param integer Number (0-9) of design
		  */
		 function _getDesign($design) {
		   $designs=array("1", "1b", "1c", "1d", "2", "2b", "2c", "3", "4", "5");
		   return $designs[$design];	
		 }
		 
		 /**
		  * Draw the html of the button
		  * @param string C or F, depending on Current or Forecast
		  * @param string Zip
		  * @param string Design of _getDesign
		  * @param string Region, e.g. DEPLZ
		  */
		 function _draw($when, $zip, $design, $region) {
		    return '<a href="http://www.wetter.com/home/extern/ex_search.php?ms=1&ss=1&sss=2&search='.$zip.'"><img src="http://www.wetter.com/home/woys/woys.php?,'.$when.','.$design.','.$region.','.$zip.'" border="0" alt=""></a>';	
		 }
		
		
	}

?>