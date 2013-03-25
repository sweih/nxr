<?php

/**
* N/X API - Returning Visitors
*
* @author   Sven Weih <sven@nxsystems.org>
*/
class phpOpenTracker_API_nxreturning_visitors extends phpOpenTracker_API_Plugin {
  var $apiCalls = array('nxreturning_visitors', 'nxavg_time_between_visits', 'nxone_time_visitors', 'nxavg_visits');
  var $apiType = 'get';

  /**
  * Runs the phpOpenTracker API call.
  *
  * @param  array $parameters
  * @return mixed
  * @access public
  */
  function run($parameters) {
    if ($parameters["api_call"] == "nxreturning_visitors") return $this->_run_returning_visitors($parameters);
    if ($parameters["api_call"] == "nxavg_time_between_visits") return $this->_run_time_between_visits($parameters);   
    if ($parameters["api_call"] == "nxone_time_visitors") return $this->_run_one_time_visitors($parameters);  
    if ($parameters["api_call"] == "nxavg_visits") return $this->_run_avg_visits($parameters);  
  }
  
  
  /**
   * Return average visits per visitor.
   * @param mixed parameter array
   */ 
  function _run_avg_visits($parameters) {
    	$allParams = $parameters;
  	$allParams["api_call"] = "visits";
  	$visits = phpOpenTracker::get($allParams);
  	
  	$allvParams = $parameters;
  	$allvParams["api_call"] = "nxvisitors";
  	$visitors = phpOpenTracker::get($allvParams);
  	
  	return ($visitors==0)? 0 : (float)($visits/$visitors);
  }
  
  /**
   * Return one time visitors.
   * @param mixed parameter array
   */ 
  function _run_one_time_visitors($parameters) {
  	$allParams = $parameters;
  	$allParams["api_call"] = "visitors";
  	$visitors = phpOpenTracker::get($allParams);
  	
  	$returningParams = $parameters;
  	$returningParams["api_call"] ="nxreturning_visitors";
  	$returningVisitors = phpOpenTracker::get($returningParams);
  	return $visitors - $returningVisitors;
  }
  
  
  /**
   * Return the average between two visits
   * @param mixed parameter array
   */
  function _run_time_between_visits($parameters) {
    $visitors = $this->queryDB($parameters);
    $timeCount = 0;
    $visitCount = 0;
    if (!empty($visitors)) {
      $keys    = array_keys($visitors);
      $numKeys = sizeof($keys);
      for ($i = 0; $i < $numKeys; $i++) {
        $visitors[$keys[$i]]['num_visits'] = sizeof($visitors[$keys[$i]]['accesslog_ids']);
        $visitors[$keys[$i]]['visitor_id'] = $keys[$i];

        if ($visitors[$keys[$i]]['num_visits'] > 1) {
          for ($j = 0; $j < $visitors[$keys[$i]]['num_visits'] - 1; $j++) {
            $timeCount += ($visitors[$keys[$i]]['timestamps'][$j+1] -
                      	   $visitors[$keys[$i]]['timestamps'][$j]);
            $visitCount++;
          }
        }
      }
      return ($visitCount==0)? 0 : (int)floor($timeCount/$visitCount);
    }
    return 0;
   }
  
  /**
   * Return the amount of returning visitors.
   * @param mixed parameter array
   */
  function _run_returning_visitors($parameters) {
      $this->db->query(
      sprintf(
        "SELECT count(visitors.visitor_id) as CALC
           FROM %s visitors
          WHERE visitors.returning_visitor = 1
                %s
      ",

        $this->config['visitors_table'],
        $this->_whereTimerange(
          $parameters['start'],
          $parameters['end'],
         'visitors'
        )
      )
    );	
    
    if ($row=$this->db->fetchRow()) return (integer)($row["CALC"]);
    return 0;
  }
  
  /**
   * Query the database for returning visitors.
   * Returns array with IDs of returning visitors.
   */
  function queryDB($parameters) {
      $visitors = array();
      $this->db->query(
      sprintf(
        "SELECT visitors.accesslog_id AS accesslog_id,
                visitors.visitor_id   AS visitor_id,
                visitors.timestamp    AS timestamp
           FROM pot_accesslog accesslog,
           %s visitors
          WHERE visitors.returning_visitor = 1
          AND visitors.accesslog_id = accesslog.accesslog_id
                %s
          ORDER BY visitors.visitor_id,
                   visitors.timestamp",

        $this->config['visitors_table'],
        $this->_whereTimerange(
          $parameters['start'],
          $parameters['end']
        )
      )
    );

    while ($row = $this->db->fetchRow()) {
      $visitors[$row['visitor_id']]['accesslog_ids'][] = $row['accesslog_id'];
      $visitors[$row['visitor_id']]['timestamps'][]    = $row['timestamp'];
    }	
    return $visitors;	
  }
  
}

//
// "phpOpenTracker essenya, gul meletya;
//  Sebastian carneron PHP."
//
?>
