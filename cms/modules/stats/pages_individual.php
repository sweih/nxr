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
        require_once "widgets/toppages.php";
	require_once "widgets/page_selector.php";
	
	$form->add(new AlignedLabel("lbl", $lang->get("pages_ind", "Individual Page Analysis"), "center","stats_title2", 3));
	$menu->topSelector = true;
	
	$wiselector = new WiPageSelector(3);
	$form->add($wiselector);
	$spid = $wiselector->value;
	
	$form->add(new StatsDiagram($lang->get("pi_overview", "Page Impressions overview"), "page_impressions", array($lang->get("pi", "Page Impressions")), "600",300,3,$spid));
	$piSummary = new StatsSummary($lang->get("pi_overview"), "page_impressions", 3, $spid);
	$piSummary->setStandardFields();
	$piSummary->addField($lang->get("avg_clickstream", "Average clickstream"), sprintf("%01.2f", phpOpenTracker::get(array_merge(array('api_call' =>'avg_clickstream'), $rangeArray ))));
	$piSummary->addSpacer();
	$piSummary->renderStatsData(array($lang->get("when", "When"), $lang->get("pi")));
	$form->add($piSummary);
	
	$form->add(new StatsDiagram($lang->get("top_pages", "Top pages"), "top:document", null, 600,400));
	$form->add(new TopPages($lang->get("top_pages", "Top pages"), "document", 3));
	
	$form->add(new StatsDiagram($lang->get("top_entrypages", "Top entry pages"), "top:entry_document", null, 300,300,1));
	$form->add(new Cell("clc", "bcopy", 1, 20, 20));
	$form->add(new StatsDiagram($lang->get("top_exitpages", "Top exit pages"), "top:exit_document", null, 300,300,1));
	
	
	$form->add(new TopPages($lang->get("top_entrypages" ), "entry_document", 1));
	$form->add(new Cell("clc", "bcopy", 1, 20, 20));
	$form->add(new TopPages($lang->get("top_exitpages"), "exit_document", 1));
	
	$page->addMenu($menu);
	$page->add($form);
	$page->draw();
?>