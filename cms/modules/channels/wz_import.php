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
 *      $Id: wz_import.php,v 1.4 2005/05/08 10:19:00 sven_weih Exp $ *
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
 
 	require_once "../../config.inc.php";
 	require_once $c["path"]."modules/channels/st_import.php";

	$auth = new auth("ANY");
	$page = new page("Import Articles");
	
	$wizard = new Wizard($lang->get("wz_articleimport", "Article-Import Wizard"));
	$wizard->setTitleText($lang->get("wz_articleimport_title", "This wizard is used to import article data to a channel from different sources. You can import from existing Multipages or other channels. You have to make sure that the corresponding templates are compatible."));
	
	////// STEP 1 //////
	
	$step = new STSelectResource("CHANNELCATEGORY");
	$step->setTitle($lang->get("wzt_articleimport_target", "Select target channel"));
	$step->setExplanation($lang->get("wze_articleimport_target", "On the right you need to select the target channel. All imported articles will be stored within this channel.")); 
	
	////// STEP 2 //////

	$step2 = new Step();
	$step2->setTitle($lang->get("wzt_articleimport_srctype", "Select source type"));
	$step2->setExplanation($lang->get("wze_articleimport_srctype", "Please select the type of source you want to import the articles from."));
	
	$source_types[0][0] = $lang->get("multipage", "Multipage");
	$source_types[0][1] = "PAGE";
	//$source_types[1][0] = $lang->get("channel");
	//$source_types[1][1] = "CHANNEL";
	//$source_types[2][0] = $lang->get("cluster");
	//$source_types[2][1] = "CLUSTER";
	
	$step2->add(new WZRadio("source_type", $source_types));
	
	
	////// Step 3 //////	
	// determine singlepages
	$sql = "Select distinct sm.MENU_ID FROM sitemap sm, sitepage_master spm WHERE sm.SPM_ID = spm.SPM_ID and sm.VERSION=0";
	$query = new query($db, $sql);
    $sourcePages = array();
    while ($query->getrow()) {
      $sourcePages[] = array(backTrail($query->field("MENU_ID")), $query->field("MENU_ID"));   	
    }
	
    sort($sourcePages);   

    $step3 = new Step();
    $step3->add(new WZSelect("menuid", $lang->get("wz_imp_selcl", "Zu importierenden Knoten wählen"), $sourcePages));  	
	
	

	
	$stepx = new STImportToChannel();
	
	$wizard->add($step);
	$wizard->add($step2);
	$wizard->add($step3);
	$wizard->add($stepx);
	
	$page->add($wizard);
	$page->draw();
 echo $errors;
 ?>