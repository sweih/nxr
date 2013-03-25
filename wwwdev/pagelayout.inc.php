<?
	/*
	 * Include this file in your page-templates
	 */

	//delete passwords that none can read it.
	$is_development = true;
	if ($_GET["sma"] == "1" || $_POST["sma"] == "1" || $sma == "1") {
		require_once $c["path"] . "api/auth/auth_sma.php";

		$auth = new authSMA("B_LIVE_AUTHORING");
		require_once $c["path"] . "api/oldcds/content_sma.inc.php";
		require_once $c["path"] . "api/oldcds/menu_sma.inc.php";
		$sid = $_COOKIE["sid"];

		if ($sid != "" && !is_numeric($sid))
			$sid = "";
	} else {
		require_once ("../cms/config.inc.php");

		require_once $c["path"] . "api/oldcds/content.inc.php";
		require_once $c["path"] . "api/oldcds/menu.inc.php";
	}

	require_once $c["path"] . "api/parser/nx2html.php";
	require_once $c["path"] . "api/oldcds/cluster.inc.php";
	require_once $c["path"] . "api/oldcds/instanciate.inc.php";
	require_once $c["path"] . "api/oldcds/layout.inc.php";
	includePGNSources();
?>