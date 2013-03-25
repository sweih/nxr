<?php
	/**
	 * @module Paths
	 * @package CMS
	 */

 
	 
	 /**
	   * pathToRoot
	   * @param integer GUID of the sitepage to determine the root of
	   */
	function SPPathToRoot($guid) {
		$res = "";
		$parent = $guid;

		while ($parent != "" && $parent != "0" && $parent != "1") {
			$res = getDBCell("sitemap", "NAME", "MENU_ID = $parent"). "&gt" . $res;
			$parent = parentPage($parent);
		}

		$res = "Website&gt;" . $res;
		return $res;
	}

	/**
	   * Return parent page. Does also support multipage-folder display
	   * @param integer GUID of a sitepage
	   */
	function parentPage($oid) {
		// determine type (multipage or what?)
		if (getDBCell("sitemap", "MENU_ID", "MENU_ID = $oid") != "") {
			return getDBCell("sitemap", "PARENT_ID", "MENU_ID = $oid");
		} else {
			$spm = getDBCell("sitepage", "SPM_ID", "SPID = $oid");

			$type = getDBCell("sitepage_master", "SPMTYPE_ID", "SPM_ID = $spm");

			if ($type == 2) {
				// multipage instance.
				return getDBCell("sitepage", "MENU_ID", "SPID = $oid");
			} else {
				$menu = getDBCell("sitepage", "MENU_ID", "SPID = $oid");

				return getDBCell("sitemap", "PARENT_ID", "MENU_ID = $menu");
			}
		}
	}

	/**
	 * returns a path to the root folder of the system
	 * @param $pnode integer ID of the current folder
	 */
	function pathToRootFolder($pnode, $baseNode="0") {
		global $c, $sid, $lang;

		if ($baseNode == "0") {
			$basehref = '<a href="?sid=' . $sid . '&pnode=';
			$str_base = $basehref . '0">Content &gt;</a> ';
		} else if ($baseNode == "11") {
			$basehref = '<a href="?sid=' . $sid . '&pnode=';
			$str_base = $basehref . '11">'.$lang->get("shop").' &gt;</a> ';			
		}
		
		$str = "";
		$tmp = $pnode;
		while ($tmp != $baseNode && $tmp != "") {
			$str = $basehref . "$tmp\">" . getDBCell("categories", "CATEGORY_NAME", "CATEGORY_ID = $tmp"). "	&gt; </a>&nbsp;&nbsp;" . $str;
			$tmp = getDBCell("categories", "PARENT_CATEGORY_ID", "CATEGORY_ID = $tmp");
		}

		$str = $str_base . $str;
		return $str;
	}
?>