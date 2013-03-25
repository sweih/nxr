<?
	/**********************************************************************
	 *	N/X - Web Content Management System
	 *	Copyright 2002 Sven Weih, FZI Research Center for Information Technologies
	 *	www.fzi.de
	 *
	 *	This file is part of N/X.
	 *	The initial has been setup as a small diploma thesis (Studienarbeit) at the FZI.
	 *	It was be coached by Prof. Werner Zorn and Dipl.-Inform Thomas Gauweiler.
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
	require_once "../../config.inc.php";

	require_once "purge_lib.inc.php";
	includePGNSources();
	$auth = new auth("PURGE_DATABASE");
	$page = new page("Purge");

	if (!isset($go))
		$go = "start";

	$page->tipp = $lang->get("help_purge", "Deletes unnecessary information from the database.");
	
	$form = new CommitForm($lang->get("purge"), "i_purge.gif");
	$form->addToTopText($lang->get("purge_mes"));

	$expiredHandler = new ActionHandler("expired");
	$expiredHandler->addFNCAction("purgeExpired");
	$form->addCheck("expired", $lang->get("purge_expired"), $expiredHandler);

	$folderHandler = new ActionHandler("folders");
	$folderHandler->addDBAction("DELETE FROM categories WHERE DELETED=1");
	$form->addCheck("folders", $lang->get("purge_folder"), $folderHandler);

	$variationHandler = new ActionHandler("variations");
	$variationHandler->addFNCAction("purgeVariations");
	$form->addCheck("variations", $lang->get("purge_var"), $variationHandler);

	$contentHandler = new ActionHandler("content");
	$contentHandler->addFNCAction("purgeContent");
	$form->addCheck("content", $lang->get("purge_content"), $contentHandler);

	$clusterHandler = new ActionHandler("cluster");
	$clusterHandler->addFNCAction("purgeCluster");
	$form->addCheck("cluster", $lang->get("purge_cluster"), $clusterHandler);

	$metaHandler = new ActionHandler("meta");
	$metaHandler->addFNCAction("purgeMeta");
	$form->addCheck("meta", $lang->get("purge_meta"), $metaHandler);

	$cltHandler = new ActionHandler("clt");
	$cltHandler->addFNCAction("purgeClusterTemplates");
	$form->addCheck("clt", $lang->get("purge_clt"), $cltHandler);

	$spmHandler = new ActionHandler("spm");
	$spmHandler->addFNCAction("purgeSitepages");
	$form->addCheck("spm", $lang->get("purge_pages"), $spmHandler);
	
	$page->add($form);
	$page->draw();
	$db->close();
?>