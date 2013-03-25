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
class WiPageSelector extends WUIInterface {
  
  var $sps;
    
  /**
   * Standard constructor
   * @param string Headline
   * @param string type name
   * @param integer Cells in table
   */
  function WiPageSelector($cells=1) {
  	$this->cells = $cells;  
  	$this->sps = new SitepageSelector2();	
  	$this->value = $this->sps->value;
  }
  
  
  /**
   * Draw HTML output
   */
   function draw() {
      global $c, $sid, $lang;
      echo '<form name="form1">';
      echo '<td colspan="'.$this->cells.'"><table width="100%" border="0" cellpadding="3" cellspacing="0"><tr>';
      $widget = new Cell("clc", "", 2, $this->width,20);
      $widget->draw();
      echo "</tr><tr>\n";		
      $widget = new Label("lbl", $this->headline, "stats_headline", 2);
      $widget->draw();
      echo "</tr>\n";
      echo "<tr><td colspan=\"".$this->cells."\" class=\"bcopy\">".$lang->get("pta", "Select page to analyze:")."</td></tr>";
      echo "<tr>";
      $this->sps->draw();
      echo "<td> ";
      $lbi = new LinkButton("action", $lang->get("go", "Go"), "navelement", "submit");
      $lbi->draw();
      retain("action", "");
      retain("sid", $sid);
      echo "</td></tr></table></td>";
      echo "</form>";
      return $this->cells;	
   }
  

	
}
	 
?>