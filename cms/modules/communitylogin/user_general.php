<?
	/**********************************************************************
	 * @module Application
	 **********************************************************************/
	require_once "../../config.inc.php";

	$auth = new auth("USER_MANAGEMENT");
	$page = new page("User Administration");
	$page->setJS("md5");
	
	$filter = new Filter("auth_user", "user_id");
	$filter->addRule($lang->get("email"), "email", "email");

	$filter->icon = "li_user.gif";
	$filter->type_name = "Users";

	$filtermenu = new Filtermenu($lang->get("user_filtermenu"), $filter);
	$filtermenu->addMenuEntry($lang->get("user_link"), "user_general.php", "user_premissions.php");
	
	
	$deleteHandler = new ActionHandler("DELETE");
	$deleteHandler->addDbAction("DELETE FROM auth_user where user_id=$oid and user_id > 999");
	
	$form = new stdEDForm($lang->get("user_head"), "i_myprofile.gif");
	$form->submitButtonAction = "if (document.form1.auth_user_password_1.value != '') document.form1.auth_user_password_1.value = document.form1.auth_user_password_2.value = hex_md5(document.form1.auth_user_password_1.value);";

	$cond = $form->setPK("auth_user", "user_id");
	
	$form->add(new TextInput($lang->get("email"), "auth_user", "email", $cond, "type:text,width:300,size:64", "MANDATORY&UNIQUE"));
	$form->add(new TextInput($lang->get("password"), "auth_user", "password", $cond, "type:text,width:200,size:40", "MANDATORY"));
	$form->add(new CheckboxInput($lang->get("user_active"), "auth_user", "active", $cond, "1", "0"));
	$form->add(new TextInput($lang->get("confirmcode", "Confirm-Code"), "auth_user", "confirm", $cond, "type:text,width:300,size:40", "MANDATORY&UNIQUE"));
	// Control Information
	$form->add(new NonDisplayedValueOnInsert("auth_user", "registration_date", $cond, "NOW()", "TIMESTAMP"));
	$form->registerActionHandler($deleteHandler);	

	$page->addMenu($filtermenu);
	$page->add($form);
	$page->drawAndForward("modules/communitylogin/user_general.php?sid=$sid&go=update&oid=<oid>");
	$db->close();
?>