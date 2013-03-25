<?
	/**********************************************************************
	 *	N/X - Web Content Management System
	 *	Copyright 2002-2006 Sven Weih <sven@nxsystems.org>
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
 * Class ClusterEditor
 *
 * Displays the input of a cluster. is initialized with a Cluster-Template ID,
 * a variation ID and the tablename and column, where the CLNID shall be stored.
 * NOTE: This control can only be used, if the cell where the CLNID is to be stored
 * already exists! The column must be set to 0, so set the control can create the cluster
 * and initialize all the data correctly!
 */
class ClusterEditor extends DBO {
	
	
	var $cltid;
	var $variation;
	var $clnid;
	var $clid;
	
	/**
	 * Standard constructor
	 *	 
	 * @param $table string Table, where the CLNID of the cluster shall be stored
	 * @param $column string Column, wehere the CLNID of the cluster shall be stored
	 * @param $cond string WhereStr to indentify the record
	 * @param $cltid integer GUID of the cluster-template, for creating a new cluster only
	 * @param $variation integer GUID of the language
	 * @param $clustername string name of the cluster which shall be created.
	 */
	function ClusterEditor($table, $column, $cond, $cltid, $variation, $clustername) {
	  global $page_action, $oid;
		DBO::DBO('', $table, $column, $cond, "", "NUMBER");
		$this->cltid = $cltid;
		$this->variation = $variation;
		$this->clnid = -1;
		$this->clid  = -1;
		$this->clustername = $clustername;
	  $this->value = getDBCell($table, $column, $cond);
	  if ($oid != "" && $oid != "0") {
		  $this->checkIfCLNExists();		
		}
	}
	
	/**
	 * initialized is automatically called by the form when doing the add-command
	 */
	function initialize() {
	  global $db, $auth, $aclf, $clusterEditState;
			
		// switch wrappers to Editmode
		$clusterEditState = true;
		
		// disable acls
		//if (!isset($aclf)) $aclf = $auth;	
	  // GET CONTENT OF THE CLUSTER		  
	  $plugins = null;
	  $types = null;   
	  // get the structure of the content.
	  $sql = "SELECT CLTI_ID, CLTITYPE_ID FROM cluster_template_items WHERE CLT_ID = $this->cltid AND FKID<>0 ORDER BY POSITION ASC";	
	  $query = new query($db, $sql);
  	while ($query->getrow()) {	  	
	  	$plugins[] = $query->field("CLTI_ID");
		  $types[] = $query->field("CLTITYPE_ID");  		
	  }
	  $query->free();
       
	  // draw plugin preview.
	  $len = count($plugins);
	  for ($i = 0; $i < $len; $i++) {
  		if ($types[$i] == 2)
	  		$this->parentForm->add(new ContentEnvelope($plugins[$i], $this->clid, true, true));

		  if ($types[$i] == 4)
			  $this->parentForm->add(new ClusterEnvelope($plugins[$i], $this->clid, true, true));

		  if ($types[$i] == 5)
			  $this->parentForm->add(new LibraryEnvelope($plugins[$i], $this->clid, true, true));

		  if ($types[$i] == 6)
			  $this->parentForm->add(new CompoundClusterEnvelope($plugins[$i], $this->clid, true, true));

      if ($types[$i] == 8)
         $this->parentForm->add(new ChannelEnvelope($plugins[$i], $this->clid, true, true));
	  }	  
	}
	
	/**
	 * Draw nothing. Operation already done in initialize();
	 */
	function draw() {	}
	
	
	/**
	 * Checks, whether the cluster already exists and creates it, if not.
	 */	 
	function checkIfCLNExists() {
	  global $db;
	  
	  // Check, if Clusternode exists.
	  if ($this->value == "0" || $this->value == "") {
	    // The Cluster in the variation will be created now.
	    $this->clnid = createClusterNode($this->clustername, $this->cltid);	  	
	    	// update the new clnid immediately to the database	  	
	  	$sql = "UPDATE $this->table SET $this->column=$this->clnid WHERE $this->row_identifier";	  	
	  	$query = new query($db, $sql);
	  	$query->free();
	  
	  }	else {
	  	// the cluster node already exists.
	  	$this->clnid = $this->value;
	  }
	  
	  // check, if clustervariation exists.
	  $clid = getDBCell("cluster_variations", "CLID", "CLNID=$this->clnid AND VARIATION_ID=$this->variation");
	  if ($clid == "") {
	  	// Cluster-Variation does not exists yet.
	  	$this->clid = createCluster($this->clnid, $this->variation);
	  } else {
	  	$this->clid = $clid;
	  }
	  
	  // sync the cluster variation
	  syncCluster($this->clid);	  
	  
	  // ensure correct CLT-ID
	  $this->cltid = getDBCell("cluster_node", "CLT_ID", "CLNID=".$this->clnid);	  
	}
}
	 
?>