<?
	/**********************************************************************
	 * @module Application
	 **********************************************************************/
	require_once "../../config.inc.php";

	require_once "configurator.php";

	$auth = new auth("USER_MANAGEMENT");
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
	$filtermenu->addMenuEntry($lang->get("role_link"), "role_general.php");
	$filtermenu->tipp = "";
	//$filtermenu->addLink("test", "test.php");
	$deleteHandler = new ActionHandler("DELETE");
	$deleteHandler->addDbAction("DELETE FROM roles where role_id=$oid AND role_id>999");
	$deleteHandler->addDbAction("DELETE FROM user_permissions where role_id=$oid AND role_id > 999");
	$deleteHandler->addDbAction("DELETE FROM role_sys_functions where role_id=$oid AND role_id > 999");

	if ($oid == 0) {
		$addtext = "";
	} else {
		$addtext = ": " . getDBCell("roles", "ROLE_NAME", "ROLE_ID = " . $oid);
	}

	$form = new stdEDForm($lang->get("role_head"). $addtext, "i_role.gif");
	$cond = $form->setExPK("roles", "ROLE_ID");
	$cond .= " AND UPPER(ROLE_NAME) <> 'ADMINISTRATOR'";

	if ($oid != "") {
		$form->headerlink = crHeaderLink($lang->get("role_permission", "Edit role permissions"), "modules/user/role_permissions.php?sid=$sid&go=update&oid=$oid");
	}

	$form->add(new TextInput($lang->get("role_name"), "roles", "ROLE_NAME", $cond, "type:text,width:200,size:16", "MANDATORY&UNIQUE"));
	$form->add(new TextInput($lang->get("role_description"), "roles", "DESCRIPTION", $cond, "type:textarea,width:340,size:2", ""));
	$form->registerActionHandler($deleteHandler);

	$page->addMenu($filtermenu);
	$page->add($form);
	$page->drawAndForward("modules/user/role_general.php?sid=$sid&go=update&oid=<oid>");
	$db->close();
?>