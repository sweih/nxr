<?
	/**********************************************************************
	 * @module Application
	 **********************************************************************/
	require_once "../../config.inc.php";

	require_once "configurator.php";

	$auth = new auth("USER_MANAGEMENT");

	if (strtoupper($go) != "UPDATE" || $oid == 0) {
		$db->close();

		header ("Location: " . $c["docroot"] . "modules/user/role_general.php?sid=$sid&go=$go");
		exit;
	}

	$page = new page("Role Administration");

	$filter = new Filter("roles", "ROLE_ID");
	$filter->addRule($lang->get("role_name"), "ROLE_NAME", "ROLE_NAME");
	$filter->setAdditionalCondition("UPPER(ROLE_NAME) <> 'ADMINISTRATOR'");
	$filter->prevent_sysvar_disp = false;
	$filter->icon = "li_role.gif";
	$filter->type_name = "Roles";

	$filtermenu = new Filtermenu($lang->get("role_filtermenu"), $filter);
	$filtermenu->addMenuEntry($lang->get("user_link"), "user_general.php");
	$filtermenu->addMenuEntry($lang->get("group_link"), "group_general.php");
	$filtermenu->addMenuEntry($lang->get("role_link"), "role_general.php", "role_permissions.php");

	if ($oid == 0) {
		$addtext = "";
	} else {
		$addtext = ": " . getDBCell("roles", "ROLE_NAME", "ROLE_ID = " . $oid);
	}

	$form = new stdEDForm($lang->get("role_head"). $addtext, "i_role.gif");
	$cond = $form->setExPK("roles", "ROLE_ID");
	$cond .= " AND UPPER(ROLE_NAME) <> 'ADMINISTRATOR'";

	if ($oid != "") {
		$form->headerlink = crHeaderLink($lang->get("role_general", "Edit general role data"), "modules/user/role_general.php?sid=$sid&oid=$oid&go=update");
	}

	if ($page_action == "UPDATE") {
		$form->add(new Configurator());
	}

	$form->forbidDelete(true);

	$page->addMenu($filtermenu);
	$page->add($form);
	$page->draw();
	$db->close();
?>