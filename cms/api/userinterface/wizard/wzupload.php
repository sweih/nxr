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
 *      $Id: wzupload.php,v 1.4 2004/11/29 08:20:45 sven_weih Exp $ *
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
  class WZUpload extends WZO {
  	
  	/**
  	 * Standard constructor
  	 * @param string Name of the widget
  	 * @param string Label of the widget
  	 * @param string type of the input TEXT|TEXTAREA
  	 * @param boolean can this field not be left empty?
  	 */
  	function WZUpload($name) {
  	  global $c, $errors, $lang;
  	  WZO::WZO($name);
  	  if ($_FILES[$this->name]['name'] !="") {
  	  	if ((strtoupper($_FILES[$this->name]['type']) != "TEXT/XML") && (strtoupper($_FILES[$this->name]['type']) != "TEXT/PLAIN") && (strtoupper($_FILES[$this->name]['type']) != "APPLICATION/OCTET-STREAM")) {
  	      $this->errortext= "<br/>".$lang->get("no_xml", "The file you uploaded is not XML or your browser does not send the file correct!");	
  	      $errors.="-NOXML";
  	      $this->css = "error";
  	    } else {
  	      $this->value="";
  	      mt_srand ((double)microtime()*1000000);
  	      $tmpfilename =  md5(uniqid(mt_rand())).".xml";
  	      move_uploaded_file($_FILES[$this->name]['tmp_name'], $c["path"]."cache/".$tmpfilename);
  	      $fp = @fopen($c["path"]."cache/".$tmpfilename, "r");
  	      while (!feof($fp)) $this->value .= fgets($fp, 128);
		   @fclose ($fp);
		   @unlink($c["path"]."cache/".$tmpfilename);
		   $_SESSION[$this->name] = $this->value;
  	    }
  	  }	  	  
  	}
  	
  	/**
  	 * Check for correctness
  	 */
  	function check() {
  	  global $errors, $lang;
  	  if ($this->value == "" && $this->errortext == "") {
  	  	 $this->css="error";
  	  	 $this->errortext = "<br/>".$lang->get("must_upload", "You must upload a file to proceed!");
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