<?php
/**
 * Import Resources Wizard
 * @package Modules
 * @subpackage Wizard
 */


/**********************************************************************
 *	N/X - Web Content Management System
 *	Copyright 2004 Sven Weih
 *
 *      $Id: wz_import.php,v 1.5 2004/03/31 15:31:09 fabian_koenig Exp $ *
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
 	require_once $c["path"]."modules/syndication/stcheckresource.php";
 	require_once $c["path"]."modules/syndication/stimportresource.php";

	$auth = new auth("IMPORT");
	$page = new page("IMPORT");
	
	$wizard = new Wizard($lang->get("import_data", "Import N/X-XML Data"));
	$wizard->setTitleText($lang->get("wz_import_title", "This wizard is used for importing data to N/X, which has formerly been exported with another N/X installation. You must delete a resource before you can import it for a second time."));
	
	////// STEP 1 //////	
	$step = new Step();
	$step->setTitle($lang->get("wzt_import_file", "Select N/X-XML File"));
	$step->setExplanation($lang->get("wze_import_file", "Please choose a N/X-XML file from your harddisk for upload into the system.<br/><br/>The system will perform a check and will display status information on the next page.")); 		
	$step->add(new WZUpload("upload"));	
	
	////// STEP 2 //////
	$step2 = new STCheckResource();
   $step2->setTitle($lang->get("wzt_import_val", "Summary of uploaded xml"));
   $step2->setExplanation($lang->get("wze_import_val", "The system has checked the correctness of the uploaded XML file. Read the report on the left for details."));		

	////// STEP 3 //////
	$step3 = new STImportResource();
	$step3->setTitle($lang->get("wzt_importing", "Importing data"));
	$step3->setExplanation($lang->get("wze_importing", "The system has tried to import the data. Check the status on the right."));
	
	$wizard->add($step);
	$wizard->add($step2);
	$wizard->add($step3);
	
	$page->add($wizard);
	$page->draw();
	$db->close();
 
 ?>