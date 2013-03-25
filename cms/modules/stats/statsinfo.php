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
   * This class stores information about the timeframe, a statistic is to be generated and which
   * statistic data is to be drawn
   */
  class Statsinfo {
    var $dateview = "";
    var $preDefRange ="";
    var $indStart="";
    var $indEnd="";
    var $tsStart = 0;
    var $tsEnd = 0;
    var $interval = "";
    var $limit;
       
    /**
     * Standard constructor
     */
     function Statsinfo() {
       $this->initializeValues();
       $this->calculateTimestamps();
       $this->calculateInterval();	
     }		
  	
     
     /**
      * Returns a string describing the interval length
      */
     function getIntervalTitle() {
     	$ivname = $this->interval;
     	if ($ivname == "") {
     	  if (stristr($this->preDefRange, "day")) $ivname = "hour";
     	  if (stristr($this->preDefRange, "month")) $ivname = "day";
     	  if (stristr($this->preDefRange, "year")) $ivname = "month";     		
     	}
     	return $ivname;
     }
     
     /**
      * Return a string that displays the time Range
      */
     function getRangeTitle() {
        if ($this->dateview == "individual") {
      	  return date("l d. F Y", $this->tsStart)." - ".date("l d. F Y", $this->tsEnd);
       	} else if ($this->dateview == "predefined") {	
      	  $title = str_replace("_", " ", $this->preDefRange);
      	  $title = str_replace("current", "this", $title);
      	  $title = str_replace("previous", "last", $title);
      	  if ($title=="total") $title = "total range";		
      	  return "Statistics of <b>".$title."</b>. <br>Now it is ".date("l d. F Y")." , ".date("H:i")."h.";
      	} 
     }
     
     function getRangeArray() {
       $result = array();
       if ($this->tsStart != 0 || $this->tsEnd != 0) {
         $result['start'] = $this->tsStart;
         $result['end'] = $this->tsEnd;	
         $result['interval'] = $this->interval;
       } else {
       	 $result['range'] = $this->preDefRange;
       	 $result['interval'] = $this->interval;	 
       }
       return $result;	    	
     }
     
     /**
      * Fetch values from request or repository.
      */
     function initializeValues() {
     	$this->dateview = initValue("dateview", "sdateview", "predefined");
     	$this->preDefRange = initValue("itf", "sitf", "total");	
     	$this->indStart = initValue("startdate", "sstartdate", "2003-01-01");
     	$this->indEnd = initValue("enddate", "senddate", "2010-01-01");	
     	$this->limit = initValue("limit", "statslimit", "10");
     }	
     
     /**
      * Calculates the length of intervals
      */
      function calculateInterval() {
      	if ($this->dateview == "individual") {
      	  $days = ($this->tsEnd - $this->tsStart) / (3600 * 24);
      	  if ($days < 3) {
      	  	$this->interval = "hour";
      	  } else if ($days < 32) {
      	  	$this->interval = "day";
      	  } else if ($days < 366) {
      	  	$this->interval = "week"; //week;
       	  } else if ($days < 731) {
       	  	$this->interval = "month";
       	  } else {
       	  	$this->interval = "year";
       	  }
       	} else if ($this->dateview == "predefined") {	
      	  switch (strtolower($this->preDefRange)) {
       	   
       	   case 'today': {
       	     $this->interval = "hour";
       	     break;	
       	   }
       	   
       	   case 'yesterday': {
       	     $this->interval = "hour";
       	     break;	
       	   }
       	   
       	   case 'current_week': {
       	     $this->interval = "day";
       	     break;	
       	   }
       	   
       	   case 'previous_week': {
       	     $this->interval = "day";
       	     break;	
       	   }
       	   
       	   case 'current_month': {
       	     $this->interval = "day";
       	     break;	
       	   }
       	   
       	   case 'previous_month': {
       	     $this->interval = "day";
       	     break;	
       	   }
       	   
       	   case 'current_year': {
       	     $this->interval = "week";
       	     break;	
       	   }
       	   
       	   case 'previous_year': {
       	     $this->interval = "week";
       	     break;	
       	   }
       	        	   
       	   default: {
             break; 
           }	
       	 }	
      	}
      }

           
     /** 
      * calculate starttimestamp and endtimestamp of period
      */
     function calculateTimestamps() {
       if ($this->dateview == "individual") {
         $startDate = $this->parseDate($this->indStart);
         $endDate = $this->parseDate($this->indEnd);      
         $this->tsStart = mktime(0, 0, 1, $startDate["month"], $startDate["day"], $startDate["year"]);
         $this->tsEnd = mktime(23, 59, 59, $endDate["month"], $endDate["day"], $endDate["year"]);        
       } else if ($this->dateview == "predefined") {
       	 switch (strtolower($this->preDefRange)) {
       	   case 'current_week': {
       	     $this->tsStart = $this->mondayOfWeek(date('Y'), date('W'));
       	     $this->tsEnd = $this->sundayOfWeek(date('Y'), date('W'));
       	     break;	
       	   }
       	 }
       }
       
       if ($this->tsStart > $this->tsEnd) {
         $temp = $this->tsStart;
         $this->tsStart = $this->tsEnd;
         $this->tsEnd = $temp;
       } 
     }
    	
     
     /**
      * Calculate the monday of a weeknumber
      * @param integer year
      * @param integer weeknumber
      */
     function mondayOfWeek ($year,$week) {
  	   return mktime(0, 0, 0, 1, ($week + ((((date("w", mktime(0,0, 0, 1, 0, $year)) +.01 - 4)/abs(date("w", mktime(0, 0, 0, 1, 0, $year))+.01 - 4))/2) +.5)) * 7  - date("w", mktime(0, 0, 0, 1, $week * 7,$year))- 6, $year);
     }
     
     /**
      * Calculate sunday of weeknumber
      * @param integer year
      * @param integer weeknumber
      */
      function sundayOfWeek($year, $week) { 	
        return $this->mondayOfWeek($year, $week) + (((60*60) * 24) * 7)-1;
      }

      /**
       * parse date
       * @param string date in format yyyy-mm-dd
       */
       function parseDate($date) {
         $result["year"] = substr($date, 0,4);
         $delim1 = strpos($date, '-');
         $delim2 = strpos(substr($date, $delim1+1,8), '-') + $delim1;
         $result["month"]= substr($date, $delim1+1, $delim2-$delim1);
         $result["day"] = substr($date, $delim2+2,2);
         if ($result["year"] < 2000) $result["year"] = 2000;
         return $result;
       }
       
       
       /**
        * Gets the seconds of an interval
        * @param string Name of Interval
        */
       function getIntervalTicks($intervalname) {
       	 $steps = array(
      		'hour'  =>     3600,
      		'day'   =>    86400,
      		'week'  =>   604800,
      		'month' =>  2592000,
      		'year'  => 31536000
    	 );
    	 return ($steps[$intervalname] == "") ? $intervalname : $steps[$intervalname];    	
       }
       
       /**
        * Gets the seconds of an interval
        * @param string Name of Interval
        */
       function getDateFormat($intervalname) {
       	 $steps = array(
      		'hour'  =>     	'H:i',
      		'day'   =>    	'd. F',
      		'week'  =>   	'W. \w\e\e\k \o\f Y',
      		'month' =>  	'F Y',
      		'year'  => 	'Y'
    	 );
    	 return ($steps[$intervalname] == "") ? 'd. M. Y' : $steps[$intervalname];    	
       }
  }
	 
?>