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

	/**
	 * Browser-Menu for browsing the objects in the CMS
	 * @package ContentManagement
	 */
	class Foldermenu extends StdMenu {
		var $title;
		var $action;
		var $staticMenu = false;
		var $pnode;
		var $pathToRoot;
		var $startNode = "0";
		var $rootTitle;
		
		
		/**
		 * Standard constructor
		 * @param string Title of the Browser
		 
		 */
		function Foldermenu($title, $saveVar="pnode") {
			global $c, $lang;
			$this->title = $title;

			if ($_GET["pnode"] != "") {
				pushVar($saveVar, value("pnode", "NUMERIC"));
			}
			$this->pnode = getVar($saveVar);
			$temp =  explode("?",$GLOBALS["REQUEST_URI"]);
			$this->action = $temp[0];
			$this->rootTitle = $lang->get("content", "Content");
			$this->staticMenu = $c["staticMenues"];
		}

		
		/**
		 * Draws one folder level
		 */
		function drawStaticFolder($categoryId, $ident=0) {			
				global $sid, $auth;
				$childs = createNameValueArrayEx("categories", "CATEGORY_NAME", "CATEGORY_ID", "PARENT_CATEGORY_ID = ".$categoryId, "ORDER BY CATEGORY_NAME");
				for ($j=0; $j < count($childs); $j++) {
			  	  $acl = aclFactory($childs[$j][1], "folder");
				  $acl->load();				
				  if ($acl->hasAccess($auth->userId)) {
				    $count = countRows("categories", "CATEGORY_ID", "PARENT_CATEGORY_ID = ".$childs[$j][1]);
				  	echo '<nobr>'.drawSpacer($ident*10, 1).'<a href="'.$this->action.'?pnode='.$childs[$j][1].'&sid='.$sid.'" class="wcopy">';
				    if ($count>0 && !(in_array($childs[$j][1], $this->pathToRoot) || $this->pnode == $childs[$j][1])) {
				      echo drawTreeImage('plus2.gif');	
				    } else {
				      echo drawTreeImage('minus2.gif');
				    }
				  	echo drawImage("folder.gif").'&nbsp;'.$childs[$j][0].'</a></nobr><br/>';				    				    
				  	if (in_array($childs[$j][1], $this->pathToRoot) || $this->pnode == $childs[$j][1]) {
				      $this->addCustomFolderContents($a, $childs[$j][1], $ident+1);
				  	  $this->drawStaticFolder($childs[$j][1], $ident+1);	
				    }
				  }
			  	}			  
		}
		
		/**
		 * draw the Tree-Folder structure
		 */
		function draw_folders() {
			global $c, $lang, $sid, $auth;

			echo "<table width=\"98%\" cellpadding=\"0\" cellspacing=\"0\">";
			echo "<tr><td >";
			echo '<div id="explore" class="sidebar_explore" style="height:400px">';		

			if ($this->staticMenu) {			  
			  $this->pathToRoot = array();
			  $id = $this->pnode;			  
			  
			  while ($id != 0) {
			    $id = getDBCell("categories", "PARENT_CATEGORY_ID", "CATEGORY_ID = $id");
			    array_unshift($this->pathToRoot, $id);			    
			  }
			  $this->drawStaticFolder($this->pathToRoot[0], 0);			  			  
			} else {
				// draw the folder here
				$icon = 'folder-expanded.gif';
				$expandedIcon = 'folder-expanded.gif';
				$menu = new HTML_TreeMenu();
				$startnode = new HTML_TreeNode(array (
				'text' => "&nbsp;".$this->rootTitle,
				'link' => $this->action . "?sid=$sid&pnode=".$this->startNode,
				'icon' => $icon,
				'expandedIcon' => $expandedIcon,
				isDynamic => true,
				'defaultClass' => "treemenu"
				));
				
				$this->buildMenu($startnode, $this->startNode);
				
				$menu->addItem($startnode);
				$treeMenu = new HTML_TreeMenu_DHTML($menu, array (
				'images' => $c["docroot"] . 'api/userinterface/tree/images',
				'defaultClass' => "treemenu"
				),                                  true);
				
				$treeMenu->printMenu();
			}	
			echo '</div>';
			echo '</td></tr>';
			echo "</table>";
		}
		
				

		/**
		 * Recurse down the folder tree and build the explorer
		 * @param object current startnode of the tree
		 * @param integer ID, of the parent of the tree
		 */
		function buildMenu(&$startnode, $parentID) {
			global $db, $sid, $auth;

			$icon = 'folder.gif';
			$expandedIcon = 'folder-expanded.gif';

			$sql = "SELECT CATEGORY_NAME, CATEGORY_ID from categories WHERE PARENT_CATEGORY_ID = $parentID AND DELETED = 0";
			$query = new query($db, $sql);
			$this->addCustomFolderContents($startnode, $parentID);

			while ($query->getrow()) {
				$name = $query->field("CATEGORY_NAME");
				$catid = $query->field("CATEGORY_ID");				
				$acl = aclFactory($catid, "folder");
				$acl->load();
				
				if ($acl->hasAccess($auth->userId)) {
					$node = &$startnode->addItem(new HTML_TreeNode(array (
						'text' => "&nbsp;".$name,
						'class' => "sidebar_explore",
						'link' => $this->action . "?sid=$sid&pnode=$catid",
						'icon' => $icon,
						'expandedIcon' => $expandedIcon,
						isDynamic => true
					)));

					$this->buildMenu($node, $catid);
				}
			}
		}

		/**
			* This function allows you to add additional custom contents to menus derived from this menu class.
		 * @param object current startnode of the tree
		 * @param integer ID, of the parent of the tree
		 */
		function addCustomFolderContents(&$startnode, $parentID, $ident=0) {
			// you may add your own foldercontents here when deriving a new class from this menu class.
			}

		/**
		   * internal. draw contents of your form.
		*/
		function draw_contents() {
			echo getFormHeadline($this->title);
			br();
			echo "<table width=\"100%\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\">\n";
			echo "<tr><td>\n";
			$this->draw_menu();
			echo "</td></tr>\n";
			echo "</table>";
			$this->draw_folders();			
		}

		/**
		 * writes HTML for Menu.
		 */
		function draw() {			
			$this->draw_header();
			$this->draw_contents();
			$this->draw_footer();			
		}
	}
?>