<?
	/**
	 * @package CDS
	 */

	/**********************************************************************
	 *	N/X - Web Content Management System
	 *	Copyright 2003 Sven Weih
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

	 /**
	  * This class is used to get Content in clusters.
	  * Access this class with $cds->content
	  */
	 class Content extends CDSInterface {
		function Content(&$parent) { CDSInterface::CDSInterface($parent);}

		/**
		 * Retrieves the output of a field as defined in Cluster-Template. 
		 * To be used for Items with maximum cardinality of 1 only!!!
		 * @param string name of the field to query the content from.
		 * @param string additional parameters for this plugin. // might be changed to array in future versions
		 * @param integer ID of the Variation to query. Leave Blank or set to zero for Page-Variation. 
		 * @returns string The output of the module.
		 */
		function get($name, $params = null, $variation = 0) {
						
			if ($variation == 0)
				$variation = $this->variation;


			// get the clti..
			$clt = getDBCell("cluster_node", "CLT_ID", "CLNID = $this->pageClusterNodeId");
			$clti = getDBCell("cluster_template_items", "CLTI_ID", "CLT_ID = $clt AND UPPER(NAME) = UPPER('$name')");

			if ($clti == "")
				return "Field not defined!";

			$type = getDBCell("cluster_template_items", "CLTITYPE_ID", "CLTI_ID = $clti");

			if ($type > 2 && $type < 5)
				return "$name is a Cluster, not a Content!";

			if ($type == 1) {        // static content
				$cid = getDBCell("cluster_template_items", "FKID", "CLTI_ID = $clti");

				$plugin = getDBCell("content", "MODULE_ID", "CID = $cid");
				$oid = getDBCell("content_variations", "FK_ID", "CID = $cid AND VARIATION_ID = $variation AND DELETED=0");
			} else if ($type == 2) { // dynamic content
				$plugin = getDBCell("cluster_template_items", "FKID", "CLTI_ID = $clti");

				$oid = getDBCell("cluster_content", "CLCID", "CLID = $this->pageClusterId AND CLTI_ID = $clti AND DELETED=0");
			} else if ($type == 5) {
                $plugin = getDBCell("cluster_template_items", "FKID", "CLTI_ID = $clti");
                $cid = getDBCell("cluster_content", "FKID", "CLID = $this->pageClusterId AND CLTI_ID = $clti AND DELETED=0");
                if ($cid == 0 && $variation <> "" && $variation == 1) {
                   $clid = getDBCell("cluster_variations", "CLID", "CLNID = $this->pageClusterNodeId AND VARIATION_ID = 1");
                   $cid = getDBCell("cluster_content", "FKID", "CLID = $clid AND CLTI_ID = $clti AND DELETED=0");
                 }
                 $oid = getDBCell("content_variations", "FK_ID", "CID = $cid AND VARIATION_ID = $variation AND DELETED=0");			
			}

			if ($oid != "" && $plugin != "") {
				$ref = createPGNRef($plugin, $oid, $clti);

				$content = $ref->draw($params);
				unset ($ref);
			} else
				$content = "";

			if ($content != "")
				return $content;

			// now the content seems to be empty. So we try standard variation.
			if ($variation != $this->parent->stdVariation)
				$content = $this->get($name, $params, $this->parent->stdVariation);

			return $content;
		}
		
		
		
		/**
		 * Retrieves the output of a field as defined in Cluster-Template. 
		 * To be used for Items with maximum cardinality of 1 only!!!
		 * @param string name of the access - key to query the content from.
		 * @param string additional parameters for this plugin. // might be changed to array in future versions
		 * @param integer ID of the Variation to query. Leave Blank or set to zero for Page-Variation. 
		 * @returns string The output of the module.
		 */
		function getByAccessKey($key, $params = null, $variation = 0) {
					
			// set variation.						
			if ($variation==0 )
				$variation = $this->variation;				
				
				// get content id.
				$cid = getDBCell("content", "CID", "UPPER(ACCESSKEY)='".strtoupper($key)."' AND VERSION=".$this->parent->level);
				if ($cid != "") {
					$plugin = getDBCell("content", "MODULE_ID", "UPPER(ACCESSKEY)='".strtoupper($key)."'");
					$oid = getDBCell("content_variations", "FK_ID", "CID = $cid AND VARIATION_ID = $variation AND DELETED=0");

					if ($oid != "" && $plugin != "") {
						$ref = createPGNRef($plugin, $oid, $clti);
						$content = $ref->draw($params);
						unset ($ref);
					} else 
					  $content = "";

					if ($content != "")
					  return $content;

					// now the content seems to be empty. So we try standard variation.
					if ($variation != $this->parent->stdVariation)
					  $content = $this->getByAccessKey($key, $params, $this->parent->stdVariation);
					return $content;			  
					
				} else {
					log_error ("Content with accesskey ".$key." not found. May be it is not published (if live version).");					
				}
			
			return "";
		}
		
		/**
		 * Retrieves the output of a field as defined in Cluster-Template. 
		 * To be used for Items with maximum cardinality of 1 only!!!
		 * @param integer $cid content.CID to query the content from.
		 * @param string additional parameters for this plugin. // might be changed to array in future versions
		 * @param integer ID of the Variation to query. Leave Blank or set to zero for Page-Variation. 
		 * @returns string The output of the module.
		 */
		function getById($cid, $params = null, $variation = 0) {
			// set variation.
			if ($variation==0)
				$variation = $this->variation;	
			
								
				if ($cid != "") {
					$plugin = getDBCell("content", "MODULE_ID", "UPPER(ACCESSKEY)='".strtoupper($key)."'");
					$oid = getDBCell("content_variations", "FK_ID", "CID = $cid AND VARIATION_ID = $variation AND DELETED=0");

					if ($oid != "" && $plugin != "") {
						$ref = createPGNRef($plugin, $oid, $clti);

						$content = $ref->draw($params);
						unset ($ref);
					} else 
					  $content = "";

					if ($content != "")
					  return $content;

					// now the content seems to be empty. So we try standard variation.
					if ($variation != $this->parent->stdVariation)
					  $content = $this->get($name, $params, $this->parent->stdVariation);
					return $content;			  
					
				} else {
					log_error ("Content with id ".$cid." not found. May be it is not published (if live version).");					
				}
			
			return "";
		}
		

		/**
		 * Retrieves the output of a field as defined in Cluster-Template. 
		 * To be used for Items with every cardinality. Returns an array with the results. 
		 * @param string name of the field to query the content from.
		 * @param string additional parameters for this plugin. 
		 * @param integer ID of the Variation to query. Leave Blank or set to zero for Page-Variation. 
		 * @param string Column, you want to order the output of.
		 * @returns string The output of the module.
		 */
		function getField($name, $params = null, $variation = 0, $order = "POSITION ASC") {
			
			if ($variation == 0)
				$variation = $this->variation;

			// $myclid = getCLID($this->pageClusterNodeId, $this->variation);
			// determine, if static or dynamic content.
			$clt = getDBCell("cluster_node", "CLT_ID", "CLNID = $this->pageClusterNodeId");
			$clti = getDBCell("cluster_template_items", "CLTI_ID", "CLT_ID = $clt AND UPPER(NAME) = UPPER('$name')");

			if ($clti == "") {
				$res[0] = "$name is not defined!";

				return $res;
			}

			$type = getDBCell("cluster_template_items", "CLTITYPE_ID", "CLTI_ID = $clti");
			$res = array ();
			
			if ($type == 1)
				$res[0] = "$name is a static content and therefore not a field!";

			if ($type == 2) { // dynamic field
				$plugin = getDBCell("cluster_template_items", "FKID", "CLTI_ID = $clti");

				$field = createDBCArray("cluster_content", "CLCID", "CLTI_ID = $clti AND CLID = $this->pageClusterId ORDER BY $order");

				if (count($field) == 0 && $variation != $this->parent->stdVariation)
					$res = $this->getField($name, $params, $this->parent->stdVariation, $order);
					
				for ($i = 0; $i < count($field); $i++) {
					if ($field[$i] != "" && $plugin != "") {
						$ref = createPGNRef($plugin, $field[$i]);

						$content = $ref->draw($params);
						unset ($ref);
						array_push($res, $content);
					}
				}
			}  else if ($type == 5) {
				$plugin = getDBCell("cluster_template_items", "FKID", "CLTI_ID = $clti");
				$field = createDBCArray("cluster_content", "FKID", "CLTI_ID = $clti AND CLID = $this->pageClusterId", "ORDER BY ".$order, false);
				
				if (count($field) == 0 && $variation != $this->parent->stdVariation)
					$res = $this->getField($name, $params, $this->parent->stdVariation, $order);

				for ($i = 0; $i < count($field); $i++) {
					if ($field[$i] != "" && $plugin != "") {
						
						$oid = getDBCell("content_variations", "FK_ID", "CID = ".$field[$i]." AND VARIATION_ID = $this->variation AND DELETED=0");
						$ref = createPGNRef($plugin, $oid);

						$content = $ref->draw($params);
						unset ($ref);
						array_push($res, $content);
					}	
				}		
			
			} else {
				$res[0] = "$name is not a content-field!";
			}
			
			return $res;
		}
		
		/**
		 * Returns an array with all content-fields in this cluster
		 */
		function getFieldnames() {
			$clt = getDBCell("cluster_node", "CLT_ID", "CLNID = $this->pageClusterNodeId");
			return createDBCArray("cluster_template_items", "NAME", "CLT_ID = $clt AND (CLTITYPE_ID=1 OR CLTITYPE_ID=2 OR CLTITYPE_ID = 5)", "ORDER BY POSITION ASC");

		}
		
		/**
		 * checks whether a content-field exists in this cluster
		 * @param string name of content-field
		 */
		function field_exists($fieldname) {
			$names = $this->getFieldnames();
			if (is_array($names)) {
			  return (in_array($fieldname, $names));
			} else {
			  return false;	
			}
		}
		
		
		/**
		 * Returns an array with all fields in the cluster
		 * @param string Parameters for all contents.
 		 * @param integer ID of the Variation to query. Leave Blank or set to zero for Page-Variation. 
		 */
		 function getAllFields($param = null, $variation=0) {
		 	$result = array();
		 	$fieldnames = $this->getFieldnames();
		 	if (is_array($fieldnames)) {
		 		foreach ($fieldnames as $name) {
		 			$result[$name] = $this->getField($name, $param, $variation);	
		 		}
		 	}
		 	return $result;
		 }
	}
?>