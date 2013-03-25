<?PHP
  require_once "nxheader.inc.php";
  $cds->layout->htmlHeader(); 

  echo $cds->content->get("headline");
  require_once "nxfooter.inc.php";
?>