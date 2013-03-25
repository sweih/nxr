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
        
	$form->add(new AlignedLabel("lbl", '<h2>'.$lang->get("visitors").'</h2>', "center","stats_title2", 3));
	$rangeArray = $statsinfo->getRangeArray();
	unset($rangeArray['interval']);
	$form->add(new StatsDiagram($lang->get("visits", "Visits"), "visits", array($lang->get("visits"))));
	$visitsSummary = new StatsSummary($lang->get("visits_overview"), "visits", 3);
	$visitsSummary->setStandardFields();
	$visitsSummary->addField($lang->get("avg_visit_length", "Average visit length"), date("i:s", phpOpenTracker::get(array_merge(array('api_call' =>'avg_visit_length'), $rangeArray )))." min");
	$visitsSummary->addField($lang->get("avg_clickstream", "Average clickstream"), sprintf("%01.2f", phpOpenTracker::get(array_merge(array('api_call' =>'avg_clickstream'), $rangeArray ))));
	$visitsSummary->addField($lang->get("vis_onl", "Visitors online"),  phpOpenTracker::get(array_merge(array('api_call' =>'num_visitors_online'), $rangeArray )));
	
	$visitsSummary->addSpacer();
	$visitsSummary->renderStatsData(array($lang->get("when", "When"), $lang->get("visits")));
	$form->add($visitsSummary);
	
	$form->add(new StatsDiagram($lang->get("ret_vis", "Returning visitors"), "visitors,nxone_time_visitors,nxreturning_visitors", array($lang->get("visitors"), $lang->get("vis_first", "First time visitors"), $lang->get("vis_ret", "Returning visitors"))));
	$retSummary = new StatsSummary($lang->get("ret_vis"), "visitors", 3);
	$retSummary->addField($lang->get("visitors"), phpOpenTracker::get(array_merge(array('api_call' =>'visitors'), $rangeArray )));
	$retSummary->addField($lang->get("vis_first", "First time visitors"), phpOpenTracker::get(array_merge(array('api_call' =>'nxone_time_visitors'), $rangeArray )));
	$retSummary->addField($lang->get("vis_ret"), phpOpenTracker::get(array_merge(array('api_call' =>'nxreturning_visitors'), $rangeArray )));
	$retSummary->addField($lang->get("time_betw_visits", "Average time between visits"), (date('m', phpOpenTracker::get(array_merge(array('api_call' =>'nxavg_time_between_visits'), $rangeArray )))-1)." months ".
		date('d', phpOpenTracker::get(array_merge(array('api_call' =>'nxavg_time_between_visits'), $rangeArray )))." days");
	$retSummary->addField($lang->get("vis_per_vis", "Average visits per visitor"), sprintf("%01.2f", phpOpenTracker::get(array_merge(array('api_call' =>'nxavg_visits'), $rangeArray ))));
	$form->add($retSummary);
	
	$form->add(new StatsDiagram($lang->get("avg_vis_length", "Average visit length (seconds)"), "avg_visit_length", array($lang->get("avg_vis_length", "Average visit length (seconds)"))));
	$vls = new StatsSummary($lang->get("avg_vis_length"), "avg_visit_length", 3);
	$vls->addField($lang->get("minimum"), date('i:s', $vls->minTs[1]).' min');
	$vls->addField($lang->get("maximum"), date('i:s', $vls->maxTs[1]).' min');
	$form->add($vls);
	
	$form->add(new StatsDiagram($lang->get("avg_clickstream", "Average clickstream"), "avg_clickstream", array($lang->get("avg_clickstream", "Average clickstream"))));
	$clickstreamSummary = new StatsSummary($lang->get("avg_clickstream"), "avg_clickstream", 3);
	$clickstreamSummary->setMinimumFields();
	$form->add($clickstreamSummary);
	
	$page->addMenu($menu);
	$page->add($form);
	$page->draw();

?>