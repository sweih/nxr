<?
	/**********************************************************************
	 *	N/X - Web Content Management System
	 *	Copyright 2002 Fabian Koenig
	 *
	 *	This file is part of N/X.
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
	require "../../config.inc.php";

	$filtermenu = new StdMenu('Synchronize Languages');
	include "menu.php";	
	
	$auth = new auth("SYNC_CLUSTERS");
	$page = new page("Synchronize Clusters");

	$go = value("go");

	if ($go == "0")
		$go = "start";

	$form = new CommitForm($lang->get("sync_clusters"));
	$form->addToTopText($lang->get("sync_lang_desc", "Creates an instance of each content in all languages defined."));
	$maintenanceHandler = new ActionHandler("sync");
	$maintenanceHandler->addFncAction("syncLanguages");
	$form->addCheck("sync", $lang->get("sync_lang", 'Synchronize Languages')." ", $maintenanceHandler);

	$page->add($form);
	$page->addMenu($filtermenu);
	$page->draw();
	$db->close();
	
	function syncLanguages() {
	  global $db, $auth;
	  $variations = createDBCArray('variations', 'VARIATION_ID');
	  // enable languages in all templates
	  $spms = createDBCArray('sitepage_master', 'SPM_ID', 'VERSION=0');
	  for ($i=0; $i<count($spms); $i++) {
		for ($j=0; $j<count($variations); $j++) {
		  $check = getDBCell("sitepage_variations", "VARIATION_ID", "SPM_ID=".$spms[$i]." AND VARIATION_ID=".$variations[$j]);		  
		  if ($check=="") {		    
		  	$update = new query($db, 'INSERT INTO sitepage_variations (SPM_ID, VARIATION_ID) VALUES ('.$spms[$i].','.$variations[$j].')');
		  }	
		}
	  }
	  
	  // enable languages for all contents
	  $cids = createDBCArray("content", "CID", "VERSION=0");
	  for ($i=0; $i<count($cids); $i++) {
	  	$module = getDBCell("content", "MODULE_ID", "CID=".$cids[$i]);
	    for ($j=0; $j<count($variations); $j++) {
	      $check = getDBCell("content_variations", "VARIATION_ID", "CID=".$cids[$i]." AND VARIATION_ID=".$variations[$j]);
	      if ($check=="") {
		      $fk = nextGUID();
	  		  $sql = "INSERT INTO content_variations (CID, VARIATION_ID, FK_ID, DELETED) VALUES ( $cids[$i], $variations[$j], $fk, 0)";
              $PGNRef = createPGNRef($module, $fk);
              $PGNRef->sync();
              $update = new query($db, $sql);
	      }
	    }	  
	  }

	  // enable languages for all clusters
	  $clnids = createDBCArray('cluster_node', 'CLNID', 'VERSION=0 AND DELETED=0');
	  for ($i=0; $i<count($clnids); $i++) {
	  	for ($j=0; $j<count($variations); $j++) {
	  	  $check = getDBCell("cluster_variations", 'VARIATION_ID', 'CLNID='.$clnids[$i].' AND VARIATION_ID='.$variations[$j]);
	  	  if ($check=="") {
			$fk = nextGUID();
			$sql = "INSERT INTO cluster_variations (CLNID, VARIATION_ID, CLID, DELETED,CREATED_AT, CREATE_USER ) VALUES ( $clnids[$i], $variations[$j], $fk, 0, NOW()+0, '".$auth->userName."')";
	  	  	$update = new query($db, $sql);
	  	  	syncCluster($fk);
	  	  }
	  	}	  	
	  }
	  
	  // enable languages for all menutexts
	  $spids = createDBCArray("sitepage", "SPID", "VERSION=0 AND DELETED=0");
	  for ($i=0; $i<count($spids); $i++) {
	    for ($j=0; $j<count($variations); $j++) {
	      $check = getDBCell("sitepage_names", "VARIATION_ID", "VARIATION_ID= $variations[$j] AND SPID=$spids[$i]");
	      if ($check =="") {
	      	$update = new query($db, 'INSERT INTO sitepage_names (SPID,VARIATION_ID,NAME,HELP,DELETED,VERSION) VALUES ('.$spids[$i].','.$variations[$j].',"","",0,0)');
	      }		      
	    }	
	  }	  	  	  
	}	
?>