<?

	if ((value("changevariation") != "0" && value("changevariation") != "") || (value("changetranslation") != "0" && value("changetranslation") != ""))
		$page_state = "";

	$switchbar = new ButtonBar("swbar");

	if ($mpProp) {
		$allowedVariations = createDBCArray("sitepage_variations", "VARIATION_ID", "SPM_ID = $spm");

		$commatext = implode(", ", $allowedVariations);
		$variations = createNameValueArray("variations", "NAME", "VARIATION_ID", "DELETED=0 AND VARIATION_ID IN ($commatext)");
	}

	$switchbar->setVariationSelector($variations, $variation);

	if ($mpProp) {
		$backup = $spid;

		$spid = $menuID;
	}

	// synchronize.... because new feature
	if (countRows("sitepage_names", "SPID", "SPID = $spid AND VARIATION_ID = $variation") < 1) {
		$sql = "INSERT INTO sitepage_names (SPID, VARIATION_ID) VALUES ($spid, $variation)";

		$query = new query($db, $sql);
		$query->free();
	}

	$menuPanel->add($switchbar);
	$menuPanel->add(new Cell('clc', '', 3, 600,10));
	$menuPanel->add(new SubTitle("st", $lang->get("sp_menuprops"), 3));
	$menuPanel->add(new Cell('clc', '', 3, 600,10));
	$menuPanel->add(new TextInput($lang->get("sp_menuname"), "sitepage_names", "NAME", "VARIATION_ID = $variation AND SPID = $spid", "type:text,size:128,width:300", ""));
	$menuPanel->add(new TextInput($lang->get("sp_menudescription", "Page Description (for sitemap...)"), "sitepage_names","HELP", "VARIATION_ID = $variation AND SPID = $spid", "type:textarea,size:5,width:400", ""));
	$menuPanel->add(new Label('lbl', $lang->get('url', 'URL'), 'standard',1));
	$uri = getPageURL($menuID, $variation);
	if (file_exists($c["livepath"].$uri)) {
		$menuPanel->add(new Label('lbl', '<a href="'.$c["host"].$c["livedocroot"].$uri.'" target="_blank">'.$c["host"].$c["livedocroot"].$uri.'</a>', 'standardlight',2));
	} else {		
		$menuPanel->add(new Label('lbl', $c["host"].$c["livedocroot"].$uri, 'standardlight',2));		
	}
	
	if ($aclf->checkAccessToFunction("DIRECT_URL")) {		
		$menuPanel->add(new Cell('clc', '', 3, 600,10));
		$menuPanel->add(new SubTitle("st", $lang->get("sp_menudirecttitle", "Define optional Path on server for direct access"), 3));
		$menuPanel->add(new Cell('clc', '', 3, 600,10));
		$durl = new TextInput($lang->get("sp_menudirect", "Direct url on Live Server"), "sitepage_names", "DIRECT_URL", "VARIATION_ID=$variation AND VERSION=0 AND SPID = " . $spid, "type:text,size:64;width:300", "UNIQUE");
		$durl->setFilter("VERSION=0");
		$menuPanel->add($durl);
	}

	if ($mpProp) {
		$menuPanel->add(new Hidden("acstate", "pproperties"));
	}

	$menuPanel->add(new Hidden("oid", $spid));
	$menuPanel->add(new Hidden("view", $view));
	$menuPanel->add(new Hidden("processing", "yes"));
	$menueditbar = new FormButtons(true, true);
	$menuPanel->add($menueditbar);
?>