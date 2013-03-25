<?
/**********************************************************************
 * @module Application
 **********************************************************************/

 require_once "../../config.inc.php";
 $auth= new auth("CUSTOMERCAREADMIN");
 $page= new page ("Tickets Category Administration");

 $filter = new Filter("tickets_categories", "id");
 $filter->addRule("Category", "name", "name");

 $menu = new Filtermenu("Edit Categories", $filter);
 include "menudef.inc.php";
  
 $deleteHandler = new ActionHandler("DELETE");
 $deleteHandler->addDbAction("DELETE FROM tickets_categories where id=$oid");
 
 $form = new stdEDForm("Edit Ticket Categories", "");
 $cond = $form->setPK("tickets_categories", "id");
 $form->add(new TextInput("Name", "tickets_categories", "name", $cond, "type:text,width:200,size:32", "MANDATORY&UNIQUE"));
 $form->add(new TextInput("POP-Server", "tickets_categories", "pophost", $cond, "type:text,width:200,size:32", "MANDATORY"));
 $form->add(new TextInput("POP-Username", "tickets_categories", "popuser", $cond, "type:text,width:200,size:32", "MANDATORY"));
 $form->add(new TextInput("POP-Password", "tickets_categories", "poppass", $cond, "type:text,width:200,size:32", "MANDATORY"));
 $form->add(new TextInput("Reply-To-Address", "tickets_categories", "replyto", $cond, "type:text,width:200,size:32", "MANDATORY"));

 $form->add(new TextInput("Notify From", "tickets_categories", "notify_from", $cond, "type:text,width:200,size:64", ""));
 $form->add(new TextInput("Notify To", "tickets_categories", "notify_to", $cond, "type:text,width:200,size:64", ""));
 $form->add(new TextInput("Notify Subject", "tickets_categories", "notify_subject", $cond, "type:text,width:200,size:64", ""));
 $form->add(new TextInput("Notify ReplyTo", "tickets_categories", "notify_replyto", $cond, "type:text,width:200,size:64", ""));
 $form->add(new TextInput("Notify Body", "tickets_categories", "notify_body", $cond, "type:textarea,width:350,size:6", ""));
 $form->add(new TextInput("Notify Headers", "tickets_categories", "notify_headers", $cond, "type:textarea,width:350,size:6", ""));

 $form->registerActionHandler($deleteHandler);

 $page->addMenu($menu);
 $page->add($form);
 $page->draw();
 $db->close();
?>