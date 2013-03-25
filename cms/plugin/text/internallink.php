<?
	/**********************************************************************
	 *	N/X - Web Content Management System
	 *	Copyright 2004 Fabian Koenig, fabian@nxsystems.org
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

	require_once("../../config.inc.php");
	
	$auth = new auth("ANY");
	$disableMenu = true;
	$sitepages = null;
	$sitepages[0][0]="&gt;";
	$sitepages[0][1]=0;

	$sitepages = createSitepageTree($sitepages, "&gt;", 0);
	
	for ($i=0; $i<count($sitepages); $i++) {
		$spid = $sitepages[$i][1];
		
		$spm = getDBCell("sitepage", "SPM_ID", "SPID = ".$spid);
		if ($spm != "") {
			$template = getDBCell("sitepage_master", "TEMPLATE_PATH", "SPM_ID = ".$spm);
			$sitepages[$i][1] = $c["devdocroot"].$template."?page=".$spid;
		}
	}
	
	$go = 1;
	$page_action = "UPDATE";
	
	$page = new page("Internal Link Selector");
	$page->setJS("FCKLIB");
	
	$form = new Form("Select Sitepage", "");
	$form->add(new Label("lbl", "Link destination:", "standardlight", 1));
	$form->add(new Dropdown("link", $sitepages, "standardlight", $sitepages[0][1], 450, 1));
	$container = new HTMLContainer("container", "", "2"); // ("buttons", "standardlight");
	$button = new ButtonInline("Submit", $lang->get('select', 'Select'), "navelement", "button", "javascript:getDoc(form1.link.value); ok();", "form1", 2);
	$out = $button->draw();
	$container->add("<div align=\"right\"><br>".$out."</div>");
	
	$form->add($container);
	
	$page->add($form);
	$page->draw();
	 
?>