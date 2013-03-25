<?
	/**
	 * @package CMS
	 */

	// Anti-Cycle-List Array
	// now in cms/api/common/prepare.php

	/**
	 * Creates a short url on the server for a certain page. E.G. www.foo.org/shorturl for direct access
	 *
	 * @param integer PageId to link to
	 * @param integer VariationId to link to
	 * @param string ShortURL, which shall be created.
	 */
	function createShortURL($page, $v, $short) {
		global $c;
		if (substr($short, 0, 1) == "/")
			$short = substr($short, 1);

		$allDir = $c["livepath"];
		// ensure that path exists
		$directories = explode("/", $short);

		if (count($directories) > 0) {
			for ($i = 0; $i < count($directories); $i++) {
				$thisDir = $directories[$i];

				if ($thisDir != "") {
					if (!is_dir($allDir . $thisDir)) {
						mkdir($allDir . $thisDir, 0755);
					}

					$allDir = $allDir . $thisDir . "/";
				}
			}

			// delete old index file 
			nxDelete ($allDir , "index.php");

			// create new index-file...
			$index = "<?php \n";
			$link = getMenuLink2($page, $v);
			$index .= 'header ("Location: ' . $link . '"); ';
			$index .= "exit; ";
			$index .= "?>";
			// write to disk 
			$index_file = fopen($allDir . "index.php", "w");
			fwrite($index_file, $index);
			fclose ($index_file);
		}
	}
	
	/**
	 * Create the page with URL-Path
	 * 
	 * @param integer SitepageId of the page
	 * @param integer VariationId of the page
	 */
	function launchURLPage($spid, $variation) {
	    global $c;
		$menuId = getDBCell("sitepage", "MENU_ID", "SPID=".$spid);
	    $short = getPageURL($menuId, $variation);
	    
	    if (substr($short, 0, 1) == "/")
			$short = substr($short, 1);

		$allDir = $c["livepath"];
		// ensure that path exists
		$directories = explode("/", $short);

		if (count($directories) > 0) {
			for ($i = 0; $i < count($directories); $i++) {
				$thisDir = $directories[$i];

				if ($thisDir != "") {
					if (!is_dir($allDir . $thisDir)) {
						mkdir($allDir . $thisDir, 0755);
					}

					$allDir = $allDir . $thisDir . "/";
				}
			}

			// delete old index file 
			@nxDelete ($allDir , "index.php");
			@nxDelete ($allDir , "index.html");
			
			$template = getTemplate($spid);			
			// create new index-file...
			$index = "<?php \n";
			$index.= ' $v='.$variation.';'."\n";
			$index.= ' $page='.$spid.';'."\n";
			$index.= ' $c["path"] = \''.$c["path"].'\';'."\n";
			if ($c["renderstatichtml"] && isCached($spid, $variation)) {
				$index.=' $renderOnAccess=true;'."\n";				
			}
			$index.= ' require_once \''.$c["livepath"].$template.'\';'."\n";			
			$index.= "?>";
			
			// write to disk 
			$index_file = fopen($allDir . "index.php", "w");
			fwrite($index_file, $index);
			fclose ($index_file);
		}			
	}
	
	/**
	 * Clear the page with URL-Path
	 * 
	 * @param integer SitepageId of the page
	 * @param integer VariationId of the page
	 */
	function clearURLPage($spid, $variation) {
		global $c;

		$menuId = getDBCell("sitepage", "MENU_ID", "SPID=".$spid);
	    $short = getPageURL($menuId, $variation);
		if (substr($short, 0, 1) == "/")
			$short = substr($short, 1);

		$allDir = $c["livepath"];
		// ensure that path exists
		$directories = explode("/", $short);

		if (count($directories) > 0) {
			for ($i = 0; $i < count($directories); $i++) {
				$thisDir = $directories[$i];

				if ($thisDir != "") {
					$allDir = $allDir . $thisDir . "/";
				}
			}

			// delete old index file 
			if (file_exists($allDir . "index.php")) {
				nxDelete ($allDir, "index.php");
			}

			// create new index-file...
			global $c;
			$index = '<html>';
			$index .= '<head>';
			$index .= '<title>Page does not exist</title>';
			$index .= '<meta name="generator" content="N/X WCMS">';
			$index .= '</head>';
			$index .= '<body text="#000000" bgcolor="#FFFFFF" link="#FF0000" alink="#FF0000" vlink="#FF0000">';
			$index .= '<center>';
			$index .= '<font face="VERDANA" size="2">';
			$index .= 'The URL you entered is not available at present.<br>';
			$index .= 'Please try again later or go to <a href="' . $c["livedocroot"] . '">Startpage</a>.';
			$index .= '</font>';
			$index .= '</center>';
			$index .= '</body>';
			$index .= '</html>';

			// write to disk 
			$index_file = @fopen($allDir . "index.php", "w");
			@fwrite($index_file, $index);
			@fclose ($index_file);
		}	
	}

	/**
	 * Places an empty page on the path and displays a text that the page ist no longer live.
	 *
	 * @param string Path to on server
	 */
	function clearShortURL($short) {
		global $c;

		if (substr($short, 0, 1) == "/")
			$short = substr($short, 1);

		$allDir = $c["livepath"];
		// ensure that path exists
		$directories = explode("/", $short);

		if (count($directories) > 0) {
			for ($i = 0; $i < count($directories); $i++) {
				$thisDir = $directories[$i];

				if ($thisDir != "") {
					$allDir = $allDir . $thisDir . "/";
				}
			}

			// delete old index file 
			if (file_exists($allDir . "index.php")) {
				nxDelete ($allDir, "index.php");
			}

			// create new index-file...
			global $c;
			$index = 'html>';
			$index .= '<head>';
			$index .= '<title>Page does not exist</title>';
			$index .= '<meta name="generator" content="N/X WCMS">';
			$index .= '</head>';
			$index .= '<body text="#000000" bgcolor="#FFFFFF" link="#FF0000" alink="#FF0000" vlink="#FF0000">';
			$index .= '<center>';
			$index .= '<font face="VERDANA" size="2">';
			$index .= 'The URL you entered is not available at present.<br>';
			$index .= 'Please try again later or go to <a href="' . $c["livedocroot"] . '">Startpage</a>.';
			$index .= '</font>';
			$index .= '</center>';
			$index .= '</body>';
			$index .= '</html>';

			// write to disk 
			$index_file = fopen($allDir . "index.php", "w");
			fwrite($index_file, $index);
			fclose ($index_file);
		}
	}

	/**
	 * Check, if a sitepage is live or not
	 * @param integer ID of the sitepage to check
	 * @returns boolean ture, if is live, false else
	 */
	function isSPLive($spid) {
		global $db, $panic;

		//$panic = true;
		$sql = "SELECT OUT_ID FROM state_translation WHERE IN_ID = $spid AND EXPIRED = 0";
		$query = new query($db, $sql);

		if ($query->getrow()) {
			$query->free();

			// check if cluster instances are live or not.
			$sql = "SELECT cv.CLID FROM cluster_variations cv, sitepage sp WHERE sp.SPID = $spid AND sp.CLNID = cv.CLNID";
			$query = new query($db, $sql);

			while ($query->getrow()) {
				$clid = $query->field("CLID");

				$check2 = getDBCell("state_translation", "OUT_ID", "IN_ID = $clid");

				if ($check2 == "")
					return false;

				$check = getDBCell("state_translation", "EXPIRED", "IN_ID = $clid");

				if ($check == "0")
					return true;

				$del = getDBCell("cluster_variations", "DELETED", "CLID = $clid");

				if ($del == "1" || $del == "")
					return false;

				return true;
			}

			return true;
		} else {
			$query->free();

			return false;
		}
	}

	/**
	 * Check, if a sitepage-variation does exist or not.
	 * @param integer ID of the sitepage to check 
	 * @param integer ID of the variation to check 
	 * @returns boolean true, if sitepage does exist, false if not. 
	 */
	function SPVarExists($spid, $variation) {
		$spm = getDBCell("sitepage", "SPM_ID", "SPID = $spid");

		$vari = getDBCell("sitepage_variations", "VARIATION_ID", "SPM_ID = $spm AND VARIATION_ID = $variation");

		if ($vari != $variation)
			return false;

		// checking existence of cluster now.
		$clnid = getDBCell("sitepage", "CLNID", "SPID = $spid");
		$cv = getDBCell("cluster_variations", "VARIATION_ID", "CLNID = $clnid AND DELETED=0 AND VARIATION_ID = $variation");

		if ($cv != $variation)
			return false;

		return true;
	}

	/**
	 * Check, if a sitepage-variation is live or not
	 * @param integer ID of the sitepage to check
	 * @param integer ID of the variation to check
	 * @returns boolean true, if is live, false else
	 */
	function isSPVarLive($spid, $variation) {
		global $db;

		$sql = "SELECT OUT_ID FROM state_translation WHERE IN_ID = $spid AND EXPIRED = 0";
		$query = new query($db, $sql);

		if ($query->getrow()) {
			$spidTrans = $query->field("OUT_ID");

			$sql = "SELECT SPID FROM sitepage_names WHERE SPID = $spidTrans AND VARIATION_ID = $variation AND DELETED=0";
			$query = new query($db, $sql);

			if ($query->getrow()) {
				$query->free();

			$sql = "SELECT cv.CLID FROM cluster_variations cv, sitepage s, state_translation st WHERE s.SPID = $out AND s.CLNID = cv.CLNID AND cv.VARIATION_ID=$variation AND cv.DELETED=0 AND cv.CLID = st.OUT_ID AND st.EXPIRED=0"; 
			$query = new query($db, $sql);
			if ($query->getrow()) {
			  return true;
			} else {
			  return false;
			}
			$query->free();
			} else {
				$query->free();

				return false;
			}
		} else {
			$query->free();

			return false;
		}
	}
	

	/**
	 * Check, if a sitepage-variation is live or not. Does no existency check of cluster.
	 * @param integer ID of the sitepage to check
	 * @param integer ID of the variation to check
	 * @returns boolean true, if is live, false else
	 */
	function isSPVarLiveEx($spid, $variation) {
		global $db, $variation;
		$out = getDBCell("state_translation", "OUT_ID", "IN_ID = $spid AND EXPIRED=0");
		if ($out != "") {
			// check if cluster is live.
			$sql = "SELECT cv.CLID FROM cluster_variations cv, sitepage s, state_translation st WHERE s.SPID = $out AND s.CLNID = cv.CLNID AND cv.VARIATION_ID=$variation AND cv.DELETED=0 AND cv.CLID = st.OUT_ID AND st.EXPIRED=0"; 
			$query = new query($db, $sql);
			if ($query->getrow()) {
			  return true;
			} else {
			  return false;
			}
			$query->free();
		} else {
			return false;
		}
	}
	
	
	/**
	 * Checks whether a cluster-instance is live or not
	 * @param integer CLID
	 */
	function isClusterLive($clid) {
		global $db;
		$clidTrans = translateState($clid, 10, false);
		$sql = "SELECT cv.CLID FROM cluster_variations cv, state_translation st WHERE cv.CLID = $clidTrans AND cv.CLID = st.OUT_ID AND st.EXPIRED = 0";
		$query = new query($db, $sql);
		if ($query->getrow()) {
			return true;
		} else {
			return false;
		}
	}


    /**
	 * Expire a sitepage-variation
	 * @param integer ID of the sitepage to expire
	 * @param integer ID of the level that will be expired
	 * @param integer ID of the variation to expire
	 */
	function expireSitepage($spid, $level, $variation) {
		global $db, $c;

		$spidTrans = translateState($spid, $level, $false);
		// update sitepage_names.
		$sql = "UPDATE sitepage_names SET DELETED=1 WHERE SPID = $spidTrans AND VARIATION_ID = $variation";
		$query = new query($db, $sql);
		// get CLID
		$sql = " SELECT cv.CLID FROM cluster_variations cv, sitepage sp WHERE sp.CLNID = cv.CLNID AND sp.SPID = $spidTrans  AND cv.VARIATION_ID = $variation";
		$query = new query($db, $sql);
		$query->getrow();
		$clid = $query->field("CLID");
		$sql = "UPDATE state_translation SET EXPIRED=1 WHERE OUT_ID = $clid";
		$query = new query($db, $sql);
		// count rest of variations of spid.
		$sql = "SELECT COUNT(VARIATION_ID) AS ANZ FROM sitepage_names WHERE SPID = $spidTrans AND DELETED = 0";
		$query = new query($db, $sql);
		$query->getrow();
		$amount = $query->field("ANZ");

		// if last variation was expired, expire also menu and sitepage.
		if ($amount == 0) {
			$sql = "UPDATE state_translation SET EXPIRED=1 WHERE IN_ID = $spid";

			$query = new query($db, $sql);
			$sql = "SELECT MENU_ID FROM sitepage WHERE SPID = $spid";
			$query = new query($db, $sql);
			$query->getrow();
			$menu = $query->field("MENU_ID");
			$sql = "UPDATE state_translation SET EXPIRED = 1 WHERE IN_ID = $menu";
			$query = new query($db, $sql);
			$query->free();
		}

		flushSitePage($spidTrans, $variation);

		// clear direct path
		$sql = "SELECT DIRECT_URL FROM sitepage_names WHERE SPID = $spid AND VARIATION_ID = $variation";
		$query = new query($db, $sql);
		$query->getrow();
		$short = $query->field("DIRECT_URL");
		$query->free();

		if ($short != "")
			clearShortURL ($short);
		
		if (!$c["classicurls"])
		  clearURLPage($spidTrans, $variation);
			
		global $JPCACHE_ON;
		//cc on launch
		
		$menu = getDBCell("sitepage", "MENU_ID", "SPID = $spid");
		$cconlaunch = getDBCell("sitemap", "CC_ON_LAUNCH", "MENU_ID = " . $menu);
		$ccarray = explode(",", $cconlaunch);
		$mparray = createDBCArray("sitepage", "SPID", "MENU_ID = " . $menu);

		for ($i = 0; $i < count($ccarray); $i++) {
			$spidTrans = translateState($ccarray[$i], $level, false);

			if ($spidTrans != ""  && isCached($ccarray[$i], $variation)) {	
				renderSitePage($spidTrans, $variation);
				if ($JPCACHE_ON) {
					@unlink($c["dyncachepath"]."dyncache-".jpcacheFilename($spidTrans, $variation));
				}
			}
		}

		for ($i = 0; $i < count($mparray); $i++) {
			$spidTrans = translateState($mparray[$i], $level, false);

			if ($spidTrans != ""  && isCached($mparray[$i], $variation)) {	
				// old html caching
				// renderSitePage($spidTrans, $variation);
				if ($JPCACHE_ON  && !$c["renderstatichtml"]) {					
					@unlink($c["dyncachepath"]."dyncache-".jpcacheFilename($spidTrans, $variation));					
				} else {
				  @unlink($c["cachepath"]."static/dyncache-".jpcacheFilename($spidTrans, $variation));									  
				}

			}
		}

	}
	
	


	/**
	 * Relaunches all menues being on the same level like the selected page
	 * 
	 * @param integeger Dev-ID of the menu to which the sitepage is associated with
	 */
	function RebuildMenuStructure($menuId) {
		global $db;
		$parentMenu = getDBCell("sitemap", "PARENT_ID", "MENU_ID = $menuId");
		$sitemapMenu = createDBCArray("sitemap", "MENU_ID", "PARENT_ID = $parentMenu");

		for ($i = 0; $i < count($sitemapMenu); $i++) {
			$liveId = translateState($sitemapMenu[$i], 10, false);

			$devPosition = getDBCell("sitemap", "POSITION", "MENU_ID = " . $sitemapMenu[$i]);
			$sql = "UPDATE sitemap SET POSITION = $devPosition WHERE MENU_ID = $liveId";
			$query = new query($db, $sql);
			$query->free();
		}

		$multipages = createDBCArray("sitepage", "SPID", "MENU_ID = $menuId");

		for ($i = 0; $i < count($multipages); $i++) {
			$liveId = translateState($multipages[$i], 10, false);

			$devPosition = getDBCell("sitepage", "POSITION", "SPID = " . $multipages[$i]);
			$sql = "UPDATE sitepage SET POSITION = $devPosition WHERE SPID = $liveId";
			$query = new query($db, $sql);
			$query->free();
		}
	}

	/** 
	 * Launch a sitepage-variation.
	 * @param integer ID of the sitepage to launch.
	 * @param integer ID of the level to launch to.
	 * @param integer ID of the Variation to launch.
	 */
	function launchSitepage($in, $level, $variation) {
		global $db, $c;
		$out = translateState($in, $level);

		//removeDynamicCache();		
		delExpired ($out);
		$sql = "SELECT * FROM sitepage WHERE SPID = $in";
		$query = new query($db, $sql);
		$query->getrow();
		$spm = $query->field("SPM_ID");
		$menu = $query->field("MENU_ID");
		$cln = $query->field("CLNID");
		$posi = $query->field("POSITION");
		$ldate = "'" . $query->field("LAUNCH_DATE"). "'";
		$edate = "'" . $query->field("EXPIRE_DATE"). "'";
		$pwp   = $query->field("PASSWORD_PROTECTED");
		if ($ldate == "''")
			$ldate = "NULL";

		if ($edate == "''")
			$edate = "NULL";

		$spmTrans = launchSitepageMaster($spm, $level, $variation);
		$clnTrans = launchCluster($cln, $level, $variation);
		$menuTrans = launchSitemap($menu, $level, $variation);
		launchSitepageOwner($in, $level);
		launchSitepageName($in, $level, $variation);
		$sql = "DELETE FROM sitepage WHERE SPID = $out";
		$query = new query($db, $sql);
		$sql = "INSERT INTO sitepage (SPID, SPM_ID, MENU_ID, POSITION, CLNID, LAUNCH_DATE, EXPIRE_DATE, DELETED, VERSION, PASSWORD_PROTECTED) VALUES ";
		$sql .= "($out, $spmTrans, $menuTrans, $posi, $clnTrans, $ldate, $edate, 0, $level, $pwp)";
		$query = new query($db, $sql);
		$query->free();

		// rebuild menu-structure...
		rebuildMenuStructure ($menu);

		// cache rebuild
	
		global $JPCACHE_ON;
		if ($JPCACHE_ON) {
		  @unlink($c["dyncachepath"]."dyncache-".jpcacheFilename($out, $variation));	
		}

	
		$cached = getDBCell("sitemap", "IS_CACHED", "MENU_ID = " . $menuTrans);
		if ($cached == 1) {
			if ($JPCACHE_ON  && !$c["renderstatichtml"]) {
		 		  @unlink($c["dyncachepath"]."dyncache-".jpcacheFilename($out, $variation));	
			}	else if ($c["renderstatichtml"]) {
			    @unlink($c["cachepath"]."static/dyncache-".jpcacheFilename($out, $variation));				
			}		
		}
	}



	/**
* Launch Name of a Sitepage.
* @param integer SPID to launch
* @param integer ID of the level to launch to.
* @param integer ID of the variation to launch. 
* @returns integer Translated ID after launch
*/
	function launchSitepageName($in, $level, $variation) {
		global $db, $c;

		$out = translateState($in, $level, false);
		$sql = "SELECT * FROM sitepage_names WHERE SPID = $in AND VARIATION_ID = $variation";
		$query = new query($db, $sql);
		$query->getrow();
		$spid = $query->field("SPID");

		if ($spid == "" && $variation != 1) {
			$sql = "SELECT * FROM sitepage_names WHERE SPID = $in AND VARIATION_ID = $variation";

			$query = new query($db, $sql);
			$query->getrow();
			$spid = $query->field("SPID");
			$variation = 1;
		}

		if ($spid != "") {
			$name = addslashes($query->field("NAME"));

			$help = addslashes($query->field("HELP"));
			$short = $query->field("DIRECT_URL");

			if ($short != "") {
				createShortUrl($out, $variation, $short);
			}
			

			if ($name != "" || $help != "") { // Aenderung wegen Menünamen in type 2 und 3			
				$sql = "DELETE FROM sitepage_names WHERE SPID = $out AND VARIATION_ID = $variation";
				$query = new query($db, $sql);
				$sql = "INSERT INTO sitepage_names (SPID, VARIATION_ID, NAME, HELP, DIRECT_URL, DELETED, VERSION) VALUES ";
				$sql .= "($out, $variation, '$name', '$help', '$short', 0, $level)";
				$query = new query($db, $sql);
				$query->free();
			}
		}
		
		if (!$c["classicurls"]) { 
			launchURLPage($out, $variation);
		}
					
		return $out;
	}

	/**
* launch Sitepage Owner
* @param integer ID of the sitepage
* @param integer ID of the level to launch to.
* @returns integer Translated ID after launch.
*/
	function launchSitepageOwner($in, $level) {
		global $db;

		$out = translateState($in, $level, false);
		$sql = "SELECT GROUP_ID FROM sitepage_owner WHERE SPID = $in";
		$query = new query($db, $sql);
		$query->getrow();
		$group = $query->field("GROUP_ID");

		if ($group != "") {
			$sql = "DELETE FROM sitepage_owner WHERE SPID = $out";

			$query = new query($db, $sql);
			$sql = "INSERT INTO sitepage_owner (SPID, GROUP_ID) VALUES ($out, $group)";
			$query = new query($db, $sql);
			$query->free();
		}

		return $out;
	}

	/**
* Launch Sitemap
* @param integer MENU_ID to launch.
* @param integer ID of the level to launch to.
* @returns integer Translated ID after launch
*/
	function launchSitemap($in, $level, $variation) {
		global $db;
		if (!checkACL($in)) {
			$out = translateState($in, $level);
			$sql = "SELECT * FROM sitemap WHERE MENU_ID = $in";

			$query = new query($db, $sql);
			$query->getrow();

			$spm = $query->field("SPM_ID");
			$name = addslashes($query->field("NAME"));
			$parent = $query->field("PARENT_ID");
			$posi = $query->field("POSITION");
			$displayed = $query->field("IS_DISPLAYED");
			$cached = $query->field("IS_CACHED");
			$ispopup = $query->field("IS_POPUP");

			$spmTrans = translateState($spm, $level, false);
			$parentTrans = translateState($parent, $level, false);

			$sql = "DELETE FROM sitemap WHERE MENU_ID = $out";
			$query = new query($db, $sql);
			$sql = "INSERT INTO sitemap (MENU_ID, PARENT_ID, SPM_ID, NAME, POSITION, DELETED, VERSION, IS_DISPLAYED, IS_CACHED, IS_POPUP) VALUES ";
			$sql .= "($out, $parentTrans, $spmTrans, '$name', $posi, 0, $level, $displayed, $cached, $ispopup)";
			$query = new query($db, $sql);
			$query->free();
			//checking for type=2 and type=3 for launching also the menu_names
			$spmtype = getDBCell("sitepage_master", "SPMTYPE_ID", "SPM_ID = $spm");

			if ($spmtype > 1) {
				launchSitepageName($in, $level, $variation);
			}

			return $out;
		} else {
			return translateState($in, $level);
		}
	}

	
	/**
	* Launch a Cluster
	* @param integer CGID to launch
	* @param integer ID of the level to launch to.
	* @returns integer Translated ID after launch
	*/	
	function launchCompoundGroup($in, $level) {
		global $db;
		if (! checkACL($in)) {
			$out = translateState($in, $level);	
			$sql = "SELECT * FROM compound_groups WHERE CGID = $in";
			$query = new query ($db, $sql);
			$name = addslashes($query->field("NAME"));
			$description = addslashes($query->field("DESCRIPTION"));
			$sortmode = $query->field("SORTMODE");
			
			$sql = "INSERT INTO compound_groups (CGID, NAME, DESCRIPTION, SORTMODE, VERSION) VALUES($out, '$name', '$description', $sortmode, $level)";
			$query = new query($db, $sql);
			$query->free();
			launchCompoundGroupMembers($in, $level);
			return $out;
		} else {
			return translateState($in, $level, false);	
		}
	}
	
	/**
	 * Launch the clusters in a compound-group
	 * @param integer $in CGID to find clusters from
	 * @param integer ID of the level to launch to.
	 */
	function launchCompoundGroupMembers($in, $level) {
		global $db;
		
		$cgidTrans = translateState($in, $level, false);
		deleteRow("compound_group_members", "CGID = ".$cgidTrans);
		
		$sql = "SELECT * FROM compound_group_members WHERE CGID = $in";
		$query = new query($db, $sql);
		while ($query->getrow()) {
			$position = $query->field("POSITION");
			$cgmid = $query->field("CGMID");
			
			$variations = createDBCArray("cluster_variations", "VARIATION_ID", "CLNID = $cgmid");
			for ($i=0; $i < count($variations); $i++) {
				$cgmidTrans = launchCluster($cgmid, $level, $variation);
			}
			
			$sql = "INSERT INTO compound_group_members (CGID, CGMID, POSITION) VALUES($cgidTrans, $cgmidTrans, $position)";
			$squery = new query($db, $sql);
			$squery->free();
		}
		$query->free();				
	}
	


	/**
* Launch a Content-Item
* @param integer CID to launch
* @param integer ID of the level to launch to.
* @param integer ID of the variation to launch.
* @returns integer Translated ID after launch
*/
	function launchContent($in, $level, $variation) {
		global $db, $auth;
		if (!checkACL($in)) {						
			$out = translateState($in, $level);
			$sql = "SELECT * FROM content WHERE CID = $in";
			$query = new query($db, $sql);
			$query->getrow();
			$module = $query->field("MODULE_ID");
			$category = $query->field("CATEGORY_ID");
			$mtid = $query->field("MT_ID");
			$name = addslashes($query->field("NAME"));
			$keywords = addslashes($query->field("KEYWORDS"));
			$description = addslashes($query->field("DESCRIPTION"));
			$delme = $query->field("DELETED");
			$accesskey = $query->field("ACCESSKEY");

			// do some launches
			$mtTrans = launchMetaTemplate($mtid, $level);

			$sql = "DELETE FROM content WHERE CID = $out";
			$query = new query($db, $sql);
			$sql = "INSERT INTO content (CID, MODULE_ID, CATEGORY_ID, MT_ID, NAME, DESCRIPTION, KEYWORDS, CREATED, LAST_MODIFIER, VERSION, DELETED, ACCESSKEY) VALUES ";
			$sql .= "($out, $module, $category, $mtTrans, '$name', '$description', '$keywords', NOW(), '$auth->user', $level, $delme, '$accesskey')";
			
			$query = new query($db, $sql);
			// launch the content.
			$dbc = createDBCArray("content_variations", "VARIATION_ID", "DELETED=0 AND CID=".$in);
			for ($i=0; $i<count($dbc);$i++) {
			  launchContentVariation($in, $module, $level, $dbc[$i]);
      }		
			// launch metas
			$sql = "SELECT MID FROM meta WHERE CID = $in AND DELETED=0";
			$query = new query($db, $sql);

			while ($query->getrow()) {
				launchMeta($query->field("MID"), $level);
			}

			$query->free();			
			return $out;
		} else
			return translateState($in, $level);
	}

	/** 
	* Launch a Content-Variation
	* @param integer CID to launch
	* @param integer ID of the plugin the content is of 
	* @param integer ID of the level to launch to.
	* @param integer ID of the variation to launch.
	* @returns integer Translated ID after launch
	*/
	function launchContentVariation($in, $plugin, $level, $variation) {
		global $db;

		$out = translateState($in, $level);
		$sql = "SELECT FK_ID, DELETED FROM content_variations WHERE CID = $in AND VARIATION_ID = $variation";
		$query = new query($db, $sql);
		$query->getrow();
		$fkid = $query->field("FK_ID");
		$delme = $query->field("DELETED");

		if ($fkid == "" && $variation != 1) {
			$sql = "SELECT FK_ID, DELETED FROM content_variations WHERE CID = $in AND VARIATION_ID = 1";

			$query = new query($db, $sql);
			$query->getrow();
			$fkid = $query->field("FK_ID");
			$variation = 1;
			$delme = $query->field("DELETED");
		}

		if ($fkid != "") {
			$fkidTrans = launchPlugin($fkid, $plugin, $level);

			$sql = "DELETE FROM content_variations WHERE CID = $out AND VARIATION_ID = $variation";
			$query = new query($db, $sql);
			$sql = "INSERT INTO content_variations (CID, FK_ID, DELETED, VARIATION_ID) VALUES ($out, $fkidTrans, $delme, $variation)";
			$query = new query($db, $sql);
		}

		$query->free();
		return $out;
	}

	/**
* Creates or retrieves a Translation of the object.
* @param integer ID of the object to launch
* @param integer level, which is to be created or retrieved. Level for Live is 10! 
* @param boolean True, if entry in ACL is to be done, false if not.
* @returns integer Translated ID for specified level.
*/
	function translateState($in, $level = 10, $setacl = true) {
		global $db, $acl;

		$out = 0;
		$sql = "SELECT OUT_ID FROM state_translation WHERE IN_ID = $in AND LEVEL = $level";
		$query = new query($db, $sql);

		if ($query->getrow()) {
			$out = $query->field("OUT_ID");

			if ($setacl) {
				$sql = "UPDATE state_translation SET MODIFIED = NOW()";

				$sql .= " , EXPIRED=0";
				$sql .= " WHERE IN_ID = $in";
				$query = new query($db, $sql);
			}

			$query->free();
		} else {
			$out = $db->nextid("GUID");

			$sql = "INSERT INTO state_translation (IN_ID, OUT_ID, LEVEL, MODIFIED, EXPIRED) VALUES ($in, $out, $level, NOW(),0)";
			$query = new query($db, $sql);
			$query->free();
		}

		if ($in != "")
			if ($setacl)
				array_push($acl, $in);

		return $out;
	}

	/**
* Checks, whether an element is already in the Anti-Cycle-list and must not be launched 
* again.
* @param ID of the object to launch
*/
	function checkACL($in) {
		global $acl;

		return in_array($in, $acl);
	}

	/**
	* Deletes the expired-Flag in the state-translations...
	* @param integer Translated-ID of an item (OUT-ID)
	*/
	function delExpired($out) {
		global $db;

		$sql = "UPDATE state_translation SET EXPIRED=0 WHERE OUT_ID = $out";
		$query = new query($db, $sql);
		$query->free();
	}
?>