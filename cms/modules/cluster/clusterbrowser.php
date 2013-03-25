<?
	/**********************************************************************
	 * @module Application
	 **********************************************************************/
	 require_once "../../config.inc.php";

	// include extra language file
	// include extra language file
	$auth = new auth("CL_BROWSER");
	if (value('resetfilter', 'NUMERIC', '0') == '1')
	  delVar('linkset');
	
	$page = new page("Cluster Browser");

	$page->setJS("TREE");
	$page->setJS("FCKLIB");

	//initialize
	$variation = variation();
	$action = value("action", "", "");
	$clt = value("clt", "NUMERIC", "");
	
	if ($action == "" && value("changevariation", "", "") != "") {
	  $action = value("acstate");
	  $clt = initValueEx("clt", "clt", "", "NUMERIC");
	}
	
	$go = value("go", "NOSPACES", "");
	$pnode = initValueEx("pnode", "pnode", "0", "NUMERIC");

	//// ACL Check ////
	$aclf = aclFactory($pnode, "folder");
	$aclf->load();
	if (! $aclf->hasAccess($auth->userId)) {
		$pnode = "0";
		delVar($pnode);
		$aclf = aclFactory(0, "folder");
		$aclf->load();
		if (! $aclf->hasAccess($auth->userId))
		   header("Location: ". $c["docroot"]."modules/common/noaccess.php?sid=$sid&guid=$pnode");
	}
	//// ACL Check ////

    $clnid = value("clnid");
    $oid = value("oid");
	$view = value("view", "NUMERIC");
	if ($clt != "")
	  pushVar("clt", $clt);

	if ($clnid == 0)
		$clnid = $oid;
	if ($oid == 0)
		$oid = $clnid;

	if ($action == "0" || $action=="")
		$action = value("acstate");
    if (value("saction") != "0") {
         if ($view == "0") $view = 1;
         $action = getVar("lastaction");
    }
    pushVar("lastaction", $action);

    $browser = new Clustermenu($lang->get("m_clb", "Library"));
	$browser->action = $c["docroot"] . "modules/cluster/clusterbrowser.php";
	$browser->tipp = $lang->get("help_clb", "Cluster is a collection of dynamic content placeholders, which are to be filled in by an editor of a webpage. The structure-definition within N/X is done with data clusters.");
	$page->addMenu($browser);

	if ($action == $lang->get("del_folder") || $action == $lang->get("edit_folder") || $action == $lang->get("new_folder") || $action == $lang->get("edit_access")) {
		require_once $c["path"] . "modules/common/folder_logic.inc.php";
	} else {
		require_once $c["path"] . "modules/cluster/cluster_logic.inc.php";
	}

	$page->draw();
	$db->close();
echo $errors;
?>