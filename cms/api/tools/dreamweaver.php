<?
/**********************************************************************
 *	N/X - Web Content Management System
 *	Copyright 2002 Fabian Koenig
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
 * @package Tools
 * @subpackage Dreamweaver Plugin
 */

   
   /**
    * Generates special information for a Dreamweaver MX-Extension to faciliate
    * selecting a Content-Field for insertion into a Template during Design
	*
	*/
	function generateDWContentFieldInfo() {
		
		$output = "var nxSettings = new NX_Settings();\n";
		$output .= "with(nxSettings.SPMSet) {\n";
		$output .= buildDWSPMInfo();
		$output .= "} /* End of nxSettings.SPMSet */\n";
		$output .= "with(nxSettings.CLTTree) {\n";
		$output .= buildDWCLTInfo();
		$output .= "} /* End of nxSettings.CLTTree */\n";

      $mime_type = (PMA_USR_BROWSER_AGENT == 'IE' || PMA_USR_BROWSER_AGENT == 'OPERA') ? 'application/octetstream' : 'application/octet-stream';
        
      $tempname = tempnam("/tmp", "NXDW");
		$fp = fopen($tempname, "w");
		fwrite($fp, $output);
		fclose($fp);
		
		$filename = "NXSettings";
        $ext = "js";
		
	    // Send headers
	    header('Content-Type: ' . $mime_type);	    
	    if (PMA_USR_BROWSER_AGENT == 'IE') {
	        header('Content-Disposition: inline; filename="' . $filename . '.' . $ext . '"');
	        header('Expires: 0');
	        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
	        header('Pragma: public');	        
	    } else {
	        header('Content-Disposition: attachment; filename="' . $filename . '.' . $ext . '"');
	        header('Expires: 0');
	        header('Pragma: no-cache');	        
	    }
	    
	    readfile($tempname);
		unlink($tempname);
		exit();
	}
	
	function buildDWSPMInfo() {
		global $db;
		
		$dwstring = "";
		
		$sqlSPMs = "SELECT * FROM sitepage_master WHERE DELETED = 0 AND VERSION = 0 ORDER BY NAME ASC";
		$query_SPMs = new query($db, $sqlSPMs);
		
		while ($query_SPMs->getrow()) {
			$dwstring .= "addMaster(\"".$query_SPMs->field("NAME")."\");\n";
			$dwstring .= "with(masters[masters.length-1]) {\n";
			$sqlContents = "SELECT * FROM cluster_template_items WHERE DELETED = 0 AND VERSION = 0 AND CLT_ID = ".$query_SPMs->field("CLT_ID")." AND MAXCARD <= 1 ORDER BY NAME ASC";
			/* AND (CLTITYPE_ID = 1 OR CLTITYPE_ID = 2 OR CLTITYPE_ID = 5) */
			$query_Contents = new query($db, $sqlContents);
			while ($query_Contents->getrow()) {
				$type = $query_Contents->field("CLTITYPE_ID");
				if ($type == 1 || $type == 2 || $type == 5) {
					$dwstring .= "addContent(\"".$query_Contents->field("NAME")."\");\n";
				} else if ($type == 3) {
					$dwstring .= "addClusterLink(\"".$query_Contents->field("NAME")."\");\n";
					$dwstring .= "with(clusterLinks[clusterLinks.length-1]) {\n";
					$sqlLinkedCluster = "SELECT * FROM cluster_node WHERE DELETED = 0 AND VERSION = 0 AND CLNID = ".$query_Contents->field("FKID")." ORDER BY NAME ASC";
					$query_LinkedCluster = new query($db, $sqlLinkedCluster);
					$query_LinkedCluster->getrow();
					$sqlLinkedContents = "SELECT * FROM cluster_template_items WHERE DELETED = 0 AND VERSION = 0 AND CLT_ID = ".$query_LinkedCluster->field("CLT_ID")." AND MAXCARD <= 1 AND (CLTITYPE_ID = 1 OR CLTITYPE_ID = 2 OR CLTITYPE_ID = 5) ORDER BY NAME";
					$query_LinkedContents = new query($db, $sqlLinkedContents);
					while ($query_LinkedContents->getrow()) {
						$dwstring .= "addContent(\"".$query_LinkedContents->field("NAME")."\");\n";
					}
					$dwstring .= "} /* End ClusterLink */\n";
				}
			}
			$dwstring .= "} /* End Master */\n";
		}
		return $dwstring;
	}
	
	function buildDWCLTInfo($category_id = 0, $buildCounter=0, $array=false) {
		global $db;
		$dwstring = "";
		$output["CLTs"] = array();
		$output["CATEGORY_NAME"] = "Home";
		$output["CATEGORY_ID"] = 0;
		$sqlCategories = "SELECT * FROM categories WHERE DELETED = 0 AND PARENT_CATEGORY_ID = $category_id ORDER BY CATEGORY_NAME ASC";
		$sqlCLTs = "SELECT * FROM cluster_templates WHERE DELETED = 0 AND VERSION = 0 AND CATEGORY_ID = $category_id ORDER BY NAME ASC";
		
		$query_Categories = new query($db, $sqlCategories);
		$query_CLTs = new query($db, $sqlCLTs);
		
		$count = 0;
		while ($query_Categories->getrow()) {
			$category_id = $query_Categories->field("CATEGORY_ID");
			$category_name = $query_Categories->field("CATEGORY_NAME");
			$output["categories"][$count]["CATEGORY_ID"] = $category_id;
			$output["categories"][$count]["CATEGORY_NAME"] = $category_name;
			$dwstring .= "addFolder(\"".$category_name."\");\n";
			$dwstring .= "with(folders[folders.length-1]) {\n";
			if ($array) {
				$temp = buildDWCLTInfo($category_id, $buildCounter, true);
				$output["categories"][$count]["categories"] = $temp["categories"];
				$output["categories"][$count]["CLTs"] = $temp["CLTs"];
			} else {
				$dwstring .= buildDWCLTInfo($category_id, $buildCounter);
			}
			$dwstring .= "} /* addFolder $category_name */ \n";
			$count++;
		}
		
		while ($query_CLTs->getrow()) {
			$CLTIs = Array();
			$sqlCLTIs = "SELECT * FROM cluster_template_items WHERE DELETED = 0 AND VERSION = 0 AND MAXCARD <= 1 AND CLT_ID = ".$query_CLTs->field("CLT_ID")." ORDER BY NAME ASC";
			/* AND (CLTITYPE_ID = 1 OR CLTITYPE_ID = 2 OR CLTITYPE_ID = 5) */
			$query_CLTIs = new query($db, $sqlCLTIs);
			$template_name = $query_CLTs->field("NAME");
			$dwstring .= "addTemplate(\"".$template_name."\");\n";
			$dwstring .= "with(templates[templates.length-1]) {\n";
			while ($query_CLTIs->getrow()) {
				//****
				$type = $query_CLTIs->field("CLTITYPE_ID");
				if ($type == 1 || $type == 2 || $type == 5) {
					$sqlModule = "SELECT * FROM modules WHERE MODULE_ID = ".$query_CLTIs->field("FKID");
					$query_Module = new query($db, $sqlModule);
					$query_Module->getrow();
					$templateItem_name = $query_CLTIs->field("NAME");
					$dwstring .= "addContent(\"".$templateItem_name."\");\n";
					array_push($CLTIs, array("CLTI_ID" => $query_CLTIs->field("CLTI_ID"), "NAME" => $templateItem_name, "MODULE" => $query_Module->field("MODULE_NAME")));
				} else if ($type == 3) {
					$dwstring .= "addClusterLink(\"".$query_CLTIs->field("NAME")."\");\n";
					$dwstring .= "with(clusterLinks[clusterLinks.length-1]) {\n";
					$sqlLinkedCluster = "SELECT * FROM cluster_node WHERE DELETED = 0 AND VERSION = 0 AND CLNID = ".$query_CLTIs->field("FKID")." ORDER BY NAME ASC";
					$query_LinkedCluster = new query($db, $sqlLinkedCluster);
					$query_LinkedCluster->getrow();
					$sqlLinkedFields = "SELECT * FROM cluster_template_items WHERE DELETED = 0 AND VERSION = 0 AND MAXCARD <= 1 AND CLT_ID = ".$query_LinkedCluster->field("CLT_ID")." AND (CLTITYPE_ID = 1 OR CLTITYPE_ID = 2 OR CLTITYPE_ID = 5) ORDER BY NAME";
					$query_LinkedFields = new query($db, $sqlLinkedFields);
					while ($query_LinkedFields->getrow()) {
						$dwstring .= "addContent(\"".$query_LinkedFields->field("NAME")."\");\n";
					}
					$dwstring .= "} /* End ClusterLink */\n";
				}		
				//****
			}
			$dwstring .= "} /* End Template $template_name */\n";
			array_push($output["CLTs"], array("CLT_ID" => $query_CLTs->field("CLT_ID"), "NAME" => $query_CLTs->field("NAME"), "CLTIs" => $CLTIs));

		}
		
		$buildCounter++;
		if ($array) {
			return $output;
		} else {
			return $dwstring;
		}
	}
	
	function myprint($item, $key) {
		if (is_array($item)) {
			echo "<blockquote>{";
			array_walk($item, 'myprint');
			echo "}</blockquote>";
		} else {
			echo $key.": ".$item."--<br>\n";
		}
		$myprefix++;
	}