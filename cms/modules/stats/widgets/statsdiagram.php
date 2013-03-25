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
class StatsDiagram extends WUIInterface {
	
  var $headline;
  var $width;
  var $height;
  var $diagramType;
  var $cells;
  var $legend = null;
  var $spid;
  
  /**
   * Standard constructor
   * @param string Headline
   * @param string Diagram type name
   * @param integer Width of Diagram
   * @param integer Height of Diagram
   * @param integer Cells in table
   */
  function StatsDiagram($headline, $diagramType, $legend=null, $width=600, $height=250, $cells=3, $pageOnly="") {
  	$this->headline = $headline;
  	$this->diagramType = $diagramType;
  	$this->width = $width;
  	$this->height = $height;
  	$this->cells = $cells;  	
  	$this->legend = $legend;
  	$this->spid = $pageOnly;
  }
  
  /**
   * Draw HTML output
   */
   function draw() {
      global $c, $sid;
      echo '<td colspan="'.$this->cells.'"><table width="100%" border="0" cellpadding="0" cellspacing="0"><tr>';
      $widget = new Cell("clc", "", 1, $this->width,40);
      $widget->draw();
      echo "</tr><tr>\n";		
      $widget = new Label("lbl", $this->headline, "headbox", 1);
      $widget->draw();
      echo "</tr><tr>\n";			
      $widget = new FormImage($c["docroot"]."modules/stats/widgets/statsimage.php?sid=$sid&diagram=".$this->diagramType."&width=".$this->width."&height=".$this->height."&spid=".$this->spid, $this->width,$this->height, 1);
      $widget->draw();
      // Draw Legend...
      echo "</tr><tr>\n";			
      $widget = new Cell("clc", "", 1, $this->width,10);
      $widget->draw();     
      $colors[0] = __RED;
      $colors[1] = __BLUE;
      $colors[2] = __YELLOW;
      $colors[3] = __GREEN;     
      for ($i=0; $i < count($this->legend); $i++) {
        echo "</tr><tr><td>";
        echo '<table width="100%" border="0" cellpadding="2" cellspacing="0"><tr>';
        echo '<td width="10">'.($i+1).'.</td>';
        echo '<td width="11">';
        echo '<table width="11" height="11" border="0" cellspacing="0" cellpadding="0"><tr><td style="background-color:'.$colors[$i].';">'.drawSpacer(11,11).'</td></tr></table>';
        echo '</td>';
        echo '<td>'.$this->legend[$i].'</td>';
        echo '</tr></table></td>';
      }     
      echo "</tr></table></td>";
      return $this->cells;	
   }
	
	
}
	 
?>