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
        require_once "widgets/topitems.php";
	
	$form->add(new AlignedLabel("lbl", '<h2>'.$lang->get("referer", "Referer").'<h2>', "center","stats_title2", 3));
	if (!$print) $menu->topSelector = true;
		
	$form->add(new StatsDiagram($lang->get("top_referer", "Top referer"), "top:referer", null, 600,500));
	$form->add(new TopItems($lang->get("top_referer" ), "referer", 3));
	
	$form->add(new StatsDiagram($lang->get("top_si", "Top search engines"), "search_engines:top_search_engines", null, 600,500));
	$form->add(new TopItems($lang->get("top_si" ), "search_engines:top_search_engines", 3));
	
	$form->add(new StatsDiagram($lang->get("top_skw", "Top search keywords"), "search_engines:top_search_keywords", null, 600,500));
	$form->add(new TopItems($lang->get("top_skw" ), "search_engines:top_search_keywords", 3));
	
	$form->add(new StatsDiagram($lang->get("top_exitt", "Top exit targets"), "top:exit_target", null, 600,500));
	$form->add(new TopItems($lang->get("top_exitt" ), "exit_target", 3));
	
	$page->addMenu($menu);
	$page->add($form);
	$page->draw();
?>