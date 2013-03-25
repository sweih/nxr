<?php
/**
 * Maintance Switch	
 * @package modules
 * @subpackage Maintenance
 */


/**********************************************************************
 *	N/X - Web Content Management System
 *	Copyright 2004 Sven Weih
 *
 *      $Id: switchmode.php,v 1.1 2004/11/18 14:38:33 sven_weih Exp $ *
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
 $auth = new auth("ADMINISTRATOR");
 $page = new page("Maintenance Mode");
 $go="update";
 
 
 if ($page_state == "processing") {   
   $cbwwwdev = $HTTP_POST_VARS["cbwwwdev"];
   $cbwww = $HTTP_POST_VARS["cbwww"];
   $cbbb = $HTTP_POST_VARS["cbbb"];
   
   if (($cbwwwdev == "1") && (reg_load("SYSTEM/MAINTENANCE/WWWDEV") != "1")) switchToMaintenanceMode("dev");
   if (($cbwww == "1") && (reg_load("SYSTEM/MAINTENANCE/WWW") != "1")) switchToMaintenanceMode("live");

   if (($cbwwwdev == "") && (reg_load("SYSTEM/MAINTENANCE/WWWDEV") == "1")) disableMaintenanceMode("dev");
   if (($cbwww == "") && (reg_load("SYSTEM/MAINTENANCE/WWW") == "1")) disableMaintenanceMode("live");

   reg_save("SYSTEM/MAINTENANCE/WWWDEV", $cbwwwdev);
   reg_save("SYSTEM/MAINTENANCE/WWW", $cbwww);
   reg_save("SYSTEM/MAINTENANCE/BB", $cbbb);
 } else {
   $cbwwwdev = reg_load("SYSTEM/MAINTENANCE/WWWDEV");
   $cbwww = reg_load("SYSTEM/MAINTENANCE/WWW");
   $cbbb = reg_load("SYSTEM/MAINTENANCE/BB");	
 }
 
 $page->tipp = $lang->get("maint_descr", "Maintenance mode displays a 'Page under maintance' message for the development or the live-website. <br>You can also switch the whole backend into maintenance mode. Then only the user ADMINISTRATOR can log in!");
 
 $form = new Form($lang->get("maint_mode", "Maintenance Mode"));
 $form->add(new Label("lbl", $lang->get("maint_bb", "Backend Maintenance"), "standard"));
 $form->add(new Checkbox("cbbb", "1", "standard", $cbbb));
 
 $form->add(new Label("lbl", $lang->get("maint_www", "Live Website Maintenance"), "standard"));
 $form->add(new Checkbox("cbwww", "1", "standard", $cbwww));
 $form->add(new Label("lbl", $lang->get("maint_wwwdev", "Dev Website Maintenance"), "standard"));
 $form->add(new Checkbox("cbwwwdev", "1", "standard", $cbwwwdev));
 
 $form->add(new Hidden("processing", "yes"));
 $form->add(new Spacer(2));
 $form->add(new FormButtons(true, true));
  
 $page->add($form);
 $page->draw();
 
 ?>