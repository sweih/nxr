<?
	/**********************************************************************
	 *	N/X - Web Content Management System
	 *	Copyright 2002 Sven Weih
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
	class Management {
		var $parent;

		/**
		* Standard constructor.
		*/
		function Management(&$parent) { $this->parent = &$parent; }

		/**
		 * Gets the Variation-Shorttag from the ID
		 * @param integer ID of the Variation
		 * @return string shorttag of the Variation
		 */
		function variationIdToTag($id) { return strtolower(getDBCell("variations", "SHORTTEXT", "VARIATION_ID = $id AND DELETED=0")); }

		/**
		* Gets the Variation-ID from the Shorttag
		* @param  string shorttag of the Variation
		* @return integer ID of the Variation
		*/
		function variationTagToId($tag) { return getDBCell("variations", "VARIATION_ID", "UPPER(SHORTTEXT) = UPPER('$tag') AND DELETED=0"); }

		
		
		function isPasswordProtected($spid) {
			$result = false;
			$res = getDBCell("sitepage", "PASSWORD_PROTECTED","SPID=".$spid);
			if ($res==1) $result = true;
			return $result;
		}
		
		/**
		 * To be used in start file (e.g. index.php) to determine the first pageId
		 * to use.
		 * @param integer ID of the variation the Startpage should be find in.
		 * @returns	integer		Sitepage-ID of the first sitepage to use.
		 */
		function getStartPage($variation=0) {
			global $c;
			
			if ($variation == 0)
			  $variation = $c["stdvariation"];
			$zeroTrans = getDBCell("state_translation", "OUT_ID", "IN_ID=0 AND LEVEL=10");
				
			if ($this->parent->level < 10) {
				$menues = createDBCArray("sitemap", "MENU_ID", "IS_DISPLAYED=1 AND PARENT_ID=0 ORDER BY POSITION");			
			} else {			
				$menues = createDBCArray("sitemap", "MENU_ID", "IS_DISPLAYED=1 AND PARENT_ID=$zeroTrans ORDER BY POSITION");				
			}

			for ($i = 0; $i < count($menues); $i++) {
				$spids = createDBCArray("sitepage", "SPID", "MENU_ID = " . $menues[$i] . " ORDER BY POSITION");
				for ($j = 0; $j < count($spids); $j++) {
					if (!$this->isSPExpired($spids[$j], $variation, $this->level))
						return $spids[$j];
				}
			}

			return 0;
		}
	
		
	/**
	 * Checks, whether a Sitepage is expired or not.
	 * @param 	integer 	ID of the CLuster to check for expiration.
	 * @param 	integer		ID of the Variation, takes std-variation from parent-api
	 * @return	boolean		true, if expired, false else.
	 */
	function isSPExpired($spid, $variation = 0) {
	
		if ($spid == "")
			return true;

		if ($variation == 0)
			$variation = $this->parent->variation;
		$check = getDBCell("state_translation", "EXPIRED", "OUT_ID = $spid");

		if ($check == 1)
			return true;

		// and now, get clid and check this
		$clnid = getDBCell("sitepage", "CLNID", "SPID = $spid");
		$clid = getDBCell("cluster_variations", "CLID", "CLNID = $clnid AND VARIATION_ID = $variation");
		
		if ($clid == "")
			return true;

		return $this->isClusterExpired($clid);
	}

	/**
	 * Checks, whether a Cluster-Instance (clid) is expired or not.
	 * works in live-versions only!
	 * Checks also, if a sitepage is live or not.
	 * @param 	integer 	ID of the CLuster to check for expiration.
	 * @return	boolean		true, if expired, false else.
	 */
	function isClusterExpired($clid) {
		
		if ($clid == "")
			return true;

		if ($this->parent->level == 10) {
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
			if ($expired != "" && $expired != "0000-00-00 00:00:00") {
				if ($today > $expired)
					return true;
			}

			if ($start != "" && $start != "0000-00-00 00:00:00") {
				if ($today < $start)
					return true;
			}
		}

		return false;
	}
		
		/**
		 * Gets the available Variations of a cluster-Node
		 * @param 		integer		Cluster-Node-ID
		 * @returns	integer		Linear array with all available Variations.
		 */
		function getClusterVariationsFromNode($clnid) {
			global $db;

			$splevel = $this->parent->level;

			if ($splevel == 10) { // checking for live-variations....
				$sql = "SELECT 
	  				DISTINCT cv.VARIATION_ID 
	  			FROM 
	  				cluster_variations cv, 
	  				cluster_node cn, 
	  				sitepage sp,
	  				sitepage_names sn,
	  				state_translation st 
	  			WHERE
	  			    cn.CLNID = $clnid
                           	AND cn.CLNID = cv.CLNID  			
	  			AND cv.CLID = st.OUT_ID
	  			AND st.EXPIRED = 0
	  			AND cv.DELETED =  0
				AND cn.CLNID = sp.CLNID
				AND sp.SPID = sn.SPID
				AND sn.DELETED = 0";
			} else {
				$sql = "SELECT 
	  				DISTINCT cv.VARIATION_ID 
	  			FROM 
	  				cluster_variations cv, 
	  				cluster_node cn, 
	  				sitepage sp,
	  				sitepage_names sn,
	  				sitepage_variations sv 
	  			WHERE
	  				cn.CLNID = $clnid
	  			AND	cv.CLNID = cn.CLNID 
	  			AND cv.DELETED =  0
				AND cn.CLNID = sp.CLNID
				AND sp.SPID = sn.SPID
				AND sn.DELETED = 0
				AND sp.SPM_ID = sv.SPM_ID
				AND sv.VARIATION_ID = cv.VARIATION_ID";
			}

			$query = new query($db, $sql);
			$returns = array ();

			while ($query->getrow()) {
				array_push($returns, $query->field("VARIATION_ID"));
			}

			$query->free();
			return $returns;
		}

		/**
		 * Determines, wheter the given pageId is a live-site or a development site.
		 * @param 		integer		Page-ID to check
		 * @returns	integer		level of the sitepage. 0=Development, 10=Live.
		 */
		function getPageLevel($pageId) {
			$level = getDBCell("state_translation", "LEVEL", "OUT_ID = $pageId");

			if ($level == "")
				$level = 0;

			return $level;
		}

		/**
		 * Gets the Cluster-ID (CLID) from the Cluster-Node-Id and a variation ID.
		 * @param 	integer		Cluster-Node-Id
		 * @param	integer		Variation-ID
		 * @returns integer	Cluster-ID (CLID)
		 */
		function getClusterFromNode($clnid, $variation = 0) {
			if ($variation == 0)
				$variation = $this->parent->variation;

			global $db;
			$sql = "SELECT CLID FROM cluster_variations WHERE CLNID = $clnid AND VARIATION_ID = $variation";
			$query = new query($db, $sql);

			if ($query->getrow())
				return $query->field("CLID");
		}

   /**
	* Get the Cluster-ID (CLID) from a Sitepage-ID and a Variation-ID.
	* @param	integer		Sitepage-ID (given as page)
	* @param	integer		Variation-ID (given as v)
	* @returns integer		Id of the cluster (clid)
	*/
		function getClusterFromPage($page, $variation = 0) {
			if ($variation == 0)
				$variation = $this->parent->variation;

			global $db;
			$sql = "SELECT 
	 				cv.CLID 
	 			FROM 
	 				cluster_variations cv,
	 				cluster_node cn, 
	 				sitepage sp 
	 			WHERE
	 				sp.SPID = $page
	 			AND sp.CLNID = cn.CLNID
	 			AND cn.CLNID = cv.CLNID
	 			AND cv.VARIATION_ID = $variation";

			$query = new query($db, $sql);

			if ($query->getrow())
				return $query->field("CLID");
		}

		/**
		* Get the Cluster-Node-ID (CLNID) from a Sitepage-ID. 
		* The difference between the cluster-node and the cluster-id is, that 
		* the cluster-node-id is free of variations. That means, you cannot get 
		* any content from a Cluster-Node-ID, but you can use it for getting different 
		* Variations of the cluster. E.G. it may happen, that a page in the selected variation 
		* is not available and therefore clid=0. You can then still use the getPageVariation-Command 
		* for getting the standard variation. 
		* @param 	integer 	Sitepage-ID
		* @returns integer		Cluster-Node Id (clnid)
		*/
		function getClusterNodeFromPage($pageId) { return getDBCell("sitepage", "CLNID", "SPID=$pageId"); }
	}
	
	
	
			/**
		 * To be used in header.inc.php to determine the URI of the startpage.
		 * to use.
		 * @param integer ID of the variation the Startpage should be find in.
		 * @returns	integer		Sitepage-ID of the first sitepage to use.
		 */		
		function getStartPageURI($variation=0, $level=10) {
			global $c;
			
			$uri=0;
			$pageId = 0;
			
			if ($variation == 0)
			  $variation = $c["stdvariation"];
			$zeroTrans = getDBCell("state_translation", "OUT_ID", "IN_ID=0 AND LEVEL=10");
			
			if ($level < 10) {
				$menues = createDBCArray("sitemap", "MENU_ID", "IS_DISPLAYED=1 AND PARENT_ID=0 ORDER BY POSITION ASC");
			} else {
				$menues = createDBCArray("sitemap", "MENU_ID", "IS_DISPLAYED=1 AND PARENT_ID=$zeroTrans ORDER BY POSITION ASC");
			}

			for ($i = 0; $i < count($menues); $i++) {
				$spids = createDBCArray("sitepage", "SPID", "MENU_ID = " . $menues[$i] . " ORDER BY POSITION");
				for ($j = 0; $j < count($spids); $j++) {
					if (!isSPExpired($spids[$j], $variation, $level)) {
						$pageId =  $spids[$j];
			 			$menu =	new Menu(null, $pageId, $variation, $level);
		  			return $c["host"].$menu->getLink();			
					}
				}
			}		  		 			
		}
?>