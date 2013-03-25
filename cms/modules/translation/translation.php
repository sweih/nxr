<?
	/**********************************************************************
	 *	N/X - Web Content Management System
	 *	Copyright 2003 Sven Weih, FZI Research Center for Information Technologies
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
	require_once "../../config.inc.php";

	$auth = new auth("TRANSLATION");
	$page = new page("Translation");

	$filter = new Filter("internal_resources_languages", "LANGID");
	$filter->prevent_sysvar_disp = false;
	$filter->addRule($lang->get("name"), "NAME", "NAME");
	$filter->type_name = "Languages";
	$filtermenu = new Filtermenu("Languages", $filter);
	$filtermenu->tipp = "1. Save often. Even if saving the page takes up to 60 seconds.<br>2. Avoid any special chars if possible.<br>3. Please come back regularly to check if new texts need to be translated.<br>4. If there are any problems email to sven@nx.weih.de !";
	$form = new PanelForm($lang->get("translation", "N/X Translation System"), '', 'trans');

	$oid = value("oid", "NOSPACES");
	$vs = value("sync");
	$items = createDBCArray("internal_resources", "RESID", "LANGID='EN'", "ORDER BY RESID");

	if ($oid != "0") {
		$mylang = getDBCell("internal_resources_languages", "NAME", "LANGID = '$oid'");

		$editPanel = new Panel("Edit Language: $mylang");
		$go = "update";
		$page_action = "UPDATE";

		if ($page_state == "start")
			syncronizeLanguage ($oid);

		for ($i = 0; $i < count($items); $i++) {
			$specialID = $items[$i];

			$editPanel->add(new Label("lbl", "<b>Translate: </b>" . getDBCell("internal_resources", "VALUE", "RESID='" . $items[$i] . "' AND LANGID='EN'"), "standardlight", 2));
			$editPanel->add(new TextInput($items[$i], "internal_resources", "VALUE", "RESID='" . $items[$i] . "' AND LANGID='$oid'", "type:textarea,size:2,width:400"));
			$editPanel->add(new TextInput("Tooltip", "internal_resources", "TOOLTIP", "RESID='" . $items[$i] . "' AND LANGID='$oid'", "type:textarea,size:2,width:400"));
			$editPanel->add(new Separator());
		}

		$editPanel->add(new FormButtons(true));
		$editPanel->add(new Hidden("oid", $oid));
		// add the panel to the form and the form to the page.
		$form->addPanel($editPanel);
		$page->add($form);
	} else {
		$form = new Form($lang->get("stats", "Statistics"));

		$page_action = "UPDATE";
		$langs = createDBCArray("internal_resources_languages", "LANGID", "1");

		for ($i = 0; $i < count($langs); $i++) {
			if ($vs != "0")
				syncronizeLanguage ($langs[$i]);

			$langname = getDBCell("internal_resources_languages", "NAME", "LANGID = '" . $langs[$i] . "'");
			$form->add(new Label("lbl", "Statistics for " . $langname, "standard", 2));
			$translated = countRows("internal_resources", "RESID", "LANGID='" . $langs[$i] . "' AND VALUE IS NOT NULL AND VALUE <>''");
			$nottranslated1 = countRows("internal_resources", "RESID", "LANGID='" . $langs[$i] . "' AND VALUE IS NULL");
			$nottranslated2 = countRows("internal_resources", "RESID", "LANGID='" . $langs[$i] . "' AND VALUE =''");
			$nottranslated = $nottranslated1 + $nottranslated2;
			$form->add(new Label("lbl", "Translated: " . $translated, "standardlight"));
			$form->add(new Label("lbl", "Not translated: " . $nottranslated, "standardlight"));
			$form->add(new Separator());
		}

		$page->add($form);
	}

	$page->addMenu($filtermenu);
	$page->draw();
	$db->close();

	/**
	 * Checks, if for a given language the translation flag exists
	 * @param $oid OID of the language to syncronize, e.g. EN
	 */
	function syncronizeLanguage($oid) {
		global $db, $items;

		// do not sync for english.
		if ($oid != "EN") {
			for ($i = 0; $i < count($items); $i++) {
				if (getDBCell("internal_resources", "RESID", "RESID='" . $items[$i] . "' AND LANGID='$oid'") == "") {
					$sql = "INSERT INTO internal_resources (RESID, LANGID) VALUES('" . $items[$i] . "', '$oid')";

					$query = new query($db, $sql);
					$query->free();
				}
			}
		}
	}
?>