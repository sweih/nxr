<?
	/**********************************************************************
	 *	N/X - Web Content Management System
	 *	Copyright 2006 Sven Weih
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
	 * MostWantedPages PlugIn
	 * Version 1.0
	 *
	 * @package Plugins
	 */
	class pgnTagCloud extends Plugin {

		// Name of the Management's Table Primary Key		
		
		var $pluginType=5; // Mashup
		/**
		   *This functions returns a reference to the GoogleMaps API
		   * It just returns the code
		   * @param 		string	Optional parameters for the draw-function. There are none supported.
		   * @return		string	HTML-CODE to be written into the template.
		   */
		function draw($param = "") {
			global $cds, $c;			
			$api = new MostWanted();
			$out = $api->drawBand(30,6,8,32);
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
				$this->name = "TagCloud";
				// A short description, what the Plugin is for.
				$this->description = "Retrieve and display lists with the most wanted pages.";
				// Version of the plugin. Use integer numbers only. Is important for future releases.
				$this->version = 1;

				/**** do not change from this point ****/
				$mtid = nextGUID(); // getting next GUID.
				//del1

				// SQL for creating the tables in the database. Do not call, if you do not need any tables in the database 
				
				// SQL for deleting the tables from the database. 
				/**** change nothing beyond this point ****/
				global $source, $classname; // the source path has to be provided by the calling template
				$modId = nextGUID();				
				$this->installHandler->addDBAction("INSERT INTO modules (MODULE_ID, MODULE_NAME, DESCRIPTION, VERSION, MT_ID, CLASS, SOURCE, MODULE_TYPE_ID) VALUES ($modId, '$this->name', '$this->description', $this->version, $mtid, '$classname', '$source', 5)");				
			}
		}
	}
	
	/**
	 * Retrieve the most wanted pages of your website.
	 */
	class MostWanted {
		
		function MostWanted() {}
		
		/**
		 * Query for the top XXX pages of your website
		 * @param integer $topCount  Number of pages that will be returned
		 */
		function query($topCount=30) {			
			global $db;
			$result = array();
			$sql = "SELECT count( a.document_id ) AS nmu, d.String FROM pot_documents d, pot_accesslog a, sitepage p WHERE a.document_id = d.data_id AND d.string = p.SPID AND p.DELETED=0 AND p.VERSION=10 GROUP BY d.String ORDER BY nmu DESC LIMIT 0,".$topCount;
			$query = new query($db, $sql);
			while ($query->getrow()) {
			   $page=array("spid" => $query->field("String"), "count" => $query->field("nmu"));
			   $result[] = $page;	
			}
			$query->free();
			return $result;
		}
		
		/**
		 * Draw the most wanted links alphabetically sorted with the top links biggest.
		 * @param integer $topCount  Number of pages that will be returned
		 * @param integer $itemsPerRow   Number of items in one row
		 * @param integer $minsize Minumum font size in pixel
     * @param integer $maxsize Maximum font size in pixel
     */
		function drawBand($topCount=30, $itemsPerRow=10, $minsize=11,$maxsize=48) {
			global $cds;
			global $db;
			$max=0;
			$min=10000000000;
			$data = array();			
			$sql = "SELECT count( a.document_id ) AS nmu, d.String, n.NAME FROM pot_documents d, pot_accesslog a, sitepage p, sitepage_names n WHERE a.document_id = d.data_id AND d.string = p.SPID AND p.DELETED=0 AND p.VERSION=10 AND p.SPID=n.SPID AND n.VARIATION_ID=$cds->variation GROUP BY d.String, n.NAME ORDER BY n.NAME ASC LIMIT 0,".$topCount;					
			$query = new query($db, $sql);
			while ($query->getrow()) {
			   $menu = new Menu(null, $query->field("String"), $cds->variation, $cds->level );			   
			   $page = array($menu->getLink(), $query->field("nmu"), $query->field("NAME"));
			   $max = max($max, $query->field("nmu"));
			   $min = min($min, $query->field("nmu"));
			   $data[] = $page;	
			}
			$query->free();
			$out = "";
			
			for ($i=0; $i < count($data); $i++) {
				$size = round((($data[$i][1] - $min) / (($max-$min)+0.1)) * ($maxsize-$minsize) + $minsize);
				if ($size > $maxsize) $size = $maxsize;
				if ($size < $minsize) $size = $minsize;
				if (($i % $itemsPerRow) == 0) $out.= '<br/>';				
				$out.='<a href="'.$data[$i][0].'" style="font-size:'.$size.'px;">'.$data[$i][2].'</a>&nbsp;&nbsp;';
			}			
			return $out;			
		}
		
	}
?>