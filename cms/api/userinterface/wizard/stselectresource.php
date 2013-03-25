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
 *      $Id: stselectresource.php,v 1.4 2005/05/08 09:52:42 sven_weih Exp $ *
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
  class STSelectResource extends Step {
  
  	  var $restype; 	  
  
    /**
     * Standard constructor
     * @param string optional to manually set resource_type 
     */
     function STSelectResource($restype = "") {
       global $lang;
       if ($restype == "") {
       	 $this->restype = $_SESSION["resource_type"];
       } else {
       	 $this->restype = $restype;
       	 $_SESSION["resource_type"] = $restype;
       }
     	 if ($this->restype == "CLUSTER") {
     	   $this->add(new WZSelectCluster("guid"));
     	   $this->explanation = $lang->get("expl_sel_cluster", "After selecting a cluster-template, a list with clusters will appear, where you can select one.");
     	 } else if ($this->restype=="CLUSTERTEMPLATE") {
     	   $this->explanation = $lang->get("expl_sel_clt", "Please Select a cluster-template.");
     	   $clts=array();
     	   createCLTTree($clts);
     	   $this->add(new WZSelect("guid", $lang->get("sel_clt", "Select Cluster-Template"), $clts));
     	 } else if ($this->restype == "PAGETEMPLATE") {
     	   $spms = createNameValueArray("sitepage_master", "NAME", "SPM_ID", "DELETED=0 AND VERSION=0");
     	   $this->add(new WZSelect("guid", $lang->get("sel_ptml", "Select Page-Template"), $spms));
     	   $this->explanation = $lang->get("expl_sel_spm", "Please select a page-template.");
     	 } else if ($this->restype == "CHANNEL") {
     	   $channels = createNameValueArray("channels", "NAME", "CHID");
     	   $this->add(new WZSelect("guid", $lang->get("sel_ch", "Select Channel"), $channels));
     	   $this->explanation = $lang->get("expl_sel_ch", "Please select a channel.");
     	 } else if ($this->restype == "CHANNELCATEGORY") {
     	   $channels = getChannelDropDownValues();
     	   $this->add(new WZSelect("guid", $lang->get("sel_ch", "Select Channel"), $channels));
     	   $this->explanation = $lang->get("expl_sel_ch", "Please select a channel.");     	 	
     	 }
     }
  
  }
 
 ?>