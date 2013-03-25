<?php
/**
 * IFrame Cluster Selector
 * @package Userinterface
 * @subpackage Special Widgets
 */


/**********************************************************************
 *	N/X - Web Content Management System
 *	Copyright 2004 Sven Weih
 *
 *      $Id: ifclselector.php,v 1.4 2004/05/07 09:59:19 sven_weih Exp $ *
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
 $auth = new auth("EXPORT"); // must be adapted for later use.
 
 $width = value("width", "NUMERIC") - 50;
 if ($width < 200) $width = 200;
 
 drawIFOHeader();
 $value = value("value", "NUMERIC");
 if ($value != "0") {
   $clnid = $value;	
 	$clt = getDBCell("cluster_node", "CLT_ID", "CLNID = $value");
 } else {
   $clt = value("clt", "NUMERIC");
   $clnid = "-1";
 } 
 
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
 retain("width", $width+50);
 drawIFOFooter();
 ?>