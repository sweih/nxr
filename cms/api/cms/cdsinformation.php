<?
	/**
	 * @module Status
	 * @package ContentManagement
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
	 * gets the template of an sitepage.
	 * @param	integer		ID of the sitepage
	 * @returns	string		template of the sitepage.
	 */
	function getTemplate($spid) {
		$spm = getDBCell("sitepage", "SPM_ID", "SPID = $spid");

		if ($spm != "") {
			return getDBCell("sitepage_master", "TEMPLATE_PATH", "SPM_ID=$spm");
		} else {
			return "";
		}
	}

	/**
 * Returns the name of a sitepage
 * @param 	integer		ID of the sitepage
 * @returns string		Name of the sitepage
 */
	function getSPName($spid) {
		$menu = getDBCell("sitepage", "MENU_ID", "SPID=$spid");

		$name = getDBCell("sitemap", "NAME", "MENU_ID = $menu");
		return $name;
	}
	
	/**
	 * Checks, whether the page with menuID and variationID is expired or not
	 */
	function isMenuExpired($menuID, $variationID) {
		$result = true;
		$spid = getDBCell("sitepage", "SPID", "MENU_ID=".$menuID);		
		if ($spid != "") {
			$result = isSPExpired($spid, $variationID);		
		}
		
		return $result;
	}

	/**
	 * Returns the name of a SitePageMaster
	 * @param     integer        ID of the sitepage
	 * @returns string        Name of the corresponding SitePageMaster
	 */
	function getSPMName($spid) {
		$spm = getDBCell("sitepage", "SPM_ID", "SPID=$spid");

		$name = getDBCell("sitepage_master", "NAME", "SPM_ID = $spm");
		return $name;
	}

	/**
	 * determines the sitepage id to a given clusternode and a sitepagemaster-name
	 * @param integer	Cluster-Node-ID
	 * @param integer	Name of the sitepage-Master
	 * @returns string	path to the sitepage with template and variation.
	 */
	function getSPFromCluster($clnid, $spmName) {
		$spm = getDBCell("sitepage_master", "SPM_ID", "UPPER(NAME) = UPPER('$spmName')");

		$spmTrans = getDBCell("state_translation", "OUT_ID", "IN_ID = $spm AND LEVEL=10");
		$alt = "";

		if ($spmTrans != "")
			$alt = " OR SPM_ID = $spmTrans";

		$spid = getDBCell("sitepage", "SPID", "(SPM_ID = $spm $alt) AND CLNID = $clnid");
		$template = getDBCell("sitepage_master", "TEMPLATE_PATH", "SPM_ID = $spm");

		if ($spid != "" && $template != "") {
			global $v;

			if (isCached($spid, $v)) {
				return getMenuLink($spid, $v);
			} else {
				if (!isSPExpired($spid))
					return $template . "?page=$spid&v=$v";
			}
		} else
			return "";
	}

	/**
	 * Determines, whether the sitepage is a single-page or a multi-page or a portal-page.
	 * @param 		integer		ID of the sitepage
	 * @returns 	integer		1=Singlepage, 2=Multipage, 3=Portalpage.
	 */
	function getSPType($spid) {
		$spm = getDBCell("sitepage", "SPM_ID", "SPID = $spid");

		$type = getDBCell("sitepage_master", "SPMTYPE_ID", "SPM_ID = $spm");
		return $type;
	}

	/**
	 * Checks, whether a Sitepage is expired or not.
	 * @param 	integer 	ID of the CLuster to check for expiration.
	 * @param 	integer		ID of the Variation
	 * @param	integer		ID of the level to scheck on.
	 * @return	boolean		true, if expired, false else.
	 */
	function isSPExpired($spid, $variation = 0, $level=-1) {
		global $v;

		if ($spid == "")
			return true;

		if ($variation == 0)
			$variation = $v;

		$check = getDBCell("state_translation", "EXPIRED", "OUT_ID = $spid");

		if ($check == 1)
			return true;

		// and now, get clid and check this
		$clnid = getDBCell("sitepage", "CLNID", "SPID = $spid");
		$clid = getDBCell("cluster_variations", "CLID", "CLNID = $clnid AND VARIATION_ID = $variation");

		if ($clid == "")
			return true;

		return isExpired($clid, $level);
	}

	/**
	 * Checks, whether a Cluster-Instance (clid) is expired or not.
	 * works in live-versions only!
	 * Checks also, if a sitepage is live or not.
	 * @param 	integer 	ID of the CLuster to check for expiration.
	 * @param	integer		ID of the level to scheck on.
	 * @return	boolean		true, if expired, false else.
	 */
	function isExpired($clid, $level=-1) {
		global $splevel;

		if ($clid == "")
			return true;

		if ($level==-1) $level = $splevel;
		
		if ($level == 10) {
			$check = getDBCell("state_translation", "EXPIRED", "OUT_ID = $clid");

			if ($check == 1)
				return true;

			// check, wheter mother cluster is deleted.
			$mother = getDBCell("state_translation", "IN_ID", "OUT_ID = $clid");
			$del = getDBCell("cluster_variations", "DELETED", "CLID = $mother");

			if ($del == 1 || $del == "")
				return true;

			// check sitepage-variables now.
			global $page;
			$start = getDBCell("sitepage", "LAUNCH_DATE", "SPID = $page");
			$expired = getDBCell("sitepage", "EXPIRE_DATE", "SPID = $page");
			$today = date("Y-m-d H:i");

			// check, if expired.
			if ($expired != "" && $expired != "0000-00-00 00:00:00" && $expired != $start) {
				if ($today > $expired)
					return true;
			}

			if ($start != "" && $start != "0000-00-00 00:00:00" && $expired != $start) {
				if ($today < $start)
					return true;
			}
		}

		return false;
	}

	/**
	 * gets the Title and Help-Text of a Sitepage
	 * @param integer ID of the Sitepage
	 * @param integer ID of the VARIATION
	 * @param boolean true =>retrieve the category names of multipages
	 * @return 2D-array, with 0=>NAME and 1=>HELP
	 */
	function getMenu($spid, $variation = 0, $isMultipageName = false) {
		global $db, $v;

		if ($variation == 0)
			$variation = $v;

		$spm = getDBCell("sitepage", "SPM_ID", "SPID=$spid");
		$spmtype = getDBCell("sitepage_master", "SPMTYPE_ID", "SPM_ID = $spm");

		if ($isMultipageName == false || $spmtype == 1) {
			$name = getDBCell("sitepage_names", "NAME", "SPID = $spid AND VARIATION_ID = $variation AND DELETED = 0");

			if ($name == "") { // the variation is not defined.
				$sql = "SELECT MIN(s.VARIATION_ID) AS ANZ FROM sitepage_names s, variations v WHERE s.SPID = $spid AND s.DELETED = 0 AND s.NAME <>'' AND s.VARIATION_ID = v.VARIATION_ID AND v.DELETED=0";

				$query = new query($db, $sql);
				$query->getrow();
				$any = $query->field("ANZ");
				$query->free();

				if ($any == "") {
					$name = "&lt;not defined&gt;";

					$variation = 0;
				} else {
					$variation = $any;

					$name = getDBCell("sitepage_names", "NAME", "SPID = $spid AND VARIATION_ID = $variation AND DELETED = 0");
				}
			}

			$help = getDBCell("sitepage_names", "HELP", "SPID = $spid AND VARIATION_ID = $variation AND DELETED = 0");
			$ergebnis[0] = $name;
			$ergebnis[1] = $help;
			return $ergebnis;
		} else {
			$menuId = getDBCell("sitepage", "MENU_ID", "SPID=$spid");

			$ergebnis[0] = getDBCell("sitepage_names", "NAME", "SPID = $menuId AND VARIATION_ID = $variation");
			$ergebnis[1] = getDBCell("sitepage_names", "HELP", "SPID = $menuId AND VARIATION_ID = $variation");
			return $ergebnis;
		}
	}

	/**
	 * returns the uri for a menu-item. Internally used for launch.
	 * @param integer ID of the Sitepage
	 * @param integer ID of the VARIATION
	 * @param array additional parameters, use: array('varname1'=>'value1', 'varname2'=>'value2'), the routine encodes the values with rawurlencode (RFC 1738), using array('value1','value2') results in '&var0=value1&var1=value2'
	 * @return string uri for menu-item.
	 */
	function getMenuLink2($spid, $variation = 0, $addparas = null) {
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
			$lc = 0;
			if ($mn != "") 
				$lc = getDBCell("sitemap", "IS_CACHED", "MENU_ID = $mn");

			if ($c["renderstatichtml"] && $lc == 1) {
				$spname = getSPNameUrlSafe($spid);

				$link_uri = $c["cachedocroot"] . $spname . "_" . $spid . "_v" . $variation . (($linkadd == "") ? ".html" : ".php?");
				// $link_uri = $c["cachedocroot"]."p".$spid."_v".$variation.(($linkadd=="")?".html":".php?");
				return ($link_uri . $linkadd);
			} else {
				$template = getTemplate($spid);

				$link_uri = $c["livedocroot"] . $template . "?page=" . $spid . "&v=" . $variation;
				return ($link_uri . $linkadd);
			}
		} else { // Development- (Template-) Version
			$template = getTemplate($spid);

			$link_uri = $c["devdocroot"] . $template . "?page=" . $spid . "&v=" . $variation;
			return ($link_uri . $linkadd);
		}
	}
?>