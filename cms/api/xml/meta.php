<?php

/**********************************************************************
 *	N/X - Web Content Management System
 *	Copyright 2004 Sven Weih
 *
 *      $Id: meta.php,v 1.4 2003/12/27 23:34:58 sven_weih Exp $ *
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
  * Return the XML-Code for META-DATA of an object
  * @param integer ID of the object
  */
  function XmlExportMetaData($id) {
    global $db;
   
    $xmlOptions = array(XML_OPTION_CASE_FOLDING => TRUE, XML_OPTION_SKIP_WHITE => TRUE);
    $xml =& new XPath(FALSE, $xmlOptions);
    $xml->appendChild('', "<nx:meta/>");          
   
    $sql = "SELECT mti.NAME, m.VALUE FROM meta_template_items mti, meta m WHERE m.MTI_ID = mti.MTI_ID AND m.CID = $id";
    $query = new query($db, $sql);
    while ($query->getrow()) {
      $name = urlencode($query->field("NAME"));
      $value = urlencode($query->field("VALUE"));
      $xml->appendChild('/NX:META', "<nx:metaitem name=\"$name\" value=\"$value\"/>");          
    } 
    $query->free();
    return $xml->exportAsXml('', '');
  }
  
  
   /**
  * Import XML-Code and create a Meta-Data
  * @param string XML-Code
  * @param integer GUID of the object to store the metadata for.
  * @param integer GUID of the Meta-Template to use as base.
  */
  function XmlImportMetaData($xmlString, $fkId, $mtId) {
    global $db;
    
    $xmlOptions = array(XML_OPTION_CASE_FOLDING => TRUE, XML_OPTION_SKIP_WHITE => TRUE);
    $xml =& new XPath(FALSE, $xmlOptions);
    $xml->importFromString($xmlString);
    if ($xml->hasChildNodes('/NX:META[1]')) {
     $i=1;
     while ($xml->getNode("/NX:META[1]/NX:METAITEM[$i]")) {               
        createMetaData( $fkId,
                        $mtId, 
                        urldecode($xml->getAttributes("/NX:META[1]/NX:METAITEM[$i]", "NAME")),
                        urldecode($xml->getAttributes("/NX:META[1]/NX:METAITEM[$i]", "VALUE")));                                  
        $i++;
      }   
    }
  }
 
 /**
  * Return the XML-Code for a Meta-Template
  * @param integer ID of the Meta-Template
  */
 function XmlExportMetaTemplate($mtId) {
   global $db;
   
   $xmlOptions = array(XML_OPTION_CASE_FOLDING => TRUE, XML_OPTION_SKIP_WHITE => TRUE);
   $xml =& new XPath(FALSE, $xmlOptions);
   
   $sql = "SELECT * FROM meta_templates WHERE MT_ID = $mtId";
   $query = new query($db, $sql);
   if ($query->getrow()) {
     $name = urlencode($query->field("NAME"));
     $description = urlencode($query->field("DESCRIPTION"));
     $xml->appendChild('', "<nx:metatemplate id=\"$mtId\" name=\"$name\" description=\"$description\"/>");     
     $query->free();
   }
   
   $sql = "SELECT * FROM meta_template_items WHERE MT_ID = $mtId";
   $query = new query($db, $sql);
   while ($query->getrow()) {
     $name = urlencode($query->field("NAME"));
     $position = $query->field("POSITION");
     $type = $query->field("MTYPE_ID");  
     $xml->appendChild('/nx:metatemplate[@id="'.$mtId.'"]', "<nx:metatemplateitem name=\"$name\" position=\"$position\" type=\"$type\"/>"); 
   }
   return $xml->exportAsXml('', '');
 }
 
 /**
  * Import XML-Code and create a Meta-Template
  * @param string XML-Code
  * @return integer new id
  */
  function XmlImportMetaTemplate($xmlString) {
    global $db;
    
    $xmlOptions = array(XML_OPTION_CASE_FOLDING => TRUE, XML_OPTION_SKIP_WHITE => TRUE);
    $xml =& new XPath(FALSE, $xmlOptions);
    $xml->importFromString($xmlString);
    $id = $xml->getAttributes('/NX:METATEMPLATE[1]', 'ID');
    $newId = translateXmlGUID($id);
    createMetaTemplate(urldecode($xml->getAttributes('/NX:METATEMPLATE[1]', 'NAME')), urldecode($xml->getAttributes('/NX:METATEMPLATE[1]', 'DESCRIPTION')), $newId);

    if ($xml->hasChildNodes('/NX:METATEMPLATE[1]')) { 
     $i=1;
     while ($xml->getNode("/NX:METATEMPLATE[1]/NX:METATEMPLATEITEM[$i]")) {               
        createMetaTemplateFigure( $newId, 
                                  urldecode($xml->getAttributes("/NX:METATEMPLATE[1]/NX:METATEMPLATEITEM[$i]", "NAME")),
                                  $xml->getAttributes("/NX:METATEMPLATE[1]/NX:METATEMPLATEITEM[$i]", "POSITION"),
                                  $xml->getAttributes("/NX:METATEMPLATE[1]/NX:METATEMPLATEITEM[$i]", "TYPE"));
        $i++;
      }   
    }
    
    return $newId;
  }
 
 ?>