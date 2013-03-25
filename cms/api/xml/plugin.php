<?php

/**********************************************************************
 *	N/X - Web Content Management System
 *	Copyright 2004 Sven Weih
 *
 *      $Id: plugin.php,v 1.2 2003/12/28 18:52:29 sven_weih Exp $ *
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
  * Export the content of a plugin to XML
  * @param integer ID of the plugin
  * @param integer PKID of the plugin table
  */
 function XmlExportPlugin($pluginId, $pkid) {
   $pgn = createPGNRef($pluginId, $pkid);
   return $pgn->export();
 }
 
 /**
  * Import the XML as content to a plugin
  * @param string XML-Data to import.
  * @param integer GUID to use for this content or null for creation
  */
 function XmlImportPlugin($xmlString, $id=null) {
 	if ($id==null) $id = nextGUID;
 	$xmlOptions = array(XML_OPTION_CASE_FOLDING => TRUE, XML_OPTION_SKIP_WHITE => TRUE);
   $xml =& new XPath(FALSE, $xmlOptions);
   $xml->importFromString($xmlString);
 	$type = strtoupper($xml->getAttributes('/NX:CONTENT[1]', "TYPE"));
   $moduleId = getDBCell("modules", "MODULE_ID", "UPPER(MODULE_NAME) = '$type'");
   if ($moduleId != "") {
     $pgn = createPGNRef($moduleId, $id);
     $pgn->import($xmlString); 	
     return $id;
   }
   return false;
 }
 
 ?>