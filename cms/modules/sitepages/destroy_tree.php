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

	$auth = new auth("B_DESTROY_TREE");
	$page = new page("Destroy Tree");
	@ini_set("max_execution_time", $c["timeout"]);

	$del = value("del","NUMERIC");
	//// ACL Check ////
	$aclf = aclFactory($del, "page");
	$aclf->load();
	if (! $aclf->checkAccessToFunction("B_DESTROY_TREE") || $del < 100000) {		
		header("Location: ". $c["docroot"]."modules/common/noaccess.php?sid=$sid&guid=$pnode");
	}
	//// ACL Check ////
	

	$go = value("go");

	if ($go == "0")
		$go = "start";

	$form = new CommitForm("Destroy Tree");

	$pageHandler = new ActionHandler("destroypages");
	$pageHandler->addFNCAction("destroyTree");
	$form->addCheck("destroypages", $lang->get("destroy_tree", "Destroy Tree"), $pageHandler);

	$clusterHandler = new ActionHandler("destroycluster");
	$clusterHandler->addFNCAction("destroyTree");
	$form->addCheck("destroycluster", $lang->get("destroy_exclusive_content", "Destroy exclusive content"), $clusterHandler);
	$form->backpage="modules/sitepages/sitepagebrowser.php?sid=".$sid;
	$form->add(new Hidden("del", value("del")));
	$form->add(new Hidden("sid", $sid));
	$form->add(new LinkLabel("lbl", $lang->get("back_sp", "Back to Website").">>", "modules/sitepages/sitepagebrowser.php?sid=".$sid, "_self", "informationheader", 2)); 
	$destroyexecuted = false;

	$page->add($form);
	$page->draw();
	echo $errors;

	function destroyTree() {
		global $destroyexecuted, $form, $db;

		$variations = createDBCArray("variations", "VARIATION_ID", "1");

		if (!$destroyexecuted) {
			$destroyexecuted = true;

			$delstart = value("del", "NUMERIC");
			$parentId = getDBCell("sitemap", "PARENT_ID", "MENU_ID = $delstart");
			$destroyArray = getPageTree($delstart);

			if (value("destroypages") == "0") {
				// geht nicht
				$meldung = "Please select also the check Destroy Tree. Only then the system will delete the tree.";

				$form->addToTopText($meldung);
			} else {
				if (value("destroycluster") == "0") {
					// nur pages löschen
					for ($i = 0; $i < count($destroyArray); $i++) {
						for ($j = 0; $j < count($variations); $j++) {
							expireSitepage($destroyArray[$i], 10, $variations[$j]);
						}

						$sql1 = "DELETE FROM sitepage WHERE SPID = $destroyArray[$i]";
						$menuid = getDBCell("sitepage", "MENU_ID", "SPID = " . $destroyArray[$i]);
						$deletequery = new query($db, $sql1);

						if ($menuid != "0" and $menuid != 0 and $menuid != "") {
							$sql2 = "DELETE FROM sitemap WHERE MENU_ID = $menuid";

							$sql3 = "DELETE FROM sitepage_names WHERE SPID = " . $destroyArray[$i];
							$sql4 = "DELETE FROM sitepage_owner WHERE SPID = " . $destroyArray[$i];

							$deletequery = new query($db, $sql3);
							$deletequery = new query($db, $sql4);
							$deletequery = new query($db, $sql2);
						}

						$deletequery->free();
					}
				} else {
					// pages und cluster
					for ($i = 0; $i < count($destroyArray); $i++) {
						for ($j = 0; $j < count($variations); $j++) {
							expireSitepage($destroyArray[$i], 10, $variations[$j]);
						}

						$clnid = getDBCell("sitepage", "CLNID", "SPID = $destroyArray[$i]");
						// only delete cluster if it is not used as channel article.
						if (!getDBCell("channel_articles", "CHID", "ARTICLE_ID = ".$clnid)) {
							$usage = 0;
							$usage += countRows("sitepage", "CLNID", "CLNID = $clnid AND DELETED=0");
							$usage += countRows("cluster_content", "FKID", "FKID = $clnid");
							$usage += countRows("cluster_template_items", "FDIK", "FKID = $clnid");
	
							if ($usage < 2) {
								$sql = "UPDATE cluster_node SET DELETED = 1 WHERE CLNID = $clnid";
	
								$query = new query($db, $sql);
							}
						}

						$sql1 = "DELETE FROM sitepage WHERE SPID = $destroyArray[$i]";
						$menuid = getDBCell("sitepage", "MENU_ID", "SPID = " . $destroyArray[$i]);
						$deletequery = new query($db, $sql1);

						if ($menuid != "0" and $menuid != 0 and $menuid != "") {
							$sql2 = "DELETE FROM sitemap WHERE MENU_ID = $menuid";

							$sql3 = "DELETE FROM sitepage_names WHERE SPID = " . $destroyArray[$i];
							$sql4 = "DELETE FROM sitepage_owner WHERE SPID = " . $destroyArray[$i];

							$deletequery = new query($db, $sql3);
							$deletequery = new query($db, $sql4);
							$deletequery = new query($db, $sql2);
						}

						$deletequery->free();
					
					}
				}
			}
			sortTableRows("sitemap", "MENU_ID", "POSITION", "PARENT_ID = $parentId");
		}
		
	}

	$db->close();
?>