<?php

/**********************************************************************
 *	N/X - Web Content Management System
 *	Copyright 2004 Sven Weih
 *
 *      $Id: sitepage_master.php,v 1.1 2003/12/28 23:59:30 sven_weih Exp $ *
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
  * Return the XML-Code for a Sitepage-Master
  * @param integer GUID of the sitepage-master
  
  */
 function XmlExportSitepageMaster($spm) {
   global $db, $xmlExchange, $c;
   
   $xmlOptions = array(XML_OPTION_CASE_FOLDING => TRUE, XML_OPTION_SKIP_WHITE => TRUE);
   $xml =& new XPath(FALSE, $xmlOptions);
   
   $sql = "SELECT * FROM sitepage_master WHERE SPM_ID = $spm";
   $query = new query($db, $sql);
   if ($query->getrow()) {
     $name = urlencode($query->field("NAME"));
     $description = urlencode($query->field("DESCRIPTION"));
     $templatePath = $query->field("TEMPLATE_PATH");
     $clt = $query->field("CLT_ID");
   
     $type = $query->field("SPMTYPE_ID");
     $template = "";
     $fp = @fopen($c["devpath"].$templatePath, "r");
     if ($fp != "") {
				  while (!feof($fp)) $template .= fgets($fp, 128);
				  @fclose ($fp);
     }
     $template = urlencode($template);
     $templatePath = urlencode($templatePath);
     $xml->appendChild('', '<NX:SITEPAGEMASTER ID="'.$spm.'" NAME="'.$name.'" DESCRIPTION="'.$description.'" TYPE="'.$type.'" FILENAME="'.$templatePath.'" CLUSTERTEMPLATE="'.$clt.'">'.$template.'</NX:SITEPAGEMASTER>');         
     $query->free();
     $xmlExchange[] = array("clt" => $clt);	    
   }   
   return $xml->exportAsXml('', '');
 }
 
 /**
  * Import XML-Code and create a Sitepage-Master
  * @param string XML-Code
  * @return integer new id
  */
  function XmlImportSitepageMaster($xmlString) {
    global $db;
    
    $xmlOptions = array(XML_OPTION_CASE_FOLDING => TRUE, XML_OPTION_SKIP_WHITE => TRUE);
    $xml =& new XPath(FALSE, $xmlOptions);
    $xml->importFromString($xmlString);
    
    $id = $xml->getAttributes('/NX:SITEPAGEMASTER[1]', 'ID');
    $clt = $xml->getAttributes('/NX:SITEPAGEMASTER[1]', 'CLUSTERTEMPLATE');
    $type = $xml->getAttributes('/NX:SITEPAGEMASTER[1]', 'TYPE');
    $newId = translateXmlGUID($id);
    $clt = translateXmlGUID($clt);
    $id = translateXmlGUID($id);
    $templatePath = urldecode($xml->getAttributes('/NX:SITEPAGEMASTER[1]', 'FILENAME'));
    $name = urldecode($xml->getAttributes('/NX:SITEPAGEMASTER[1]', 'NAME'));
    $description = urldecode($xml->getAttributes('/NX:SITEPAGEMASTER[1]', 'DESCRIPTION'));
    $template = urldecode($xml->getData('/NX:SITEPAGEMASTER[1]'));
    return createSitepageMaster($name, $description, $templatePath, $template, $clt, $type, $id);    
 }
 
 ?>