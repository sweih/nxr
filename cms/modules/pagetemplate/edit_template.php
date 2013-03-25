<?
	/**********************************************************************
	 * @module Application
	 **********************************************************************/
	require_once "../../config.inc.php";

	$auth = new auth("SITEPAGE_MASTER");
	$page = new page("Sitepage-Master");

	$filter = new Filter("sitepage_master", "SPM_ID");
	$filter->addRule($lang->get("name"), "NAME", "NAME");
	$filter->addRule($lang->get("description"), "DESCRIPTION", "NAME");
	$filter->setAdditionalCondition("DELETED = 0 AND VERSION=0");
	$filter->setNewAction($c["docroot"]."modules/pagetemplate/sitepage_master.php");
	$filter->icon = "li_template.gif";
	$filter->type_name = "Templates";

	$filtermenu = new Filtermenu($lang->get("spm"), $filter);
	if ($oid!="") {
	  $title = getDBCell("sitepage_master", "NAME", "SPM_ID = $oid");
	} else {
	  $title = "";	
	}
	
	$form = new stdEDForm($lang->get("spm_edit")." - ".$title, "i_scheme.gif");
	$cond = $form->setPK("sitepage_master", "SPM_ID");
		
	if ($oid != "") {
		$form->addHeaderLink(crHeaderLink($lang->get("edit_spm", "Edit template properties"), "modules/pagetemplate/sitepage_master.php?sid=$sid&oid=$oid&go=update"));
		$filename = getDBCell("sitepage_master", "TEMPLATE_PATH", "SPM_ID = $oid");
		$form->add(new PHPEditor("phpedit", $filename, "standard"));
	}

	$form->forbidDelete(true);

	$page->addMenu($filtermenu);
	$page->add($form);
	$page->draw();
	$db->close();
?>