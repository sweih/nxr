<?PHP
require_once "nxheader.inc.php";
$cds->layout->setStandardSearchEngineCSS();
require_once $cds->path."inc/header.php";

$headline = $cds->content->get("Headline");
if ($headline != "") {
	echo '<h1>'.$headline.'</h1>';
	br();
}

$body = $cds->content->get("Body");
if ($body !="") {
	echo $body;
	br();
}


if ($sma!=1) {
	// output of content
	$cds->searchengine->drawSearchForm($cds->content->get("search button"));
	if (value("searchphrase") != "0")
	$cds->searchengine->drawResultList($cds->searchengine->search(value("searchphrase")), $cds->content->get("No Results Text"));
}

include $cds->path."inc/footer.php";
require_once "nxfooter.inc.php";
?>