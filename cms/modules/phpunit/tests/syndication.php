<?php
	require_once 'PHPUnit.php';

	class tests_syndication extends PHPUnit_TestCase {

		var $dbCount;
		var $clt;
		var $mtid;
		var $spm;
		var $clnid;
		
		function tests_syndication($name) { $this->PHPUnit_TestCase($name); }

		function setUp() {			
			$this->clt = createClusterTemplate("name", "description", "layout", 0,0);
			$this->mt = createMetaTemplate("name", "description");			
			$this->dbCount = countAllRecords();
		}

		function testCreateClusterTemplate() { 
			$clt = createClusterTemplate("name", "description", "layout", 0,0);
			$this->assertDBCount(1);
			deleteClusterTemplate($clt);
		}

		function testDeleteClusterTemplate() { 
			deleteClusterTemplate( $this->clt);
			$this->assertDBCount(-1);
		}
		
	   function testCreateMetaTemplate() {
	   	$mt = createMetaTemplate("name", "description");
			$this->assertDBCount(1);
			deleteMetaTemplate($mt);
	   }
	   
	   function testDeleteMetaTemplate() { 
			deleteMetaTemplate( $this->mt);
			$this->assertDBCount(-1);
		}
		
		function testCreateMetaTemplateFigure() {
			$mt = createMetaTemplate("name", "description");
			$this->assertDBCount(1);
			createMetaTemplateFigure($mt, "test", 1,1);
			createMetaTemplateFigure($mt, "test", 2,1);
			createMetaTemplateFigure($mt, "test", 3,1);
			$this->assertDBCount(4);
			deleteMetaTemplate($mt);
			$this->assertDBCount(0);
		}
			
		
		function assertDBCount($diff) {
			$this->assertTrue(($this->dbCount+$diff) == countAllRecords()); 
		}
		
		function tearDown() {
		  deleteClusterTemplate($this->clt);
		  deleteMetaTemplate($this->mt);
		}
	}

//$tests_demotest = new PHPUnit_TestSuite();
//$tests_demotest->addTest(new demotest('testAdd'));
//$tests_demotest->addTest(new demotest('testSub'));
?>