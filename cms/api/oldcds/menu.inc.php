<?
	/**
	 * MENU
	 * Functions for creating menues.
	 * @module Menu
	 * @package CDS
	 */
	require_once $c["path"] . "api/cms/cdsinformation.php";

	/**
	 * Draws a square for entering the editing mode of the Website
	 */
	function drawSMAEntry() {
		global $c, $page, $v;

		$out = '<a href="' . $c["docroot"] . 'modules/sma/sma.php?page=' . $page . '&v=' . $v . '" target=_blank><img src="sys/sma_start.gif" border="0" alt=""></a>';
		echo $out;
	}

	/**
	 * Returns all Pages beeing on the same level, i.e. having the same
	 * parent page.
	 *
	 * @param string $path Path to the page, "." if you want to use $spid, else use the path in this parameter.
	 * @param integer $spid ID of the sitepage to get the menu strucutre from from.
	 */
	function getSameLevel($path = ".", $spid = 0) {
		global $page;

		if (($path == ".") && ($spid == 0))
			$spid = $page;

		if (($path == "/"))
			return (array ( getStartPage() ));

		if ($path == ".") {
			$spidUsed = getDBCell("sitepage", "MENU_ID", "SPID = $spid");
		} else {
			$spidUsed = getPageFromTree2($path);
		}

		$parentMenuId = getDBCell("sitemap", "PARENT_ID", "MENU_ID = $spidUsed");
		#		echo $parentMenuId;
		$menues = createDBCArray("sitemap", "MENU_ID", "IS_DISPLAYED=1 AND PARENT_ID = $parentMenuId ORDER BY POSITION ASC");
		#		echo count($menues);
		$res = array ();

		for ($i = 0; $i < count($menues); $i++) {
			$id = $menues[$i];

			$spid = getDBCell("sitepage", "SPID", "MENU_ID = $id ORDER BY POSITION ASC");

			if (!isSPExpired($spid))
				array_push($res, $spid);
		}

		return $res;
	}

	/**
	 * Returns all Pages beeing on the lower level, i.e. having the given
	 * path or SPID as parent.
	 *
	 * @param string $path Path to the page, "." if you want to use $spid, else use the path in this parameter.
	 * @param integer $spid ID of the sitepage to get the menu strucutre from from.
	 */
	function getLowerLevel($path = ".", $spid = 0) {
		global $page, $db;

		if (($path == ".") && ($spid == 0))
			$spid = $page;

		if (($path == "/") && ($spid == 0))
			$spid = getStartPage();

		if ($path == ".") {
			$spidUsed = getDBCell("sitepage", "MENU_ID", "SPID = $spid");
		} else {
			$spidUsed = getPageFromTree2($path);
		}

		$childs = createDBCArray("sitepage sp, sitemap sm", "SPID", "sm.PARENT_ID = $spidUsed AND sp.MENU_ID = sm.MENU_ID AND sm.IS_DISPLAYED=1 ORDER BY sm.POSITION, sp.POSITION");
		global $splevel, $v;
		$checked = array ();

		for ($i = 0; $i < count($childs); $i++) {
			// get clid
			$myspid = $childs[$i];
			$clnid = getDBCell("sitepage", "CLNID", "SPID = $myspid");
			$clid = getDBCell("cluster_variations", "CLID", "CLNID = $clnid AND VARIATION_ID = $v");

			if ($splevel == 10) {
				if (!isExpired($clid)) {
					array_push($checked, $myspid);

					$last = $menuID;
				}
			} else {
				array_push($checked, $myspid);

				$last = $menuID;
			}

		}

		return $checked;
	}

	/**
	 * Returns the parent page of the given
	 *  path or SPID.
	 *
	 * @param string $path Path to the page, "." if you want to use $spid, else use the path in this parameter.
	 * @param integer $spid ID of the sitepage to get the menu strucutre from from.
	 */
	function getUpperLevel($path = ".", $spid = 0) {
		global $page;

		if (($path == ".") && ($spid == 0))
			$spid = $page;

		if (($path == "/"))
			return (0);

		if ($path == ".") {
			$spidUsed = getDBCell("sitepage", "MENU_ID", "SPID = $spid");
		} else {
			$spidUsed = getPageFromTree2($path);
		}

		$parentMenuId = getDBCell("sitemap", "PARENT_ID", "MENU_ID = $spidUsed");
		$parSP = getDBCell("sitepage", "SPID", "MENU_ID = $parentMenuId");

		if ($parSP == "")
			$parSP = getDBCell("state_translation", "OUT_ID", "IN_ID=0");

		return $parSP;
	}

	/**
	 * Returns all instances of a multipage.
	 * path or SPID as parent.
	 *
	 * @param integer $spid ID of the sitepage to get the menu strucutre from from.
	 */
	function getInstances($spid = 0) {
		$menuId = getDBCell("sitepage", "MENU_ID", "SPID = $spid");

		$res = createDBCArray("sitepage", "SPID", "MENU_ID = $menuId ORDER BY POSITION");
		$twins = array ();

		for ($i = 0; $i < count($res); $i++) {
			if (!isSPExpired($res[$i]))
				array_push($twins, $res[$i]);
		}

		return $twins;
	}

	/**
	 * To be used in start file (e.g. index.php) to determine the first SPID
	 * to use.
	 * @returns	integer		Sitepage-ID of the first sitepage to use.
	 */
	function getStartPage() {
		global $v, $is_development;

		if ($v == "")
			$v = 1;

		$zeroTrans = getDBCell("state_translation", "OUT_ID", "IN_ID=0 AND LEVEL=10");

		if ($is_development) {
			$menues = createDBCArray("sitemap", "MENU_ID", "IS_DISPLAYED=1 AND PARENT_ID=0 ORDER BY POSITION");
		} else {
			$menues = createDBCArray("sitemap", "MENU_ID", "IS_DISPLAYED=1 AND PARENT_ID=$zeroTrans ORDER BY POSITION");
		}

		for ($i = 0; $i < count($menues); $i++) {
			$spids = createDBCArray("sitepage", "SPID", "MENU_ID = " . $menues[$i] . " ORDER BY POSITION");

			for ($j = 0; $j < count($spids); $j++) {
				if (!isSPExpired($spids[$j], $v))
					return $spids[$j];
			}
		}

		return 0;
	}

	/**
	 * Use this function for directly referencing a sitepage you know the name of.
	 * Works relative to current pageId
	 * @param string   Id of page to get the other page relative from.
	 * @param string		Name of the Sitepage in form "name1/name2/name3"
	 * @returns	integer		Sitepage-ID of the searched page
	 */
	function getPageRelative($startId, $pagename) {
		$pagename = strtoupper($pagename);

		$menuId = getDBCell("sitepage", "MENU_ID", "SPID=" . $startId);
		$id = getDBCell("sitemap", "MENU_ID", "PARENT_ID = $menuId AND UPPER(NAME) = '" . $pagename . "'");
		$spid = getDBCell("sitepage", "SPID", "MENU_ID = $id");
		return $spid;
	}

	/**
	 * Use this function for directly referencing a sitepage you know the name of.
	 * Works with singlePages only!!!
	 * @param	string		Name of the Sitepage in form "name1/name2/name3"
	 * @returns	integer		Sitepage-ID of the searched page
	 */
	function getPageFromTree($tree = "/") {
		global $splevel, $panic;

		$liste = explode("/", strtoupper($tree));

		if ($splevel != 0) {
			$page = getDBCell("state_translation", "OUT_ID", "IN_ID=0 AND LEVEL=$splevel");

			$backup = $page;
		} else {
			$page = 0;

			$backup = 0;
		}

		for ($i = 1; $i < count($liste); $i++) {
			if ($page == "")
				$page = 0;

			if ($liste[$i] != "") {
				$page = getDBCell("sitemap", "MENU_ID", "PARENT_ID=$page AND UPPER(NAME)='" . $liste[$i] . "'");
			}
		}

		if ($page != "") {
			$spid = getDBCell("sitepage", "SPID", "MENU_ID=$page AND DELETED=0");
		}

		if ($spid != "") {
			return $spid;
		} else
			return $backup;
	}

	function getPageFromTree2($tree = "/") {
		global $splevel, $panic;

		$liste = explode("/", strtoupper($tree));

		if ($splevel != 0) {
			$page = getDBCell("state_translation", "OUT_ID", "IN_ID=0 AND LEVEL=$splevel");

			$backup = $page;
		} else {
			$page = 0;

			$backup = 0;
		}

		for ($i = 1; $i < count($liste); $i++) {
			if ($page == "")
				$page = 0;

			if ($liste[$i] != "") {
				$page = getDBCell("sitemap", "MENU_ID", "PARENT_ID=$page AND UPPER(NAME)='" . $liste[$i] . "'");
			}
		}

		if ($page != "") {
			return $page;
		} else
			return $backup;
	}

	/**
	 * returns the uri for a dispather-link for building a menu-structure
	 * @param integer ID of the Sitepage
	 * @param integer ID of the VARIATION
	 * @param string filename of dispatcher
	 * @param array additional parameters, use: array('varname1'=>'value1', 'varname2'=>'value2'), the routine encodes the values with rawurlencode (RFC 1738), using array('value1','value2') results in '&var0=value1&var1=value2'
	 * @return string uri for dispatcher-link
	 */
	function getDispatcherLink($spid, $variation = 0, $dispatcher = "index.php", $addparas = null) {
		global $c, $is_development;

		global $c_sessionidname;
		global $v;

		if ($variation == 0)
			$variation = $v;

		$sessionid = $addparas[$c_sessionidname];
		$sessionid = ($sessionid != "") ? $sessionid : value($c_sessionidname, "NOSPACES");
		$linkadd = ($sessionid != "") ? ('&' . $c_sessionidname . '=' . $sessionid) : "";

		if (is_array($addparas))
			foreach ($addparas as $key => $value) {
				if ($key != $c_sessionidname)
					$linkadd .= '&' . ((is_int($key)) ? 'var' . $key : $key) . '=' . rawurlencode($value);
			}

		//if (isSPVarLive($spid, $variation)) { // Live-Version
		if (!isset($is_development)) { // Live-Version
			$mn = getDBCell("sitepage", "MENU_ID", "SPID = $spid");

			$lc = getDBCell("sitemap", "IS_CACHED", "MENU_ID = $mn");

			if ($c["renderstatichtml"] && $lc == 1) {
				$link_uri = $c["cachedocroot"] . "p" . $spid . "_v" . $variation . (($linkadd == "") ? ".html" : ".php?");

				return ($link_uri . $linkadd);
			} else {
				$link_uri = $c["livedocroot"] . $dispatcher . "?page=" . $spid . "&v=" . $variation;

				return ($link_uri . $linkadd);
			}
		} else {                       // Development- (Template-) Version
			$link_uri = $c["devdocroot"] . $dispatcher . "?page=" . $spid . "&v=" . $variation;

			return ($link_uri . $linkadd);
		}
	}

	/**
	 * returns the string entered for direct-url
	 * @param integer ID of the Sitepage
	 * @return string name of direct-url
	 */
	function getDirectUrl($spid) {
		global $v;

		return getDBCell("sitepage_names", "DIRECT_URL", "SPID = $spid AND VARIATION_ID = $v");
	}

	/**
	 * returns the uri for a menu-item
	 * @param integer ID of the Sitepage
	 * @param integer ID of the VARIATION
	 * @param array additional parameters, use: array('varname1'=>'value1', 'varname2'=>'value2'), the routine encodes the values with rawurlencode (RFC 1738), using array('value1','value2') results in '&var0=value1&var1=value2'
	 * @return string uri for menu-item.
	 */
	function getMenuLink($spid, $variation = 0, $addparas = null) {
		global $c, $is_development;

		global $c_sessionidname;
		global $v;

		if ($variation == 0)
			$variation = $v;

		$sessionid = $addparas[$c_sessionidname];
		$sessionid = ($sessionid != "") ? $sessionid : value($c_sessionidname, "NOSPACES");
		$linkadd = ($sessionid != "") ? ('&' . $c_sessionidname . '=' . $sessionid) : "";

		if (is_array($addparas))
			foreach ($addparas as $key => $value) {
				if ($key != $c_sessionidname)
					$linkadd .= '&' . ((is_int($key)) ? 'var' . $key : $key) . '=' . rawurlencode($value);
			}

		//if (isSPVarLive($spid, $variation)) { // Live-Version
		if (!isset($is_development) || $is_development == false) { // Live-Version
			$mn = getDBCell("sitepage", "MENU_ID", "SPID = $spid");

			$lc = getDBCell("sitemap", "IS_CACHED", "MENU_ID = $mn");

			if ($c["renderstatichtml"] && $lc == 1) {
				$spname = getSPNameUrlSafe($spid);

				$link_uri = $c["cachedocroot"] . $spname . "_" . $spid . "_v" . $variation . ".html";
				// $link_uri = $c["cachedocroot"]."p".$spid."_v".$variation.(($linkadd=="")?".html":".php?");
				return ($link_uri);
			} else {
				$template = getTemplate($spid);

				$link_uri = $c["docroothtml"] . $template . "?page=" . $spid . "&v=" . $variation;
				return ($link_uri . $linkadd);
			}
		} else { // Development- (Template-) Version
			$template = getTemplate($spid);

			$link_uri = $c["devdocroot"] . $template . "?page=" . $spid . "&v=" . $variation;
			return ($link_uri . $linkadd);
		}
	}

	/**
 * DEPRECATED! use getSameLevel instead.
 * Gets the IDs of all pages having the same parent page. Note, that multi-instance
 * and portal-pages will be only displayed with the page on position 1.
 * @param	integer		Sitepage-Id to get all the Brothers and Sisters from 
 * @param	integer		array of all Sitepage-IDs having the same parent.
 */
	function getSisterPages($spid) {
		$menuId = getDBCell("sitepage", "MENU_ID", "SPID = $spid");

		$parentMenuId = getDBCell("sitemap", "PARENT_ID", "MENU_ID = $menuId");
		$menues = createDBCArray("sitemap", "MENU_ID", "IS_DISPLAYED=1 AND PARENT_ID = $parentMenuId ORDER BY POSITION ASC");
		$res = array ();

		for ($i = 0; $i < count($menues); $i++) {
			$id = $menues[$i];

			$spid = getDBCell("sitepage", "SPID", "MENU_ID = $id ORDER BY POSITION ASC");

			if (!isSPExpired($spid))
				array_push($res, $spid);
		}

		return $res;
	}

	/**
	 * DEPRECATED! USe getInstances instead.
	 * Gets the IDs of any pages belonging to the same instance of a sitepage. 
	 * You use this function for getting a ordered list of all Members of a portal 
	 * or a multi-instance sitepage. Using it with a single-instance-page will only 
	 * return the ID of the single-instance page. The results will be returned in a 
	 * linear array. 
	 * @param 	integer		Sitepage-Id to get all other instances from.
	 * @returns integer		array of all Sitepage-IDs belonging to the same sitepage. 
	 */
	function getTwinPages($spid) {
		$menuId = getDBCell("sitepage", "MENU_ID", "SPID = $spid");

		$res = createDBCArray("sitepage", "SPID", "MENU_ID = $menuId ORDER BY POSITION");
		$twins = array ();

		for ($i = 0; $i < count($res); $i++) {
			if (!isSPExpired($res[$i]))
				array_push($twins, $res[$i]);
		}

		return $twins;
	}

	/**
	 * DEPRECATED! USe getUpperLevel instead
	 * Gets the ID of the parent-Sitepage
	 * @param	integer		Sitepage-ID to get the parent-ID from
	 * @returns integer		Sitepage-ID of the parent Page.
	 */
	function getParentPage($spid) {
		$menuId = getDBCell("sitepage", "MENU_ID", "SPID = $spid");

		$parentMenuId = getDBCell("sitemap", "PARENT_ID", "MENU_ID = $menuId");
		$parSP = getDBCell("sitepage", "SPID", "MENU_ID = $parentMenuId");
		return $parSP;
	}

	/**
 * DEPRECATED! use getLowerLevel instead.
 * Gets the IDs of the child-pages and returns them as linear array.
 * @param 		integer		Sitepage-ID to get the children from.
 * @param		string		Column, to order the results.
 */
	function getChildrenPages($spid) {
		$menuId = getDBCell("sitepage", "MENU_ID", "SPID = $spid");

		$childs = createDBCArray("sitepage sp, sitemap sm", "SPID", "sm.PARENT_ID = $menuId AND sp.MENU_ID = sm.MENU_ID AND sm.IS_DISPLAYED=1 ORDER BY sm.POSITION");
		global $splevel, $v;

		if ($splevel == 10) { // check live-pages for expiration.
			$checked = array ();

			for ($i = 0; $i < count($childs); $i++) {
				// get clid
				$myspid = $childs[$i];

				$clnid = getDBCell("sitepage", "CLNID", "SPID = $myspid");
				$clid = getDBCell("cluster_variations", "CLID", "CLNID = $clnid AND VARIATION_ID = $v");

				if (!isExpired($clid))
					array_push($checked, $myspid);
			}

			return $checked;
		} else
			return $childs;
	}
	
   /**
	* Returns array containing all pages an the path from specified page to root
	*
	* @param string $path Path to the page, "." if you want to use $spid, else use the path in this parameter.
	* @param integer $spid ID of the sitepage to get the menu structure from.
	* @returns array 
	*/
	function getPathToRoot($path=".", $spid=0) {
		global $page, $v;
		if (($path==".") && ($spid==0)) $spid=$page;

		$currentMenuId = getDBCell("sitepage", "MENU_ID", "SPID = $spid");
		$currentSPID = $page;
		if (getSPType($currentSPID) > 1) 
			$currentSPID = getDBCell("sitepage", "SPID", "MENU_ID = $currentMenuId ORDER BY POSITION");
		$currentSPName = getMenu($spid, $v, true);
		$path = array();
		while ($currentMenuId != 0) {
			array_push($path, $currentSPID);
			$currentMenuId = getDBCell("sitemap", "PARENT_ID", "MENU_ID = $currentMenuId");
			$currentSPID = getDBCell("sitepage", "SPID", "MENU_ID = ".$currentMenuId);
			$currentSPName = getMenu($currentSPID, $v, true);
		}
		
		return $path;
	}	
?>