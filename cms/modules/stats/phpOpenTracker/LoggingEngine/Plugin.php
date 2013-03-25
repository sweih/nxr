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
// $Id: Plugin.php,v 1.1 2003/09/02 11:18:56 sven_weih Exp $
//

/**
* Base Class for phpOpenTracker LoggingEngine plugins
*
* @author   Sebastian Bergmann <sebastian@phpOpenTracker.de>
* @version  $Revision: 1.1 $
* @since    phpOpenTracker 1.0.0
*/
class phpOpenTracker_LoggingEngine_Plugin {
  /**
  * Configuration
  *
  * @var array $config
  */
  var $config = array();

  /**
  * Container
  *
  * @var array $container
  */
  var $container = array();

  /**
  * DB
  *
  * @var object $db
  */
  var $db;

  /**
  * Parameters
  *
  * @var array $parameters
  */
  var $parameters = array();

  /**
  * Constructor.
  *
  * @param  array $parameters
  * @access public
  */
  function phpOpenTracker_LoggingEngine_Plugin($parameters) {
    $this->config     = &phpOpenTracker_Config::singleton(true);
    $this->container  = &phpOpenTracker_Container::singleton();
    $this->db         = &phpOpenTracker_DB::singleton();
    $this->parameters = $parameters;
  }

  /**
  * @return boolean
  * @access public
  */
  function pre() {
    return true;
  }

  /**
  * @return array
  * @access public
  */
  function post() {
    return array();
  }
}

//
// "phpOpenTracker essenya, gul meletya;
//  Sebastian carneron PHP."
//
?>
