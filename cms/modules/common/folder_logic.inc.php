<?
	/**********************************************************************
	 * @module Application
	 **********************************************************************/

	/**
	 * Logic for processing folders
	 * @package InternalLogic
	 */

	
	
	
	//// ACL Check ////
	if (! $aclf->hasAccess($auth->userId))
	  header("Location: ". $c["docroot"]."modules/common/noaccess.php?sid=$sid&guid=$pnode");
	//// ACL Check ////
	
	// initialize
	$action = value("action");
	$oid = value("oid", "NUMERIC");
	$deleteobject = value("deleteobject");
	
    $forward = "";
	// processing page actions
	if ($action != "0") {
		if ($action == $lang->get("new_folder") && $auth->checkAccessToFunction("NEW_FOLDER")) {
			if (value("go") == "0")
				$go = "insert";

			if ($go == "insert")
				$page_action = "INSERT";

			$form = new stdEDForm($lang->get("r_newfolder"), "i_newfolder.gif");
			$cond = $form->setPK("categories", "CATEGORY_ID");
			$catname = new TextInput($lang->get("r_foldername"), "categories", "CATEGORY_NAME", $cond, "type:text,width:200,size:32", "MANDATORY&UNIQUE");
			$catname->setFilter("PARENT_CATEGORY_ID = $pnode");
			$form->add($catname);
			$form->add(new Hidden("action", $lang->get("new_folder")));
			$form->add(new Hidden("pnode", $pnode));
			$form->add(new NonDisplayedValueOnInsert("categories", "PARENT_CATEGORY_ID", $cond, $pnode, "NUMBER"));
			$form->add(new NonDisplayedValueOnInsert("categories", "DELETED", $cond, 0, "NUMBER"));
			$form->forbidDelete(true);
			$page->add($form);
			$handled = true;
			$forward = doc();
		} else if ($action == $lang->get("del_folder") && $auth->checkAccessToFunction("DELETE_FOLDER") && $pnode != "0") {
			if (!isset($go))
				$go = "start";

			$delhandler = new ActionHandler("deletefolder");
			
			// removed comment-out from following line because deleting folders didn't work anymore.
			$oid = $pnode;

			if ($go != $lang->get("Cancel")) {
				$amount = 0;

				$sql1 = "SELECT COUNT(CATEGORY_ID) AS ANZ FROM content WHERE CATEGORY_ID = $oid AND DELETED=0";
				$sql2 = "SELECT COUNT(CATEGORY_ID) AS ANZ FROM cluster_templates WHERE CATEGORY_ID = $oid AND DELETED=0";
				$sql3 = "SELECT COUNT(CATEGORY_ID) AS ANZ FROM categories WHERE PARENT_CATEGORY_ID = $oid AND DELETED=0";

				$query = new query($db, $sql1);
				$query->getrow();
				$amount += $query->field("ANZ");
				$query = new query($db, $sql2);
				$query->getrow();
				$amount += $query->field("ANZ");
				$query = new query($db, $sql3);
				$query->getrow();
				$amount += $query->field("ANZ");

				if ($amount == 0) {
					if (value("decision") == $lang->get("yes")) {
						// set new folder-id.
						$parentId = getDBCell("categories", "PARENT_CATEGORY_ID", "CATEGORY_ID = " . $oid);
						pushVar("pnode", $parentId);
						
						// delete folder.
						$delhandler->addDBAction("DELETE FROM categories WHERE CATEGORY_ID = $oid");
						$delhandler->process("deletefolder");
						// set new id.
						$oid = $pnode = $parentId;
					} else if (value("decision") != $lang->get("no")) {
						$title = getDBCell("categories", "CATEGORY_NAME", "CATEGORY_ID = " . $oid);

						$form = new YesNoForm($lang->get("r_deletefolder"). " $title", $lang->get("folder_delmes", "Do you really want to delete this folder?"));
						$form->add(new Hidden("action", $lang->get("del_folder")));
						$form->add(new Hidden("pnode", $pnode));
						$page->add($form);
						$handled = true;
					}
				} else {
					$title = getDBCell("categories", "CATEGORY_NAME", "CATEGORY_ID = $oid");

					$delform = new MessageForm($lang->get("r_deletefolder"). " $title", $lang->get("r_foldernotempty"), doc(). "?" . $auth->getSid());
					$page->add($delform);
					$handled = true;
				}
			}
		} else if ($action == $lang->get("edit_folder") && $auth->checkAccessToFunction("ED_FOLDER_PROPS") && $pnode != "0") {
			$go = "UPDATE";

			$isFolder = true;
			$oid = $pnode;
			$page_action = "UPDATE";
			$form = new EditForm($lang->get("r_editfolder"), "i_folderproperties.gif");
			$cond = $form->setPK("categories", "CATEGORY_ID");
			$form->add(new TextInput($lang->get("r_foldername"), "categories", "CATEGORY_NAME", $cond, "type:text,width:200,size:32", "MANDATORY"));
			$form->add(new FolderDropdown($lang->get("r_parent"), "categories", "PARENT_CATEGORY_ID", $cond));
			$form->add(new Hidden("pnode", $pnode));
			$form->add(new Hidden("action", $lang->get("edit_folder")));
			$page->add($form);
			$handled = true;
				$forward = doc();
		} else if ($action == $lang->get("edit_access") && $auth->checkAccessToFunction("ED_FOLDER_ACL")) {
			$go = "UPDATE";
			$oid = $pnode;
			$page_action = "UPDATE";
			$title = getDBCell("categories", "CATEGORY_NAME", "CATEGORY_ID = " . $pnode); // used by ACLPanel.
			// Build breadcrumb
			$aclPanel = new EditForm($lang->get("edit_access"));

			if ($pnode == "")
				$pnode = "0";

			$str = pathToRootFolder($pnode);
			$aclPanel->add(new Label("lbl", $str, "informationheader", 2));
			$aclPanel->add(new Hidden("action", $lang->get("edit_access")));

			$aclid = $pnode;
			$aclType = "folder";
			include $c["path"] . "api/userinterface/panels/acl_panel.inc.php";


			$aclPanel->add(new Hidden("pnode", $pnode));
			$page->add($aclPanel);
			$handled = true;
		}
	} // end isset($action)

	/**
	 * internally used only for creating a array for deleting a folder tree.
	 */
	function createDelArray(&$handler, $id) {
		global $db;

		$sql = "SELECT CATEGORY_ID FROM categories WHERE PARENT_CATEGORY_ID = $id";
		$query = new query($db, $sql);

		while ($query->getrow()) {
			$cid = $query->field("CATEGORY_ID");

			$dsql = "UPDATE categories SET DELETED = 1 WHERE CATEGORY_ID = $cid";
			$handler->addDBAction($dsql);
			createDelArray($handler, $cid);
		}

		$query->free();
		$dsql = "UPDATE categories SET DELETED = 1 WHERE CATEGORY_ID = $id";
		$handler->addDBAction($dsql);
	}
?>