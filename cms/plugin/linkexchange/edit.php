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
  
  $auth = new auth("ADMINISTRATOR");
  $page = new Page("Edit Link");
  
  $form = new stdEDForm($lang->get("edlink", "Edit Link"));
  $cond = $form->setPK("pgn_linkexchange", "ID");
  
  $form->addHeaderLink(crHeaderLink($lang->get("back"), "plugin/linkexchange/overview.php?sid=".$sid));
  $form->add(new TextInput($lang->get("title", "Title"), "pgn_linkexchange", "TITLE", $cond, "type:text,size:128,width:300", "MANDATORY"));
  $form->add(new TextInput($lang->get("url", "URL"), "pgn_linkexchange", "URL", $cond, "type:url,size:256,width:300", "MANDATORY"));  
  $form->add(new TextInput($lang->get("backlink", "Backlink"), "pgn_linkexchange", "RECIPROCALURL", $cond, "type:url,size:256,width:300", ""));
  $form->add(new TextInput($lang->get("email"), "pgn_linkexchange", "EMAIL", $cond, "type:text,size:256,width:300", ""));
  $form->add(new SitepageSelector('Page', 'pgn_linkexchange', 'SOURCEID', $cond, "", "", "NUMBER", true));
  $form->add(new CheckboxTxtInput($lang->get("approved", "Approved"), "pgn_linkexchange", "APPROVED", $cond ));
  $form->add(new TextInput($lang->get("description"), "pgn_linkexchange", "DESCRIPTION", $cond, "type:textarea,size:5,width:300", ""));
  $form->add(new TextInput($lang->get("Keywords"), "pgn_linkexchange", "KEYWORDS", $cond, "type:textarea,size:5,width:300", ""));
  $form->add(new NonDisplayedValueOnInsert("pgn_linkexchange", 'INSERTTIMESTAMP', $cond, getdate()));
  
  
  $deleteHandler = new ActionHandler("DELETE");
  $deleteHandler->addDbAction("DELETE FROM pgn_linkexchange WHERE ID=$oid ");  
  $form->registerActionHandler($deleteHandler);
	
  $page->add($form);
  $page->draw();
?>