<?php
/**
 * IFrame Item Editor for Shop Configurator
 * @package Userinterface
 * @subpackage Special Widgets
 */


/**********************************************************************
 *	N/X - Web Content Management System
 *	Copyright 2004 Sven Weih
 *
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
 
 require_once "../../../config.inc.php";
 $auth = new auth('SHOPADM'); 

 $width = value("width", "NUMERIC", 600);
 if ($width < 200) $width = 200;
 drawIFOHeader('', true);
 $configuratorId = value("value", "NUMERIC", LoadFromSession("configuratorId"));
 br();
 if ($configuratorId != 0) {
    SaveToSession("configuratorId");
 	$action = value("go", "NOSPACES", 0);
    $ar = array();
    $ar[] = array("Checkbox", 0);
    $ar[] = array("Dropdown", 1);
    $ar[] = array("Textinput", 2);
    if (sameText($action, "create") || sameText($action, "update")) {    	
    	don();
    	$form = new stdEDForm("Create configurator item");
    	$form->width = 700;
    	$cond = $form->setPK("shop_configurator_item", "GUID");
    	$form->add(new NonDisplayedValueOnInsert("shop_configurator_item", "CONFIGURATOR_ID", $cond, $configuratorId, "NUMBER"));
    	$form->add(new TextInput($lang->get("title", "Title"), "shop_configurator_item", "TITLE", $cond, "type:text,size:255,width:300", "MANDATORY", "TEXT"));
    	$form->add(new TextInput($lang->get("position", "Position"), "shop_configurator_item", "POSITION", $cond, "type:text,size:2,width:40", "MANDATORY&NUMBER", "NUMBER"));
    	$form->add(new SelectOneInputFixed($lang->get("conf_type", "Configurator Type"), "shop_configurator_item", "TYPE", $ar, $cond, "type:dropdown,width:150", "MANDATORY", "NUMBER"));
    	$form->add(new TextInput($lang->get("configuration", "Configuration"), "shop_configurator_item", "VALUE", $cond, "type:text,size:1024,width:300", "", "TEXT"));    	
    	$form->add(new Hidden("go", $action));
    	$form->check();
    	$form->process();
    	echo $form->draw();
    } else if (sameText($action, "update")) {
    	echo $action;
    } else { 	
      // draw list with all items.
      $page = new page("foo"); // dummy page with no use.
      $form = new MenuForm($lang->get("configurator_settings", "Configurator Settings"), array($lang->get("position0", 'Position'), $lang->get("name","Name"), $lang->get("value", "Value")), "shop_configurator_item", "GUID", array("POSITION","TITLE", "VALUE"), "1", 10); 
 	  $form->addFilterRule($lang->get("name"), "TITLE");
 	  $form->width=700;
 	  $form->newAction = "api/userinterface/spinput/".doc().'?go=create&sid='.$sid;
   	  $form->editAction = doc();
 	  echo $form->draw();	
    }
 }
 
 /**
 td($style);
 echo $lang->get("sel_clt");
 tde();
 tr();
 $clts=array();
 createCLTTree($clts);
 $sb = new Dropdown("clt", $clts, $style, $clt, $width);
 $sb->params = 'onChange="if (this.value != \'-1\') document.ifoform.submit();"';
 $sb->draw(); 
 tr();
 td($style);
 echo drawSpacer(5);
 echo '<script language="JavaScript">';
 echo '  parent.document.getElementById("'.$callback.'").value = "'.$clnid.'";';
 echo '</script>';
 tde();
 if ($clt != "-1" && $clt != "0" && $clt != "") {
   $clusters = createNameValueArray("cluster_node", "NAME", "CLNID", "CLT_ID = $clt AND VERSION=0 AND DELETED=0");
   tr();
   td($style);
   echo $lang->get("sel_cluster", "Select Cluster")." (".(count($clusters)-1).")";
   tde();
   tr();
   $sb2 = new Dropdown("clnid", $clusters, $style, $clnid, $width);
   $sb2->params = 'onChange="'.drawIfoSave($callback, "clnid").'"';
   $sb2->draw(); 
   
 }
 */
 echo $errros;
 drawIFOFooter(true);
 ?>