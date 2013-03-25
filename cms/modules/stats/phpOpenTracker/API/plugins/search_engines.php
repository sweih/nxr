<?php
//
// phpOpenTracker - The Website Traffic and Visitor Analysis Solution
//
// Copyright 2000 - 2005 Sebastian Bergmann. All rights reserved.
//
// Licensed under the Apache License, Version 2.0 (the "License");
// you may not use this file except in compliance with the License.
// You may obtain a copy of the License at
//
//   http://www.apache.org/licenses/LICENSE-2.0
//
// Unless required by applicable law or agreed to in writing, software
// distributed under the License is distributed on an "AS IS" BASIS,
// WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
// See the License for the specific language governing permissions and
// limitations under the License.
//

/**
 * phpOpenTracker API - Search Engines
 *
 * @author      Sebastian Bergmann <sb@sebastian-bergmann.de>
 * @copyright   Copyright &copy; 2000-2005 Sebastian Bergmann <sb@sebastian-bergmann.de>
 * @license     http://www.apache.org/licenses/LICENSE-2.0 The Apache License, Version 2.0
 * @category    phpOpenTracker
 * @package     SearchEngines
 * @since       phpOpenTracker-Search_Engines 1.0.0
 */
class phpOpenTracker_API_search_engines extends phpOpenTracker_API_Plugin {
  /**
  * API Calls
  *
  * @var array $apiCalls
  */
  var $apiCalls = array(
    'search_engines'
  );

  /**
  * API Type
  *
  * @var string $apiType
  */
  var $apiType = 'get';

  /**
  * Constructor.
  *
  * @access public
  */
  function phpOpenTracker_API_search_engines() {
    parent::phpOpenTracker_API_Plugin();

    $this->config['plugins']['search_engines']['table'] = isset($this->config['plugins']['search_engines']['table']) ? $this->config['plugins']['search_engines']['table'] : 'pot_search_engines';
  }

  /**
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

    $constraint = $this->_constraint($parameters['constraints']);

    $timerange = $this->_whereTimerange(
      $parameters['start'],
      $parameters['end']
    );

    switch ($parameters['what']) {
      case 'combined_statistics': {
        $_parameters = $parameters;
        $_parameters['what'] = 'top_search_engines';

        $searchEngines = phpOpenTracker::get(
          $_parameters
        );

        $_parameters['what'] = 'top_search_keywords';

        for ($i = 0; $i < sizeof($searchEngines['top_items']); $i++) {
          $_parameters['search_engine'] = $searchEngines['top_items'][$i]['string'];

           $searchKeywords = phpOpenTracker::get(
            $_parameters
          );

          $searchEngines['top_items'][$i]['search_keywords'] = $searchKeywords['top_items'];
        }

        return $searchEngines['top_items'];
      }
      break;

      case 'top_search_engines':
      case 'top_search_keywords': {
        switch ($parameters['result_format']) {
          case 'csv': {
            $csv = "Rank;Item;Count;Percent\n";
          }
          break;

          case 'xml':
          case 'xml_object': {
            $tree = new XML_Tree;
            $root = $tree->addRoot('top');
          }
          break;

          case 'separate_result_arrays': {
            $names   = array();
            $values  = array();
            $percent = array();
          }
          break;

          default: {
            $topItems = array();
          }
        }

        if (isset($parameters['search_engine'])) {
          $searchEngineConstraint = sprintf(
            "AND search_engines.search_engine = '%s'",
            $parameters['search_engine']
          );
        } else {
          $searchEngineConstraint = '';
        }

        $field = $parameters['what'] == 'top_search_engines' ? 'search_engine' : 'keywords';

        $nestedQuery = sprintf(
          "SELECT search_engines.%s AS item
             FROM %s accesslog,
                  %s visitors,
                  %s search_engines
            WHERE visitors.client_id    = '%d'
              AND visitors.accesslog_id = accesslog.accesslog_id
              AND visitors.accesslog_id = search_engines.accesslog_id
                  %s
                  %s
                  %s
            GROUP BY visitors.accesslog_id,
                     search_engines.%s",

          $field,
          $this->config['accesslog_table'],
          $this->config['visitors_table'],
          $this->config['plugins']['search_engines']['table'],
          $parameters['client_id'],
          $searchEngineConstraint,
          $constraint,
          $timerange,
          $field
        );

        if ($this->db->supportsNestedQueries()) {
          $queryTotalUnique = sprintf(
            'SELECT COUNT(item)           AS total_items,
                    COUNT(DISTINCT(item)) AS unique_items
               FROM (%s) items',

            $nestedQuery
          );

          $queryItems = sprintf(
            'SELECT COUNT(item) AS item_count,
                    item
               FROM (%s) items
              GROUP BY item
              ORDER BY item_count %s,
                       item',

            $nestedQuery,
            $parameters['order']
          );
        } else {
          if ($this->config['db_type'] == 'mysql' ||
              $this->config['db_type'] == 'mysql_merge') {
            $dropTemporaryTable = true;

            $this->db->query(
              sprintf(
                'CREATE TEMPORARY TABLE pot_temporary_table %s',

                $nestedQuery
              )
            );

            $queryTotalUnique = sprintf(
              'SELECT COUNT(item)           AS total_items,
                      COUNT(DISTINCT(item)) AS unique_items
                 FROM pot_temporary_table',

              $nestedQuery
            );

            $queryItems = sprintf(
              'SELECT COUNT(item) AS item_count,
                      item
                 FROM pot_temporary_table
                GROUP BY item
                ORDER BY item_count %s,
                         item',

              $parameters['order']
            );
          } else {
            return phpOpenTracker::handleError(
              'You need a database system capable of nested ' .
              'queries to use the "search_engines" API calls.',
              E_USER_ERROR
            );
          }
        }

        $this->db->query($queryTotalUnique);

        if ($row = $this->db->fetchRow()) {
          $totalItems  = intval($row['total_items']);
          $uniqueItems = intval($row['unique_items']);
        } else {
          return phpOpenTracker::handleError(
            'Database query failed.'
          );
        }

        if ($totalItems > 0) {
          $this->db->query($queryItems, $parameters['limit']);

          $i = 0;

          while ($row = $this->db->fetchRow()) {
            $percentValue = doubleval(
              number_format(
                ((100 * $row['item_count']) / $totalItems),
                2
              )
            );

            switch ($parameters['result_format']) {
              case 'csv': {
                $csv = sprintf(
                  "%d;%s;%d;%d\n",

                  $i+1,
                  $row['item'],
                  intval($row['item_count']),
                  $percentValue
                );
              }
              break;

              case 'xml':
              case 'xml_object': {
                $itemChild = &$root->addChild('item');

                $itemChild->addChild('rank',    $i+1);
                $itemChild->addChild('string',  $row['item']);
                $itemChild->addChild('count',   intval($row['item_count']));
                $itemChild->addChild('percent', $percentValue);
              }
              break;

              case 'separate_result_arrays': {
                $names[$i]   = $row['item'];
                $values[$i]  = intval($row['item_count']);
                $percent[$i] = $percentValue;
              }
              break;

              default: {
                $topItems[$i]['count'  ] = intval($row['item_count']);
                $topItems[$i]['string' ] = $row['item'];
                $topItems[$i]['percent'] = $percentValue;
              }
            }

            $i++;
          }
        }

        if (isset($dropTemporaryTable)) {
          $this->db->query('DROP TABLE pot_temporary_table');
        }

        switch ($parameters['result_format']) {
          case 'csv': {
            return $csv;
          }
          break;

          case 'xml':
          case 'xml_object': {
            $root->addChild('total',  $totalItems);
            $root->addChild('unique', $uniqueItems);

            switch ($parameters['result_format']) {
              case 'xml': {
                return $root->get();
              }
              break;

              case 'xml_object': {
                return $root;
              }
              break;
            }
          }
          break;

          case 'separate_result_arrays': {
            return array(
              $names,
              $values,
              $percent,
              $uniqueItems
            );
          }
          break;

          default: {
            return array(
              'top_items'    => $topItems,
              'unique_items' => $uniqueItems
            );
          }
        }
      }
      break;
    }
  }
}

//
// "phpOpenTracker essenya, gul meletya;
//  Sebastian carneron PHP."
//
?>
