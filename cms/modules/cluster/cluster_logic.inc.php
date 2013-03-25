<?
	/**********************************************************************
	 *@module Application
	 **********************************************************************/
	
	//// ACL Check ////
	if (! $aclf->hasAccess($auth->userId))
	  header("Location: ". $c["docroot"]."modules/common/noaccess.php?sid=$sid&guid=$pnode");
	//// ACL Check ////

	// process object actions.
	$linkset = value("linkset");
	$filter = value("filter");
	$handled = false;
	if (value('resetfilter', 'NUMERIC', '0') == '1') 
	  delVar('linkset');
	if ($action != "0" || $view != "0") {
		
		
		//check if cluster exists in this variation
		if ($clnid != "0") {
			if ($action == "cr_cluster" && value("decision") == $lang->get("yes")) {
			  $oid = value("oid", "NUMERIC");
			  $crvar = value("crvar", "NUMERIC");	
  			  if ($oid != "0" && $crvar != "0") {
			    if (getDBCell("cluster_variations", "CLID", "CLNID = $oid AND VARIATION_ID = $crvar") != "") {
			      $sql = "UPDATE cluster_variations SET DELETED=0 WHERE CLNID = $oid AND VARIATION_ID = $crvar"; 	
			      $query = new query($db, $sql);
			      $query->free(); 
			    } else {
			      createCluster($oid, $crvar, $auth->userName);
			    }
			    $view = 1;			    
			  }		
			} else {

        			$clid = getDBCell("cluster_variations", "CLID", "CLNID = $clnid AND VARIATION_ID = $variation AND DELETED=0");
        			if ($clid == "") {
        				$form2 = new YesNoForm($lang->get("cr_vr", "Create variation"), $lang->get("crclv_mes", "The cluster does not exists in this variation. Do you want to create it?"));
        				$form2->add(new Hidden("action", "cr_cluster"));
        				$form2->add(new Hidden("oid", $oid));
        				$form2->add(new Hidden("crvar", $variation));
        				$page->add($form2);
        				$handled = true;
        				$action = "foo";
        				$page_action = "UPDATE";
        			}
        		}
		}
		
		if ($action == $lang->get("cl_launch") && $aclf->checkAccessToFunction("B_EXPIRE_LAUNCH")) {
			launchCluster($clnid, 10, $variation);	
		} else if ($action == $lang->get("cl_expire") && $aclf->checkAccessToFunction("B_EXPIRE_LAUNCH")) {
			expireCluster($clnid, $variation);
		}
		
		if ($action == $lang->get("CL_DEL") && $aclf->checkAccessToFunction("DELETE_CL")) {
			//if (!isset($go)) $go = "start";
			if ($go == "0" || $go == "")
				$go = "start";

			$delhandler = new ActionHandler("deleteobject");

			if (value("decision") == $lang->get("yes")) {
				$delhandler->addDBAction("UPDATE cluster_node SET DELETED = 1 WHERE CLNID = $oid");

				$delhandler->addDBAction("UPDATE cluster_content SET FKID=0 WHERE FKID = $oid");
				$delhandler->addDBAction("UPDATE cluster_template_items SET FKID=0 WHERE FKID = $oid");
				
				$delhandler->process("deleteobject");
				$handled = true;
			} else if (value("decision") != $lang->get("no")) {
				$title = getDBCell("cluster_node", "NAME", "CLNID = $oid");
				$form = new YesNoForm($lang->get("cl_del"). " $title", $lang->get("cl_delmes", "Do you really want to delete this cluster-instance?"));
				$form->add(new Hidden("action", $lang->get("CL_DEL")));
				$form->add(new Hidden("oid", $oid));
				$page->add($form);
				$handled = true;				
			}
		} else if (($action == $lang->get("cl_new")) && $aclf->checkAccessToFunction("NEW_INSTANCE")) {
			if (!isset($go) || $go == "0")
				$go = "insert";

			$page_action = "INSERT";		
			$clt = getVar("clt");
			$form = new stdEDForm($lang->get("cl_new"), "i_edit.gif");
			$cond = $form->setPK("cluster_node", "CLNID");
			$oname = new TextInput($lang->get("name"), "cluster_node", "NAME", $cond, "type:text,width:200,size:32", "MANDATORY&UNIQUE");
			$oname->setFilter("CLT_ID = $clt");
			$form->add($oname);
			$form->add(new SelectMultiple2Input($lang->get("variations"), "cluster_variations", "VARIATION_ID", "1", "variations", "NAME", "VARIATION_ID", "DELETED=0"));
			$form->add(new Hidden("action", $lang->get("cl_new")));
			$form->add(new NonDisplayedValueOnInsert("cluster_node", "CLT_ID", $cond, $clt, "NUMBER"));
			$form->add(new NonDisplayedValueOnInsert("cluster_node", "DELETED", $cond, 0, "NUMBER"));
			$form->forbidDelete(true);
			$form->forbidUpdate(true);

			$handler = new ActionHandler("INSERT");
			$handler->addFncAction("syncClusterVariations");
			$form->registerActionHandler($handler);

			$page->add($form);
			// edit the properties of an object.
			$handled = true;

		} else if ($action != "foo" && ($action != "0"  || $view != "0")) {
			if ($action == "") $action = value("acstate");	

			if ($action == "createCluster") {
				$id = value("id");

				$clnid = createClusterNode(value("cluster_node_NAME".$id), $clt);
    		    $variations = createDBCArray("variations", "VARIATION_ID");
                for ($varX=0; $varX < count($variations); $varX++) {
                        $clid = createCluster($clnid, $variations[$varX], $auth->userName);
                }

	   		    $sql = "UPDATE cluster_content SET FKID = '$clnid' WHERE CLCID = '$id';";
				$query = new query($db, $sql);
				$query->getrow();
				
				// Trying to set action to Edit All, but it obviously doesn't work.
				$action = value("acstate");
			}
					
			$handled = true;
			$page_action = "UPDATE";

			// Fetch variables
			$view = value("view", "NUMERIC");

			if ($view == "0")
				$view = 1;
				
			$clnid = value("oid", "NUMERIC");
			
			$clt = getDBCell("cluster_node", "CLT_ID", "CLNID = $clnid");
			if ($view == 1)
				$variations = populateVariations($clnid, variation());

			$clid = getDBCell("cluster_variations", "CLID", "CLNID = $clnid AND VARIATION_ID = $variation");
			$name = getDBCell("cluster_node", "NAME", "CLNID = $clnid");
			$form = new PanelForm($name, "i_editc.gif", 'cl');

			// Order of Panels
			$pos_clusterPanel = 1;
			$pos_metaPanel = 2;
			$pos_objectProp = 3;

			if (! $aclf->checkAccessToFunction("ED_CL_META_DATA")) 
			  $pos_objectProp = 2;
			  
			// Process actions:
			if ($action == $lang->get("save") && $errors == "" && $search == "") {
				processSaveSets();

				if ($errors != "") {
					$form->addToTopText($lang->get("saveerror"));
				} else {
					$form->addToTopText($lang->get("savesuccess"));
				}
			}
			$clusterPanel = new Panel($lang->get("ed_content"));
			$clusterPanel->backto = $c["docroot"]."modules/cluster/clusterbrowser.php?sid=$sid&oid=$clnid&view=1";
			$propPanel = new Panel($lang->get("cl_properties"));
			$metaPanel = new Panel($lang->get("ed_meta"));			
			if ($view == $pos_clusterPanel && count($variations) > 0 && ($aclf->checkAccessToFunction("EDIT_CL_CONTENT"))) {
				require_once $c["path"] . "modules/common/panel_cluster.inc.php";
			} else if ($view == $pos_objectProp && $aclf->checkAccessToFunction("CL_PROPS")) {
				$mynode = getVar("cluster");
				$cond = "CLNID = $clnid";
				$oname = new TextInput($lang->get("cl_name"), "cluster_node", "NAME", $cond, "type:text,width:200,size:32", "MANDATORY&UNIQUE");
				$oname->setFilter("CLT_ID = $mynode");
				$propPanel->add(new Hidden("view", $view));
				$propPanel->add(new Hidden("processing", "yes"));

				$propPanel->add($oname);
				$propPanel->add(new SelectMultiple2Input($lang->get("variations"), "cluster_variations", "VARIATION_ID", $cond . " AND DELETED=0", "variations", "NAME", "VARIATION_ID", "DELETED=0"));
				$propPanel->add(new Hidden("action", "objectprop"));
				$propPanel->add(new FormButtons(true, true));
				$handler = new ActionHandler("UPDATE");
				$handler->addFncAction("syncClusterVariations");
				$propPanel->registerActionHandler($handler);
			} else if ($view == $pos_metaPanel && ($aclf->checkAccessToFunction("ED_CL_META_DATA"))) {
				require_once $c["path"] . "modules/common/panel_meta.inc.php";
			}

			// Build form
			if ($aclf->checkAccessToFunction("EDIT_CL_CONTENT"))
				$form->addPanel($clusterPanel);

			if ($aclf->checkAccessToFunction("ED_CL_META_DATA"))
				$form->addPanel($metaPanel);

			if ($aclf->checkAccessToFunction("CL_PROPS"))
				$form->addPanel($propPanel);

			$page->add($form);
		}
	}

	if (!$handled) {				
			$variation = variation();
			$go = "view";
			
			$add = "";
			if ($clt != 0) {
			  $add = ": ".getDBCell("cluster_templates", "NAME", "CLT_ID = $clt");				
			}
			$form = new Form($lang->get("cl_browse", "Browse Clusters").$add);
				
	
			if ($clt == 0) {
				$form->buttonbar->add("action", $lang->get("cl_new", "New Instance"), "submit", "document.form1.processing.value = '';", "", false);
				$form->buttonbar->add("separator", "", "", "", "");
			} else {
				$form->buttonbar->add("action", $lang->get("cl_new", "New Instance"), "submit", "document.form1.processing.value = '';");
				$form->buttonbar->add("separator", "", "", "", "");
			}
	
			include $c["path"]."modules/common/buttonbar_folder.inc.php";			
	
			// Build breadcrumb
			if ($pnode == "")
				$pnode = "0";
	
			$basehref = '<a href="' . $c["docroot"] . 'modules/cluster/clusterbrowser.php?sid=' . $sid . '&pnode=';
			$str_base = $basehref . '0">Content &gt;</a> ';
			$str = "";
			$tmp = $pnode;
	
			while ($tmp != "0") {
				$str = $basehref . "$tmp\">" . getDBCell("categories", "CATEGORY_NAME", "CATEGORY_ID = $tmp"). "	&gt;</a> " . $str;
				$tmp = getDBCell("categories", "PARENT_CATEGORY_ID", "CATEGORY_ID = $tmp");
			}
	
			$str = $str_base . $str;
			$form->add(new Spacer(2));
			$form->add(new Label("lbl", getBoxedText($str, 'headergrey', '100%'), "", 2));
			$form->add(new Spacer(2));
			// add contents
			if ($filter != "0") {
				pushVar("filter", $filter);
			} else {
				$filter = getVar("filter");
			}
	
			if ($linkset != "0") {
				pushVar("linkset", $linkset);
			} else {
				$linkset = getVar("linkset");
				if ($linkset == "") {
					$linkset = "LIB";
				}
			}
			if (strlen($linkset) > 3) $disableMenu = true;
	
			$filtersql = "";
			if ($filter != "") {
				$modules[0] = $filter;
	
				if (count($modules) > 0) {
					$f = 0;
	
					$module_id = getDBCell("modules", "MODULE_ID", "UPPER(MODULE_NAME) = '" . $modules[$f] . "'");
					$filtersql = " AND (MODULE_ID = $module_id ";
	
					for ($f = 1; $f < count($modules); $f++) {
						$module_id = getDBCell("modules", "MODULE_ID", "UPPER(MODULE_NAME) = '" . $modules[$f] . "'");
	
						$filtersql .= "OR MODULE_ID = $module_id ";
					}
	
					$filtersql .= ")";
				}
			}
	
			/*
					$content = createDBCArray("content", "CID", "VERSION=0 AND DELETED=0 AND CATEGORY_ID = ".$pnode.$filtersql, "ORDER BY NAME");
					 includePGNSources();
					  for ($i=0; $i < count($content); $i++) {
						$form->add( new LibraryViewer($content[$i], 2, $linkset, "image"));
					 }
					 if (count($content) == 0) {
					   $form->add(new Label("lbl", "<center>".$lang->get("no_contents", "No Contents available in this folder.")."</center>", "standard", 2));	
					 }
			*/
			// $content = createDBCArray("
			if ( $clnid == 0) {
												
				$titles = array($lang->get("name", "Name"));
				$rows = array();
				
				$sql = "SELECT NAME, CLNID FROM cluster_node WHERE CLT_ID = $clt AND DELETED = 0 AND VERSION = 0 ORDER BY NAME";
				$query = new query($db, $sql);
				
				while ($query->getrow()) {
					array_push($rows, array($query->field("CLNID"), $query->field("NAME")));
				}
				if (count($rows)>0) {
					$form->add(new EmbeddedMenu($titles, $rows, $c["docroot"]."modules/cluster/clusterbrowser.php?sid=$sid&view=1&oid="));
				} else {
					$form->add(new Label("lbl", "<center>" . $lang->get("no_cluster", "There are no clusters."). "</center>", "standard", 2));	
				}		
			}

			$form->add(new Hidden("action", ""));
			$form->add(new Hidden("pnode", $pnode));
			$page->add($form);

	}
?>