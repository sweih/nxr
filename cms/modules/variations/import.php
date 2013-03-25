<?php
	require "../../config.inc.php";

	$filtermenu = new StdMenu('Import Translation File');
	include "menu.php";	
	
	$auth = new auth("SYNC_CLUSTERS");
	$page = new page("Import Translation File");

	$import = value("import", "", "");
	if ($import != "") import($import);
	
	$go = value("go");
	$export = "";
	if ($go == "0")
		$go = "start";

	$form = new Form('Import Translation');
	$form->add(new Textarea("import", "", "", 20, "", 600, "", 2));
	$form->add(new ButtonInCell("sumit", "Import", "", "submit", ""));
	$page->add($form);
	$page->addMenu($filtermenu);
	$page->draw();
	$db->close();
	
	
	function import($text) {
		global $db, $message;
		$ar0 = explode("\n", $text);
		if (is_array($ar0)) {
			// Header einlesen
			$header = explode('|', $ar0[0]);
			$variations = array();
			$variations[0]=0;
			$variations[1]=0;
			$variations[2]=0;
			
			for ($i=3; $i<count($header); $i++) {
			  $var = getDBCell("variations", "VARIATION_ID", "UPPER(SHORTTEXT)=UPPER('$header[$i]')", false);
			  if ($var=="") $var='0';
				$variations[$i] = $var;					
			}									
		}
		for ($i=1; $i<count($ar0); $i++) {
			$line = explode('|', $ar0[$i]);
			if ($line[0]=='C') importContent($line, $variations);
			if ($line[0]=='M') importMenu($line, $variations);
			if ($line[0]=='O') importClusterContent($line, $variations);		
		}
	}
	
	function importContent($line, $variations) {
		$cid = $line[1];
		for ($i=3; $i<count($variations); $i++) {
			if ($line[$i] != "") {
			  $fk = getDBCell("content_variations", "FK_ID", "CID=".$cid." AND VARIATION_ID=".$variations[$i], false );
			  if ($fk != "") {
  			    $table = "";
			    $module = getDBCell("content", "MODULE_ID", "CID=".$cid, false);
			    $modname = getDBCell("modules", "MODULE_NAME", "MODULE_ID=".$module);
			    if ($modname=="Text") $table = "pgn_text";
			    if ($modname=="Label") $table = "pgn_label";
			    if ($table != "") {
			  	  writeContent($fk, $table, $line[$i]);			
			    }
			  }
			}
		}		
	}

	function importClusterContent($line, $variations) {
		$clnid = $line[1];
		$clti  = $line[2];
		$module= getDBCell("cluster_template_items", "FKID", "CLTI_ID=".$clti);
		$modname = getDBCell("modules", "MODULE_NAME", "MODULE_ID=".$module);
	    $table = '';
		if ($modname=="Text") $table = "pgn_text";
		if ($modname=="Label") $table = "pgn_label";
		if ($table != "") {			  	
		  for ($i=3; $i<count($variations);$i++) {			  
		  	  if ($line[3])
		  	  $clid = getDBCell('cluster_variations', 'CLID', 'CLNID='.$clnid.' AND VARIATION_ID='.$variations[$i], false);
			  if ($clid != "") {
		  	    $clcid = getDBCell('cluster_content', 'CLCID', 'CLID='.$clid.' AND CLTI_ID='.$clti);
			    writeContent($clcid, $table, $line[$i]);
			  }			   
		  }
		}
		
	}
	
	function importMenu($line, $variations) {
		global $db;
		$spid = $line[1];
		for ($i=3; $i<count($variations); $i++) {
			$check = getDBCell("sitepage_names", "NAME", "SPID=$spid AND VARIATION_ID=$variations[$i]");
			if ($check=="") {
				if ($line[$i] != "") {				  
					$sql = "UPDATE sitepage_names SET NAME='".$line[$i]."' WHERE SPID=$spid AND VARIATION_ID=$variations[$i]";
				    $update = new query($db, $sql);
				}  
			}
		}
	}
	
	function writeContent($fkid, $table, $content, $force=false) {
	  global $db;
	  $check = getDBCell($table, "CONTENT", "FKID=".$fkid, false);
	  if (($check == '') || $force) {
	  	$sql="UPDATE $table SET CONTENT='".addslashes($content)."' WHERE FKID=$fkid";	  	
	  	$update = new query($db, $sql);	
	  }		
	}
	
	echo $errors;
	
?>