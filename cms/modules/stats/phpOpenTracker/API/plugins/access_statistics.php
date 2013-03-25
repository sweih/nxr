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
// $Id: access_statistics.php,v 1.1 2003/09/02 11:18:56 sven_weih Exp $
//

/**
* phpOpenTracker API - Access Statistics
*
* @author   Sebastian Bergmann <sebastian@phpOpenTracker.de>
* @version  $Revision: 1.1 $
* @since    phpOpenTracker 1.2.0
*/
class phpOpenTracker_API_access_statistics extends phpOpenTracker_API_Plugin {
  /**
  * API Calls
  *
  * @var array $apiCalls
  */
  var $apiCalls = array(
    'page_impressions',
    'visitors',
    'visits'
  );

  /**
  * API Type
  *
  * @var string $apiType
  */
  var $apiType = 'get';

  /**
  * Runs the phpOpenTracker API call.
  *
  * @param  array $parameters
  * @return mixed
  * @access public
  */
  function run($parameters) {
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
}

//
// "phpOpenTracker essenya, gul meletya;
//  Sebastian carneron PHP."
//
?>
