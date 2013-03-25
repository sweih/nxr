<?php
	// Define Buttonbars

	if (!file_exists($c["path"] . "index.php")) {
		die ("Path not found.");
	}
	if ((value("changevariation") != "0" && value("changevariation") != "") || (value("changetranslation") != "0" && value("changetranslation") != ""))
		$page_state = "";

	$clusterEditState = (($action == $lang->get("edit_all")) || ($action == $lang->get("save") || $action == "editsingle" || value("status") == "editsingle") && $action != $lang->get("back")  && $action != $lang->get("save_back")) || $isArticle;
	if ($action=="") $action = value("acstate");

	$editState = (($action == $lang->get("edit_all")) || ($action == $lang->get("save") || $action == $lang->get("save_back") || $action == "editsingle" || value("status") == "editsingle") && $action != $lang->get("back")) || $isArticle;
	$clustereditbar = new ButtonBar("clusteredtbar");
	if (($aclf->checkAccessToFunction("EDIT_CONTENT") || $aclf->checkAccessToFunction("EDIT_CL_CONTENT"))) {
             if (!$editState)  {
          		if ($aclf->checkAccessToFunction("B_EDIT_ALL") && ! $isArticle)
					$clustereditbar->add("action", $lang->get("edit_all"), "submit", "document.form1.processing.value='';");
             }

             if ($aclf->checkAccessToFunction("B_USAGE")  && ! $isArticle)
				
				$clustereditbar->addAction($lang->get('usage', 'Usage'), "window.open('" . $c["docroot"] . "modules/common/cluster_usage.php?oid=" . $clnid . "&sid=$sid', 'usage','Width=300px,Height=500px,help=no,status=yes,scrollbars=yes,resizable=yes');");
				if (!$sitepage) {
					if (! $isArticle) {
						if ($aclf->checkAccessToFunction("DELETE_CL"))
						  $clustereditbar->add("action", $lang->get("CL_DEL"), "submit", getWaitupScreen() ."; document.form1.processing.value='';", "", !getDBCell("channel_articles", "CHID", "ARTICLE_ID = ".$clnid));
						if ($aclf->checkAccessToFunction("LAUNCH_CL")) {
						  $clustereditbar->addAction('separator');
						  $clustereditbar->addAction($lang->get("cl_launch", "Launch Cluster"));
						  $clustereditbar->addAction($lang->get("cl_expire", "Expire Cluster"));
						}
					} else {
						if ($aclf->checkAccessToFunction("CHANNEL_LAUNCH")) {
							$clustereditbar->add("laction", $lang->get("ar_launch", "Launch Article"), "submit", getWaitupScreen());
							$clustereditbar->add("laction", $lang->get("ar_expire", "Expire Article"), "submit", getWaitupScreen());
						}
						
                        if ($aclf->checkAccessToFunction("CHANNEL_DELETE")) 
                          $clustereditbar->addConfirm("action", $lang->get("delete"), $lang->get("del_article", "Do you really want to delete this article in all variations?"), $c["docroot"]."modules/channels/overview.php?sid=$sid&action=deletearticle&article=$oid");
					}
					// add compound preview...
					if (getDBCell("cluster_templates", "CLT_TYPE_ID", "CLT_ID=".$clt) == 1) {
						if ($aclf->checkAccessToFunction("B_PREVIEW_PAGE")) 
						  $clustereditbar->addAction($lang->get("preview", "Preview"), "window.open('" . $c["docroot"] . "modules/common/previewcpcl.php?sid=$sid&oid=$clnid', 'Preview','Width=400px,Height=400px,help=no,status=yes,scrollbars=yes,resizable=yes');");
					}
				}

				if ($sitepage) {
					// draw customized job buttons...
					$path = getDBCell("sitepage_master", "TEMPLATE_PATH", "SPM_ID = $spm");

					if ($aclf->checkAccessToFunction("B_LIVE_AUTHORING"))
						$clustereditbar->addAction($lang->get("wysiwyg", 'WYSIWYG Editor'), "window.open('" . $c["docroot"] . "modules/sma/sma.php?page=$spid&v=$variation&sid=$sid', 'Preview','Width=800px,Height=700px,help=no,status=yes,scrollbars=yes,resizable=yes');");
				}

                if (! $isArticle) {
                    $clustereditbar->setVariationSelector($variations, $variation);
                }

	}


	if ($isArticle) {
		$clustereditbar->setVariationSelector(getChannelVariations($chid), $variation);
	}

	if (($sitepage && !($action == $lang->get("edit_all") || $action == $lang->get("save") || $action == $lang->get("save_back") || $action == "editsingle" || $action == "delete" || $action == "addci")) && $aclf->checkAccessToFunction("B_PREVIEW_PAGE"))
		$clustereditbar->add("preview", $lang->get("sp_preview"), "BUTTON", "window.open('" . $c["devdocroot"] . $path . "?page=$spid&v=$variation', 'Preview','Width=800px,Height=600px,help=no,status=yes,scrollbars=yes,resizable=yes');");

	if (isset($clustereditbar) && (! $clusterEditState || $isArticle))  {
           $clusterPanel->add($clustereditbar);
    }
		
	
	$clustereditbar->addAction('separator');
    if ($sitepage && ($aclf->checkAccessToFunction("B_EXPIRE_LAUNCH") || $aclf->checkAccessToFunction("B_LAUNCH_TREE") || $aclf->checkAccessToFunction("B_RELAUNCH_TREE") || $aclf->checkAccessToFunction("B_EXPIRE_TREE") || $aclf->checkAccessToFunction("B_DESTROY_TREE"))
		&& !($action == $lang->get("edit_all") || $action == $lang->get("save")  || $action == $lang->get("save_back") || $action == "editsingle")) {
		if ($aclf->checkAccessToFunction("B_EXPIRE_LAUNCH")) {
			if ($spid != "") {
				if (isSPVarLiveEx($spid, $variation)) {
					$clustereditbar->addAction($lang->get("sp_expire"));
				}
				$clustereditbar->add("action", $lang->get("sp_launch"), "submit", getWaitupScreen());					
				$clustereditbar->addAction($lang->get('sp_launch'));
			}
		}

	if ($sitepage && $aclf->checkAccessToFunction("DELETE_SITEPAGE")) {
        $mid = getDBCell("sitepage", "MENU_ID", "SPID = $oid");
		$spm = getDBCell("sitepage", "SPM_ID", "SPID=$oid");
		$sptype = getDBCell("sitepage_master", "SPMTYPE_ID", "SPM_ID = $spm");
		$clustereditbar->addAction('separator');
	  	if ($sptype == 1) {
	  		if (countRows("sitemap", "MENU_ID", "PARENT_ID=$mid") == 0) {
	  	  	    if (!isSPLive($oid) && $isDeletable)
	  	  		$clustereditbar->addAction($lang->get("delete_page", "Delete Page"));
	  	  	}
	  	} else if ($sptype == 2) {
	  	 	if (!isSPLive($oid) && $isDeletable)
	  	 	  $clustereditbar->add($lang->get("delete"));
	  	}
	}

		
		if ($aclf->checkAccessToFunction("B_LAUNCH_TREE"))
			$clustereditbar->addAction($lang->get('launch_tree', "Launch Tree"));

		if ($aclf->checkAccessToFunction("B_RELAUNCH_TREE"))
			$clustereditbar->addAction($lang->get("sp_rltree", "Refresh Tree"));

		if ($aclf->checkAccessToFunction("B_EXPIRE_TREE"))
			$clustereditbar->addAction($lang->get('exp_tree', 'Expire Tree'));

		if ($aclf->checkAccessToFunction("B_DESTROY_TREE") && $isDeletable) {
			$clustereditbar->addAction('separator');
			$clustereditbar->addAction($lang->get('del_tree', 'Delete Tree'));
		}

	}


	if (! $c["disableTranslationHelp"] && $editState && (countRows("variations", "VARIATION_ID", "1") > 1)) {
		$toolbar = new Buttonbar("Toolbar");
		$toolbar->selectBoxDescr = true;
		$toolbar->setTranslationSelector(createNameValueArrayEx("variations", "NAME", "VARIATION_ID", "VARIATION_ID <>".variation()), translation());
		if (! $isArticle)
		  $toolbar->setVariationSelector(populateVariations($clnid, variation()), variation());
		$clusterPanel->add($toolbar);
	}

	// PANELS
	// Edit-Content Panel.
	// include needed sources
	includePGNISources(); //Plugin Sources.

	$clid = getClusterFromNode($clnid, $variation);

	if ($view == $pos_clusterPanel)
		syncCluster ($clid);

	$clusterPanel->add(new Hidden("view", $view));
	$clusterPanel->add(new Hidden("oid", value("oid", "NUMERIC")));
	if (!$clusterEditState || $isArticle) $clusterPanel->add(new ActionField("panel_cluster"));

	if ($action == $lang->get("edit_all") || $action == $lang->get("save")) {
		$clusterPanel->add(new Hidden("processing", "yes"));
	}

	// start processing of content-items

	// GET CONTENT OF THE CLUSTER
	// set variables that will contain the content later to null.
	$clusters = null;
	$plugins = null;
	$types = null;

	// get the structure of the content.
	$sql = "SELECT CLTI_ID, CLTITYPE_ID FROM cluster_template_items WHERE CLT_ID = $clt AND FKID<>0 ";
	if (! $aclf->checkAccessToFunction("EDIT_EXCLUSIVE")) {
	  $sql.="AND EXCLUSIVE = 0 ";	
	} 
	$sql.="ORDER BY POSITION";
	$query = new query($db, $sql);

	while ($query->getrow()) {
		$cltitype = $query->field("CLTITYPE_ID");

		$ni = count($plugins);
		$plugins[$ni] = $query->field("CLTI_ID");
		$types[$ni] = $cltitype;
	}

	$query->free();

	// draw the back link in clusters-editor
	if (! $sitepage && ! $isArticle) {
		$clusterPanel->add(new LinkLabel("link1", $lang->get("back_to_cv", "Back to cluster overview"), "modules/cluster/clusterbrowser.php?sid=$sid&clt=$clt", "_self", "informationheader", 2));
	}

	// draw some cluster-information.
	$clusterPanel->add(new Spacer(3));
	$clusterPanel->add(new Subtitle("", "<b>".$lang->get("cluster_information", "Information about this record")."</b>", 3));
	$clusterPanel->add(new ClusterInformation($clid));
    $clusterPanel->add(new Spacer(3));    
	
    $clusterPanel->add(new SingleHidden("saction", ""));
	// draw plugin preview.
	$len = count($plugins);
	for ($i = 0; $i < $len; $i++) {
		if ($types[$i] == 2)
			$clusterPanel->add(new ContentEnvelope($plugins[$i], $clid));

		if ($types[$i] == 4)
			$clusterPanel->add(new ClusterEnvelope($plugins[$i], $clid));

		if ($types[$i] == 5)
			$clusterPanel->add(new LibraryEnvelope($plugins[$i], $clid));

		if ($types[$i] == 6)
			$clusterPanel->add(new CompoundClusterEnvelope($plugins[$i], $clid));

        if ($types[$i] == 8)
          $clusterPanel->add(new ChannelEnvelope($plugins[$i], $clid));
	}

	if ($isArticle || $action == $lang->get("edit_all") || ($action == $lang->get("save")) || $action == $lang->get("save_back")) {
       	$clusterPanel->add(new NonDisplayedValue("cluster_variations", "LAST_CHANGED", "CLID = ".$clid, "(NOW()+0)", "NUMBER"));
		$clusterPanel->add(new NonDisplayedValue("cluster_variations", "LAST_USER", "CLID = ".$clid, $auth->userName, "TEXT"));
	}

	// todo: ACL-CHecks
	//if ($action == $lang->get("edit_all") || $action == $lang->get("save") || $action == "editsingle") {
	//	$clusterPanel->add(new FormButtons());
	//}

	if ($clusterEditState && ! $isArticle) {
		$clusterPanel->add(new FormButtons());	
	} else if ($isArticle) {
        $clusterPanel->add(new Hidden("laction", ""));
        $clusterPanel->add(new FormButtons());
	}
	

// end processing of content-items    
    	 
?>