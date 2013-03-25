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
 *      $Id: stimportresource.php,v 1.1 2004/03/31 15:31:09 fabian_koenig Exp $ *
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
   * Step module for exporting a resource to XML
   */
  class STImportResource extends Step {
  
  	 var $xmlString;
  	  
    /**
     * Standard constructor
     */
     function STImportResource() {
       global $lang;
       $this->xmlString = $_SESSION["upload"];       
     }
     
   /**
    * Perform the export.
    */
    function execute() {
      global $errors, $imported, $lang;
      XmlImportSyndication($this->xmlString);
      if ($errors == "") {
        $this->add(new WZLabel($lang->get("numb_imported", "Number of imported recordsets:")." ".$imported));
      } else {
        $this->add(new WZLabel($lang->get("import_failed", "There was an error while importing the data.")));	
      }
      $this->parent->finished=true;
    }
   	
  
  }
 
 ?>