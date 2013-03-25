<?
	/**********************************************************************
	 * @module Application
	 **********************************************************************/
	require_once "../../config.inc.php";

	$auth = new auth("CL_TEMP_BROWSER");
	$page = new page("Cluster Templates");
	$page->setJS("TREE");
	
	$c["clustertemplatebrowser"] = true;
	$pnode = initValueEx("pnode", "pnode", "0", "NUMERIC");

	//// ACL Check ////
	$aclf = aclFactory($pnode, "folder");
	$aclf->load();
	if (! $aclf->hasAccess($auth->userId)) {
		$pnode = "0";
		delVar("pnode");
		$aclf = aclFactory(0, "folder");
		$aclf->load();
		if (! $aclf->hasAccess($auth->userId)) 
		   header("Location: ". $c["docroot"]."modules/common/noaccess.php?sid=$sid&guid=$pnode");
	}
	//// ACL Check ////
	
	$browser = new Clustermenu($lang->get("clt_browse"));
	$browser->action = $c["docroot"] . "modules/clustertemplate/clustertemplates.php";	
	$browser->tipp = $lang->get("help_clt", "Cluster template is a form that enables you to create structure for your site and define the type of contents. Afterwards, cluster template is merged with a page-template that includes the content of the web page.");
	$page->addMenu($browser);

	require_once $c["path"] . "modules/common/folder_logic.inc.php";
	require_once $c["path"] . "modules/clustertemplate/clustertemplate_logic.inc.php";

	if ($action == "newobject" && value("processing") == "yes" && $errors == "") {
		$page->drawAndForward("modules/clustertemplate/clustertemplates.php?sid=$sid&pnode=$pnode&oid=<oid>&action=editobject");
	} else {
		$page->draw();	
	}
	$db->close();	
?>