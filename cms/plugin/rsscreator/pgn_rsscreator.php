<?
	/**********************************************************************
	 *	N/X - Web Content Management System
	 *	Copyright 2004 Sven Weih
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

	require_once $c["path"]."plugin/rsscreator/rsscreator.php";
	/**
	 * RSS Creator PlugIn
	 * Version 1.0
	 *
	 * @package Plugins
	 */
	class pgnRSSCreator  extends Plugin {

		
        
		var $pluginType = 3; //CDS-API-Extension
        
		/**
		   * This function is used for drawing the html-code out to the templates.
		   * It just returns the code
		   * @param 		string	Optional parameters for the draw-function. There are none supported.
		   * @return		string	HTML-CODE to be written into the template.
		   */
		function draw($param = "") {
			global $c;
			$rss = new NXRSSCreator();			

			$rss->useCached();
			$rss->title = "Made with N/X"; 
			$rss->description = "set this->title and this->description"; 
			$rss->link = $c["host"].$c["livedocroot"];
			$rss->syndicationURL = $c["livedocroot"].$_SERVER["PHP_SELF"]; 
			return $rss;			
			
		}
	

		/**
		   * Specifies information for installation and deinstallation of the plugin.
		   */
		function registration() {
			global $auth;	
			if ($auth->checkPermission("ADMINISTRATOR")) {
				Plugin::registration();
				$this->name = "RSSCreator";
				$this->description = "CDS-API-Extension for creating RSS-Feeds";
				$this->version = 1;
				$mtid = nextGUID(); // getting next GUID.
				global $source, $classname; // the source path has to be provided by the calling template
				$modId = nextGUID();
				$this->installHandler->addDBAction("INSERT INTO modules (MODULE_ID, MODULE_NAME, DESCRIPTION, VERSION, MT_ID, CLASS, SOURCE, MODULE_TYPE_ID) VALUES ($modId, '$this->name', '$this->description', $this->version, $mtid, '$classname', '$source', 3)");
			}
		}
	}
	
	class NXRSSCreator extends UniversalFeedCreator {
		
		/**
		 * Add ClusterFeedcontent
		 * @param array with clnids
		 * @param string $titlename
		 * @param string $description
		 * @param string $baselink
		 */
		function addArticles($clnids, $titlename, $descriptionname, $baselink="") {
		  global $cds, $c;
		  foreach( $clnids as $clnid) {
		  	$cl = $cds->cluster->getById($clnid);		  		     	        
	     	        $item = new FeedItem(); 
    			$item->title = $cl->content->get($titlename);
    			$item->link = $c["host"].$cds->channel->getLink($clnid);    			
    			$item->description = $cl->content->get($descriptionname);
    			$item->date = $cds->channel->getArticleDate($clnid, '%a, %d %b %Y %H:%i:%s +00:00');		    			    			
    			$item->author = ""; 
    			$this->addItem($item); 
		  } 	
		}
		 
	}
		

?>