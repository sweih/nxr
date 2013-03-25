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

	$auth = new auth("META_TEMP");
	$page = new page("Meta-Templates");

	$filter = new Filter("meta_templates", "MT_ID");
	$filter->addRule($lang->get("name"), "NAME", "NAME");
	$filter->setNewAction("meta.php");
	$filter->setAdditionalCondition("INTERNAL = 0 AND VERSION = 0");
	$filter->icon = "li_meta.gif";
	$filter->type_name = "Meta Templates";

	$filtermenu = new Filtermenu($lang->get("metatemplates"), $filter);
	
	$deleteHandler = new ActionHandler("DELETE");
	// add update action for referenced meta-templates!!!
	$deleteHandler->addDbAction("DELETE FROM meta_templates where MT_ID=$oid and MT_ID > 999");
	$deleteHandler->addDbAction("DELETE FROM meta_template_items where MT_ID=$oid and MT_ID>999");

	$form = new stdEDForm($lang->get("mt_properties"), "i_meta.gif");
	$cond = $form->setPK("meta_templates", "MT_ID");
	$form->add(new TextInput($lang->get("name"), "meta_templates", "NAME", $cond, "type:text,width:200,size:32", "MANDATORY&UNIQUE"));
	$form->add(new TextInput($lang->get("description"), "meta_templates", "DESCRIPTION", $cond, "type:textarea,width:300,size:4", ""));
	$form->registerActionHandler($deleteHandler);
	//check, if deletion is alowed or not.
	$amount = countRows("cluster_templates", "MT_ID", "MT_ID = $oid");
	$amount += countRows("content", "MT_ID", "MT_ID = $oid");

	if ($amount > 0)
		$form->forbidDelete(true);

	if ($page_action == 'UPDATE' || $page_action == "DELETE" || ($page_action=="INSERT" && $page_state == 'processing' && $errors == "")) {
		$form->headerlink = crHeaderLink($lang->get('mt_scheme', 'Edit Scheme'), 'modules/meta/metascheme.php?sid='.$sid.'&oid=<oid>&go=update');
	}

	$page->addMenu($filtermenu);
	$page->add($form);
	$page->draw();
	$db->close();
?>