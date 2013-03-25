<?PHP
  require_once "nxheader.inc.php";
  
  //extract the Link as array
  $link = $cds->content->get("URL", "ALL");
  
  // forward to the link
  if ($sma != 1) {
  	header('Location: ' . $link["HREF"]);
  } else {
  	echo 'External URL: '.$link;
  }
  require_once "nxfooter.inc.php";
?>