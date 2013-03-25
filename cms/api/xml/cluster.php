<?php

/**********************************************************************
 *	N/X - Web Content Management System
 *	Copyright 2004 Sven Weih
 *
 *      $Id: cluster.php,v 1.4 2004/01/10 14:53:22 sven_weih Exp $ *
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
   * Import ClusterNode from XML
   * @param string XMLString
   */
   function XmlImportClusterNode($xmlString) {
     global $db;
     $xmlOptions = array(XML_OPTION_CASE_FOLDING => TRUE, XML_OPTION_SKIP_WHITE => TRUE);
     $xml =& new XPath(FALSE, $xmlOptions);
     $xml->importFromString($xmlString);	
     
     $id = translateXmlGUID($xml->getAttributes('/NX:CLUSTERNODE[1]', 'ID'));
     $name = urldecode($xml->getAttributes('/NX:CLUSTERNODE[1]', 'NAME'));
     $clt = translateXmlGUID($xml->getAttributes('/NX:CLUSTERNODE[1]', 'CLUSTERTEMPLATE'));     

     createClusterNode($name, $clt, $id);

	  if ($xml->hasChildNodes('/NX:CLUSTERNODE[1]')) { 
       $i=1;
       while ($xml->getNode("/NX:CLUSTERNODE[1]/NX:CLUSTER[$i]")) {                  
         $child = & new XPath(FALSE, $xmlOptions);
     		XmlImportCluster($xml->exportAsXml('/NX:CLUSTERNODE[1]/NX:CLUSTER['.$i.']', ''), $id, $clt); 		
     		$i++;
     	 }
     }     
   }
   
   /**
    * Import a cluster-variation form generated XML
    * @param string XML Input
    * @param integer GUID of the Cluster-Node
    * @param integer GUID of the CLuster-TEmpalte
    */
   function XmlImportCluster($xmlString, $clnid, $clt) {
     global $db;
     $xmlOptions = array(XML_OPTION_CASE_FOLDING => TRUE, XML_OPTION_SKIP_WHITE => TRUE);
     $xml =& new XPath(FALSE, $xmlOptions);
     $xml->importFromString($xmlString);	

     $variationId = getDBCell("variations", "VARIATION_ID", "UPPER(NAME) = '".strtoupper(urldecode($xml->getAttributes('/NX:CLUSTER[1]', 'VARIATION')))."'");    	
     if ($variationId != "") {
       $clid = createCluster($clnid, $variationId, "XMLAPI");	
       if ($xml->hasChildNodes('/NX:CLUSTER[1]')) { 
         $i=1;
         while ($xml->getNode("/NX:CLUSTER[1]/NX:CLUSTERITEM[$i]")) {                  
           $child = & new XPath(FALSE, $xmlOptions);
    		  XmlImportClusterItem($xml->exportAsXml('/NX:CLUSTER[1]/NX:CLUSTERITEM['.$i.']', ''), $clid, $clt); 		
 
     		 $i++;
     	  }
       }     
     }
   }
  
  
  /**
   * Import the content to the cluster
   * @param string XML String
   * @param integer Cluster-ID which will be used for import
   * @param integer Cluter-Template GUID which will be used for import
   */  
  function XmlImportClusterItem($xmlString, $clid, $clt) {
    global $db;
    $xmlOptions = array(XML_OPTION_CASE_FOLDING => TRUE, XML_OPTION_SKIP_WHITE => TRUE);
    $xml =& new XPath(FALSE, $xmlOptions);
    $xml->importFromString($xmlString);	
    
    $clti = getDBCell("cluster_template_items", "CLTI_ID", "UPPER(NAME) = '".strtoupper(urldecode($xml->getAttributes('/NX:CLUSTERITEM[1]', "NAME")))."'");
  	 $position = $xml->getAttributes('/NX:CLUSTERITEM[1]', "POSITION");
  	 $type = getDBCell("cluster_template_items", "CLTITYPE_ID", "CLTI_ID = ".$clti);
  	 
  	 if ($clti != "") {
  	   $fkid = 'NULL';
  	   if ($type == 2) {
  	 	  if ($xml->hasChildNodes('/NX:CLUSTERITEM[1]')) {
  	       $fkid = getDBCell("cluster_content", "CLCID", "CLID = $clid AND CLTI_ID = $clti AND POSITION = $position");
  	       XmlImportPlugin($xml->exportAsXml('/NX:CLUSTERITEM[1]/NX:CONTENT[1]', ''), $fkid);
  	       $fkid=0;
  	     }
  	   } else if ($type == 4) {
  	     $fkid = translateXmlGUID($xml->getAttributes('/NX:CLUSTERITEM[1]/NX:REFERENCE[1]', 'ID'));	
  	   }
  	   if ($fkid == false) $fkid = 'NULL';
  	   return updateClusterItem($clid, $clti, $position, $fkid);
  	 }
  }
  
  /**
   * Export a cluster-Node with all its variations
   * @param integer GUID of the Cluster-Node (CLNID)
   */
  function XmlExportClusterNode($clnid) {
  	 global $db, $xmlExchange;
    
    $xmlOptions = array(XML_OPTION_CASE_FOLDING => TRUE, XML_OPTION_SKIP_WHITE => TRUE);
    $xml =& new XPath(FALSE, $xmlOptions);
    
    $sql = "SELECT * FROM cluster_node WHERE CLNID = $clnid";
    $query = new query($db, $sql);
    if ($query->getrow()) {
    	$name = urlencode($query->field("NAME"));
    	$clt = $query->field("CLT_ID");
    	$xmlExchange[] = array("clt" => $clt);	   	
    	$xml->appendChild('', "<NX:CLUSTERNODE ID=\"$clnid\" NAME=\"$name\" CLUSTERTEMPLATE=\"$clt\"/>");
    	
    	$variations = createDBCArray("cluster_variations", "CLID", "CLNID = $clnid");
    	for ($i=0; $i < count($variations); $i++) {
    	  $cluster = XmlExportCluster($variations[$i]);
    	  if ($cluster != false) {
    	  	  $xml->appendChild('/NX:CLUSTERNODE[1]', $cluster);
    	  }	
    	}    	
    	return $xml->exportAsXml('','');
   }  
   return false;
  }
  
  
  /**
   * Export a cluster variation with all exportable contents
   * @param integer GUID (CLID) of the cluster to export
   */
   function XmlExportCluster($clid) {
     global $db, $xmlExchange;
     $xmlOptions = array(XML_OPTION_CASE_FOLDING => TRUE, XML_OPTION_SKIP_WHITE => TRUE);
     $xml =& new XPath(FALSE, $xmlOptions);
    
     $variation = getDBCell("variations", "NAME", "VARIATION_ID = ".getDBCell("cluster_variations", "VARIATION_ID", "CLID=$clid"));
     $xml->appendChild('', '<NX:CLUSTER VARIATION="'.urlencode($variation).'"/>');
     
     $sql = "SELECT * FROM CLUSTER_CONTENT WHERE CLID = $clid";
     $query = new query($db, $sql);
     $i=0;
     while ($query->getrow()) {
     	 $i++;
     	 $position = $query->field("POSITION");
     	 $fkid = $query->field("FKID");
     	 $clti = $query->field("CLTI_ID");
     	 $clcid = $query->field("CLCID");
     	 $figureName = urlencode(strtoupper(getDBCell("cluster_template_items", "NAME", "CLTI_ID = $clti")));    	 
     	 $type = getDBCell("cluster_template_items", "CLTITYPE_ID", "CLTI_ID = $clti");
     	 $fkid = getDBCell("cluster_template_items", "FKID", "CLTI_ID = $clti");
     	 $xml->appendChild('/NX:CLUSTER[1]', "<NX:CLUSTERITEM NAME=\"$figureName\" POSITION = \"$position\"/>");
     	 if ($type == 2) {
     	   $payload = XmlExportPlugin($fkid, $clcid);	
     	   if ($payload != false) 
     	     $xml->appendChild('NX:CLUSTER[1]/NX:CLUSTERITEM['.$i.']', $payload);
     	 } else if ($type == 4) {
     	     $xml->appendChild('NX:CLUSTER[1]/NX:CLUSTERITEM['.$i.']', '<NX:REFERENCE ID="'.$fkid.'"/>');
     	     $xmlEchange[] = array("cln" => $fkid);
     	 }   	 
     }     
     return $xml->exportAsXml('', '');
   }
 
 ?>