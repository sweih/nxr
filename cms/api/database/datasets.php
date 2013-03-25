<?
	/**
	 * Database Tools
	 * @package Database
	 * @subpackage Helpfunctions
	 */

	/**
	 * Counts the number of elements selected by the row-identifier.
	 * @param string Table, where to count
	 * @param string Row, where to count
	 * @param string row-identifier, to select data with.
	 * @returns integer Number of rows counted.
	 */
	function countRows($table, $column, $cond) {
		global $db;

		$sql = "SELECT COUNT($column) AS ANZ FROM $table WHERE $cond";
		$query = new query($db, $sql);
		$query->getrow();
		$amount = $query->field("ANZ");
		$query->free();

		return $amount;
	}

	/**
	 * Returns the number of records in a table
	 * @param string tablename
	 */
	function countTableRecords($table) {
		global $db;
		$query = $db->ADODB->Execute("SELECT * FROM ".$table);
		return $query->RecordCount();
	}
	
	/**
	 * Returns the number of records in the database
	 * Very expensive. Use in phpUnit only!!!! Does not return records in translation tables!
	 */
	function countAllRecords() {
		global $c_datatypes, $db;
		$tables = array_keys($c_datatypes);
		
		$count=0;
		for ($i=0; $i < count($tables); $i++) {
		  if ($tables[$i] != "state_translation" && $tables[$i] != "syndication") {
		    $query = $db->ADODB->Execute("SELECT * FROM ".$tables[$i]);
		    $count+= 	$query->RecordCount();
		   }
		}
		return $count;
	}

	/**
	* Get the maximum value of a column
	*
	* @param string Table, where to evaluate
	* @param string Row, where to evaluate
	* @param string row-identifier, to select data with.
	* @returns integer Maximum value
	*/
	function getMax($table, $column, $cond) {
		global $db;

		$sql = "SELECT MAX($column) AS ANZ FROM $table WHERE $cond";

		$query = new query($db, $sql);
		$query->getrow();
		$amount = $query->field("ANZ");
		$query->free();
		return $amount;
	}

	/**
	 * Retrieves the next systemwide Genuine Unique ID
	 * @returns integer next GUID.
	 */
	function nextGUID() {
		global $db;

		$nextid = $db->nextid("GUID");

		if ($nextid < 1000)
			$errors .= "-GUID ERROR!!!";

		return $nextid;
	}

	/**
	 * The method processSaveSets is used for automatically calling
	 * all execute-Methods in registered InsertSets, UpdateSets
	 * and DeleteSets. The Sets are registered with the methods
	 * addInsert, addUpdate and addDelete
	 */
	function processSaveSets() {
		global $deletestatements, $insertstatements, $updatestatements, $rawstatements, $oids, $db;

		for ($i = 0; $i < count($deletestatements); $i++) {
			$deletestatements[$i]->execute();
		}
		$deletestatements = null;;

		for ($j = 0; $j < count($insertstatements); $j++) {			
			$oids[$j] = $insertstatements[$j]->execute();
		}
		$insertstatements = null;

		for ($k = 0; $k < count($updatestatements); $k++) {
			$updatestatements[$k]->execute();
		}
		$updatestatements = null;

		for ($l = 0; $l < count($rawstatements); $l++) {
			$query = new query($db, $rawstatements[$l]);
			$query->free();
		}
		
		$rawstatements = null;
	}
	/** For Down-Grade-Compatibility only **/
	function proccessSaveSets() { processSaveSets(); }

	/**
	 * Adds SQL which will be automatically processed when an update or insert is done.
	 *
	 * @param string $sql SQL - Stament to execute on update or insert.
	 */
	function addRawSQL($sql) {
		global $rawstatements;

		$rssize = count($rawstatements);
		$rawstatements[$rssize] = $sql;
	}

	/**
	 * addInsert creates a new InsertSet for given table or adds the column
	 * and value automatically to an exisiting InsertSet on the same table.
	 * You cannot use this method for multiple insertions on one table at
	 * once!
	 * Note: You must call processSaveSets to make the changes to the database!
	 * @param string Name of table, you want to insert values
	 * @param string Name of column to insert value
	 * @param string Value you want to insert
	 * @param string Kind of Datatype you want to insert.
	 * Allowed ar CHAR|NUMBER|DATE|DATETIME|TIMESTAMP|PASSWORD
	 */
	function addInsert($table, $column, $value, $datatype) {
		global $insertstatements;

		$issize = count($insertstatements);
		$set = "-1";

		for ($i = 0; $i < $issize; $i++) {
			if ($insertstatements[$i]->table == $table)
				$set = $i;
		}

		if ($set != "-1") {
			$insertstatements[$set]->add($column, $value, $datatype);
		} else {
			$insertstatements[$issize] = new InsertSet($table);
			$insertstatements[$issize]->setPK($column, $value, $datatype);
		}
	}

	/**
	 * addUpdate creates a new UpdateSet for given table or adds the column
	 * and value automatically to an exisiting UpdatetSet on the same table.
	 * You can use several Updatesets on one table at a time, as you use
	 * different row_identifiers. A row_identifier is about the same as the
	 * WHERE-Statement in a SQL-Query.
	 * Note: You must call processSaveSets to make the changes to the database!
	 * @param string Name of table, you want to insert values
	 * @param string Name of column to insert value
	 * @param string Value you want to insert
	 * @param string Statement of SQL-Where-Clause for selecting one or more Records in the table.
	 * @param string Kind of Datatype you want to insert.
	 * Allowed ar CHAR|NUMBER|DATE|DATETIME|TIMESTAMP|PASSWORD
	 */
	function addUpdate($table, $column, $value, $row_identifier, $datatype) {
		global $updatestatements;

		$issize = count($updatestatements);
		$set = "-1";

		for ($i = 0; $i < $issize; $i++) {
			if ($updatestatements[$i]->table == $table && $updatestatements[$i]->row_identifier == $row_identifier)
				$set = $i;
		}

		if ($set != "-1") {
			$updatestatements[$set]->add($column, $value, $datatype);
		} else {
			$updatestatements[$issize] = new UpdateSet($table, $row_identifier);
			$updatestatements[$issize]->add($column, $value, $datatype);
		}
	}

	/**
	 * addDelete creates a new DeleteSet for given table and row_identifier
	 * you cannot create two DeleteSets on one row_identifier. addDelete will filter
	 * those Sets for you and ignore them.
	 * Note: You must call processSaveSets to make the changes to the database!
	 * @param string Name of the table, you want to delete from
	 * @param string Statement of your SQL-Where-Clause for selecting one or more records in the table for deletion.
	 */
	function addDelete($table, $row_identifier) {
		global $deletestatements;

		$dssize = count($deletestatements);
		$set = "-1";

		for ($i = 0; $i < $dssize; $i++) {
			if ($deletestatements[$i]->table == $table && $deletestatements[$i]->row_identifier == $row_identifier)
				$set = $i;
		}

		if ($set == "-1") {
			$deletestatements[$dssize] = new DeleteSet($table, $row_identifier);
		}
	}

	/**
	 * Creates an Array from a table with following form:
	 * array[i][0] = $name
	 * array[i][1] = $value
	 * Does not include the "Please select" with Value -1!
	 * The function therefore queries the table for names and values that
	 * match the given data_identifier and processes them into 2D-Array.
	 * This function should be used with WUI Select and Dropdown or some DBO
	 * classes for easy creation of NAME/VALUE-paired Arrays!
	 * @param string Name of table you are working with
	 * @param string Name of column, you want to take the Names for the Array form.
	 * @param string Name of column, you want to take the Names for the Values form.
	 * @param string $data_identifier Statement of your SQL-Where-Clause for selecting one or more records in the table for processing.
	 * @param string $order ORder clause like SQL-ORder.
	 * @return Array
	 */
	function createNameValueArrayEx($table, $name, $value, $data_identifier = "1", $order = "") {
		global $db;

		$values = null;
		$counter = 0;
		$sql = "SELECT DISTINCT $name, $value FROM $table WHERE $data_identifier " . $order;
        if (stristr($name, " AS ")) $name = substr($name, strpos($name, " AS ") + 4);
        if (stristr($value, " AS ")) $value = substr($value, strpos($value, " AS ") + 4);

        $query = new query($db, $sql);

		while ($query->getrow()) {
			$values[$counter][0] = $query->field($name);

			$values[$counter][1] = $query->field($value);
			$counter++;
		}

		$query->free();

		return $values;
	}

	/**
	 * Creates an Array from a table with following form:
	 * array[i][0] = $name
	 * array[i][1] = $value
	 * The function therefore queries the table for names and values that
	 * match the given data_identifier and processes them into 2D-Array.
	 * This function should be used with WUI Select and Dropdown or some DBO
	 * classes for easy creation of NAME/VALUE-paired Arrays!
	 * @param string Name of table you are working with
	 * @param string Name of column, you want to take the Names for the Array form.
	 * @param string Name of column, you want to take the Names for the Values form.
	 * @param string $data_identifier Statement of your SQL-Where-Clause for selecting one or more records in the table for processing.
	 * @param string $order ORder clause like SQL-ORder.
	 * @return Array
	 */
	function createNameValueArray($table, $name, $value, $data_identifier = "1", $order = "") {
		global $db;

		$values = null;
		$counter = 1;
		$sql = "SELECT DISTINCT $name, $value FROM $table WHERE $data_identifier " . $order;

		$query = new query($db, $sql);
		$values[0][0] = "Please select";
		$values[0][1] = "-1";

        if (stristr($name, " AS ")) $name = substr($name, strpos($name, " AS ") + 4);
        if (stristr($value, " AS ")) $value = substr($value, strpos($value, " AS ") + 4);

		while ($query->getrow()) {
			$values[$counter][0] = $query->field($name);

			$values[$counter][1] = $query->field($value);
			$counter++;
		}

		$query->free();

		return $values;
	}

	/**
	* Creates an Array from a table with following form:
	* array[i] = $name
	* The function queries the table for names that
	* match the given data_identifier and processes them into Array.
	* @param string Name of table you are working with
	* @param string Name of column, you want to take the Names for the Array form.
	* @param string $data_identifier Statement of your SQL-Where-Clause for selecting one or more records in the table for processing.
	* @param string $order Name of the column which shall be ordered by.
	* @param boolean Should the values be filtered for duplicates ("distinct")
	* @return Array
	*/
	function createDBCArray($table, $name, $data_identifier = "1", $order = "", $distinct=true) {
		global $db;
		
		$values = null;
		$counter = 0;
		
		if ($distinct) {
			$filter = "DISTINCT";
		} else {
			$filter = "";
		}
		
		$sql = "SELECT $filter $name FROM $table WHERE $data_identifier" . " $order";
		$query = new query($db, $sql);

		while ($query->getrow()) {
			$values[$counter] = $query->field($name);

			$counter++;
		}

		$query->free();

		return $values;
	}

	/**
	 * Use, if you want to get the content of a certain database-cell.
	 * The function works with recordsets internally. That means, that
	 * an SQL-Query for each table and row_identifier pair ist performed
	 * and then the resulting recordset is stored within the system.
	 * Recalling the functions with the same table and row_identifier
	 * the system does not perform a new query but takes the data from
	 * the already existing recordset.
	 * @param string Name of the table you want to query in
	 * @param string Name of the column you want to get the value from.
	 * Note: The column-Name is case-sensitive!
	 * @param string Statement of the Where-Clause in SQL.
	 * @return string the Content of Queried column.
	 */
	function getDBCell($table, $column, $row_identifier) {
		global $recordsets;

		$rssize = count($recordsets);

		if ($rssize > 0) {
			for ($i = 0; $i < $rssize; $i++) {
				if ($recordsets[$i]->match($table, $row_identifier)) {
					return $recordsets[$i]->getValue($column);
				}
			}
		}

		$recordsets[$rssize] = new Recordset($table, "*", $row_identifier);
		return $recordsets[$rssize]->getValue($column);
	}

	/**
	 * Replace simple ' with '\ for getting correct SQL-Statements.
	 * @param string Text you want to parse
	 * @return string parsed text.
	 */
	function parseSQL($data) {
		$mydata = trim(ereg_replace("'", "\'", $data));

		return $mydata;
	}
	
	/**
	 * Clear the query-cache
	 */
	function resetDBCache() {
	  global $recordsets;
	  $recordsets = array();	
	}
?>