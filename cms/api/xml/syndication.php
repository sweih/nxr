<?php
 /**
  * XML Syndication
  * @package XML
  * @subpackage sydication
  */
  
/**********************************************************************
 *	N/X - Web Content Management System
 *	Copyright 2004 Sven Weih
 *
 *      $Id: syndication.php,v 1.9 2004/09/21 10:45:37 sven_weih Exp $ *
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
  * Create a XML-File for Syndication
  * Automatically determines, if GUID is a Cluster-Node, a Cluster, a Template or a Meta-Template
  * @param integer GUID of the resource to syndicate
  * @param string Description of this resource
  */ 
 function XmlExportSyndication($guid, $description="") {
 	global $db, $xmlExchange, $c;
   
   $xmlOptions = array(XML_OPTION_CASE_FOLDING => TRUE, XML_OPTION_SKIP_WHITE => TRUE);
   $xml =& new XPath(FALSE, $xmlOptions);	
   $xml->appendChild('', '<NX:SYNDICATION VERSION="1.0" PROVIDER="'.$c["provider"].'"/>');
 	
 	$childs = array();
 	$childs[] = '<NX:INFO WWW="www.nxsystems.org" MAIL="info@nxsystems.org"/>';
 	$childs[] = '<NX:DESCRIPTION>'.urlencode($description).'</NX:DESCRIPTION>';
 	
 	
 	if (! syndicated($guid)) {
 		$type = getTypeByGUID($guid);
 		$childs[] = '<NX:TYPE TYPE="'.$type.'"/>';
 		if ($type=="PAGETEMPLATE") {
 		  $childs[] = XmlExportSitepageMaster($guid);
 		} else if ($type=="CLUSTERNODE") {
 		  $childs[] = XmlExportClusterNode($guid);
 		}  else if ($type=="METATEMPLATE") {
 		  $childs[] = XmlExportMetaTemplate($guid);
 		} else if ($type=="PAGETEMPLATE") {
 		  $childs[] = XmlExportClusterNode($guid);
 		} else if ($type=="CLUSTERTEMPLATE") {
 		  $childs[] = XmlExportClusterTemplate($guid);
 		}
   }
   
   while (count($xmlExchange)>1) {     
     $handle = array_pop($xmlExchange);
     if (array_key_exists("clt", $handle)) {
       if (! syndicated($handle["clt"])) $childs[] = XmlExportClusterTemplate($handle["clt"]);	
     }
     if (array_key_exists("mt", $handle)) {
       if (! syndicated($handle["mt"])) $childs[] = XmlExportMetaTemplate($handle["mt"]);		
     }
   }
   
   // export Plugins
   $plugins = array_values($xmlExchange["PLUGINS"]);
   for ($i=0; $i < count($plugins); $i++) {
     $childs[] = '<NX:REQUIRE PLUGIN="'.urlencode($plugins[$i]).'"/>';	
   }
   
 	for ($j=count($childs)-1; $j>-1; $j--) {
 	  $xml->appendChild('/NX:SYNDICATION[1]', $childs[$j]);	
 	}
 	return $xml->exportAsXml();
 }

 /**
  * Import a NX-Syndication-XML-File
  * @param string XML for syndication
  */
 function XmlImportSyndication($xmlString) {
   global $provider;
   $xmlOptions = array(XML_OPTION_CASE_FOLDING => TRUE, XML_OPTION_SKIP_WHITE => TRUE);
   $xml =& new XPath(FALSE, $xmlOptions);	 
   $xml->importFromString($xmlString);
   if ($xml->hasChildNodes('/NX:SYNDICATION[1]')) {
     $provider = $xml->getAttributes('/NX:SYNDICATION[1]', 'PROVIDER');
     $version = $xml->getAttributes('/NX:SYNDICATION[1]', 'VERSION');
     // import meta-templates

     $i=1;  
     while ($xml->getNode("/NX:SYNDICATION[1]/NX:METATEMPLATE[$i]")) {
       if (! syndicateImported ($xml->getAttributes("/NX:SYNDICATION[1]/NX:METATEMPLATE[$i]", "ID")))
         XmlImportMetaTemplate($xml->exportAsXml("/NX:SYNDICATION[1]/NX:METATEMPLATE[$i]", ''));
       $i++;
     }
     
     // import cluster-templates
      $i=1;
     while ($xml->getNode("/NX:SYNDICATION[1]/NX:CLUSTERTEMPLATE[$i]")) {
       if (! syndicateImported ($xml->getAttributes("/NX:SYNDICATION[1]/NX:CLUSTERTEMPLATE[$i]", "ID")))
         XmlImportClusterTemplate($xml->exportAsXml("/NX:SYNDICATION[1]/NX:CLUSTERTEMPLATE[$i]", ''));
       $i++;
     }
     
     // import sitepage-master
      $i=1;
     while ($xml->getNode("/NX:SYNDICATION[1]/NX:SITEPAGEMASTER[$i]")) {
       if (! syndicateImported ($xml->getAttributes("/NX:SYNDICATION[1]/NX:SITEPAGEMASTER[$i]", "ID")))
         XmlImportSitepageMaster($xml->exportAsXml("/NX:SYNDICATION[1]/NX:SITEPAGEMASTER[$i]", ''));
       $i++;
     }
     
     // import cluster-nodes
      $i=1;
     while ($xml->getNode("/NX:SYNDICATION[1]/NX:CLUSTERNODE[$i]")) {
       if (! syndicateImported ($xml->getAttributes("/NX:SYNDICATION[1]/NX:CLUSTERNODE[$i]", "ID")))
         XmlImportClusterNode($xml->exportAsXml("/NX:SYNDICATION[1]/NX:CLUSTERNODE[$i]", ''));
       $i++;
     }
   }
 }
 
 /**
  * Checks a XML-Syndication file and returns status values
  * @param string XML of Syndication file
  * @param mixed array of result values
  */
 function XmlGetImportInfo($xmlString) {
 	global $lang;
 	$result = null;
 	$xmlOptions = array(XML_OPTION_CASE_FOLDING => TRUE, XML_OPTION_SKIP_WHITE => TRUE);
   $xml =& new XPath(FALSE, $xmlOptions);	 
   $xml->importFromString($xmlString);
   if ($xml->hasChildNodes('/NX:SYNDICATION[1]')) {
   	$result["provider"] = urldecode($xml->getAttributes('/NX:SYNDICATION[1]', 'PROVIDER'));
     $result["description"] = urldecode($xml->getData('/NX:SYNDICATION[1]/NX:DESCRIPTION[1]'));
     $result["type"] = urldecode($xml->getAttributes('/NX:SYNDICATION[1]/NX:TYPE[1]', 'TYPE'));
     
     $i=1;
     while ($xml->getNode("/NX:SYNDICATION[1]/NX:REQUIRE[$i]")) {
     	 $pgn = $xml->getAttributes("/NX:SYNDICATION[1]/NX:REQUIRE[$i]", "PLUGIN");
     	 if (getDBCell("modules", "MODULE_ID", "UPPER(MODULE_NAME) = '$pgn'") == "") {
     	   $result["errors"][] = $lang->get("missing_pgn", "Missing Plugin: ").$pgn;	
     	 }
     	 $i++;
     }     
     return $result;
   }
   return false;
 }
 
 /**
  * Checks, whether the resource with given GUID has already been syndicated.
  * @param integer GUID to syndicate
  */
 function syndicated($guid) {
 	global $syndACL;
 	if (in_array($guid, $syndACL)) return true;
 	array_push($syndACL, $guid);
  	return false;
 }
 
 /**
  * Checks, whether a resource has once been imported or not.
  * @param integer GUID of the Resource to import
  */
 function syndicateImported($guid) {
 	global $imported;
 	resetDBCache();
 	$trans = translateXmlGUID($guid);
 	if (getTypeByGUID($trans) != "") {
 		return true;
 	}
 	$imported++;
 	return false;
 }
 
 /**
  * Reset Syndicated-Flag for doing multiple syndications
  */
 function resetSyndication() {
   global $syndACL, $xmlExchange;
   $syndACL = array();
   $xmlExchange["PLUGINS"] = array();	
 }
 
 ?>