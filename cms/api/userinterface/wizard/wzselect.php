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
 *      $Id: wzselect.php,v 1.1 2004/03/31 15:11:11 fabian_koenig Exp $ *
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
  * Radio-Selector for wizards
  */
  class WZSelect extends WZO {
  	
  	var $nameValueArray;
  	var $label;
  	
  	/**
  	 * Standard constructor
  	 * @param string Name of the widget
  	 * @param mixed NameValue-Array
  	 */
  	function WZSelect($name, $label, $nameValueArray) {
  	  WZO::WZO($name);	
  	  $this->label = $label;
  	  $this->nameValueArray = $nameValueArray;
  	}
  	
  	/**
  	 * Check, whether selection was okay or not.
  	 */
  	function check() {
  		global $lang, $errors;
  		if (value($this->name, "NUMERIC") == "-1") {
  			$this->error=true;
  			$this->errortext= "<br/>".$lang->get("MANDATORY");
  			$errors.="-MANDATORY";
  		}
  	}
  	
  	/**
  	 * Draw the widget
  	 */
  	function draw() {
  		$css="standardlight";
  		if ($this->error) $css='error';
  		echo "<td class=\"$css\">";  		
  		echo tableStart();
  		echo "<td colspan=\"2\">".drawSpacer(2,5)."</td>";
  		tr();
  		if ($this->value == "") $checked="checked";
  		echo '<td class="'.$css.'">'.$this->label.$this->errortext;
  		echo "</td>";  		  
  		tr();
  		$sb = new Dropdown($this->name, $this->nameValueArray, "$css", $this->value, "200");
  		$sb->draw();
  		tr();  		    		
  		echo "<td colspan=\"2\">".drawSpacer(1,5)."</td>";
  		echo tableEnd();
  		echo "</td>";
  	}
  	
  }