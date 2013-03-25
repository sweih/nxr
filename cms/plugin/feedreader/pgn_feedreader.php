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
	 * FeedReader
	 *
	 * @package Plugins
	 */
	class pgnFeedReader extends Plugin {

		// Name of the Management's Table Primary Key
		var $pk_name = "FKID";

		// Name of the Plugin's Management Table. All tables should start with pgn_
		var $management_table = "pgn_feeds";
		var $isSingleConfig = true;
		
		/**
		  * Creates the input fields for editing text
		  * @param integer &$form link to the form the input-fields are to be created in 
		  */
		function edit(&$form) {
			global $lang, $sid, $c;

			// add button for external editor.
			$cond = "FKID = $this->fkid";
			$form->add(new TextInput($lang->get("feed_url", "Feed URL"), "pgn_feeds", "FEEDURL", $cond, "type:text,width:350,size:255", ""));
			$form->add(new CheckboxTxtInput($lang->get("aggregate", "Aggregate news in channel"), "pgn_feeds", "SHOWLINKS", $cond));
			
			$chcat = array();
            $channels = createNameValueArrayEx("channels", "NAME", "CHID", "1 ORDER BY NAME ASC");
            if (!is_array($channels)) $channels=array();               
            foreach ($channels as $channel){               
              $categories = createNameValueArrayEx("channel_categories", "NAME", "CH_CAT_ID", "CHID = ".$channel[1]." ORDER BY NAME");
              if (count($categories)>0) {
                foreach ($categories as $category) {
                  $chcat[] = array($channel[0]." - ".$category[0], $category[1]);
                }
              }
            }
            $form->add(new SelectOneInputFixed($lang->get("channel"), "pgn_feeds", "CHANNEL_CAT", $chcat, $cond));
            $form->add(new SitepageSelector($lang->get("cop", "Channel Overview Page"), "pgn_feeds", 'CHANNEL_OVERVIEW_PAGE', $cond));
		}
		

		/** 
		  * Used, for painting a preview of the content in the cms. Note, that the data
		  * is to be drawn in a table cell. Therefore you may design any html output, but
		  * you must return it as return value!
		  */
		function preview() {
			global $lang;
			$content = "&lt;".$lang->get("no_preview", "No preview available.")."&gt;";			
			return $content;
		}

		/**
		   * This function is used for drawing the html-code out to the templates.
		   * It just returns the code
		   * @param 		string	Optional parameters for the draw-function. There are none supported.
		   * @return		string	HTML-CODE to be written into the template.
		   */
		function draw($param = "") {
		  global $cds, $c;
		  $url = getDBCell('pgn_feeds', 'FEEDURL', 'FKID='.$this->fkid);		  
		  include $c["path"].'plugin/feedreader/index.php';
		  return $out;
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
				$this->name = "FeedReader";
				// A short description, what the Plugin is for.
				$this->description = "ATOM and RSS FeedReader.";
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
				$this->installHandler->addDBAction("CREATE TABLE `pgn_feeds` (`FKID` BIGINT NOT NULL ,`FEEDURL` VARCHAR( 256 ) NULL ,`SHOWLINKS` TINYINT( 1 ) NOT NULL DEFAULT '0',`CHANNEL_CAT` BIGINT NOT NULL DEFAULT '0',`CHANNEL_TEMPLATE` BIGINT NOT NULL DEFAULT '0',`CHANNEL_OVERVIEW_PAGE` BIGINT NOT NULL DEFAULT '0',PRIMARY KEY ( `FKID` ) ) ENGINE = MYISAM ;");

				// SQL for deleting the tables from the database. 
				$this->uninstallHandler->addDBAction("DROP TABLE `pgn_feeds`");

				/**** change nothing beyond this point ****/
				global $source, $classname; // the source path has to be provided by the calling template
				$modId = nextGUID();
				$this->installHandler->addDBAction("INSERT INTO modules (MODULE_ID, MODULE_NAME, DESCRIPTION, VERSION, MT_ID, CLASS, SOURCE) VALUES ($modId, '$this->name', '$this->description', $this->version, $mtid, '$classname', '$source')");
			}
		}
	}
?>