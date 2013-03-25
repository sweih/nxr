<?php
  require_once "../../config.inc.php";
  require "sitemap.php";
  
  $auth=new auth("GOOGLESITEMAP");
  $page = new page("Google Sitemaps");
  $variations = createDBCArray("variations", "VARIATION_ID", "1");
  if (!isset($go))
		$go = "start";
		
  $form = new CommitForm($lang->get("udgoogles", 'Update Google Sitemaps'));
	
	$updateHandler = new ActionHandler("updategs");
	$updateHandler->addFNCAction("updateSitemap");
	$form->addCheck("updategs", $lang->get("startgoogles", 'Update the Google Sitemap XML to: ').$c["host"].$c["livedocroot"].'sitemap.xml', $updateHandler);

  $page->add($form);
	$page->draw();
	$db->close();


  /**
  *  Creates the new sitemap.xml
  */
  function updateSitemap() {
    global $c;
    // Create sitemap
    $map = new GoogleSiteMap();
    // Get the startnode of the live website;
    $startNode = translateState(0,10);
    
    // Is there a live-website?
    
    if (strlen($startNode) > 0) {
      addWebsite($startNode, $map);	
		$map->CloseMap();
		$map->WriteFlux($c["livepath"]."sitemap.xml");  	    	
    }  	
  }
  
  /**
   * Add a page to the Sitemap and start adding the children
   */
   function addWebsite($startNode, &$map) {
     global $c, $variations;          
     $childs = createDBCArray("sitemap", "MENU_ID", "PARENT_ID=".$startNode." AND VERSION=10 AND DELETED=0");     
     for ($i=0; $i < count($childs); $i++) {
       for ($j=0; $j< count($variations); $j++) {
         if (!isMenuExpired($childs[$i], $variations[$j])) {
           $url = $c["livehost"].$c["livedocroot"].getPageURL($childs[$i], $variations[$j]);
           $map->AddURL($url);                   
         }
         addWebsite($childs[$i], $map);
       }
     	
     }  
     	
   }
  
?>