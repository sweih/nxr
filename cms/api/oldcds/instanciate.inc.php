<?
	/**
	 * Instanciate.
	 * Functions for getting pageIds and so on for
	 * background operations mainly.
	 * @module Initialisation
	 * @package CDS
	 */

	/**--------------------------------- populate some variables ------------------------------**/
	// Initialize Page configuration
	if (!isset($page))
		$page = value("page", "NUMERIC");

	if (!isset($v))
		$v = value("v", "NUMERIC");

	if ($v == "0" || $v == "")
		$v = 1;

	if ($page == "0" || $page == "")
		$page = getStartPage();

	$clid = getPageCluster($page, $v);
	$clnid = getClusterNode($page);
	$splevel = getSitepageLevel($page);

	// if (isExpired($clid)) exit;

	/**------------------------------- end of variables population ----------------------------**/

	/**
	 * sets a new page ID and therefore reinits all variables!
	 * @param integer New Page-ID
	 */
	function setPage($newpage) {
		global $page, $clid, $clnid, $v;

		$page = $newpage;
		$clid = getPageCluster($page, $v);
		$clnid = getClusterNode($page);

		if (isExpired($clid)) {
			echo "ERROR: The page you want view does not exist or is no longer live.";
		}
	}

	function thisPage() {
		global $REQUEST_URI;

		echo $REQUEST_URI;
	}

	/**
	 * Gets the Variation-Shorttag from the ID
	 * @param integer ID of the Variation
	 * @return string shorttag of the Variation
	 */
	function variationIdToTag($id) { return strtolower(getDBCell("variations", "SHORTTEXT", "VARIATION_ID = $id AND DELETED=0")); }

	/**
	* Gets the Variation-ID from the Shorttag
	* @param  string shorttag of the Variation
	* @return integer ID of the Variation
	*/
	function variationTagToId($tag) { return getDBCell("variations", "VARIATION_ID", "UPPER(SHORTTEXT) = UPPER('$tag') AND DELETED=0"); }

	/**
	 * Gets the available Variations of a cluster-Node
	 * @param 		integer		Cluster-Node-ID
	 * @returns	integer		Linear array with all available Variations.
	 */
	function getClusterVariations($clnid) {
		global $db, $splevel;

		if ($splevel == 10) { // checking for live-variations....
			$sql = "SELECT 
	  				DISTINCT cv.VARIATION_ID 
	  			FROM 
	  				cluster_variations cv, 
	  				cluster_node cn, 
	  				sitepage sp,
	  				sitepage_names sn,
	  				sitepage_variations sv RIGHT JOIN  
	  				state_translation st ON cv.CLID = st.OUT_ID AND st.EXPIRED = 0
	  			WHERE
	  				cn.CLNID = $clnid
	  			AND	cv.CLNID = cn.CLNID 
	  			AND cv.DELETED =  0
				AND cn.CLNID = sp.CLNID
				AND sp.SPID = sn.SPID
				AND sn.DELETED = 0
				AND sp.SPM_ID = sv.SPM_ID
				AND sv.VARIATION_ID = cv.VARIATION_ID";
		} else {
			$sql = "SELECT 
	  				DISTINCT cv.VARIATION_ID 
	  			FROM 
	  				cluster_variations cv, 
	  				cluster_node cn, 
	  				sitepage sp,
	  				sitepage_names sn,
	  				sitepage_variations sv 
	  			WHERE
	  				cn.CLNID = $clnid
	  			AND	cv.CLNID = cn.CLNID 
	  			AND cv.DELETED =  0
				AND cn.CLNID = sp.CLNID
				AND sp.SPID = sn.SPID
				AND sn.DELETED = 0
				AND sp.SPM_ID = sv.SPM_ID
				AND sv.VARIATION_ID = cv.VARIATION_ID";
		}

		$query = new query($db, $sql);
		$returns = array ();

		while ($query->getrow()) {
			array_push($returns, $query->field("VARIATION_ID"));
		}

		$query->free();
		return $returns;
	}

	/**
	 * Retrieves the Cluster-ID from the Cluster-Node-Id and the variation. 
	 * If the selected variation does not exist, the standard-variation is choosen.
	 * @param integer Cluster-Node-Id
	 * @param integer Variation-ID
	 * @returns integer ID of Cluster-Variation.
	 */
	function getCLID($cclnid, $cvariation) {
		global $clnid, $v, $clid, $c;

		// get the clid
		if ($cclnid == $clnid && $cvariation == $v) {
			$myclid = $clid;
		} else {
			$myclid = getDBCell("cluster_variations", "CLID", "CLNID = $cclnid AND VARIATION_ID = $cvariation AND DELETED=0");
		}

		if ($myclid == "") {
			$cvariation = $c["stdvariation"];

			$myclid = getDBCell("cluster_variations", "CLID", "CLNID = $cclnid AND VARIATION_ID = $cvariation AND DELETED=0");

			if ($myclid == "")
				return "";
		}

		return $myclid;
	}

	/**
	 * Determines, wheter the given spid is a live-site or a development site.
	 * @param 		integer		Sitepage-ID to check
	 * @returns	integer		level of the sitepage. 0=Development, 10=Live.
	 */
	function getSitepageLevel($spid) {
		$level = getDBCell("state_translation", "LEVEL", "OUT_ID = $spid");

		if ($level == "")
			$level = 0;

		return $level;
	}

	/**
	 * Gets the Cluster-ID (CLID) from the Cluster-Node-Id and a variation ID.
	 * @param 	integer		Cluster-Node-Id
	 * @param	integer		Variation-ID
	 * @returns integer	Cluster-ID (CLID)
	 */
	function getCluster($clnid, $variation) {
		global $db;

		$sql = "SELECT CLID FROM cluster_variations WHERE CLNID = $clnid AND VARIATION_ID = $variation";
		$query = new query($db, $sql);

		if ($query->getrow())
			return $query->field("CLID");
	}

	/**
	* Get the Cluster-Node-ID (CLNID) from a Sitepage-ID. 
	* The difference between the cluster-node and the cluster-id is, that 
	* the cluster-node-id is free of variations. That means, you cannot get 
	* any content from a Cluster-Node-ID, but you can use it for getting different 
	* Variations of the cluster. E.G. it may happen, that a page in the selected variation 
	* is not available and therefore clid=0. You can then still use the getPageVariation-Command 
	* for getting the standard variation. 
	* @param 	integer 	Sitepage-ID
	* @returns integer		Cluster-Node Id (clnid)
	*/
	function getClusterNode($page) {
		global $db;

		$sql = "SELECT 
	 				sp.CLNID
	 			FROM
	 				sitepage sp
	 			WHERE
	 				sp.SPID = $page";

		$query = new query($db, $sql);

		if ($query->getrow())
			return $query->field("CLNID");
	}

	/**
	* Get the Cluster-ID (CLID) from a Sitepage-ID and a Variation-ID.
	* @param	integer		Sitepage-ID (given as page)
	* @param	integer		Variation-ID (given as v)
	* @returns integer		Id of the cluster (clid)
	*/
	function getPageCluster($page, $variation) {
		global $db;

		$sql = "SELECT 
	 				cv.CLID 
	 			FROM 
	 				cluster_variations cv,
	 				cluster_node cn, 
	 				sitepage sp 
	 			WHERE
	 				sp.SPID = $page
	 			AND sp.CLNID = cn.CLNID
	 			AND cn.CLNID = cv.CLNID
	 			AND cv.VARIATION_ID = $variation";

		$query = new query($db, $sql);

		if ($query->getrow())
			return $query->field("CLID");
	}
?>