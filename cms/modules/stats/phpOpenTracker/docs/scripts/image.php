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
// $Id: image.php,v 1.1 2003/09/01 17:19:33 sven_weih Exp $
//

header('Content-type: image/gif');
header('P3P: CP="NOI NID ADMa OUR IND UNI COM NAV"');
header('Expires: Sat, 22 Apr 1978 02:19:00 GMT');
header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
header('Cache-Control: no-store, no-cache, must-revalidate');
header('Cache-Control: post-check=0, pre-check=0', false);
header('Pragma: no-cache');

require 'phpOpenTracker.php';

if ( isset($_GET['document_url']) &&
    !empty($_GET['document_url'])) {
  $parameters['document_url'] = base64_decode($_GET['document_url']);
}

else if (isset($_SERVER['HTTP_REFERER'])) {
  $parameters['document_url'] = $_SERVER['HTTP_REFERER'];
}

if (!isset($parameters['document_url'])) {
  exit;
}

if ( isset($_GET['document']) &&
    !empty($_GET['document'])) {
  $parameters['document'] = $_GET['document'];
} else {
  $parameters['document'] = $parameters['document_url'];
}

$parameters['client_id'] = isset($_GET['client_id']) ? $_GET['client_id']              : 1;
$parameters['referer']   = isset($_GET['referer'])   ? base64_decode($_GET['referer']) : '';

if (   isset($_GET['add_data']) &&
    is_array($_GET['add_data'])) {
  foreach ($_GET['add_data'] as $data) {
    list($field, $value) = explode('::', $data);

    $parameters['add_data'][$field] = $value;
  }
}

phpOpenTracker::log($parameters);

printf(
  '%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%',
  71,73,70,56,57,97,1,0,1,0,128,255,0,192,192,192,0,0,0,33,249,4,1,0,0,0,0,44,0,0,0,0,1,0,1,0,0,2,2,68,1,0,59
);
?>
