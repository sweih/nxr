<?
	/**********************************************************************
	 *	N/X - Web Content Management System
	 *	Copyright 2002 Sven Weih & Fabian König
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
	 * Browser-Menu for browsing the clusters in the CMS
		* Actually this is nearly the same as Foldermenu, except that it also displays ClusterTemplates as Folders and, if you like, ClusterNodes.
		* If $c["clusterTemplateBrowser"] then browse cluster-templates.
		* @package ContentManagement
		*/
	class Clustermenu extends Foldermenu {

		var $title;
		var $action;

		/**
		 * Standard constructor
		 * @param string Title of the Browser
		 */
		function Clustermenu($title) { Foldermenu::Foldermenu($title); }

		/**
			* This function allows you to add additional custom contents to menus derived from this menu class.
		 * @param object current startnode of the tree
		 * @param integer ID, of the parent of the tree
		 */
		function addCustomFolderContents(&$startnode, $parentID, $ident=0) { 
			if ($this->staticMenu) {
			  $this->addStaticCLTFolders($parentID, $ident);	
			} else {
			  $this->addCLTFolders($startnode, $parentID); 
			}
		}

		/**
			* add ClusterTemplates as folders in order to cary the corresponding cluster instances later on
			* @param object current node to add CLT-Folders to
			* @param integer ID of the parent of the tree
			*/
		function addCLTFolders(&$startnode, $parentID) {
			global $db, $sid, $c, $lang, $aclf;

			if ($c["clustertemplatebrowser"] && $aclf->checkAccessToFunction("NEW_CL_TEMP")) {
				$node = &$startnode->addItem(new HTML_TreeNode(array (
					'text' => $lang->get("new", "New"),
					'class' => "sidebar_explore",					
					'link' => $this->action . "?sid=$sid&pnode=$parentID&action=newobject&go=insert",
					'icon' => 'clt_add.gif',
					'expandedIcon' => 'clt_add.gif',
					isDynamic => true
				)));
			}
			
			$CLTicon = 'clt.gif';
			$expandedCLTicon = 'clt.gif';

			$sql = "SELECT NAME, CLT_ID FROM cluster_templates WHERE CATEGORY_ID = $parentID AND DELETED = 0 AND VERSION = 0 ORDER BY NAME";
			$query = new query($db, $sql);
			if ($c["clustertemplatebrowser"]) {
				$action = "&action=editobject";	
			} else {
				$action ="";
			}
			
			while ($query->getrow()) {				
				$name = $query->field("NAME");
				$clt = $query->field("CLT_ID");
				
				if ($c["clustertemplatebrowser"]) {
					$href = $this->action . "?sid=$sid&pnode=$parentID&oid=$clt&action=editobject";	
				} else {
					$href = $this->action . "?sid=$sid&pnode=$parentID&clt=$clt";
				}
				
				$node = &$startnode->addItem(new HTML_TreeNode(array (
					'text' => $name,
					'class' => "sidebar_explore",
					'link' => $href,
					'icon' => $CLTicon,
					'expandedIcon' => $expandedCLTicon,
					isDynamic => true
				)));
				/** If you'd like to display the Cluster-Nodes as Folders in the left menu, just remove the comment from the following line. **/
				if (! $c["disableClustersInTree"] && ! $c["clustertemplatebrowser"]) $this->addCLNFolders($node, $parentID, $clt);
			}
		}
		
		
		
		/**
			* add ClusterTemplates as folders in order to cary the corresponding cluster instances later on			
			* @param integer ID of the parent of the tree
			* @param integer level of menu
			*/
		function addStaticCLTFolders($parentID, $ident) {
			global $db, $sid, $c, $lang, $aclf;
			if ($c["clustertemplatebrowser"] && $aclf->checkAccessToFunction("NEW_CL_TEMP")) {
				echo '<nobr>'.drawSpacer($ident*10, 1).'<a href="'.$this->action.'?pnode='.$parentID.'&sid='.$sid.'&action=newobject&go=insert" class="wcopy">'.drawTreeImage("clt_add2.gif").'&nbsp;'.$lang->get("new").'</a></nobr><br/>';				    
			}	
			
			$sql = "SELECT NAME, CLT_ID FROM cluster_templates WHERE CATEGORY_ID = $parentID AND DELETED = 0 AND VERSION = 0 ORDER BY NAME";
			$query = new query($db, $sql);
			if ($c["clustertemplatebrowser"]) {
				$action = "&action=editobject";	
			} else {
				$action ="";
			}
			
			while ($query->getrow()) {				
				$name = $query->field("NAME");
				$clt = $query->field("CLT_ID");
				
				if ($c["clustertemplatebrowser"]) {
					$href = $this->action . "?sid=$sid&pnode=$parentID&oid=$clt&action=editobject";	
				} else {
					$href = $this->action . "?sid=$sid&pnode=$parentID&clt=$clt";
				}
				
				echo '<nobr>'.drawSpacer($ident*10, 1).'<a href="'.$href.'" class="wcopy">'.drawTreeImage("clt2.gif").'&nbsp;'.$name.'</a></nobr><br/>';				    			

			}
		}
		

		/**
			* add ClusterNodes as folders in order to edit their contents
			* @param object current node to add CLI-Folders to
			* @param
			* @param integer ID of the parent of the tree
		 */
		function addCLNFolders(&$startnode, $parentID, $clt) {
			global $db, $sid;

			$CLNicon = 'cln.gif';
			$expandedCLNicon = 'cln.gif';

			$sql = "SELECT NAME, CLNID FROM cluster_node WHERE CLT_ID = $clt AND DELETED = 0 AND VERSION = 0 ORDER BY NAME";
			$query = new query($db, $sql);

			while ($query->getrow()) {
				$name = $query->field("NAME");

				$clnid = $query->field("CLNID");
				$node = &$startnode->addItem(new HTML_TreeNode(array (
					'text' => $name,
					'class' => "sidebar_explore",
					'link' => $this->action . "?sid=$sid&oid=$clnid&view=1",
					'icon' => $CLNicon,
					'expandedIcon' => $expandedCLNicon,
					isDynamic => true
				)));
			}
		}
	}
?>