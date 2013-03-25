<?php
/**********************************************************************
 *	N/X - Web Content Management System
 *	Copyright 2002 Sven Weih, Thomas Gauweiler
 *	FZI Research Center for Information Technologies
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

  $go = "UPDATE";
  require_once "../../config.inc.php";
  $auth = new auth("PLUGIN_CONTROL");
  $page = new page("Install Plugin");

  $filter = new Filter("modules", "MODULE_ID");
  $filter->addRule($lang->get("name"), "MODULE_NAME", "MODULE_NAME");
  $filter->icon = "li_plugin.gif";
  $filter->type_name = "Plugins";
  $filter->newLabel = $lang->get('install_pgn', 'Install a plugin');
 
  $filtermenu = new Filtermenu($lang->get("pg_title"), $filter);
  $filtermenu->tipp = $lang->get("help_pgn", "Plug-ins allow you to enhance N/X beyond its standard features. Typically, plug-ins are used to create new objects types in addition to the two standard object types text and image.");
  //$oid = value("oid", "NUMERIC", '');  
  if ($pagestate->insert) {
  	$form = new EditForm($lang->get("pg_install"), "i_plugin.gif");
  	$form->addToTopText($lang->get("pg_installdesc")."<br><br>");
  	$form->add(new Label("lbl", $lang->get("pg_choose"), "standard"));
  	$form->add(new Dropdown("filenameselect", getPluginFileList(), "standard"));
  	$form->add(new Hidden("go", "CREATE"));
  	$form->add(new Hidden("action", "install"));
  } 
  	
  if (!$pagestate->insert || (value("action") == "install") || (value("action") == "uninstall")) {  	
  	$form = new StdEDForm($lang->get("pgn_info", "Plugin information"));
  	if ((value("action") == "install")) {
  	  $oid=install();   	  
  	  $page_state = "start";
  	}
  	    
  	includePGNSource ($oid);
	$modRef = createPGNRef($oid, 0);
	if ($modRef->helpfile != "") {
	  $form->buttonbar->add("help", $lang->get("disp_doc", "Display Documentation"), "button", "window.open('".$c["docroot"]."plugin/".$modRef->helpfile."', 'pgnhelp', '')");	
	}
	  $modtype = getDBCell("modules", "MODULE_TYPE_ID", "MODULE_ID = $oid");	  
	  if ($modtype==5) {
	    $form->buttonbar->add("action", $lang->get("settings", "Settings"));
	  }
	  $form->buttonbar->addConfirm("action", $lang->get("uninstall", "Uninstall"), $lang->get("uninstconf","Uninstalling a plugin can cause damage to the website! Do you want to proceed?"), $c["docroot"]."modules/plugins/install.php?sid=$sid&oid=$oid&action=uninstall");    
  	$form->add(new DisplayedValue($lang->get("name"), "modules", "MODULE_NAME", "MODULE_ID=$oid"));
  	
  	$form->add(new DisplayedValue($lang->get("modtype", "Plugin Type"), "module_type", "NAME", "MODULE_TYPE_ID=$modtype"));
  	$form->add(new DisplayedValue($lang->get("description"), "modules", "DESCRIPTION", "MODULE_ID=$oid"));
  	$form->add(new DisplayedValue($lang->get("version", "Version"), "modules", "VERSION", "MODULE_ID=$oid"));
  	$form->add(new DisplayedValue($lang->get("sourcefile", "Source file"), "modules", "SOURCE", "MODULE_ID=$oid"));
  	$form->add(new Hidden("action", ""));
  	$form->add(new Hidden("oid", $oid));
  	$form->forbidDelete(true);
  	$form->forbidUpdate(true);
  	if ((value("action") == "uninstall") && is_numeric($oid)) {
  		uninstallPlugin($oid);
  	}
  }
  if (value("action") == $lang->get("settings")) {  	
  	if ($page_action=="") {
  		$page_action = 'UPDATE';
  		$page_state = "start";
  	}
  	//if (!isset($go)) $go="update";
  	  	
  	//$page_action="UPDATE";
  	$oid = value("oid", "NUMERIC");  	  	
  	$form = new StdEDForm($lang->get("settings"));
  	$cond = $form->setPK("modules", "MODULE_ID");  	
  	$form->forbidDelete(true);
  	$form->addHeaderLink(crHeaderLink($lang->get("Back"), 'modules/plugins/install.php?oid='.$oid.'&sid='.$sid.'&go=update'));
  	$form->add(new DisplayedValue($lang->get("name"), "modules", "MODULE_NAME", "MODULE_ID=$oid"));
  	$region[0][0] = 'Please Select';
  	$region[0][1] = '0';
  	$region[1][0] = 'Website Header';
  	$region[1][1] = 'HEAD1';
  	$region[2][0] = 'Website Footer';
  	$region[2][1] = 'FOOT1';
  	$region[3][0] = 'Content Header';
  	$region[3][1] = 'HEAD2';
  	$region[4][0] = 'Content Footer';
  	$region[4][1] = 'FOOT2';
  	$region[5][0] = 'Content Side';
  	$region[5][1] = 'SIDE1';
  	$form->add(new SelectOneInputFixed($lang->get("region", "Place"), "modules", "REGION", $region, $cond, "type:dropdown", "MANDATORY", "TEXT"));
  	$form->add(new TextInput($lang->get("position"), "modules", "POSITION", $cond, "type:text", "", "NUMBER"));
  	$form->add(new Hidden("action", $lang->get("settings")));  	
  	$modRef = createPGNRef($oid, $oid, $oid);
  	$modRef->registration();
  	$modRef->edit($form);
  }

  $page->addMenu($filtermenu);
  $page->add($form);
  $page->draw();
  $db->close();
 
  /**
	 * Uninstalls a Plugin
	 * @param integer GUID of the plugin to uninstall.
	 */
	function uninstallPlugin($oid) {
		global $db;
		// module uninstall handler
		includePGNSource ($oid);
		$modRef = createPGNRef($oid, 0);

		if ($modRef != null) {
			$modRef->registration();

			$modRef->_uninstaller();

			// content deletion
			$delHandler = new ActionHandler("DELETE");
			$sql = "SELECT CID FROM content WHERE MODULE_ID = $oid";

			$query = new query($db, $sql);

			while ($query->getrow()) {
				$cid = $query->field("CID");

				$delHandler->addDBAction("DELETE FROM meta WHERE CID = $cid");
				$delHandler->addDBAction("UPDATE cluster_template_items SET FKID=0 WHERE FKID=$cid");
				$delHandler->addDBAction("DELETE FROM content_variations WHERE CID = $cid");
				$delHandler->addDBAction("DELETE FROM content WHERE CID = $cid");
			}

			// meta-templates deletion.
			$mtid = getDBCell("modules", "MT_ID", "MODULE_ID = $oid");

			if ($mtid != 0) {
				$delHandler->addDBAction("DELETE FROM meta_template_items WHERE MT_ID = $mtid");

				$delHandler->addDBAction("DELETE FROM meta_templates WHERE MT_ID = $mtid");
			}

			$delHandler->addDBAction("DELETE FROM modules WHERE MODULE_ID = $oid");
			$delHandler->process("DELETE");
			$delHandler = new ActionHandler("DELETE");

			// cluster deletion.
			$delHandler->addDBAction("UPDATE cluster_template_items SET FKID = 0 WHERE FKID = $oid AND CLTITYPE_ID=2");
			$cltis = createDBCArray("cluster_template_items", "CLTI_ID", "CLTITYPE_ID = 2 AND FKID = $oid");

			for ($i = 0; $i < count($cltis); $i++) {
				$id = $cltis[$i];

				$delHandler->addDBAction("DELETE FROM cluster_content WHERE CLTI_ID = $id");
			}

			$delHandler->process("DELETE");
			writeDataTypeArray();
		}
	}
	
	
/**
 * Handler for installing a plugin
 */
function install() {
	global $filename, $filenameselect, $form, $lang, $errors, $c, $db;
	$filename = value("filename");
	$filenameselect = value("filenameselect");

	if ($filename=="0" && $filenameselect!="")
		$filename = rawurldecode($filenameselect);
	if ($filename=="0" || !file_exists($c["path"].'plugin/'.$filename)) :
		$form->addToTopText($lang->get("pg_filewrong")."<br>");
		$form->setTopStyle("errorheader");
		$errors.="-WRONGFILE";
	else :
		// retrieving classname.
		$fcontent = join ('', file ($c["path"].'plugin/'.$filename));

		//search for class ***** extends Plugin {
		$cpos = strpos($fcontent, "class ");
		$epos = strpos($fcontent, "extends Plugin");
		if ($cpos==0 || $epos==0) :
			$form->addToTopText($lang->get("pg_classwrong")."<br>");
			$form->setTopStyle("errorheader");
			$errors.="-WRONGCLASS";
			return false;
		endif;
		global $classname;
		$classname = trim(substr($fcontent, $cpos+6, $epos-$cpos-6));

		include_once($c["path"].'plugin/'.$filename);
		$module = new $classname(0);
		$module->registration();
		// check, if already version installed
		$ver = getDBCell("modules", "VERSION", "MODULE_NAME = '".$module->name."'");

		// preparing source-variable for db-actions.
		global $source;
		$source = $filename;

		if ($ver=="") :
			$module->_installer();
			$form->addToTopText($lang->get("pg_installed"));
		elseif ($ver < $module->version) :
			$module->upgradeHandler->process("UPGRADE");
			$sql = "UPDATE modules SET VERSION = $module->version, DESCRIPTION='$module->description', CLASS='$classname', SOURCE='$source' WHERE MODULE_NAME='$module->name'";
			global $db;

			$query = new query($db, $sql);

			$form->addToTopText($lang->get("pg_upgraded"));
		elseif ($ver == $module->version) :
			$form->addToTopText($lang->get("pg_alreadyinstalled"));
		elseif ($ver > $module->version) :
			$form->addToTopText($lang->get("pg_newerinstalled"));
		endif;
	endif;
	
	writeDataTypeArray();
	$modId = getDBCell("modules", "MODULE_ID", "MODULE_NAME='$module->name'");
	$sql = "INSERT INTO pgn_config_store (CLTI_ID) VALUES ($modId)";
	$query = new query($db, $sql);
	return $modId;
}

/**
 * Returns all plugins that exist
 */
function getPluginFileList () {
	global $c;
	$ret = array ();
	$dirhandle = opendir($c["path"].'plugin/');
	while (false !== ($fname = readdir ($dirhandle))):
		$pgn_fname = $fname."/pgn_".$fname.".php";
		if (is_dir ($c["path"].'plugin/'.$fname) && file_exists($c["path"].'plugin/'.$pgn_fname)):
			$ret[] = array ($pgn_fname, rawurlencode($pgn_fname));
		endif;
	endwhile;
	closedir($dirhandle);
	return $ret;
} 

?>