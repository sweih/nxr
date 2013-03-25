<?php
	/**********************************************************************
	 *	N/X - Web Content Management System
	 *	Copyright 2003 Sven Weih, 
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
	
	require_once "statsinfo.php";
	require_once "statsmenu.php";
	require_once "widgets/statsdiagram.php";
	require_once "widgets/statssummary.php";
	require_once "widgets/print_button.php";
	require_once "phpOpenTracker.php";
	$print = value("print");
	
	if ($print) {
	  $menu = new WUIInterface();	
	} else {
	  // setting up menu
	  $menu = new StatsMenu();
	  $menu->addMenuEntry($lang->get("overview", "Overview"), "report.php");
	  $menu->addMenuEntry($lang->get("visitors", "Visitors"), "visitors.php");
	  $menu->addMenuEntry($lang->get("pages", "Pages"), "pages.php");
	  //$menu->addMenuEntry($lang->get("indiv_pages", "Individual pages"), "pages_individual.php");
    $menu->addMenuEntry($lang->get("weekday", "Weekdays"), "weekdays.php");
	  $menu->addMenuEntry($lang->get("hour", "Hours"), "hours.php");
	  $menu->addMenuEntry($lang->get("referer", "Referer"), "referer.php");
	  $menu->addMenuEntry($lang->get("environment", "Environment"), "environment.php");
	  $menu->addMenuEntry($lang->get("paths", "Clickpaths"), "paths.php");
	}
	
	$page = new page("Statistics");

	// retrieving datainformations
	$statsinfo = new Statsinfo();
		
	$form = new Container(3);
	
	$form->add(new AlignedLabel("lbl", '<h1>'.$statsinfo->getRangeTitle().'</h1>', "center","", 3));
	
	if (!$print) {
		$form->add(new PrintButton());
	} else {
	  echo "<script language='JavaScript'>window.print();</script>";	
	} 
?>