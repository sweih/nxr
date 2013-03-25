<?
	/**********************************************************************
	 *	N/X - Web Content Management System
	 *	Copyright 2002 Sven Weih
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
	require_once $c["path"] . "api/userinterface/panels/acl_info.php";

	/**
	 * Draws a to the database-connected ACL-Editor
	 * @package DatabaseConnectedInput
	 */
	class ACLEditor extends Container {
		var $acl;

		var $title;

		/**
		  * standard constructor
		  */
		function ACLEditor($acl, $title = "") {
			global $lang;

			Container::Container(3);
			$this->acl = $acl;
			$this->title = $title;
			$id = $acl->guid;

			// load the data of the field.
			global $page_state, $page_action;

			if ($page_state == "processing") {
				$acl->inherit = value("acl_inherit" . $id);

				// because the config is not displayed in inherit mode, this is necessarry to load the data!
				if ($acl->inherit != value("checkednow")) {
					$page_state = "start";
				} else {
					$acl->owner = value("acl_owner" . $id);
				}
			}

			// prepare the form....
			$clc = new Cell("clc", "embedded", 1, 195, 1);
			$this->add($clc);
			$this->add($clc);
			$this->add($clc);
			$this->add(new Subtitle("title", $lang->get("acl_title", "Setting permissions for "). "<b> $title</b>", 3));
			$this->add(new CheckboxTxt("acl_inherit" . $id, 1, $lang->get("acl_inherit", "Inherit permissions from parent"), "standard", $acl->inherit, 3));
			$this->add(new Hidden("checkednow", $acl->inherit));

			if ($acl->inherit) {
				$this->add(new Label("lbl", $lang->get("acl_inherit_note", "Note: When the box is checked, there is no other configuration available!"), "standardlight", 3));

				$this->add(new Subtitle("title", $lang->get("acl_info", "Inherited Access from parent"), 3));
				$this->add(new ACLInfo($acl));
			} else {
				$this->add(new Subtitle("title", $lang->get("alc_owner", "Set owner"), 3));

				$groups = createNameValueArrayEx("groups", "GROUP_NAME", "GROUP_ID", "1");
				$this->add(new Label("lbl", $lang->get("acl_owner", "Owner"), "standard", 1));
				$this->add(new Select("acl_owner" . $id, $groups, "standard", $acl->owner, 1, "", 250, 2));

				$this->add(new Subtitle("title", $lang->get("acl_access", "Edit permissions in groups on "). "<b> $title</b>", 3));
				// Create Comboeditor.
				$groups = createNameValueArray("groups", "GROUP_NAME", "GROUP_ID", "1");
				$roles = createNameValueArray("roles", "ROLE_NAME", "ROLE_ID", "1");
				$headlines = array (
					"head1" => $lang->get("acl_groups", "Select Group to add"),
					"head1_selected" => $lang->get("acl_groupedit", "Select group to edit roles"),
					"head2_selected" => $lang->get("acl_role", "Select roles for group")
				);

				$pk = array ( array (
					"GUID",
					$id,
					"NUMBER"
				) );

				$comboeditor = new CombinationEditor($groups, $roles, $headlines, "acl_relations", $pk, "ACCESSOR_GUID", "NUMBER", "ROLE_ID", "NUMBER");
				$this->add($comboeditor);
			}

			$acl->syncEx();
		}

		/** 
		 * Draw the control
		 */
		function draw() {
			echo "<td colspan=\"2\">";

			echo "<table width=\"100%\" cellpadding=\"0\" cellspacing=\"0\" border=\"0\">";
			echo "<!-- here we start -->";
			$this->draw_contents();
			echo "</table>";
			echo "</td>";
		}

		/**
		 *save the values to the db...
		 */
		function process() {
			global $db, $oid;

			Container::process();
		}

		/**
		 * Check for correctness
		 */
		function check() { }
	}
?>