<?php

/**********************************************************************
 *	N/X - Web Content Management System
 *	Copyright 2004 Sven Weih
 *
 *      $Id: cluster_template.php,v 1.7 2004/12/08 21:15:38 sven_weih Exp $ *
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
  * Create a ClusterTemplate. Returns the GUID of the template for creating figures
  * @param string Name of the Template
  * @param string Description of the Template
  * @param string Layout of the template
  * @param integer ID of parent Category
  * @param integer ID of underlying META-Template
  * @param integer GUID of the new template. leave blank for auto-value.
  */
 function createClusterTemplate($name, $description, $layout = "", $categoryId = 0, $mtId=1, $cltId=null) {
   global $db;
   if ($cltId == null) {
   	$newId = nextGUID();
   } else {
     $newId = $cltId;	
   }
   
   $name = makeCopyName("cluster_templates", "NAME", parseSQL($name), "CATEGORY_ID = $categoryId AND VERSION=0"); 	
   $description = parseSQL($description);
   $layout = parseSQL($layout);
   if ($layout != "") {
   	$type = "1";
   } else {
   	$type = "0";	
   }
   
   $sql = "INSERT INTO cluster_templates (CLT_ID, MT_ID, CATEGORY_ID, NAME, DESCRIPTION, TEMPLATE, CLT_TYPE_ID, DELETED, VERSION) VALUES($newId, $mtId, $categoryId, '$name', '$description', '$layout', $type, 0,0)";
   $query = new query($db, $sql);
   $query->free();
   return $newId;
 }
 
 
 /**
  * Copy a cluster template
  * @param integer GUID of the Source CLT
  * @param string New Name of the CLT
  */
function copyClusterTemplate($source, $newName) {
  global $db;
  $newrows = array();
  $guid = nextGUID();
  $newrows["CLT_ID"] = $guid;;
  $newrows["NAME"] = $newName;
  copyRow("cluster_templates", "CLT_ID = $source", $newrows);
  
  // copy figures
  $sql = "SELECT CLTI_ID FROM cluster_template_items WHERE CLT_ID = $source";
  $query = new query($db, $sql);  
  while ($query->getrow()) {
	$id = $query->field("CLTI_ID");
	$newrows = array();
	$newrows["CLT_ID"] = $guid;
	$newrows["CLTI_ID"] = nextGUID();
	copyRow("cluster_template_items", "CLTI_ID=$id", $newrows);			
  }
  $query->free();
  return $guid; 	
}
 
 /**
  * Create a figure for a cluster-tempalte
  * @param string name of the figur
  * @param integer GUID of the Template
  * @param integer Position in the template
  * @param integer minimum cardinality of this figure
  * @param integer maximum cardinality of this figure
  * @param integer configuration value for this figure
  * @param integer type-ID of this figure (actually 1-8). Refer to table cluster_template_item_types
  */
 function createClusterTemplateFigure($name, $clt, $position, $maxcard, $mincard, $config, $type) {
   global $db;
   $name = makeCopyName("cluster_template_items", "NAME", parseSQL($name), "CLT_ID = $clt"); 	
   $newId = nextGUID();
   $sql = "INSERT INTO cluster_template_items (CLTI_ID, CLT_ID, NAME, POSITION, MINCARD, MAXCARD, FKID, CLTITYPE_ID, DELETED, VERSION) VALUES ($newId, $clt, '$name', $position, $mincard, $maxcard, $config, $type, 0,0)";
   $query = new query($db, $sql);
   $query->free();
   return $newId;
 }
 
 	/** 
* Launch a Cluster-Template
* @param integer ID to launch
* @param integer ID of the level to launch to
* @param integer ID of the variation to launch.
* @returns integer Translated ID after launch
*/
	function launchClusterTemplate($in, $level, $variation) {
		global $db;

		if (!checkACL($in)) {
			$out = translateState($in, $level);

			$sql = "SELECT NAME, DESCRIPTION, CATEGORY_ID, MT_ID, CLT_TYPE_ID, TEMPLATE FROM cluster_templates WHERE CLT_ID = $in";
			$query = new query($db, $sql);
			$query->getrow();
			$name = addslashes($query->field("NAME"));
			$desc = addslashes($query->field("DESCRIPTION"));
			$template = addslashes($query->field("TEMPLATE"));
			$cltTypeId = $query->field("CLT_TYPE_ID");
			$category = $query->field("CATEGORY_ID");
			$mtid = $query->field("MT_ID");
			$sql = "DELETE FROM cluster_templates WHERE CLT_ID = $out";
			$query = new query($db, $sql);
			$metalaunched = launchMetaTemplate($mtid, $level, $variation);

			$sql = "INSERT INTO cluster_templates (CLT_ID, MT_ID, CATEGORY_ID, NAME, DESCRIPTION, CLT_TYPE_ID, TEMPLATE, DELETED, VERSION) VALUES ($out, $metalaunched, $category, '$name', '$desc', $cltTypeId, '$template', 0, $level)";
			$query = new query($db, $sql);
 
			// launch ClusterTemplateItems
			$sql = "DELETE FROM cluster_template_items WHERE CLT_ID = $out";
			$query = new query($db, $sql);
			$sql = "SELECT CLTI_ID FROM cluster_template_items WHERE CLT_ID = $in AND DELETED = 0";
			$query = new query($db, $sql);

			while ($query->getrow()) {
				$id = $query->field("CLTI_ID");
				launchClusterTemplateItem($id, $out, $level, $variation);
			}

			$query->free();
			return $out;
		} else
			return translateState($in, $level);
	}

	/** 
	* Launch a Cluster-Template-Item
	* @param integer ID to launch
	* @param integer ID of the corresponding Cluster-Template
	* @param integer ID of the level to launch to
	* @param integer ID of the variation to launch.
	* @returns integer Translated ID after launch
	*/
	function launchClusterTemplateItem($in, $clt, $level, $variation) {
		global $db;

		if (!checkACL($in)) {
			$out = translateState($in, $level);

			$sql = "SELECT NAME, POSITION, MINCARD, MAXCARD, FKID, CLTITYPE_ID FROM cluster_template_items WHERE CLTI_ID = $in";
			$query = new query($db, $sql);
			$query->getrow();
			$name = addslashes($query->field("NAME"));
			$position = $query->field("POSITION");
			$type = $query->field("CLTITYPE_ID");
			$mincard = $query->field("MINCARD");
			$maxcard = $query->field("MAXCARD");
			$fkid = $query->field("FKID");
			$foreign = $fkid;

			// launch static content or cluster-instance.
			if ($type == 1)
				$foreign = launchContent($fkid, $level, $variation);

			if ($type == 2)
			  launchPgnConfigStore($in);
				
			if ($type == 3)
				$foreign = launchCluster($fkid, $level, $variation);

			if ($type == 7)
				$foreign = translateState($fkid, $level, false);
				
			$sql = "DELETE FROM cluster_template_items WHERE CLTI_ID = $out";
			$query = new query($db, $sql);
			$sql = "INSERT INTO cluster_template_items (CLTI_ID, CLT_ID, NAME, POSITION, MINCARD, MAXCARD, FKID, CLTITYPE_ID, DELETED, VERSION) ";
			$sql .= "VALUES ($out, $clt, '$name', $position, $mincard, $maxcard, $foreign, $type, 0, $level)";
			$query = new query($db, $sql);
			$query->free();
			return $out;
		} else
			return translateState($in, $level);
	}
	
	 /**
  * Delete a meta-template
  * @param integer GUID of the Template
  */
 function deleteClusterTemplate($id) {
   global $db;
   $items = createDBCArray("cluster_node", "CLNID", "CLT_ID = $id");
   for ($i=0; $i < count($items); $i++) {
     deleteClusterNode($items[$i]);	
   }
   
   $items = createDBCArray("cluster_template_items", "CLTI_ID", "CLT_ID = $id");
   for ($i=0; $i < count($items); $i++) {
     deleteClusterTemplateFigure($items[$i]);
   }
      
   deleteRow("cluster_templates", "CLT_ID = $id");
   $id = translateState($id, 10, false);
   deleteRow("cluster_templates", "CLT_ID = $id");
   
   $items = createDBCArray("cluster_node", "CLNID", "CLT_ID = $id");
   for ($i=0; $i < count($items); $i++) {
     deleteClusterNode($items[$i]);	
   }
   
   $items = createDBCArray("cluster_template_items", "CLTI_ID", "CLT_ID = $id");
   for ($i=0; $i < count($items); $i++) {
     deleteClusterTemplateFigure($items[$i]);
   }
   
  }


  /**
   * Launch the configstore of a plugin
   * @id integer GUID od the CLTI_ID
   */
  function launchPgnConfigStore($id) {
  	$out = translateState($id, 10, false);
  	deleteRow("pgn_config_store", "CLTI_ID = $out");
  	$replace["CLTI_ID"] = $out;
  	copyRow("pgn_config_store", "CLTI_ID = $id", $replace);
  }
  
  /**
  * Delete a meta-template-figure
  * @param integer GUID of the figure
  */
 function deleteClusterTemplateFigure($id) {
   deleteRow("cluster_template_items", "CLTI_ID = $id");
   $id = translateState($id, 10, false);
   deleteRow("cluster_template_items", "CLTI_ID = $id");
 }

	/**
	  * Create a tree of the CLuster-Templates.
	  * Recursive function. 	  
	  * @param array array with name-value pairs of the folders
	  * @param string prefix, which to write in front of all foldernames. Leave blank, is internally used.
	  * @param integer node where to start indexing
	  * @param boolean show compound clusters only.
	  */
	function createCLTTree(&$folder, $prefix="&nbsp;&gt;&nbsp;", $node=0, $compoundsOnly=false) {
		global $db;

		// find CLT in homenode first.
		($compoundsOnly > 0)? $filter = " AND CLT_TYPE_ID=1 ": $filter="";
		$sql = "SELECT NAME, CLT_ID FROM cluster_templates WHERE DELETED=0 AND CATEGORY_ID = $node  AND VERSION=0 $filter ORDER BY NAME ASC";
		$query = new query($db, $sql);

		while ($query->getrow()) {
			$name = $query->field("NAME");
			$id = $query->field("CLT_ID");
			$nextID = count($folder);
			$folder[$nextID][0] = $prefix . $name;
			$folder[$nextID][1] = $id;
		}

		$query->free();

		$sql = "SELECT CATEGORY_ID, CATEGORY_NAME from categories WHERE DELETED = 0 AND PARENT_CATEGORY_ID=$node ORDER BY CATEGORY_NAME ASC";
		$query = new query($db, $sql);

		while ($query->getrow()) {
			$name = $query->field("CATEGORY_NAME");

			$id = $query->field("CATEGORY_ID");
			$nprefix = $prefix . $name . $prefix;
			createCLTTree($folder, $nprefix, $id, $compoundsOnly);
		}

		$query->free();
	}

 
 ?>