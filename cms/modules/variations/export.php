<?php
	require "../../config.inc.php";

	$filtermenu = new StdMenu('Export Translation File');
	include "menu.php";	
	
	$auth = new auth("SYNC_CLUSTERS");
	$page = new page("Export Translation File");

	$go = value("go");
	$export = "";
	if ($go == "0")
		$go = "start";

	$form = new CommitForm('Export Translation');
	$form->addToTopText('Export Translation File as CSV');
	$maintenanceHandler = new ActionHandler("export");
	$maintenanceHandler->addFncAction("exportLanguages");
	$form->addCheck("export", "Export ", $maintenanceHandler);

	$page->add($form);
	$page->addMenu($filtermenu);
	$page->draw();
	$db->close();
	
	if ($export != "") {
		echo '<textarea style="width:600px;height:300px;font-size:9px;">'.$export.'</textarea>';
	}
	
	function exportLanguages() {
		global $export, $recordsets;
		$variations=createDBCArray("variations", "VARIATION_ID",'1', 'ORDER BY VARIATION_ID ASC');
		$variations2=createDBCArray("variations", "SHORTTEXT",'1', 'ORDER BY VARIATION_ID ASC');
		$export = 'TYPE|KEY1|KEY2';
		for ($i=0; $i<count($variations2); $i++) {
			$export.='|'.$variations2[$i];
		}
		$export.="\n";
		
		// getPluginIds
		$lp = getDBCell("modules", "MODULE_ID", "MODULE_NAME='Label'", false);
		$tp = getDBCell("modules", "MODULE_ID", "MODULE_NAME='Text'", false);
		
		
		// export contents
	    $cids = createDBCArray("content", "CID", "VERSION=0");
	    for ($i=0; $i<count($cids); $i++) {
	  	  $module = getDBCell("content", "MODULE_ID", "CID=".$cids[$i]);
	  	  $moduleTable = '';
	  	  if ($module == $tp) $moduleTable = 'pgn_text';
	  	  if ($module == $lp) $moduleTable = 'pgn_label'; 
	      if ($moduleTable != '') {
	      	$export.='C|'.$cids[$i].'|';
	      	for ($j=0; $j<count($variations); $j++) {		      	  	      		
	      		$fk = getDBCell("content_variations", "FK_ID", "CID=$cids[$i] AND VARIATION_ID=$variations[$j]", false);	      	  	      		
	      		$content = getDBCell($moduleTable, "CONTENT", "FKID=".$fk, false);
	      		$export.='|'.preg_replace("/\r|\n/s", "", $content);
	      	}
	      	$export.="\n";
	      }	      
	     }

	     // export menues
	     $spids = createDBCArray("sitepage", "SPID", "VERSION=0 AND DELETED=0");
	     for ($i=0; $i<count($spids); $i++) {
	       $export.='M|'.$spids[$i].'|';
	       for ($j=0; $j<count($variations); $j++) {
		     $menu = getDBCell("sitepage_names", "NAME", "SPID=$spids[$i] AND VARIATION_ID=$variations[$j]", false);
		   	 $export.='|'.$menu;    	      
	      }
	      $export.="\n";		      
	    }	
	  	  	  	  	     	     
	     
	     // export cluster-content	  	
	  $clnids = createDBCArray('cluster_node', 'CLNID', 'VERSION=0 AND DELETED=0');
	  for ($i=0; $i<count($clnids); $i++) {
	  	$clt = getDBCell("cluster_node", 'CLT_ID', 'CLNID='.$clnids[$i],false);	  	
	  	$cltis = createDBCArray('cluster_template_items', 'CLTI_ID', 'CLT_ID='.$clt.' AND FKID IN ('.$tp.','.$lp.') AND CLTITYPE_ID=2');  	
	  	for ($k=0; $k<count($cltis); $k++) {
	  	  $module = getDBCell('cluster_template_items', 'FKID', 'CLTI_ID='.$cltis[$k]);	  	  
	  	  $moduleTable = '';
	  	  if ($module == $tp) $moduleTable = 'pgn_text';
	  	  if ($module == $lp) $moduleTable = 'pgn_label'; 
	      if ($moduleTable != '') {	  	  
	  	    $export.='O|'.$clnids[$i].'|'.$cltis[$k];
	  	  	for ($j=0; $j<count($variations); $j++) {
	  	      $clid = getDBCell("cluster_variations", 'CLID', 'CLNID='.$clnids[$i].' AND VARIATION_ID='.$variations[$j],false);	  	   
	  	      $clcid = getDBCell("cluster_content", "CLCID", "CLID=".$clid.' AND CLTI_ID='.$cltis[$k], false);
	  	      $content = getDBCell($moduleTable, "CONTENT", "FKID=".$clcid, false);
	      	  $export.='|'.preg_replace("/\r|\n/s", "", $content);	  	     
	  	    }
	  	    $export.="\n";  
	  	  }
	  	}	  	
	  }
	     
	     
   }
	
?>