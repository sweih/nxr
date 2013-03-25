<?
	/**********************************************************************
	 * @module Application
	 **********************************************************************/
	require_once "../../config.inc.php";

	$auth = new auth("VARIATIONS");
	$page = new page("Variations");

	$filter = new Filter("variations", "VARIATION_ID");
	$filter->prevent_sysvar_disp = false;
	$filter->addRule($lang->get("v_name"), "NAME", "NAME");
	$filter->addRule($lang->get("v_short"), "SHORTTEXT", "SHORTTEXT");
	$filter->setAdditionalCondition("DELETED=0");
	$filter->icon = "li_variation.gif";	

	$filtermenu = new Filtermenu($lang->get("variations"), $filter);
	include "menu.php";		
	$deleteHandler = new ActionHandler("DELETE");
	$deleteHandler->addDbAction("UPDATE variations SET DELETED=1 where variation_id=$oid and variation_id > 999");

	$form = new stdEDForm($lang->get("v_name", "Edit Language"), "i_variation.gif");
	$cond = $form->setExPK("variations", "VARIATION_ID");
	$form->add(new TextInput($lang->get("v_name"), "variations", "NAME", $cond, "type:text,width:200,size:32", "MANDATORY&UNIQUE"));
	$form->add(new TextInput($lang->get("v_short"), "variations", "SHORTTEXT", $cond, "type:text,width:40,size:5", "MANDATORY&UNIQUE"));
	$form->add(new TextInput($lang->get("description"), "variations", "DESCRIPTION", $cond, "type:textarea,width:300,size:4", ""));
	if ($oid==1)
	  $form->forbidDelete(true);
	$form->registerActionHandler($deleteHandler);
	$page->addMenu($filtermenu);
	$page->add($form);
	$page->tipp = $lang->get("help_variation", "Content may have different variations. So variations can be languages or different profiles of content.");
	$page->draw();
	$db->close();
?>