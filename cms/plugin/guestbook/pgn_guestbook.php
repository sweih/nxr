<?
	/**********************************************************************
	 *	N/X - Web Content Management System
	 *	Copyright 2004 Fabian Koenig, fabian@nxsystems.org
	 *						Sven Weih, sven@nxsystems.org
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
	 * CMS PlugIn
	 * Version 1.0
	 *
	 * @package Plugins
	 */
	class pgnGuestbook extends Plugin {
		
		var $pluginType = 3; //CDS-API-Extension
        
		/**
		   * This function is used for drawing the html-code out to the templates.
		   * It just returns the code
		   * @param 		string	Optional parameters for the draw-function. There are none supported.
		   * @return		string	HTML-CODE to be written into the template.
		   */
		function draw($param = "") {
			return new CDS_Guestbook();
		}
	

		/**
		   * Specifies information for installation and deinstallation of the plugin.
		   */
		function registration() {
			global $auth;	
			if ($auth->checkPermission("ADMINISTRATOR")) {
				Plugin::registration();
				$this->name = "Guestbook";
				$this->description = "CDS-API-Extension for realizing a guestbook.";
				$this->version = 1;
				$mtid = nextGUID(); // getting next GUID.
				global $source, $classname; // the source path has to be provided by the calling template
				$modId = nextGUID();
				$this->installHandler->addDBAction("INSERT INTO modules (MODULE_ID, MODULE_NAME, DESCRIPTION, VERSION, MT_ID, CLASS, SOURCE, MODULE_TYPE_ID) VALUES ($modId, '$this->name', '$this->description', $this->version, $mtid, '$classname', '$source', 3)");
				$this->installHandler->addFncAction("_createGuestbookClusterTemplate");
				
				$this->uninstallHandler->addFncAction("_removeGuestbookClusterTemplate");
			}
		}
	}
	
	/**
	 * creates the cluster-template for guestbook-entries
	 */
	function _createGuestbookClusterTemplate() {
		global $c;
		$layout = "";
		$layout = implode('\n', file($c[path]."plugin/guestbook/layout.php"));
		$clt = createClusterTemplate("pgnGuestbook", "automatically created Cluster-Template for Guestbook-Plugin", $layout, $categoryId = 0, $mtId=1, $cltId=null);
		reg_save("PLUGINS/GUESTBOOK/CLT", $clt);
		$label_ID = getDBCell("modules", "MODULE_ID", "UPPER(MODULE_NAME) = 'LABEL'");
		$text_ID = getDBCell("modules", "MODULE_ID", "UPPER(MODULE_NAME) = 'TEXT'");
		createClusterTemplateFigure("Name", $clt, 1, 1, 1, $label_ID, 2);
		createClusterTemplateFigure("Email", $clt, 2, 1, 1, $label_ID, 2);
		createClusterTemplateFigure("Date", $clt, 3, 1, 1, $label_ID, 2);
		createClusterTemplateFigure("Message", $clt, 4, 1, 1, $text_ID, 2);
	}
	
	/**
	 * removes the cluster-template for guestbook-entries and all of its data when uninstalling plugin
	 */
	function _removeGuestbookClusterTemplate() {
		deleteClusterTemplate(reg_load("PLUGINS/GUESTBOOK/CLT"));
	}
	
	
	
	/**
	 * Supplies CDS with functions for creating, launching and editing clusters.
	 */
	class CDS_Guestbook {
		
		var $cms;
		
		/**
		 * Standard constructor
		 */
		function CDS_Guestbook() {
		  global $cds;
		  $this->cms = $cds->plugins->getAPI("CMS");
		}
		 
		
		function createEntry($name, $email, $date, $message) {
			global $cds;			
			$entryName = date("Y.m.d-H:i")."-".$name;
			$clt = $this->cms->loadRegistryKey("PLUGINS/GUESTBOOK/CLT");
			
			$clnid = $this->cms->createCluster($entryName, $clt, $cds->variation, array("Name" => $name, "Email" => $email, "Date" => $date, "Message" => $message));
			$this->cms->launchCluster($clnid);
		}
		
		/**
		 * draws the guestbook with all entries
		 */
		function drawGuestbook() {
			global $cds;			
			$clt = $this->cms->loadRegistryKey("PLUGINS/GUESTBOOK/CLT");
			$clusterField = $this->cms->getClusterField($clt, "CREATED_AT");

			$output = "";
			
			for ($i=0; $i<count($clusterField); $i++) {
				$cluster = $cds->cluster->getById($clusterField[$i]);
				$output .= $cluster->cluster->draw();
				$output .= "\n<hr>\n";
			}			
			return $output;					
		}
		
		/**
		* Draw the plugin
		* @param string Question
		* @param string Name-Text
		* @param string Email-Text
		* @param string message-Text
		* @param string Submitbutton-text
		* @param string Restebutton-text
		* @param string Thankyou-Text
		* @param string backbutton-Text
		*/
		function drawForm($question="Please enter your messages and data here.", $name="Name", $email="Email", $message="Message", $submit="Submit", $reset="Reset form", $thankyou="Thank you for your message!", $back="Back to Guestbook") {
			if ($this->_processForm()) {
				$out = "<form name='guestbook' method='post'>\n";
				$out .= $thankyou;
				$out .= "<br>\n";
				$out .= "<input type='submit' method='post' value='$back'>\n";
				$out .= "</form>\n";
			} else {
				$out = "<form name='guestbook' method='post'>\n";
				$out .= "<table align='center' class='guestbook_table'><tr>\n";
				$out .= "<tr><td class='guestbook_label'>$name</td><td class='guestbook_copy'><input type=text name='pgnguestbookname' size='15' value='' maxlength='50'></td></tr>\n";
				$out .= "<tr><td class='guestbook_label'>$email</td><td class='guestbook_copy'><input type=text name='pgnguestbookemail' size='15' value='' maxlength='50'></td></tr>\n";
				$out .= "<tr><td valign='top' class='guestbook_label'>$message</td><td class='guestbook_copy'><textarea name='pgnguestbookmessage' cols=50 rows=8 wrap=virtual></textarea></td></tr>\n";
				$out .= "<tr><td colspan='2' align='center' class='guestbook_copy'><input type='submit' name='submit' value='$submit'>&nbsp;&nbsp;<input type='reset' value='$reset'>\n";
	
				$out .= "<input type='hidden' name='pgnguestbooksend' value='1'></td></tr>\n";
	
				$out .= "</table>\n";    
				$out .= "</form>\n";
			}
			return $out;
		}	

		
		/**
		 * Store a gutestbook-entry
		 * @param string name
		 * @param string email
		 * @param string date
		 * @param string message of user
		 */
		function saveData($name, $email, $date, $message) {
			if ($name != "" && $email != "" && $date != "" && $message != "") {
				$this->createEntry($name, $email, $date, $message);				
				return true;  
			}
	     		return false;
		}		

		/**
		 * Process the formfields, if set. Takes fields pgnguestbookname, pgnguestbookemail, pgnguestbookdate, pgnguestbookmessage
		 */
		function _processForm() {
			if (value("pgnguestbooksend") == "1") {
				return $this->saveData(value("pgnguestbookname", "NUMERIC"), value("pgnguestbookemail"), date("Y-m-d H:i:s", time()), value("pgnguestbookmessage"));
			}
			return false;
		}		

	}

?>