<?

	/**
	* @module Application
	*/

	/**
	 * delete all expired contents form the database (at least try it :-))
	 */
	function purgeExpired() {
		global $db;

		$delHandler = new ActionHandler("del");
		$expired = createDBCArray("state_translation", "OUT_ID", "EXPIRED=1");

		for ($i = 0; $i < count($expired); $i++) {
			$id = $expired[$i];

			// the ID can be wether a sitemap-entry or a sitepage-entry.
			$sitepage = countRows("sitepage", "SPID", "SPID = $id");
			$sitemap = countRows("sitemap", "MENU_ID", "MENU_ID = $id");

			if ($sitemap > 0)
				$delHandler->addDBAction("UPDATE sitemap SET DELETED=1 WHERE MENU_ID = $id");

			if ($sitepage > 0) {
				$delHandler->addDBAction("UPDATE sitepage SET DELETED=1 WHERE SPID = $id");
			}
		}

		// step ready, so process.
		$delHandler->process("del");
		$delHandler = new ActionHandler("del");

		// check, whether all sitepage_masters are require_onced in live-instance.
		$spms = createDBCArray("sitepage_master", "SPM_ID", "DELETED=0 AND VERSION=10");

		for ($i = 0; $i < count($spms); $i++) {
			$id = $spms[$i];

			$check = countRows("sitepage", "SPID", "SPM_ID = $id AND DELETED = 0 AND VERSION = 10");

			if ($check == 0)
				$delHandler->addDBAction("UPDATE sitepage_master SET DELETED=1 WHERE SPM_ID = $id");
		}

		// step ready, so process.
		$delHandler->process("del");
		$delHandler = new ActionHandler("del");

		// check, whether all cluster-templates are still require_onced.
		$clts = createDBCArray("cluster_templates", "CLT_ID", "DELETED=0 AND VERSION=10");

		for ($i = 0; $i < count($clts); $i++) {
			$id = $clts[$i];

			$check = countRows("sitepage_master", "CLT_ID", "CLT_ID = $id AND DELETED = 0 AND VERSION = 10");
			$clnids = createDBCArray("cluster_node", "CLNID", "CLT_ID = $id AND VERSION=10");

			for ($j = 0; $j < count($clnids); $j++) {
				$check += countRows("cluster_template_items", "CLTI_ID", "FKID = " . $clnids[$j]);
			}

			if ($check == 0)
				$delHandler->addDBAction("UPDATE cluster_templates SET DELETED=1 WHERE CLT_ID = $id");
		}

		// step ready, so process.
		$delHandler->process("del");
		$delHandler = new ActionHandler("del");

		// check, whether all meta-templates are still require_onced.
		$meta = createDBCArray("meta_templates", "MT_ID", "DELETED=0 AND VERSION=10");

		for ($i = 0; $i < count($meta); $i++) {
			$id = $meta[$i];

			$check = countRows("cluster_templates", "MT_ID", "MT_ID = $id AND DELETED = 0 AND VERSION = 10");
			$backtrans = getDBCell("state_translation", "IN_ID", "OUT_ID = $id AND LEVEL = 10");
			$check += countRows("modules", "MT_ID", "MT_ID = $backtrans");

			if ($backtrans < 1000)
				$check += 1;

			$check += countRows("content", "MT_ID", "MT_ID = $id AND DELETED=0 AND VERSION = 10");

			if ($check == 0) {
				$delHandler->addDBAction("DELETE FROM meta_templates WHERE MT_ID = $id");

				$delHandler->addDBAction("DELETE FROM meta_template_items WHERE MT_ID = $id");
			}
		}

		// step ready, so process.
		$delHandler->process("del");
		$delHandler = new ActionHandler("del");
		
		// check, whether all clusters are still required.
		// note: checks for variations are not done here, as there may be dependencies between 
		// standard and other variations.
		$clids = createDBCArray("cluster_node", "CLNID", "DELETED=0 AND VERSION=10");

		for ($i = 0; $i < count($clids); $i++) {
			$id = $clids[$i];

			$check = countRows("sitepage", "CLNID", "CLNID = $id AND DELETED = 0 AND VERSION = 10");
			$check += countRows("cluster_template_items", "FKID", "FKID = $id AND DELETED = 0 AND VERSION = 10");
			$check += countRows("cluster_content", "FKID", "FKID = $id AND DELETED = 0");

			if ($check == 0)
				$delHandler->addDBAction("UPDATE cluster_node SET DELETED=1 WHERE CLNID = $id");
		}

		// step ready, so process.
		$delHandler->process("del");
		$delHandler = new ActionHandler("del");
	}

	/**
	 * delete all menu-entries, sitepages and masters that are marked for deletion
	 */
	function purgeSitepages() {
		global $db;

		$delHandler = new ActionHandler("del");

		$menu = createDBCArray("sitemap", "MENU_ID", "DELETED = 1");

		for ($i = 0; $i < count($menu); $i++) {
			$sql = "UPDATE sitepage SET DELETED = 1 WHERE MENU_ID = " . $menu[$i];

			$query = new query($db, $sql);
		}

		$delHandler->addDBAction("DELETE FROM sitemap WHERE DELETED=1");

		$spms = createDBCArray("sitepage_master", "SPM_ID", "DELETED=1");

		for ($i = 0; $i < count($spms); $i++) {
			$delHandler->addDBAction("DELETE FROM sitepage_variations WHERE SPM_ID = " . $spms[$i]);
		}

		$delHandler->addDBAction("DELETE FROM sitepage_master WHERE DELETED = 1");
		$spid = createDBCArray("sitepage", "SPID", "DELETED = 1");

		for ($i = 0; $i < count($spid); $i++) {
			$id = $spid[$i];

			$delHandler->addDBAction("DELETE FROM sitepage_names WHERE SPID = $id");
			$delHandler->addDBAction("DELETE FROM sitepage_owner WHERE SPID = $id");
		}

		$delHandler->addDBAction("DELETE FROM sitepage WHERE DELETED = 1");
		$delHandler->addDBAction("DELETE FROM sitepage_names WHERE DELETED = 1");
		$delHandler->process("del");
	}

	/**
	 * delete all CLuster-templates that are marked for deletion.
	 */
	function purgeClusterTemplates() {
		$clt = createDBCArray("cluster_templates", "CLT_ID", "DELETED = 1");

		$delHandler = new ActionHandler("del");

		for ($i = 0; $i < count($clt); $i++) {
			$delHandler->addDBAction("DELETE FROM cluster_template_items WHERE CLT_ID = " . $clt[$i]);

			$delHandler->addDBAction("DELETE FROM cluster_templates WHERE CLT_ID = " . $clt[$i]);
		}

		$delHandler->process("del");
	}

	/**
	 * Purge all deleted META-Data
	 */
	function purgeMeta() {
		global $db;

		$delHandler = new ActionHandler("del");
		$delHandler->addDBAction("DELETE FROM meta WHERE DELETED=1");
		$delHandler->process("del");
	}

	/**
	 * Purge all deleted clusters from the database
	 */
	function purgeCluster() {
		global $db;

		$clusters = createDBCArray("cluster_node", "CLNID", "DELETED = 1");
		$delHandler = new ActionHandler("del");

		for ($i = 0; $i < count($clusters); $i++) {
			$cln = $clusters[$i];

			$sql = "UPDATE cluster_variations SET DELETED=1 WHERE CLNID=$cln";
			$query = new query($db, $sql);
			$delHandler->addDBAction("DELETE FROM meta WHERE CID = $cln");
		}

		$delHandler->addDBAction("DELETE FROM cluster_node WHERE DELETED = 1");
		$delHandler->addDBAction("DELETE FROM cluster_variations WHERE DELETED = 1");
		$clids = createDBCArray("cluster_variations", "CLID", "DELETED = 1");

		for ($i = 0; $i < count($clids); $i++) {
			deleteCluster ($clids[$i]);
		}

		$delHandler->process("del");
	}

	/** 
	 * Purge all deleted contents form the database
	 */
	function purgeContent() {
		global $db;

		$sql = "SELECT CID, MODULE_ID FROM content WHERE DELETED = 1";
		$query = new query($db, $sql);
		$delHandler = new ActionHandler("del");

		while ($query->getrow()) {
			$cid = $query->field("CID");

			$module = $query->field("MODULE_ID");
			$fkid = createDBCArray("content_variations", "FK_ID", "CID=$cid");

			for ($i = 0; $i < count($fkid); $i++) {
				deletePlugin($fkid[$i], $module);
			}

			$delHandler->addDBAction("DELETE FROM content_variations WHERE CID = $cid");
			$delHandler->addDBAction("DELETE FROM content WHERE CID = $cid");
			$delHandler->addDBAction("DELETE FROM meta WHERE CID = $cid");
		}

		$delHandler->process("del");
	}

	/**
	 * purge all variations set are marked for delete.
	 */
	function purgeVariations() {
		$vari = createDBCArray("variations", "VARIATION_ID", "DELETED = 1");

		for ($i = 0; $i < count($vari); $i++) {
			deleteVariation ($vari[$i]);
		}
	}

	/**
	 * delete all (also live) instances of a variation.
	 * @param integer ID of the Variation
	 */
	function deleteVariation($variation) {
		global $db;

		$delHandler = new ActionHandler("del");
		$delHandler->addDBAction("DELETE FROM sitepage_names WHERE VARIATION_ID = $variation");
		$delHandler->addDBAction("DELETE FROM sitepage_variations WHERE VARIATION_ID = $variation");

		// delete clusters
		$sql = "SELECT CLID FROM cluster_variations WHERE VARIATION_ID = $variation";

		$query = new query($db, $sql);

		while ($query->getrow()) {
			deleteCluster ($query->field("CLID"));
		}

		$delHandler->addDBAction("DELETE FROM cluster_variations WHERE VARIATION_ID = $variation");

		// delete content
		$sql = "SELECT v.FK_ID, c.MODULE_ID FROM content_variations v, content c WHERE c.CID = v.CID AND v.VARIATION_ID = $variation";

		$query = new query($db, $sql);

		while ($query->getrow()) {
			deletePlugin($query->field("FK_ID"), $query->field("MODULE_ID"));
		}

		$delHandler->addDBAction("DELETE FROM content_variations WHERE VARIATION_ID = $variation");
		$delHandler->addDBAction("DELETE FROM variations WHERE VARIATION_ID = $variation");

		$delHandler->process("del");
	}


?>