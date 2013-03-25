<?php
	require "../../config.inc.php";

	$disableMenu = true;
	$auth = new auth("CL_USAGE");
	$page = new page("Cluster Usage");

	// start processing
	$oid = value("oid", "NUMERIC");
	$clt = getDBCell("cluster_node", "CLT_ID", "CLNID=$oid");
	$cat = getDBCell("cluster_templates", "CATEGORY_ID", "CLT_ID = $clt");
	//// ACL Check ////
	$aclf = aclFactory($cat, "folder");
	$aclf->load();
	if (! $aclf->hasAccess($auth->userId) || ! $aclf->checkAccessToFunction("CL_USAGE")) {
	   header("Location: ". $c["docroot"]."modules/common/noaccess.php?sid=$sid&guid=$pnode");
	}
	//// ACL Check ////

	$objectName = getDBCell("cluster_node", "NAME", "CLNID=$oid");

	$form = new Form("Cluster: " . $objectName, "");
	$form->width = 250;
	$form->add(new Label("lbl", "The cluster $objectName is used in following pages...", "informationheader", 2));

	// Initializing Array
	$clusters = array ( $oid );

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

	$pclusters = parentClusters($clusters, 0);
	$clusters = array_merge($pclusters, $clusters);
	$clusters = array_unique($clusters);

	for ($j = 0; $j < count($clusters); $j++) {
		$spids = createDBCArray("sitepage", "SPID", "CLNID = " . $clusters[$j]);

		for ($k = 0; $k < count($spids); $k++) {
			$iname = getDBCell("sitepage_names", "NAME", "VARIATION_ID=1 AND SPID = " . $spids[$k]);

			$menu = getDBCell("sitepage", "MENU_ID", "SPID = " . $spids[$k]);
			$linktext = backTrail($menu);
			$spm = getDBCell("sitepage", "SPM_ID", "SPID = " . $spids[$k]);
			$spmtype = getDBCell("sitepage_master", "SPMTYPE_ID", "SPM_ID = " . $spm);

			if ($iname != "" && $spmtype != 1)
				$linktext = $linktext . "&nbsp;&gt;&nbsp;" . $iname;

			$linkhref = $c["docroot"] . "modules/sitepages/sitepagebrowser.php?sid=$sid&jump=go&oid=" . $spids[$k];
			$link = "<a href=\"#\" onClick=\"window.opener.document.location.href='" . $linkhref . "';return false;\">$linktext</a>";
			$form->add(new Label("lbl", $link, "standardlight", 2));
		}
	}
	
	// process channels
	$ids = createNameValueArrayEx("channel_articles", "TITLE", "CHID", "ARTICLE_ID=$oid");
	for ($i=0; $i<count($ids); $i++) {
		$title = $lang->get("channel", "Channel")." ".getDBCell("channels", "NAME", "CHID=".$ids[$i][1])." &gt; ".$ids[$i][0];
		$link = '<a href="#"  onClick="window.opener.document.location.href=\'' . $c["docroot"]."modules/channels/edit.php?sid=".$sid.'&go=update&oid='.$oid.'&setch='.$ids[$i][1].'\';return false;">'.$title.'</a>';
		$form->add(new Label("lbl", $link, "standardlight", 2));
	}

	echo $errors;
	$go = "UPDATE";
	$page->add($form);
	$page->draw();
	$db->close();
?>