<?php
require_once "../../cms/config.inc.php";
require_once $c["path"].$cds->path."modules/stats/phpOpenTracker.php";


$fkid  = value("id", "NUMERIC", 0);
$scope = strtoupper(addslashes(value("scope", "NOSPACES", '')));

if (($fkid != 0) && ($scope != '')) {
  $check = getDBCell('hits', 'ID', 'ID='.$fkid);
  if ($check != "") {
  	$sql = 'UPDATE hits SET HIT=HIT+1 WHERE ID='.$fkid.' AND SCOPE="'.$scope.'"';  	
  } else {
	  $sql = 'INSERT INTO hits (ID, SCOPE, HIT) VALUES ('.$fkid.',"'.$scope.'",1)';
  }
	$query = new query($db, $sql);	
	$db->close();
}
	

header('P3P: CP="NOI NID ADMa OUR IND UNI COM NAV"');

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
?>
