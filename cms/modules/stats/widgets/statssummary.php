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
class StatsSummary extends WUIInterface {
	
  var $headline;
  var $summaryType;
  var $cells;
  var $data;
  var $analyzedData;
  var $minTs;
  var $maxTs;
  var $median;
  var $total;
  var $average;
  var $dateFormat;
  var $container = '';
  var $weekdays = false;
  var $hours = false;
  var $spid = "";
    
  /**
   * Standard constructor
   * @param string Headline
   * @param string type name
   * @param integer Cells in table
   */
  function StatsSummary($headline, $summaryType, $cells=1, $pageOnly="") {
  	$this->headline = $headline;
  	$this->summaryType = $summaryType;
  	$this->cells = $cells;  	
  	$this->spid = $pageOnly;
  	$this->prepareStatsData();
  }
  
  /**
   * Add standard fields to the summary
   */
  function setStandardFields() {
  	global $lang;
  	$this->addNumberField($lang->get("total", "Total"), "total");
	$this->addNumberField($lang->get("average", "Average"), "average");
	$this->addNumberField($lang->get("median", "Median"), "median");
	$this->addNumberField($lang->get("maximum", "Maximum"), "maxTs");
	$this->addDateField($lang->get("maximumat", "Maximum at"), "maxTs");
	$this->addNumberField($lang->get("minimum", "Minimum"), "minTs");
	$this->addDateField($lang->get("minimumat", "Minimum at"), "minTs");
  }

  /**
   * Add minimum fields to the summary
   */
  function setMinimumFields() {
  	global $lang;
  	$this->addNumberField($lang->get("average", "Average"), "average");
	$this->addNumberField($lang->get("median", "Median"), "median");
	$this->addNumberField($lang->get("maximum", "Maximum"), "maxTs");
	$this->addNumberField($lang->get("minimum", "Minimum"), "minTs");
	
  }
  
  /**
   * Add a statistic data field with internal data
   * @param string $title in the row.
   * @param string $type in of the row.
   */
  function addNumberField($title, $type) {
    $value = $this->$type;
    if (is_array($value)) $value = $value[1];
    if (is_float($value)) $value = sprintf("%01.2f", $value);   	
    $this->addField($title, $value);
  }
  
  /**
   * Add a statistic data field with internal data
   * @param string $title in the row.
   * @param string $type in of the row.
   */
  function addDateField($title, $type) {
    $value = $this->$type;
    if (is_array($value)) $value = date($this->dateFormat, $value[0]);
    $this->addField($title, $value);
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
      echo '<td colspan="'.$this->cells.'" align="center"><table width="600" border="0" cellpadding="0" cellspacing="0"><tr>';
      $widget = new Label("lbl", '<br><br>'.$this->headline.'<br><br>', "h3", 2);
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
    function renderStatsData($legend) {
          $output = '<tr><td width="70%" class="underlined">'.$legend[0].'</td><td width="30%" class="underlined" align="right">'.$legend[1].'</td></tr>'."\n";	
       	  $this->container.=$output;	
    	  for ($i=0; $i < count($this->data); $i++) {
    	    if ($this->hours || $this->weekdays) {
    	       $this->addField($this->getLegend( $this->data[$i]["timestamp"]), $this->data[$i]["value"]);	  	  	    	  	
    	    } else {
    	      $this->addField(date($this->dateFormat, $this->data[$i]["timestamp"]), $this->data[$i]["value"]);	  	  	    	  	
    	    }
    	  }
   }
   
   /**
    * Add vertical space (40px)
    */
   function addSpacer() {
     $this->container.='<tr><td colspan="2">'.drawSpacer(40,40).'</td></tr>';	
   }
   
   /**
    * Take the input and calculate a weekday or an hour display.
    * @param string input 
    */
   function getLegend($input) {
   	if ($this->weekdays) {
   	  $wda = array("Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday");	
   	  return $wda[$input];
   	}
   	if ($this->hours) {
   	  return sprintf("%02d", $input).":00 - ".sprintf("%02d", $input+1).":00 h";	
   	}
   }

   /**
    * Retrieve the data needed for computation of this form
    */
    function prepareStatsData() {
      global $statsinfo;
      $timeRange = $statsinfo->getRangeArray();
      $this->dateFormat = $statsinfo->getDateFormat($timeRange['interval']);
      $timeRange['interval'] = $statsinfo->getIntervalTicks($timeRange['interval']);
      $diagram = $this->summaryType;
      $paramArray = array('api_call' => $this->summaryType);
      if (substr($diagram, 0, 8) == "nxhours:") {
         $paramArray = array('what' => substr($diagram, 8),
          'api_call' => "hours");
          $this->hours = true;
      } else if (substr($diagram, 0, 11) == "nxweekdays:") {
          $paramArray = array('what' => substr($diagram, 11),
          'api_call' => "weekdays");
          $this->weekdays = true;
         
      }
    if (substr($diagram, 0, 11) == "nxweekdays:") {
    $weekdays = true;
    $diagram = substr($diagram, 11);	
  }
      
      $constraint = array();
      if ($this->spid != "") {
        $constraint["constraints"] = array('document_string' => $this->spid);	
      }
      
      $this->data = phpOpenTracker::get(array_merge($paramArray, $timeRange, $constraint));	
      $this->minTs = getMinTs($this->data);
      $this->maxTs = getMaxTs($this->data);
      $this->median = getMedian($this->data);
      $this->total = getTotal($this->data);
      $this->average = getAverage($this->data);  
    }
	
	
}
	 
?>