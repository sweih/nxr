<?
	/**********************************************************************
	 *	N/X - Web Content Management System
	 *	Copyright 2003 Sven Weih
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

	/**
	 * Container for displaying a Content-Objects & Co.
	 * @package ContentManagement
	 */
	class AbstractEnvelope extends Container {
		
		var $clti;
		var $cl;
		var $eid;
		var $mincard;
		var $maxcard;
		var $name;
		var $plugin;
		var $container = null;
		var $deleteable = false;
		var $action;
        var $saction;
		var $members = null;
		var $membersCount = 0;
		var $editState = false;
		var $editor = false;
		var $developer = false;
    var $forceEditAll = false;
    

		/**
		 * Standard constructor
		 * @param integer ID of the Cluster-Template-Item that is used to create this Content-Item.
		 * @param integer ID of the Cluster-Variation the clti is from.
     * @param booleean Forced Edit-All-Mode
     * @param booleaan Suppress permissions checks
		 */

		function AbstractEnvelope($clti, $cl, $forceEditAll=false, $isExternal = false) {
			global $db, $lang, $aclf, $isArticle;
      		$this->forceEditAll = $forceEditAll;
      		$this->clti = $clti;
			$this->cl = $cl;
			$this->action = value("action");
            $this->saction = value("saction");
			$this->eid = value("eid", "NUMERIC");
			if ($this->action == "0" || $this->action == "")
				$this->action = value("acstate");
            if (value("saction", "", "") != "")
                $this->action = getVar("lastaction");
			// Get the other configuration variables...
			$sql = "SELECT MINCARD, MAXCARD, NAME, FKID FROM cluster_template_items WHERE CLTI_ID = $clti";

			$query = new query($db, $sql);
			$query->getrow();
			$this->mincard = $query->field("MINCARD");
			$this->maxcard = $query->field("MAXCARD");
			$this->name = $query->field("NAME");
			$this->plugin = $query->field("FKID");
			$query->free();
			$this->editState = (($this->action == $lang->get("edit_all")) || ($this->action == $lang->get("save") || $this->action == $lang->get("save_back") || $this->action == "editsingle" || value("status") == "editsingle") && $this->action != $lang->get("back")) || $isArticle || $forceEditAll;
			if ($isExternal) {
			  $this->developer = true;
			  $this->editor    = true;	
			} else {
			  $this->developer =  $aclf->checkAccessToFunction("EDIT_CL_CONTENT");
			  $this->editor =  $aclf->checkAccessToFunction("EDIT_CL_CONTENT");
			}
			
			$this->members = $this->getItemData();
			$this->prepare_overview();
		}
		
		/**
		 * Retrieve the Items of the Template figure
		 */
		function getItemData() {
			global $db;
			$members = null;
			$sql = "SELECT CLCID, TITLE FROM cluster_content WHERE CLID = $this->cl AND CLTI_ID = $this->clti ORDER BY POSITION";
			$query = new query($db, $sql);
			while ($query->getrow()) {
				$ni = count($members);
				$members[$ni][0] = $query->field("TITLE");
				$members[$ni][1] = $query->field("CLCID");
			}
			$query->free();	
			$this->membersCount = count($members);
            return $members;
		}
		
		/**
		 * Create new items
		 */
		function createItems() {
			global $db;				
			$allowed_number = ($this->maxcard - count($this->members));
			if ($allowed_number < 0) $allowed_number = 0;
			$number_of_instances = value("number_of_instances".$this->clti, "NUMERIC");
			if ($allowed_number < $number_of_instances)$number_of_instances = $allowed_number;
				
			$newpos = getMax("cluster_content", "POSITION", "CLTI_ID = $this->clti AND CLID = $this->cl") + 1;
			if ($newpos < 1) $newpos=1;
			$created = false;	
			for ($i=0; $i < $number_of_instances; $i++) {
				$nextId = nextGUID();
				$ssql = "INSERT INTO cluster_content (CLCID, CLID, CLTI_ID, POSITION, TITLE, FKID, DELETED) VALUES ";
				$ssql .= "($nextId, $this->cl, $this->clti, $newpos+$i, '', 0, 0)";
				$synq = new query($db, $ssql);
				$this->createReferencedItem($nextId);
				$created = true;
			}
			if ($created) $this->members = $this->getItemData();
		}
		
		/**
		 * Delete one item out of the list
		 */
		function deleteItem() {
			global $db;
			$deleted = false;
			for ($i = 0; $i < count($this->members); $i++) {
				if ($this->eid == $this->members[$i][1]) {
					$sql = "DELETE FROM cluster_content WHERE CLCID = $this->eid";
					$query = new query($db, $sql);
					$query->free();
					if ($this->membersCount > 1) sortTableRows("cluster_content", "CLCID", "POSITION", "CLTI_ID = $this->clti AND CLID = $this->cl");
					$deleted = true;
                    $this->deleteReferencedItem($this->eid);
				}
			}
			if ($deleted) {
				syncCluster ($this->cl);
				$this->members = $this->getItemData();					
			}
		}
		
		
		/**
		 * Draw the input boxes needed for editing the contents in the envelope.
		 * @param integer id of cluster_content.CLCID
		 */
		function getSingleEdit($id) {
			// abstract	
		}
		
		/**
		 * Override this function for deleting not just list entries but also the referenced
		 * contents
		 * @param integer ID of the content to delete
		 */
		function deleteReferencedItem($id) {
			// abstract.
		}
		
		/**
		 * Override this function for createing not just list entries but also the referenced
		 * contents
		 * @param integer ID of the content to create.
		 */
		function createReferencedItem($id) {
			// abstract method. override.	
		}
		
		/**
		 * Move one item up or down
		 * @param string direction [UP|DOWN]
		 */
		function move($direction="UP") {
			$moved = false;
			$eid = value("eid", "NUMERIC");
			for ($i = 0; $i < count($this->members); $i++) {
				if ($eid == $this->members[$i][1]) {
					if (strtoupper($direction)=="UP") {
						moveRowUp("cluster_content", "CLCID", $eid, "POSITION", "CLTI_ID = $this->clti AND CLID = $this->cl");
						$moved=true;
					} else if (strtoupper($direction) =="DOWN") {
						moveRowDown("cluster_content", "CLCID", $eid, "POSITION", "CLTI_ID = $this->clti AND CLID = $this->cl");
						$moved=true;
					}
				}
			}
			if ($moved) $this->members = $this->getItemData();	
		}
		
				/**
		 * Prepare and declare wui for overview
		 */
		function prepare_overview() {
			global $lang, $db, $sid, $specialID;

			// initializing
			$doc = doc();
			$oid = value("oid", "NUMERIC");

			if ($this->membersCount > $this->mincard && $this->developer)
				$this->deleteable = true;

			// process moving up and moving down....
			if ($this->saction == "up" && ($this->editor || $this->developer)) {
				$this->move("UP");
			} else if ($this->saction == "down" && ($this->editor || $this->developer)) {
				$this->move("DOWN");
			}

			// process adding of items...
			if (($this->saction == "addci_".$this->clti) && ($this->editor || $this->developer)) {
				$this->createItems();		
			} 
			
			// process delete ...
			if ($this->saction == "delete" && value("back") != $lang->get("no") && ($this->editor || $this->developer)) {
				$this->deleteItem();
			} 

			if ((($this->action == "editsingle" || (value("status") == "editsingle" && $this->action != $lang->get("back"))) && $this->editor) && ! $this->forceEditAll) {
				for ($i = 0; $i < count($this->members); $i++) {
					if ($this->eid == $this->members[$i][1]) {
						$this->add(new Hidden("status", "editsingle"));
						$this->add(new Hidden("eid", $this->eid));
						$this->add(new Hidden("processing", "yes"));
						$this->getSingleEdit($this->members[$i][1]);
					}
				}
			} else {
				// draw the main view of the content-envelope
				if ($this->membersCount == 1 && $this->maxcard == 1 && $this->mincard == 1) {
					$labeltext = "<b>" . $this->name . "</b>";
					// Draw the title bar 

					$container = new HtmlContainer('box', 'headbox',2);
					// Edit-Button

					if ($this->editor && ! $this->editState) {
					    $menuLabel = crLink($lang->get("edit"), $doc . "?sid=$sid&oid=$oid&action=editsingle&eid=" . $this->members[0][1], "box");
					} else {
						$menuLabel = '&nbsp;';						
					}					
					$table = '<table width="100%" border="0" cellpadding="0" cellspacing="0">';
					$table.= '<tr><td>'.$labeltext.'</td>';
					$table.= '<td align="right"><b>'.$menuLabel.'</b></td></tr>';
					$table.= '</table>';					
					$container->add($table);					
					$this->add($container);
					$this->add(new Cell('', '', 2, 10, 10));
					$this->getSingleEdit($this->members[0][1]);
					
				// processing of lists.	
				} else {
					$labeltext = '<b>'.$this->name.'</b>';
					$container2 = new HtmlContainer('box', 'headbox',2);
					if ($this->mincard != 1 || $this->maxcard != 1) {
						$labeltext .= " &nbsp;($this->mincard - $this->maxcard)";
					}

					$menuLabel = "";

					// Add-Button
					if ($this->membersCount < $this->maxcard) {
						$toggleButton = new LinkButtonInline("toggle_create_".$this->clti, $lang->get("create_instances", "Create Instances"), "box", "button", "toggle('crinst_".$this->clti."')");
						$menuLabel .= $toggleButton->draw();
					}
									
					$table = '<table width="100%" border="0" cellpadding="0" cellspacing="0">';
					$table.= '<tr><td>'.$labeltext.'</td>';
					$table.= '<td align="right"><b>'.$menuLabel.'</b></td></tr>';
					$table.= '</table>';					
					$container2->add($table);					
					$this->add($container2);															
									
					// Add Toggle-Field
					$container = new Container(3);
					$container->add(new Label("lbl", $lang->get("number_of_instances", "Please specify how many instances you want to create"), "standardlight", 3));
					$container->add(new Input("number_of_instances".$this->clti, "1", "standardlight", 2, "", 100));

					
					if ($this->editState) {
						$createInstancesJS = "javascript:if (confirm('".$lang->get("confirm_unsaved_changes", "Note: Unsaved changes will be lost if you proceed. If you have already edited something, you may cancel now and save your work. Proceed ?")."')) { document.form1.saction.value='addci_".$this->clti."';document.form1.submit(); };";
					} else {
						$createInstancesJS = "javascript:document.form1.saction.value='addci_".$this->clti."';document.form1.submit();";
					}

					$container->add(new ButtonInCell("create_".$this->clti, $lang->get("create_instances", "Create instances"), "standardlight navelement", "button", $createInstancesJS));
					$this->add(new IDWrapper("crinst_".$this->clti, $container, "embedded", ' style="display:none;" ', 3));	
					$this->add(new Cell('clc', '', 2,10,10));					
					// draw list content
					$container0 = array();
					for ($i = 0; $i < $this->membersCount; $i++) {
						$labeltext = $this->name . '&nbsp;&nbsp;'.($i + 1) . "/" . $this->maxcard . "&nbsp;" . $this->members[$i][0] . "";

						// Draw one instance
						$table = '<table width="100%" cellpadding="0" cellspacing="0" border="0">';
						$table.= '<tr><td width="200">'.$labeltext.'</td>';
						$table.= '<td align="right">';					
											

						$menuLabel = "";
						// Edit-Button
						if ($this->editState) {
							$menuLabel = crLink($lang->get("up"), "javascript:confirmAction('".$lang->get("confirm_unsaved_changes")."', '".$doc."?sid=$sid&oid=$oid&saction=up&eid=".$this->members[$i][1]."');", "box");
							$menuLabel .= "&nbsp;".crLink($lang->get("down"), "javascript:confirmAction('".$lang->get("confirm_unsaved_changes")."', '".$doc . "?sid=$sid&oid=$oid&saction=down&eid=" . $this->members[$i][1]."');", "box");

							$menuLabel .= "&nbsp;&nbsp" . crLink($lang->get("delete"), "javascript:if (confirm('".$lang->get("confirm_unsaved_changes")."')) { confirmAction('".$lang->get("confirm_delete", "Do you really want to delete this item?")."', '". $doc . "?sid=$sid&oid=$oid&saction=delete&eid=" . $this->members[$i][1]."') } ;", "box");
						} else {
							$menuLabel = crLink($lang->get("up"), $doc . "?sid=$sid&oid=$oid&saction=up&eid=" . $this->members[$i][1], "box");
							$menuLabel .= "&nbsp;".crLink($lang->get("down"), $doc . "?sid=$sid&oid=$oid&saction=down&eid=" . $this->members[$i][1], "box");	
							$menuLabel .= "&nbsp;&nbsp" . crLink($lang->get("delete"), "javascript:confirmAction('".$lang->get("confirm_delete", "Do you really want to delete this item?")."', '". $doc . "?sid=$sid&oid=$oid&saction=delete&eid=" . $this->members[$i][1]."');", "box");
						}
						
						if ($this->editor && !$this->editState)
							$menuLabel .= "&nbsp;".crLink($lang->get("edit"), $doc . "?sid=$sid&oid=$oid&action=editsingle&eid=" . $this->members[$i][1], "box");

						$table.= $menuLabel;				
						$table.= '</td></tr></table>';
						$container0[$i] = new HTMLContainer('subbox', 'headbox2', 2);
						$container0[$i]->add($table);
						$this->add($container0[$i]);
						$this->getSingleEdit($this->members[$i][1]);
						$this->add(new Cell('clc', '', 2, 10,10));
					}
				}
			}
		}
		
		
		/**
		 * Draws the Envelope.
		 */
		function draw() {

			echo "<td colspan=\"2\" width=\"100%\"><table width=\"100%\" cellpadding=\"0\" cellspacing=\"0\">\n";
			if (count($this->container) > 0 ) {
				echo "<tr>";
				$cl1 = new Cell("cl1", $style, 1, 200, 1);
				$cl3 = new Cell("cl2", $style, 1, 400, 1);
				$cl1->draw();
				$cl3->draw();
				echo "</tr>";
			}
			$col = 1;
			for ($i = 0; $i < count($this->container); $i++) {
				if ($col == 1)
					echo "<tr>";
				$col += $this->container[$i]->draw();
				if ($col > 2) {
					$col = 0;
					echo "</tr>";
				}
				$col++;
			}
			if ($col != 1)
				echo "</tr>";
			if (count($this->container) > 0 ) {
				echo "<tr>";
				$cl = new Cell("cl3", $style, 3, 600,20);
				$cl->draw();	
				echo "</tr>";
			}
			echo "</table></td>";
			return 2;
		}
	}
?>