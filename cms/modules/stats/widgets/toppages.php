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
class TopPages extends WUIInterface {
	
  var $headline;
  var $summaryType;
  var $cells;
  var $data;
  var $dateFormat;
  var $container = '';
    
  /**
   * Standard constructor
   * @param string Headline
   * @param string type name
   * @param integer Cells in table
   */
  function TopPages($headline, $summaryType, $cells=1) {
  	global $lang;
  	$this->headline = $headline;
  	$this->summaryType = $summaryType;
  	$this->cells = $cells;  	
  	$this->prepareStatsData();
  	$this->renderStatsData(array($lang->get("rank", "Rank"), $lang->get("page", "Page")));
  }
  
  
  /**
   * Add a statistic data field with internal data
   * @param string $title in the row.
   * @param string $value in of the row.
   */
  function addField($title, $value) {
    $output = '<tr><td width="70%" class="underlined">'.$title.'</td><td width="30%" class="underlined" align="right">'.$value.'</td></tr>'."\n";	
    $this->container.=$output;	
  }
  
  /**
   * Draw HTML output
   */
   function draw() {
      global $c, $sid;
      echo '<td colspan="'.$this->cells.'" valign="top"><table width="100%" border="0" cellpadding="0" cellspacing="0"><tr>';
      $widget = new Cell("clc", "", 4, $this->width,20);
      $widget->draw();
      echo "</tr><tr>\n";		
      $widget = new Label("lbl", '<h2>'.$this->headline.'</h2>', "", 4);
      $widget->draw();
      echo "</tr>\n";
      echo $this->container;
      echo "</tr></table></td>";
      return $this->cells;	
   }
   
   /**
    * prepare Statistic data
    * @param array (0=>timeframe, 1=>valuetype)
    */
    function renderStatsData() {
          global $lang;
          $output = '<tr><td width="10%" class="underlined"><b>'.$lang->get("rank", "Rank").'</b></td><td width="40%" class="underlined"><b>'.$lang->get("page", "Page").'</b></td><td width="25%" class="underlined" align="right"><b>'.$lang->get("count", "Count").'</b></td><td width="25%" class="underlined" align="right"><b>'.$lang->get("percent", "Percent").'</b></td></tr>'."\n";	
       	  $this->container.=$output;	
     	  for ($i=0; $i < count($this->data["top_items"]); $i++) {
    	    $this->container.= "<tr>";
    	    $this->container.= '<td class="underlined">'.($i+1).'</td>';
    	    $this->container.= '<td class="underlined">'.resolvePageToLink($this->data["top_items"][$i]["string"]).'</td>';
    	    $this->container.= '<td class="underlined" align="right">'.$this->data["top_items"][$i]["count"].'</td>';
    	    $this->container.= '<td class="underlined" align="right">'.sprintf("%01.2f", $this->data["top_items"][$i]["percent"]).' %</td>';
    	    $this->container.= '</tr>';
    	  }
   }
   
   /**
    * Add vertical space (40px)
    */
   function addSpacer() {
     $this->container.='<tr><td colspan="2">'.drawSpacer(40,40).'</td></tr>';	
   }

   /**
    * Retrieve the data needed for computation of this form
    */
    function prepareStatsData() {
      global $statsinfo;
      $timeRange = $statsinfo->getRangeArray();
      $timeRange['interval'] = $statsinfo->getIntervalTicks($timeRange['interval']);
      $this->data = phpOpenTracker::get(array_merge(array('api_call' =>'top', 'what' =>  $this->summaryType, 'limit' => $statsinfo->limit), $timeRange));	

    }
	
	
}
	 
?>