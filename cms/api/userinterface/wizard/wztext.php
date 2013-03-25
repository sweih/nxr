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
 *      $Id: wztext.php,v 1.1 2004/03/31 15:11:11 fabian_koenig Exp $ *
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
  * Text-Input-Field for wizards
  */
  class WZText extends WZO {
  	
  	var $type="";
  	var $label;
  	var $mandatory;
  	var $css="standardlight";
  	
  	/**
  	 * Standard constructor
  	 * @param string Name of the widget
  	 * @param string Label of the widget
  	 * @param string type of the input TEXT|TEXTAREA
  	 * @param boolean can this field not be left empty?
  	 */
  	function WZText($name, $label, $type="TEXT", $mandatory=false) {
  	  WZO::WZO($name);	
  	  $this->type=strtoupper($type);
  	  $this->label = $label;
  	  $this->mandatory = $mandatory;
  	}
  	
  	/**
  	 * Draw the radios
  	 */
  	function draw() {
  		echo "<td>";
  		echo tableStart();
  		td($this->css);
  		echo drawSpacer(2,5);
  		tde();
  		tr();
  		td($this->css);
  		echo $this->label;
  		tde();
  		tr();  		
  		if ($this->type=="TEXTAREA") {
  			$tb = new Textarea($this->name, $this->value, $this->css, 4, "width:250", 260);
  		} else {
  			$tb = new Input($this->name, $this->value, $this->css, 256, "width:250", 260);
  		}
  		$tb->draw();
  		echo tableEnd();
  		echo "</td>";
  	}
  	
  }
 
 ?>