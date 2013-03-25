<?php
	require_once $c["path"] . "ext/adodb/adodb.inc.php";
	require_once $c["path"] . "api/database/query.php";
	$ADODB_CACHE_DIR = $c["path"]."cache/adodb";
	
	//create initial db
	$db = new db($c["dbdriver"]);
	$db->open();

	/**
	* provides all functions for accessing and writing data to a database. No other way
	* is used throughout this programm to acces the DB.
	* This class is for MySQL-Databases only, but one might change it and use other databases
	* instead. 
	* @module Database
	* @package Database
	*/
	class db {
		var $ADODB;

		var $type;

		/**
		* standard constructor
		* @param string $database_type selects a database abstraction layer for use.
		* standard db in nx is mySQL.
		*/
		function db($database_type = "mysql") { $this->type = $database_type; }

		/**
		* Opens connection to the database.
		* You must call this function after instanciating your class, before doing queries.
		* Otherways all queries will fail! You also must have authentificated a user, before
		* you can use this class!
		*/
		function open($dbhost = "", $dbuser = "", $dbpasswd = "", $database = "") {
			global $c;

			// initialize configuratin variables.
			if ($dbhost == "")
				$dbhost = $c["dbhost"];

			if ($dbuser == "")
				$dbuser = $c["dbuser"];

			if ($dbpasswd == "")
				$dbpasswd = $c["dbpasswd"];

			if ($database == "")
				$database = $c["database"];

			if ($c["dbdriver"] == "mysql") {
                    $this->ADODB = NewADOConnection($this->type);			        		
			        			$this->ADODB->PConnect($dbhost, $dbuser, $dbpasswd, $database);
                        } else if ($c["dbdriver"] == "mssql") {
                                $this->ADODB = &ADONewConnection("ado_mssql");
                                $dsn = "PROVIDER=MSDASQL;DRIVER={SQL Server};SERVER=".$dbhost.";DATABASE=".$database.";UID=".$dbuser.";PWD=".$dbpasswd.";";                      
                                $this->ADODB->Connect($dsn);
                        }
		}

		/**
	   * Creates a counter named sequence starting with 1000 once and increasing value with each
	   * call by 1. The counters are stored in the database. Works like sequences in oracle do.
	   *
	   * @param string Name of the sequence you want to retrieve the value. E.g. you use it for
	   *			   getting the next id for an table entry.
	   * @return integer Next Seuquence value.
	   */
		function nextid($sequence) {
			$sql = "SELECT val from sequences where seq='" . $sequence . "'";

			$rs = &$this->ADODB->Execute($sql);

			//include($c["path"]."ext/adodb/tohtml.inc.php");
			if ($rs <> false) {
				$id = $rs->Fields("val");

				$rs->Close();
			}

			if ($id == "") {
				// insert new sequence into table.
				$insertstatement = "INSERT INTO sequences (seq, val) VALUES ('$sequence',100001)";

				$rs = $this->ADODB->Execute($insertstatement);
				$id = 100000;
			} else {
				// increase sequence by one.
				$updatestatement = "UPDATE sequences SET val=" . ($id + 1) . " WHERE seq='" . $sequence . "'";

				$this->ADODB->Execute($updatestatement);
			}

			return $id;
		}

		/**
		* returns an error, if occured in a query
		* @return string Error string of last query.
		*/
		function error() {
			if ($this->ADODB->ErrorMsg() <> "")
				return $this->ADODB->ErrorNo(). " - " . $this->ADODB->ErrorMsg();
			else
				return "0:";
		}

		/**
		* close the database. Should be called after doing your queries to close the connection
		* to the database.
		* @return string result of action.
		*/
		function close() { 
			$this->ADODB->close(); 
		}
	}
?>