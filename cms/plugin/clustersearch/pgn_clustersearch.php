<?
	/**********************************************************************
	 *	N/X - Web Content Management System
	 *	Copyright 2004 by Sven Weih, sven@nxsystems.org
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
	 * ClusterSearch PlugIn
	 * Version 1.0
	 *
	 * @package Plugins
	 */
	class pgnClustersearch extends Plugin {
		
		var $pluginType = 3; //CDS-API-Extension
        
		/**
		   * This function is used for drawing the html-code out to the templates.
		   * It just returns the code
		   * @param 		string	Optional parameters for the draw-function. There are none supported.
		   * @return		string	HTML-CODE to be written into the template.
		   */
		function draw($param = "") {
			return new CDS_ClusterSearch();
		}
	

		/**
		   * Specifies information for installation and deinstallation of the plugin.
		   */
		function registration() {
			global $auth;	
			if ($auth->checkPermission("ADMINISTRATOR")) {
				Plugin::registration();
				$this->name = "ClusterSearch";
				$this->description = "CDS-API-Extension for searching content in clusters.";
				$this->version = 1;
				$mtid = nextGUID(); // getting next GUID.
				global $source, $classname; // the source path has to be provided by the calling template
				$modId = nextGUID();
				$this->installHandler->addDBAction("INSERT INTO modules (MODULE_ID, MODULE_NAME, DESCRIPTION, VERSION, MT_ID, CLASS, SOURCE, MODULE_TYPE_ID) VALUES ($modId, '$this->name', '$this->description', $this->version, $mtid, '$classname', '$source', 3)");
			}
		}
	}
	

	/**
 	 * Class for searching content in clusters
	 */
	class CDS_ClusterSearch {
	  var $cltId;
          var $cltiId;
          var $index = null;
          var $pluginTableName;
          var $filterTables = null;
          var $filter="";
          
	  /** 
	   * Standard constructor
	   */
	  function CDS_ClusterSearch() {
	  	$this->filterTables = array();
  	  }
          
          /**
           * Set the cluster-template for search
           * @param integer ID of the cluster template the search is based on
           */
           function setCLT($cltId) {
             $this->cltId = $cltId;
           }
           
           /**
            * Adds a channel-category filter to the query.
            * @param integer ID of the channel-category
            */
           function setChannelCategory($categoryId) {
             $categoryID = parseSQL($categoryId);
             $this->filterTables[] = "channel_articles ca";
             $this->filter.=" AND ca.ARTICLE_ID = cv.CLNID AND ca.CH_CAT_ID = $categoryId";
           }
                   
           /**
            * Set the fieldname, the search is to be performed on
            * You can search on dynamic content fields of type label or text only!!
            * if you try something else you will get errors.
            * @param string fieldname
            */
            function setSearchField($name) {              
              $this->pluginTableName = "";
              $this->cltiId = getDBCell("cluster_template_items", "CLTI_ID", "CLT_ID = ".$this->cltId." AND UPPER(NAME) = '".strtoupper($name)."' AND CLTITYPE_ID=2");
              $moduleId = getDBCell("cluster_template_items", "FKID", "CLT_ID = ".$this->cltId." AND UPPER(NAME) = '".strtoupper($name)."' AND CLTITYPE_ID=2");
              $modulename = getDBCell("modules", "MODULE_NAME", "MODULE_ID = ".$moduleId);
              if ($modulename == "Text") $this->pluginTableName = "pgn_text";
              if ($modulename == "Label") $this->pluginTableName = "pgn_label";              
              if ($this->pluginTableName != "") 
                $this->buildIndex();
            }
            
            /**
             * Search the cluster for a keyword
             * @param string Searchphrase
             */
             function search($searchphrase) {
                global $db;
                $result = array();
                $searchphrase = strtoupper(parseSQL($searchphrase));                
                if ($this->pluginTableName != "" && is_array($this->index)) {
                  if (count($this->index) > 0 ) {
                     $sql = "SELECT FKID FROM ".$this->pluginTableName." $columns WHERE UPPER(CONTENT) LIKE '%" . $searchphrase . "%' AND FKID IN (".implode(",", $this->index).")";   
                     $query = new query($db, $sql);
                     while ($query->getrow()) {
                        $result[] = $query->field("FKID");
                      }
                      $result = createCLNIDsFromCLCIDs($result);
                    }
                }
                return $result;
             }
             
             /**
              * Create an array with all FKIDs in clusters, where to search on.
              */
             function buildIndex() {
               global $db, $cds;
               $result = array();
               if ($this->cltiId != "") {
                 $columns = implode(',', array_unique($this->filterTables));
                 if (strlen($columns) > 0) $columns = ",".$columns;                     
                 $sql = "Select cc.CLCID FROM cluster_content cc, cluster_variations cv $columns WHERE cv.CLID = cc.CLID AND cc.CLTI_ID = $this->cltiId AND cv.VARIATION_ID = $cds->variation ".$this->filter;                                
                 $query = new query($db, $sql);
                 while($query->getrow()) {
                   $result[] = $query->field("CLCID");
                 }
                 $query->free();
                 $this->index = $result;
               }
             }

	}	


