<?
	/**
	 * Dictioniary with Information about Resources
	 * @package CMS
	 * @subpackage Information
	 */

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
	 * Returns an array with all Variation IDs
	 */
	 function getVariations() {
	   return createDBCArray("variations", "VARIATION_ID");	 	
	 }	 
	 
	 /**
	 * Takes an ID of a plugin-content and tries to retrieve the
	 * corresponding Id in another variation
	 * @param integer ID of the plugin content
	 * @param integer ID of the variation
	 */
	function translateClusterContentItem($fkid, $variation) {
  	  $clid = getDBCell("cluster_content", "CLID", "CLCID = $fkid");
      $cltiId = getDBCell("cluster_content", "CLTI_ID", "CLCID = $fkid");
      $position = getDBCell("cluster_content", "POSITION", "CLCID = $fkid");
	  if ($clid != "") {
	  	$clnid = getDBCell("cluster_variations", "CLNID", "CLID = $clid");
	  	if ($clnid != "") {
	  	  $clid2 = getDBCell("cluster_variations", "CLID", "CLNID = $clnid AND VARIATION_ID = $variation");
	  	  if ($clid2 != "") {
	  	    return getDBCell("cluster_content", "CLCID", "CLID = $clid2 AND CLTI_ID = $cltiId AND POSITION = $position");
	  	  } 
	  	} 	  	 
	  } 
	  return false;	
	}
	
	
	/**
	 * Determines the type of a GUID
	 * @param integer GUID of a resource
	 * @returns string CLUSTER, CLUSTERNODE, METATEMPLATE, CLUSTERTEMPLATE, PAGETEMPLATE
	 */
	 function getTypeByGUID($guid) {
	   $type == "";
	   if (countRows("cluster_variations", "CLNID", "CLNID = $guid") > 0) {
	   	$type = "CLUSTERNODE";
	   } else if (countRows("cluster_variations", "CLID", "CLID = $guid") > 0) {
	   	$type = "CLUSTER";
	   } else if (countRows("cluster_templates", "CLT_ID", "CLT_ID = $guid") >0) {
	   	$type = "CLUSTERTEMPLATE";  	
	   } else if (countRows("meta_templates", "MT_ID", "MT_ID = $guid") > 0) {
	   	$type = "METATEMPLATE";
	   } else if (countRows("sitepage_master", "SPM_ID", "SPM_ID = $guid") > 0) {
	     $type = "PAGETEMPLATE";	
	   }
	 	return $type;
	 }
	 
	/**
	 * Get the name of a resource specified by its guid
	 * @param string GUID of a resource
	 */
	function getResourceName($guid) {
	 	$type = getTypeByGUID($guid);
	 	if ($type=="PAGETEMPLATE") {
	 	  return getSPMLink($guid);	
	 	} else if ($type=="CLUSTERTEMPLATE") {
	 	  return getCLTLink($guid);
	 	} else if ($type=="METATEMPLATE") {
	 		return getMTLink($guid);
	 	} else if ($type== "CLUSTERNODE") {
	     return getCLNLink($guid);
	   }
	}
	 
	/**
	 * Checks, whether the given ID belongs to a dev or a live object.
	 * Always returns a development ID.
	 * @param $oid ID that will be checked, if it is dev or not.
	 * @return ID of the object in DEV-State
	 */
	function ensureDevelopmentID($oid) {
		$check = getDBCell("state_translation", "IN_ID", "OUT_ID = $oid AND LEVEL = 10");

		if ($check != "") {
			return $check;
		} else {
			return $oid;
		}
	}
	
	/**
	 * Links to the sitepage-master with given ID.
	 * @param integer Id of the sitepage master
	 */
	 function getSPMLink($spmId) {
      global $sid, $c;
	   $name = getDBCell("sitepage_master", "NAME", "SPM_ID = ".$spmId);
	   $href = $name.'<a href="'.$c["docroot"].'modules/pagetemplate/sitepage_master.php?sid='.$sid.'&oid='.$spmId.'&go=update">'._drawShortcut().'</a>';	
	   return $href;
	 }
	 
	 /**
	 * Links to the Cluster-Node
	 * @param integer Id of the cluster node
	 */
	 function getCLNLink($guid) {
      global $sid, $c;
	   $name = getDBCell("cluster_node", "NAME", "CLNID=$guid");
	   $href = $name.'<a href="'.$c["docroot"].'modules/cluster/clusterbrowser.php?sid='.$sid.'&oid='.$guid.'&go=update&view=1">'._drawShortcut().'</a>';	
	   return $href;
	 }

	/**
	 * Links to the sitepage-master with given ID.
	 * @param integer Id of the cluster-template
	 */
	 function getCLTLink($cltId) {
       global $sid, $c;
	   $name = getDBCell("cluster_templates", "NAME", "CLT_ID = ".$cltId);
	   $href = $name.'<a href="'.$c["docroot"].'modules/clustertemplate/clustertemplates.php?sid='.$sid.'&oid='.$cltId.'&action=editobject">'._drawShortcut().'</a>';	
	   return $href;
	 }
	 
	 /**
	 * Links to a meta-template
	 * @param integer Id of the meta template
	 */
	 function getMTLink($guid) {
      global $sid, $c;
	   $name = getDBCell("meta_templates", "NAME", "MT_ID = ".$guid);
	   $href = $name.'<a href="'.$c["docroot"].'modules/meta/metatemplates.php?sid='.$sid.'&oid='.$guid.'&action=editobject">'._drawShortcut().'</a>';	
	   return $href;
	 }
	 
	 /**
	  * Returns the name of a page-id
	  * @param integer ID of the page
	  */
	 function resolvePage($spid) {
		global $c;
		$name = getMenu($spid, $c["stdvariation"]);
		return (stristr($name[0], "not defined")) ? $spid : $name[0];
	}

	/**
	 * Returns a link to the page Id with the title
	 * @param integer ID of the page
	 */
	function resolvePageToLink($spid) {
		global $c;
				
		$spm = getDBCell("sitepage", "SPM_ID", "SPID = $spid");
		$name = resolvePage($spid);
		$template = getDBCell("sitepage_master", "TEMPLATE_PATH", "SPM_ID=$spm");
		return "<a href=\"" . $c["livedocroot"] . $template . "?page=$spid&v=$va\" class=\"menu\" target=\"_blank\">$name</a>";
	}
	 
	 /**
	  * draw a small shortcut icon
	  */
	 function _drawShortcut() {
	   $sc = drawImage("icons/li_shortcut.gif", 15,14);	
	   return $sc;
	 }
	 
	 /**
	  * Gets the Cluster-ID (CLID) from the Cluster-Node-Id and a variation ID.
	  * @param 	integer		Cluster-Node-Id
	  * @param	integer		Variation-ID
	  * @returns integer	Cluster-ID (CLID)
	  */
	  function getClusterFromNode($clnid, $variation=0) {
   	 	if ($variation==0) $variation = $this->parent->variation;	
		global $db;
	  	$sql = "SELECT CLID FROM cluster_variations WHERE CLNID = $clnid AND VARIATION_ID = $variation";
	  	$query = new query($db, $sql);
	  	if ($query->getrow()) return $query->field("CLID");
	  }	
?>