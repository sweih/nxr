<?php
	// check, whether the instance is already initialized....

	// define a custom form with special processing methods.
	/**
		* customized form for selecting an Cluster-instance when creating a page.
		*/

			$selected = value("selected");
			$create = value("create");
			$sitepage_CLNID = value("sitepage_CLNID");
			$cluster_node_NAME = value("cluster_node_NAME");

			
	class csForm extends Form {

		/**
			 * save the changes...
			 */
		function process() {
			global $errors, $selected, $create, $sitepage_CLNID, $cluster_node_NAME, $db, $oid, $sid, $clt;
			
			$this->check();

			if (($selected != "0") && $sitepage_CLNID != "0" && $sitepage_CLNID != "") {
				$mid = getVar("mid");

				$sql = "UPDATE sitepage SET CLNID = $sitepage_CLNID WHERE SPID = $oid";

				$query = new query($db, $sql);
				$query->free();

				// reload page, now in editing mode...
				global $db;
				$db->close();
				header ("Location: sitepagebrowser.php?sid=$sid&mid=$mid&action=editobject&go=update&oid=$oid");
				exit;
			} else if (($create != "0") && ($errors == "")) {
				$mid = getVar("mid");

				$nextId = nextGUID();
				$sql = "INSERT INTO cluster_node (CLNID, CLT_ID, NAME, DELETED) VALUES($nextId, $clt, '$cluster_node_NAME', 0)";

				$query = new query($db, $sql);
				$sql = "UPDATE sitepage SET CLNID = $nextId WHERE SPID = $oid";
				$query = new query($db, $sql);
				$query->free();

				$backup = $oid;
				$oid = $nextId;
				syncClusterVariations();
				$oid = $backup;
				global $db;
				$db->close();
				header ("Location: sitepagebrowser.php?sid=$sid&mid=$mid&action=editobject&go=update&oid=$oid");
				exit;
			}
		}
	}

	// doing a trick, to prevent an error display on name-input-field when linking.
	if ($selected != "0")
		$cluster_node_NAME = " ";

	// we are starting and are creating a new instance or selecting an existing one.
	$slcluster = true;
	$form = new csForm($lang->get("sp_configure"), "");
	$form->add(new Hidden("action", "editobject"));
	$form->add(new Hidden("goon", "UPDATE"));
	$form->addToTopText($lang->get("sp_confdesc"). "<br>");
	$form->add(new Label("lbl", $lang->get("sp_link"), "standard", 2));
	$form->add(new CLSelector($lang->get("cli"), "sitepage", "CLNID", "SPID = $spid", $clt, $params = "", $check = ""));
	$form->add(new Cell("clc", "standard", 1, 150));
	$form->add(new ButtonInCell("selected", $lang->get("commit"), "standard", "SUBMIT"));
	$form->add(new Cell("clc", "border", 2, 500));
	$form->add(new Label("lbl", $lang->get("sp_clnew"), "standard", 2));
	$oname = new TextInput($lang->get("cli"), "cluster_node", "NAME", 1, "type:text,size:32,width:200", "MANDATORY&UNIQUE");
	$oname->setFilter("CLT_ID = $clt");
	$form->add($oname);
	$form->add(new Cell("clc", "standard", 1, 150));
	$form->add(new ButtonInCell("create", $lang->get("commit"), "standard", "SUBMIT"));
	// add some variations now...
	$vari = createDBCArray("sitepage_variations", "VARIATION_ID", "SPM_ID = $spm");

	for ($i = 0; $i < count($vari); $i++) {
		$form->add(new Hidden("cluster_variations_VARIATION_ID_" . $vari[$i], "1"));
	}

// check end	
?>