<?
	/**********************************************************************
	 * @module Application
	 **********************************************************************/
	require_once "../../config.inc.php";

	// Check Authorization
	$auth = new auth("USER_MANAGEMENT");
	
	// Initialize Page-Container
	$page = new page("Group Administration");

	// Create Menu-Filter
	$filter = new Filter("groups", "GROUP_ID");
	$filter->addRule($lang->get("group_name"), "GROUP_NAME", "GROUP_NAME");
	$filter->icon = "li_group.gif";
	$filter->type_name = "Groups";

	// Create Menu
	$filtermenu = new Filtermenu($lang->get("group_filtermenu"), $filter);
	$filtermenu->addMenuEntry($lang->get("user_link"), "user_general.php");
	$filtermenu->addMenuEntry($lang->get("group_link"), "group_general.php");
	$filtermenu->addMenuEntry($lang->get("role_link"), "role_general.php");

	// ActionHandler for deleting groups
	$deleteHandler = new ActionHandler("DELETE");
	$deleteHandler->addDbAction("DELETE FROM groups where group_id=$oid AND group_id>999");
	$deleteHandler->addDbAction("DELETE FROM user_permissions where group_id=$oid AND group_id > 999");

	// Create Form Container and add widgets
	$form = new stdEDForm($lang->get("group_head"), "i_group.gif");
	$cond = $form->setPK("groups", "GROUP_ID");
	$form->add(new TextInput($lang->get("group_name"), "groups", "GROUP_NAME", $cond, "type:text,width:200,size:16", "MANDATORY&UNIQUE"));
	$form->add(new TextInput($lang->get("group_description"), "groups", "DESCRIPTION", $cond, "type:textarea,width:340,size:2", ""));
	$form->registerActionHandler($deleteHandler);

	// Register Menu and form in Pagbe
	$page->addMenu($filtermenu);
	$page->add($form);
	
	// Draw the page
	$page->draw();
	$db->close();
?>