<?php
  if ($aclf->checkAccessToFunction("SET_PAGE_ACCESS")) {			
			$aclid = $mid;
			$aclType = "page";
			$title = $name[0];
			include $c["path"] . "api/userinterface/panels/acl_panel.inc.php";	
			$aclPanel->add(new Hidden("mid", $mid));
			$aclPanel->add(new Hidden("oid", $spid));
			$aclPanel->add(new Hidden("view", $view));
			$aclPanel->add(new Hidden("processing", "yes"));
			$aclPanel->add(new FormButtons(true, true));
  }
?>