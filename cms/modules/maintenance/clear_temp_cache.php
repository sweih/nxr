<?
	/**********************************************************************
	 *	N/X - Web Content Management System
	 *	Copyright 2007 Sven Weih
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

	$auth = new auth("MAINTENANCE");
	$page = new page("Clear TempCache");

	$go = value("go");

	if ($go == "0")
		$go = "start";

	$form = new CommitForm($lang->get("clear_cache", "Clear Cache"));
	$form->addToTopText($lang->get("mt_clear_cache", "Clear the temporory cache files."));
	$maintenanceHandler = new ActionHandler("generate");
	$maintenanceHandler->addFncAction("emptyTMPCache");
	$form->addCheck("generate", $lang->get("clear_cache", "Clear Cache"), $maintenanceHandler);

	$page->add($form);	
	$page->draw();
	$db->close();
	
	
	function emptyTMPCache() {
		global $c;
		$dir = $c["dyncachepath"];
		$files = opendir($dir);
	    while ($file = readdir ($files))  {
                if($file != "." && $file != "..")
                {
                    unlink($dir.$file);
                }
        }
        closedir($files);	
	}
?>