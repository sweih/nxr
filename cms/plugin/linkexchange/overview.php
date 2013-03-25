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
  $auth = new auth("LINK_EXCHANGE");
  $page = new Page("Edit Links");
  $filtermenu = new StdMenu("Link Directory Editor");
  $filtermenu->addMenuEntry("Links", "overview.php");
      
  $form = new MenuForm($lang->get("links_overview", "Links Overview"), array($lang->get("TITLE","title"), 'URL',$lang->get('crdate', 'Creation Date') ), "pgn_linkexchange", "ID", array("TITLE", "URL", "INSERTTIMESTAMP"), "1", $rows=40); 	
 	$form->addFilterRule($lang->get("title"), "TITLE");
 	$form->addFilterRule($lang->get("url", "URL"), "URL"); 	
 	$form->width="700";
 	$form->newAction = "plugin/linkexchange/edit.php?go=create&sid=".$sid;
 	$form->editAction = "edit.php";
 	$page->add($form);
	$page->addMenu($filtermenu);
  $page->draw();
?>