<?
	/**
	 * @module Stack
	 * @package Management
	 */

	/**
	 * pushes a variable into the variable stack
	 * @param string name of the variable
	 * @param string value of the variable
	 */
	function pushVar($name, $value) {
		global $auth, $db;

		$userId = $auth->userId;
		$sql1 = "DELETE FROM temp_vars WHERE USER_ID=$userId and NAME='$name'";
		$sql2 = "INSERT INTO temp_vars (USER_ID, NAME, VALUE) VALUES ($userId, '$name', '$value')";

		$query = new query($db, $sql1);
		$query = new query($db, $sql2);
		$query->free();
	}

	/**
	 * retrieves the value of a variable from the variable stack.
	 * @param string name of the variable
	 */
	function getVar($name) {
		$back = "";

		global $auth, $db;
		$userId = $auth->userId;
		$sql = "SELECT VALUE FROM temp_vars WHERE USER_ID=$userId and NAME='$name'";
		$query = new query($db, $sql);

		if ($query->getrow()) {
			$back = $query->field("VALUE");
		} else {
			$back = "";
		}

		$query->free();

		return $back;
	}

	/**
	 * Deletes a variable from the variable stack
	 * @param string name of the variable
	 */
	function delVar($name) {
		$back = "";

		global $auth, $db;
		$userId = $auth->userId;
		$sql = "DELETE FROM temp_vars WHERE USER_ID=$userId and NAME='$name'";

		$query = new query($db, $sql);
		$query->free();
	}

	/**
	 * Pops a Variable form the variable stack.
	 * @param string name of the variable
	 * @returns variant Value of the variable.
	 */
	function popVar($name) {
		$back = getVar($name);

		delVar ($name);
		return $back;
	}
?>