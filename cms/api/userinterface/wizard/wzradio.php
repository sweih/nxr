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
 *      $Id: wzradio.php,v 1.1 2004/03/31 15:11:11 fabian_koenig Exp $ *
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
  class WZRadio extends WZO {
  	
  	var $nameValueArray;
  	
  	/**
  	 * Standard constructor
  	 * @param string Name of the widget
  	 * @param mixed NameValue-Array
  	 */
  	function WZRadio($name, $nameValueArray) {
  	  WZO::WZO($name);	
  	  $this->nameValueArray = $nameValueArray;
  	}
  	
  	/**
  	 * Draw the radios
  	 */
  	function draw() {
  		echo "<td>";
  		echo tableStart();
  		echo "<td colspan=\"2\">".drawSpacer(2,5)."</td>";
  		tr();
  		$checked="";
  		if ($this->value == "") $checked="checked";
  		for ($i=0; $i < count($this->nameValueArray); $i++) {
  		  if ($this->nameValueArray[$i][1] == $this->value) $checked="checked";
  		  echo '<td valign="middle" class="standardlight" align="right">';
  		  echo "<input type=\"radio\" name=\"$this->name\" id=\"$this->name\" value=\"".$this->nameValueArray[$i][1]."\" $checked >&nbsp;";
  		  echo "</td>";  		  
  		  echo '<td width="95%" valign="middle" class="standardlight">';
  		  echo $this->nameValueArray[$i][0];
  		  echo '</td>';
  		  tr();
  		  $checked="";
  		}
  		echo "<td colspan=\"2\">".drawSpacer(1,5)."</td>";
  		echo tableEnd();
  		echo "</td>";
  	}
  	
  }
 
 ?>