<?
	/**
	 * ClusterContent
	 * Functions for accessing clusters
	 * @module ClusterContent
	 * @package CDS
	 */

	/**
	 * returns a 2d NAme-Value- array with all instances a cluster-template has.
	 * the result is stored by array[counter][0] = title, array[counter][1] = clnid 
	 * Note that entry counter=0 has always Please select and Value -1!
	 * @param integer Cluster-Node-Id of the cluster you want to get all brothers from
	 * @returns array NAME-Value-array.
	 */
	function getClusterBrothers($clnid) {
		$clt = getDBCell("cluster_node", "CLT_ID", "CLNID = $clnid");

		return createNameValueArray("cluster_node", "NAME", "CLNID", "CLT_ID = $clt ORDER BY NAME ASC");
	}

	/**
	 * Returns the title of the cluster
	 * @param integer Cluster-Node-ID (CLNID)
	 * @returns string title of the cluster-node
	 */
	function getClusterTitle($clnid) { return getDBCell("cluster_node", "NAME", "CLNID = $clnid"); }

	/**
	 * Retrieves the CLuster-Node-ID (CLNID) from a included Cluster or Cluster Field by title.
	 * @param string title of the cluster to query the cluster-node-id from.
	 * @param integer ID of the Cluster-Node to query. Leave Blank or set to zero for Page-Cluster. 
	 * @param integer ID of the Variation to query. Leave Blank or set to zero for Page-Variation. 
	 * @returns integer CLNID of the included cluster.
	 */
	function getContentClusterByTitle($title, $cclnid = 0, $cvariation = 0) {
		global $clnid, $v, $clid, $c;

		if ($cclnid == 0)
			$cclnid = $clnid;

		if ($cvariation == 0)
			$cvariation = $v;

		$myclid = getCLID($cclnid, $cvariation);
		$res = getDBCell("cluster_content", "FKID", "CLID = $myclid AND FKID <> 0 AND UPPER(TITLE) = UPPER('$title')");
		return $res;
	}

	/**
	 * Retrieves the CLuster-Node-ID (CLNID) from a included Cluster.
	 * To be used for Items with maximum cardinality of 1 only!!!
	 * @param string name of the field to query the cluster-node-id from.
	 * @param integer ID of the Cluster-Node to query. Leave Blank or set to zero for Page-Cluster. 
	 * @param integer ID of the Variation to query. Leave Blank or set to zero for Page-Variation. 
	 * @returns integer CLNID of the included cluster.
	 */
	function getContentCluster($name, $cclnid = 0, $cvariation = 0) {
		global $clnid, $v, $clid, $c;

		if ($cclnid == 0)
			$cclnid = $clnid;

		if ($cvariation == 0)
			$cvariation = $v;

		$myclid = getCLID($cclnid, $cvariation);

		// get the clti..
		$clt = getDBCell("cluster_node", "CLT_ID", "CLNID = $cclnid");
		$clti = getDBCell("cluster_template_items", "CLTI_ID", "CLT_ID = $clt AND UPPER(NAME) = UPPER('$name')");

		if ($clti == "")
			return "Field not defined!";

		$type = getDBCell("cluster_template_items", "CLTITYPE_ID", "CLTI_ID = $clti");

		if ($type < 3)
			return "$name is a content-item not a cluster!";

		if ($type == 3) { //static cluster
			$oid = getDBCell("cluster_template_items", "FKID", "CLTI_ID = $clti");
		}

		if ($type == 4) { //dynamic cluster
			$oid = getDBCell("cluster_content", "FKID", "CLTI_ID = $clti AND CLID = $myclid");
		}

		if ($oid != "")
			return $oid;

		// now the content seems to be empty. So we try standard variation.
		if ($cvariation != $c["stdvariation"])
			$oid = getContentCluster($name, $cclnid, $c["stdvariation"]);

		return $oid;
	}

	/**
	 * Retrieves the CLuster-Node-IDs (CLNID) from included Clusters.
	 * To be used for clusters with any cardinatlity. Returns an array.
	 * @param string name of the field to query the cluster-node-id from.
	 * @param string name of the column to order the cluster-nodes. 
	 * @param integer ID of the Cluster-Node to query. Leave Blank or set to zero for Page-Cluster. 
	 * @param integer ID of the Variation to query. Leave Blank or set to zero for Page-Variation. 
	 * @returns integer CLNID of the included cluster.
	 */
	function getContentClusterField($name, $order = "POSITION ASC", $cclnid = 0, $cvariation = 0) {
		global $clnid, $v, $clid, $c;

		if ($cclnid == 0)
			$cclnid = $clnid;

		if ($cvariation == 0)
			$cvariation = $v;

		$myclid = getCLID($cclnid, $cvariation);

		// get the clti..
		$res = array ();
		$clt = getDBCell("cluster_node", "CLT_ID", "CLNID = $cclnid");
		$clti = getDBCell("cluster_template_items", "CLTI_ID", "CLT_ID = $clt AND UPPER(NAME) = UPPER('$name')");

		if ($clti == "") {
			$res[0] = "$name is not defined!";

			return $res;
		}

		$type = getDBCell("cluster_template_items", "CLTITYPE_ID", "CLTI_ID = $clti");

		if ($type == 3)
			$res[0] = "$name is a static cluster and therefore not a field!";

		if ($type == 4) {
			$res = createDBCArray("cluster_content", "FKID", "CLTI_ID = $clti AND CLID = $myclid ORDER BY $order");

			if (count($res) == 0 && $cvariation != $c["stdvariation"])
				$res = getContentClusterField($name, $order, $cclnid, $c["stdvariation"]);
		}

		if ($type < 3 || $type > 4)
			$res[0] = "$name is not a cluster-field!";

		return $res;
	}
?>