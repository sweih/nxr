<?php
	/**********************************************************************
	 *	N/X - Web Content Management System
	 *	Copyright 2003 Sven Weih
	 *
	 *	This file is part of N/X.
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
   * Parses an array of form array(array(timestamp, value))).
   * @param mixed timestamp value
   */
  function getMinTs($statsArray) {
    $minValue = null;
    $minTs = null;
    for ($i=0; $i<count($statsArray[0]); $i++) {
      	if ($statsArray[$i]["value"] < $minValue || $minValue == null) {
      	  $minValue = $statsArray[$i]["value"];
      	  $minTs = $statsArray[$i]["timestamp"];	
      	}
    }
    return array($minTs, $minValue);
  }	 

  /**
   * Parses an array of form array(array(timestamp, value))).
   * @param mixed timestamp value
   */
  function getMaxTs($statsArray) {
    $maxValue = null;
    $maxTs = null;
    for ($i=0; $i<count($statsArray); $i++) {
      	if ($statsArray[$i]["value"] > $maxValue || $maxValue == null) {
      	  $maxValue = $statsArray[$i]["value"];
      	  $maxTs = $statsArray[$i]["timestamp"];	
      	}
    }
    return array($maxTs, $maxValue);;
  }
  
  /**
   * Get Median of Statistic Data.
   * @param mixed timestamp value
   */
  function getMedian($statsArray) {
    $sa = tssort ($statsArray);
    if (count($sa) % 2 == 0){
	$median = (($sa[count($sa)/2]['value'])+($sa[1+(count($sa)/2)]['value']))/2;
    } else {
	$median = $sa[floor(count($sa)/2)]['value'];
    }
    return $median;	
  }
  
  /**
   * Calculate the average value.
   * @param mixed timestamp value
   */
  function getAverage($statsArray) {
    return floor(getTotal($statsArray) / count($statsArray));   	
  }
  
  /**
   * Return Sum of all values
   * @param mixed timestamp value
   */
  function getTotal($statsArray) {
    $result = 0;
    for ($i=0; $i<count($statsArray); $i++) {
    	$result += $statsArray[$i]['value'];
    }
    return $result;	
  }
  
  /**
   * Sorts a timestamp array
   */
  function tssort($tsArray) {
    for ($i=0; $i<count($tsArray); $i++) {
      for ($j=0; $j < count($tsArray); $j++) {	
    	if ($tsArray[$i]["value"] > $tsArray[$j]["value"]) {
    	  $temp = $tsArray[$i];
    	  $tsArray[$i] = $tsArray[$j];
    	  $tsArray[$j] = $temp;
    	}
      }
    }
    return $tsArray;	
  }	
  	 
?>