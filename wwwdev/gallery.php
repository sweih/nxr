<?PHP
require_once "nxheader.inc.php";
$cds->layout->preloadImageByAccessKey("beach");
require_once $cds->path."inc/header.php";

// draw the Headline and the body
echo '<h1>'.$cds->content->get("Headline").'</h1>';
br();
$body = $cds->content->get("Body");
if ($body != "") {
	echo $body;
	br();
}


echo '<div align="center">';
echo $cds->content->get("Gallery", array("BODY", 640,480));
echo '</div>';

require_once $cds->path."inc/footer.php";
require_once "nxfooter.inc.php";
?>