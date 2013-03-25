<?php
	require_once "../../config.inc.php";

	$auth = new auth("B_LIVE_AUTHORING", false);
	$page = value("page", "NUMERIC");
	$menu = getDBCell("sitepage", "MENU_ID", "SPID=$page");
	
	//// ACL Check ////
	$aclf = aclFactory($menu, "page");
	$aclf->load();
	if (! $aclf->hasAccess($auth->userId) || ! $aclf->checkAccessToFunction("B_LIVE__AUTHORING")) {		
	  header("Location: ". $c["docroot"]."modules/common/noaccess.php?sid=$sid&guid=$pnode");
	}
	//// ACL Check ////
	
	$spm = getDBCell("sitepage", "SPM_ID", "SPID = $page");
	$path = getDBCell("sitepage_master", "TEMPLATE_PATH", "SPM_ID = $spm");
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">

<html>
	<head>
		<title>N/X Live Authoring</title>
	</head>

	<frameset border = 0 framespacing = 0 frameborder = 2 framespacing = 0 rows = "100%">
		<frame name = "content" src = "<?php echo $c["devdocroot"].$path."?page=$page&v=$v&sma=1&sid=$sid";?>" scrolling = auto>
	</frameset>
</html>

<?php
	$db->close();
?>