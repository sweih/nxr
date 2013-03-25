<?

	/**
	 * Used for all accesses to the database. The db class is for connection-handling,
	 * the query class does all the SELECT, UPDATE, INSERT and DELETE-Statements.
	  * @copyright: sweih
	 * @module query
	 * @package Database
	 */
	class query extends ADORecordSet {
		var $result;

		var $row;
		var $firstflag;
		var $rs;
		var $valid;
		/**
		 * Constructor of the query object.
		 * executes the query, notifies the db object of the query result to clean
		 * up later
		 * @param integer &$db References an open db-connection.
		 * @param string $query The SQL-Statement, that is to be executed.
		 */
		function query(&$db, $query = "") {
			global $QUERY_CACHE, $QUERY_CACHE_TIME;
			if ($db->cache && $QUERY_CACHE) {
				$this->rs = $db->ADODB->CacheExecute($QUERY_CACHE_TIME, $query);
			} else {
				$this->rs = $db->ADODB->Execute($query);
			}
			$this->firstflag = false;

			if ($this->rs == false)
				$this->valid = false;
			else
				$this->valid = true;

			global $panic;

			if ($panic)
				echo $query . "--";
		}

		/**
		 * Move cursor and check if next row is available.
		 * @return integer result of mysql_fetch_array or 0 if end of transaction
		 */
		function getrow() {
			if ($this->valid == false)
				return false;

			if ($this->firstflag)
				$this->rs->MoveNext();

			$this->firstflag = true;

			if ($this->rs->EOF) {
				return false;
			} else
				return true;
		}

		/**
		* get the value of the field with name $field
		* in the current row
		* @param string Name of the Column, you want to extract the value from.
		* Note: You must make difference between lower- and uppercase!
		* @return string Value of the column in the database.
		*/
		function field($field) {
			if ($this->valid)
				return $this->rs->Fields($field);
		}

		function count() {
			if ($this->valid)
				return $this->rs->RecordCount();
		}

		/**
		 * Free the query and give Memory back to the system.
		 * compare to mysql_free_result method.
		 */
		function free() {
			if ($this->valid)
				$this->rs->close();
		}
	}

	;
?>