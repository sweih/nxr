<?php
	if ($aclf->checkAccessToFunction("NEW_FOLDER"))
		$form->buttonbar->add("action", $lang->get("new_folder", "Create Folder"), "submit", "document.form1.processing.value = '';");

	if ($pnode != "0" && ($aclf->checkAccessToFunction("ED_FOLDER_PROPS") || $aclf->checkAccessToFunction("DELETE_FOLDER"))) {		

		if ($aclf->checkAccessToFunction("ED_FOLDER_PROPS"))
			$form->buttonbar->add("action", $lang->get("edit_folder", "Edit Folder"), "submit", "document.form1.processing.value= '';");

		if ($aclf->checkAccessToFunction("DELETE_FOLDER"))
			$form->buttonbar->add("action", $lang->get("del_folder", "Delete Folder"), "submit", "document.form1.processing.value= '';");
		$form->buttonbar->add("separator", "", "", "", "");
	}

	if ($aclf->checkAccessToFunction("ED_FOLDER_ACL")) {		
		$form->buttonbar->add("action", $lang->get("edit_access", "Set Access"), "submit", "document.form1.processing.value= '';");
	}
	$form->buttonbar->setVariationSelector(createNameValueArrayEx("variations", "NAME", "VARIATION_ID", "1"), variation());
?>