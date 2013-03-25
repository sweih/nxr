<?
	/**********************************************************************
	 * @module Application
	 **********************************************************************/
	require_once "../../config.inc.php";

	$auth = new auth("ANY");
	$page = new page("User Administration");
	$page->setJS("md5");

	// get user id from session id.
	$oid = getDBCell("user_session", "USER_ID", "SESSION_ID='$auth->session'");

	// restrict operations to update, do not alow insert
	$page_action = "UPDATE";
	$go = "UPDATE";

	$form = new EditForm($lang->get("user_profile"), "i_myprofile.gif");
	$form->submitButtonAction = "if (document.form1.users_PASSWORD_1.value != '') document.form1.users_PASSWORD_1.value = document.form1.users_PASSWORD_2.value = hex_md5(document.form1.users_PASSWORD_1.value);";
	$cond = "USER_ID = $oid";

	$form->add(new TextInput($lang->get("full_name"), "users", "FULL_NAME", $cond, "type:text,width:200,size:32", "MANDATORY"));
	$form->add(new PasswordInput($lang->get("password"), "users", "PASSWORD", $cond, "type:text,width:200,size:32", "MANDATORY"));
	$form->add(new TextInput($lang->get("user_email"), "users", "EMAIL", $cond, "type:text,width:200,size:64", "MANDATORY"));
	$form->add(new SelectOneInput($lang->get("user_bl"), "users", "LANGID", "internal_resources_languages", "NAME", "LANGID", "1", $cond, "type:dropdown", "MANDATORY", "TEXT"));

	$page->add($form);
	$page->draw();
	$db->close();

?>