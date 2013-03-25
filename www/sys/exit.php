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
// $Id: exit.php,v 1.5 2004/10/11 18:29:15 sven_weih Exp $
//

require_once "../../cms/config.inc.php";
require_once $c["path"].$cds->path."modules/stats/phpOpenTracker.php";

if (isset($_GET['url'])) {
  $exitURL = str_replace('&amp;', '&', base64_decode($_GET['url']));

  $config    = &phpOpenTracker_Config::singleton(true);
  $db        = &phpOpenTracker_DB::singleton();

  $container = &phpOpenTracker_Container::singleton(
    array(
      'initNoSetup' => true
    )
  );

  $db->query(
    sprintf(
      "UPDATE %s
          SET exit_target_id = '%d'
        WHERE accesslog_id   = '%d'
          AND document_id    = '%d'
          AND timestamp      = '%d'",

      $config['accesslog_table'],
      $db->storeIntoDataTable($config['exit_targets_table'], $exitURL),
      $container['accesslog_id'],
      $container['document_id'],
      $container['timestamp']
    )
  );

  header('Location: http://' . $exitURL);
}
?>
