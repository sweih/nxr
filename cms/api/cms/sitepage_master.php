<?php

/**********************************************************************
 *	N/X - Web Content Management System
 *	Copyright 2004 Sven Weih
 *
 *      $Id: sitepage_master.php,v 1.4 2004/01/05 23:18:34 sven_weih Exp $ *
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
   * Create a new Page Template
   * @param string Name
   * @param string Description
   * @param string Filename of the template
   * @param string Template
   * @param integer GUID of Cluster Template
   * @param integer Id of Type (1=singlepage, 2=multipage)
   * @param integer OPtional key to use.
   */
  function createSitepageMaster($name, $description, $templatePath, $template, $clt, $type, $id=null) {
  	 global $db, $c, $errors;
  	 if ($id==null) $id=nextGUID();
  	 $name = makeCopyName("sitepage_master", "NAME", parseSQL($name), "VERSION=0 AND DELETED=0"); 	
  	 $description = parseSQL($description);
  	 $filename = substr($templatePath, 0, strcspn($templatePath, "."));
  	 $filesuffix = $templatePath = substr($templatePath, strcspn($templatePath, ".")+1);
  	 $templatePath = makeUniqueFilename($c["devpath"], parseSQL($filename), $filesuffix);
  	 
  	 $fp = @fopen($c["devpath"].$templatePath, "w+");
	  if ($fp !="") {
   	  	  @fwrite($fp, $template);
		  	  @fclose($fp);
  	 } else {
  	   $errors.="--Could not write spm: ".$templatePath;	
  	 }
  	 $sql = "INSERT INTO sitepage_master (SPM_ID, NAME, DESCRIPTION, TEMPLATE_PATH, CLT_ID, SPMTYPE_ID) VALUES ";
  	 $sql.=                              "($id, '$name', '$description', '$templatePath', $clt, $type)";
  	 $query = new query($db, $sql);
  	 $query->free();
	 $variations = createDBCArray("variations", "VARIATION_ID", "1");
	 for ($i=0; $i<count($variations); $i++) {
	   $sql = "INSERT INTO sitepage_variations (SPM_ID, VARIATION_ID) VALUES ( $id, ".$variations[$i].")";	
	   $query = new query($db, $sql);
	   $query->free();
	 }
  	 return $id;
  }
  
  	/**
	 * Launch a Sitepage-MAster
	 * @param integer SPM_ID to launch
	 * @param integer ID of the level to launch to.
	 * @param integer ID of the variation to launch. 
	 * @returns integer Translated ID after launch
	 */
	function launchSitepageMaster($in, $level, $variation) {
		global $db;

		$out = translateState($in, $level, false);
		$sql = "SELECT * FROM sitepage_master WHERE SPM_ID = $in";
		$query = new query($db, $sql);
		$query->getrow();

		$clt = $query->field("CLT_ID");
		$type = $query->field("SPMTYPE_ID");
		$name = addslashes($query->field("NAME"));
		$desc = addslashes($query->field("DESCRIPTION"));
		$path = addslashes($query->field("TEMPLATE_PATH"));
		$cltTrans = launchClusterTemplate($clt, $level, $variation);

		$sql = "DELETE FROM sitepage_master WHERE SPM_ID = $out";
		$query = new query($db, $sql);
		$sql = "INSERT INTO sitepage_master (SPM_ID, NAME, DESCRIPTION, TEMPLATE_PATH, CLT_ID, SPMTYPE_ID, DELETED, VERSION) VALUES ";
		$sql .= "($out, '$name', '$desc', '$path', $cltTrans, $type, 0, $level)";
		$query = new query($db, $sql);
		$query->free();

		// copy template physically.
		global $c;

		if (file_exists($c["devpath"].$path)) {
			nxDelete($c["livepath"],$path);
			nxCopy($c["devpath"] . $path, $c["livepath"], $path);
		}

		launchSPMVariations($in, $level);
		return $out;
	}
	
	/**
	 * Deletes a sitepage master
	 */
	function deleteSitepageMaster($spm) {
		$deleteArray = array();
		$deleteArray[] = $spm;
		$deleteArray[] = translateState($spm, 10, false);
		for ($i=0; $i<count($deleteArray); $i++) {
		  deleteRow("sitepage_variations", "SPM_ID = ".$deleteArray[$i]);
		  deleteRow("sitepage_master", "SPM_ID = ".$deleteArray[$i]);	
		  deleteRow("sitemap", "SPM_ID = ".$deleteArray[$i]);
		  $sitepages = createDBCArray("sitepage", "SPID", "SPM_ID = ".$deleteArray[$i]);
		  for ($j=0; $j<count($sitepages); $j++) {
		    $da2 = array();
		    $da2[] = $sitepages[$j];
		    $da2[] = translateState($sitepages[$j], 10, false);
		    for ($k=0; $k < count($da2); $k++) {
		      deleteRow("sitepage", "SPID = ".$da2[$k]);
		      deleteRow("sitepage_names", "SPID = ".$da2[$k]);	
		    }
		  }
		}		
	}

	/**
* Launch available Sitepage-Variations. 
* @param integer SPM_ID to launch 
* @param integer Level, you want to launch to. 
*/
	function launchSPMVariations($spm, $level) {
		global $db;

		$out = translateState($spm, $level, false);
		$vars = createDBCArray("sitepage_variations", "VARIATION_ID", "SPM_ID = $spm");
		$sql = "DELETE FROM sitepage_variations WHERE SPM_ID = $out";
		$query = new query($db, $sql);

		for ($i = 0; $i < count($vars); $i++) {
			$sql = "INSERT INTO sitepage_variations (SPM_ID, VARIATION_ID) VALUES ( $out, " . $vars[$i] . ")";

			$query = new query($db, $sql);
		}

		$query->free();
	}
	
		/**
	 * syncronize variations with entered data to the database.
	 * The configuration for this function must be set manually.
	 * I.E. there must be the $oid-Variable set and there must(!)
	 * be also the global vars sitepage_variations_VARIATION_ID_XX
	 * set which are automatically set by the SelectMultiple2Input.
	  */
	function syncSPMVariations() {
		global $db, $oid;

		//delete all variations first.
		$del = "DELETE FROM sitepage_variations WHERE SPM_ID = $oid";

		$query = new query($db, $del);

		// get list of variations
		$variations = createNameValueArray("variations", "NAME", "VARIATION_ID", "DELETED=0");

		for ($i = 0; $i < count($variations); $i++) {
			$id = $variations[$i][1];

			if (value("sitepage_variations_VARIATION_ID_" . $id) != "0") {
				$sql = "INSERT INTO sitepage_variations (SPM_ID, VARIATION_ID) VALUES ( $oid, $id)";

				$query = new query($db, $sql);
			}
		}
	}
  
?>