<?
	/**********************************************************************
	 * @module Application
	 **********************************************************************/
	require_once "../../config.inc.php";

	$auth = new auth("OBJECT_BROWSER");
	if (value("resetfilter") == '1') {		
		delVar ("filter");
		delVar ("sname");
		pushVar("linkset", '');		
	}

	$page = new page("Object Browser");
	$page->setJS("TREE");
	$page->setJS("FCKLIB");
	// Reset Filter
	
	// initialize variables
	$action = value("action");
	$handled = false;
	$pnode = initValueEx("pnode", "pnode", "0", "NUMERIC");
	//// ACL Check ////
	$aclf = aclFactory($pnode, "folder");
	$aclf->load();
	if (! $aclf->hasAccess($auth->userId)) {
		delVar("pnode");	
		$pnode="0";	
		$aclf = aclFactory(0, "folder");
		$aclf->load();		
		if (! $aclf->hasAccess($auth->userId)) 
		   header("Location: ". $c["docroot"]."modules/common/noaccess.php?sid=$sid&guid=$pnode");
	}
	//// ACL Check ////
	
	
	$browser = new Foldermenu($lang->get("library", "Library"));
	$browser->action = $c["docroot"] . "modules/content/objectbrowser.php";
	$browser->tipp = $lang->get("help_objbrowse", "Content library contains all content material of the site");
	$page->addMenu($browser);
	require_once $c["path"] . "modules/common/folder_logic.inc.php";
	require_once $c["path"] . "modules/content/object_logic.inc.php";

	//$debug = true;
	//$panic = true;
	if ($action == $lang->get("new_content") && $errors == "" && $page_state == "processing") {
		$page->drawandforward("modules/content/object_edit.php?sid=$sid&go=update&oid=<oid>");
	} else {
		$page->draw();
	}

	$db->close();
//echo "Error: $errors OID: $oid GO: $go PAGEACTION $page_action PAGESTATE $page_state PROCCESING $processing";
?>