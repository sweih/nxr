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
 *      $Id: wzlabel.php,v 1.2 2005/05/08 09:52:42 sven_weih Exp $ *
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
  * Text-Display
  */
  class WZLabel extends WZO {
  	
  	var $label;
  	var $css = "standardlight";
  	
  	/**
  	 * Standard constructor
  	 * @param string Label of the widget
  	 * @param string Link to a certain page
  	 */
  	function WZLabel($label, $linkto="") {
      global $c;
  	  WZO::WZO("label");	 	  
  	  $this->label = $label;
  	  if ($linkto != "") {
  	  	$this->label = '<a style="text-decoration:underline;" href="'.$c["docroot"].$linkto.'">'.$this->label.'</a>'; 	  	
  	  }
  	}
  	
  	/**
  	 * Draw the radios
  	 */
  	function draw() {
  		td($this->css);
  		echo drawSpacer(2,5);
  		tde();
  		tr();
  		td($this->css);
  		echo $this->label;
  		tde();
  	}
  	
  }
 
 ?>