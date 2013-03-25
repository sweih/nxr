<?
	/**********************************************************************
	 *	N/X - Web Content Management System
	 *	Copyright 2004 Sven Weih, sven@nxsystems.org
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
	 * FAQ PlugIn
	 * Version 1.0
	 *
	 * @package Plugins
	 */
	class pgnFAQ extends Plugin {
		
		var $pluginType = 3; //CDS-API-Extension
        
		/**
		   * This function is used for drawing the html-code out to the templates.
		   * It just returns the code
		   * @param 		string	Optional parameters for the draw-function. There are none supported.
		   * @return		string	HTML-CODE to be written into the template.
		   */
		function draw($param = "") {
			return new CDS_FAQ();
		}
	

		/**
		   * Specifies information for installation and deinstallation of the plugin.
		   */
		function registration() {
			global $auth;	
			if ($auth->checkPermission("ADMINISTRATOR")) {
				Plugin::registration();
				$this->name = "FAQ";
				$this->description = "CDS-API-Extension for realizing a FAQ";
				$this->version = 1;
				$mtid = nextGUID(); // getting next GUID.
				global $source, $classname; // the source path has to be provided by the calling template
				$modId = nextGUID();
				$this->installHandler->addDBAction("INSERT INTO modules (MODULE_ID, MODULE_NAME, DESCRIPTION, VERSION, MT_ID, CLASS, SOURCE, MODULE_TYPE_ID) VALUES ($modId, '$this->name', '$this->description', $this->version, $mtid, '$classname', '$source', 3)");
				$this->installHandler->addFncAction("_createFAQClusterTemplate");
				
				$this->uninstallHandler->addFncAction("_removeFAQClusterTemplate");
			}
		}
	}
	
	/**
	 * creates the cluster-template for faq-entries
	 */
	function _createFAQClusterTemplate() {
		global $c;
		$clt = createClusterTemplate("pgnFAQ", "automatically created Cluster-Template for FAQ", "", $categoryId = 0, $mtId=1, $cltId=null);
		reg_save("PLUGINS/FAQ/CLT", $clt);
		$label_ID = getPluginId("Label");
		$text_ID = getPluginId("Text");
		createClusterTemplateFigure("Question", $clt, 1, 1, 1, $label_ID, 2);
		createClusterTemplateFigure("Answer", $clt, 2, 1, 1, $text_ID, 2);		
	}
	
	/**
	 * removes the cluster-template for guestbook-entries and all of its data when uninstalling plugin
	 */
	function _removeFAQClusterTemplate() {
		deleteClusterTemplate(reg_load("PLUGINS/FAQ/CLT"));
	}
	
	
	
	/**
	 * Supplies CDS with functions for creating, launching and editing clusters.
	 */
	class CDS_FAQ {
		
		var $cms;
		var $clt;
		
		/**
		 * Standard constructor
		 */
		function CDS_FAQ() {
		  global $cds;
		  $this->cms = $cds->plugins->getAPI("CMS");
		  $this->clt = $this->cms->loadRegistryKey("PLUGINS/FAQ/CLT");			
		}
		 
		
		/**
		 * Create a new entry to to the FAQ database
		 * @param string Question of the user+
		 */
		function createEntry($question) {
			global $cds;						
			$clnid = $this->cms->createCluster($question, $this->clt, $cds->variation, array("Question" => parseSQL($question)));			
		}
		
		/**
		 * draws the guestbook with all entries
		 */
		function getClusterIds() {						
			return $this->cms->getClusterField($this->clt, "NAME");						
		}		
	}
?>