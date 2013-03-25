<?php
	$resetbar = new ButtonBar("resetbar");

	$resetbar->add("action", $lang->get('resetcli', 'Change Object'), "submit", "", "", true, $lang->get("tt_rstci", "Each page in N/X has a cluster. By pressing Reset ClusterInstance you can assign a new cluster to the page and remove the old one."));

	$spname = getDBCell("sitepage_master", "NAME", "SPM_ID = $spm");
	$clt = getDBCell("sitepage_master", "CLT_ID", "SPM_ID = $spm");
	$cltname = getDBCell("cluster_templates", "NAME", "CLT_ID = $clt");

	if (!$mpProp) {
		$clnname = getDBCell("cluster_node", "NAME", "CLNID = $clnid");
	}

	if ($auth->checkAccessToFunction("B_RESET_CL_INST") && !$mpProp) {
		$propPanel->add($resetbar);
	}
	

	if ($type == 2 && !$mpProp) {
		$go = "UPDATE";

		$page_action = "UPDATE";
		$propPanel->add(new Cell("clc", "title1", 3, 600, 2));
		$propPanel->add(new Label("lbl", "<b>" . $lang->get("sp_launchdates"). "</b>", "headbox", 3));
		$propPanel->add(new Cell("clc", "title1", 3, 600, 2));

		if ($auth->checkAccessToFunction("AUTO_EXP_LAUN"))
			$propPanel->add(new DateTimeInput($lang->get("sp_launchdate"), "sitepage", "LAUNCH_DATE", "SPID = $spid"));

		if ($auth->checkAccessToFunction("AUTO_EXP_LAUN"))
			$propPanel->add(new DateTimeInput($lang->get("sp_expiredate"), "sitepage", "EXPIRE_DATE", "SPID = $spid"));

		$propPanel->add(new Subtitle('tit', $lang->get('sp_properties'),3));
		$propPanel->add(new Spacer(3))	;
		
		$propPanel->add(new Label("lbl", $lang->get("spm"), "standard"));
		$propPanel->add(new Label("lbl", $spname, "standard"));
		
		if ($auth->checkAccessToFunction("POSITION")) {	
			$propPanel->add(new MultipagePosition($lang->get("position"), "sitepage", "POSITION", "SPID = $spid", $spid, $menuID));			
		}
		
		
		
		$propPanel->add(new Hidden("view", $view));
		$propPanel->add(new Hidden("oid", $spid));
		$propPanel->add(new Hidden("processing", "yes"));
		$propPanel->add(new HIdden("acstate", "iproperties"));
		
	}

	if ($type == 1 || $type == 3 || ($type == 2 && $mpProp)) {
		$go = "UPDATE";
		$page_action = "UPDATE";
		//$spname = getDBCell("sitepage_master", "NAME", "SPM_ID = $spm");

		if ($type == 1) {
			$propPanel->add(new Cell("clc", "", 3, 600, 10));	
			$propPanel->add(new Label("lbl", "<b>" . $lang->get("sp_launchdates"). "</b>", "headbox", 3));
			$propPanel->add(new Cell("clc", "", 3, 600, 10));	

			if ($auth->checkAccessToFunction("AUTO_EXP_LAUN"))
				$propPanel->add(new DateTimeInput($lang->get("sp_launchdate"), "sitepage", "LAUNCH_DATE", "SPID = $spid"));

			if ($auth->checkAccessToFunction("AUTO_EXP_LAUN"))
				$propPanel->add(new DateTimeInput($lang->get("sp_expiredate"), "sitepage", "EXPIRE_DATE", "SPID = $spid"));
		}

		$propPanel->add(new Cell("clc", "", 3, 600, 10));	
		$propPanel->add(new Label("lbl", "<b>" . $lang->get("sp_properties"). "</b>", "headbox", 3));
		$propPanel->add(new Cell("clc", "", 3, 600, 10));	

		$cond = "MENU_ID = $menuID";
		$alternativeTemplates = createNameValueArrayEx("sitepage_master", "NAME", "SPM_ID", "CLT_ID = $clt AND DELETED=0");
		if ((count($alternativeTemplates) > 1) && $aclf->checkAccessToFunction("CHANGE_TEMPLATE")) {
		  $sel = new SelectOneInputFixed($lang->get("change_template", "Change Template"), "sitepage", "SPM_ID", $alternativeTemplates, $cond);
  		  $propPanel->add($sel);	
		  $propPanel->add(new NonDisplayedValue("sitemap", "SPM_ID", $cond, $sel->value, "NUMBER"));
		} else {
			$propPanel->add(new Label("lbl", $lang->get("ptempl", "Page Template"), "standard"));
			$propPanel->add(new Label("lbl", getSPMLink($spm), "standard"));			
		}
		
		$propPanel->add(new Label("lbl", $lang->get("spm_cluster"), "standard"));
		$propPanel->add(new Label("lbl", $cltname, "standard"));
		$propPanel->add(new Label("lbl", $lang->get("cli"), "standard"));
		$propPanel->add(new Label("lbl", $clnname, "standard"));

		$parentMenuID = getDBCell("sitemap", "PARENT_ID", "MENU_ID = ".$menuID);
						
		if ($auth->checkAccessToFunction("SITEPAGE_PROPS")) {
			//$propPanel->add(new MenuDropdown($lang->get("sp_parmenu"), "sitemap", "PARENT_ID", $cond, "type:dropdown", "MANDATORY"));
			$oname = new TextInput($lang->get("name"), "sitemap", "NAME", $cond, "type:text,width:200,size:32", "MANDATORY&UNIQUE");
			$oname->setFilter("PARENT_ID = $parentMenuID");
			$propPanel->add($oname);
			
		}
		
		if ($auth->checkAccessToFunction("POSITION")) {	
			$propPanel->add(new SitemapPosition($lang->get("position"), "sitemap", "POSITION", $cond, $menuID, $parentMenuID));			
			$propPanel->add(new MenuDropdown($lang->get("par_page", "Parent Page"), "sitemap", "PARENT_ID", $cond));
		}

		if ($auth->checkAccessToFunction("C_ACTIVE")) {
			$propPanel->add(new CheckboxInput($lang->get("active"), "sitemap", "IS_DISPLAYED", $cond, "1", "0"));
			$propPanel->add(new CheckboxInput($lang->get("restricted_page", "Restricted page", "Page requires login by the user."), 'sitepage', 'PASSWORD_PROTECTED', "SPID = $spid"));
		
		}

		if ($auth->checkAccessToFunction("C_CACHE") && $c["renderstatichtml"])
			$propPanel->add(new CheckboxInput($lang->get("cached"), "sitemap", "IS_CACHED", $cond, "1", "0"));
		
		if ($auth->checkAccessToFunction("LOCK_MENU") && $type==1)
			$propPanel->add(new CheckboxInput($lang->get("lock_menu", "Lock Menu"), "sitemap", "IS_LOCKED", $cond, "1", "0"));
		
		$propPanel->add(new CheckboxInput($lang->get("popup_menu", "Popup window"), "sitemap", "IS_POPUP", $cond, "1", "0"));

		if ($auth->checkAccessToFunction("C_CACHE") && $c["renderstatichtml"]) {
			$propPanel->add(new Cell("clc", "standard", 3, 600, 10));
			$propPanel->add(new Subtitle("st", $lang->get("cconlaunch"),3));
			$propPanel->add(new Textinput($lang->get("cconlaunch_lbl", "Dev-Page-IDs (commaseparated)"), "sitemap", "CC_ON_LAUNCH", "MENU_ID = " . $menuID, "type:text,size:250,width:300"));
		}
		
		if (defined('ADMINMODE')) {
			  $propPanel->add(new CheckboxInput('Deletable', 'sitepage', 'DELETABLE', "SPID = $spid"));
		}
		

		$propPanel->add(new Hidden("view", $view));
		$propPanel->add(new Hidden("oid", $menuID));
		$propPanel->add(new Hidden("spid", $spid));
		$propPanel->add(new Hidden("processing", "yes"));

		if ($mpProp || $type == 3 || $type == 1)
			$propPanel->add(new Hidden("acstate", "pproperties"));

	}

	$sppropeditbar = new FormButtons(true, true);
	$propPanel->add($sppropeditbar);
?>