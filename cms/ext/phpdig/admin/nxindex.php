<?
  require_once "../../../config.inc.php";
  $auth = new auth("SearchEngineAdm");
  $page = new Page('Search Engine');
  $page->setJSContainer($c['docroot'].'ext/phpdig/admin/index.php?sid='.$sid);
  $page->draw();
  $db->close();  
?>