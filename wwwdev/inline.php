<?PHP  
require_once "nxheader.inc.php";
include $cds->path."inc/header.php";
$headline = $cds->content->get("Headline");
$body = $cds->content->get("Body");

if ($headline != "") {
	echo '<h1>'.$headline.'</h1>';
	br();
}

if ($body !="") {
	echo $cds->content->get("Body");
	br();
	br();
}


$url = $cds->content->get("URL", "ALL");
if ($sma != 1) {
	echo '<iframe  frameborder="0" width="100%" height="900" src="'.$url["HREF"].'"></iframe>';
}  else  {
	echo "Inline Frame: ".$url;
}

include $cds->path.'inc/footer.php';
require_once "nxfooter.inc.php";
?>