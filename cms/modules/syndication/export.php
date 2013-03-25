<?php
/**
 * Export Resources Wizard
 * @package Modules
 * @subpackage Wizard
 */


/**********************************************************************
 *	N/X - Web Content Management System
 *	Copyright 2004 Sven Weih
 *
 *      $Id: export.php,v 1.4 2004/03/31 15:31:09 fabian_koenig Exp $ *
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
 
 require "../../config.inc.php";
 $auth = new auth("EXPORT");
 session_register("guid");
 session_register("exp_description");
 
 		$output = XmlExportSyndication($_SESSION["guid"], $_SESSION["exp_description"]);   
      $mime_type = (PMA_USR_BROWSER_AGENT == 'IE' || PMA_USR_BROWSER_AGENT == 'OPERA') ? 'application/octetstream' : 'application/octet-stream';
        
      $tempname = tempnam("/tmp", "NXEXP_".$_SESSION["guid"]);
		$fp = fopen($tempname, "w");
		fwrite($fp, $output);
		fclose($fp);
		
		$filename = $_SESSION["resource_type"]."_".$_SESSION["guid"];
      $ext = "nxxml";
		
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
	   $db->close();	
		exit;
?>