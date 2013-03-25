<?
	/**
	 * @module Mass Operations
	 * @package Management
	 */

	#require_once $c["path"]."common/cds/menu.inc.php";

	/**
	 * Relaunches all pages having the same spm.
	 * Launches only pages, which are already launched.
	 *
	 * @param integer ID of the sitepage-master
	 */
	function relaunchPagesBySPM($spm) {
		global $c;

		ini_set("max_execution_time", $c["timeout"]);
		$launchArray = createDBCArray("sitepage", "SPID", "SPM_ID = $spm AND DELETED=0 AND CLNID IS NOT NULL AND CLNID<>0");
		$variations = createDBCArray("variations", "VARIATION_ID", "DELETED=0");
		includePGNSources();

		for ($i = 0; $i < count($launchArray); $i++) {
			for ($j = 0; $j < count($variations); $j++) {
				if (SPVarExists($launchArray[$i], $variations[$j])) {
					if (isSPVarLive($launchArray[$i], $variations[$j])) {
						launchSitepage($launchArray[$i], 10, $variations[$j]);
					}
				}
			}
		}
	}

	/**
	 * Relaunches a complete Menutree
	 * Launches only pages, which are already launched.
	 *
	 * @param integer ID of the start-node from where to launch.
	 * @param integer ID of the Variation you want to launch
	 */
	function relaunchMenuTree($menuId, $variation) {
		global $c;

		ini_set("max_execution_time", $c["timeout"]);
		$launchArray = getPageTree($menuId);

		for ($i = 0; $i < count($launchArray); $i++) {
			if (isSPVarLive($launchArray[$i], $variation)) {
				launchSitepage($launchArray[$i], 10, $variation);
			}
		}
	}

	
	/**
	 * launches the whole website!
	 */
	function launchWholeSite() {
	  $variations = createDBCArray("variations", "VARIATION_ID", "1");
	  foreach ($variations as $variation) {
	  	launchMenuTree(0, $variation);
	  }
	}
	
	/**
	 * Launches a complete Menutree
	 *
	 * @param integer ID of the start-node from where to launch.
	 * @param integer ID of the Variation you want to launch
	 */
	function launchMenuTree($menuId, $variation) {
		global $c;

		ini_set("max_execution_time", $c["timeout"]);
		$launchArray = getPageTree($menuId);

		for ($i = 0; $i < count($launchArray); $i++) {
			launchSitepage($launchArray[$i], 10, $variation);
		}
	}

	/**
	   * Expires a complete Menutree
	   *
	   * @param integer ID of the start-node from where to expire
	   * @param integer ID of the Variation you want to launch
	   */
	function expireMenuTree($menuId, $variation) {
		global $c;

		ini_set("max_execution_time", $c["timeout"]);
		$launchArray = getPageTree($menuId);

		for ($i = 0; $i < count($launchArray); $i++) {
			expireSitepage($launchArray[$i], 10, $variation);
		}
	}

	/**
	   * Builds an array, with all pages having one certain page as parent-node. Recursive down the tree
	   *
	   * @param $start integer MENU_ID of the page where to start
	   */
	function getPageTree($start) {
		global $db;

		$multipages = createDBCArray("sitepage", "SPID", "MENU_ID=$start AND DELETED=0");
		$menuPages = createDBCArray("sitemap", "MENU_ID", "PARENT_ID = $start AND DELETED=0");
		$result = $multipages;

		if (! is_array($result)) $result = array();
		for ($i = 0; $i < count($menuPages); $i++) {
			$result = array_merge($result, getPageTree($menuPages[$i]));
		}

		if (is_array($result))
			$result = array_unique($result);

		return $result;
	}
?>