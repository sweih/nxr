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

	$auth = new auth("ADMINISTRATOR|QUOTER");
	$page = new page("Quote of the day");

	$filter = new Filter("pgn_quote", "QUOTE_ID");
	$filter->addRule("Title", "TITLE", "TITLE");
	$filter->addRule("Quote", "QUOTE", "TITLE");

	$filtermenu = new Filtermenu("Browse Quotes", $filter);

	$deleteHandler = new ActionHandler("DELETE");
	$deleteHandler->addDbAction("DELETE FROM pgn_quote where QUOTE_ID=$oid");

	$form = new stdEDForm("Edit Quote", "");
	$cond = $form->setPK("pgn_quote", "QUOTE_ID");
	$form->add(new TextInput("Title", "pgn_quote", "TITLE", $cond, "type:text,width:300,size:64", "MANDATORY&UNIQUE"));
	$form->add(new TextInput("Quote", "pgn_quote", "QUOTE", $cond, "type:textarea,width:300,size:3", ""));

	$form->registerActionHandler($deleteHandler);

	$page->addMenu($filtermenu);
	$page->add($form);
	$page->draw();
?>