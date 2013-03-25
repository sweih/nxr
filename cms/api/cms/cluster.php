<?php
/**
 * Cluster API
 * @package CMS
 */


/**********************************************************************
 *	N/X - Web Content Management System
 *	Copyright 2004 Sven Weih
 *
 *      $Id: cluster.php,v 1.10 2004/06/25 10:23:33 sven_weih Exp $ *
 *
 *	This file is part of N/X.
 *	The initial has been setup as a small diploma thesis (Studienarbeit) at the FZI.
 *	It was be coached by Prof. Werner Zorn and Dipl.-Inform Thomas Gauweiler.
 *
 *	N/X is free software; you can redistribute it and/or modify
 *	it under the terms of the GNU General Public License as published by
 *	the Free Software Foundation; either version 2 of the License, or
 *	(at your option) any later version.
 *
 *	N/X is distributed in the hope that it will be useful,
 *	but WITHOUT ANY WARRANTY; without even the implied warranty of
 *	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *	GNU General Public License for more details.
 *
 *	You should have received a copy of the GNU General Public License
 *	along with N/X; if not, write to the Free Software
 *	Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 **********************************************************************/
 
 /**
  * Creates a new cluster node. returns the GUID
  * @param string Name of the Cluster-Node for internal use
  * @param integer GUID of the underlying Cluster-Template
  * @param integer Optional. ID which shall be used to create the Node
  */
  function createClusterNode($name, $clt, $id=null) {
 	 global $db;
 	 $name = makeCopyName("cluster_node", "NAME", parseSQL($name), "CLT_ID = $clt"); 	
 	 if ($id == null) $id = nextGUID();
 	 $sql = "INSERT INTO cluster_node (CLNID, CLT_ID, NAME, DELETED, VERSION) VALUES ($id, $clt, '$name', 0, 0)";
 	 $query = new query($db, $sql);
 	 $query->free();
 	 return $id;
  }
  
  /**
   * Creates a cluster and a cluster-node for the given clt. returns the clnid.
   * Can only import dynamic content and dynamic clusters (GUIDs of Cluster_node!).
   * @param string name of the cluster
   * @param string GUID of Cluster template
   * @param string ID od Variation to use
   * @param string array with data to post to plugins, like array("Headline" => "Test Headline", ...)
   * @param string Username to use for create
   */
  function createCluster2($name, $clt, $variationId, $data, $createUser="Internal User") {
    $name = makeCopyName("cluster_node", "NAME", parseSQL($name), "CLT_ID = $clt"); 	
    if (!is_numeric($clt)) exit();
    if (!is_numeric($variationId)) exit();
    $clnid = createClusterNode($name, $clt);
    $clid = createCluster($clnid, $variationId, $createUser);	
    $datarows = array_keys($data);
    for ($i=0; $i < count($datarows); $i++) {
      $type = getDBCell("cluster_template_items", "CLTITYPE_ID", "CLT_ID = $clt AND UPPER(NAME) = UPPER('".$datarows[$i]."')");	
      $clti = getDBCell("cluster_template_items", "CLTI_ID", "CLT_ID = $clt AND UPPER(NAME) = UPPER('".$datarows[$i]."')");	
      $clcid = getDBCell("cluster_content", "CLCID", "CLID=$clid AND CLTI_ID = $clti");
      if ($type == 2) {
        $pgType = strtoupper(getDBCell("modules", "MODULE_NAME", "MODULE_ID = ".getDBCell("cluster_template_items", "FKID", "CLT_ID = $clt AND UPPER(NAME) = UPPER('".$datarows[$i]."')")));	
        $moduleId = getDBCell("cluster_template_items", "FKID", "CLT_ID = $clt AND UPPER(NAME) = UPPER('".$datarows[$i]."')"); 
        if ($pgType== "TEXT" || $pgType=="LABEL") $data[$datarows[$i]] = urlencode($data[$datarows[$i]]);
        $xml = '<NX:CONTENT TYPE="'.$pgType.'">'.$data[$datarows[$i]].'</NX:CONTENT>';	        
        $pgn = createPGNRef($moduleId, $clcid);
        $pgn->import($xml); 	     
      } else if ($type == 4) {        
        if (is_numeric($data[$datarows[$i]])) {
        	$sql = "UPDATE cluster_content SET FKID=".$data[$datarows[$i]]." WHERE CLCID = $clcid";
        	global $db;
        	$query = new query($db, $sql);
        	$query->free();
        }
      }
    }
    return $clnid;
  }
  
  /**
   * Create a cluster 
   * @param integer GUID of the Cluster-Node
   * @param integer GUID of the Variation
   * @param string Username which shall be inserted for create-user
   */
  function createCluster($clnid, $variationId, $createUser="Internal User") {
  	 global $db;
  	 $clid = nextGUID();
	 $sql = "INSERT INTO cluster_variations (CLNID, VARIATION_ID, CLID, DELETED,CREATED_AT, CREATE_USER, LAST_CHANGED, LAST_USER ) VALUES ( $clnid, $variationId, $clid, 0, NOW()+0, '".$createUser."', NOW()+0, '".$createUser."')";	
	 $query = new query($db, $sql);
	 $query->free();
	 syncCluster($clid);
	 return $clid;
  }
 
  /**
   * Create one content-figure of a cluster
   * @param integer GUID of the Cluster
   * @param integer GUID of the Cluster-Template
   * @param integer Position of the figure
   * @param integer Reference-ID of the figure
   */
  function updateClusterItem($clid, $clti, $position, $fkid) {
    global $db;
    $sql = "UPDATE cluster_content SET POSITION=$position, FKID = $fkid WHERE CLID=$clid AND CLTI_ID=$clti";
    $query = new query($db, $sql);
    $query->free();
    return $clcid; 
  }
 
 
 
     /**
     * Delete a cluster node
     * @param integer Cluster-Node-ID
     * @param boolean also delete version?
     */
    function deleteClusterNode($clnid, $recursiveLevels = true) {
       if ($recursiveLevels) {
           deleteClusterNode(translateState($clnid, 10, false), false);
       }

       $clids = createDBCArray("cluster_variations", "CLID", "CLNID = $clnid");
       if (count($clids) > 0 ) {
              foreach ($clids as $clid) {
                       deleteCluster($clid);
              }
       }
       deleteRow("cluster_variations", "CLNID = $clnid");
       deleteRow("cluster_node", "CLNID = $clnid");
    }

    /**
     * delete a cluster
     * @param integer ID of cluster, that is to be deleted.
     */
    function deleteCluster($clid) {
        global $db;

        $delHandler = new ActionHandler("del");
        $sql = "SELECT CLCID, CLTI_ID, FKID FROM cluster_content WHERE CLID = $clid";
        $query = new query($db, $sql);

        while ($query->getrow()) {
            $clcid = $query->field("CLCID");

            $clti = $query->field("CLTI_ID");
            $fkid = $query->field("FKID");

            if ($fkid == 0 && $clti != 0) { // delete no other clusters as this is done automatically by the callers anyway.
                $sql = "SELECT FKID FROM cluster_template_items WHERE CLTI_ID = $clti";

                $cquery = new query($db, $sql);
                $cquery->getrow();
                $module = $cquery->field("FKID");
                $cquery->free();
                deletePlugin($clcid, $module);
            }
        }

        $sql = "DELETE FROM cluster_content WHERE CLID = $clid";
        $query = new query($db, $sql);
    }
    
    
	/**
	  * syncronize variations with entered data to the database.
	  * The configuration for this function must be set manually.
	  * I.E. there must be the $oid-Variable set and there must(!)
	  * be also the global vars cluster_variations_VARIATION_ID_XX
	  * set which are automatically set by the SelectMultiple2Input.
	  * and the cluster variable must be pushed.
	  */
	function syncClusterVariations() {
		global $db, $oid, $auth;

		$clt = getVar("cluster");

		//delete all variations first.
		$del = "UPDATE cluster_variations SET DELETED=1 WHERE CLNID = $oid";

		$query = new query($db, $del);

		// get list of variations
		$variations = createNameValueArray("variations", "NAME", "VARIATION_ID", "DELETED=0");

		for ($i = 0; $i < count($variations); $i++) {
			$id = $variations[$i][1];

			if (value("cluster_variations_VARIATION_ID_" . $id) != "0") {
				// create or restore variation
				// check, if variations already exists and is set to deleted.
				$sql = "SELECT COUNT(CLNID) AS ANZ FROM cluster_variations WHERE CLNID = $oid AND VARIATION_ID = $id";

				$query = new query($db, $sql);
				$query->getrow();
				$amount = $query->field("ANZ");

				if ($amount > 0) {
					$sql = "UPDATE cluster_variations SET DELETED=0 WHERE CLNID = $oid AND VARIATION_ID = $id";
				} else {
					$fk = nextGUID();

					$sql = "INSERT INTO cluster_variations (CLNID, VARIATION_ID, CLID, DELETED,CREATED_AT, CREATE_USER ) VALUES ( $oid, $id, $fk, 0, NOW()+0, '".$auth->userName."')";
				}

				$query = new query($db, $sql);
			}
		}
	}

	/**
	 * synchronize a Variation of a Cluster-Instance with the Cluster-Template.
	 * @param integer Cluster-ID, i.e. ID of the Variation.
	 */
	function syncCluster($clid) {
		global $db;

		$syncList = null;

		// determine cluster-node.
		$cln = getDBCell("cluster_variations", "CLNID", "CLID = $clid");

		// determine cluster-template.
		$clt = getDBCell("cluster_node", "CLT_ID", "CLNID = $cln");

		// get a list with the configuration of the cluster...
		$sql = "SELECT CLTI_ID, NAME, MINCARD, MAXCARD, FKID, CLTITYPE_ID FROM cluster_template_items WHERE CLT_ID = $clt AND DELETED=0 AND FKID <> 0 ORDER BY POSITION";
		$cltis = new query($db, $sql);

		while ($cltis->getrow()) {
			$cltiId = $cltis->field("CLTI_ID");

			$name = $cltis->field("NAME");
			$mincard = $cltis->field("MINCARD");
			$maxcard = $cltis->field("MAXCARD");
			$type = $cltis->field("CLTITYPE_ID");
			$fkid = $cltis->field("FKID");

			// check, if enough fields are present.
			$syncSQL = "SELECT COUNT(CLCID) AS ANZ FROM cluster_content WHERE CLID = $clid AND CLTI_ID = $cltiId AND DELETED = 0";
			$syncQuery = new query($db, $syncSQL);
			$syncQuery->getrow();
			$amount = $syncQuery->field("ANZ");

			if ($amount < $mincard) {
				// we must(!) syncronize as there are not enough fields.
				$newpos = 1;

				$maxposSQL = "SELECT MAX(POSITION) AS MPOS FROM cluster_content WHERE CLTI_ID = $cltiId AND CLID = $clid";
				$mq = new query($db, $maxposSQL);

				if ($mq->getrow())
					$newpos = $mq->field("MPOS") + 1;

				$mq->free();

				if ($type == 2) {        // dynamic content item.
					while ($amount < $mincard) {
						$nextId = $db->nextid("GUID");

						$ssql = "INSERT INTO cluster_content (CLCID, CLID, CLTI_ID, POSITION, TITLE, FKID, DELETED) VALUES ";
						$ssql .= "($nextId, $clid, $cltiId, $newpos, '', 0,0)";
						$synq = new query($db, $ssql);
						$amount++;
						$newpos++;
						$nextsync = count($syncList);
						$syncList[$nextsync][0] = $nextId;
						$syncList[$nextsync][1] = $fkid;
					}
				} else if ($type == 4 || $type == 6) { //dynamic cluster item. Create slots.
					while ($amount < $mincard) {
						$nextId = $db->nextid("GUID");

						$ssql = "INSERT INTO cluster_content (CLCID, CLID, CLTI_ID, POSITION, TITLE, FKID, DELETED) VALUES ";
						$ssql .= "($nextId, $clid, $cltiId, $newpos, '', 0,0)";
						$synq = new query($db, $ssql);
						$amount++;
						$newpos++;
					}
				} else if ($type == 5) { //library content. Create slots.
					while ($amount < $mincard) {
						$nextId = $db->nextid("GUID");

						$ssql = "INSERT INTO cluster_content (CLCID, CLID, CLTI_ID, POSITION, TITLE, FKID, DELETED) VALUES ";
						$ssql .= "($nextId, $clid, $cltiId, $newpos, '', 0,0)";
						$synq = new query($db, $ssql);
						$amount++;
						$newpos++;
					}
				} else if ($type == 8) { //channel. Create empty slots.
                     while ($amount < $mincard) {
                            $nextId = nextGUID();
                            $ssql = "INSERT INTO cluster_content (CLCID, CLID, CLTI_ID, POSITION, TITLE, FKID, DELETED) VALUES ";
                            $ssql .= "($nextId, $clid, $cltiId, $newpos, '', 0,0)";
                            $synq = new query($db, $ssql);
                            $amount++;
                            $newpos++;
                            createCenterstage($nextId);
                     }

                }
			}

		/*if ($amount > $maxcard) {
			  // there is nothing to be done. This check is to be performed when
			  // launching a cluster and therefore an error has to be prompted!
		  }*/
		}

		// traverse through synclist;
		$counter = 0;

		while (count($syncList) > $counter) {
			$PGNRef = createPGNRef($syncList[$counter][1], $syncList[$counter][0]);

			if ($PGNRef != null)
				$PGNRef->sync();

			//syncMetas($syncList[$counter][0], "CLUSTERCONTENT");
			//reactivate for having meta on each cluter-item... 
			$counter++;
		}
	} 
	
	/**
	* Launch a Cluster
	* @param integer CLNID to launch
	* @param integer ID of the level to launch to.
	* @param integer ID of the variation to launch. 
	* @returns integer Translated ID after launch
	*/
	function launchCluster($in, $level, $variation) {
		global $db;
		if (!checkACL($in)) {
			$out = translateState($in, $level);
			$sql = "SELECT * FROM cluster_node WHERE CLNID = $in";
			$query = new query($db, $sql);
			$query->getrow();
			$clt = $query->field("CLT_ID");
			$name = addslashes($query->field("NAME"));
			$cltTrans = launchClusterTemplate($clt, $level, $variation);

			$sql = "DELETE FROM cluster_node WHERE CLNID = $out";
			$query = new query($db, $sql);
			$sql = "INSERT INTO cluster_node (CLNID, CLT_ID, NAME, DELETED, VERSION) VALUES ";
			$sql .= "($out, $cltTrans, '$name', 0, $level)";
			
			$query = new query($db, $sql);
			launchClusterVariation($in, $level, $variation);
			// launch meta
			$sql = "SELECT MID FROM meta WHERE CID = $in AND DELETED=0";
			$query = new query($db, $sql);

			while ($query->getrow()) {
				launchMeta($query->field("MID"), $level);
			}

			$query->free();

			return $out;
		} else
			return translateState($in, $level);
	}
	
	
		/**
	* Launch a Cluster. Does not automatically launch the cluster-template
	* @param integer CLNID to launch
	* @param integer ID of the level to launch to.
	* @param integer ID of the variation to launch. 
	* @returns integer Translated ID after launch
	*/
	function launchCluster2($in, $level, $variation) {
		global $db;
		if (!checkACL($in)) {
			$out = translateState($in, $level);
			$sql = "SELECT * FROM cluster_node WHERE CLNID = $in";
			$query = new query($db, $sql);
			$query->getrow();
			$clt = $query->field("CLT_ID");
			$name = addslashes($query->field("NAME"));
			$cltTrans = translateState($in, $level, false);

			$sql = "DELETE FROM cluster_node WHERE CLNID = $out";
			$query = new query($db, $sql);
			$sql = "INSERT INTO cluster_node (CLNID, CLT_ID, NAME, DELETED, VERSION) VALUES ";
			$sql .= "($out, $cltTrans, '$name', 0, $level)";
			
			$query = new query($db, $sql);
			launchClusterVariation($in, $level, $variation);
			// launch meta
			$sql = "SELECT MID FROM meta WHERE CID = $in AND DELETED=0";
			$query = new query($db, $sql);

			while ($query->getrow()) {
				launchMeta($query->field("MID"), $level);
			}

			$query->free();

			return $out;
		} else
			return translateState($in, $level);
	}

	/**
* Launch a Cluster-Variation
* @param integer CLNID to launch
* @param integer ID of the level to launch to.
* @param integer ID of the variation to launch.
* @returns integer Translated ID after launch
*/
	function launchClusterVariation($in, $level, $variation) {
		global $db, $auth;		
		$out = translateState($in, $level);
		$sql = "SELECT CLID, LAST_CHANGED, CREATED_AT FROM cluster_variations WHERE CLNID = $in AND VARIATION_ID = $variation";
		$query = new query($db, $sql);
		$query->getrow();
		$clid = $query->field("CLID");
		
		$createdAt = $query->field("CREATED_AT");
		$lastChanged = $query->field("LAST_CHANGED");
		
		if ($clid == "" && $variation != 1) {
			$sql = "SELECT CLID, LAST_CHANGED, CREATED_AT FROM cluster_variations WHERE CLNID = $in AND VARIATION_ID = 1";
			$query = new query($db, $sql);
			$query->getrow();
			$clid = $query->field("CLID");			
			$lastChanged = $query->field("LAST_CHANGED");			
			$createdAt = $query->field("CREATED_AT");
			$variation = 1;
		}
		if ($lastChanged=="") $lastChanged = "NULL";
		if ($createdAt=="") $createdAt = "NULL";
		
		if ($clid != "") {
			$clidTrans = translateState($clid, $level, false);

			delExpired ($clidTrans);
			$sql = "DELETE FROM cluster_variations WHERE CLID = $clidTrans AND VARIATION_ID = $variation";
			$query = new query($db, $sql);
			$sql = "INSERT INTO cluster_variations (CLNID, VARIATION_ID, CLID, DELETED, LAST_CHANGED, CREATED_AT) VALUES ";
			$sql .= "($out, $variation, $clidTrans, 0, $lastChanged, $createdAt)";
			$query = new query($db, $sql);

			// delete the old cluster content
			$sql = "DELETE FROM cluster_content WHERE CLID = $clidTrans";
			$query = new query($db, $sql);

			// launch the content now.
			$sql = "SELECT CLCID FROM cluster_content WHERE CLID = $clid";
			$query = new query($db, $sql);

			while ($query->getrow()) {
				launchClusterContent($query->field("CLCID"), $level, $variation);
			}

			$query->free();
			
			$sql = "UPDATE cluster_variations SET LAUNCHED_AT = NOW()+0, LAUNCH_USER = '".$auth->userName."' WHERE CLID = $clid";
			$query = new query($db, $sql);
			$query->free();
		}

		return $out;
	}

	/**
* Launch a Cluster-Content-Item
* @param integer CLCID to launch
* @param integer ID of the level to launch to.
* @param integer ID of the variation to launch.
* @return integer Translated ID after launch
*/
	function launchClusterContent($in, $level, $variation) {
		global $db;

		if (!checkACL($in)) {
			$out = translateState($in, $level);

			$sql = "SELECT * FROM cluster_content WHERE CLCID = $in AND DELETED=0";
			$query = new query($db, $sql);
			$query->getrow();
			$clid = $query->field("CLID");
			$clti = $query->field("CLTI_ID");
			$posi = $query->field("POSITION");
			$title = addslashes($query->field("TITLE"));
			$fkid = $query->field("FKID");
			$delme = $query->field("DELETED");

			// get type from clti...
			$sql = "SELECT CLTITYPE_ID, FKID FROM cluster_template_items WHERE CLTI_ID = $clti";
			$query = new query($db, $sql);
			$query->getrow();
			$type = $query->field("CLTITYPE_ID");
			$clfktype = $query->field("FKID");

			$fkTrans = 0;
       		if ($fkid != 0 && ($type == 4 || $type == 6) )
				$fkTrans = launchCluster($fkid, $level, $variation);

			if ($type == 2)
				launchPlugin($in, $clfktype, $level, $clti);

			if ($fkid != 0 && $type == 5)
				$fkTrans = launchContent($fkid, $level, $variation);

            if ($type == 8)
                $fkTrans = launchCenterstage($in, $level);
			// translate ids
			$clid = translateState($clid, $level, false);
			$clti = translateState($clti, $level, false);

			$sql = "DELETE FROM cluster_content WHERE CLCID = $out";
			$query = new query($db, $sql);

			$sql = "INSERT INTO cluster_content (CLCID, CLID, CLTI_ID, POSITION, TITLE, FKID, DELETED) VALUES ";
			$sql .= "($out, $clid, $clti, $posi, '$title', $fkTrans, $delme)";
			$query = new query($db, $sql);
			$query->free();
			return $out;
		} else
			return translateState($in, $level);
	}   
	
	/**
	 * Expire a cluster-instance
	 * @param integer clnid to expire
	 * @param integer variation to expire
	 */
	 function expireCluster($clnid, $variation) {
	   global $db;
	   $clid = translateState(getDBCell("cluster_variations", "CLID", "CLNID = $clnid AND VARIATION_ID = $variation"), 10, false);	
	   $sql = "UPDATE state_translation SET EXPIRED = 1 WHERE OUT_ID = $clid";
	   $query = new query($db, $sql);
	   $query->free();
	 }
 ?>