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
  $page = new Page("Edit Recipes");
  $filtermenu = new StdMenu("Recipe Editor");
  $filtermenu->addMenuEntry("Recipes", "overview.php");
  $filtermenu->addMenuEntry("Tags", "tags.php");
    
    $form = new MenuForm($lang->get("recipes_overview", "Recipes Overview"), array($lang->get("name","Name"), "ID" ), "pgn_recipes", "ID", array("NAME", "ID"), "1", $rows=40); 	
 	$form->addFilterRule($lang->get("name"), "NAME");
 	$form->addFilterRule("ID", "ID");
 	$form->width="700";
 	$form->newAction = "plugin/recipe/edit.php?go=create&sid=".$sid;
 	$form->editAction = "edit.php";
 	$page->add($form);
	$page->addMenu($filtermenu);
    $page->draw();
?>