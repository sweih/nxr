<?
	/**
	 * Auth Access
	 * Functions for creating and deleting SYS-FUNCTIONS AND ROLES
	 * @module AuthAccess
	 * @package Tools
	 */

	/**
	 * Creates a role
	 *
	 * @param string NAme of the role to create
	 * @param string Description of the role to create
	 */
	function createRole($roleName, $roleDescription) {
		if (getDBCell("roles", "ROLE_ID", "UPPER(ROLE_NAME) = UPPER('$roleName')") == "") {
			global $db;

			$guid = nextGUID();
			$sql = "INSERT INTO roles (ROLE_ID, ROLE_NAME, DESCRIPTION) VALUES($guid, '$roleName', '$roleDescription')";
			$query = new query($db, $sql);
			$query->free();
		}
	}

	/**
	 * Deletes a role. System-Roles cannot be deleted.
	 * 
	 * @param string Name of the role to delete
	 */
	function deleteRole($roleName) {
		global $db;

		$rlid = getDBCell("roles", "ROLE_ID", "UPPER(ROLE_NAME) = UPPER('$roleName') AND ROLE_ID > 100000");
		$sql = "DELETE FROM roles WHERE UPPER(ROLE_NAME) = UPPER('$roleName') AND ROLE_ID > 100000";
		$query = new query($db, $sql);
		$query->free();

		if ($rlid != "") {
			deleteRow("user_permissions", "ROLE_ID = $rlid");

			deleteRow("acl_relations", "ROLE_ID = $rolid");
		}
	}

	/**
	 * Register a System-Function for checking access with $auth->checkAccessToFunction.
	 *
	 * @param string ID of the new Function
	 * @param string Name of the function for config output.
	 * @param string Description of the function for config output.
	 * @param string Parent-ID of the function, empty if is parent
	 */
	function createSysFunction($functionId, $name, $description, $parent = "0") {
		$functionId = strtoupper($functionId);
		if (getDBCell("sys_functions", "FUNCTION_ID", "UPPER(FUNCTION_ID) = UPPER('$functionId')") == "") {
			global $db;

			$sql = "INSERT INTO sys_functions (FUNCTION_ID, PARENT_ID, DESCRIPTION, NAME) VALUES ('$functionId', '$parent', '$description', '$name')";
			$query = new query($db, $sql);
			$query->free();
		}
	}

	/**
	 * Delete a Syste-Function
	 *
	 * @param string Name of the function to delete
	 */
	function deleteSysFunction($functionId) {
		deleteRow("sys_functions", "FUNCTION_ID = UPPER('$functionId')");

		deleteRow("role_sys_functions", "FUNCTION_ID = UPPER('$functionId')");
	}

	/**
	 * Add a Function to a role
	 *
	 * @param string ID of the Function
	 * @param string Name of the role
	 */
	function addFunctionToRole($functionId, $roleName) {
		$functionId = strtoupper($functionId);

		$rlid = getDBCell("roles", "ROLE_ID", "UPPER(ROLE_NAME) = UPPER('$roleName')");
		deleteRow("role_sys_functions", "ROLE_ID = $rlid AND FUNCTION_ID = '$functionId'");
		global $db;
		$sql = "INSERT INTO role_sys_functions (ROLE_ID, FUNCTION_ID) VALUES ( $rlid, '$functionId')";
		$query = new query($db, $sql);
		$query->free();
	}

	/**
	 * Remove a Function from a role
	 *
	 * @param string ID of the Function
	 * @param string Name of the role
	 */
	function removeFunctionFromRole($functionId, $roleName) {
		$functionId = strtoupper($functionId);

		$rlid = getDBCell("roles", "ROLE_ID", "UPPER(ROLE_NAME) = UPPER('$roleName')");
		deleteRow("role_sys_functions", "ROLE_ID = $rlid AND FUNCTION_ID = '$functionId'");
	}
?>