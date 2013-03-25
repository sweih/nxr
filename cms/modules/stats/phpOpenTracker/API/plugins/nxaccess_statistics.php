<?php
//
// +---------------------------------------------------------------------+
// | phpOpenTracker - The Website Traffic and Visitor Analysis Solution  |
// +---------------------------------------------------------------------+
// | Copyright (c) 2000-2003 Sebastian Bergmann. All rights reserved.    |
// +---------------------------------------------------------------------+
// | This source file is subject to the phpOpenTracker Software License, |
// | Version 1.0, that is bundled with this package in the file LICENSE. |
// | If you did not receive a copy of this file, you may either read the |
// | license online at http://phpOpenTracker.de/license/1_0.txt, or send |
// | a note to license@phpOpenTracker.de, so we can mail you a copy.     |
// +---------------------------------------------------------------------+
//
// $Id: nxaccess_statistics.php,v 1.2 2003/09/08 21:28:38 sven_weih Exp $
//

/**
* phpOpenTracker API - Access Statistics
*
* @author   Sven Weih <sven@nxsystems.org>
*	    initial by Sebastian Bergmann
*/
class phpOpenTracker_API_nxaccess_statistics extends phpOpenTracker_API_access_statistics {
  /**
  * API Calls
  *
  * @var array $apiCalls
  */
  var $apiCalls = array(
    'avg_clickstream',
    'avg_visit_length',
    'nxvisitors'   
  );

  var $apiType = 'get';

  /**
  * Runs the phpOpenTracker API call.
  *
  * @param  array $parameters
  * @return mixed
  * @access public
  */
  function run($parameters) {
    if ($parameters['api_call'] == 'avg_clickstream') return $this->_avg_clickstream($parameters);
    if ($parameters['api_call'] == 'avg_visit_length') return $this->_avg_visit_length($parameters);
    if ($parameters['api_call'] == 'nxvisitors') return $this->_nxvisitors($parameters);
  }
  
  
  /**
   * Run the api-call for average visit length
   *
   * @param  array $parameters
   * @return mixed
   * @access private
   */
  function _avg_visit_length($parameters) {
    $result = array();
    
    $steps = array(
      'hour'  =>     3600,
      'day'   =>    86400,
      'week'  =>   604800,
      'month' =>  2592000,
      'year'  => 31536000
    );
    
    $start = $parameters["start"];
    $end = $parameters["end"];
    
    $interval = $steps[$parameters["interval"]];
    if ($interval =="") $interval = $parameters["interval"];
    
    if ($interval != "") { 
      $j=0;
      for ($i=$start; $i<$end; $i= $i + $interval) {
    	  $this->db->query("SELECT AVG(duration) AS CALC FROM pot_nxlog WHERE starttime > $i AND endtime < ".($i+$interval));
    	  $result[$j]["timestamp"] = (int) $i;
      	  if ($row = $this->db->fetchRow()) {      		
        		$result[$j]['value']= (int) $row['CALC'];
    	  } else {
     	 	$result[$j]['value'] = 0;
    	  }  
    	  $j++;
      }
    } else {
       $this->db->query("SELECT AVG(duration) AS CALC FROM pot_nxlog WHERE starttime > $start AND endtime < $end");
       if ($row = $this->db->fetchRow()) {      		
          $result = (int) $row['CALC'];
       }	
    }
    return $result;
  }
  
  /**
   * Return the Visitors of the page
   * @return mixed
   * @access private
   */
  function _nxvisitors_old($parameters) {
   $result = array();
    
    $steps = array(
      'hour'  =>     3600,
      'day'   =>    86400,
      'week'  =>   604800,
      'month' =>  2592000,
      'year'  => 31536000
    );
    
    $start = $parameters["start"];
    $end = $parameters["end"];
    
    $interval = $steps[$parameters["interval"]];
    if ($interval =="") $interval = $parameters["interval"];
    $correct = 0;
    if ($interval == 604800) {
    	$correct = 86400;
    }
    echo $correct;
    if ($interval != "") { 
      $j=0;
      for ($i=$start; $i<$end; $i= $i + $interval) {
    	  $this->db->query("SELECT COUNT(DISTINCT pot_visitors.visitor_id) as CALC FROM pot_visitors, pot_accesslog WHERE pot_accesslog.accesslog_id = pot_visitors.accesslog_id AND pot_visitors.timestamp > ". ($i-$correct) ." AND pot_visitors.timestamp < ".($i+$interval - $correct));
    	  $result[$j]["timestamp"] = (int) $i;
      	  if ($row = $this->db->fetchRow()) {      		
        		$result[$j]['value']= (int) $row['CALC'];
    	  } else {
     	 	$result[$j]['value'] = 0;
    	  }  
    	  $j++;
      }
    } else {
       $this->db->query("SELECT COUNT(DISTINCT pot_visitors.visitor_id) as CALC FROM pot_visitors, pot_accesslog WHERE pot_accesslog.accesslog_id = pot_visitors.accesslog_id AND pot_visitors.timestamp > $start AND pot_visitors.timestamp < ".$end);
       if ($row = $this->db->fetchRow()) {      		
          $result = (int) $row['CALC'];
       }	
    }
    return $result;  	
  }
  
  
  function _nxvisitors($parameters) {
    $parameters['interval'] = isset($parameters['interval']) ? $parameters['interval'] : false;

    $intervalStrings = array();
    $timestamps      = array();
    $values          = array();

    switch ($parameters['result_format']) {
      case 'csv': {
        if ($parameters['api_call'] == 'page_impressions') {
          $csv = "Interval;Page Impressions\n";
        } else {
          $csv = "Interval;Visitors\n";
        }
      }
      break;

      case 'xml':
      case 'xml_object': {
        $tree = new XML_Tree;

        if ($parameters['api_call'] == 'page_impressions') {
          $root = &$tree->addRoot('pageimpressions');
        } else {
          $root = &$tree->addRoot('visitors');
        }
      }
      break;

      default: {
        $result = array();
      }
    }

    if ($parameters['interval'] != false) {
      $start = $parameters['start'] ? $parameters['start'] : 0;
      $end   = $parameters['end']   ? $parameters['end']   : time();

      for ($i = $parameters['start']; $i < $parameters['end']; $i += $parameters['interval']) {
        $correct = ((mktime(0, 0, 0, date('m', $i), date('d', $i) + 1, date('Y', $i))
                   - mktime(0, 0, 0, date('m', $i), date('d', $i),     date('Y', $i)))
                   * ($parameters['interval'] / 86400))
                   - $parameters['interval'];

        // Correct weeks
        $j = $i;
        if ($parameters['interval'] == 604800) {        	
        	$j = $i + 86400;
        }
        
        $intervalStrings[] = sprintf(
          '%s - %s',

          date('d-m-Y', $j),
          date('d-m-Y', $j + $parameters['interval'] + $correct)
        );

        $values[] = phpOpenTracker::get(
          array(
            'client_id'   => $parameters['client_id'],
            'api_call'    => $parameters['api_call'],
            'start'       => $j,
            'end'         => $j + $parameters['interval'] + $correct,
            'constraints' => $parameters['constraints']
          )
        );

        $timestamps[] = $i;

        $i += $correct;
      }
    } else {
      $this->db->query(
        sprintf(
          "SELECT %s AS result
             FROM %s accesslog,
                  %s visitors
            WHERE visitors.client_id    = '%d'
              AND visitors.accesslog_id = accesslog.accesslog_id
                  %s
                  %s",

          ($parameters['api_call'] == 'page_impressions') ? 'COUNT(*)' : 'COUNT(DISTINCT(visitors.accesslog_id))',
          $this->config['accesslog_table'],
          $this->config['visitors_table'],
          $parameters['client_id'],
          $this->_constraint($parameters['constraints']),
          $this->_whereTimerange(
            $parameters['start'],
            $parameters['end'],
            'accesslog'
          )
        )
      );

      if ($row = $this->db->fetchRow()) {
        $values = array(intval($row['result']));
      } else {
        $values = array(0);
      }

      if ($parameters['start'] != false &&
          $parameters['end']   != false) {
        $intervalStrings = array(
          sprintf(
            '%s - %s',

            date('d-m-Y', $parameters['start']),
            date('d-m-Y', $parameters['end'])
          )
        );
      } else {
        $intervalStrings = array('');
      }
    }

    switch ($parameters['result_format']) {
      case 'csv': {
        for ($i = 0; $i < sizeof($values); $i++) {
          $csv .= sprintf(
            "%s;%d\n",

            $intervalStrings[$i],
            $values[$i]
          );
        }

        return $csv;
      }
      break;

      case 'xml':
      case 'xml_object': {
        for ($i = 0; $i < sizeof($values); $i++) {
          $intervalChild = &$root->addChild('interval');

          $intervalChild->addChild('interval', $intervalStrings[$i]);
          $intervalChild->addChild('value',    $values[$i]);
        }

        if ($parameters['result_format'] == 'xml') {
          return $root->get();
        } else {
          return $root;
        }
      }
      break;

      default: {
        if (sizeof($values) == 1) {
          return $values[0];
        } else {
          $result = array();

          for ($i = 0; $i < sizeof($values); $i++) {
            $result[] = array(
              'timestamp' => $timestamps[$i],
              'value'     => $values[$i]
            );
          }
        }

        return $result;
      }
    }
  }  
  
  
  
  /**
   * Run the api-call for average clickstream
   *
  * @param  array $parameters
  * @return mixed
  * @access private
  */
  function _avg_clickstream($parameters) {
    $pi_params = $parameters;
    $vi_params = $parameters;
    $pi_params['api_call'] = 'page_impressions';
    $vi_params['api_call'] = 'visits';
    
    $pi = phpOpenTracker::get($pi_params);
    $vi = phpOpenTracker::get($vi_params);
    
    if (is_array($pi)) {
      for ($i=0; $i < count($pi); $i++) {
        $result[$i] = array('timestamp' => $pi[$i]["timestamp"], 'value' => ($pi[$i]["value"] / (($vi[$i]["value"]==0) ? 1 : $vi[$i]["value"])));
      }
    } else {
      if ($vi != 0) { 
      	return $pi/$vi;	
      } else {
        return 0;	
      }
    }
    return $result; 
  }
  
}
?>
