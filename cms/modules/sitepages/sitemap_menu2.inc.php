<?
	/**********************************************************************
	 *	N/X - Web Content Management System
	 *	Copyright 2003-2004 Sven Weih, FZI Research Center for Information Technologies
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

	/**
	 * Menu for browsing the Sitemap.
	 * Highly customized!
	 * @package WebUserInterface
	 */
	class SitemapMenu2 extends Foldermenu {

		/**
		 * draw the Tree-Folder structure
		 */
		function draw_folders() {
			global $c, $lang, $sid;

			echo "<table width=\"98%\" cellpadding=\"0\" cellspacing=\"0\">";
			echo "<tr><td >";
			echo '<div id="explore" class="sidebar_explore" style="height:400px;">';
			
			// draw the folder here
			$icon = 'site_root.gif';
			$expandedIcon = 'site_root.gif';
			$menu = new HTML_TreeMenu();
			$startnode = new HTML_TreeNode(array (
				'text' => $lang->get("website", "Website"),
				'link' => $this->action . "?sid=$sid&pnode=0",
				'icon' => $icon,
				'expandedIcon' => $expandedIcon,
				isDynamic => true,
				'defaultClass' => "treemenu"
			));

			$this->buildSitemap($startnode, "0");

			$menu->addItem($startnode);
			$treeMenu = new HTML_TreeMenu_DHTML($menu, array (
				'images' => $c["docroot"] . 'api/userinterface/tree/images',
				'defaultClass' => "treemenu"
			),                                  true);

			$treeMenu->printMenu();

			echo '</div>';
			echo '</td></tr>';
			echo "</table>";
		}
		
		
		/**
		 * Build Sitemap structure
		 * @param $startnode Reference to the latest node.
		 * @param $parentId ID of the the page where to start with
		 */
		function buildSitemap(&$startnode, $parentId, $spmType="1", $isLocked=0) {
			global $db, $sid, $lang, $variation;
			
			// define icons
			$red = "site_red.gif";
			$green = "site_green.gif";
			$yellow = "site_yellow.gif";
			$siteadd = "site_add.gif";
			$folder = "folder.gif";
			$expandedfolder = "folder-expanded.gif";
			
			// Add new Funktion					
			if ($isLocked != 1) {
				$acl = 	$aclf = aclFactory($parentId, "page");
				$acl->load();				
				
				if ( $acl->checkAccessToFunction("ADD_SITEPAGE") && ($spmType == "1" || $parentId=="0")) {
				  $node = &$startnode->addItem(new HTML_TreeNode(array (
					'text' => $lang->get("sp_newpage"),
					'class' => "treemenu",
					'link' => $this->action . "?sid=$sid&mid=" . $parentId . "&action=newpage&go=insert",
					'icon' => $siteadd,
					'expandedIcon' => $siteadd,
					isDynamic => true
				  )));				
				}
			}
			
			// build tree
			$sql = "SELECT s.MENU_ID, s.NAME, s.IS_LOCKED, m.SPMTYPE_ID, sp.SPID FROM  sitepage_master m, sitemap s LEFT JOIN sitepage sp ON s.MENU_ID = sp.MENU_ID AND sp.POSITION < 2 WHERE s.DELETED=0 AND s.SPM_ID = m.SPM_ID AND s.VERSION=0 AND s.PARENT_ID = " . $parentId . " ORDER BY s.POSITION ASC";
			$query = new query($db, $sql);
			
			while ($query->getrow()) {
				$id = $query->field("MENU_ID");
				$type = $query->field("SPMTYPE_ID");
				$name = $query->field("NAME");
				$locked = $query->field("IS_LOCKED");
				$spid = $query->field("SPID");				
				if ($spid != "") {
                				if ($type == 1) {							
                					$href =  $this->action . "?sid=$sid&mid=" . $id . "&oid=$spid&go=update&action=editobject";
                					if (isSPVarLiveEx($spid, $variation)) {
                						$icon = $green;
                						$expandedicon = $green;
                					} else {
                					  	$icon = $red;
                					  	$expandedicon = $red;	
                					}
                				} else if ($type == 2 || $type == 3) {	
                					$icon = $folder;
                					$expandedicon = $folderexpanded;
                					$href =  $this->action . "?sid=$sid&mid=" . $parentId . "&oid=$id&action=pproperties&view=1";
                				}
                	
                				$acl = 	$aclf = aclFactory($id, "page");
                				$acl->load();						
                				if (   ($acl->checkAccessToFunction("SET_PAGE_ACCESS")
                		 			|| $acl->checkAccessToFunction("EDIT_CONTENT")
                		 			|| $acl->checkAccessToFunction("EDIT_META_DATA")
                		 			|| $acl->checkAccessToFunction("SITEPAGE_PROPS")
                		 			|| $acl->checkAccessToFunction("MENU"))) {
                		 
                					$node = &$startnode->addItem(new HTML_TreeNode(array (
                						'text' => $name,
                						'class' => "treemenu",
                						'link' => $href,
                						'icon' => $icon,
                						'expandedIcon' => $expandedicon,
                						isDynamic => true
                					)));				
                				
                					if ($type == 1) {
                				  		$this->buildSitemap($node, $id, $type, $locked);
                					} else if ($type==2 || $type == 3) {
                						$this->buildInstances($node, $id, $locked);	
                					}
                			}
				}
			}
		}
		
		/* 
		 * Build a list with all instances of a multipage or portalpage
		 * @param mixed tree-object reference
		 * @param mixed MEnuId of Sitemap table
		 */
		function buildInstances(& $startnode, $menuId, $isLocked=0) {		
			global $variation, $sid, $lang;
			$siteadd = "site_add.gif";
			$red = "site_red.gif";
			$green = "site_green.gif";
			
			if ($isLocked != 1) {
				  $node = &$startnode->addItem(new HTML_TreeNode(array (
					'text' => $lang->get("sp_newinstance"),
					'class' => "treemenu",
					'link' => $this->action . "?sid=$sid&go=insert&mid=" . $menuId . "&action=newinstance", 
					'icon' => $siteadd,
					'expandedIcon' => $siteadd,
					isDynamic => true
				  )));				
			}
			
			
						
			$spids = createDBCArray("sitepage", "SPID", "MENU_ID = " . $menuId, "ORDER BY POSITION");	
			for ($i = 0; $i < count($spids); $i++) {			
				$href = $this->action . "?sid=$sid&mid=" . $menuId . "&action=editcontent&oid=".$spids[$i];
				$title = getMenu($spids[$i], $variation);
				if (isSPVarLiveEx($spids[$i], $variation)) {
					$icon = $green;
					$expandedicon = $green;	
				} else {
					$icon = $red;
					$expandedicon = $red;	
				}
				
				$node = &$startnode->addItem(new HTML_TreeNode(array (
					'text' => $title[0],
					'class' => "treemenu",
					'link' => $href,
					'icon' => $icon,
					'expandedIcon' => $expandedicon,
					isDynamic => true
				)));
			}
		}	
	}
?>