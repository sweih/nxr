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
	 * E-MAIL ObfuscatorPlugin
	 * Filters E-Mail-Adresses and replaces them with other chars.
	 * Example for a type-4-Plugin(Outputfilter.) Can be applied in other plugins
	 * with the function applyFilterPlugins($text)
	 * Version 1.0
	 *
	 * @package Plugins
	 */
	class pgnEmailobfuscator extends Plugin {
		
		var $pluginType =4 ; //Output-Text-Filter
        
		/**
		 * Used for filtering texts in type-4-plugins
		 * This filter is applied before the parsers for images etc. run
		 * @param string Text to parse
		 */
		 function parseText($text) {
		   return $text.'parsed';
		 }
		 
		 
		 /**
		  * Mask the mailaddress so that harvestors cannot make use of it.
		  * @param string mailaddress
		  */
		 function getMaskedMailAddress($mailAddress) {
		 	//<!-- Begin user = "yourname"; site = "yoursite.com"; document.write('<a href=\"mailto:' + user + '@' + site + '\">'); document.write('email me'+'</a>'); // End --> 
		 }
		

		/**
	     * Specifies information for installation and deinstallation of the plugin.
		 */
		function registration() {
			global $auth;	
			if ($auth->checkPermission("ADMINISTRATOR")) {
				Plugin::registration();
				$this->name = "E-Mail Obfuscator";
				$this->description = "Filters email-addresses form texts and recodes them to avoid spidering";
				$this->version = 1;
				$mtid = nextGUID(); // getting next GUID.
				global $source, $classname; // the source path has to be provided by the calling template
				$modId = nextGUID();
				$this->installHandler->addDBAction("INSERT INTO modules (MODULE_ID, MODULE_NAME, DESCRIPTION, VERSION, MT_ID, CLASS, SOURCE, MODULE_TYPE_ID) VALUES ($modId, '$this->name', '$this->description', $this->version, $mtid, '$classname', '$source', 4)");
			}
		}
	}

?>