<?php
    /**********************************************************************
     *    N/X - Web Content Management System
     *    Copyright 2002-2006 Sven Weih
     *
     *    This file is part of N/X.
     *    The initial has been setup as a small diploma thesis (Studienarbeit) at the FZI.
     *    It was be coached by Prof. Werner Zorn and Dipl.-Inform Thomas Gauweiler.
     *
     *    N/X is free software; you can redistribute it and/or modify
     *    it under the terms of the GNU General Public License as published by
     *    the Free Software Foundation; either version 2 of the License, or
     *    (at your option) any later version.
     *
     *    N/X is distributed in the hope that it will be useful,
     *    but WITHOUT ANY WARRANTY; without even the implied warranty of
     *    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
     *    GNU General Public License for more details.
     *
     *    You should have received a copy of the GNU General Public License
     *    along with N/X; if not, write to the Free Software
     *    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
     **********************************************************************/
  
  require_once "../../config.inc.php";
  $auth = new auth("ADDRESS");
  $page = new Page("Edit Contacts");
  
  $form = new MenuForm($lang->get("contacts_overview", "Contacts Overview"), array($lang->get("no", "NO"),$lang->get("name","Name"), $lang->get("FIRSTNAME", "Firstname"), $lang->get("email", "E-Mail-Address"), $lang->get("city", "City"), $lang->get("last_mod", "Last Modified")), "address", "GGUID", array("GGUID","NAME", "FIRSTNAME", "MAILADDRESS","CITY", "LAST_MODIFIED"), "1", $rows=40); 
 	$form->addFilterRule($lang->get("no"), "GGUID");
 	$form->addFilterRule($lang->get("name"), "NAME");
 	$form->addFilterRule($lang->get("firstname"), "Firstname");
 	$form->addFilterRule($lang->get("email"), "MailAddress");
 	$form->addFilterRule($lang->get("city"), "City");
 	$form->width="900";
 	$form->newAction = "modules/address/edit.php?go=create&sid=".$sid;
 	$form->editAction = "edit.php";
 	$page->add($form);

  $page->draw();
?>