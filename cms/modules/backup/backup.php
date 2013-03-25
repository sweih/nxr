<?
	/**********************************************************************
	 *	N/X - Web Content Management System
	 *	Copyright 2003 Sven Weih, FZI Research Center for Information Technologies
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

	$auth = new auth("BACKUP");

    
    $page->tipp = $lang->get("bak_tipp", "You can backup your database and www and wwwdev folder here. Make sure you have setup backup in settings.inc.php!");
    // redirect to execute backup.
	if (value("action") == $lang->get("start_bak")) {
		$db->close();

		header ("Location: " . $c["docroot"] . "modules/backup/execbackup.php?sid=$sid");
		exit;
	}

	$page = new page("Backup");

	if (value("action") == "delete" && value("decision") == $lang->get("yes")) {
		$fn = value("fn");

		$db->close();
		header ("Location: " . $c["docroot"] . "modules/backup/delete.php?sid=$sid&action=delete&fn=$fn");
		exit;
	}

	if (value("action") == "delete" && value("decision") != $lang->get("no")) {
		$fn = value("fn", "NOSPACES");

		$go = "UPDATE";
		$form = new YesNoForm($lang->get("del_backup", "Delete backup file?"), $lang->get("del_backup_mes", "Do you really want to delete this backup file?"). "<br>" . $fn);
		$form->add(new Hidden("fn", $fn));
		$form->add(new Hidden("action", "delete"));
	} else {
		$go = "UPDATE";

		
		$form = new Form($lang->get("backup", "System Backup"));
		$form->add(new Label("lbl", $lang->get("back_info", "The list below shows you the backups of N/X on your server. You can delete the backups here. To restore a backup,you need to manually log in your server and restore the files yourself."), "informationheader", 2));
		$form->add(new Hidden("action", ""));
		$form->add(new Spacer(2));

		$grid = new NXGrid("", 2);
		$grid->setRatio(array (
			450,
			100
		));

		$grid->addTitleRow(array ($lang->get("bak_file", "Backup file"), '&nbsp;'));
		

		// read dir structure
		if (!file_exists($c["backupStore"])) {
			$form->addToTopText($lang->get('backup_config', 'The backup directory was not found on the harddisc!'));	
			$form->topstyle = 'headererror';
		} else {
		
		$form->buttonbar->add("action", $lang->get("start_bak", "Start Backup"));
		$handle = @opendir($c["backupStore"]);
		if ($handle) {
			while (false !== ($file = readdir($handle))) {
				if ($file != "." && $file != "..")
					$grid->addRow(array (
						new Label("lbl", $file, "standardwhite"),
						new Label("lbl", crLink($lang->get("delete"), $c["docroot"] . "modules/backup/backup.php?sid=$sid&action=delete&fn=$file", "navelement"))
					));
			}

			closedir ($handle);
		}
		}

		$form->add($grid);
	}

	$page->add($form);
	$page->draw();
	$db->close();
?>