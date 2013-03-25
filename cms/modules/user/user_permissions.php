<?
	/**********************************************************************
	 * @module Application
	 **********************************************************************/
	require_once "../../config.inc.php";

	$auth = new auth("USER_MANAGEMENT");

	if (strtoupper($go) != "UPDATE" || $oid == 0) {
		$db->close();

		header ("Location: " . $c["docroot"] . "modules/user/user_general.php?sid=$sid&go=$go");
		exit;
	}

	$page = new page("User Administration");

	$filter = new Filter("users", "USER_ID");
	$filter->addRule($lang->get("user_name"), "USER_NAME", "USER_NAME");
	$filter->addRule($lang->get("full_name"), "FULL_NAME", "FULL_NAME");
	$filter->addRule($lang->get("user_email"), "EMAIL", "EMAIL");
	$filter->icon = "li_user.gif";
	$filter->type_name = "Users";

	$filtermenu = new Filtermenu($lang->get("user_filtermenu"), $filter);
	$filtermenu->addMenuEntry($lang->get("user_link"), "user_general.php", "user_permissions.php");
	$filtermenu->addMenuEntry($lang->get("group_link"), "group_general.php");
	$filtermenu->addMenuEntry($lang->get("role_link"), "role_general.php");

	if ($oid == 0) {
		$addtext = "";
	} else {
		$addtext = ": " . getDBCell("users", "USER_NAME", "USER_ID = " . $oid);
	}

	$form = new stdEDForm($lang->get("userperm_head", "Edit user permissions"). $addtext, "i_myprofile.gif");
	$cond = $form->setPK("users", "USER_ID");

	if ($oid != "") {
		$form->headerlink = crHeaderLink($lang->get("user_general", "Edit general user data"), "modules/user/user_general.php?sid=$sid&oid=$oid&go=update");
	}

	// User Permissions
	$groups = createNameValueArray("groups", "GROUP_NAME", "GROUP_ID", "1");
	$roles = createNameValueArray("roles", "ROLE_NAME", "ROLE_ID", "1");

	$headlines = array (
		"head1" => $lang->get("acl_groups", "Select Group to add"),
		"head1_selected" => $lang->get("acl_groupedit", "Select group to edit roles"),
		"head2_selected" => $lang->get("acl_role", "Select roles for group")
	);

	$pk = array ( array (
		"USER_ID",
		$oid,
		"NUMBER"
	) );

	$form->add(new CombinationEditor($groups, $roles, $headlines, "user_permissions", $pk, "GROUP_ID", "NUMBER", "ROLE_ID", "NUMBER", 3, "standard"));
	$form->forbidDelete(true);

	$page->addMenu($filtermenu);
	$page->add($form);
	$page->draw();
	$db->close();
?>