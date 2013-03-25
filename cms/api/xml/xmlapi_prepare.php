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
 *      $Id: xmlapi_prepare.php,v 1.5 2004/01/10 14:53:22 sven_weih Exp $ *
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
  
  $xmlExchange = array();
  $xmlExchange["PLUGINS"] = array();
  $xmlTrans = array();
  $syndACL = array();
  $provider = $c["provider"];
  $imported=0;
  
  /**
   * Translate an old into a new GUID
   * @param integer old GUID
   * @param boolean set AntiCycle
   */
   function translateXmlGUID($oldId) {
     global	$db, $provider, $c;
     if ($oldId < 1000) {
     	  // System data.
     	  $out = $oldId;
     } else if (strtoupper($provider) == strtoupper($c["provider"])) {
     	  // own data.
     	  $out = $oldId;
     } else { 
     	$provider = strtoupper(parseSQL($provider));     
     	resetDBCache();
     	$out = getDBCell("syndication", "OUT_ID", "IN_ID = $oldId AND PROVIDER = '$provider'");    
     	if ($out == "") {
      	 $out = nextGUID();
       	$sql = "INSERT INTO syndication (IN_ID, OUT_ID, PROVIDER) VALUES ($oldId, $out, '$provider')";
       	$query = new query($db, $sql);
       	$query->free();
     	}
     }	
     return $out;
   }
        	  
 ?>