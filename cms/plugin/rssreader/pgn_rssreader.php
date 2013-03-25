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

	require_once $c["path"]."plugin/rssreader/lastrss.php";
	/**
	 * RSS Feed Reader PlugIn
	 * Version 1.0
	 *
	 * @package Plugins
	 */
	class pgnRSSReader  extends Plugin {

		
        
		var $pluginType = 3; //CDS-API-Extension
        
		/**
		   * This function is used for drawing the html-code out to the templates.
		   * It just returns the code
		   * @param 		string	Optional parameters for the draw-function. There are none supported.
		   * @return		string	HTML-CODE to be written into the template.
		   */
		function draw($param = "") {
			global $c;
			$parser = new NXRSS();
			$parser->cache_dir = $c["dyncachepath"];
			$parser->cache_time = 3600; //1h
			return $parser;
			
		}
	

		/**
		   * Specifies information for installation and deinstallation of the plugin.
		   */
		function registration() {
			global $auth;	
			if ($auth->checkPermission("ADMINISTRATOR")) {
				Plugin::registration();
				$this->name = "RSSReader";
				$this->description = "CDS-API-Extension for reading rss feeds";
				$this->version = 1;
				$mtid = nextGUID(); // getting next GUID.
				global $source, $classname; // the source path has to be provided by the calling template
				$modId = nextGUID();
				$this->installHandler->addDBAction("INSERT INTO modules (MODULE_ID, MODULE_NAME, DESCRIPTION, VERSION, MT_ID, CLASS, SOURCE, MODULE_TYPE_ID) VALUES ($modId, '$this->name', '$this->description', $this->version, $mtid, '$classname', '$source', 3)");
			}
		}
	}
	
	/**
	 * Extended class for parsing RSS
	 */
	class NXRSS extends lastRSS {
	
	 /**
	  * draw a rss feed
	  */
	  function draw($feeds_url, $limit=5, $showTitle=false) {
	    $feed = $this->get($feeds_url);
	    if ($showTitle) {
	      if ($feed[image_url] != '') {
		  echo "<a href=\"$feed[image_link]\"><img src=\"$feed[image_url]\" alt=\"$feed[image_title]\" vspace=\"1\" border=\"0\" /></a><br />\n";
	      }
	      echo "<big><b><a href=\"$feed[link]\">$feed[title]</a></b></big><br />\n";
	      echo "$feed[description]<br />\n";	
	    }
	    echo "<ul>\n";
	    $counter = 0;
	    foreach($feed['items'] as $item) {
		if ($counter < $limit)
		  echo "\t<li><a href=\"$item[link]\" target=\"_blank\">".$item['title']."</a><br />".$item['description']."</li>\n";
		$counter++;
	    }
	    echo "</ul>\n";
	  }	
	}	

?>