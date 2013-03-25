<?php
	require_once "../../config.inc.php";

	require_once $c["path"] . "api/auth/auth_sma.php";
	$sid = $_COOKIE["nxwcms"];
	$auth = new authSMA("B_LIVE_AUTHORING", false);
	$disableMenu = true;
	$page = new page("SMA Editor");	
	$sma = 1;
	$oid = value("oid", "NUMERIC");

	if ($oid != "0") {
		includePGNISources();

		$page_action = "UPDATE";
		$go = "-";
		$form = new EditForm($lang->get("ed_content"));
		$jsupdate = new HTMLContainer("con", "standard", 2);
		$jsupdate->add("<script language=\"JavaScript\">opener.document.location.reload();window.close();</script>");

		$ref = createPGNRef(getModuleFromCLC($oid), $oid);
		$ref->edit($form);

		if ($page_state == "processing") {
			$form->add($jsupdate);
		}

		$page->add($form);
	}

	$page->draw();
?>