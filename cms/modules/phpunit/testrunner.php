<?php
	require_once "../../config.inc.php";

	$sid = value("sid", "NOSPACES");
	$auth = new Auth("TESTS");

	require_once ($c["path"] . "modules/phpunit/PHPUnit/GUI/SetupDecorator.php");
	require_once ($c["path"] . "modules/phpunit/PHPUnit/GUI/HTML.php");
	//require_once($c["path"]."modules/phpunit/PHPUnit.php");
	$gui = new PHPUnit_GUI_SetupDecorator(new PHPUnit_GUI_HTML());
	$gui->getSuitesFromDir($c["path"] . 'modules/phpunit/tests', '.*\.php$', array (
		'index.php',
		'sql.php'
	));

	$gui->show();
?>