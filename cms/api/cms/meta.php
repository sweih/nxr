<?php

/**********************************************************************
 *	N/X - Web Content Management System
 *	Copyright 2004 Sven Weih
 *
 *      $Id: meta.php,v 1.5 2004/01/05 23:18:34 sven_weih Exp $ *
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
  * Create metadata for on object
  * @param integer GUID of the object
  * @param integer GUID of the underlying meta-template
  * @param string Name of the figure
  * @param string Value of the figure
  * @return ID of the metadata or false if failed.
  */
  function createMetaData($fkid, $mtid, $name, $value) {
    global $db;
    $name = parseSQL($name);
    $value = parseSQL($value);
    $mti = getDBCell("meta_template_items", "MTI_ID", "MT_ID = $mtid AND UPPER(NAME) = '$name'");
    if ($mti != "") {
      $newId = nextGUID();
      $sql = "INSERT INTO meta (MID, MTI_ID, CID, VALUE, DELETED) VALUES ($newId, $mti, $fkid, '$value', 0)";
      $query = new query($db, $sql);
    } else {
      return false;
    }
    return $newId;
  }
 
 /**
  * Create a MetaTemplate. Returns the GUID of the template for creating figures
  * @param string Name of the Template
  * @param string Description of the Template
  * @param integer ID that shall be used to create the template. Leave blank if auto-generate Key.
  */
 function createMetaTemplate($name, $description, $id=null) {
   global $db;
   if ($id == null) {
   	$newId = nextGUID();
   } else {
   	$newId = $id;
   }
   $name = makeCopyName("meta_templates", "NAME", parseSQL($name), "VERSION=0");
   $description = parseSQL($description);
   $sql = "INSERT INTO meta_templates (MT_ID, NAME, DESCRIPTION, INTERNAL, VERSION) VALUES($newId, '$name', '$description', 0,0)";
   $query = new query($db, $sql);
   $query->free();
   return $newId;
 }
 
 /**
  * Create a figure in a Meta-Template
  * @param integer ID of the Meta-Template
  * @param string NAme
  * @param string Description
  * @param integer Position
  * @param integer Datatype-Id, compare table meta_template_item_types
  */
 function createMetaTemplateFigure($mtid, $name, $position, $type) {
   global $db;
   $newId = nextGUID();
   $name = makeCopyName("meta_template_items", "NAME", parseSQL($name), "MT_ID = $mtid");
   $sql = "INSERT INTO meta_template_items (MTI_ID, MT_ID, NAME, POSITION, MTYPE_ID, VERSION) VALUES ($newId, $mtid, '$name', $position, $type, 0)";
   $query = new query($db, $sql);
   $query->free();
   return $newId;  
 }
 
 /**
  * Deletes the meta-data for an object
  * @param integer GUID of the object 
  */
  function deleteMetaData($id) {
    deleteRow("meta", "CID = $id");
    $id = translateState($id, 10, false);
    deleteRow("meta", "CID = $id");
  }
 
 /**
  * Delete a meta-template
  * @param integer GUID of the Template
  */
 function deleteMetaTemplate($id) {
   global $db;
   $items = createDBCArray("meta_template_items", "MTI_ID", "MT_ID = $id");
   for ($i=0; $i < count($items); $i++) {
     deleteMetaTemplateFigure($items[$i]);
   }
   
   deleteRow("meta_templates", "MT_ID = $id");
   $id = translateState($id, 10, false);
   deleteRow("meta_templates", "MT_ID = $id");
  }

 /**
  * Delete a meta-template-figure
  * @param integer GUID of the figure
  */
 function deleteMetaTemplateFigure($id) {
   deleteRow("meta", "MTI_ID = $id");
   deleteRow("meta_template_items", "MTI_ID = $id");
   $id = translateState($id, 10, false);
   deleteRow("meta", "MTI_ID = $id");
   deleteRow("meta_template_items", "MTI_ID = $id");   
 }
 
 
  /**
  * Launch a Meta-Tag
  * @param integer ID to launch
  * @param integer ID of the level to launch to
  * @returns integer Translated ID after launch
  */
  function launchMeta($in, $level) {
	global $db;

	if (!checkACL($in)) {
		$out = translateState($in, $level);
		$sql = "SELECT MTI_ID, CID, VALUE FROM meta WHERE MID = $in AND DELETED=0";
		$query = new query($db, $sql);
		$query->getrow();
		$value = addslashes($query->field("VALUE"));
		$mti_id = $query->field("MTI_ID");
		$cid = $query->field("CID");

		$cidTrans = translateState($cid, $level, false);
		$mtiTrans = translateState($mti_id, $level, false);

		$sql = "DELETE FROM meta WHERE MID = $out";
		$query = new query($db, $sql);
		$sql = "INSERT INTO meta (MID, MTI_ID, CID, VALUE, DELETED) VALUES ($out, $mtiTrans, $cidTrans, '$value',0)";
		$query = new query($db, $sql);
		$query->free();
		return $out;
	} else
		return translateState($in, $level);
  }


  /** 
  * Launch a Meta-Template
  * @param integer ID to launch
  * @param integer ID of the level to launch to
  * @returns integer Translated ID after launch
  */
  function launchMetaTemplate($in, $level) {
	global $db;

	if (!checkACL($in)) {
		$out = translateState($in, $level);
		$sql = "SELECT NAME, DESCRIPTION, INTERNAL FROM meta_templates WHERE MT_ID = $in";
		$query = new query($db, $sql);
		$query->getrow();
		$name = addslashes($query->field("NAME"));
		$desc = addslashes($query->field("DESCRIPTION"));
		$internal = $query->field("INTERNAL");
		$sql = "DELETE FROM meta_templates WHERE MT_ID = $out";
		$query = new query($db, $sql);
		$sql = "INSERT INTO meta_templates (MT_ID, NAME, DESCRIPTION, INTERNAL, VERSION) VALUES ($out, '$name', '$desc', $internal, $level)";
		$query = new query($db, $sql);

		// launch metaTEmplateItems
		$sql = "DELETE FROM meta_template_items WHERE MT_ID = $out";
		$query = new query($db, $sql);
		$sql = "SELECT MTI_ID FROM meta_template_items WHERE MT_ID = $in";
		$query = new query($db, $sql);

		while ($query->getrow()) {
			$id = $query->field("MTI_ID");
			launchMetaTemplateItem($id, $out, $level);
		}

		$query->free();
		return $out;
	} else
		return translateState($in, $level);
  }

  /** 
  * Launch a Meta-Template-Item
  * @param integer ID to launch
  * @param integer ID of the corresponding Meta-TEmplate.
  * @param integer ID of the level to launch to
  * @returns integer Translated ID after launch
  */
  function launchMetaTemplateItem($in, $mt, $level) {
	global $db;

	if (!checkACL($in)) {
		$out = translateState($in, $level);

		$sql = "SELECT NAME, POSITION, MTYPE_ID FROM meta_template_items WHERE MTI_ID = $in";
		$query = new query($db, $sql);
		$query->getrow();
		$name = addslashes($query->field("NAME"));
		$position = $query->field("POSITION");
		$type = $query->field("MTYPE_ID");
		$sql = "DELETE FROM meta_template_items WHERE MTI_ID = $out";
                $query = new query($db, $sql);
		$sql = "INSERT INTO meta_template_items (MTI_ID, MT_ID, NAME, POSITION, MTYPE_ID, VERSION) VALUES ($out, $mt, '$name', $position, $type, $level)";
		$query = new query($db, $sql);
		$query->free();
		return $out;
        } else
                return translateState($in, $level);
  }
  
  	/**
	 * synchronize meta-data with meta-data-templates and the object itself.
	 * @param int $id ID of the META-DATA to synchronize.
	 * @param string $type Type of the Metas to sync. Allowed are OBJECT|CLUSTER|CLUSTERCONTENT|SITEPAGE
	 */
	function syncMetas($id, $type) {
		global $db;

		$metaTemplates[0] = 1;

		if ($type == "OBJECT") {
			// make list of templates complete.
			$module = getDBCell("content", "MODULE_ID", "CID = $id");

			$metaTemplates[1] = getDBCell("modules", "MT_ID", "MODULE_ID = $module");
			$metaTemplates[2] = getDBCell("content", "MT_ID", "CID = $id");
		} else if ($type == "CLUSTERCONTENT") {
			$sql = "SELECT m.MT_ID FROM modules m, cluster_template_items ct, cluster_content c WHERE c.CLCID = $id AND c.CLTI_ID = ct.CLTI_ID AND ct.FKID = m.MODULE_ID";

			$query = new query($db, $sql);
			$query->getrow();
			$metaTemplates[1] = $query->field("MT_ID");
			$query->free();
		} else if ($type == "CLUSTER") {
			//we are givinig him a CLNID.
			$sql = "SELECT ct.MT_ID FROM cluster_templates ct, cluster_node n WHERE ct.CLT_ID = n.CLT_ID AND n.CLNID = $id";

			$query = new query($db, $sql);
			$query->getrow();
			$metaTemplates[1] = $query->field("MT_ID");
			$query->free();
		}

		// set all meta-data to deleted first!
		$del = "UPDATE meta SET DELETED=1 WHERE CID = $id";

		$query = new query($db, $del);

		for ($i = 0; $i < count($metaTemplates); $i++) {
			//make mti_list
			$mti = createDBCArray("meta_template_items", "MTI_ID", "MT_ID = " . $metaTemplates[$i]);

			for ($j = 0; $j < count($mti); $j++) {
				// create or restore variation
				// check, if variations already exists and is set to deleted.
				$sql = "SELECT COUNT(MID) AS ANZ FROM meta WHERE CID = $id AND MTI_ID =" . $mti[$j];

				$query = new query($db, $sql);
				$query->getrow();
				$amount = $query->field("ANZ");

				if ($amount > 0) {
					$sql = "UPDATE meta SET DELETED=0 WHERE CID = $id AND MTI_ID = " . $mti[$j];
				} else {
					$fk = nextGUID();

					$sql = "INSERT INTO meta (MID, MTI_ID, CID, VALUE, DELETED) VALUES ( $fk, " . $mti[$j] . ", $id,'', 0)";
				}

				$query = new query($db, $sql);
			}
		}
	}
  
 ?>