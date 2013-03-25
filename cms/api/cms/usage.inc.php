<?php
	/**
	 * @module Usage
	 * @package CMS
	 */

	/**
	 * Find clusters, in which a plugin-entry is used.
	 * @param integer ID of the plugin-Key
	 */
	function findContentUsageClusterNodes($oid) {
		global $db;

		// Initializing Array
		$clusters = array ();

		// Determine cluster_templates using the object as static content...
		$sql = "SELECT CLT_ID FROM cluster_template_items WHERE FKID = $oid";
		$query = new query($db, $sql);

		while ($query->getrow()) {

			// Determine clusters using this template
			$sql = "SELECT CLNID FROM cluster_node WHERE CLT_ID = " . $query->field("CLT_ID");

			$subquery = new query($db, $sql);

			while ($subquery->getrow()) {
				array_push($clusters, $subquery->field("CLNID"));
			}

			$subquery->free();
		}

		$query->free();

		// determine clusters using this content as library link...
		$sql = "SELECT CLID FROM cluster_content WHERE FKID = $oid OR CLCID = $oid";
		$query = new query($db, $sql);

		while ($query->getrow()) {
			// Determine clusters using this template
			$sql = "SELECT CLNID FROM cluster_variations WHERE CLID=" . $query->field("CLID");

			$subquery = new query($db, $sql);

			while ($subquery->getrow()) {
				array_push($clusters, $subquery->field("CLNID"));
			}

			$subquery->free();
		}

		$query->free();

		$clusters = array_unique($clusters);
		return $clusters;
	}

	/**
	 * Find all clusters having one from the given array included.
	 * @params array Array with ClusterNodeIds to check for incluseion
	 * @param integer used for recursion control, do not change manuallay.
	 */
	function parentClusters($clArray, $level = 0) {
		$clnids = array ();

		if ($level > 5)
			return $clnids;

		for ($i = 0; $i < count($clArray); $i++) {
			$cl = $clArray[$i];

			// find dynamic clusters....
			$clids = createDBCArray("cluster_content", "CLID", "FKID = $cl");

			for ($j = 0; $j < count($clids); $j++) {
				array_push($clnids, getDBCell("cluster_variations", "CLNID", "CLID = " . $clids[$j]));
			}

			// find static clusters...
			$clts = createDBCArray("cluster_template_items", "CLT_ID", "FKID = " . $cl);

			for ($j = 0; $j < count($clts); $j++) {
				$clns = createDBCArray("cluster_node", "CLNID", "CLT_ID = " . $clts[$j]);

				$clnids = array_merge($clnids, $clns);
			}
		}

		if (is_array($clnids))
			$clnids = array_unique($clnids);

		// traverse down the tree, max 10 levels...
		$parents = parentClusters($clnids, $level + 1);
		$clnids = array_merge($clnids, $parents);

		if (is_array($clnids))
			$clnids = array_unique($clnids);

		return $clnids;
	}
?>