<?
	/**
	 * @package CMS
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
	 * Gets all available variations for a cluster from the database.
	 * @param integer $clnid Cluster-Node-ID where to determine the variations from
	 * @param integer $variation Currently set variation.
	 */
	function populateVariations($clnid, $variation) {
		global $db;

		$isInVariations = false;
		$variations = null;
		$sql = "SELECT v.NAME AS NAM, v.VARIATION_ID AS VARI FROM variations v, cluster_variations c WHERE c.CLNID = $clnid AND c.DELETED=0 AND c.VARIATION_ID = v.VARIATION_ID AND v.DELETED = 0 ORDER BY v.NAME";

		$query = new query($db, $sql);

		while ($query->getrow()) {
			$nextId = count($variations);

			$variations[$nextId][0] = $query->field("NAM");
			$variations[$nextId][1] = $query->field("VARI");

			if ($variation == $variations[$nextId][1])
				$isInVariations = true;
		}

		// set another variation, as standard variation is not available!
		if (!$isInVariations) {
			global $c;

			pushVar("variation", $variations[0][1]);
			$variation = $variations[0][1];

			if ($variation == "")
				$variation = $c["stdvariation"];
		}

		return $variations;
	}

	/**
	 * Gets all available variations for a sitepage from the database.
	 * @param integer $spid Sitepage-ID where to determine variations from.
	 * @param integer $clnid Cluster-Node-ID where to determine the variations from
	 * @param integer $variation Currently set variation.
	 */
	function populateSPVariations($spid, $clnid, &$variation) {
		global $db;

		$spm = getDBCell("sitepage", "SPM_ID", "SPID = $spid");
		$vars = createDBCArray("sitepage_variations", "VARIATION_ID", "SPM_ID = $spm");
		if (count($vars) > 1) {
			$cvars = implode(", ", $vars);
		} else {
		  $cvars = $vars[0];	
		}
		$isInVariations = false;
		$variations = null;
		$sql = "SELECT v.NAME AS NAM, v.VARIATION_ID AS VARI FROM variations v, cluster_variations c WHERE c.CLNID = " . $clnid . " AND c.DELETED=0 AND c.VARIATION_ID = v.VARIATION_ID AND v.DELETED = 0 AND v.VARIATION_ID IN ($cvars) ORDER BY v.NAME";
		$query = new query($db, $sql);

		while ($query->getrow()) {
			$nextId = count($variations);

			$variations[$nextId][0] = $query->field("NAM");
			$variations[$nextId][1] = $query->field("VARI");

			if ($variation == $variations[$nextId][1])
				$isInVariations = true;
		}

		// set another variation, as standard variation is not available!
		if (!$isInVariations) {
			global $c;

			pushVar("variation", $variations[0][1]);
			$variation = $variations[0][1];

			if ($variation == "")
				$variation = $c["stdvariation"];
		}

		return $variations;
	}

	/**
	 * Function for changing the order of the Menu
	 * @param integer ID of the menu Entry
	 * @param integer New position of the Menu entry.
	 */
	function reorderMenu($mid, $position) {
		if ($mid=="0") return false;
		global $db;
			
		$parentMenu = getDBCell("sitemap", "PARENT_ID", "MENU_ID = $mid");

		if (!is_numeric($position) || $position == "")
			$position = 1;

		if ($position < 1)
			$position = 1;

		$oldposition = getDBCell("sitemap", "POSITION", "MENU_ID = $mid");

		// special functions for if the parent-node has changed.
		// to be implemented later.
		if ($oldposition != $positon) {

			// get number of positions...
			$sql = "SELECT COUNT(MENU_ID) AS ANZ FROM sitemap WHERE DELETED=0 AND PARENT_ID = $parentMenu";

			$query = new query($db, $sql);
			$query->getrow();
			$maxpos = $query->field("ANZ");

			// correct positioning
			if ($position > $maxpos)
				$position = $maxpos;

			// is new position smaller then actual one?
			if ($position < $oldposition) {
				$sql = "UPDATE sitemap SET POSITION = (POSITION+1) WHERE POSITION >= $position AND POSITION < $oldposition AND PARENT_ID = $parentMenu";

				$query = new query($db, $sql);
				$sql = "UPDATE sitemap SET POSITION = $position WHERE MENU_ID = $mid";
				$query = new query($db, $sql);
				$query->free();
			} else {
				$sql = "UPDATE sitemap SET POSITION = (POSITION-1) WHERE POSITION > $oldposition AND POSITION <= $position AND PARENT_ID = $parentMenu";

				$query = new query($db, $sql);
				$sql = "UPDATE sitemap SET POSITION = ($position) WHERE MENU_ID = $mid";
				$query = new query($db, $sql);
				$query->free();
			}
		}
		sortTableRows("sitemap", "MENU_ID", "POSITION", "PARENT_ID = $parentMenu AND DELETED=0");
	}
	

	/**
	 * Function for changing the position of a multipage
	 * @param integer ID of the Multipage-Folder+
	 * @param integer ID of the Multiapage
	 * @param integer New Position
	 */
	function reorderSitepage($menu, $spid, $position) {
		global $db;
		
		if ($position < 1)
			$position = 1;
				
		$oldposition = getDBCell("sitepage", "POSITION", "SPID = $spid");

		if ($oldposition != $positon) {

			// get number of positions...
			$sql = "SELECT COUNT(SPID) AS ANZ FROM sitepage WHERE DELETED=0 AND MENU_ID = $menu";

			$query = new query($db, $sql);
			$query->getrow();
			$maxpos = $query->field("ANZ");

			// correct positioning
			if ($position > $maxpos)
				$position = $maxpos;

			// is new position smaller then actual one?
			if ($position < $oldposition) {
				$sql = "UPDATE sitepage SET POSITION = (POSITION+1) WHERE POSITION >= $position AND POSITION < $oldposition AND MENU_ID=$menu";

				$query = new query($db, $sql);
				$sql = "UPDATE sitepage SET POSITION = $position WHERE SPID = $spid";
				$query = new query($db, $sql);
				$query->free();
			} else {
				$sql = "UPDATE sitepage SET POSITION = (POSITION-1) WHERE POSITION > $oldposition AND POSITION <= $position AND MENU_ID = $menu";

				$query = new query($db, $sql);
				$sql = "UPDATE sitepage SET POSITION = ($position) WHERE SPID = $spid";
				$query = new query($db, $sql);
				$query->free();
			}
		}
		sortTableRows("sitepage", "SPID", "POSITION", "MENU_ID = $menu AND DELETED=0");
	}

	/**
	 * Handler for creating a page instance
	 */
	function createPage() {
		
		$position = value("sitepage_POSITION", "NUMERIC");
		$mid = getVar("mid");
		$spid = createSitepage($mid, $position);
		syncSPVariations ($spid);
	}

	/**
	 * Handler for creating a valid contentpage for a site.
	 */
	function createContentPage() {
		global $oid, $db, $variation, $auth;

		$checked = value("createpage");

		if ($checked) {
			$spm = value("sitemap_SPM_ID");

			// determine spm-type
			$type = getDBCell("sitepage_master", "SPMTYPE_ID", "SPM_ID = $spm");
			$clt = getDBCEll("sitepage_master", "CLT_ID", "SPM_ID = $spm");

			if ($type == 1) {
				// create contentpage
				$name = value("sitemap_NAME");

				if ($name == "")
					$name = "Cluster";

				$usename = makeCopyName("cluster_node", "NAME", $name, $filter = "CLT_ID = $clt", "", false);

				$nextId = nextGUID();
				$sql = "INSERT INTO cluster_node (CLNID, CLT_ID, NAME, DELETED) VALUES($nextId, $clt, '$usename', 0)";

				$query = new query($db, $sql);
				$spid = getDBCell("sitepage", "SPID", "MENU_ID = $oid");
				$sql = "UPDATE sitepage SET CLNID = $nextId WHERE SPID = $spid";

				$query = new query($db, $sql);
				$query->free();

				$variations = createDBCArray("sitepage_variations", "VARIATION_ID", "SPM_ID = $spm");

				for ($i = 0; $i < count($variations); $i++) {
					$fk = nextGUID();
					$sql = "INSERT INTO cluster_variations (CLNID, VARIATION_ID, CLID, DELETED, CREATE_USER, CREATED_AT ) VALUES ( $nextId, $variations[$i], $fk, 0, '$auth->userName', Now()+0)";
					$query = new query($db, $sql);
				}

				$query->free();
				global $errors, $sid, $c, $mid;
				if ($errors == "") {
					header ("Location: " . $c["docroot"] . "modules/sitepages/sitepagebrowser.php?sid=$sid&go=update&action=editobject&oid=$spid&mid=$oid");
					exit;
				}				
			}
		}
	}

	/** 
	 * Handler for inserting a sitepage-master into the menu-structure.
	 */
	function createMenuEntry() {
		global $oid, $mid, $db;

		$spm = value("sitemap_SPM_ID");
		$position = value("sitemap_POSITION", "NUMERIC");
		if ($position == "")
			$position = 1;

		if ($position < 1)
			$position = 1;

		// get number of positions...
		$sql = "SELECT COUNT(MENU_ID) AS ANZ FROM sitemap WHERE DELETED=0 AND PARENT_ID = $mid";

		$query = new query($db, $sql);
		$query->getrow();
		$maxpos = $query->field("ANZ");

		// correct positioning...
		if ($position > $maxpos)
			$position = $maxpos + 1;

		$uhandler = new ActionHandler("POSITIONS");
		$uhandler->addDBAction("UPDATE sitemap SET POSITION = (POSITION+1) WHERE PARENT_ID = $mid AND POSITION >= $position");
		$uhandler->addDBAction("UPDATE sitemap SET POSITION = $position WHERE MENU_ID = $oid");
		$uhandler->process("POSITIONS");

		// determine spm-type
		$type = getDBCell("sitepage_master", "SPMTYPE_ID", "SPM_ID = $spm");

		// single instance
		if ($type == 1) {
			$spid = createSitepage($oid, 1);
			syncSPVariations ($spid);
		}
	}

	/**
	 * Synchronize Sitepage-Variations
	 * to allowed variations.
	 * @param integer Sitepage ID, which is to be synchronized.
	 */
	function syncSPVariations($spid) {
		global $db;

		$spm = getDBCell("sitepage", "SPM_ID", "SPID = $spid");

		// set all variations to deleted.
		$sql = "UPDATE sitepage_names SET DELETED=1 WHERE SPID = $spid";

		$query = new query($db, $sql);

		// query allowed variations.
		$sql = "SELECT VARIATION_ID FROM sitepage_variations WHERE SPM_ID = $spm";
		$query = new query($db, $sql);

		while ($query->getrow()) {
			$id = $query->field("VARIATION_ID");

			$sql = "SELECT COUNT(VARIATION_ID) AS ANZ FROM sitepage_names WHERE SPID = $spid AND VARIATION_ID = $id";
			$cquery = new query($db, $sql);
			$cquery->getrow();
			$amount = $cquery->field("ANZ");
			$cquery->free();

			// perform update
			if ($amount > 0) {
				$sql = "UPDATE sitepage_names SET DELETED=0 WHERE SPID = $spid AND VARIATION_ID = $id";
			} else {
				$name=value("sitemap_NAME", "", "");
				$sql = "INSERT INTO sitepage_names (SPID, VARIATION_ID, NAME, HELP, DELETED) VALUES ( $spid,  $id,'$name', '', 0)";
			}

			$dquery = new query($db, $sql);
			$dquery->free();
		}
	}

	/**
	 * Creates a new sitepage.
	 * @param integer ID of the Menu-Entry
	 * @param integer Position, where sitepage is to be inserted.
	 * @return integer SPID, that was created when inserting the sitepage.
	 */
	function createSitepage($menu, $position) {
		global $db;

		$spm = getDBCell("sitemap", "SPM_ID", "MENU_ID = $menu");

		if (!is_numeric($position) || $position == "")
			$position = 1;

		if ($position < 1)
			$position = 1;

		// get number of positions...
		$sql = "SELECT COUNT(SPID) AS ANZ FROM sitepage WHERE DELETED=0 AND MENU_ID = $menu";

		$query = new query($db, $sql);
		$query->getrow();
		$maxpos = $query->field("ANZ");

		// correct positioning...
		if ($position > $maxpos)
			$position = $maxpos + 1;

		$uhandler = new ActionHandler("POSITIONS");
		$uhandler->addDBAction("UPDATE sitepage SET POSITION = (POSITION+1) WHERE MENU_ID = $menu AND POSITION >= $position");
		$uhandler->process("POSITIONS");

		$nextId = nextGUID();
		$sql = "INSERT INTO sitepage (SPID, SPM_ID, MENU_ID, POSITION, DELETED) VALUES ($nextId, $spm, $menu, $position, 0)";

		$query = new query($db, $sql);

		return $nextId;
	}
?>