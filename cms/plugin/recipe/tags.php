<?
	/**********************************************************************
	 * @module Application
	 **********************************************************************/
	require_once "../../config.inc.php";

	// Check Authorization
	$auth = new auth("ADMINISTRATOR");
	
	// Initialize Page-Container
	$page = new page("Tags");

	// Create Menu-Filter
	$filter = new Filter("pgn_recipes_tags", "TAG_ID");
	$filter->addRule($lang->get("name"), "TAG", "TAG");
	$filter->type_name = "Tags";

	// Create Menu
	$filtermenu = new Filtermenu("Recipe Editor", $filter);	
	$filtermenu->addMenuEntry("Recipes", "overview.php");
    $filtermenu->addMenuEntry("Tags", "tags.php");    
	
    // ActionHandler for deleting groups
	$deleteHandler = new ActionHandler("DELETE");
	$deleteHandler->addDbAction("DELETE FROM pgn_recipes_tags Where TAG_ID=$oid");
	$deleteHandler->addDbAction("DELETE FROM pgn_recipes_tag_relation Where TAG_ID=$oid");

	// Create Form Container and add widgets
	$form = new stdEDForm("Edit Tags");
	$cond = $form->setPK("pgn_recipes_tags", "TAG_ID");
	$form->add(new TextInput("Tag", "pgn_recipes_tags", "TAG", $cond, "type:text,width:300,size:64", "MANDATORY&UNIQUE"));
	
	$values = array();
	$values[] = array("Category 1",1);
	$values[] = array("Category 2",2);
	$values[] = array("Internal",0);
	$form->add(new SelectOneInputFixed("Tag-Type", "pgn_recipes_tags", "TAG_CAT", $values, $cond));
	
	
	$form->registerActionHandler($deleteHandler);

	// Register Menu and form in Pagbe
	$page->addMenu($filtermenu);
	$page->add($form);
	
	// Draw the page
	$page->draw();
	$db->close();
	echo $errors;
?>