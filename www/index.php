<?php
	if (isset($c["path"])) {
	 	require_once $c["path"]."config.inc.php";
	 } else {
	   require_once "../cms/config.inc.php";
	 }
 
	 require_once $c["path"]."api/cds/lib.inc.php";

	 $cds = new CDSApi(false);
	 $template = $cds->menu->getTemplate();
	 $page = $cds->pageId;
	 $v = $cds->variation;
	 
	 require $c["livepath"].$template; 
?>