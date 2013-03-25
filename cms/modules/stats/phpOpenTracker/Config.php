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
// $Id: Config.php,v 1.1 2003/09/02 11:18:56 sven_weih Exp $
//


define ('__BLUE', '#9ABCE0');
define ('__RED', '#e49e9e');
define ('__YELLOW', '#f7b63e');
define ('__GREEN', '#97ce8a');
define ('__PURPLE', '#e2ace5');

/**
* phpOpenTracker Configuration Container
*
* @author   Sebastian Bergmann <sebastian@phpOpenTracker.de>
* @version  $Revision: 1.1 $
* @since    phpOpenTracker 1.0.0
*/
class phpOpenTracker_Config {
  /**
  * Loads the configuration.
  *
  * @param  optional boolean $cacheInSession
  * @return array
  * @access public
  * @static
  */
  function &singleton($cacheInSession = false) {
    global $c;
  	static $config;

    if (!isset($config)) {
      if ($cacheInSession) {
        session_register('_phpOpenTracker_Config');
        $config = &$_SESSION['_phpOpenTracker_Config'];
      }

      if ($config = @parse_ini_file(POT_CONFIG_PATH . 'phpOpenTracker.ini')) {
        $config = array_change_key_case($config, CASE_LOWER);
      } else {
        $config = array();
      }

      $config['db_type']                            = $c["dbdriver"];
      $config['db_host']                            = $c["dbhost"];
      $config['db_port']                            = isset($config['db_port'])                            ? $config['db_port']                            : 'default';
      $config['db_socket']                          = isset($config['db_socket'])                          ? $config['db_socket']                          : 'default';
      $config['db_user']                            = $c["dbuser"];
      $config['db_password']        				= $c["dbpasswd"];
      $config['db_database']                        = $c["database"];
      $config['additional_data_table']              = isset($config['additional_data_table'])              ? $config['additional_data_table']              : 'pot_add_data';
      $config['accesslog_table']                    = isset($config['accesslog_table'])                    ? $config['accesslog_table']                    : 'pot_accesslog';
      $config['documents_table']                    = isset($config['documents_table'])                    ? $config['documents_table']                    : 'pot_documents';
      $config['exit_targets_table']                 = isset($config['exit_targets_table'])                 ? $config['exit_targets_table']                 : 'pot_exit_targets';
      $config['hostnames_table']                    = isset($config['hostnames_table'])                    ? $config['hostnames_table']                    : 'pot_hostnames';
      $config['operating_systems_table']            = isset($config['operating_systems_table'])            ? $config['operating_systems_table']            : 'pot_operating_systems';
      $config['referers_table']                     = isset($config['referers_table'])                     ? $config['referers_table']                     : 'pot_referers';
      $config['user_agents_table']                  = isset($config['user_agents_table'])                  ? $config['user_agents_table']                  : 'pot_user_agents';
      $config['visitors_table']                     = isset($config['visitors_table'])                     ? $config['visitors_table']                     : 'pot_visitors';
      $config['document_env_var']                   = isset($config['document_env_var'])                   ? $config['document_env_var']                   : 'REQUEST_URI';
      $config['clean_referer_string']               = isset($config['clean_referer_string'])               ? $config['clean_referer_string']               : false;
      $config['clean_query_string']                 = isset($config['clean_query_string'])                 ? $config['clean_query_string']                 : false;
      $config['get_parameter_filter']               = isset($config['get_parameter_filter'])               ? $config['get_parameter_filter']               : '';
      $config['resolve_hostname']                   = isset($config['resolve_hostname'])                   ? $config['resolve_hostname']                   : true;
      $config['group_hostnames']                    = isset($config['group_hostnames'])                    ? $config['group_hostnames']                    : true;
      $config['group_user_agents']                  = isset($config['group_user_agents'])                  ? $config['group_user_agents']                  : true;
      $config['track_returning_visitors']           = isset($config['track_returning_visitors'])           ? $config['track_returning_visitors']           : false;
      $config['returning_visitors_cookie']          = isset($config['returning_visitors_cookie'])          ? $config['returning_visitors_cookie']          : 'pot_visitor_id';
      $config['returning_visitors_cookie_lifetime'] = isset($config['returning_visitors_cookie_lifetime']) ? $config['returning_visitors_cookie_lifetime'] : 365;
      $config['locking']                            = isset($config['locking'])                            ? $config['locking']                            : false;
      $config['log_reload']                         = isset($config['log_reload'])                         ? $config['log_reload']                         : false;
      $config['jpgraph_path']                       = $c["path"]."ext/jpgraph/";
      $config['merge_tables_threshold']             = isset($config['merge_tables_threshold'])             ? $config['merge_tables_threshold']             : 6;
      $config['logging_engine_plugins']             = isset($config['logging_engine_plugins'])             ? $config['logging_engine_plugins']             : '';
      $config['query_cache']                        = isset($config['query_cache'])                        ? $config['query_cache']                        : false;
      $config['query_cache_dir']                    = isset($config['query_cache_dir'])                    ? $config['query_cache_dir']                    : '/tmp/';
      $config['query_cache_lifetime']               = isset($config['query_cache_lifetime'])               ? $config['query_cache_lifetime']               : 3600;
      $config['debug_level']                        = isset($config['debug_level'])                        ? $config['debug_level']                        : 1;
      $config['exit_on_fatal_errors']               = isset($config['exit_on_fatal_errors'])               ? $config['exit_on_fatal_errors']               : true;
      $config['log_errors']                         = isset($config['log_errors'])                         ? $config['log_errors']                         : false;

      if ($config['debug_level'] > 1) {
        error_reporting(E_ALL);
      }

      if ($config['get_parameter_filter']) {
        $config['get_parameter_filter'] = explode(
          ',',
          str_replace(
            ' ',
            '',
            $config['get_parameter_filter']
          )
        );
      } else {
        $config['get_parameter_filter'] = array();
      }

      if ($config['logging_engine_plugins']) {
        $config['logging_engine_plugins'] = explode(
          ',',
          str_replace(
            ' ',
            '',
            $config['logging_engine_plugins']
          )
        );
      } else {
        $config['logging_engine_plugins'] = array();
      }
    }

    return $config;
  }

  /**
  * Gets the current value of a configuration directive.
  *
  * @param  string $directive
  * @return mixed
  * @access public
  * @static
  * @since  phpOpenTracker 1.2.0
  */
  function get($directive) {
    $config = &phpOpenTracker_Config::singleton();

    return isset($config[$directive]) ? $config[$directive] : false;
  }

  /**
  * Sets the value of a configuration directive.
  *
  * @param  string $directive
  * @param  mixed  $value
  * @access public
  * @static
  * @since  phpOpenTracker 1.2.0
  */
  function set($directive, $value) {
    $config = &phpOpenTracker_Config::singleton();

    $config[$directive] = $value;
  }
}

//
// "phpOpenTracker essenya, gul meletya;
//  Sebastian carneron PHP."
//
?>
