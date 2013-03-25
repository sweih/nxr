<?
	/**********************************************************************
	 * @module Application
	 **********************************************************************/
	require_once "../../config.inc.php";
	require_once $c["path"] . "modules/sitepages/sitemap_menu2.inc.php";
	// include extra language file
	require_once $c["path"] . "modules/cluster/lang_clusterbrowser.inc.php";
	$auth = new auth("EXPLORE_SITE_S");
	$page = new page("Sitepage Browser");
	$page->setJS('TREE');
	$action = value("action");

	$mid = value("mid", "NUMERIC");
	$spid = value("oid", "NUMERIC");	
	$isDeletable = (defined('ADMINMODE') || (getDBCell("sitepage", "DELETABLE", "SPID = $spid")=='1'));
	
	if ($mid == "0" && $spid != "0") 
		$mid = getDBCell("sitepage", "MENU_ID", "SPID = $spid");
	if ($mid == "") $mid = "0";

		if (value("jump") != "0") {
		  $oid = value("oid");
		  if (! is_numeric($oid)) $oid="-1";

		if (countRows("sitepage", "SPID", "SPID=$oid") > 0) {
			// check if is live id...
			$check = getDBCell("state_translation", "IN_ID", "OUT_ID=$oid");

			if ($check != "")
				$oid = $check;

			// start processing
			$spm = getDBCell("sitepage", "SPM_ID", "SPID=$oid");
			$sptype = getDBCell("sitepage_master", "SPMTYPE_ID", "SPM_ID = $spm");

			if ($sptype == 1) {
				$action = "editobject";

				$omid = getDBCell("sitepage", "MENU_ID", "SPID=$oid");
				$mid = getDBCell("sitemap", "PARENT_ID", "MENU_ID = $omid");
				$db->close;
				header ("Location: " . $c["docroot"] . "modules/sitepages/sitepagebrowser.php?sid=$sid&go=update&action=$action&oid=$oid&mid=$mid");
				exit;
			} else if ($sptype == 2) {
				$action = "editobject";

				$mid = getDBCell("sitepage", "MENU_ID", "SPID=$oid");

				$db->close;
				header ("Location: " . $c["docroot"] . "modules/sitepages/sitepagebrowser.php?sid=$sid&go=update&action=$action&oid=$oid&mid=$mid");
				exit;
			}
		} else {
			$form = new MessageForm($lang->get("not_found", "Sorry, I found nothing"), $lang->get("pnf", "The page with the ID you entered was not found."), $c["docroot"] . "modules/sitepages/sitepagebrowser.php?sid=$sid");
			$page->add($form);
			$page->draw();
			$db->close();
			exit;
		}
	}
	
	
	//// ACL Check ////
	$aclf = aclFactory($mid, "page");
	$aclf->load();
	if (! $aclf->hasAccess($auth->userId)) {		
		$mid="0";
		pushVar("mid", "0");
		$aclf = aclFactory(0, "page");
		$aclf->load();		
		if (! $aclf->hasAccess($auth->userId)) 
		   
		   header("Location: ". $c["docroot"]."modules/common/noaccess.php?sid=$sid&guid=$pnode");
	}
	//// ACL Check ////
	
	if ( ! ($aclf->checkAccessToFunction("SET_PAGE_ACCESS")
		 || $aclf->checkAccessToFunction("EDIT_CONTENT")
		 || $aclf->checkAccessToFunction("EDIT_META_DATA")
		 || $aclf->checkAccessToFunction("SITEPAGE_PROPS")
		 || $aclf->checkAccessToFunction("MENU"))) {
		   header("Location: ". $c["docroot"]."modules/common/noaccess.php?sid=$sid&guid=$pnode");			
	}
	
	$variation = variation();

	if ($action == "0")
		$action = value("acstate");
    if (value("saction") != "0") {
          $action = getVar("lastaction");
    }

    pushVar("lastaction", $action);
	if ($action == $lang->get('resetcli') && $aclf->checkAccessToFunction("B_RESET_CL_INST")) {
		$spid = value("spid");

		$sql = "UPDATE sitepage SET CLNID=0 WHERE SPID = $spid";
		$query = new query($db, $sql);
		$query->free();
		$action = "editobject";
		$go = "CREATE";
		$processing = "no";
		$page_state = "start";
	}

	if ($action == "newpage" && $aclf->checkAccessToFunction("ADD_SITEPAGE")) {
		if ($go == "insert")
			$page_action = "INSERT";
		$mid = initValueEx("mid", "mid", "0");
		$form = new stdEDForm($lang->get("sp_newpage"), "i_edit.gif");
		$cond = $form->setPK("sitemap", "MENU_ID");
		$form->add(new IconSelectInput($lang->get("tmpl_name", "Select a template"), "sitemap", "SPM_ID", "sitepage_master", "NAME", "SPM_ID", "DESCRIPTION", $c["spmthumbdocroot"], "THUMBNAIL", "DELETED=0 AND VERSION=0", $cond, "type:dropdown", "MANDATORY"));
		$oname = new TextInput($lang->get("stname", "Select a name to identify this page in the sitemap"), "sitemap", "NAME", $cond, "type:text,width:200,size:32", "MANDATORY&UNIQUE");
		$oname->setFilter("PARENT_ID = $mid");
		$form->add($oname);
		$form->add(new SitemapPosition($lang->get("pos_in_menu"), "sitemap", "POSITION", $cond, 0, $mid));			
		$form->add(new Label("lbl", $lang->get("cr_content", "Create Content for this page?"), "standard"));
		$form->add(new Checkbox("createpage", "1", "standard", true));
		$form->add(new ActionField("newpage"));
		$form->add(new NonDisplayedValueOnInsert("sitemap", "PARENT_ID", $cond, $mid, "NUMBER"));
		$form->add(new NonDisplayedValueOnInsert("sitemap", "DELETED", $cond, 0, "NUMBER"));
		$form->add(new NonDisplayedValueOnInsert("sitemap", "IS_DISPLAYED", $cond, 1, "NUMBER"));
		$form->forbidDelete(true);
		$form->forbidUpdate(true);

		$handler = new ActionHandler("INSERT");
		$handler->addFncAction("createMenuEntry");
		$handler->addFncAction("createContentPage");
		$form->registerActionHandler($handler);

		$page->add($form);
	} else if ($action == "newinstance") {
		if ($go == "insert")
			$page_action = "INSERT";

		$mid = initValue("mid", "mid","0");
		$spm = getDBCell("sitemap", "SPM_ID", "MENU_ID = $mid");
		$type = getDBCell("sitepage_master", "SPMTYPE_ID", "SPM_ID = $spm");

		if ($type == 2) {
			$form = new stdEDForm($lang->get("sp_newinstance"), "");

			$form->add(new MultipagePosition($lang->get("pos_in_menu"), "sitepage", "POSITION", "SPID=0", 0, $mid));			
			$form->add(new Label("lbl", $lang->get("cr_content", "Create Content for this page?"), "standard"));
			$form->add(new Checkbox("createpage", "1", "standard", true));	
			$form->add(new Label("lbl", $lang->get("cl_name", "Cluster Name"), "standard", 1));
			$form->add(new Input("cluster_name", $lang->get("insname", "<Cluster Name>"), "standard", 32));
			
			$form->add(new ActionField("newinstance"));
			$form->forbidDelete(true);
			$form->forbidUpdate(true);
			$handler = new ActionHandler("INSERT");
			$handler->addFncAction("createPage");
			$form->registerActionHandler($handler);
			$page->add($form);
		} // if type==2
	} else if ($action == $lang->get("delete") && $aclf->checkAccessToFunction("DELETE_SITEPAGE")) {
		if (!isset($go))
			$go = "start";

		$delhandler = new ActionHandler("deleteobject");

		if ($go != $lang->get("Cancel")) {
			if (value("deleteobject") != "0") {
				$mid = getVar("mid");
				$posi = getDBCell("sitepage", "POSITION", "SPID = $oid");
				$delhandler->addDBAction("UPDATE sitepage SET POSITION = (POSITION-1) WHERE POSITION > $posi AND MENU_ID = $mid");
				$delhandler->addDBAction("DELETE FROM sitepage WHERE SPID = $oid");
				$delhandler->addDBAction("DELETE FROM sitepage_names WHERE SPID = $oid");
				$spm = getDBCell("sitepage", "SPM_ID", "SPID=$oid");
  			$sptype = getDBCell("sitepage_master", "SPMTYPE_ID", "SPM_ID = $spm");
				if ($sptype!=2) {				  
				  $currMid = getDBCell("sitepage", "MENU_ID", "SPID=".$spid);
				  $delhandler->addDBAction("DELETE FROM sitemap WHERE MENU_ID =$currMid");
				}

			//include live site also!!!
			//$delhandler->addDBAction("UPDATE cluster_template_items SET FKID=0 WHERE FKID=$oid");
			}

			$title = getMenu($oid, 1);
			$form = new CommitForm($lang->get("sp_pdelete"). " $title[0]", "i_purge.gif");
			$form->addToTopText($lang->get("sp_pdelmes"));
			$form->addCheck("deleteobject", $lang->get("sp_pdelete"), $delhandler);
			$form->add(new ActionField($lang->get("delete")));
			$form->add(new Hidden("oid", $oid));
			$page->add($form);
		}
	} else if ($action == $lang->get("delete_page") && $aclf->checkAccessToFunction("DELETE_SITEPAGE")) {
		if (!isset($go))
			$go = "start";

		$delhandler = new ActionHandler("deleteobject");

		if ($go != $lang->get("Cancel")) {				
			$mid = getDBCell("sitepage", "MENU_ID", "SPID = $oid");
			if (value("deleteobject") != "0") {			
				$posi = getDBCell("sitemap", "POSITION", "MENU_ID = $mid");
				$delhandler->addDBAction("UPDATE sitemap SET POSITION = (POSITION-1) WHERE POSITION > $posi AND PARENT_ID = $mid");
				$delhandler->addDBAction("UPDATE sitemap SET DELETED = 1 WHERE MENU_ID = $mid");
			}

			$title = getDBCell("sitemap", "NAME", "MENU_ID = $mid");
			$form = new CommitForm($lang->get("sp_delete"). " $title", "i_purge.gif");
			$form->addToTopText($lang->get("sp_delmes"));
			$form->addCheck("deleteobject", $lang->get("sp_delete"), $delhandler);
			$form->add(new ActionField($lang->get("delete_page")));
			$form->add(new Hidden("oid", $oid));
			$page->add($form);
		}
	} else if (value("pnode", "NUMERIC") == "0" && $oid ==0 && $auth->userName == "Administrator") {
	 	$go="UPDATE";
	 	$form = new EditForm($lang->get("node_access", "Set access for site root"), ""); 
	 	$aclPanel = new Container;	 		 	
		$aclid = "1";
		$aclType = "page";
		$title = $name[0];
		include $c["path"] . "api/userinterface/panels/acl_panel.inc.php";	
		$aclPanel->add(new Hidden("mid", $mid));
		$aclPanel->add(new Hidden("oid", $spid));
		$aclPanel->add(new Hidden("view", $view));
		$aclPanel->add(new Hidden("processing", "yes"));			
  	 	$form->add($aclPanel);
	 	$page->add($form);
	 
	} else if ($action != "0" || value("view") != "0") {
		if ($action =="") $action = value("acstate");
		$page_action = "UPDATE";

		// Fetch variables
		$view = initValue("view", doc()."view", 1);

		if ($view == "0")
			$view = 1;

		$oid = value("oid", "NUMERIC");

		if ($action == "pproperties" || (value("acstate") == "pproperties")) {
			// oid containes $menuID;
			$menuID = $oid;
			$spm = getDBCell("sitemap", "SPM_ID", "MENU_ID = $menuID");
			$clt = getDBCEll("sitepage_master", "CLT_ID", "SPM_ID = $spm");
			$type = getDBCell("sitepage_master", "SPMTYPE_ID", "SPM_ID = $spm");
			$spid = getDBCell("sitepage", "SPID", "MENU_ID = $menuID");

			if ($type == 1) {
				$clnid = getDBCell("sitepage", "CLNID", "MENU_ID = $menuID");

				if ($clnid != "0" && $clnid != 0) {
					$clid = getDBCell("cluster_variations", "CLID", "CLNID = $clnid AND VARIATION_ID = $variation");
				}
			}
		} else {
			$spid = value("oid", "NUMERIC");

			if ($spid != "") {
				$clnid = getDBCell("sitepage", "CLNID", "SPID = $spid");

				if ($view == 1 || $view == 4)
					$variations = populateSPVariations($spid, $clnid, $variation);

				$clid = getDBCell("cluster_variations", "CLID", "CLNID = $clnid AND VARIATION_ID = $variation");
				$spm = getDBCell("sitepage", "SPM_ID", "SPID = $spid");
				$clt = getDBCell("sitepage_master", "CLT_ID", "SPM_ID = $spm");
				$type = getDBCell("sitepage_master", "SPMTYPE_ID", "SPM_ID = $spm");
				$menuID = getDBCell("sitepage", "MENU_ID", "SPID = $spid");
			}
		}

		if (($action == "pproperties" || value("acstate") == "pproperties") && $type != 1) {
			$name[0] = getDBCell("sitemap", "NAME", "MENU_ID=$menuID");	
		} else if ($spid != "")
			$name = getMenu($spid, 1);

		// Determine position of panels
		$nextPosition = 1;
		$mpProp = ($action == "pproperties" || value("acstate") == "pproperties") && ($type > 1);
		
		if ($aclf->checkAccessToFunction("EDIT_CONTENT")  && !$mpProp) {
			$pos_clusterPanel = $nextPosition;
			$nextPosition ++;
		}
		
		if ($aclf->checkAccessToFunction("EDIT_META_DATA") && ! $mpProp) {
			$pos_metaPanel = $nextPosition;
			$nextPosition ++;	
		}
		
		if ($aclf->checkAccessToFunction("SITEPAGE_PROPS") && $isDeletable) {
			$pos_objectProp = $nextPosition;
			$nextPosition ++;	
		}
		
		if ($aclf->checkAccessToFunction("MENU")) {
			$pos_menuPanel = $nextPosition;
			$nextPosition++;	
		}
		
		if ($aclf->checkAccessToFunction("SET_PAGE_ACCESS")) {
			$pos_aclPanel = $nextPosition;
			$nextPosition++;
		}

		
		// Create headline for the form
		$addendum = "( " . $spid;
		if (!$mpProp) {
			if ($spid != "")
				$out = getDBCell("state_translation", "OUT_ID", "IN_ID = " . $spid);
			if ($out != "") {
				$addendum .= " / " . $out. ' )';
			}
		}

		if (($action == "iproperties" || $action == "pproperties") && value("acstate") != "pproperties")
			$view = $pos_objectProp;

		$headline='<table width="100%" border="0" cellspacing="0" cellpadding="0"><tr><td class="h2">'.$name[0].'</td><td align="right">'.$addendum.'</td></tr></table>';
		
		$form = new PanelForm($headline, '');

		// Process actions:
		if ($action == $lang->get("save") && $errors == "") {
			processSaveSets();
			if ($errors != "") {
				$form->addToTopText($lang->get("saveerror"));
			} else {
				$form->addToTopText($lang->get("savesuccess"));
			}
		}

		$clusterPanel = new Panel($lang->get("ed_content"));
		$clusterPanel->backto = $c["docroot"]."modules/sitepages/sitepagebrowser.php?sid=$sid&oid=$oid&go=update&action=editobject";
		$propPanel = new Panel($lang->get("sp_properties"));
		$metaPanel = new Panel($lang->get("ed_meta"));
		$menuPanel = new Panel($lang->get("menu"));
		$aclPanel = new Panel($lang->get("edit_access"));
		
		// check, whether a variation change was performed. if not, do action.
		if (value("action") != "0") {
			if ($action == $lang->get("sp_launch") || $action == $lang->get('launch_tree') || $action == $lang->get("sp_rltree", "Refresh Tree")) {
				if ($spid != "")
					$title = getDBCell("sitepage_names", "NAME", "VARIATION_ID = $variation AND SPID = $spid");

				if ($title != "") {
					includePGNSources();

					if ($action == $lang->get("sp_rltree", "Refresh Tree") && $aclf->checkAccessToFunction("B_RELAUNCH_TREE")) {
						$mymenu = getDBCell("sitepage", "MENU_ID", "SPID=$spid");

						relaunchMenuTree($mymenu, $variation);
					} elseif ($action == $lang->get('launch_tree') && $aclf->checkAccessToFunction("B_LAUNCH_TREE")) {
						$mymenu = getDBCell("sitepage", "MENU_ID", "SPID=$spid");

						launchMenuTree($mymenu, $variation);
					} else if ($aclf->checkAccessToFunction("B_EXPIRE_LAUNCH")) {
						launchSitepage($spid, 10, $variation);
					}
				} else {
					$clusterPanel->addToTopText($lang->get("nosptitle"));
					$clusterPanel->setTopStyle("errorheader");
				}
			} else if ($action == $lang->get("sp_expire") && $aclf->checkAccessToFunction("B_EXPIRE_LAUNCH")) {
				expireSitepage($oid, 10, $variation);
			}
			else if ($action == $lang->get("rb_cache")) {
				// rebuildCache();	
				} else if ($action == $lang->get('exp_tree') && $aclf->checkAccessToFunction("B_EXPIRE_TREE")) {
				$mymenu = getDBCell("sitepage", "MENU_ID", "SPID=$spid");

				expireMenuTree($mymenu, $variation);
			} else if ($action == $lang->get('del_tree') && $aclf->checkAccessToFunction("B_DESTROY_TREE")) {
				$mymenu = getDBCell("sitepage", "MENU_ID", "SPID=$spid");
				header ("Location: destroy_tree.php?sid=$sid&del=$mymenu");
				exit;
			}
		}
		if ($clnid == "0" && ($type == 1 || ($type == 2 && ($action != "pproperties" && value("acstate") != "pproperties")))) {
			require_once $c["path"] . "modules/sitepages/logic_selectcluster.inc.php";
		} else if ($view == $pos_clusterPanel && count($variations) > 0 && $aclf->checkAccessToFunction("EDIT_CONTENT")) {
			$sitepage = true;
			require_once $c["path"] . "modules/common/panel_cluster.inc.php";
		} else if ($view == $pos_objectProp && $aclf->checkAccessToFunction("SITEPAGE_PROPS")) {
			if ($isDeletable) {
			  require_once $c["path"] . "modules/common/panel_spproperties.inc.php";
			}
		} else if ($view == $pos_metaPanel && $aclf->checkAccessToFunction("EDIT_META_DATA")) {
			require_once $c["path"] . "modules/common/panel_meta.inc.php";
		} else if (($view == $pos_menuPanel) && $aclf->checkAccessToFunction("MENU")) {
			require_once $c["path"] . "modules/common/panel_menu.inc.php";
		} else if (($view == $pos_aclPanel) && $aclf->checkAccessToFunction("SET_PAGE_ACCESS")) {
			require_once $c["path"] . "modules/common/panel_page_access.inc.php";
		}

		// Build form
		if ($mpProp) {
			$addLink = "&acstate=pproperties";
		}

		if (!$slcluster) {
			if ($type != 3 && !$mpProp) {
				if ($aclf->checkAccessToFunction("EDIT_CONTENT")) 
					$clusterPanel->add(new ActionField(""));
					$form->addPanel($clusterPanel);

				if ($aclf->checkAccessToFunction("EDIT_META_DATA"))
					$form->addPanel($metaPanel);
			}

			if ($aclf->checkAccessToFunction("SITEPAGE_PROPS") && $isDeletable) 
				$form->addPanel($propPanel);
		
			if ($aclf->checkAccessToFunction("MENU"))
				$form->addPanel($menuPanel);
			
		    	if ($aclf->checkAccessToFunction("SET_PAGE_ACCESS")) 
		      		$form->addPanel($aclPanel); 		    	
		}
		
		if (count($form->panelcontainer) < $form->view) $form->view=0;
		
		$page->add($form);
	}

	$menu = new SitemapMenu2($lang->get("sp_browse"));
	$menu->tipp = $lang->get("help_sp", "Sitemap is used for editing site structure and pages.");
	$page->addMenu($menu);

	if (!$mpProp)
		$oid = $spid;
	$page->draw();
	$db->close();
	echo $errors;
?>