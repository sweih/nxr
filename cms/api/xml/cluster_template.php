<?php

/**********************************************************************
 *	N/X - Web Content Management System
 *	Copyright 2004 Sven Weih
 *
 *      $Id: cluster_template.php,v 1.3 2003/12/28 23:59:29 sven_weih Exp $ *
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
  * Return the XML-Code for a Cluster-Template
  * @param integer ID of the Cluster-Template
  * @param mixed data to export also as array. [][type] = "meta", [][guid] = .....
  */
 function XmlExportClusterTemplate($clt) {
   global $db, $xmlExchange;
   
   $xmlOptions = array(XML_OPTION_CASE_FOLDING => TRUE, XML_OPTION_SKIP_WHITE => TRUE);
   $xml =& new XPath(FALSE, $xmlOptions);
   
   $sql = "SELECT * FROM cluster_templates WHERE CLT_ID = $clt";
   $query = new query($db, $sql);
   if ($query->getrow()) {
     $name = urlencode($query->field("NAME"));
     $description = urlencode($query->field("DESCRIPTION"));
     $layout = urlencode($query->field("TEMPLATE"));
     $xmlExchange[] = array("mt" => $query->field("MT_ID"));
     $xml->appendChild('', "<nx:clustertemplate id=\"$clt\" name=\"$name\" description=\"$description\" metaTemplate=\"".$query->field("MT_ID")."\" />");     
     $xml->appendChild('/nx:clustertemplate[@id="'.$clt.'"]', "<nx:layout>$layout</nx:layout>"); 
     $query->free();
   }
  
   $requiredPlugins = array();
   $sql = "SELECT * FROM cluster_template_items WHERE CLT_ID = $clt";
   $query = new query($db, $sql);
   while ($query->getrow()) {
     $name = urlencode($query->field("NAME"));
     $position = $query->field("POSITION");
     $type = $query->field("CLTITYPE_ID");  
     $mincard = $query->field("MINCARD");
     $maxcard = $query->field("MAXCARD");    
     $fkid = $query->field("FKID");
     
     $config = "";
     if ($type == 2 || $type==5) { //dynamic content or library.
       $config = strtoupper(getDBCell("modules", "MODULE_NAME", "MODULE_ID = $fkid"));	
       if (! in_array($config, $requiredPlugins)) $requiredPlugins[] = $config;
     } else if ($type == 4) {
     	  $config = $fkid;
     	  $xmlExchange[] = array("clt" => $fkid);	
     }
     $xml->appendChild('/nx:clustertemplate[@id="'.$clt.'"]', "<nx:clustertemplateitem name=\"$name\" position=\"$position\" type=\"$type\" mincard=\"$mincard\" maxcard=\"$maxcard\" configuration=\"$config\"/>"); 
   } 
   for ($i=0; $i<count($requiredPlugins); $i++) {
   	if (! in_array($requiredPlugins[$i], $xmlExchange["PLUGINS"])) array_push($xmlExchange["PLUGINS"], $requiredPlugins[$i]); 
   }
   return $xml->exportAsXml('', '');
 }
 
 /**
  * Import XML-Code and create a Cluster-Template
  * @param string XML-Code
  * @param string Category ID where the clt will be created.
  * @return integer new id
  */
  function XmlImportClusterTemplate($xmlString, $categoryId="0") {
    global $db;
    
    $xmlOptions = array(XML_OPTION_CASE_FOLDING => TRUE, XML_OPTION_SKIP_WHITE => TRUE);
    $xml =& new XPath(FALSE, $xmlOptions);
    $xml->importFromString($xmlString);
    
    $id = $xml->getAttributes('/NX:CLUSTERTEMPLATE[1]', 'ID');
    $mtId = $xml->getAttributes('/NX:CLUSTERTEMPLATE[1]', 'METATEMPLATE');
    $newId = translateXmlGUID($id);
    $mtId = translateXmlGUID($mtId);
    
    createClusterTemplate(	urldecode($xml->getAttributes('/NX:CLUSTERTEMPLATE[1]', 'NAME')), 
    								urldecode($xml->getAttributes('/NX:CLUSTERTEMPLATE[1]', 'DESCRIPTION')),
    								urldecode($xml->getData('/NX:CLUSTERTEMPLATE[1]/NX:LAYOUT[1]')),
    								$categoryId,
    								$mtId,
    								$newId);



 if ($xml->hasChildNodes('/NX:CLUSTERTEMPLATE[1]')) { 
     $i=1;
     while ($xml->getNode("/NX:CLUSTERTEMPLATE[1]/NX:CLUSTERTEMPLATEITEM[$i]")) {               
        $config = "null";
        $type = $xml->getAttributes("/NX:CLUSTERTEMPLATE[1]/NX:CLUSTERTEMPLATEITEM[$i]", "TYPE");
        $itemCfg = urldecode($xml->getAttributes("/NX:CLUSTERTEMPLATE[1]/NX:CLUSTERTEMPLATEITEM[$i]", "CONFIGURATION"));
        if ($type==2 || $type==5) {
          $config = getDBCell("modules", "MODULE_ID", "UPPER(MODULE_NAME) = '".strtoupper(urldecode($itemCfg))."'");
        } else if ($type==4) {
          $config = translateXmlGUID($itemCfg);
        }
        
        createClusterTemplateFigure( 
          urldecode($xml->getAttributes("/NX:CLUSTERTEMPLATE[1]/NX:CLUSTERTEMPLATEITEM[$i]", "NAME")),
          $newId, 
          $xml->getAttributes("/NX:CLUSTERTEMPLATE[1]/NX:CLUSTERTEMPLATEITEM[$i]", "POSITION"),
          $xml->getAttributes("/NX:CLUSTERTEMPLATE[1]/NX:CLUSTERTEMPLATEITEM[$i]", "MAXCARD"),
          $xml->getAttributes("/NX:CLUSTERTEMPLATE[1]/NX:CLUSTERTEMPLATEITEM[$i]", "MINCARD"),
          $config,
          $type
          );          
        $i++;
      }   
    }
   
    return $newId;
  }
  
  
 
 ?>