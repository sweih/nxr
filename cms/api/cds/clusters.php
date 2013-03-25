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
	  * Use this class to retriebe clusters
	  * Access this class with $cds->clusters
	  */
	 class Clusters extends CDSInterface {
		function Clusters(&$parent) { CDSInterface::CDSInterface($parent); }

		/**
		 * Returns an array with all clusters in this cluster
		 */
		function getFieldnames() {
			$clt = getDBCell("cluster_node", "CLT_ID", "CLNID = $this->pageClusterNodeId");
			return createDBCArray("cluster_template_items", "NAME", "CLT_ID = $clt AND (CLTITYPE_ID=3 OR CLTITYPE_ID=4)", "ORDER BY POSITION ASC");

		}

		/**
		 * returns a reference to the cluster wich is selected
		 * @param string Name of the cluster
 		 * @param integer ID of the Variation to query. Leave Blank or set to zero for Page-Variation. 
		 */
		 function get($name, $variation=0) {
		    global $clusterCache; 
		 	if ($variation == 0)
			    	$variation = $this->variation;	
			 $name = strtolower($name);
			 
		    // did we already fetch the clsuter (for speedup reasons);
			if (!isset($clusterCache[$this->pageClusterId][$name][$variation])) {		    
				$clnid = $this->getContentCluster($name, $variation);   	    	
		    	$clusterCache[$this->pageClusterId][$name][$variation] = new AbstractCDSApi($this->parent->is_development, $clnid, $variation);
			}
			
		    return $clusterCache[$this->pageClusterId][$name][$variation];
		 }
		 

		 
	/**
	 * Return the APIs of the clusters in the compound group
	 * @param string name of the cluster
	 * @param integer ID of the Variation to query. Leave Blank or set to zero for Page-Variation. 
	 */
	function getCompoundGroup($name, $variation = 0) {
		global $c, $db;
		if ($variation == 0) $variation = $this->variation;
		$name = strtoupper($name);
		
		// get the clti..
		$clid = getDBCell("cluster_variations", "CLID", "CLNID = ".$this->parent->pageClusterNodeId." AND VARIATION_ID = $variation");
		$clt = getDBCell("cluster_node", "CLT_ID", "CLNID = ".$this->parent->pageClusterNodeId);
		$clti = getDBCell("cluster_template_items", "CLTI_ID", "CLT_ID = $clt AND UPPER(NAME) = '$name'");
		if ($clti == "") return "Field not defined!";
		
		$type = getDBCell("cluster_template_items", "CLTITYPE_ID", "CLTI_ID = $clti");
		
		if ($type == 7) {
			$fkid = getDBCell("cluster_template_items", "FKID", "CLTI_ID = $clti");
			$sortmode = getDBCell("compound_groups", "SORTMODE", "CGID = ".$fkid);
					
			if ($sortmode == 1 || $sortmode == 4) {
				$clusters = createDBCArray("compound_group_members", "CGMID", "CGID=$fkid ORDER BY POSITION ASC");
				if ($sortmode == 1) { // Random
					// shuffle
					$i = count($clusters);
					while (-- $i) {
						$j = mt_rand(0, $i);
						if ($i != $j) {
							// swap
							$tmp = $clusters[$i];
							$clusters[$i] = $clusters[$j];
							$clusters[$j] = $tmp;
						}
					}
				}
			} else {
				$clusters = array();
				if ($sortmode == 2) { 
					$order = "DESC";
				} else if ($sortmode == 3) {
					$order = "ASC";	
				}
				
				$sql = "SELECT cgm.CGMID FROM compound_group_members cgm, cluster_variations cv WHERE cgm.CGID = $fkid AND cgm.CGMID = cv.CLNID AND cv.VARIATION_ID = $variation ORDER BY cv.LAST_CHANGED $order";
				$query = new query($db, $sql);
				while ($query->getrow()) {
					array_push($clusters, $query->field("CGMID"));
				}
				$query->free();
			}

			$clusterObj = array();
			for ($i=0; $i < count($clusters); $i++) {
				$clid = getDBCell("cluster_variations", "CLID", "CLNID = ".$clusters[$i]." AND VARIATION_ID = $variation");				
				if (getDBCell("state_translation", "EXPIRED", "OUT_ID = ".$clid) != "1")
					$clusterObj[count($clusterObj)] = new AbstractCDSApi($this->parent->is_development, $clusters[$i], $variation);
			}
			return $clusterObj;					
		} else {
		  return "This is not a compound-group!";	
		}	
	}

	/**
	 * Retrieves the CLuster-Node-ID (CLNID) from a included Cluster.
	 * To be used for Items with maximum cardinality of 1 only!!!
	 * For accessing a cluster always use the get() function.
	 * @param string name of the field to query the cluster-node-id from.	 
	 * @param integer ID of the Variation to query. Leave Blank or set to zero for Page-Variation.
	 * @returns integer CLNID of the included cluster.
	 * @see function cluster()
	 */
	function getContentCluster($name, $variation = 0) {
		global $c;
		if ($variation == 0)
				$variation = $this->variation;
				
		// get the clti..
		$clid = getDBCell("cluster_variations", "CLID", "CLNID = ".$this->parent->pageClusterNodeId." AND VARIATION_ID = $variation");
		$clt = getDBCell("cluster_node", "CLT_ID", "CLNID = ".$this->parent->pageClusterNodeId);
		$clti = getDBCell("cluster_template_items", "CLTI_ID", "CLT_ID = $clt AND UPPER(NAME) = UPPER('$name')");
		if ($clti == "")
			return "Field not defined!";

		$type = getDBCell("cluster_template_items", "CLTITYPE_ID", "CLTI_ID = $clti");

		if ($type < 3)
			return "$name is a content-item not a cluster!";

		if ($type == 3) { //static cluster
			$oid = getDBCell("cluster_template_items", "FKID", "CLTI_ID = $clti");
		}

		if ($type == 4  || $type == 6) { //dynamic cluster
			$oid = getDBCell("cluster_content", "FKID", "CLTI_ID = $clti AND CLID = $clid");
		}

		if ($oid != "")
			return $oid;

		// now the content seems to be empty. So we try standard variation.
		if ($variation != $c["stdvariation"])
			$oid = $this->getCluster($name, $c["stdvariation"]);

		return $oid;
	}

    /**
     * Return a cluster by given CLNID. Use with channels.
     * @param integer ID of the cluster
     * @param integer variation of the cluster
     */
    function getById($clnid, $variation=0) {
        global $c;
        if ($variation == 0)
                $variation = $this->variation;
        return new AbstractCDSApi($this->parent->is_development, $clnid, $variation);
    }
	/**
	 * Get the date the article was last edited
	 */
	function getClusterDate($dateformat="%d.%m.%Y") {
	  global $db;
	  $sql = "SELECT DATE_FORMAT(CREATED_AT, '$dateformat') as d FROM cluster_variations WHERE CLID = ".$this->pageClusterId;
	  $query = new query($db, $sql);
	  if ($query->getrow()) {
		$datum = $query->field("d");
	    $query->free();
	    return $datum;
	  }
	}
	
	/**
	 * Can be called with compound clusters to draw the layout.
	 * @param string name or id of the cluster to draw
	 */
	function draw($name="") {
		global $cds;
		$result = "";
		$me = null;
		if (is_numeric($name)) {
	        	$me = $this->getById($name);
	         	$clnid = $name;
		} else if ($name=="") {
			$clnid = $this->pageClusterNodeId;
			if ($clnid != "" && $clnid != "0") {
				$me = $this->parent;
			}
		} else {
			$clnid = $this->getContentCluster($name);
			if (is_numeric($clnid)) {
				$me = $this->get($name);
			}
		}
	
		if (isset($me)) {
			$clt = getDBCell("cluster_node", "CLT_ID", "CLNID = $clnid");
	  		if (getDBCell("cluster_templates", "CLT_TYPE_ID", "CLT_ID=$clt") == 1) {
				$template = html_entity_decode(getDBCell("cluster_templates", "TEMPLATE", "CLT_ID=$clt"));
				$template = str_replace('&#039;', "'", $template);		
				ob_start();
		 		$result = eval($template);
		 		$result = ob_get_contents();
		 		ob_end_clean();
		 		
				unset($me);
			}
	  	}
	  	
		return $result;
	}
	
	/**
	 * Retrieves the CLuster-Node-IDs (CLNID) from included Clusters.
	 * To be used for clusters with any cardinatlity. Returns an array.
	 * @param string name of the field to query the cluster-node-id from.
	 * @param integer ID of the Variation to query. Leave Blank or set to zero for Page-Variation.
	 * @param string name of the column to order the cluster-nodes.
	 * @returns integer CLNID of the included cluster.
	 */
	function getField($name, $variation = 0, $order = "POSITION ASC" ) {
		global $c;

		
		if ($variation == 0)
			$variation = $this->variation;
		
		// get the clti..
		$res = array ();
		$clt = getDBCell("cluster_node", "CLT_ID", "CLNID = ".$this->parent->pageClusterNodeId);	
      
		$clti = getDBCell("cluster_template_items", "CLTI_ID", "CLT_ID = $clt AND UPPER(NAME) = UPPER('$name')");

		if ($clti == "") {
			$res[0] = "$name is not defined!";
			return $res;
		}

		$type = getDBCell("cluster_template_items", "CLTITYPE_ID", "CLTI_ID = $clti");

		if ($type == 3)
			$res[0] = "$name is a static cluster and therefore not a field!";

		if ($type == 4 || $type == 6) {
			$res = createDBCArray("cluster_content", "FKID", "CLTI_ID = $clti AND CLID = ".$this->parent->pageClusterId." ORDER BY $order", "", false);

			if (count($res) == 0 && $variation != $c["stdvariation"]) {
				$res = $this->getField($name, $c["stdvariation"], $order);
				$variation = $c["stdvariation"];
			}
		}

		if ($type != 3 && $type != 4 && $type != 6)
			$res[0] = "$name is not a cluster-field!";

		$clusters = array();
		for ($i=0; $i < count($res); $i++) {
			$clusters[$i] = new AbstractCDSApi($this->parent->is_development, $res[$i], $variation);
		}
		return $clusters;
		}
	}
?>