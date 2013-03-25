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
 
 $auth= new auth("ROLLOUT");
 
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

 
 if ($dest=="0") {
 	$destMenu = "0";
 } else {
	$destMenu = getDBCell("sitepage", "MENU_ID", "SPID = $dest");
 }
 
 if ($source=="0") {
 	$sourceMenu = "0";
 } else {
 	$sourceMenu = getDBCell("sitepage", "MENU_ID", "SPID = $source");
 }
 
 
 $page= new page ("Rollout");

 
 $go = "CREATE";
 $form = new form($lang->get("M_Rollout"), "");

 @ini_set("max_execution_time", $c_siteCaching_timeout);
 copyTree($sourceMenu, $destMenu);
 $form->buttonbar->add('link', $lang->get('new_rollout'), 'button', "document.location.href='" .$c["docroot"]. "modules/rollout/rollout.php?sid=".$sid."';");
// $form->add(new Linklabel("lbl", $lang->get("new_rollout", "Start another rollout"), "modules/rollout/rollout.php?sid=$sid", "_self", "informationheader", 2));
 if ($errors == "") {
   $form->addToTopText($lang->get('rolloutsuccess', "The copy was successful!"));
   $form->topstyle = 'headersuccess'; 	
 } else {
   $form->addToTopText($lang->get('rollouterror', "There was an error while rolling out. Please pass the following string to your Administrator!").'<br>'.$errors);
   $form->topstyle = 'headererror'; 	 	
 }
  
 
  $page->add($form);
  $page->draw();

 echo $errors;
 $db->close();
 ?>
