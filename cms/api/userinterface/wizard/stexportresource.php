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
 *      $Id: stexportresource.php,v 1.2 2004/09/21 10:45:36 sven_weih Exp $ *
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
  class STExportResource extends Step {
  
  	  var $guid; 	  
  	  var $description;
  
    /**
     * Standard constructor
     */
     function STExportResource() {
       global $lang;
       $this->guid = $_SESSION["guid"];
       $this->description = $_SESSION["exp_description"];
     	 $this->setTitle($lang->get("exec_export", "Exporting the resource"));
     	 $this->setExplanation($lang->get("expl_exec_export", "The system is generating a XML-File for export now.<br/><br/>In a few seconds, a popup will appear. Press Save for storing the Resource on your harddisk."));     	 
     	 $this->add(new WZLabel($lang->get("exp_report", "Exporting following data").":"));
     	 $this->add(new WZLabel($lang->get("type").":<br/>".$_SESSION["resource_type"]));
     	 $this->add(new WZLabel($lang->get("name").":<br/>".getResourceName($this->guid)));
     	 $this->add(new WZLabel($lang->get("Description").":<br/>".$this->description));
     }
     
   /**
    * Perform the export.
    */
    function execute() {
      global $sid, $c, $page;
      $this->parent->finished = true;
      $page->onLoad.='document.location.href=\''.$c["docroot"].'modules/syndication/export.php?sid='.$sid.'\'";';
      //$export = '<iframe name = "export" src="'. style = "width:0px; height:0px; border: 0px solid #cccccc;" frameborder = "0" scrolling="no"></iframe>';
      //$this->addToDrawString($export);
    }
   	
  
  }
 
 ?>