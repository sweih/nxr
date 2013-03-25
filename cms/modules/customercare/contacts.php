<?

/**********************************************************************

 * @module Application

 **********************************************************************/



 require_once "../../config.inc.php";

$auth= new auth("CUSTOMERCARE");
$page= new page ("Tickets Category Administration");



 $filter = new Filter("pgn_tickets", "ID");

 $filter->addRule("Name", "name", "name");



$menu = new Filtermenu("Edit Contact", $filter);
include "menudef.inc.php";

if ($auth->checkPermission("ADMINISTRATOR")) $menu->addMenuEntry("Edit Categories", "category.php");

  

  $form = new stdEDForm("Browse Contacts", "");

 $cond = $form->setPK("pgn_tickets_categories", "id");

 $form->add(new TextInput("Name", "pgn_tickets", "name", $cond, "type:text,width:300,size:64", "MANDATORY"));

 $form->add(new TextInput("E-Mail", "pgn_tickets", "email", $cond, "type:text,width:300,size:64", ""));

 $form->add(new TextInput("Phone", "pgn_tickets", "phone", $cond, "type:text,width:200,size:32", ""));

 $form->forbidDelete(true);

 



 $page->addMenu($menu);

 $page->add($form);

 $page->draw();

 $db->close();

 ?>

