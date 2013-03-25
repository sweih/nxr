<?
	/**
	 * Tableorder
	 * Functions for sorting tables for positions
	 * @module TableOrder
	 * @package Tools
	 */

	/**
	 * Sorts the Rows in a table ascending
	 *
	 * @param string $table Name of the table to sort
	 * @param string $indexColumn Name of the Primary Key of the table
	 * @param string $sortColumn Name of the Column to perform the sort about
	 * @param string $cond Where-Clause, to select only some rows.
	 */
	function sortTableRows($table, $indexColumn, $sortColumn, $cond = "1") {
		global $db;

		$sql = "SELECT $indexColumn, $sortColumn FROM $table WHERE $cond ORDER BY $sortColumn";
		$query = new query($db, $sql);

		while ($query->getrow()) {
			$values[$query->field($indexColumn)] = $query->field($sortColumn);
		}
        $query->free();
        if ($values != null)
			asort ($values);	
		
		$position = 1;
		
		if (is_array($values) && ($copy != $values)) {
			while (list($key, $val) = each($values)) {
				$sql = "UPDATE $table SET $sortColumn = $position WHERE $indexColumn = $key AND $cond";
				$query = new query($db, $sql);
				$query->free();
				$position++;
			}
		}
	}

	
	/**
		 * Moves the row selected one position up. (Pos = Pos -1);
		 *
		 * @param string $table Name of the table to move up from
		 * @param string $indexColumn Name of the Primary Key of the table
		 * @param integer $indexValue Value of the PK of the row to sort.
		 * @param string $sortColumn Name of the Column where order is stored
		 * @param string $cond Where-Clause, to select only some rows.
		 */
	function moveRowUp($table, $indexColumn, $indexValue, $sortColumn, $cond = "1") {
		global $db;

		$curPos = getDBCell($table, $sortColumn, $cond . " AND $indexColumn = $indexValue");

		if ($curPos > 1) {
			$chKey = getDBCell($table, $indexColumn, $cond . " AND $sortColumn =" . ($curPos - 1));

			$sql = "UPDATE $table SET $sortColumn=" . ($curPos) . " WHERE $cond AND $indexColumn = $chKey";
			$query = new query($db, $sql);
			$query->free();
			$sql = "UPDATE $table SET $sortColumn=" . ($curPos - 1) . " WHERE $cond AND $indexColumn = $indexValue";
			$query = new query($db, $sql);
			$query->free();
			sortTableRows($table, $indexColumn, $sortColumn, $cond);
		}
	}

	/**
		 * Moves the row selected one position down. (Pos = Pos + 1);
		 *
		 * @param string $table Name of the table to move down from
		 * @param string $indexColumn Name of the Primary Key of the table
		 * @param integer $indexValue Value of the PK of the row to sort.
		 * @param string $sortColumn Name of the Column where order is stored
		 * @param string $cond Where-Clause, to select only some rows.
		 */
	function moveRowDown($table, $indexColumn, $indexValue, $sortColumn, $cond = "1") {
		global $db;

		$curPos = getDBCell($table, $sortColumn, $cond . " AND $indexColumn = $indexValue");
		$maxPos = getMax($table, $sortColumn, $cond);

		if ($curPos < $maxPos) {
			$chKey = getDBCell($table, $indexColumn, $cond . " AND $sortColumn =" . ($curPos + 1));
		
			$sql = "UPDATE $table SET $sortColumn=" . ($curPos) . " WHERE $cond AND $indexColumn = $chKey";
			$query = new query($db, $sql);
			$query->free();
			$sql = "UPDATE $table SET $sortColumn=" . ($curPos + 1) . " WHERE $cond AND $indexColumn = $indexValue";
			$query = new query($db, $sql);
			$query->free();
			sortTableRows($table, $indexColumn, $sortColumn, $cond);
		}
	}

	/**
		 * Frees a position for inserting a new dataset.
		 *
		 * @param string $table Name of the table to free a row
	 	 * @param string $sortColumn Name of the Column where order is stored
		 * @param integer $position Value of the PK of the row to free
		 * @param string $cond Where-Clause, to select only some rows.
		 */
	function freeRowPosition($table, $sortColumn, $position, $cond = "1") {
		global $db;
		if (getDBCell($table, $sortColumn, "$cond AND $sortColumn = $position") != "") {
		  $sql = "UPDATE $table SET $sortColumn = ($sortColumn + 1) WHERE $cond AND $sortColumn >= $position";
		  $query = new query($db, $sql);
		  $query->free();
		}
	}
?>