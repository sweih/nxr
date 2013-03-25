<?PHP
require_once "nxheader.inc.php";
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


echo '<base target="_blank">';
$rssReader = $cds->plugins->getApi("RSSReader");
if ($sma != 1) {
	$feed = $rssReader->get($cds->content->get("Address"));

	for ($i=0; $i < count($feed["items"]); $i++) {
		$item = $feed["items"][$i];
		echo "\t<b><a href=\"".$item["link"]."\" target=\"_blank\">$item[title]</a></b>\n";
		$item["description"] = str_replace("&#60;", "<", $item["description"]);
		$item["description"] = str_replace("&#62;", ">", $item["description"]);
		$item["description"] = str_replace("&#34;", '"', $item["description"]);
		echo $item["description"]."<br><br><br>";
	}
	echo '</base>';
} else {
	echo 'Feed URL: '.$cds->content->get("Address");

}
require_once $cds->path."inc/footer.php";
require_once "nxfooter.inc.php";
?>