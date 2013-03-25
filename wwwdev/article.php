<?PHP
  require_once "nxheader.inc.php";
  require_once $cds->path."inc/header.php";

  // get the id of the article from the request
  // do type validation
  
  echo $cds->cluster->draw($article);
  br();
  br();
    
  // link back to the page where the article was called    
  echo $cds->tools->getBackLink($cds->content->get("Backlink Title"));
  
  require_once $cds->path."inc/footer.php";
  require_once "nxfooter.inc.php";
?>