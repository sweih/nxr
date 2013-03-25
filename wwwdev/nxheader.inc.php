<?php
	/*
	 * Include this file in your page-templates
	 */	 
	if (isset($_GET["c"]) || isset ($_POST["c"]) || isset($_SESSION["c"])) {    	
    	exit;
    }
	 if (! isset($c)) require_once "../cms/config.inc.php";
	 require_once $c["path"]."api/cds/lib.inc.php";
	 includePGNISources();

	if (value("sma") == "1" || $sma == "1") {
		require_once $c["path"] . "api/auth/auth_sma.php";
		require_once $c["path"]."api/common/lang.php";		
		$sma=1;
		$auth = new authSMA("B_LIVE_AUTHORING");
		$sid = $_COOKIE["sid"];
		if ($sid != "" && !is_numeric($sid))
			$sid = "";
		$cds = new SMA_CDSApi(true);
	} else {
 		
 	// determine startpage and forward to startpage if no pageid isset.	 
	 if (!isset($page)) {
	    $page = value("page", "NUMERIC",-1);	    
	    if (($page == -1)  &&  ! defined('ACCESS_CONTENT_ONLY')) {	 			 
	 			 $forward = getStartPageURI(value("v", "NUMERIC", 0),0);
	 			 if ($forward != "") 
	 			   header("location: ".$forward); 
	    }    	
	 }
 		
 	  if (!isset($article)) 
	   $article = value("article", "NUMERIC"); 			 		
 	  $cds = new CDSApi(true);
	}	
?>
