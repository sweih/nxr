<?PHP
  require_once "nxheader.inc.php";
  $articles = $cds->channel->get("Channel"); 
  $rssCreator = $cds->plugins->getApi("RSSCreator");
  // "headline" and "body" are the names of the fields in the Article which will be parsed
  $rssCreator->title = $cds->content->get("Feed Title");
  $rssCreator->description = $cds->content->get("Feed Description");
  $rssCreator->addArticles($articles, "Headline", "Body" );

  $feedname = $cds->content->get("Feedname");
  if (strlen($feedname)>0) {
  	$feedname = $feedname . '.xml';
  } else {
  	$feedname = 'feed.xml';
  }
  
  echo $rssCreator->saveFeed("RSS1.0", $feedname); 
  $db->close();
?>