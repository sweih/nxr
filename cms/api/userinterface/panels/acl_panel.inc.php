<?php
	require_once $c["path"] . "api/userinterface/panels/acl_editor.php";

	// Copyright 2003 by S.Weih

	// $aclType must be set!!!!!
	// $aclid must be set!!!!
	// $aclPanel must exist!
	//$debug = true;
	if ($aclType != "") {
		$aclObject = aclFactory($aclid, $aclType);
		$aclObject->load();
		$aclPanel->add(new ACLEditor($aclObject, $title));

		// Special for Site-page editing
		if ($mpProp) {
			$aclPanel->add(new Hidden("acstate", "pproperties"));
		}

		$aclPanel->add(new Hidden("oid", $aclid));
		$aclPanel->add(new Hidden("view", $view));
		$aclPanel->add(new Hidden("processing", "yes"));
	}
?>