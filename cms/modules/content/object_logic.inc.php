<?
	/**********************************************************************
	 *@module Application
	 **********************************************************************/
	if (! $aclf->hasAccess($auth->userId))
	  header("Location: ". $c["docroot"]."modules/common/noaccess.php?sid=$sid&guid=$pnode");
	
	 // process object actions.
	$linkset = value("linkset", "NOSPACES");
	$sname = value("sname", "NOSPACES");
	$filter = value("filter", "NOSPACES");
	
	if ($action != "0") {
	
		/** Create a new object **/
		if ($action == "launch" && $aclf->checkAccessToFunction("OBJECT_LAUNCH")) {
			launchContent($oid, 10, $variation);
			if ($errors == "") {
				$topText = $lang->get("objlaunch_success", "The object was successfully launched.");
			}	
		}
		
		if ($action == $lang->get("new_content") && $aclf->checkAccessToFunction("NEW_OBJECT")) {
			$handled = true;

			if (!isset($go) || $go == "0")
				$go = "insert";

			if ($go == "insert")
				$page_action = "INSERT";
			$form = new stdEDForm($lang->get("o_new"), "i_edit.gif");
			$cond = $form->setPK("content", "CID");
			$oname = new TextInput($lang->get("o_name"), "content", "NAME", $cond, "type:text,width:200,size:32", "MANDATORY&UNIQUE");
			$oname->setFilter("CATEGORY_ID = $pnode");
			$form->add($oname);
			$form->add(new SelectOneInput($lang->get("type"), "content", "MODULE_ID", "modules", "MODULE_NAME", "MODULE_ID", "MODULE_TYPE_ID=1", "1", "type:dropdown", "MANDATORY"));
			$form->add(new SelectMultiple2Input($lang->get("variations"), "content_variations", "VARIATION_ID", "1", "variations", "NAME", "VARIATION_ID", "DELETED=0"));
			$form->add(new SelectOneInput($lang->get("metatemplate"), "content", "MT_ID", "meta_templates", "NAME", "MT_ID", "INTERNAL=0 AND VERSION=0", "1", "type:dropdown", "MANDATORY"));
			$form->add(new TextInput($lang->get("description"), "content", "DESCRIPTION", $cond, "type:textarea, width:300,size:3", ""));
			$form->add(new TextInput($lang->get("keywords"), "content", "KEYWORDS", $cond, "type:textarea, width:300,size:3", ""));
			$form->add(new Hidden("action", "newobject"));
			$form->add(new NonDisplayedValueOnInsert("content", "CATEGORY_ID", $cond, $pnode, "NUMBER"));
			$form->add(new NonDisplayedValueOnInsert("content", "CREATED", $cond, "", "TIMESTAMP"));
			$form->add(new NonDisplayedValue("content", "LAST_MODIFIER", $cond, $auth->user, "TEXT"));
			$form->add(new NonDisplayedValue("content", "LAST_MOD_DATE", $cond, "", "TIMESTAMP"));
			$form->add(new NonDisplayedValueOnInsert("content", "DELETED", $cond, 0, "NUMBER"));
			$form->add(new Hidden("action", $lang->get("new_content")));
			$form->forbidDelete(true);
			$form->forbidUpdate(true);

			$handler = new ActionHandler("INSERT");
			$handler->addFncAction("syncVariations");
			$form->registerActionHandler($handler);

			$page->add($form);
		// delete an object
		} else if ($action == "delobject" && $aclf->checkAccessToFunction("DELETE_OBJECT")) {
			if (!isset($go))
				$go = "start";

			$delhandler = new ActionHandler("deleteobject");

			if (value("decision") == $lang->get("yes")) {
				$delhandler->addDBAction("UPDATE content SET DELETED = 1 WHERE CID = $oid");

				$delhandler->addDBAction("UPDATE cluster_template_items SET FKID=0 WHERE FKID=$oid");
				$delhandler->process("deleteobject");
			} else if (value("decision") != $lang->get("no")) {
				$title = getDBCell("content", "NAME", "CID = $oid");

				$form = new YesNoForm($lang->get("o_del"). " $title", $lang->get("o_delmes2", "Do you really want to delete this content? It may still be used in some clusters."));
				$form->add(new Hidden("action", "delobject"));
				$form->add(new Hidden("oid", $oid));
				$page->add($form);
				$handled = true;
			}
		}
	}

	if (!$handled) {
		$variation = variation();

		$go = "view";
		$form = new Form($lang->get("library", "Content Library"));
		$form->cols = 3;

		if ($aclf->checkAccessToFunction("NEW_OBJECT")) {
		  $form->buttonbar->add("action", $lang->get("new_content", "New Content"), "submit", "document.form1.processing.value = '';");
		  $form->buttonbar->add("separator", "", "", "", "");		

		}
		include $c["path"] . "modules/common/buttonbar_folder.inc.php";


		// Build breadcrumb
		if ($pnode == "")
			$pnode = "0";

		$str = pathToRootFolder($pnode);
		
		$form->add(new Spacer(3));
		$form->add(new AlignedLabel('lnl', getBoxedText($str, 'headergrey', '100%'), 'left', '', 3));
		$form->add(new Spacer(3));

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

		
		if ($sname != "0") {
			pushVar("sname", $sname);
		} else {
			$sname = getVar("sname");	
		}

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

		$content = createDBCArray("content", "CID", "VERSION=0 AND DELETED=0 AND CATEGORY_ID = " . $pnode . $filtersql, "ORDER BY NAME");
		includePGNSources();

		for ($i = 0; $i < count($content); $i++) {
			$form->add(new LibraryViewer($content[$i], 1, $linkset, "image"));
		}

		if (count($content) == 0) {
			$form->add(new Label("lbl", "<center>" . $lang->get("no_contents", "No Contents available in this folder."). "</center>", "standard", 3));
		}

		if (isset($topText)) {
			$form->addToTopText($topText);
			$form->topicon = "ii_success.gif";
		}
		
		$form->add(new Hidden("action", ""));
		$page->add($form);
	}
?>