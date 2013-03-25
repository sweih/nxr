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
class StatsClickpath extends WUIInterface {
	
  var $headline;
  var $summaryType;
  var $cells;
  var $data;
  var $container = '';
    
  /**
   * Standard constructor
   * @param string Headline
   * @param string type name
   * @param integer Cells in table
   */
  function StatsClickpath($headline, $summaryType, $cells=1) {
  	$this->headline = $headline;
  	$this->summaryType = $summaryType;
  	$this->cells = $cells;  	
  	$this->prepareStatsData();
  	$this->renderStatsData();
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
      echo '<td colspan="'.$this->cells.'"><table width="600" border="0" cellpadding="0" cellspacing="0"><tr>';
      $widget = new Cell("clc", "", 2, $this->width,20);
      $widget->draw();
      echo "</tr><tr>\n";		
      $widget = new Label("lbl", '<br><h3>'.$this->headline.'</h3>', '', 2);
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
  	    $output = '<tr><td width="70%" class="underlined">'.$lang->get("path", "Path").'</td><td width="30%" class="underlined" align="right">'.$lang->get("count", "Count").'</td></tr>'."\n";	
        $this->container.=$output;	   	  
    	  for ($i=0; $i < count($this->data); $i++) {
    	    $spc="";
    	    $do = $this->data[$i];
    	    $paths = "";
    	    $count = $do->count;
    	    
    	    for ($j=0; $j < $do->length; $j++) {
    	      $paths.=$spc.($j+1).". ".resolvePageToLink($do->documents[$j])."<br>";		
    	      $spc.="&nbsp;";
    	    }
    	    $this->container.= '<tr><td width="70%" class="underlined">'.$paths.'</td><td width="30%" class="underlined" align="right" valign="top">'.$count.'</td></tr>'."\n";		    
    	  }
   }
   
   

   /**
    * Retrieve the data needed for computation of this form
    */
    function prepareStatsData() {
      global $statsinfo;
      $timeRange = $statsinfo->getRangeArray();
      unset($timeRange['interval']);
      $timeRange['limit'] = (int)$statsinfo->limit;
      $paramArray = array('api_call' => $this->summaryType, 'result_format' => 'array');      
      $this->data = phpOpenTracker::get(array_merge($paramArray, $timeRange));	
    }
	
	
}
	 
?>