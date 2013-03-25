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
        $auth = new auth("TRAFFIC");
        require_once "statsinit.php";
        
	$form->add(new AlignedLabel("lbl", '<h2>'.$lang->get("weekday_as", "Weekday Analysis").'</h2>', "center","", 3));
	$rangeArray = $statsinfo->getRangeArray();
	unset($rangeArray['interval']);
	
	$form->add(new StatsDiagram($lang->get("visits", "Visits"), "nxweekdays:visits", array($lang->get("visits"))));
	$visitsSummary = new StatsSummary($lang->get("visits_overview"), "nxweekdays:visits", 3);
	$visitsSummary->setMinimumFields();
	$visitsSummary->addSpacer();
	$visitsSummary->renderStatsData(array($lang->get("when"), $lang->get("visits")));
	$form->add($visitsSummary);
	
	$form->add(new StatsDiagram($lang->get("pi_overview"), "nxweekdays:pi", array($lang->get("pi"))));
	$pi = new StatsSummary($lang->get("pi_overview"), "nxweekdays:pi", 3);
	$pi->setMinimumFields();
	$pi->addSpacer();
	$pi->renderStatsData(array($lang->get("when"), $lang->get("pi")));
	$form->add($pi);
	
	$form->add(new StatsDiagram($lang->get("avg_clickstream"), "nxweekdays:avg_clickstream", array($lang->get("avg_clickstream"))));
	$clkstream = new StatsSummary($lang->get("avg_clickstream"), "nxweekdays:avg_clickstream", 3);
	$clkstream->setMinimumFields();
	$form->add($clkstream);
	
	$page->addMenu($menu);
	$page->add($form);
	$page->draw();

?>