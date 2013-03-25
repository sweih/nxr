<?
	/**********************************************************************
	 * @module Application
	 **********************************************************************/
	require_once "../../config.inc.php";

	$auth = new auth("Administrator");
	$page = new page("Designs");
	require_once "menudef.inc.php";
	
	$form = new SettingsForm($lang->get("designs", "Website Designs"));
	$ar = createNameValueArray('sitemap', 'NAME', 'MENU_ID');
	$designclasses = getDCFileList('designs');	
	$form->addRadioSetting($lang->get('sel_design', 'Select Design'), 'CDS/MENU', $designclasses);
	
	$mclass = reg_load('CDS/MENU');
	if ((value("set0", "", "") != $mclass) && (value("set0", "", "") != ""))
	  $mclass = value("set0");
	if ($mclass != "") {
	  $form->addHeadline($lang->get('adjust/desugb', 'Adjust Design'));
	  $ref = createDCRef($c["basepath"]."designs/".$mclass);
	  $ref->editConfiguration(&$form);
	  $form->add(new Spacer(2));
	}
	$page->add($form);	
	$page->draw();
	$db->close();
?>