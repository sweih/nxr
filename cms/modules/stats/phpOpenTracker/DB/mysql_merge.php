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
// $Id: mysql_merge.php,v 1.1 2003/09/02 11:18:56 sven_weih Exp $
//

require POT_INCLUDE_PATH . 'DB/mysql.php';

/**
* phpOpenTracker MySQL Database Handler (using Merge Tables)
*
* @author   Sebastian Bergmann <sebastian@phpOpenTracker.de>
* @version  $Revision: 1.1 $
* @since    phpOpenTracker 1.2.0
*/
class phpOpenTracker_DB_mysql_merge extends phpOpenTracker_DB_mysql {
  /**
  * Schema for the pot_accesslog table.
  *
  * @var    string $accesslogSchema
  * @access private
  */
  var $accesslogSchema =
      "CREATE %s TABLE %s %s (
         accesslog_id      INT(11)                NOT NULL,
         timestamp         INT(10)    UNSIGNED    NOT NULL,
         document_id       INT(11)                NOT NULL,
         exit_target_id    INT(11)    DEFAULT '0' NOT NULL,
         entry_document    TINYINT(3) UNSIGNED    NOT NULL,

         KEY accesslog_id   (accesslog_id),
         KEY timestamp      (timestamp),
         KEY document_id    (document_id),
         KEY exit_target_id (exit_target_id)
       ) %s";

  /**
  * Schema for the pot_visitors table.
  *
  * @var    string $visitorsSchema
  * @access private
  */
  var $visitorsSchema =
      "CREATE %s TABLE %s %s (
         accesslog_id        INT(11)             NOT NULL,
         visitor_id          INT(11)             NOT NULL,
         client_id           INT(10)    UNSIGNED NOT NULL,
         operating_system_id INT(11)             NOT NULL,
         user_agent_id       INT(11)             NOT NULL,
         host_id             INT(11)             NOT NULL,
         referer_id          INT(11)             NOT NULL,
         timestamp           INT(10)    UNSIGNED NOT NULL,
         returning_visitor   TINYINT(3) UNSIGNED NOT NULL,

         PRIMARY KEY         (accesslog_id),
         KEY client_time     (client_id, timestamp)
       ) %s";

  /**
  * Creates a new merge table.
  *
  * @param           string  $name
  * @param           string  $type
  * @param           mixed   $tables
  * @param  optional boolean $temporary
  * @access public
  */
  function createMergeTable($name, $type, $tables, $temporary = false) {
    if (is_array($tables)) {
      $tables = implode(',', $tables);
    }

    if ($temporary) {
      parent::query(
        sprintf(
          'DROP TABLE IF EXISTS %s',

          $name
        )
      );
    }

    parent::query(
      sprintf(
        ($type == 'accesslog') ? $this->accesslogSchema : $this->visitorsSchema,
        $temporary  ? 'TEMPORARY'     : '',
        !$temporary ? 'IF NOT EXISTS' : '',
        $name,
        'TYPE=MRG_MyISAM UNION=(' . $tables . ')'
      )
    );
  }

  /**
  * Fetches a row from the current result set.
  *
  * @param  optional boolean $fetchAssoc
  * @access public
  * @return array
  */
  function fetchRow($fetchAssoc = true) {
    if (is_resource($this->result)) {
      if ($fetchAssoc) {
        $row = @mysql_fetch_assoc($this->result);
      } else {
        $row = @mysql_fetch_row($this->result);
      }

      if (is_array($row)) {
        return $row;
      }
    }

    return false;
  }

  /**
  * Performs an SQL query.
  *
  * @param  string           $query
  * @param  optional mixed   $limit
  * @param  optional boolean $warnOnFailure
  * @param  optional boolean $tablesAlreadyCreated
  * @param  optional boolean $rewrite
  * @access public
  */
  function query($query, $limit = false, $warnOnFailure = true, $tablesAlreadyCreated = false, $rewrite = true) {
    if (!$rewrite) {
      return parent::query($query, $limit, $warnOnFailure);
    }

    $insert = false;

    if (!$tablesAlreadyCreated &&
        strstr($query, 'INSERT')) {
      $insert = true;

      $query = $this->_replaceTableNames(
        $query,
        date('Y') . date('m')
      );
    } else {
      $query = $this->_rewriteSelectQuery($query);

      if ($limit != false) {
        $query .= ' LIMIT ' . $limit;
      }
    }

    if ($this->config['debug_level'] > 1) {
      $this->debugQuery($query);
    }

    if (isset($this->result) && is_resource($this->result)) {
      @mysql_free_result($this->result);
    }

    $this->result = @mysql_unbuffered_query($query, $this->connection);

    if (!$this->result) {
      $throwError = $warnOnFailure ? true : false;

      if ($insert &&
          !$tablesAlreadyCreated &&
          mysql_errno($this->connection) == 1146) {
        $this->_newTables();
        $this->query($query, $limit, $warnOnFailure, true);
        $throwError = false;
      }

      if ($throwError) {
        phpOpenTracker::handleError(
          @mysql_error($this->connection),
          E_USER_ERROR
        );
      }
    }
  }

  /**
  * Creates new pot_accesslog and pot_visitors tables
  * for a given month.
  *
  * @param  optional integer $month
  * @param  optional integer $year
  * @access private
  */
  function _newTables($month = '', $year = '') {
    $month = !empty($month) ? $month : date('n');
    $year  = !empty($year)  ? $year  : date('Y');

    $this->_newTable(
      $this->config['accesslog_table'],
      'accesslog',
      $month,
      $year
    );

    $this->_newTable(
      $this->config['visitors_table'],
      'visitors',
      $month,
      $year
    );
  }

  /**
  * Creates a new table and adds it to the
  * appropriate MRG_MyISAM table.
  *
  * @param  string  $name
  * @param  string  $type
  * @param  integer $month
  * @param  integer $year
  * @access private
  */
  function _newTable($name, $type, $month, $year) {
    $mergeTableExists = false;
    $tablePrefix      = $name . '_';
    $tables           = array();

    for ($i = $month; $i <= 12; $i++) {
      parent::query(
        sprintf(
          ($type == 'accesslog') ? $this->accesslogSchema : $this->visitorsSchema,
          '',
          'IF NOT EXISTS',
          $tablePrefix . $year . sprintf('%02d', $i),
          'DELAY_KEY_WRITE=1'
        )
      );
    }

    parent::query('SHOW TABLES');

    while ($row = $this->fetchRow(false)) {
      if (strstr($row[0], $tablePrefix)) {
        $tables[] = $row[0];
      }

      else if ($row[0] == $name) {
        $mergeTableExists = true;
      }
    }

    sort($tables);
    $tables = implode(',', $tables);

    if ($mergeTableExists) {
      parent::query(
        sprintf(
          'ALTER TABLE %s UNION(%s)',

          $name,
          $tables
        )
      );
    } else {
      $this->createMergeTable(
        $name,
        $type,
        $tables
      );
    }
  }

  /**
  * Replaces the names of the pot_accesslog and pot_visitors
  * merge tables with the ones for a given month.
  *
  * @param  string  $query
  * @param  string  $suffix
  * @return string
  * @access private
  */
  function _replaceTableNames($query, $suffix) {
    return str_replace(
      array(
        $this->config['accesslog_table'],
        $this->config['visitors_table']
      ),
      array(
        $this->config['accesslog_table'] . '_' . $suffix,
        $this->config['visitors_table']  . '_' . $suffix
      ),
      $query
    );
  }

  /**
  * Rewrites an SQL SELECT query.
  *
  * @param  string $query
  * @return string
  * @access private
  */
  function _rewriteSelectQuery($query) {
    $_query  = explode(' ', $query);
    $between = array_search('BETWEEN', $_query);

    if ($between != false) {
      $month    = date('n', $_query[$between + 1]);
      $year     = date('Y', $_query[$between + 1]);

      $endMonth = date('n', $_query[$between + 3]);
      $endYear  = date('Y', $_query[$between + 3]);

      if ($year  == $endYear &&
          $month == $endMonth) {
        $query = $this->_replaceTableNames(
          $query,
          $year . sprintf('%02d', $month)
        );
      } else {
        $accesslogTables = array();
        $visitorsTables  = array();

        $done  = false;

        while (!$done) {
          $accesslogTables[] = sprintf(
            '%s_%d%02d',

            $this->config['accesslog_table'],
            $year,
            $month
          );

          $visitorsTables[] = sprintf(
            '%s_%d%02d',

            $this->config['visitors_table'],
            $year,
            $month
          );

          if ($month == $endMonth &&
             $year   == $endYear) {
            $done = true;
          }

          else if ($month < 12) {
            $month++;
          } else {
            $month = 1;
            $year++;
          }
        }

        if (sizeof($accesslogTables) <=
            $this->config['merge_tables_threshold']) {
          $this->createMergeTable(
            $this->config['accesslog_table'] . '_temporary',
            'accesslog',
            $accesslogTables,
            true
          );

          $this->createMergeTable(
            $this->config['visitors_table'] . '_temporary',
            'visitors',
            $visitorsTables,
            true
          );

          $query = $this->_replaceTableNames(
            $query,
            'temporary'
          );
        }
      }
    }

    return $query;
  }
}

//
// "phpOpenTracker essenya, gul meletya;
//  Sebastian carneron PHP."
//
?>
