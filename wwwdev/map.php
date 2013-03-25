<?PHP
require_once "nxheader.inc.php";

// Get the Google Maps API-Key
$apikey = $cds->content->get("Google-API-Key");

// Create the Maps-API. The apikey is passed as parameter.
$maps = $cds->plugins->getApi("Google Maps API", $apikey);

$cds->layout->addToHeader($maps->printGoogleJS());

require_once $cds->path."inc/header.php";
$headline = $cds->content->get("Headline");
$body = $cds->content->get("Body");

if ($headline != "") {
	echo '<h1>'.$headline.'</h1>';
	br();
}

if ($body !="") {
	echo $cds->content->get("Body");
	br();
}

br();

if ($sma != 1) {
	// add a point to the map
	$maps->addAddress($cds->content->get("Address"), $cds->content->get("Address Description"));

	// setup width and height and zoom factor (0-17)
	$maps->setWidth(600);
	$maps->setHeight(450);
	$maps->zoomLevel = 2;

	// setup controls
	$maps->showControl = $cds->content->get("ShowControls");

	// draw the map.
	$maps->showMap();
} else {
	echo "Address: ".$cds->content->get("Address");
	br();
	echo "Address Description: ".$cds->content->get("Address Description");

}

require_once $cds->path."inc/footer.php";
require_once "nxfooter.inc.php";
?>