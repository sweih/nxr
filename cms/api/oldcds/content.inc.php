<?
	/**
	 * Content.
	 * Functions for getting the content out of the database.
	 * @module Content
	 * @package CDS
	 */

	/**
	 * Retrieves the meta-data of an object.
	 * @param 		string	Name of the META-Field to retrieve.
	 * @param		integer	ID of the cluster-Node to get the meta-data from or just 0 for page-cluster.
	 * @param		string	optional. Name of a static content-item to get metas from.
	 * @returns	string 	Content of the META-Template.
	 */
	function getMeta($name, $cclnid = 0, $contentItem = "") {
		global $clnid;

		$result = ""; // save result here later.

		if ($cclnid == 0)
			$cclnid = $clnid;

		if ($contentItem == "") {
			// get mti-id.
			$clt = getDBCell("cluster_node", "CLT_ID", "CLNID = $cclnid");

			$mt = getDBCell("cluster_templates", "MT_ID", "CLT_ID = $clt");
			$mti = getDBCell("meta_template_items", "MTI_ID", "MT_ID = $mt AND UPPER(NAME) = UPPER('$name')");

			if ($mti == "")
				return "";

			$result = getDBCell("meta", "VALUE", "MTI_ID = $mti AND CID = $cclnid");
		} else {
			// get the mti-id
			$clt = getDBCell("cluster_node", "CLT_ID", "CLNID = $cclnid");

			$cid = getDBCell("cluster_template_items", "FKID", "CLT_ID = $clt AND UPPER(NAME) = UPPER('$contentItem') AND CLTITYPE_ID = 1");

			if ($cid == "")
				return "";

			$mt = getDBCell("content", "MT_ID", "CID = $cid");
			$mti = getDBCell("meta_template_items", "MTI_ID", "MT_ID = $mt AND UPPER(NAME) = UPPER('$name')");

			if ($mti == "")
				return "";

			$result = getDBCell("meta", "VALUE", "MTI_ID = $mti AND CID = $cclnid");
		}

		return $result;
	}

	/**
	 * Retrieves the output of a field as defined in Cluster-Template by querying for its title. 
	 * To be used with single-content as well as with content-fields.
	 * @param string name of the field to query the content from.
	 * @param string additional parameters for this plugin.
	 * @param integer ID of the Cluster-Node to query. Leave Blank or set to zero for Page-Cluster. 
	 * @param integer ID of the Variation to query. Leave Blank or set to zero for Page-Variation. 
	 * @returns string The output of the module.
	 */
	function getContentByTitle($title, $param = "", $cclnid = 0, $cvariation = 0) {
		global $clnid, $v, $clid, $c;

		if ($cclnid == 0)
			$cclnid = $clnid;

		if ($cvariation == 0)
			$cvariation = $v;

		$myclid = getCLID($cclnid, $cvariation);

		// get the clti..
		$fkid = getDBCell("cluster_content", "CLCID", "CLID = $myclid AND UPPER(TITLE) = UPPER('$title') AND FKID=0");
		$clti = getDBCell("cluster_content", "CLTI_ID", "CLID = $myclid AND UPPER(TITLE) = UPPER('$title') AND FKID=0");
		$plugin = getDBCell("cluster_template_items", "FKID", "CLTI_ID = $clti AND CLTITYPE_ID < 3");

		if ($fkid != "" && $plugin != "") {
			$ref = createPGNRef($plugin, $fkid);

			$content = $ref->draw($param);
			unset ($ref);
		} else
			return "";

		if ($content != "")
			return $content;

		// now the content seems to be empty. So we try standard variation.
		if ($cvariation != $c["stdvariation"])
			$content = getContentByTitle($title, $param, $cclnid, $c["stdvariation"]);

		return $content;
	}

	/**
	 * Retrieves the output of several fields as defined in Cluster-Template. 
	 * To be used for Items with maximum cardinality of 1 only!!!
	 * Useful for retrieving lots of fields in one call.
	 * @param array names["name"] of the fields to query the contents from.
	 * @param string additional parameters for this plugin.
	 * @param integer ID of the Cluster-Node to query. Leave Blank or set to zero for Page-Cluster. 
	 * @param integer ID of the Variation to query. Leave Blank or set to zero for Page-Variation. 
	 * @returns string The output of the module.
	 */
	function getAssociativeContent($names, $param = "", $cclnid = 0, $cvariation = 0) {
		global $clnid, $v, $clid, $c;

		if ($cclnid == 0)
			$cclnid = $clnid;

		if ($cvariation == 0)
			$cvariation = $v;

		$myclid = getCLID($cclnid, $cvariation);

		for ($assocID = 0; $assocID < count($names); $assocID++) {
			// get the clti..
			$clt = getDBCell("cluster_node", "CLT_ID", "CLNID = $cclnid");

			$clti = getDBCell("cluster_template_items", "CLTI_ID", "CLT_ID = $clt AND UPPER(NAME) = UPPER('" . $names[$assocID] . "')");

			if ($clti == "")
				return "Field not defined!";

			$type = getDBCell("cluster_template_items", "CLTITYPE_ID", "CLTI_ID = $clti");

			if ($type > 2)
				return $names[$assocID] . " is a Cluster, not a Content!";

			if ($type == 1) {        // static content
				$cid = getDBCell("cluster_template_items", "FKID", "CLTI_ID = $clti");

				$plugin = getDBCell("content", "MODULE_ID", "CID = $cid");
				$oid = getDBCell("content_variations", "FK_ID", "CID = $cid AND VARIATION_ID = $cvariation AND DELETED=0");
			} else if ($type == 2) { // dynamic content
				$plugin = getDBCell("cluster_template_items", "FKID", "CLTI_ID = $clti");

				$oid = getDBCell("cluster_content", "CLCID", "CLID = $myclid AND CLTI_ID = $clti AND DELETED=0");
			}

			if ($oid != "" && $plugin != "") {
				$ref = createPGNRef($plugin, $oid);

				$content = $ref->draw($param);
				unset ($ref);
			} else
				$content = "";

			if ($content != "")
				$assocOutput[$names[$assocID]] = $content;

			// now the content seems to be empty. So we try standard variation.
			if ($cvariation != $c["stdvariation"])
				$content = getContent($names[$assocID], $param, $cclnid, $c["stdvariation"]);

			$assocOutput[$names[$assocID]] = $content;
		}

		return $assocOutput;
	}

	/**
	 * Retrieves the output of a field as defined in Cluster-Template. 
	 * To be used for Items with maximum cardinality of 1 only!!!
	 * @param string name of the field to query the content from.
	 * @param string additional parameters for this plugin.
	 * @param integer ID of the Cluster-Node to query. Leave Blank or set to zero for Page-Cluster. 
	 * @param integer ID of the Variation to query. Leave Blank or set to zero for Page-Variation. 
	 * @returns string The output of the module.
	 */
	function getContent($name, $param = "", $cclnid = 0, $cvariation = 0) {
		global $clnid, $v, $clid, $c;

		if ($cclnid == 0)
			$cclnid = $clnid;

		if ($cvariation == 0)
			$cvariation = $v;

		$myclid = getCLID($cclnid, $cvariation);

		// get the clti..
		$clt = getDBCell("cluster_node", "CLT_ID", "CLNID = $cclnid");
		$clti = getDBCell("cluster_template_items", "CLTI_ID", "CLT_ID = $clt AND UPPER(NAME) = UPPER('$name')");

		if ($clti == "")
			return "Field not defined!";

		$type = getDBCell("cluster_template_items", "CLTITYPE_ID", "CLTI_ID = $clti");

		if ($type > 2 && $type < 5)
			return "$name is a Cluster, not a Content!";

		if ($type == 1) {        // static content
			$cid = getDBCell("cluster_template_items", "FKID", "CLTI_ID = $clti");

			$plugin = getDBCell("content", "MODULE_ID", "CID = $cid");
			$oid = getDBCell("content_variations", "FK_ID", "CID = $cid AND VARIATION_ID = $cvariation AND DELETED=0");
		} else if ($type == 2) { // dynamic content
			$plugin = getDBCell("cluster_template_items", "FKID", "CLTI_ID = $clti");

			$oid = getDBCell("cluster_content", "CLCID", "CLID = $myclid AND CLTI_ID = $clti AND DELETED=0");
		} else if ($type == 5) {
			$plugin = getDBCell("cluster_template_items", "FKID", "CLTI_ID = $clti");

			$cid = getDBCell("cluster_content", "FKID", "CLID = $myclid AND CLTI_ID = $clti AND DELETED=0");
			$oid = getDBCell("content_variations", "FK_ID", "CID = $cid AND VARIATION_ID = $cvariation AND DELETED=0");
		}

		if ($oid != "" && $plugin != "") {
			$ref = createPGNRef($plugin, $oid);

			$content = $ref->draw($param);
			unset ($ref);
		} else
			$content = "";

		if ($content != "")
			return $content;

		// now the content seems to be empty. So we try standard variation.
		if ($cvariation != $c["stdvariation"])
			$content = getContent($name, $param, $cclnid, $c["stdvariation"]);

		return $content;
	}

	/**
	 * Retrieves the output of a field as defined in Cluster-Template. 
	 * To be used for Items with every cardinality. Returns an array with the results. 
	 * @param string name of the field to query the content from.
	 * @param string additional parameters for this plugin.
	 * @param string Column, you want to order the output of.
	 * @param integer ID of the Cluster-Node to query. Leave Blank or set to zero for Page-Cluster. 
	 * @param integer ID of the Variation to query. Leave Blank or set to zero for Page-Variation. 
	 * @returns string The output of the module.
	 */
	function getContentField($name, $param = "", $order = "POSITION ASC", $cclnid = 0, $cvariation = 0) {
		global $clnid, $v, $clid, $c;

		if ($cclnid == 0)
			$cclnid = $clnid;

		if ($cvariation == 0)
			$cvariation = $v;

		$myclid = getCLID($cclnid, $cvariation);
		// determine, if static or dynamic content.
		$clt = getDBCell("cluster_node", "CLT_ID", "CLNID = $cclnid");
		$clti = getDBCell("cluster_template_items", "CLTI_ID", "CLT_ID = $clt AND UPPER(NAME) = UPPER('$name')");

		if ($clti == "") {
			$res[0] = "$name is not defined!";

			return $res;
		}

		$type = getDBCell("cluster_template_items", "CLTITYPE_ID", "CLTI_ID = $clti");
		$res = array ();

		if ($type == 1)
			$res[0] = "$name is a static content and therefore not a field!";

		if ($type == 2) { // dynamic field
			$plugin = getDBCell("cluster_template_items", "FKID", "CLTI_ID = $clti");

			$field = createDBCArray("cluster_content", "CLCID", "CLTI_ID = $clti AND CLID = $myclid ORDER BY $order");

			if (count($field) == 0 && $cvariation != $c["stdvariation"])
				$res = getContentField($name, $param, $order, $cclnid, $c["stdvariation"]);

			for ($i = 0; $i < count($field); $i++) {
				if ($field[$i] != "" && $plugin != "") {
					$ref = createPGNRef($plugin, $field[$i]);

					$content = $ref->draw($param);
					unset ($ref);
					array_push($res, $content);
				}
			}
		}

		if ($type > 2)
			$res[0] = "$name is not a content-field!";

		return $res;
	}

	/**
	 * Retrieves the output of a dynamic field as defined in Cluster-Template. 
	 * To be used for Items with maximum cardinality of 1 only. Faster than getContent
	 * @author Tim Haedke
	 * @param string name of the field to query the content from.
	 * @param string additional parameters for this plugin.
	 * @param integer ID of the Cluster-Node to query. Leave Blank or set to zero for Page-Cluster. 
	 * @param integer ID of the Variation to query. Leave Blank or set to zero for Page-Variation. 
	 * @returns string The output of the module.
	 */
	function getDynamicContent($name, $param = "", $cclnid = 0, $cvariation = 0) {
		global $clnid, $v, $clid, $c, $db, $contentCounter;

		if ($cclnid == 0)
			$cclnid = $clnid;

		if ($cvariation == 0)
			$cvariation = $v;

		$myclid = getCLID($cclnid, $cvariation);

		$contentCounter['dynamic']++;

		$sql = "SELECT cn.CLT_ID clt, cti.CLTI_ID clti, cti.FKID plugin, cc.CLCID oid FROM cluster_node cn, cluster_template_items cti, cluster_content cc WHERE cn.CLNID = $cclnid AND cti.CLT_ID = cn.CLT_ID AND UPPER(cti.NAME) = UPPER('$name') AND cc.CLID = $myclid AND cc.CLTI_ID = cti.CLTI_ID;";
		$query = new query($db, $sql);

		while ($query->getrow()) {
			$clt = $query->field("clt");

			$clti = $query->field("clti");
			$plugin = $query->field("plugin");
			$oid = $query->field("oid");
		}

		$query->free();

		if ($oid != "" && $plugin != "") {
			$ref = createPGNRef($plugin, $oid);

			$content = $ref->draw($param);
			unset ($ref);
		} else
			$content = "";

		if ($content != "")
			return $content;

		// now the content seems to be empty. So we try standard variation.
		if ($cvariation != $c["stdvariation"])
			$content = getDynamicContent($name, $param, $cclnid, $c["stdvariation"]);

		return $content;
	}

	/**
	 * Retrieves the output of static field as defined in Cluster-Template. 
	 * To be used for Items with maximum cardinality of 1 only. Faster than getContent
	 * @author Tim Haedke
	 * @param string name of the field to query the content from.
	 * @param string additional parameters for this plugin.
	 * @param integer ID of the Cluster-Node to query. Leave Blank or set to zero for Page-Cluster. 
	 * @param integer ID of the Variation to query. Leave Blank or set to zero for Page-Variation. 
	 * @returns string The output of the module.
	 */
	function getStaticContent($name, $param = "", $cclnid = 0, $cvariation = 0) {
		global $clnid, $v, $clid, $c, $db, $contentCounter;

		if ($cclnid == 0)
			$cclnid = $clnid;

		if ($cvariation == 0)
			$cvariation = $v;

		$myclid = getCLID($cclnid, $cvariation);

		$contentCounter['static']++;

		$sql = "SELECT cn.CLT_ID clt, cti.CLTI_ID clti, cti.FKID cid, c.MODULE_ID plugin, cv.FK_ID oid, cti.NAME FROM cluster_node cn, cluster_template_items cti, content c, content_variations cv WHERE cn.CLNID = $cclnid AND cti.CLT_ID = cn.CLT_ID AND UPPER(cti.NAME) = UPPER('$name') AND c.CID = cti.FKID AND cv.CID = cti.FKID AND cv.VARIATION_ID = $cvariation AND cv.DELETED=0;";
		$query = new query($db, $sql);

		while ($query->getrow()) {
			$clt = $query->field("clt");

			$clti = $query->field("clti");
			$clti = $query->field("cid");
			$plugin = $query->field("plugin");
			$oid = $query->field("oid");
		}

		$query->free();

		if ($oid != "" && $plugin != "") {
			$ref = createPGNRef($plugin, $oid);

			$content = $ref->draw($param);
			unset ($ref);
		} else
			$content = "";

		if ($content != "")
			return $content;

		// now the content seems to be empty. So we try standard variation.
		if ($cvariation != $c["stdvariation"])
			$content = getStaticContent($name, $param, $cclnid, $c["stdvariation"]);

		return $content;
	}
?>