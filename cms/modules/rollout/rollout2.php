<?
	/**********************************************************************
	 *	N/X - Web Content Management System
	 *	Copyright 2002-2004 Sven Weih
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

	$auth = new auth("ROLLOUT");
	$dest = value("d");
	$source = value("s");
	$sm = getDBCell("sitepage", "MENU_ID", "SPID=$source");
	
	if ($dest<1000) {
		$dm = 1;
	} else {
		$dm = getDBCell("sitepage", "MENU_ID", "SPID=$dest");
	}

	//// ACL Check ////
	$acl = aclFactory($dm, "page");
	$acl->load();
	
	if (! $acl->checkAccessToFunction("ROLLOUT")) {				   
		   header("Location: ". $c["docroot"]."modules/common/noaccess.php?sid=$sid&guid=$pnode");
	}
	$acl = aclFactory($sm, "page");
	$acl->load();
	if (! $acl->checkAccessToFunction("ROLLOUT")) {				   
		   header("Location: ". $c["docroot"]."modules/common/noaccess.php?sid=$sid&guid=$pnode");
	}
	//// ACL Check ////

	
	if ($dest == "0") {
		$destMenu = "0";
	} else {
		$destMenu = getDBCell("sitepage", "MENU_ID", "SPID = $dest");
	}

	if ($source == "0") {
		$sourceMenu = "0";
	} else {
		$sourceMenu = getDBCell("sitepage", "MENU_ID", "SPID = $source");
	}

	$dlabel = menuBreadCrumb($destMenu);
	$slabel = menuBreadCrumb($sourceMenu);

	$page = new page("Rollout");
	
	$go = "UPDATE";
	$page_action = "UPDATE";
	$form = new Form($lang->get('m_rollout'), "", "form1", "rollout3.php");
	$form->add(new Label("lbl", $lang->get('roll2',"You can change the names of the objects now. This is necessary because you cannot have duplicate names. If names are not too important for you, N/X will use its autonaming algorithm for resolving duplicate name constraints."), '', 2));
	$form->add(new Cell("clc", "standardwhite", 2, 600, 2));
	$form->add(new Subtitle("lbl", $lang->get("rollout_sel", "Source and Destination for Rollout"),  2));
	$form->add(new Spacer(2));
	$form->add(new Label("lbl", $lang->get('source_node', 'Source Node:'), "standardwhite"));
	$form->add(new Label("lbl", $slabel, "standardwhite"));
	$form->add(new Label("lbl", $lang->get('dest_node', 'Destination node:'), "standardwhite"));
	$form->add(new Label("lbl", $dlabel, "standardwhite"));
	$form->add(new Hidden("d", $dest));
	$form->add(new Hidden("s", $source));
	$form->add(new Hidden("sid", $sid));

	
	$form->add(new Spacer(2));
	$form->add(new Subtitle("lbl", $lang->get('search_repl', 'Search and replace object names'), 2));
	$form->add(new Spacer(2));
	

	$repCon1 = new HTMLContainer("con", "standardlight", 2);
	$repCon1->add($lang->get('searchphrase', "Search phrase:") ."<input type='text' size='32' style='width:150px;' name='search'>  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;");
	$repCon1->add($lang->get('replacephrase', "Replace phrase:")." <input type='text' size='32' style='width:150px;' name='replace'><br>");
	$rpbut = new ButtonInline("sar", $lang->get('replaceall', "Replace All"), "navelement", "button", "replaceAll();", "form1");
	
		
	$repCon1->add("<br>".$rpbut->draw());
	$spidArray = getPageTree($sourceMenu);
	$jsArray = array ();
	$form->add(&$repCon1);
	$form->add(new Spacer(2));
	$form->add(new Subtitle("lbl", $lang->get('manedit', "Manual edit rollout names and properties"), 2));
	$form->add(new Spacer(2));
	
	for ($i = 0; $i < count($spidArray); $i++) {
		$clnid = getDBCEll("sitepage", "CLNID", "SPID = " . $spidArray[$i]);

		$menu = getDBCell("sitepage", "MENU_ID", "SPID = " . $spidArray[$i]);
		$specialID = $spidArray[$i];
		$form->add(new Textinput(menuBreadCrumb($menu), "cluster_node", "NAME", "CLNID=$clnid", "type:text,width:250,size:64"));
		$form->add(new Label("lbl", $lang->get("keep_cluster", "Keep original Cluster"), "standard"));
		$form->add(new Checkbox("cln" . $specialID, "1", "standard", false));
		$form->add(new Cell("clc", 10, 3));
		array_push($jsArray, "cluster_node_NAME" . $specialID);
	}

	$repCon1->add("<script language='JavaScript'>");
	$repCon1->add(" var rpa = new Array(");
	$comma = false;

	for ($i = 0; $i < count($jsArray); $i++) {
		if ($comma)
			$repCon1->add(",");

		$repCon1->add('"' . $jsArray[$i] . '"');
		$comma = true;
	}

	$repCon1->add(");\n\n");
	$repCon1->add("function replaceAll() {\n");
	$repCon1->add("  var lSearch = form1.search.value;\n");
	$repCon1->add("  var lReplace = form1.replace.value;\n");
	$repCon1->add("  if (lReplace != '' && lSearch != '') { \n");
	$repCon1->add("    for (var i=0; i < rpa.length; i++) {\n");
	$repCon1->add("     eval(' worktext = form1.' + rpa[i] + '.value');\n");
	$repCon1->add("     var re = new RegExp(lSearch, 'gi');");
	$repCon1->add("     worktext = worktext.replace(re, lReplace);");
	$repCon1->add("     eval('form1.' + rpa[i] + '.value = worktext;');\n");
	$repCon1->add("  }}\n");
	$repCon1->add(" else alert('" .$lang->get('searchreplacevalid', 'You must enter a search and a replace string!'). "');");
	$repCon1->add("}\n");

	$repCon1->add("</script>");

	$repCon1->add("</tr><tr>"); //temporärer fix!!

	$container = new HTMLContainer("cnt", "informationheader", 2);
	$but = new ButtonInline("action", "Next", "navelement", "submit", "", "form1",2);
	$container->add('<br><div align="right">'.$but->draw().'</div>');
	$form->add($container);	
	$form->add(new ActionField());

	$menu = new StdMenu(" ");
	$menu->tipp = $lang->get("help_rollout", "Rollout is a feature of N/X that enables you to make copies of a section on your web page and re-use it with or without the old content.");

	$page->add($form);
	$page->draw();

	$db->close();
?>