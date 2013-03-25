<?
	/**
	 * @module Copy
	 * @package Tools
	 */

	
	/**
	 * Copy a file from one location to another one. Wrapper function for
	 * different access-types (filesystem, ftp). Currently only fs is supported.
	 * $destpath and $destFile must be a valid location.
	 *
	 * @param string $source Source-File of the Copy Operation.
	 * @param string $destPath Destination-Location, to copy the sourcefile to
	 * @param string $destFile Destination-File to copy the source to.
	 */
	 function nxCopy($source, $destPath, $destFile) {
	 	global $c, $deploy;
	 	if ($destPath == $c["cachepath"]) {
	 		// launchCache
	 		// at first we always do the usual copy to live-path
	 	    _executeCopy($source, $destPath, $destFile, "file");
			for ($i=0; $i < count($deploy); $i++) {
	 		   if ($deploy[$i]["mode"] == "file") {
	 			  _executeCopy($source, $deploy[$i]["cachepath"], $destFile, "file");
	 		  } else if ($deploy[$i]["mode"] == "ftp") {
	 			  $conn = getFTPConnection($deploy[$i]["ftpserver"], $deploy[$i]["ftpport"], $deploy[$i]["ftpuser"], $deploy[$i]["ftppasswd"], $deploy[$i]["ftprootdir"]);
	 			  _executeCopy($source, $deploy[$i]["cachepath"], $destFile, "ftp", $conn);
	 			  $conn->ftp_quit();
	 		  }
	 	  }	 		 		
	 	} else { 	
	 	  // at first we always do the usual copy to live-path
	 	  _executeCopy($source, $destPath, $destFile, "file");
	 	
	 	  for ($i=0; $i < count($deploy); $i++) {
	 		  $newDestPath = str_replace($c["livepath"],$deploy[$i]["livepath"], $destPath);
	 		  if ($deploy[$i]["mode"] == "file") {
	 			  _executeCopy($source, $newDestPath, $destFile, "file");
	 		  } else if ($deploy[$i]["mode"] == "ftp") {
	 			  $conn = getFTPConnection($deploy[$i]["ftpserver"], $deploy[$i]["ftpport"], $deploy[$i]["ftpuser"], $deploy[$i]["ftppasswd"], $deploy[$i]["ftprootdir"]);
	 			  _executeCopy($source, $newDestPath, $destFile, "ftp", $conn);
	 			  $conn->ftp_quit();
	 		  }
	 	  }
	 	}
	 } 
	  
	 /**
	  * Help function of nxCopy. copies a file from source to destination
	  * @param string $source Source-File of the Copy Operation.
	  * @param string $destPath Destination-Location, to copy the sourcefile to
	  * @param string $destFile Destination-File to copy the source to.
	  * @param string $mode file or ftp transfer.
	  * @param object FTP-Connection-Object if needed.
	  */
	 function _executeCopy($source, $destPath, $destFile, $mode, $conn=null) {
	 	if (file_exists($source)) {
	 		if ($mode == "file") {
	 			if (@copy($source, $destPath.$destFile)) {	 	  			
	 	  			return true;
	 			} else {
	 				$log = new DBLog("LAUNCH");
	  		   		$log->log($destPath.$destFile." could not be launched.");
		  		   	unset($log);
	  			   	return false;
		 		}
		 	} else if ($mode == "ftp") {
		  	  	$conn->ftp_chdir($destPath);
		 		if ($conn->ftp_file_exists($destFile)) {
		  	  	  $conn->ftp_delete($destFile);	
		  	  	}
		  	  	$conn->ftp_put($destFile, $source);	  	  		
		  	}
	 		return true;
	 	} else {
	 		$log = new DBLog("LAUNCH");
	  		$log->log($source." could not be found for launch.");
		   	unset($log);
	 	}
	 	return false;
	 }
	 
	 /**
	  *  Deletes a file from a location. Wrapper function for different access-types 
	  * (filesystem, ftp). 
	  *
	  * @param string $destPath Destination-Location where delete shall be performed.
	  * @param string $destFile Destination-File to delete.
	  */
	  function nxDelete($destPath, $destFile) {
	      global $c, $deploy;
			_executeDelete($destPath, $destFile, "file");
	      if ($destPath == $c["cachepath"]) {
	 		// delete Cache
			for ($i=0; $i < count($deploy); $i++) {
				if ($deploy[$i]["mode"] == "file") {
	 			_executeDelete($deploy[$i]["cachepath"], $destFile, "file");
	 			} else if ($deploy[$i]["mode"] == "ftp") {
	 			$conn = getFTPConnection($deploy[$i]["ftpserver"], $deploy[$i]["ftpport"], $deploy[$i]["ftpuser"], $deploy[$i]["ftppasswd"], $deploy[$i]["ftprootdir"]);
	 			_executeDelete($deploy[$i]["cachepath"], $destFile, "ftp", $conn);
	 			$conn->ftp_quit();
	 		}
			
			}	 		 		
	 	} else { 		        
	      for ($i=0; $i < count($deploy); $i++) {
	 		$newDestPath = str_replace($c["livepath"],$deploy[$i]["livepath"], $destPath);
	 		if ($deploy[$i]["mode"] == "file") {
	 			_executeDelete($newDestPath, $destFile, "file");
	 		} else if ($deploy[$i]["mode"] == "ftp") {
	 			$conn = getFTPConnection($deploy[$i]["ftpserver"], $deploy[$i]["ftpport"], $deploy[$i]["ftpuser"], $deploy[$i]["ftppasswd"], $deploy[$i]["ftprootdir"]);
	 			_executeDelete($newDestPath, $destFile, "ftp", $conn);
	 			$conn->ftp_quit();
	 		}
	 	  }
	 	}
	  }

	  
	/**
	  * Help function of nxDelete. Deletes a file destination
	  * @param string $destPath Destination-Location where delete shall be performed.
	  * @param string $destFile Destination-File to delete.
	  * @param string $mode file or ftp transfer.
	  * @param object FTP-Connection-Object if needed.
	  */
	  function _executeDelete($destPath, $destFile, $mode, $conn=null) {
	  	if ($mode == "file") {
	  		if (file_exists($destPath.$destFile)) {
	  		   @unlink($destPath.$destFile);	
	  		   return true;
	  		} else {
	  		   $log = new DBLog("LAUNCH");
	  		   $log->log($destPath.$destFile." could not be deleted.");
	  		   unset($log);
	  		   return false;	
	  		}
	  	  } else if ($mode == "ftp") {
	  	  	$conn->ftp_chdir($destPath);
	  	  	if ($conn->ftp_file_exists($destFile)) {
	  	  	  $conn->ftp_delete($destFile);	
	  	  	}	
	  	  }
	  	  return true;
	  }
	  
	  
	  /**
	   * Etablish a ftp-connection, cache connection-object
	   * @param string $server Address of the ftp-server
	   * @param string $port Port of the ftp-server
	   * @param string $user for the connection
	   * @param string $pass Password for the connection
	   * @param string StartDirectory, where to change dir to.
	   */
	   function getFTPConnection($server, $port, $user, $pass, $startdir) {
	     $ftpConn = new ftp();
		 $ftpConn->ftp_connect($server, $port);
		 $ftpConn->ftp_login($user, $pass);       	  
		 $ftpConn->ftp_chdir($startdir);
		 return $ftpConn; 	
	   }
	 
	/**
	 * Deletes a row with specified parameters
	 *
	 * @param string Name of the table to select the row from
	 * @param string Filter to select rows
	 */
	function deleteRow($table, $filter = "") {
		if ($filter != "1" && $filter != "" & $table != "") {
			global $db, $debug;

			$sql = "DELETE FROM $table WHERE $filter";
            if ($debug) echo "DELETE: ".$sql."<br>";
			$query = new query($db, $sql);
			$query->free();
		}
	}

	/**
	 * Copies a row an replaces specified values
	 * if translate is specified as value, the given id will be translated to a live id.
	 *
	 * @param string Name of table in which row shall be copied
	 * @param string Filter to apply on table to select record(s)
	 * @param array array[n]["column"]: Column to replace, array[n]["value"]: Value to set, array[n]["datatype"]: type of data (NUMBER|CHAR|DATE)
	 */
	function copyRow($table, $filter, $values) {
		global $db, $c_datatypes, $panic;		
		$sql = "SELECT * FROM $table WHERE $filter";
		$query = new query($db, $sql);

		for ($i = 0; $i < $query->count(); $i++) {
			$row = $query->getrow();

			$newRec = new CreateSet($table);

			$columns = $db->ADODB->MetaColumns($table);
			if (!is_array($columns)) return false;
			foreach ($columns as $name=>$obvalue) {
				$value[$n] = $query->field($name);
				foreach ($values as $vcol => $vval) {
					if ($name == $vcol) {
						if (sameText($vval, "translate")) {
							if (is_numeric($value[$n]) && ($value[$n] != "0"))
							  $value[$n] = translateState($value[$n], 10, false);
						} else {
						  $value[$n] = $vval;
						}
					}
				}
				$column[$n] = $name;
				$newRec->add($column[$n], $value[$n], $c_datatypes[$table][$name]);
			}
			$newRec->execute();			
		}		
	}

	/**
	 * Creates a unique filename
	 * @param string Filepath (e.g. /home/www/)
	 * @param string Filename (e.g. index)
	 * @param string Filesuffix (e.g. php)	 
	 */
	 function makeUniqueFilename($path, $name, $suffix) {
	 	$tryname = $path.$name.".".$suffix;
	 	$counter=2;
	 	while (file_exists($tryname)) {
	 		$counter++;
	 		$tryname = $path.$name.$counter.".".$suffix;
	 	}
	 	return basename(strval($tryname));
	 }
	
	

	/**
	 * Creates a scheme-dump of the DBMS and stores it in api/tools/datatypes.php
	 * @param $table Name of the table to get the scheme or * for all tables
	 */
	function writeDataTypeArray($table = "*") {
		global $c, $lang, $form;
		$output = "<?\n";
		$output .= "// This file is auto-generated. DO NOT CHANGE !!!\n";
		$output .= generateDataTypeArray($table);
		$output .= "?>";
		$fp = fopen($c["path"] . "api/tools/datatypes.php", "w");
		fwrite($fp, $output);
		fclose ($fp);

		if (isset($form))
			$form->addToTopText($lang->get("mt_generate_dta_succeeded", "<br><br>DataTypes were successfully generated and file was written.<br>"));
	}

	/**
	 * Creates an Array containing the scheme-dump of a database table or of all tables
	 * @param $table Name of the table to get the scheme or * for all tables
	 * @param $recursion Used internally for cleaning the datatypes array on recursion start
	 */
	function generateDataTypeArray($table = "*", $recursion = false) {
		global $db, $c_datatypes, $c;

		if (!$recursion) {
			$output = "";
			$c_datatypes = NULL;
		}

		if ($table == "*") {
			$tables = $db->ADODB->MetaTables();
			for ($i = 0; $i < count($tables); $i++) {
				$output .= generateDataTypeArray($tables[$i], true);
			}

		} else {
			$columns = $db->ADODB->MetaColumns($table);
						
			if (is_array($columns)) {
			  foreach ($columns as $name=>$value) {
			  	$type = strtoupper($value->type);
			  	if ($type == "INT" || $type == "TINYINT" || $type == "BIGINT" || $type == "SMALLINT" || $type == "MEDIUMINT") $type = "NUMBER";
			  	if ($type == "STRING" || $type == "VARCHAR" || $type == "CHAR") $type = "TEXT";
				$output .= "\$c_datatypes[\"$table\"][\"$name\"] = \"$type\";\n";
				$c_datatypes[$table][$name] = $type;
			  }
			}
		}

		return $output;
	}
?>