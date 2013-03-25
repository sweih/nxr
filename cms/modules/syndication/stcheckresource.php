<?php
/**
 * Wizard API Library
 * @package Userinterface
 * @subpackage Wizard
 */


/**********************************************************************
 *	N/X - Web Content Management System
 *	Copyright 2004 Sven Weih
 *
 *      $Id: stcheckresource.php,v 1.2 2004/09/21 10:45:38 sven_weih Exp $ *
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
   * Step module for selecting a resource
   */
  class STCheckResource extends Step {
  
  	  var $xmlString; 	  
  
    /**
     * Standard constructor
     */
     function STCheckResource() {
       global $lang, $errors;
       $this->xmlString = $_SESSION["upload"];  
       if ($this->xmlString != "") {
       	$information = XmlGetImportInfo($this->xmlString);
       	if ($information != false) {
       		$this->add(new WZLabel($lang->get("type").":<br/>".$information["type"]));
       		$this->add(new WZLabel($lang->get("description").":<br/>".$information["description"]));       		
       		if (count($information["errors"]) > 0) {
       		  $errtxt = $lang->get("imp_err", "You cannot import, because of following errors:");
       		  for ($i=0; $i < count($information["errors"]); $i++) {
       		    $errtext.= "<br/>".$information["errors"];
       		  }
       		  $this->add(new WZLabel(	$errtext ));
       		  $this->css="error";
       		  $errors.="-MISSINGREQUIREMENT";
       		} else {
       		  $this->add(new WZLabel( $lang->get("go_import", "Press Next to import this data now. If the data has already been imported, nothing will be changed.")));
       		}       		
       	} else {
       	  $errors.="-NOXML";	
       	  $this->add(new WZLabel($lang->get("no_xml"), "error"));	  	      
  	        $this->css = "error";
       	}
       }
     }
  
  }
 
 ?>