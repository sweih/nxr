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
 *      $Id: wzselectcluster.php,v 1.2 2004/05/07 09:59:19 sven_weih Exp $ *
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
  * Widget for selecting a cluster in wizards.
  */
  class WZSelectCluster extends WZO {
  	  	
  	 var $ifo=null;
  	 
  	/**
  	 * Standard constructor
  	 * @param string Name of the widget
  	 * @param mixed NameValue-Array
  	 * @param integer Width of the Inputbox in pixel
  	 * @param integer Colspan of the widget.
  	 */
  	function WZSelectCluster($name, $width=300, $columns=1) {
  	  WZO::WZO($name);
  	  $this->ifo=new IFrameObject($this->name, $this->value, "ifclselector.php", "standard", $width, 250, $columns);
  	}
  	
  	/**
  	 * Check, whether selection was okay or not.
  	 */
  	function check() {  		
  		$this->ifo->check();
  	}
  	
  	/**
  	 * Draw the widget
  	 */
  	function draw() {  		
		$this->ifo->draw();
  	}
  	
  }