<?php

/**
* N/X Top API
*
* @author   Sven Weih <sven@weih.de>
*/
class phpOpenTracker_API_hours extends phpOpenTracker_API_Plugin {
  var $apiCalls = array('hours');
  var $apiType = 'get';
  var $whereString = "";

  /**
  * Runs the phpOpenTracker API call.
  *
  * @param  array $parameters
  * @return mixed
  * @access public
  */
  function run($parameters) {
    if (!isset($parameters['what'])) {
      return phpOpenTracker::handleError(
        'Required parameter "what" missing.'
      );
    }

    list($constraint, $selfJoin) = $this->_constraint(
      $parameters['constraints'],
      true
    );

    if ($selfJoin) {
      $selfJoinConstraint = 'AND accesslog.accesslog_id = accesslog2.accesslog_id';

      $selfJoinTable = sprintf(
        '%s accesslog2,',

        $this->config['accesslog_table']
      );
    } else {
      $selfJoinConstraint = '';
      $selfJoinTable      = '';
    }

    $timerange = $this->_whereTimerange(
      $parameters['start'],
      $parameters['end']
    );

    if ($parameters["start"] > 0) {
       $this->whereString = "AND starttime > ".$parameters["start"]." AND endtime < ".$parameters["end"]." ";
    }
    
    if ($parameters['what'] == 'pi') return $this->_run_pi($parameters);
    if ($parameters['what'] == 'visits') return $this->_run_visits($parameters);
    if ($parameters['what'] == 'avg_clickstream') return $this->_run_avg_clickstream($parameters);
    if ($parameters['what'] == 'avg_time') return $this->_run_avg_time($parameters);
  }
  
 
  /**
   * Calculate the top days in time range
   * @param mixed Parameter Array
   */
  function _run_avg_time($parameters) {
    $result = array();
    for ($i=0; $i<24; $i++) {
    	$this->db->query("SELECT AVG(duration) AS CALC FROM pot_nxlog WHERE hour = $i ".$this->whereString."");
    	$row = $this->db->fetchRow();
    	$day = array("timestamp" => $i, "value" => ($row["CALC"] == NULL)? 0:(int)($row["CALC"]));
    	array_push($result, $day);
    } 	
    return $result;	
  }

  /**
   * Calculate the top days in time range
   * @param mixed Parameter Array
   */
  function _run_avg_clickstream($parameters) {
    $result = array();
    for ($i=0; $i<24; $i++) {
    	$this->db->query("SELECT AVG(pi) AS CALC FROM pot_nxlog WHERE hour = $i ".$this->whereString."");
    	$row = $this->db->fetchRow();
    	$day = array("timestamp" => $i, "value" => ($row["CALC"] == NULL)? 0:(int)$row["CALC"]);
    	array_push($result, $day);
    } 	
    return $result;	
  }
 
  /**
   * Calculate the days in time range
   * @param mixed Parameter Array
   */
  function _run_visits($parameters) {
    $result = array();
    for ($i=0; $i<24; $i++) {
    	$this->db->query("SELECT COUNT( pi ) AS CALC FROM pot_nxlog WHERE hour = $i ".$this->whereString."");
    	$row = $this->db->fetchRow();
    	$day = array("timestamp" => $i, "value" => ($row["CALC"] == NULL)? 0:(int)$row["CALC"]);
    	array_push($result, $day);
    } 	
    return $result;	
  }
 
  /**
   * Calculate the days in time range
   * @param mixed Parameter Array
   */
  function _run_pi($parameters) {
    $result = array();
    for ($i=0; $i<24; $i++) {
    	$this->db->query("SELECT SUM( pi ) AS CALC FROM pot_nxlog WHERE hour = $i ".$this->whereString."");
    	$row = $this->db->fetchRow();
    	$day = array("timestamp" => $i, "value" => ($row["CALC"] == NULL)? 0:(int)$row["CALC"]);
    	array_push($result, $day);
    } 	
    return $result;	
  }
  
}

?>