<?PHP
  require_once "nxheader.inc.php";  
  if ($cds->menu->hasLowerLevel()) {
    $childs = $cds->menu->lowerLevel();
    $url = $childs[0]->getLink();
    if (strlen($url)>0)  
      header("Location:".$url);
  } else if ($cds->menu->hasUpperLevel()) {
    $upperLevel = $cds->menu->UpperLevel();
    $url = $upperLvbel->getLink();
    if (strlen($url)>0)  
      header("Location:".$url);
  }

  require_once "nxfooter.inc.php";
?>