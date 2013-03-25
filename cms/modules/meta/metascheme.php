<?php
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
	require_once "../../config.inc.php";

	$auth = new auth("META_TEMP");

	/**
	 * Containerform for META-Templates.
	 */
	class MetaTemplateForm extends ContainerForm {
		function process_object_dispatcher($id, $action) {
			global $lang, $go, $page_action, $page_state;

			if ($action == $lang->get("delete")) {
				$this->drawPage = "deleting";

				$text = $lang->get("delete"). ": <b>" . getDBCell($this->item_table, $this->item_name, $this->item_pkname . " = $id"). "</b><br><br>\n";
				$text .= "<input type=\"hidden\" name=\"did\" value=\"$id\">";
				$this->addTopYNPrompt($text . $lang->get("mt_delete"));
			} else if ($action == $lang->get("config")) {
				$cond = $this->item_pkname . " = $id";

				// configure DBO
				$page_action = "UPDATE";
				$page_state = "";
				$go = "UPDATE";
				$this->add(new TextInput($lang->get("name"), $this->item_table, $this->item_name, $cond, "type:text,size:32,width:200", "UNIQUE&MANDATORY"));
				$this->add(new Hidden("editing", $id));
				$this->drawPage = "editing";
				$this->draw_contents;
			} else if ($action == $lang->get("up")) {
				$this->move_up($id);

				$go = "UPDATE";
			} else if ($action == $lang->get("down")) {
				$this->move_down($id);

				$go = "UPDATE";
			}

			// update output!
			$this->filterItems();
		}

		/**
		 * Updates the changes towards the object with the given id.
		 * @param integer Id of the item, to be deleted.
		 */
		function process_object_update($id) {
			global $lang, $errors, $db, $page_action, $page_state;

			$cond = $this->item_pkname . " = $id";
			// configure DBO
			$page_action = "UPDATE";
			$page_state = "processing";
			$tip = new TextInput($lang->get("name"), $this->item_table, $this->item_name, $cond, "type:text,size:32,width:200", "UNIQUE&MANDATORY");
			$this->add($tip);
			$this->add(new Hidden("editing", $id));
			$this->check();
			$this->drawPage = "editing";

			if ($errors == "") {
				$name = value($this->item_table . "_" . $this->item_name);

				global $c_magic_quotes_gpc;

				if ($c_magic_quotes_gpc == 1)
					$name = str_replace("\\", "", $name);

				$pname = parseSQL($name);
				$sql = "UPDATE " . $this->item_table . " SET " . $this->item_name . " = '" . $pname . "' WHERE " . $this->item_pkname . " = $id";

				$query = new query($db, $sql);
				$query->free();
			}

			if ($errors == "") {
				$this->addToTopText($lang->get("savesuccess"));
			} else {
				$this->addToTopText($lang->get("procerror"));

				$this->setTopStyle("errorheader");
			}
		}

		
		
	
			/**
			 * Returns the type of the object on position 
			 * @param integer Position of the object information is asked for
			 */
			function getType($position) {
				return getDBCell($this->new_table, $this->new_name, $this->new_pkname . "=" . $this->objects[$position]["plugin"]);										
			}
				
			/**
			 * Returns the configuration of the object on position 
			 * @param integer Position of the object information is asked for
			 */
			function getInformation($position) {
				global $lang;
				return $lang->get("ready_to_use", "Ready to use");
		}
	}

	$page = new page("Meta-Template Scheme");

	$filter = new Filter("meta_templates", "MT_ID");
	$filter->addRule($lang->get("name"), "NAME", "NAME");
	$filter->setNewAction("meta.php");
	$filter->setAdditionalCondition("INTERNAL = 0 AND VERSION=0");
	$filter->icon = "li_meta.gif";
	$filter->type_name = "Meta Templates";

	$filtermenu = new Filtermenu($lang->get("metatemplates"), $filter);
	$page->tipp = $lang->get("help_metatemp", "A meta template is used for defining which metadata fields need to go on a new page. When you create a new metadata field in the template, each new page based on that template will contain that field. ");
	
	if (value("oid", "NUMERIC") != "0") {
		$form = new MetaTemplateForm($lang->get("mt_scheme"), "i_meta.gif");
		$form->width='100%';
		$form->headerlink = crHeaderLink($lang->get('mt_properties'), 'modules/meta/meta.php?sid='.$sid.'&oid='.$oid.'&go=update');
		$form->define_home("meta_templates", "MT_ID", $oid);
		$form->define_item("meta_template_items", "MTI_ID", "NAME", "MT_ID", "MTYPE_ID");
		$form->define_new("meta_datatypes", "MTYPE_ID");
		$page->add($form);
	}

	$page->addMenu($filtermenu);

	$page->draw();
	$db->close();
?>