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
	$filter->icon = "li_template.gif";
	$filter->type_name = "Templates";

	$filtermenu = new Filtermenu($lang->get("spm"), $filter);
	$filtermenu->tipp = $lang->get("help_pagetemp", "In spreadsheet and database applications, a template is a blank form that shows which fields exist, their locations, and their length. In N/X, templates are the basis of every output. A template is a form in which all the cells have been defined but no data has yet been entered.");

	$insertHandler = new ActionHandler("INSERT");
	$insertHandler->addFNCAction("syncSPMVariations");
	$updateHandler = new ActionHandler("UPDATE");
	$updateHandler->addFNCAction("syncSPMVariations");

	$deleteHandler = new ActionHandler("DELETE");
	$deleteHandler->addDBAction("UPDATE sitepage_master SET DELETED=1 WHERE SPM_ID = $oid");
	$deleteHandler->addDBAction("UPDATE sitepage SET DELETED=1 WHERE SPM_ID = $oid");
	$deleteHandler->addDBAction("UPDATE sitemap SET DELETED = 1 WHERE SPM_ID = $oid");
	$deleteHandler->addDBAction("DELETE FROM sitepage_variations WHERE SPM_ID = $oid");
	if ($oid!="") {
	  $title = getDBCell("sitepage_master", "NAME", "SPM_ID = $oid");
	} else {
	  $title = "";	
	}
	$action = value("action");
	
	$form = new stdEDForm($lang->get("spm_edit")." - ".$title, "i_scheme.gif");
	$cond = $form->setPK("sitepage_master", "SPM_ID");
	if ($auth->checkAccessToFunction("B_RELAUNCH_INST")) {
	  $form->buttonbar->add("action", $lang->get("spm_rlaunch", "Refresh instances"), "submit", "", "", true, $lang->get("tt_refresh", "Refresh Instances updates the changes made to a template to cached pages."));
	}
	
	// process action...
	if ($action == $lang->get("spm_rlaunch", "Refresh instances") && $auth->checkAccessToFunction("B_RELAUNCH_INST")) {
		$go = "update";

		$processing = "no";
		$page_action = "UPDATE";
		$page_state = "start";
		relaunchPagesBySPM (value("oid", "NUMERIC"));

		if ($errors == "") {
			$form->addToTopText($lang->get("spm_lauch_success", "The pages based on this master were relaunched successfully.<br>"));
		}
	}

	

	if ($oid != "") {
		$form->addHeaderLink(crHeaderLink($lang->get('edit_template'), "modules/pagetemplate/edit_template.php?sid=$sid&oid=$oid&go=update"));		
	}

	$form->add(new TextInput($lang->get("name"), "sitepage_master", "NAME", $cond, "type:text,width:200,size:32", "MANDATORY&UNIQUE"));
	$form->add(new TextInput($lang->get("spm_path"), "sitepage_master", "TEMPLATE_PATH", $cond, "type:text,width:200,size:64", "MANDATORY"));
	$form->add(new TextInput($lang->get("description"), "sitepage_master", "DESCRIPTION", $cond, "type:textarea,width:300,size:3", ""));
	
	$form->add(new ManualDropdownInput($lang->get("spm_selectthumb", "Select Thumbnail"), "sitepage_master", "THUMBNAIL", getThumbnailList(), $cond));
	
	//$form->add(new TextInput($lang->get("spm_thumbnail", "Thumbnail"), "sitepage_master", "THUMBNAIL", $cond, "type:text,width:200,size:64", ""));
	
	if (($go == "create") || ($creating != "0")) {
		$form->add(new SelectOneInput($lang->get("spm_type"), "sitepage_master", "SPMTYPE_ID", "sitepage_types", "NAME", "SPMTYPE_ID", "1", $cond, "type:dropdown", "MANDATORY"));

		$clts = new CLTSelector($lang->get("spm_cluster"), "sitepage_master", "CLT_ID", $cond, 0, "", "MANDATORY");
		$form->add($clts);
		$form->add(new Hidden("creating", "yes"));
		$clts->process2();
	} else {
		$clt = getDBCell("sitepage_master", "CLT_ID", $cond);

		$clttype = getDBCell("sitepage_master", "SPMTYPE_ID", $cond);
		$clttypename = getDBCell("sitepage_types", "NAME", "SPMTYPE_ID = $clttype");
		$cltname = getDBCell("cluster_templates", "NAME", "CLT_ID = $clt");
		$form->add(new Label("lbl", $lang->get("spm_type"), "standard"));
		$form->add(new Label("lbl", $clttypename, "standard"));
		$form->add(new Label("lbl", $lang->get("spm_cluster"), "standard"));
		$form->add(new Label("lbl", $cltname, "standard"));
	}

	//$form->add(new SelectOneInput($lang->get("spm_meta"), "sitepage_master", "MT_ID", "meta_templates", "NAME", "MT_ID", "INTERNAL=0", $cond, "type:dropdown", "MANDATORY")); 
	$form->add(new SelectMultiple2Input($lang->get("spm_variations"), "sitepage_variations", "VARIATION_ID", $cond, "variations", "NAME", "VARIATION_ID", "DELETED=0"));

	// forbid deletion, until no more SPMs are used in sitemap.
	$amount = countRows("sitemap", "MENU_ID", "SPM_ID = $oid AND DELETED=0");

	if ($amount > 0)
		$form->forbidDelete(true);

	$form->registerActionHandler($deleteHandler);
	$form->registerActionHandler($insertHandler);
	$form->registerActionHandler($updateHandler);
	$form->add(new Hidden("action", ""));
	$page->addMenu($filtermenu);
	$page->add($form);
	$page->drawAndForward("modules/pagetemplate/sitepage_master.php?sid=$sid&go=update&oid=<oid>");
	$db->close();
	
	
	/**
	 * retrieves available thumbnails automatically from the server
	 */
	function getThumbnailList () {
		global $c;
		$ret = array ();
		$ret[] = array("", "");
		$filetypes = array(".png", ".jpg", ".gif");
		$dirhandle = opendir($c["path"].'modules/sitepages/thumbnails/');
		while (false !== ($fname = readdir ($dirhandle))) {
			if (is_file ($c["path"].'modules/sitepages/thumbnails/'.$fname) && in_array(substr($fname, strrpos($fname, "."), strlen($fname) - strrpos($fname, ".")), $filetypes)) {
				$ret[] = array ($fname, $fname);
			}
		}
		closedir($dirhandle);
		return $ret;
	} // function getThumbnailList ()
?>