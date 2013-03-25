<?
	/**********************************************************************
	 * @module Application
	 **********************************************************************/
	require_once "../../config.inc.php";

	$auth = new auth("COMPOUND_GROUPS");
	
	if (strtoupper($go) != "UPDATE" || $oid == 0) {
		$db->close();

		header ("Location: " . $c["docroot"] . "modules/compoundgroup/compound_groups.php?sid=$sid&go=$go");
		exit;
	}
	
	
	$page = new page("Compound Groups");

	$filter = new Filter("compound_groups", "CGID");
	$filter->addRule($lang->get("name"), "NAME", "NAME");
	$filter->setAdditionalCondition("VERSION = 0");
	$filter->type_name = $lang->get("cp_group");;
	$filter->icon = "li_cggroup.gif";
	
	$filtermenu = new Filtermenu($lang->get("cp_group"), $filter);
	
	
	$name = getDBCell("compound_groups", "NAME", "CGID = $oid");
	$form = new stdEDForm($lang->get("ed_cpgroup", "Edit Compound Group").":".$name);
	$cond = $form->setPK("compound_groups", "CGID");
	if ($oid != "") {
		$form->headerlink = crHeaderLink($lang->get("ed_cpgroupgeneral", "Edit Group General"), "modules/compoundgroup/compound_groups.php?sid=$sid&oid=$oid&go=update");
	}
	$compoundClts = createDBCArray("cluster_templates", "CLT_ID", "CLT_TYPE_ID=1");
	if (count($compoundClts) > 0 ) {
		$members = createNameValueArrayEx("cluster_node", "NAME", "CLNID", "VERSION=0 AND CLT_ID IN (".implode(", ", $compoundClts).")", "ORDER BY NAME ASC");
	} else {
		$members = array();	
	}
	$form->add(new SelectMultipleInputPos($lang->get("cpg_members_select", "Select members of this compound group"), $members, "compound_group_members", "CGMID", "CGID", $cond, "cluster_node", "CLNID", "NAME", "standardlight", 2));
	$form->forbidDelete(true);
	$page->addMenu($filtermenu);
	$page->add($form);
	$page->draw();
	$db->close();
?>