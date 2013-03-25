<?php

	/**
	 * @module CopyTree
	 * @package Tools
	 */
	includePGNSources();

	/**
	 * Makes Copy of a sitemap-tree and the corresponding sitepages...
	 *
	 * @param integer ID of the Menu to copy
	 * @param integer ID of the Parent Menu where you want to copy the menu to.
	 */
	function copyTree($menuSource, $menuDestination) {
		$childs = createDBCArray("sitemap", "MENU_ID", "PARENT_ID=$menuSource");

		$dest = copySitemap($menuSource, $menuDestination);

		for ($i = 0; $i < count($childs); $i++) {
			copyTree($childs[$i], $dest);
		}
	}

	/**
	 * Makes Copy of a sitemap-entry and the corresponding sitepages...
	 *
	 * @param integer ID of the Menu to copy
	 * @param integer ID of the Parent Menu where you want to copy the menu to.
	 * @param string  new name of the entry.
	 * @returns integer ID of the new row.
	 */
	function copySitemap($menuSource, $menuDestination, $newName = "") {
		if ($newName == "")
			$newName = getDBCell("sitemap", "NAME", "MENU_ID=$menuSource");

		$newName = makeCopyName("sitemap", "NAME", $newName, "PARENT_ID = $menuDestination");

		$values["MENU_ID"] = nextGUID();
		$values["PARENT_ID"] = $menuDestination;
		$values["NAME"] = $newName;

		copyRow("sitemap", "MENU_ID=$menuSource", $values);

		$spidArray = createDBCArray("sitepage", "SPID", "MENU_ID = $menuSource");

		for ($i = 0; $i < count($spidArray); $i++) {
			copySitepage($spidArray[$i], $values["MENU_ID"], "");
		}

		return $values["MENU_ID"];
	}

	/**
	 * Makes Copy of a sitepage-entry
	 *
	 * @param integer ID of the Page to copy
	 * @param integer ID of the Parent Menu where you want to copy the menu to.
	 * @returns integer ID of the new row.
	 */
	function copySitepage($spidSource, $menuDestination) {
		$clname = value("cluster_node_NAME" . $spidSource);

		$remainCLN = value("cln" . $spidSource);
		$clnid = getDBCell("sitepage", "CLNID", "SPID=$spidSource");

		if ($remainCLN == "1") {
			$cclnid = $clnid;
		} else {
			$cclnid = copyClusterNode($clnid, $clname);
		}

		$values["SPID"] = nextGUID();
		$values["MENU_ID"] = $menuDestination;
		$values["CLNID"] = $cclnid;

		copyRow("sitepage", "SPID=$spidSource", $values);

		$values2["SPID"] = $values["SPID"];
		copyRow("sitepage_names", "SPID=$spidSource", $values2);
		copyMeta($spidSource, $values["SPID"]);
		return $values["SPID"];
	}

	/**
	 * Makes Copy of a metadata-set
	 *
	 * @param integer ID of the Metaset to copy
	 * @param integer new ID of the Metaset.
	 */
	function copyMeta($oldCid, $newCid) {
		$md = createDBCArray("meta", "MID", "CID=$oldCid");

		for ($i = 0; $i < count($md); $i++) {
			$values["MID"] = nextGUID();

			$values["CID"] = $newCid;
			copyRow("meta", "MID=" . $md[$i], $values);
		}
	}

	/**
	 * Makes Copy of a cluster_node
	 *
	 * @param integer ID of the Clusternode to copy
	 * @param string New name of the Clusternode
	 */
	function copyClusterNode($clnid, $newName) {
		if ($newName == "")
			$newName = getDBCell("cluster_node", "NAME", "CLNID=$clnid");

		$clt = getDBCell("cluster_node", "CLT_ID", "CLNID=$clnid");
		$newName = makeCopyName("cluster_node", "NAME", $newName, "CLT_ID=$clt");

		$values["CLNID"] = nextGUID();
		$values["NAME"] = $newName;

		copyRow("cluster_node", "CLNID=$clnid", $values);
		copyMeta($clnid, $values["CLNID"]);
		copyClusterVariations($clnid, $values["CLNID"]);
		return $values["CLNID"];
	}

	/**
	 * Makes copy of the variations of a cluster
	 *
	 * @param integer ID of the Clusternode to make the copy of
	 * @param integer new Id to set for clusternode
	 */
	function copyClusterVariations($oldClnid, $newClnid) {
		$clids = createDBCArray("cluster_variations", "CLID", "CLNID=$oldClnid");

		for ($i = 0; $i < count($clids); $i++) {
			$values["CLID"] = nextGUID();

			$values["CLNID"] = $newClnid;
			copyRow("cluster_variations", "CLID=$clids[$i]", $values);
			copyClusterContent($clids[$i], $values["CLID"]);
			copyMeta($clids[$i], $values["CLID"]);
		}
	}

	/**
	 * Makes copy of cluster's content
	 * 
	 * @param integer Old CLID ID
	 * @param integer New CLID ID
	 */
	function copyClusterContent($oldClid, $newClid) {
		global $db;

		$clcid = createDBCArray("cluster_content", "CLCID", "CLID = $oldClid");

		for ($i = 0; $i < count($clcid); $i++) {
			$values["CLCID"] = nextGUID();

			$values["CLID"] = $newClid;
			copyRow("cluster_content", "CLCID=" . $clcid[$i], $values);
			// now do it for the plugin :-)
			$clti = getDBCell("cluster_content", "CLTI_ID", "CLCID =" . $clcid[$i]);
			$cltitype = getDBCell("cluster_template_items", "CLTITYPE_ID", "CLTI_ID=$clti");

			if ($cltitype == 2) {
				$mod = getDBCell("cluster_template_items", "FKID", "CLTI_ID=$clti");

				$classname = getDBCell("modules", "CLASS", "MODULE_ID = $mod");

				if ($classname != "") {
					$ref = new $classname($clcid[$i]);

					$sql = $ref->copyRecord($values["CLCID"]);
					$query = new query($db, $sql);
					$query->free();
				}
			}
		}
	}
?>