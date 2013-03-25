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
// $Id: graph.php,v 1.1 2003/09/02 11:18:56 sven_weih Exp $
//

require 'phpOpenTracker.php';

switch ($_GET['what']) {
  case 'access_statistics': {
    $time = time();

    phpOpenTracker::plot(
      array(
        'api_call'  => 'access_statistics',
        'client_id' => isset($_GET['client_id']) ? $_GET['client_id'] : 1,
        'start'     => isset($_GET['start'])     ? $_GET['start']     : mktime( 0, 0, 0, date('m', $time),   1, date('Y', $time)),
        'end'       => isset($_GET['end'])       ? $_GET['end']       : mktime( 0, 0, 0, date('m', $time)+1, 0, date('Y', $time)),
        'interval'  => isset($_GET['interval'])  ? $_GET['interval']  : 'day',
        'width'     => isset($_GET['width'])     ? $_GET['width']     : 640,
        'height'    => isset($_GET['height'])    ? $_GET['height']    : 480
      )
    );
  }
  break;
}
?>
