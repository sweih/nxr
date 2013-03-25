<?php
	/**********************************************************************
	 *	N/X - Web Content Management System
	 *	Copyright 2003 Sven Weih, 
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
  $auth = new auth("TRAFFIC");	 
  require_once $c["path"]."modules/stats/statsinfo.php";
  require_once $c["path"]."modules/stats/phpOpenTracker.php";
  
  $statsinfo = new Statsinfo();
  $diagram = value("diagram", "NOSPACES");
  $width = value("width", "NUMERIC", 600);
  $height = value("height", "NUMERIC", 250);
  
  $hours = false;
  $weekday = false;
  
  if (substr($diagram, 0, 8) == "nxhours:") {
    $hours=true;
    $diagram = substr($diagram, 8);	
  }
  
  if (substr($diagram, 0, 11) == "nxweekdays:") {
    $weekdays = true;
    $diagram = substr($diagram, 11);	
  }
   
  if (substr($diagram, 0, 4) == "top:") {
  	$top = true;
  	$diagram = substr($diagram, 4);
  } else {
  	$top = false;
  }
  if (substr($diagram, 0, 15) == "search_engines:") {
  	$top = true;
  }
      
  if ($diagram != "") {
    if ($weekdays) {
    	$paramArray = array(
		'api_call' => 'nxweekdays',
		'what' => $diagram,
		'width' => $width,
		'height' => $height,
	);
    } else if ($hours) {
    	$paramArray = array(
		'api_call' => 'nxhours',
		'what' => $diagram,
		'width' => $width,
		'height' => $height,
	);
    } else if (!$top) {
      $paramArray = array(
	'api_call' => 'nxaccess_statistics',
	'width' => $width,
	'height' => $height,
	'what' => explode(",", $diagram),
	'whatcolors' => array(__RED, __BLUE, __YELLOW, __GREEN, _PURPLE)
      );
    } else if ($top){
    	$paramArray = array(
	'api_call' => 'nxtop',
	'width' => $width,
	'height' => $height,
	'what' => $diagram,
	'limit' => 10
      );
    } 
    
    $constraint = array();
    if (value("spid", "NUMERIC") != "0") {
    	 $constraint["constraints"] = array('document_string' => value("spid"));	
    }

    $paramArray = array_merge($paramArray, $statsinfo->getRangeArray(), $constraint);      
    phpOpenTracker::plot($paramArray);	
  }
	 
?>