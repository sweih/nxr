<?php
//
// +---------------------------------------------------------------------+
// | phpOpenTracker - The Website Traffic and Visitor Analysis Solution  |
// +---------------------------------------------------------------------+
// | Copyright (c) 2000-2002 Sebastian Bergmann. All rights reserved.    |
// +---------------------------------------------------------------------+
// | This source file is subject to the phpOpenTracker Software License, |
// | Version 1.0, that is bundled with this package in the file LICENSE. |
// | If you did not receive a copy of this file, you may either read the |
// | license online at http://phpOpenTracker.de/license/1_0.txt, or send |
// | a note to license@phpOpenTracker.de, so we can mail you a copy.     |
// +---------------------------------------------------------------------+
// | Author: Sebastian Bergmann <sebastian@phpOpenTracker.de>            |
// +---------------------------------------------------------------------+
//
// $Id: image.php,v 1.7 2004/12/23 21:42:55 sven_weih Exp $
//
require_once "../../cms/config.inc.php";
require_once $c["path"].$cds->path."modules/stats/phpOpenTracker.php";


$parameters['client_id']    = isset($_GET['client_id'])       ? $_GET['client_id']       : 1;
$parameters['document_url'] = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
$parameters['document']     = isset($_GET['document'])        ? $_GET['document']        : $parameters['document_url'];
$parameters['referer']      = isset($_GET['referer'])         ? $_GET['referer']         : '';

header('P3P: CP="NOI NID ADMa OUR IND UNI COM NAV"');
phpOpenTracker::log($parameters);

header('Content-type: image/gif');
header('Expires: Sat, 22 Apr 1978 02:19:00 GMT');
header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); 
header('Cache-Control: no-store, no-cache, must-revalidate');
header('Cache-Control: post-check=0, pre-check=0', false);
header('Pragma: no-cache');

printf(
  '%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%', 
  71,73,70,56,57,97,1,0,1,0,128,255,0,192,192,192,0,0,0,33,249,4,1,0,0,0,0,44,0,0,0,0,1,0,1,0,0,2,2,68,1,0,59
);

/*
 * vim600:  et sw=2 ts=2 fdm=marker
 * vim<600: et sw=2 ts=2
 */
?>
