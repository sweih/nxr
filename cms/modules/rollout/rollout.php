<?
	/**********************************************************************
	 *	N/X - Web Content Management System
	 *	Copyright 2002 Sven Weih, FZI Research Center for Information Technologies
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

	require_once "menu_selector.php";
	$auth = new auth("ROLLOUT");

	$dest = value("destination_");
	$source = value("source_");
	$page->tipp = $lang->get("help_rollout", "Rollout is a feature of N/X that enables you to make copies of a section on your web page and re-use it with or without the old content.");
	
	if ($source != "0") {
		$db->close;

		header ("Location: " . $c["docroot"] . "modules/rollout/rollout2.php?sid=$sid&go=insert&d=$dest&s=$source");
		exit;
	}

	$page = new page("Rollout");

	$go = "INSERT";
	$form = new Form($lang->get("m_rollout"), "");
	$form->add(new MenuSelector("Source Node", "source", "", "", "width:600px"));
	$form->add(new MenuSelector("Destination Node", "destination", "", "", "width:600px"));
	$container = new HTMLContainer("cnt", '', 2);
	$but = new ButtonInline("action", "Next", "navelement", "submit", "", "form1",2);
	$container->add('<br><div align="right">'.$but->draw().'</div>');
	$form->add($container);	
	$form->add(new ActionField());

	$page->add($form);
	$page->draw();
	$db->close();
?>