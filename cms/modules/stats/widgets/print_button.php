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
	 
/**
 * Class for painting diagrams for statistic reasons
 */
class PrintButton extends WUIInterface {
  
      
  /**
   * Standard constructor
   * @param string Headline
   * @param string type name
   * @param integer Cells in table
   */
  function PrintButton() {
  }
  
  
  /**
   * Draw HTML output
   */
   function draw() {
      global $c, $sid, $lang;
      echo '<td colspan="3"><table width="100%" border="0" cellpadding="3" cellspacing="0"><tr>';
      $widget = new Cell("clc", "", 2, $this->width,20);
      $widget->draw();
      echo "</tr><tr>\n";		
      echo '<td colspan="2"align="right">&nbsp;</td>';
      echo '<td align="right">';
      $lbi = new Button("action", $lang->get("print", "print"), "navelement", "button", "window.open('".$_SERVER['REQUEST_URI']."&print=1');");
      $lbi->draw();
      echo "&nbsp;&nbsp;";
      $lbi = new Button("action", $lang->get("refresh", "refresh"), "navelement", "button", "document.location.href = document.location.href;");
      $lbi->draw();
      retain("action", "");
      retain("sid", $sid);
      echo "</td></tr></table></td>";
      return $this->cells;	
   }
  

	
}
	 
?>