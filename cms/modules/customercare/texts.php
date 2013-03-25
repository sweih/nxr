<?
/**********************************************************************
* @module Application
**********************************************************************/

require_once "../../config.inc.php";
$auth= new auth("CUSTOMERCAREADMIN");
$page= new page ("Textblock Administration");

$filter = new Filter("tickets_textblocks", "BLOCK_ID");
$filter->addRule("Blockname", "NAME", "NAME");

$menu = new Filtermenu("Edit Textblocks", $filter);
include "menudef.inc.php";

$deleteHandler = new ActionHandler("DELETE");
$deleteHandler->addDbAction("DELETE FROM tickets_textblocks where BLOCK_ID=$oid");

$form = new stdEDForm("Edit Textblock", "");
$cond = $form->setPK("tickets_textblocks", "BLOCK_ID");
$form->add(new TextInput("Name", "tickets_textblocks", "NAME", $cond, "type:text,width:200,size:32", "MANDATORY&UNIQUE"));
$form->add(new TextInput("Text", "tickets_textblocks", "CONTENT", $cond, "type:textarea,width:340,size:7", ""));
$form->registerActionHandler($deleteHandler);

$page->addMenu($menu);
$page->add($form);
$page->draw();
$db->close();
 ?>