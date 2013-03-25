<?
	/*
	 * Include this file in your page-templates
	 */
	require ("../cms/config.inc.php");
	$db->cache = true; // enable database cache.
	
	if ($c["trackexitpages"]) {
		function encode_exit_urls($buffer) {  
			return preg_replace(    "#<a href=(\"|')http://([^\"']+)(\"|')#ime",    '"<a href=\"sys/exit.php?url=".base64_encode(\'\\2\')."\""',    $buffer  );
		}
		ob_start('encode_exit_urls');
	}
	
	include $c["path"] . "api/oldcds/jpcache/jpcache.php";
	require_once $c["path"] . "api/oldcds/menu.inc.php";
	require_once $c["path"] . "api/oldcds/content.inc.php";
	require_once $c["path"] . "api/parser/nx2html.php";
	require_once $c["path"] . "api/oldcds/cluster.inc.php";
	require_once $c["path"] . "api/oldcds/instanciate.inc.php";
	require_once $c["path"] . "api/oldcds/layout.inc.php";
	includePGNSources();
	$splevel = 10;
	$is_development = false;

	function logAccess() {
		global $c, $page;

		if ($c["pagetracking"]) {
		  if ($c["usewebbug"]) {
		  	echo "<img src=\"" . $c["livedocroot"] . "sys/image.php?document=$page\" width=\"1\" height=\"1\">";
		  } else {
		  	include_once $c["path"].'modules/stats/phpOpenTracker.php';
		  	phpOpenTracker::log(array('document' => $page));
		  }
		}
	}
?>