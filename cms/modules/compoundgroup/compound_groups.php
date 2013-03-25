<?
	/**********************************************************************
	 * @module Application
	 **********************************************************************/
	require_once "../../config.inc.php";

	$auth = new auth("COMPOUND_GROUPS");
	$page = new page("Compound Groups");

	$filter = new Filter("compound_groups", "CGID");
	$filter->addRule($lang->get("name"), "NAME", "NAME");
	$filter->setAdditionalCondition("VERSION = 0");
	$filter->icon = "li_cggroup.gif";
	
	$filter->type_name = $lang->get("cp_group");
	$filtermenu = new Filtermenu($lang->get("cp_group"), $filter);
	$filtermenu->tipp = $lang->get("help_compgrp", "Compound group is a group of clusters. Clusters templates may have an own layout. This enables you to build your homepage out of blocks, e.g. News-Article, Poll, Image-Gallery all on one page and on demand.");
	
	$deleteHandler = new ActionHandler("DELETE");
	$deleteHandler->addDbAction("DELETE FROM compound_groups where CGID=$oid AND CGID > 999");
	$deleteHandler->addDbAction("DELETE FROM compound_group_members where CGID=$oid");

	$form = new stdEDForm($lang->get("ed_cpgroup", "Edit Compound Group"));
	$cond = $form->setPK("compound_groups", "CGID");
	
	if ($oid != "" && $page_action != "DELETE") {		
		$form->buttonbar->add("todo", $lang->get("launch"), "submit", "");	
		$form->headerlink = crHeaderLink($lang->get("ed_cpgroup"), "modules/compoundgroup/compound_group_members.php?sid=$sid&oid=$oid&go=update");
	}
	$form->add(new TextInput($lang->get("group_name"), "compound_groups", "NAME", $cond, "type:text,width:200,size:32", "MANDATORY&UNIQUE"));
	$form->add(new TextInput($lang->get("group_description", "Description"), "compound_groups", "DESCRIPTION", $cond, "type:textarea,width:340,size:2", ""));
	$form->add(new Hidden("todo", ""));
	
    $values = array();
    array_push($values, array($lang->get("latest", "Latest changed first"), 2));
    array_push($values, array($lang->get("oldest", "Oldest changed first"), 3));    
    array_push($values, array($lang->get("by_order", "By order"), 4));
    array_push($values, array($lang->get("random", "Random"), 1));
    

	$form->add(new SelectOneInputFixed($lang->get("ordercomp", "Order of Compounds"), "compound_groups", "SORTMODE", $values, $cond, "type:dropdown"));
	$form->registerActionHandler($deleteHandler);

	if (value("todo") == $lang->get("launch")) {
		launchCompoundGroup($oid, 10);
		$page_action = "UPDATE";
		$page_state = "";	
		if ($errors == "") {
			$form->addToTopText($lang->get("objlaunch_success"));
			$form->topicon = "ii_success.gif";
		}
	}

	$page->addMenu($filtermenu);
	$page->add($form);
	$page->drawAndForward("modules/compoundgroup/compound_groups.php?sid=$sid&go=update&oid=<oid>");
	$db->close();
?>