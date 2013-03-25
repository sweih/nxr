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
	$menu->tipp= $lang->get("help_synccl", "A feature used for synchronizing clusters after having modified/changed a cluster template.");
	
	$auth = new auth("SYNC_CLUSTERS");
	$page = new page("Synchronize Clusters");

	$go = value("go");

	if ($go == "0")
		$go = "start";

	$form = new CommitForm($lang->get("sync_clusters"));
	$form->addToTopText($lang->get("sync_clusters_descr", "Whe you are changing a cluster template, the clusters are not automatically synched. They are synched when you are working with them the next time. You can sync all of them here."));
	$maintenanceHandler = new ActionHandler("sync");
	$maintenanceHandler->addFncAction("syncClids");
	$form->addCheck("sync", $lang->get("sync_clusters")." ", $maintenanceHandler);

	$page->add($form);
	$page->addMenu($menu);
	$page->draw();
	$db->close();
	
	function syncClids() {
	   global $db, $form;
	   $counter = 0;
	   $sql = "SELECT cv.CLID FROM cluster_variations cv, cluster_node cn WHERE cv.DELETED=0 AND cv.CLNID=cn.CLNID AND cn.VERSION=0";	
	   $query = new query($db, $sql);
	   while ($query->getrow()) {
	     syncCluster($query->field("CLID"));	
	     $counter++;
	   }	
	   //$form->addToTopText("<br/>".$lang->get("num_cl_sync", "Number of cluster who were synchronized").": ".$counter);
	}
?>