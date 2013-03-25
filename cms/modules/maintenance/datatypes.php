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

	$auth = new auth("MAINTENANCE");
	$page = new page("Generate DataTypes-Array");

	$go = value("go");

	if ($go == "0")
		$go = "start";

	$form = new CommitForm($lang->get("mt_generate_dta", "Generate DataTypes"));
	$form->addToTopText($lang->get("mt_generate_dta_mes", "generates DataTypes"));
	$maintenanceHandler = new ActionHandler("generate");
	$maintenanceHandler->addFncAction("writeDataTypeArray");
	$form->addCheck("generate", $lang->get("mt_generate_dta", "Generate DataTypes"), $maintenanceHandler);

	$page->add($form);
	$page->addMenu($menu);
	$page->draw();
	$db->close();
?>