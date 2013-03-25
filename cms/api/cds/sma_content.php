<?
	/**********************************************************************
	 *	N/X - Web Content Management System
	 *	Copyright 2003 Sven Weih
	 *
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
	
	 /**
	  * CDS-Class for accessing contents
	  */
	 class SMA_Content extends Content {
		
	 	/**
	 	 * Standard constructor. Interally used.
	 	 */
	 	function SMA_Content(&$parent) {
			Content::Content($parent);	
		}
		
				/**
		 * Retrieves the output of a field as defined in Cluster-Template. 
		 * To be used for Items with maximum cardinality of 1 only!!!
		 * @param string name of the field to query the content from.
		 * @param string additional parameters for this plugin. // might be changed to array in future versions
		 * @param integer ID of the Variation to query. Leave Blank or set to zero for Page-Variation. 
		 * @returns string The output of the module.
		 */
		function get($name, $params = null, $variation = 0) {
			global $sid, $lang, $c;			
			
			$linkadd = "";
			if ($variation)
				$this->variation = $variation;
			// get the clti..
			$clt = getDBCell("cluster_node", "CLT_ID", "CLNID = $this->pageClusterNodeId");
			$clti = getDBCell("cluster_template_items", "CLTI_ID", "CLT_ID = $clt AND UPPER(NAME) = UPPER('$name')");

			if ($clti == "")
				return "Field not defined!";

			$type = getDBCell("cluster_template_items", "CLTITYPE_ID", "CLTI_ID = $clti");

			if ($type > 2 && $type < 5)
				return "$name is a Cluster, not a Content!";

			if ($type == 1) {        // static content
				$cid = getDBCell("cluster_template_items", "FKID", "CLTI_ID = $clti");

				$plugin = getDBCell("content", "MODULE_ID", "CID = $cid");
				$oid = getDBCell("content_variations", "FK_ID", "CID = $cid AND VARIATION_ID = $this->variation AND DELETED=0");
			} else if ($type == 2) { // dynamic content
				$plugin = getDBCell("cluster_template_items", "FKID", "CLTI_ID = $clti");
				$oid = getDBCell("cluster_content", "CLCID", "CLID = $this->pageClusterId AND CLTI_ID = $clti AND DELETED=0");
				$linkadd = "<a href=\"#\" onClick=\"window.open('" . $c["docroot"] . "modules/sitepages/sma_editor.php?sid=$sid&oid=$oid', 'sma', 'top=100,width=650,height=380,toolbar=no,menubar=no,status=no,location=no,dependent=yes,scrollbars=yes');\"><img src=\"" . $c["docroot"] . "img/icons/sma_edit.gif\" alt=\"" . $lang->get("sma_ext_edit", "Open edit window. Save all inline edited texts before!"). "\" width=\"16\" height=\"16\" border=\"0\"></a>";
			} else if ($type == 5) {
				$plugin = getDBCell("cluster_template_items", "FKID", "CLTI_ID = $clti");
				$cid = getDBCell("cluster_content", "FKID", "CLID = $this->pageClusterId AND CLTI_ID = $clti AND DELETED=0");
				$oid = getDBCell("content_variations", "FK_ID", "CID = $cid AND VARIATION_ID = $this->variation AND DELETED=0");			
			}

			if ($oid != "" && $plugin != "") {
				$ref = createPGNRef($plugin, $oid, $clti);

				$content = $ref->drawLiveAuthoring($params);
				if ($param == "")
					$content .= $linkadd;
				unset ($ref);
			} else
				$content = "";

			if ($content != "")
				return $content;

			// now the content seems to be empty. So we try standard variation.
			if ($this->variation != $this->parent->stdVariation)
				$content = $this->get($name, $params, $this->parent->stdVariation);

			return $content;
		}
		
		/**
		 * Retrieves the output of a field as defined in Cluster-Template. 
		 * To be used for Items with every cardinality. Returns an array with the results. 
		 * @param string name of the field to query the content from.
		 * @param string additional parameters for this plugin. 
		 * @param integer ID of the Variation to query. Leave Blank or set to zero for Page-Variation. 
		 * @param string Column, you want to order the output of.
		 * @returns string The output of the module.
		 */
		function getField($name, $params = null, $variation = 0, $order = "POSITION ASC") {
			global $sid, $lang, $c;			
			$linkadd = "";	
			
			if ($variation == 0)
				$variation = $this->variation;

			// $myclid = getCLID($this->pageClusterNodeId, $this->variation);
			// determine, if static or dynamic content.
			$clt = getDBCell("cluster_node", "CLT_ID", "CLNID = $this->pageClusterNodeId");
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

				$field = createDBCArray("cluster_content", "CLCID", "CLTI_ID = $clti AND CLID = $this->pageClusterId ORDER BY $order");

				if (count($field) == 0 && $this->variation != $this->parent->stdVariation)
					$res = $this->getField($name, $params, $order, $this->parent->stdVariation);

				for ($i = 0; $i < count($field); $i++) {
					if ($field[$i] != "" && $plugin != "") {
						$linkadd = "<a href=\"#\" onClick=\"window.open('" . $c["docroot"] . "modules/sitepages/sma_editor.php?sid=$sid&oid=" . $field[$i] . "', 'sma', 'top=100,width=650,height=380,toolbar=no,menubar=no,status=no,location=no,dependent=yes,scrollbars=yes');\"><img src=\"" . $c["docroot"] . "img/icons/sma_edit.gif\" alt=\"" . $lang->get("sma_ext_edit", "Open edit window. Save all inline edited texts before!"). "\" width=\"16\" height=\"16\" border=\"0\"></a>";
						$ref = createPGNRef($plugin, $field[$i]);
						$content = $ref->drawLiveAuthoring($param);
						if ($param == "")
							$content .= $linkadd;
						unset ($ref);
						array_push($res, $content);
					}
				}
			}  else if ($type == 5) {
				$plugin = getDBCell("cluster_template_items", "FKID", "CLTI_ID = $clti");

				$field = createDBCArray("cluster_content", "FKID", "CLTI_ID = $clti AND CLID = $this->pageClusterId ORDER BY $order");

				if (count($field) == 0 && $this->variation != $this->parent->stdVariation)
					$res = $this->getField($name, $params, $order, $this->parent->stdVariation);

				for ($i = 0; $i < count($field); $i++) {
					if ($field[$i] != "" && $plugin != "") {
						
						$oid = getDBCell("content_variations", "FK_ID", "CID = ".$field[$i]." AND VARIATION_ID = $this->variation AND DELETED=0");
						$ref = createPGNRef($plugin, $oid);

						$content = $ref->drawLiveAuthoring($params);
						unset ($ref);
						array_push($res, $content);
					}	
				}		
			
			} else {
				$res[0] = "$name is not a content-field!";
			}
			
			return $res;
		}
	 }
?>