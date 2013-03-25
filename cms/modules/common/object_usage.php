<?php
	require "../../config.inc.php";

	$disableMenu = true;
	$auth = new auth("OBJ_USAGE");
	$page = new page("Object Usage");

	// start processing
	$oid = value("oid", "NUMERIC");
	$objFolder = getDBCell("content", "CATEGORY_ID", "CID=$oid");
	
	//// ACL Check ////
	$aclf = aclFactory($objFolder, "folder");
	$aclf->load();
	if (! $aclf->checkAccessToFunction("OBJ_USAGE"))
	  header("Location: ". $c["docroot"]."modules/common/noaccess.php?sid=$sid&guid=$pnode");
	//// ACL Check ////

	$form = new Form("Object: " . $objectName, "");
	$form->width = 250;
	$form->add(new Label("lbl", "The object $objectName is used in following pages...", "informationheader", 2));

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
	$sql = "SELECT CLID FROM cluster_content WHERE FKID = $oid";
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

	//echo "USED:".count($usedPageClusters)." UNUSED:".count($unusedPageClusters);
	//echo " ALL CLUSTERS:".count($clusters);

	// now find all clusters recursive that contain the unused values...
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

	// create backtrail...
	function backTrailOB($menu) {
		global $db;

		$result = getDBCell("sitemap", "NAME", "MENU_ID = $menu");
		$parent = getDBCell("sitemap", "PARENT_ID", "MENU_ID = $menu");

		if ($parent != 0 && $parent != "" && $parent != "0" && $parent != $menu)
			$result = backTrailOB($parent). "&nbsp;&gt;&nbsp;" . $result;

		return $result;
	}

	$pclusters = parentClusters($clusters, 0);
	$clusters = array_merge($pclusters, $clusters);
	$clusters = array_unique($clusters);

	for ($j = 0; $j < count($clusters); $j++) {
		$spids = createDBCArray("sitepage", "SPID", "CLNID = " . $clusters[$j]);

		for ($k = 0; $k < count($spids); $k++) {
			$iname = getDBCell("sitepage_names", "NAME", "VARIATION_ID=1 AND SPID = " . $spids[$k]);

			$menu = getDBCell("sitepage", "MENU_ID", "SPID = " . $spids[$k]);
			$linktext = backTrailOB($menu);
			$spm = getDBCell("sitepage", "SPM_ID", "SPID = " . $spids[$k]);
			$spmtype = getDBCell("sitepage_master", "SPMTYPE_ID", "SPM_ID = " . $spm);

			if ($iname != "" && $spmtype != 1)
				$linktext = $linktext . "&nbsp;&gt;&nbsp;" . $iname;

			$linkhref = $c["docroot"] . "modules/sitepages/sitepagebrowser.php?sid=$sid&jump=go&oid=" . $spids[$k];
			$link = "<a href=\"#\" onClick=\"window.opener.document.location.href='" . $linkhref . "';return false;\">$linktext</a>";
			$form->add(new Label("lbl", $link, "standardlight", 2));
		}
	}

	echo $errors;
	$go = "UPDATE";
	$page->add($form);
	$page->draw();
	$db->close();
?>