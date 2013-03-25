<?
	/**********************************************************************
	 * @module Application
	 **********************************************************************/
	require_once "../../config.inc.php";

	$auth = new auth("USER_MANAGEMENT");
	$page = new page("User Administration");
	$page->setJS("md5");
	
	$filter = new Filter("users", "USER_ID");
	$filter->addRule($lang->get("user_name"), "USER_NAME", "USER_NAME");
	$filter->addRule($lang->get("full_name"), "FULL_NAME", "FULL_NAME");
	$filter->addRule($lang->get("user_email"), "EMAIL", "EMAIL");
	$filter->icon = "li_user.gif";
	$filter->type_name = "Users";

	$filtermenu = new Filtermenu($lang->get("user_filtermenu"), $filter);
	$filtermenu->addMenuEntry($lang->get("user_link"), "user_general.php", "user_premissions.php");
	$filtermenu->addMenuEntry($lang->get("group_link"), "group_general.php");
	$filtermenu->addMenuEntry($lang->get("role_link"), "role_general.php");    
 
	
	$deleteHandler = new ActionHandler("DELETE");
	$deleteHandler->addDbAction("DELETE FROM users where user_id=$oid and user_id > 999");
	$deleteHandler->addDbAction("DELETE FROM temp_vars where user_id=$oid and user_id > 999");
	$deleteHandler->addDbAction("DELETE FROM user_session where user_id=$oid and user_id > 999");
	$deleteHandler->addDbAction("DELETE FROM user_permissions where user_id=$oid and user_id > 999");

	$insertHandler = new ActionHandler("INSERT");
	$insertHandler->addDbAction("INSERT INTO user_session (USER_ID) VALUES (<oid>)");
	$insertHandler->addDbAction("INSERT INTO temp_vars (NAME, USER_ID, VALUE) VALUES ('variation', <oid>, 1)");
	$insertHandler->addDBAction("INSERT INTO temp_vars (NAME, USER_ID, VALUE) VALUES ('mid', <oid>, 0)");

	if ($oid == 0) {
		$addtext = "";
	} else {
		$addtext = ": " . getDBCell("users", "USER_NAME", "USER_ID = " . $oid);
	}

	$form = new stdEDForm($lang->get("user_head"). $addtext, "i_myprofile.gif");
	$form->submitButtonAction = "if (document.form1.users_PASSWORD_1.value != '') document.form1.users_PASSWORD_1.value = document.form1.users_PASSWORD_2.value = hex_md5(document.form1.users_PASSWORD_1.value);";

	$cond = $form->setPK("users", "USER_ID");

	if ($oid != "") {
		$form->headerlink = crHeaderLink($lang->get("user_permission", "Edit user permissions"), "modules/user/user_permissions.php?sid=$sid&go=update&oid=$oid");
	}

	$form->add(new TextInput($lang->get("user_name"), "users", "USER_NAME", $cond, "type:text,width:200,size:16", "MANDATORY&UNIQUE"));
	$form->add(new TextInput($lang->get("full_name"), "users", "FULL_NAME", $cond, "type:text,width:200,size:32", "MANDATORY"));
	$form->add(new PasswordInput($lang->get("password"), "users", "PASSWORD", $cond, "type:text,width:200,size:32", "MANDATORY"));
	$form->add(new TextInput($lang->get("user_email"), "users", "EMAIL", $cond, "type:text,width:200,size:64", "MANDATORY"));
	$form->add(new CheckboxInput($lang->get("user_active"), "users", "ACTIVE", $cond, "1", "0"));
	$form->add(new SelectOneInput($lang->get("user_bl"), "users", "LANGID", "internal_resources_languages", "NAME", "LANGID", "1", $cond, "type:dropdown", "MANDATORY", "TEXT"));

	// Control Information
	$form->add(new NonDisplayedValueOnInsert("users", "REGISTRATION_DATE", $cond, "NOW()", "TIMESTAMP"));
	$form->registerActionHandler($deleteHandler);
	$form->registerActionHandler($insertHandler);

	$page->addMenu($filtermenu);
	$page->add($form);
	$page->drawAndForward("modules/user/user_general.php?sid=$sid&go=update&oid=<oid>");
	$db->close();
?>