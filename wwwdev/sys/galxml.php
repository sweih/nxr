<?
	require "../../cms/config.inc.php";

	// 
	// XML Header 
	// 
	Header ('Content-Type: text/xml');
	echo '<?xml version="1.0" ?>';
?>

<?php
	$gallery = value("gallery");

	if ($gallery == 0) {
		$gallery = value("pageid");
	}

	$variation = value("v");

	if ($gallery == "0")
		exit;

	if ($variation == "0" || $variation == 0)
		$variation = $c["stdvariation"];

	$live = isLive($gallery);

	// get standard information....
	$rows = getDBCell("pgn_gallery", "ROWS", "GALLERY_ID = " . $gallery);
	$cols = getDBCell("pgn_gallery", "COLS", "GALLERY_ID = " . $gallery);
	$imFolder = getDBCell("pgn_gallery", "IMAGE_FOLDER_ID", "GALLERY_ID = " . $gallery);

	// write header...
	echo "<gallery rows='$cols' cols='$rows'>\n";

	// prepare conent..
	$imagePGNId = getDBCell("modules", "MODULE_ID", "UPPER(MODULE_NAME) ='IMAGE'");
	$imageList = createDBCArray("content", "CID", "MODULE_ID = $imagePGNId AND CATEGORY_ID = $imFolder");

	if ($live) {
		$impath = $c["livefilesdocroot"];
	} else {
		$impath = $c["devfilesdocroot"]; //todo: versioning.
	}

	// create content-list.
	for ($i = 0; $i < count($imageList); $i++) {
		$cid = $imageList[$i];

		if ($live == isLive($cid)) {
			$fkid = getDBCell("content_variations", "FK_ID", "CID = $cid AND DELETED=0 AND VARIATION_ID = $variation");

			$filename = getDBCell("pgn_image", "FILENAME", "FKID=$fkid");

			echo "\t<photo furl='$impath" . $filename . "' purl='$impath" . "t" . $filename . "' comment=''/>\n";
		}
	}

	// write footer
	echo "</gallery>\n";

	function isLive($id) {
		if (getDBCell("state_translation", "OUT_ID", "OUT_ID=$id") != "")
			return true;

		return false;
	}
?>