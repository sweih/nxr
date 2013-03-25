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

		require_once($c["path"]."api/cms/cluster.php");
		require_once($c["path"]."api/tools/copy.php");
		require_once($c["path"]."api/cms/cluster_template.php");
		require_once($c["path"]."api/cms/launch.php");
		require_once($c["path"]."api/cms/meta.php");
		require_once($c["path"]."api/cms/plugin.php");
		require_once($c["path"]."api/cms/channel.php");
		require_once($c["path"]."api/userinterface/processing/actionhandler.php");
		require_once($c["path"]."api/xml/xpath.class.php");
		require_once($c["path"]."api/common/registry.php");


	/**
	 * CMS PlugIn
	 * Version 1.0
	 *
	 * @package Plugins
	 */
	class pgnCMS extends Plugin {

		
        
		var $pluginType = 3; //CDS-API-Extension
        
		/**
		   * This function is used for drawing the html-code out to the templates.
		   * It just returns the code
		   * @param 		string	Optional parameters for the draw-function. There are none supported.
		   * @return		string	HTML-CODE to be written into the template.
		   */
		function draw($param = "") {
			return new CDS_CMS();
		}
	

		/**
		   * Specifies information for installation and deinstallation of the plugin.
		   */
		function registration() {
			global $auth;	
			if ($auth->checkPermission("ADMINISTRATOR")) {
				Plugin::registration();
				$this->name = "CMS";
				$this->description = "CDS-API-Extension for creating, launching and editing Clusters.";
				$this->version = 1;
				$mtid = nextGUID(); // getting next GUID.
				global $source, $classname; // the source path has to be provided by the calling template
				$modId = nextGUID();
				$this->installHandler->addDBAction("INSERT INTO modules (MODULE_ID, MODULE_NAME, DESCRIPTION, VERSION, MT_ID, CLASS, SOURCE, MODULE_TYPE_ID) VALUES ($modId, '$this->name', '$this->description', $this->version, $mtid, '$classname', '$source', 3)");
			}
		}
	}
	
	/**
	 * Supplies CDS with functions for creating, launching and editing clusters.
	 */
	class CDS_CMS {
		

			
		/**
		 * Standard constructor
		 */
		function CDS_CMS() {}
		 
		 
		/**
		 * Creates a cluster and a cluster-node for the given clt. returns the clnid.
	 	 * Can only import dynamic content and dynamic clusters (GUIDs of Cluster_node!).
	 	 * @param string name of the cluster
	 	 * @param string GUID of Cluster template
	 	 * @param string ID od Variation to use
	 	 * @param string array with data to post to plugins, like array("Headline" => "Test Headline", ...)
	 	 * @param string Username to use for create
		 */
		function createCluster($name, $clt, $variationId, $data, $createUser="Internal User") {
			$name = parseSQL($name);
			return createCluster2($name, $clt, $variationId, $data, $createUser);		
		}
		
		/**
		 * Launch a Cluster. The ClusterTemplate must! be launched before!
		 * @param integer CLNID to launch
		 * @param integer ID of the variation to launch. 
		 * @returns integer Translated ID after launch
		 */
		function launchCluster($in, $variation=null) {			
			global $cds, $acl;
			$acl = array();
			if ($variation==null) $variation = $cds->variation;
			return launchCluster2($in, 10, $variation);
		}
		
		/**
		 * load a key from the N/X-Registry
		 * @param string Key in format: folder1/folder2/keyname
		 * @returns string saved value of the key
		 */
		function loadRegistryKey($key) {
			return reg_load($key);
		}
		
		/**
		 * saves a key-value-pair to the N/X-Registry
		 * @param string Key in format: folder1/folder2/keyname
		 * @param string Value
		 */
		function saveRegistryKey($key, $value) {
			reg_save($key, $value);
		}
		
		
		/**
		 * returns an array of CLNIDs assigned to a CLT_ID
		 * @param integer CLT_ID of the Cluster-Template to retrieve ClusterNodes for
		 * @param string Order by LAST_CHANGED|CREATED_AT|LAUNCHED_AT
		 * @param integer Variation-ID of the Cluster to retrieve
		 * @returns array Array of CLNIDs
		 */
		function getClusterField($clt, $orderBy="LAST_CHANGED",$variationId=null) {
			global $db, $cds;
			if ($variationId == null) $variationId = $cds->variation;
			if (strtoupper($orderBy) == "NAME") {
				$order = "cn.NAME ASC";
			} else {
				$order = "cv.".$orderBy." DESC";
			}
			$sql = "SELECT cn.CLNID FROM cluster_node cn, cluster_variations cv WHERE cn.CLT_ID = $clt AND cn.CLNID = cv.CLNID AND cn.DELETED = '0' AND cv.VARIATION_ID = $variationId ORDER BY ".$order;
			$query = new query($db, $sql);
			$array = array();
			while ($query->getrow()) {
				array_push ($array, $query->field("CLNID"));
			}
			return $array;
		}
		
	}

?>