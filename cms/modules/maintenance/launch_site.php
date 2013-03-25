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


	$auth = new auth("ADMINISTRATOR");
	$page = new page("Rebuild Cache");

	$go = value("go");

	if ($go == "0")
		$go = "start";

	$form = new CommitForm($lang->get("mt_lw_site"));
	$form->addToTopText($lang->get("mt_lws_messages", "Do you really want to launch the whole website?"));
	$maintenanceHandler = new ActionHandler("generate");
	$maintenanceHandler->addFncAction("launchWholeSite");	
	$chllaunchHandler = new ActionHandler("launchchannels");
	$chllaunchHandler->addFncAction("launchAllChannels");
	$form->addCheck("generate", $lang->get("mt_lw_site"), $maintenanceHandler);
	$form->addCheck("launchchannels", $lang->get("mt_lwc", "Launch all articles"), $chllaunchHandler);
	

	$page->add($form);
	$page->draw();
	$db->close();		
?>