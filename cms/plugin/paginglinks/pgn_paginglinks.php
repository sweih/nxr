<?
	/**********************************************************************
	 *	N/X - Web Content Management System
	 *	Copyright 2007 Sven Weih, FZI Research Center for Information Technologies
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
	 * Breadcrumb Mashup
	 * Version 1.0
	 *
	 * @package Plugins
	 */
	class pgnPagingLinks extends Plugin {
		
	  var $pluginType = 5;
						

		
		/**
		   * This function is used for drawing the html-code out to the templates.
		   * It just returns the code
		   * @param 		string	Optional parameters for the draw-function. There are none supported.
		   * @return		string	HTML-CODE to be written into the template.
		   */
		function draw($param = "") {			
			global $cds;
			echo '<div style="float:left;">';
			echo $cds->layout->menu->getPrevForwardLinks();
			echo '</div>';
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
				$this->name = "PagingLinks";
				// A short description, what the Plugin is for.
				$this->description = "Add previous page - next page links to the website.";
				// Version of the plugin. Use integer numbers only. Is important for future releases.
				$this->version = 1;

				// Every module can have its own and individual META-Data in NX. The following Handler is
				// for creating a META-Data-Template and for assigning it to the Plugin.
				// IF you do not want to declare an individual META-Scheme, then set $mtid=0 and delete
				// everything between del1 and /del1!


				/**** change nothing beyond this point ****/
				global $source, $classname; // the source path has to be provided by the calling template
				$modId = nextGUID();
				$mtid  = nextGUID();	      
	
				$this->installHandler->addDBAction("INSERT INTO modules (MODULE_ID, MODULE_NAME, DESCRIPTION, VERSION, MT_ID, CLASS, SOURCE, MODULE_TYPE_ID) VALUES ($modId, '$this->name', '$this->description', $this->version, $mtid, '$classname', '$source', $this->pluginType)");
			}
		}
	}
?>