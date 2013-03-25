<?
	/**********************************************************************
	 *	N/X - Web Content Management System
	 *	Copyright 2002 Fabian Koenig
	 *
	 *	This file is part of N/X.
	 *
	 *	N/X is free software; you can redistribute it and/or modify
	 *	it under the terms of the GNU General Public License as published by
	 *	the Free Software Foundation; either version 2 of the License, or
	 *	(at your option) any later version.
	 *
	 *	N/X is distributed in the hope that it will be useful,
	 *	but WITHOUT ANY WARRANTY; without even the implied warranty of
	 *	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	 *	GNU General Public License for more details.
	 *
	 *	You should have received a copy of the GNU General Public License
	 *	along with N/X; if not, write to the Free Software
	 *	Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
	 **********************************************************************/
	require "../../config.inc.php";

	require "menudef.inc.php";
    $menu->tipp = $lang->get("help_dw", "Creates a configuration-file for N/X Dreamweaver Plug-in. With the help of the Plug-in, templates can be developed with Dreamweaver.");
    
	$auth = new auth("DREAMWEAVER");
	$page = new page("Dreamweaver Extensions");	
	$go = value("go");

	if ($go == "0")
		$go = "start";

	$form = new CommitForm($lang->get("dwext_contentfieldinfo", "Generate Content-Field-Information"));
	$form->addToTopText($lang->get("dwext_contentfieldinfo_mes", "generates Content-Field-Information for Dreamweaver MX"));
	$maintenanceHandler = new ActionHandler("generate");
	$maintenanceHandler->addFncAction("generateDWContentFieldInfo");
	$form->addCheck("generate", $lang->get("dwext_contentfieldinfo", "Generate Content-Field-Information"), $maintenanceHandler);

	$page->add($form);
	$page->addMenu($menu);
	$page->draw();
	$db->close();
?>