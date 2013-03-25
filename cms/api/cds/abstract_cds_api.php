<?
/**********************************************************************
 *	N/X - Web Content Management System
 *	Copyright 2002 Sven Weih and Fabian Koenig
 *
 *	This file is part of N/X.
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
 
 define("_LIVE", "10");
 define("_DEV", "0");
 $clusterCache = array();
 $logger = "";
 
  /**
   * Base class for the oo CDS-API
   */
 class AbstractCDSApi {
 	
	// general information
	var $variation = 0;
	var $stdVariation = 0;
	var $level=0; // 0 = Dev < staged < Live =10
	var $pageClusterId = 0;
	var $pageClusterNodeId = 0;	
	var $is_development = false;	
 	var $docroot = "";
 	var $path = "";
 	
	var $content = null;
 	var $tools = null;
	var $messages = null;
	var $cluster = null;
	var $meta = null;
    var $channel = null;
    var $logTo = "";
    var $langTag;
		
 	/**
 	 * Constructor for creating the CDS API interface
 	 * @param boolean Flag, if the CDS should get content from live-server or not
 	 * @param integer ID of the corresponding Cluster-Node
 	 * @param integer ID of the current Variation.
 	 */
	function AbstractCDSApi($is_development, $clusterNodeId, $variation) {
 		global $c;	
		$this->is_development = $is_development;
		if (! $is_development) {
			$this->level = _LIVE;
			$this->docroot = $c["livedocroot"];
			$this->path = $c["livepath"];
		} else {
			$this->docroot = $c["devdocroot"];	
			$this->path = $c["devpath"];
		}
 		
 	
 		// initialize toolkits
	    	$this->tools = new Tools($this); // static functions for help
		$this->management = new Management($this);	
		$this->messages = new Messages($this);
		
		// set these values always before anything else in the constructor!
		
		$this->variation = $variation;
		$this->stdVariation = $c["stdvariation"];	
		if ($this->variation == "0" || $this->variation == "")
		    $this->variation = $this->stdVariation;

		$this->langTag = strtoupper(getDBCell("variations", "SHORTTEXT", "VARIATION_ID=".$this->variation));		    
		// Initialize Management
		$this->pageClusterNodeId = $clusterNodeId;
		if ($this->pageClusterNodeId != "") {
		  $this->pageClusterId = getDBCell("cluster_variations", "CLID", "CLNID = $clusterNodeId AND VARIATION_ID = ".$this->variation);
		}
		
		// Initialize Content
		$this->content = new Content($this);
		$this->cluster = new Clusters($this);
		$this->meta = new Meta($this);
        $this->channel = new Channel($this);
 	}
 	
 	/**
 	 * Set the variation with the short tag
 	 * @param string Shorttag of the variation
 	 */
 	 function setVariation($shortTag) {
	 	$id = getDBCell("variations", "VARIATION_ID", "UPPER(SHORTTEXT) = UPPER('$shortTag')");
 	 	if ($id != "") $this->variation = $id;
 	 }
 	
 	/**
 	 * Echoes a dump with the configuration of the object
 	 */
 	function dump() {
 		echo "CDSApi-Dump:<br>";
 		echo "PageID: ".$this->pageId."<br>";
 		echo "Variation: ".$this->variation."<br>";
 		echo "Level: ".$this->level."<br>";
 		echo "Is-Development: ".$this->is_development."<br>";
 		echo "Cluster-Node:".$this->pageClusterNodeId."<br>";
 		echo "Cluster: ".$this->pageClusterId."<br>";		
  	}	
 }
 
 
 /**
  * Logs a CDS-Error and writes it to the place which is setup.
  *
  * @param unknown_type $txt
  */
 function log_error($txt) {
   global $cds, $logger;	
   if ($cds->logTo =="" && $cds->level == 0) {
   	  echo $txt;   	
   }
   
   if ($cds->logTo == "screen") {
   	 echo $txt;
   }
   
   if ($cds->logTo == "logger") {
   	$logger.=$txt."\n";
   }
 	
 }
 
 
?>