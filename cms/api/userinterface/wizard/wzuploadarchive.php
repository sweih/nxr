<?php
/**
 * Wizard API Library
 * @package Userinterface
 * @subpackage Wizard
 */


/**********************************************************************
 *	N/X - Web Content Management System
 *	Copyright 2004 Sven Weih
 *
 *      $Id: wzuploadarchive.php,v 1.2 2005/05/09 19:13:35 sven_weih Exp $ *
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
 
 /**
  * File upload for Wizards
  */
  class WZUploadArchive extends WZO {
  	
  	/**
  	 * Standard constructor
  	 * @param string Name of the widget
  	 * @param string Label of the widget
  	 * @param string type of the input TEXT|TEXTAREA
  	 * @param boolean can this field not be left empty?
  	 */
  	function WZUploadArchive($name) {
  	  global $c, $errors, $lang, $_FILES;
  	  WZO::WZO($name);  	  
  	  if ($_FILES[$this->name]['name'] !="") { 	  	  	  	
  	  	if  ( stristr(strtoupper($_FILES[$this->name]['type']), "ZIP") === false) {
  	      $this->errortext= "<br/>".$lang->get("no_archive", "The file you uploaded is not an zip-archive or your browser does not send the file correct!");	
  	      $errors.="-NOZIP";
  	      $this->css = "error";
  	    } else {
  	    	$this->value="";
  	    	mt_srand ((double)microtime()*1000000);
  	      $tmpfilename =  md5(uniqid(mt_rand())).".zip";
  	      move_uploaded_file($_FILES[$this->name]['tmp_name'], $c["path"]."cache/".$tmpfilename);  	      
  	      $tmpFolder =  $c['path']."cache/".md5(uniqid(mt_rand()));
  	      mkdir($tmpFolder);	        		
     	  nxunzip($c["path"]."cache/".$tmpfilename, $tmpFolder."/");
     	  $_SESSION["archivefolder"] = $tmpFolder;	  	      
     	  @unlink($c['path']."cache/".$tmpfilename);
  	    }  	    
  	  }	 	    	  
  	}
  	
  	/**
  	 * Check for correctness
  	 */
  	function check() {
  	  global $errors, $lang;
  	  if ($this->errortext != "") {
  	  	 $this->css="error";
  	  	 $this->errortext.= "<br/>".$lang->get("must_upload", "You must upload a file to proceed!");
  	  	 $errors.="-MANDATORY";
  	  }	
  	}
  	
  	/**
  	 * Draw the radios
  	 */
  	function draw() {
  		global $lang;
  		echo "<td>";
  		echo tableStart();
  		td($this->css);
  		echo drawSpacer(2,5);
  		tde();
  		tr();
  		td($this->css);
  		echo $lang->get("sel_file", "Select a file");
  		echo $this->errortext;
  		tde();
  		tr();  		
  		$ul = new Filebox($this->name, $this->css, "",128,280);  		
  		$ul->draw();
  		if ($this->value != "") {
  		  tr();
  		  td($this->css);
  		  echo drawSpacer(5,1);
  		  tde();
  		  tr();
  		  td($this->css);
  		  echo $lang->get("file_already_uploaded", "You have already uploaded a file. You can go on by pressing Next.");
  		  tde();
  		}
  		echo tableEnd();
  		echo "</td>";
  	}
  	
  }
 
 ?>