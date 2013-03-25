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
 *      $Id: wz_export.php,v 1.8 2004/03/31 15:31:09 fabian_koenig Exp $ *
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

	$auth = new auth("EXPORT");
	$page = new page("Export");
	
	$wizard = new Wizard($lang->get("export_data", "Export Content and Templates Wizard"));
	$wizard->setTitleText($lang->get("wz_export_title", "This wizard is used to exchange clusters, cluster-templates and page-templates between your N/X installation and others. The wizard generates a XML File, which you can store on your local hard drive and exchange with other N/X-Users."));
	
	////// STEP 1 //////
	
	$step = new Step();
	$step->setTitle($lang->get("wzt_export_type", "Select type to export"));
	$step->setExplanation($lang->get("wze_export_type", "On the right you need to select the type of data you want to export. Clusters are storing content. When you export clusters, the templates are automatically exported too. Cluster-Templates are schemes for creating clusters. Page-Templates are used for creating pages in the website. Cluster-Templates, Meta-Templates and layout are automatically exported when you export a Page-Template.")); 
	
	$resources[0][0] = $lang->get("cluster", "Cluster");
	$resources[0][1] = "CLUSTER";
	$resources[1][0] = $lang->get("cluster_template", "Cluster Template");
	$resources[1][1] = "CLUSTERTEMPLATE";
	$resources[2][0] = $lang->get("page_template", "Page Template");
	$resources[2][1] = "PAGETEMPLATE";
	
	$step->add(new WZRadio("resource_type", $resources));
	
	////// STEP 2 //////
	$step2=new STSelectResource();
	$step2->setTitle($lang->get("wzt_sel_exp_res", "Select Resource for export"));
	
	////// STEP 3 //////
	$step3 = new Step();
	$step3->setTitle($lang->get("wzt_descr", "Add description"));
	$step3->setExplanation($lang->get("wzt_descr_expl", "You should add a short description to the exported data.<br/><br/> Anyone who will import the data will easier understand, what he exports."));
	$step3->add(new WZText("exp_description", $lang->get("description", "Description"), "TEXTAREA"));
	
	////// Step 4 //////
	$step4 = new STExportResource();
	
	$wizard->add($step);
	$wizard->add($step2);
	$wizard->add($step3);
	$wizard->add($step4);
	
	$page->add($wizard);
	$page->draw();
 
 ?>