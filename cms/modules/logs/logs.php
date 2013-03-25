<?
	/**********************************************************************
	 *	N/X - Web Content Management System
	 *	Copyright 2003-2004 Sven Weih, FZI Research Center for Information Technologies
	 *	www.fzi.de
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

	$auth = new auth("LOGS");

	// redirect to execute backup.
	$page = new page("Logging");

	
	if (value("action") == $lang->get("reset_logs")) {
	  resetNXLogs();	
	}
	
	$go = "UPDATE";	
	$form = new Form($lang->get("logfile", "View System Logs"));	
	$form->buttonbar->add("action", $lang->get("reset_logs", "Reset Logs"));		
	$form->add(new Label("lbl", $lang->get("logs_info", "The list below shows you logs made while running N/X."), "informationheader", 2));
	$form->add(new Hidden("action", ""));

	$grid = new NXGrid("grid", 3);
	$grid->setRatio(array (
		150,
		350,
		100
	));

	$grid->addTitleRow($lang->get("date", "Date"), $lang->get("message", "Message"), $lang->get("category", "Category"));
	$sql = "Select * FROM log WHERE 1 ORDER BY LOG_TIME DESC";
	$query = new query($db, $sql);
	while ($query->getrow()) {
	  $grid->addRow(array (
		new Label("lbl", $query->field("LOG_TIME"), "standardwhite"),
		new Label("lbl", $query->field("MESSAGE"), "standardwhite"),
		new Label("lbl", $query->field("CATEGORY"), "standardwhite")
	  ));			
	}
	$query->free();

	$form->add($grid);
	$page->add($form);
	$page->draw();
	$db->close();
?>