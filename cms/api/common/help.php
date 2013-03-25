<?
	/**
	 * @module Common
	 * @package Management
	 */


  /** 
   * adds a parameter to a given url whether with ? or &
   * @param string URL
   * @param string Parameter in format paramname=value
   * 
   * Based on: http://www.phpinsider.com/smarty-forum/viewtopic.php?t=4356  
   */
  function addParam($url, $paramstring) {
    
    $paramData = explode("=",$paramstring);
    $paramName = $paramData[0];  
    $paramValue = $paramData[1];
    
    // first check whether the parameter is already 
    // defined in the URL so that we can just update 
    // the value if that's the case. 
    
    if (preg_match('/[?&]('.$paramName.')=[^&]*/', $url)) { 
    
      // parameter is already defined in the URL, so 
      // replace the parameter value, rather than 
      // append it to the end. 
      $url = preg_replace('/([?&]'.$paramName.')=[^&]*/', '$1='.$paramValue, $url) ; 
    
    } else { 
      // can simply append to the end of the URL, once 
      // we know whether this is the only parameter in 
      // there or not. 
      $url .= strpos($url, '?') ? '&' : '?'; 
      $url .= $paramName . '=' . $paramValue; 
    
    } 
    return $url ;
    
    
	  
  }

/**
 * Fix for php4.
 */
	if(!function_exists('str_ireplace')) {
   function str_ireplace($search, $replacement, $string){
       $delimiters = array(1,2,3,4,5,6,7,8,14,15,16,17,18,19,20,21,22,23,24,25,
       26,27,28,29,30,31,33,247,215,191,190,189,188,187,186,
       185,184,183,182,180,177,176,175,174,173,172,171,169,
       168,167,166,165,164,163,162,161,157,155,153,152,151,
       150,149,148,147,146,145,144,143,141,139,137,136,135,
       134,133,132,130,129,128,127,126,125,124,123,96,95,94,
       63,62,61,60,59,58,47,46,45,44,38,37,36,35,34);
       foreach ($delimiters as $d) {
           if (strpos($string, chr($d))===false){
               $delimiter = chr($d);
               break;
           }
       }
       if (!empty($delimiter)) {
           return preg_replace($delimiter.quotemeta($search).$delimiter.'i', $replacement, $string);
       }
       else {  
           trigger_error('Homemade str_ireplace could not find a proper delimiter.', E_USER_ERROR);
       }
   }
}
	
	
	/**
	 * Reverse htmlspecialchars
	 *
	 * @param string $string input string
	 * @return string reversed output string
	 */
	function unhtmlspecialchars( $string )   {
       $string = str_replace ( '&amp;', '&', $string );
       $string = str_replace ( '&#039;', '\'', $string );
       $string = str_replace ( '&quot;', '\"', $string );
       $string = str_replace ( '&lt;', '<', $string );
       $string = str_replace ( '&gt;', '>', $string );
       $string = str_replace ( '\\"', '"', $string);
       
       return $string;
   }
	 
	 /**
	  * Places a static html file at the www or wwwdev folder that show a under maintenance message.
	  * @param string live or dev depeding on the site you want to set to maintenance.
	  */
	 function switchToMaintenanceMode($site="live") {
	   global $c;
       $path = $c[$site."path"];
	   if ($path != "") {
 	     if (@file_exists($path."index.php")) @rename($path."index.php", $path."index.php.php");
 	     if (@file_exists($path."index.html")) @rename($path."index.html", $path."index.html.html"); 	     
 	     @copy($c["path"]."modules/maintenance_mode/index.html", $path."index.html");
 	     @copy($c["path"]."modules/maintenance_mode/index.html", $path."index.php");
	   }		 	
	 }
	 
	 /**
	  * Disables the maintenance page
	  * @param string live or dev depending on the site you want to enable
	  */
	 function disableMaintenanceMode($site="live") {
	   global $c;
       $path = $c[$site."path"];
	   if ($path != "") {
	     if (@file_exists($path."index.php")) @unlink($path."index.php");
 	     if (@file_exists($path."index.html")) @unlink($path."index.html"); 	     
 	     @rename($path."index.php.php", $path."index.php");
 	     @rename($path."index.html.html", $path."index.html");
	   } 	      	   
	 }
	 
	 /**
	  * Compare two strings
	  * @param string String1
	  * @param string String2
	  */
	  function sameText($str1, $str2) {
	  	return (strtoupper(trim($str1)) == strtoupper(trim($str2)));
	  }
	 
	/**
	 * Switch panic on
	 */
	 function pon() {
	   global $panic;
	   $panic = true;	
	 }
	 
	 /**
	 * Switch panic off
	 */
	 function poff() {
	   global $panic;
	   $panic = false;	
	 }

	/**
	 * Switch debug on
	 */
	 function don() {
	   global $debug;
	   $debug = true;	
	 }
	 
	 /**
	 * Switch debug off
	 */
	 function doff() {
	   global $debug;
	   $debug = false;	
	 }
	 
	 /**
	  * dump an error message and write it to the DBLog
	  */
	 function nxlog($category, $message, $silent=false) {
	 	$log = new DBLog($category);
	 	if (!$silent) 
	 		echo strtoupper($category).": ".$message." -- <br>\n";
	 	$log->log($message);
	 	unset($log);
	 }
	
	
  /** 
   * Takes an array with CLCIDs and returns the
   * corresponding array with CLNIDS
   */
  function createCLNIDsFromCLCIDs($clcids) {
    global $db;
    $result = array();
    for ($i=0; $i<count($clcids); $i++) {
      $sql = "SELECT cv.CLNID FROM cluster_variations cv, cluster_content cc WHERE cc.CLCID = ".$clcids[$i]." AND cc.CLID = cv.CLID";
      $query = new query($db, $sql);
      if ($query->getrow())
        $result[] = $query->field("CLNID");    
    }
    return array_unique($result);
  }
  
  /**
   * Unzip a ZIP-File or OpenOffice Document. Requires the zip-extension.
   * @param string Path to zip file
   * @param string Path to unzip to, must end with Backslash
   * @author Found at php.net
   */
  function nxunzip($file, $path) {
    if (function_exists("zip_open")) {
     	$zip = zip_open($file);
      if ($zip) {
       while ($zip_entry = zip_read($zip)) {
         if (zip_entry_filesize($zip_entry) > 0) {
           // str_replace must be used under windows to convert "/" into "\"
           $complete_path = $path.str_replace('/','\\',dirname(zip_entry_name($zip_entry)));
           $complete_name = $path.str_replace ('/','\\',zip_entry_name($zip_entry));
           if(!file_exists($complete_path)) { 
             $tmp = '';
             foreach(explode('\\',$complete_path) AS $k) {
               $tmp .= $k.'\\';
               if(!file_exists($tmp)) {
                 mkdir($tmp, 0777); 
               }
             } 
           }
           if (zip_entry_open($zip, $zip_entry, "r")) {
             $fd = fopen($complete_name, 'w');
             fwrite($fd, zip_entry_read($zip_entry, zip_entry_filesize($zip_entry)));
             fclose($fd);
             zip_entry_close($zip_entry);
           }
         }
       }
       zip_close($zip);
      }
    } else {
    	echo "php_zip extension is not installed.";
    }
  }
  
  
  /**
   * Delete a whole directory. 
   * @param string path to delete
   * @author found at php.net
   */
  function nxRMDir ($del_path)  {    
   if (strpos($del_path, '..') != false ) exit();
   $dir = $del_path; 
   $current_dir = opendir($dir);
   while($entryname = readdir($current_dir)){
       if(is_dir("$dir/$entryname") and ($entryname != "." and $entryname!="..")){
       nxRMDir("${dir}/${entryname}");
     }elseif($entryname != "." and $entryname!=".."){
       unlink("${dir}/${entryname}");
     }
  }
  closedir($current_dir);
  rmdir(${dir});
  } 
	
	
	/**
	 * Creates a new unique name for a copied item by adding an incrementing number to the name.
	 *
	 * @param string Name of table in which row shall be copied
	 * @param string Column in which the name shall be looked for
	 * @param string new name for the item
	 * @param string SQL-Where-Clause compatible filter
	 * @param string prefix to add to the beginning of the new name
	 * @param boolean specifies if the prefix is added also if $newName is already unique
	 */
	function makeCopyName($table, $nameColumn, $newName, $filter = "1", $prefix = "", $forceprefix = true) {
		if ($filter != "")
			$filter = $filter . " AND ";
		if ($forceprefix)
			$newName = $prefix . $newName;
		$tryName = $newName;
		$count = 2;
		resetDBCache();
		while (getDBCell($table, $nameColumn, $filter . $nameColumn . " = '$tryName'") != "") {
			$tryName = $prefix . $newName . " ($count)";
			$count++;
		}
		$newName = $tryName;
		return $newName;
	}
	
	/**
	 * Initialize a value. Tries to get it from request. If it is not in
	 * request, get it from repository. If it is not in rep. take default.
	 * @param string Name of the variable in Request
	 * @param string Name of the variable in Repository
	 * @param string Default value of the variable
	 * @param string check, Checks to perform when fetching the variable from request
	 */
	function initValue($name, $repositoryName, $default, $checks="NOSPACES") {	
	  	$result = value($name, $checks, "0");
     		if ($result != "0") {
     	  		pushVar($repositoryName, $result);
     		} else {
     	  		$result = getVar($repositoryName);
     		}
     		if ($result=="") $result = $default;
     		return $result;
	}
	
	/**
	 * Initialize a value. Tries to get it from request. If it is not in
	 * request, get it from repository. If it is not in rep. take default. 
	 * This function allows also "0" as value.
	 * @param string Name of the variable in Request
	 * @param string Name of the variable in Repository
	 * @param string Default value of the variable
	 * @param string check, Checks to perform when fetching the variable from request
	 */
	function initValueEx($name, $repositoryName, $default, $checks="NOSPACES") {
	  	if (isset($_POST[$name]) || isset($_GET[$name])) {
	  		$result = value($name, $checks, "");
     			pushVar($repositoryName, $result);
       		} else {
       			$result = getVar($repositoryName);
     		}
     		if ($result=="") $result = $default;
     		return $result;
	}
	
	/**
	 * Display a message in your browser with JS popup window.
	 * @param $message Message that will be displayed.
	 */
	function message($message) {
		$o = "<script language='Javascript'>";

		$o .= "  alert('$message');";
		$o .= "</script>";
		echo $o;
	}

	// replaces wildcards in the form %%wildcard%%
	function text_wildcards_replace($text, $array) {
		if (!is_array($array))
			return $text;

		reset ($array);

		while (list($key, $value) = each($array)) {
			$text = str_replace('%%' . $key . '%%', $value, $text);
		}

		$text = stripcslashes($text);
		return ($text);
	}

	// converts an associative array into a human readable text
	function array2txt($data) {
		if (is_array($data)) {
			foreach ($data as $key => $value) {
				$fillspaces_count = 0;

				$fillspaces = "";
				$fillspaces_count = 18 - strlen($key);

				for ($i = 1; $i <= $fillspaces_count; $i++) {
					$fillspaces .= " ";
				}

				$txt .= ucfirst($key). ":" . $fillspaces . $value . "\n";
			}
		} else {
			$txt = $data;
		}

		return $txt;
	}

	function sendForm($formdata, $mail_to, $mail_to_name, $mail_from, $mail_from_name, $mail_subject, $mail_text) {
		global $c;

		require_once $c["path"] . "modules/mimeMail/mail.php";
		$wcards['formdata'] = array2txt($formdata);
		$plain_text = text_wildcards_replace($mail_text, $wcards);
		mail_simple_send($mail_to, $mail_to_name, $mail_from, $mail_from_name, $mail_subject, $plain_text);
	}

	function getSPNameUrlSafe($spid) {
		$spname = getSPName($spid);
		$spname = makeURLSave($spname);
		return $spname;
	}
	
	/**
	 * Ensures the string is url and directory save
	 *
	 * @param string $uri URI to encode
	 */
	function makeURLSave($uri) {
		$spname = strtolower($uri);
		$spname = stripslashes($spname);
		$spname = strip_tags($spname);
		$spname = str_replace("&", "", $spname);
		$spname = str_replace("?", "", $spname);
		$spname = str_replace(",", "", $spname);
		$spname = str_replace(";", "", $spname);
		$spname = str_replace("#", "", $spname);
		$spname = str_replace("$", "", $spname);
		$spname = str_replace("§", "", $spname);
		$spname = str_replace("(", "_", $spname);
		$spname = str_replace(")", "_", $spname);
		$spname = str_replace("%", "", $spname);
		$spname = str_replace("@", "", $spname);
		$spname = str_replace("/", "", $spname);
		$spname = str_replace("!", "_", $spname);
		$spname = str_replace("\\", "", $spname);	
		$spname_split = explode(' ', $spname);
		$spname = implode('_', $spname_split);	
		return $spname;
	}
	
	/**
	 * Get the nice pageURL
	 *
	 * @param integer sitemap.menuId
	 * @param integer variationId
	 */
	function getPageURL($menuId, $v) {
		$result = "";
		while ($menuId != "") {				  
		  $tmpresult = makeURLSave(getDBCell("sitemap", "NAME", "MENU_ID=".$menuId));
		  if ($tmpresult != "")
		    $result =	'/'.$tmpresult.$result;
		  $menuId = getDBCell("sitemap", "PARENT_ID", "MENU_ID=".$menuId); 	    
	  }
		$result = getDBCell('variations', 'SHORTTEXT', 'VARIATION_ID='.$v).$result;
		return $result;
	}
	
	/**
	 * Get the nice URL of an channel-article
	 *
	 * @param integer $articleId
	 * @param integer $v - VariationId		
	 */
	function getArticleURL($articleId, $v) {
	  global $lang;
	  $cat = getDBCell('channel_articles', 'CH_CAT_ID', "ARTICLE_ID = ".$articleId);	  
	  $name = getDBCell('channel_articles', 'TITLE', "ARTICLE_ID = ".$articleId);	  
	  $catname = getDBCell('channel_categories', 'NAME', 'CH_CAT_ID='.$cat);
	  $spid = getDBCell('channel_categories', 'PAGE_ID', 'CH_CAT_ID='.$cat);
	  if ($spid != "") {
	    $spid = getDBCell("state_translation", "OUT_ID", "IN_ID=$spid AND LEVEL=10");
	    if ($spid != "") {
	      $menuId = getDBCell('sitepage', 'MENU_ID', 'SPID='.$spid);
	      $result = getPageURL($menuId, $v);
	      $result.='/'.makeURLSave($catname);
	      $result.='/'.makeURLSave($name);
	    } else {
	    	$result = $lang->get('url_disp_later', 'The URL will be displayed after the linked template was launched.');
	    }
	  }
	  return $result;		
	}

	/**
	 * Checks, whether the keys variation and changevariation are set and changes
	 * the selected variations. Returns always the selected or standard variation.
	 */
	function variation() {
		if (value("variation") != "0" && value("variation") != "-1" && value("changevariation") != "0" && value("changevariation") != "") {
			pushVar("variation", value("variation", "NUMERIC"));
			pushVar("translation", "0");
		}

		$result = getVar("variation");

		if ($result == "") {
			global $c;

			$result = $c["stdvariation"];
		}

		global $variation;
		$variation = $result;
		return $result;
	}

	
	/**
	 * Checks, whether the keys variation and changevariation are set and changes
	 * the selected variations. Returns always the selected or standard variation.
	 */
	function translation() {
		if (value("translation") != "0" && value("translation") != "-1" && value("changetranslation") != "0" && value("changetranslation") != "") {
			pushVar("translation", value("translation", "NUMERIC"));
		}

		$result = getVar("translation");

		if ($result == "") {
			global $c;

			$result = 0;
		}

		if ($result > 0) {
			return $result;
		} else {
		    return 0;	
		}
	}

	
	/**
	 * Determines the URL of the script which made the last call.
	 */
	function doc() {
		$temp = explode("?", $_SERVER["REQUEST_URI"]);
		$temp2 = explode("/", $temp[0]);
		return $temp2[count($temp2) - 1];
	}

	/**
	 * Determines the complete Path and URL of the script which made the last call
	 */
	function docRoot() {
		$temp = explode("?", $_SERVER["REQUEST_URI"]);
		return $temp[0];
	}
 


	/**
	 * Returns the value of a variable sent with post or get
	 * returns "", if variable not found.
	 * @param string $variable_name Name of the variable to get value of
	 * @param string $validate Validate the input variable. Allowed are NUMERIC, NOSPACES
	 * @param string $default Value that is set if value is not found.
	 */
	function value($variable_name, $validate = "", $default = "0") {
		$result = $default;

		if (isset($_GET[$variable_name]))
			$result = $_GET[$variable_name];
		else if (isset($_POST[$variable_name]))
			$result = $_POST[$variable_name];

		// doing the validation check
		$validate = strtoupper($validate);

		if ($validate == "NUMERIC") {
			if ((!is_numeric($result)) && $result != "" && $result != 0) {
				$log = new DBLog("INTRUSION");

				$log->log("There seems to be a variable modification on variable " . $variable_name . " on page " . $_SERVER["REQUEST_URI"] . ". The request was blocked. IP:" . $_SERVER['REMOTE_ADDR']);
				echo "The provided data is not of correct type.";
				exit();
				$result = $default;
			}
		} else if ($validate == "NOSPACES") {
			if (strstr($result, " ") != false) {
				global $auth;

				$log = new DBLog("INTRUSION");
				$log->log("There seems to be a variable modification on variable " . $variable_name . " on page " . $_SERVER["REQUEST_URI"] . " The request was blocked. IP:" . $_SERVER['REMOTE_ADDR']);
				echo "Type mismatch! Exiting....";
				exit();
				$result = $default;
			}
		}
	 	
	 	if ($result == "") 
		    $result = $default;		

		return $result;
	}

	/**
	 * Retrieves the module_id from a given CLCID
	 * @param integer CLCID of a cluster-content-item.
	 * @returns integer MODULE_ID of CLCID.
	 */
	function getModuleFromCLC($clc) {
		global $db;

		$sql = "SELECT ct.FKID FROM cluster_template_items ct, cluster_content c WHERE c.CLCID = $clc AND c.CLTI_ID = ct.CLTI_ID";

		$query = new query($db, $sql);
		$query->getrow();
		$mod = $query->field("FKID");
		$query->free();

		return $mod;
	}

	/**
		  * Retrieves the CLTITYPE_ID from a given CLCID
		  * @param integer CLCID of a cluster-content-item.
		  * @returns integer CLTITYPE_ID of CLCID.
		  */
	function getTypeFromCLC($clc) {
		global $db;

		$sql = "SELECT ct.CLTITYPE_ID FROM cluster_template_items ct, cluster_content c WHERE c.CLCID = $clc AND c.CLTI_ID = ct.CLTI_ID";

		$query = new query($db, $sql);
		$query->getrow();
		$mod = $query->field("CLTITYPE_ID");
		$query->free();

		return $mod;
	}

	/**
	 * Creates a directory tree of the category-table
	 * Recursive function. require_onces an open Database-connection! 
	 * @param array array with name-value pairs of the folders
	 * @param string prefix, which to write in front of all foldernames. Leave blank, is internally used.
	 * @param integer node where to start indexing
	 * @param integer $stopnode  This ID is excluded from the tree.
	 */
	function createFolders(&$folder, $prefix, $node, $stopnode="-1") {
		global $db;

		$sql = "SELECT CATEGORY_ID, CATEGORY_NAME from categories WHERE DELETED = 0 AND PARENT_CATEGORY_ID=$node AND CATEGORY_ID <> $stopnode ORDER BY CATEGORY_NAME ASC";
		$query = new query($db, $sql);

		while ($query->getrow()) {
			$name = $query->field("CATEGORY_NAME");
			$id = $query->field("CATEGORY_ID");

			$nextId = count($folder);
			$nprefix = $prefix . "&nbsp;" . $name . "&nbsp;&gt;";

			if ($id != $oid) {
				$folder[$nextId][0] = $nprefix;
				$folder[$nextId][1] = $id;
				createFolders($folder, $nprefix, $id, $stopnode);
			}
		}

		$query->free();
	}

	
	
	/**
	 * Return the drawn content of a library object
	 * @param integer Content-ID of the object
	 * @param integer Variation-ID to select
	 * @param mixed additional parameters (ALL or s.th. else)
	 */
	function getLibraryContent($cid, $variation, $param=null) {
		$ref = createPGNRef2($cid, $variation);
		if (is_object($ref)) return $ref->draw($param);			
	}
	
	/**
	 * Return the drawn content of a directly referenced content.
	 * @param integer FKID of the Plugin
	 * @param string Name of the Plugin
	 * @param mixed additional parameters (ALL or s.th. else)
	 */
	function getPluginContent($fkid, $pluginName, $param) {
		if ($fkid != 0 && $fkid != "0") {
			$pluginId = getDBCell("modules", "MODULE_ID", "UPPER(MODULE_NAME) = '".strtoupper($pluginName)."'");		 	
			$ref = createPGNRef($pluginId, $fkid);
			return $ref->draw($param);
		}
	}
	
	/**
	 * Creates an instance of the Selected module and variation.
	 * returns the plugin-Class
	 * @param integer ID of the content
	 * @param integer Variation ID to select appropriate recordset for.
	 * @return Object	Object of type Plugin.
	 */
	function createPGNRef2($contentId, $variation=0) {
		global $c, $cds;
		if ($variation==0 && ! is_object($cds)) $variation = variation();
		$modId = getDBCell("content", "MODULE_ID", "CID=".$contentId);
		$check = getDBCell("content_variations", "VARIATION_ID", "VARIATION_ID = $variation AND CID = $contentId");
		if ($check =="") {
			$variation = getDBCell("content_variations", "VARIATION_ID", "CID = $contentId");
		}
		$fkid = getDBCell("content_variations", "FK_ID", "CID = $contentId AND VARIATION_ID = ".$variation);
		
        if ($modId != "") {
			includePGNSource($modId);
			return createPGNRef($modId, $fkid);
        }
	}
	
	/**
	 * Creates an instance of the Selected module and fkid.
	 * returns the plugin-Class
	 * @param integer ID of the plugin
	 * @param integer Primary-key ID of the Recordset to be referenced.
	 * @param integer ID of the clustertemplateitem
	 * @return Object	Object of type Plugin.
	 */
	function createPGNRef($pluginId, $fkid, $cltiid="0") {
		global $c;

		$classname = getDBCell("modules", "CLASS", "MODULE_ID = $pluginId");

		if ($classname != "") {			
			$PGNRef = new $classname($fkid, $cltiid);
		} else {
			$PGNRef = null;
		}

		return $PGNRef;
	}
		

	/**
	 * Loads the source code of all! Plugins
	 */
	function includePGNSources() {
		global $c;

		require_once $c["path"]."plugin/plugin.inc.php";
		$sources = createDBCArray("modules", "SOURCE");

		for ($i = 0; $i < count($sources); $i++) {
			require_once $c["path"] . "plugin/" . $sources[$i];
		}
	}

	/**
	 * Loads the source code of all Plugins with input-fields
	 */
	function includePGNISources() {
		global $c;
		
		require_once $c["path"]."plugin/plugin.inc.php";
		$sources = createDBCArray("modules", "SOURCE", "MODULE_TYPE_ID=1");

		for ($i = 0; $i < count($sources); $i++) {
			require_once $c["path"] . "plugin/" . $sources[$i];
		}
	}

	/**
	 * Loads just one src-file of a special plugin.
	 * @param integer ID of the Module/PlugIn
	 */
	function includePGNSource($id) {
		global $c;

		$require_onced = getDBCell("modules", "SOURCE", "MODULE_ID = $id");

		if ($require_onced != "")
			require_once $c["path"] . "plugin/" . $require_onced;
	}

	/**im
	* Checks, whether there are form variables named commit or cancel in the sent
	* data.
	*
	* @return string 	"YES", if user clicked the Commit button
	* 			"NO", if user clicked the Cancel button
	*			"", if no variable was found.
	*/
	function checkPrompt() {
		global $commit, $cancel;

		$commit = value("commit");
		$cancel = value("cancel");

		$output = "";

		if ($commit != "0")
			$output .= "YES";

		if ($cancel != "0")
			$output .= "NO";

		return $output;
	}

	function strpos_case_insensitive($haystack, $needle, $offset = 0) {
		$haystack = substr($haystack, $offset, strlen($haystack));

		$temp = stristr($haystack, $needle);
		$pos = strlen($haystack) - strlen($temp);

		if ($pos == strlen($haystack))
			$pos = FALSE;
		else
			$pos += $offset;

		return $pos;
	}
	
 	/**
 	 * Used to create a directory tree of the sitepages in a page
 	 * Recursive function. 
 	 * (copied from sitepage_selector by FK)
 	 * Create a global variable $isFolder if you are moving folders, because there are special rules then.
 	 * @param array array with name-value pairs of the folders
 	 * @param string prefix, which to write in front of all foldernames. Leave blank, is internally used.
 	 * @param integer node where to start indexing
 	 */
 	function createSitepageTree(&$folder, $prefix, $node) {
 		global $db, $c, $oid;
	 	$isFolder = true;
 		$sql = "SELECT MENU_ID, SPM_ID, NAME from sitemap WHERE DELETED = 0 AND PARENT_ID=$node ORDER BY POSITION ASC";
 		$query = new query($db, $sql);
 		while ($query->getrow()) {
 			$name = $query->field("NAME");
 			$id = $query->field("MENU_ID");
 			$spm = $query->field("SPM_ID");
 			$sql = "SELECT SPMTYPE_ID FROM sitepage_master WHERE SPM_ID = $spm";
 			$tquery = new query($db, $sql);
 			$tquery->getrow();
 			$spmType = $tquery->field("SPMTYPE_ID");
 			$tquery->free();
 			
 			$nprefix = $prefix."&nbsp;".$name."&nbsp;&gt;";
 			
 			$sql = "SELECT SPID FROM sitepage WHERE MENU_ID = $id ORDER BY POSITION";
 			$subquery = new query($db, $sql);
 			while ($subquery->getrow()) {
	 			$nextId = count($folder);
	 			$spid = $subquery->field("SPID");
	 			if ($spmType == 1) {
	 				$folder[$nextId][0] = $nprefix;
 					$folder[$nextId][1] = $spid;
				} else {
					if ($spid != "") {
						$sql = "SELECT NAME FROM sitepage_names WHERE SPID = $spid AND VARIATION_ID = ".$c["stdvariation"];
						$nquery = new query($db, $sql);
						$nquery->getrow();
						$myname = $nquery->field("NAME");
						$nquery->free();
						if ($myname != "") {
							$folder[$nextId][0] = $nprefix.$myname;
 							$folder[$nextId][1] = $spid;
 						}
 					}
				}	
 			} 
 			$folder = createSitepageTree($folder, $nprefix, $id);
  		}
 		$query->free();
 		
 		return $folder;
 	
 	}	
?>
