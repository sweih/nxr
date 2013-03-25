<?
	/**********************************************************************
	 *	N/X - Web Content Management System
	 *	Copyright 2002 Sven Weih, FZI Research Center for Information Technologies
	 *	www.fzi.de
	 *
	 *	This file is part of N/X.
	 *	The initial has been setup as a small diploma thesis (Studienarbeit) at the FZI.
	 *	It was be coached by Prof. Werner Zorn and Dipl.-Inform Thomas Gauweiler.
	 *
	 *	N/X is free software; you can redistribute it and/or modify
	 *	it under the terms of the GNU General Public License as published by
	 *	the Free Software Foundation; either version 2 of the License, or
	 *	(at your option) any later version.
	 *
	 *	N/X is distributed in the hope that it will be useful,
	 *	but WITHOUT ANY WARRANTY; without even the implied warranty of
	 *	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	 *	GNU General Public License for more details.
	 *
	 *	You should have received a copy of the GNU General Public License
	 *	along with N/X; if not, write to the Free Software
	 *	Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
	 **********************************************************************/
	require "../../config.inc.php";
	$auth = new auth("EDIT_OBJECT|OBJECT_PROPS");
	if (value("resetfilter") == '1') {		
		delVar ("filter");
		delVar ("sname");
		pushVar("linkset", '');		
	}
	
	$page = new page("Edit Object");
	$page->setJS("TREE");	
	if (strlen(getVar("linkset")) > 1) $disableMenu = true;
	//// ACL Check ////
	$aclf = aclFactory($pnode, "folder");
	$aclf->load();
	if (! $aclf->hasAccess($auth->userId))
	  header("Location: ". $c["docroot"]."modules/common/noaccess.php?sid=$sid&guid=$pnode");
	//// ACL Check ////
	/************************************************************************************
	 * Initializing
	 ************************************************************************************/
	$oid = value("oid", "NUMERIC");
	$template = getDBCell("content", "MT_ID", "CID = $oid");
	$name = getDBCell("content", "NAME", "CID = $oid");
	$category_id = getDBCell("content", "CATEGORY_ID", "CID = $oid");
	$cond = "CID = $oid";
	$content_MODULE_ID = getDBCell("content", "MODULE_ID", "CID = $oid");
	$variation = variation();
	$fkid = getDBCell("content_variations", "FK_ID", "VARIATION_ID = $variation AND CID = $oid AND DELETED=0");
	$page_action = "UPDATE";
	$handled = false;
       	/************************************************************************************
       	 * Creating the form
       	 ************************************************************************************/
        if ($fkid == "") {        	
		if (value("action") == "cr_content" && value("decision") == $lang->get("yes")) {
			if (getDBCell("content_variations", "CID", "CID=$oid AND VARIATION_ID = $variation") != "") {
				$sql = "UPDATE content_variations SET DELETED=0 WHERE CID=$oid AND VARIATION_ID = $variation";
				$query = new query($db, $sql); 	
				$fkid = getDBCell("content_variations", "FK_ID", "CID=$oid AND VARIATION_ID = $variation");			
			} else {
				$fkid =nextGUID();
				$sql = "INSERT INTO content_variations (CID, FK_ID, VARIATION_ID, DELETED) VALUES ( $oid, $fkid, $variation, 0)";
				$query = new query($db, $sql);				
				$PGNRef = createPGNRef($content_MODULE_ID, $fkid);
        $PGNRef->sync();
			}			
			
			$page_state = "start";			
		} else if (value("action") == "cr_content" && value("decision") == $lang->get("no")) {				
				header("Location: ".$c["docroot"]."modules/content/objectbrowser.php?sid=$sid");
				exit;
		} else {
			$form = new YesNoForm($lang->get("cr_vr", "Create variation"), $lang->get("crlib_mes", "The content element does not exists in the selected variation. Do you want to create it?"));
			$form->add(new Hidden("action", "cr_content"));
			$form->add(new Hidden("oid", $oid));
			$form->add(new Hidden("crvar", $variation));
			$handled = true;
		}
        }
        
        if (! $handled) {
        	$form = new PanelForm($lang->get("edit_content", "Edit Content"). ": " . $name, '', 'con');
        
        	if ($aclf->checkAccessToFunction("EDIT_OBJECT")) {
        		$editpanel = new Panel($lang->get("edit_content"));
        		$buttonbar = new ButtonBar("variations");
        		$buttonbar->selectBoxDescr = true;
        		$buttonbar->setVariationSelector(createNameValueArrayEx("variations", "NAME", "VARIATION_ID", "1"), $variation);        		
        		$editpanel->add($buttonbar);
        		$editpanel->add(new Subtitle("st", $lang->get("title")));
        		$oname = new TextInput($lang->get("o_name"), "content", "NAME", $cond, "type:text,width:200,size:32", "MANDATORY&UNIQUE");
        		$oname->setFilter("CATEGORY_ID = $category_id");
        		$editpanel->add($oname);
        		$editpanel->add(new TextInput($lang->get('access_key', 'Access Key', 'Key-Value with which you can access this content from the editor by typing [key].'), 'content', 'ACCESSKEY', $cond, "type:text,width:100,size:16",'UNIQUE'));        		
        		$editpanel->backto = $c["docroot"] . "modules/content/objectbrowser.php?sid=$sid&resetfilter=1&pnode=$category_id";
        
        		// Edit
        		$editpanel->add(new Subtitle("st", $lang->get("edit_content")));
        		includePGNSource ($content_MODULE_ID);
        		$ref = createPGNRef($content_MODULE_ID, $fkid);
        
        		if ($ref != "") {
        			$ref->edit($editpanel);
        		} else {
        			$editpanel->add(new Label("std", $lang->get("error_init", "Could not initialize the content"), "standardlight"));
        		}
        
        		// Description
        		$editpanel->add(new Subtitle("st", $lang->get("content_desc", "Description of Content")));
        		//$editpanel->add(new TextInput($lang->get("description"), "content", "DESCRIPTION", $cond, "type:textarea,width:300,size:3", ""));
        		$editpanel->add(new TextInput($lang->get("keywords"), "content", "KEYWORDS", $cond, "type:textarea,width:300,size:3", ""));
        		// MetaData 
        		$editpanel->add(new Hidden("resetfilter", '1'));        		
        		$editpanel->add(new MetaDataEditor($oid, $template, 2));
        		$editpanel->add(new FormButtons());
        
        		$form->addPanel($editpanel);
        	}
        
        	if ($aclf->checkAccessToFunction("OBJECT_PROPS")) {
        		// Properties
        		$proppanel = new Panel($lang->get("properties", "Properties"));
        
        		$proppanel->add(new Subtitle("st", $lang->get("properties", "Properties")));
        		$proppanel->add(new FolderDropdown($lang->get("r_parent"), "content", "CATEGORY_ID", $cond));
        		$proppanel->add(new SelectMultiple2Input($lang->get("variations"), "content_variations", "VARIATION_ID", $cond . " AND DELETED=0", "variations", "NAME", "VARIATION_ID", "DELETED=0"));
        		$proppanel->add(new SelectOneInput($lang->get("metatemplate"), "content", "MT_ID", "meta_templates", "NAME", "MT_ID", "INTERNAL=0 AND VERSION=0", $cond, "type:dropdown", "MANDATORY"));
        		$proppanel->add(new NonDisplayedValue("content", "LAST_MODIFIER", $cond, $auth->user, "TEXT"));
        		$proppanel->add(new NonDisplayedValue("content", "LAST_MOD_DATE", $cond, "", "TIMESTAMP"));		
        		$handler = new ActionHandler("UPDATE");
        		$handler->addFncAction("syncVariations");
        		$proppanel->registerActionHandler($handler);
        		$proppanel->backto = $c["docroot"] . "modules/content/objectbrowser.php?sid=$sid";
        		$proppanel->add(new FormButtons());
        		$form->addPanel($proppanel);
        	}
        }
	/************************************************************************************
	 * Adding the menu
	 ************************************************************************************/
	$browser = new Foldermenu($lang->get("library", "Library"));
	$browser->action = $c["docroot"] . "modules/content/objectbrowser.php";
	$page->addMenu($browser);

	$page->add($form);
	$page->draw();
	echo $errors;
?>