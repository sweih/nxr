<?php
/**********************************************************************
 *	N/X - Web Content Management System
 *	Copyright 2002 Sven Weih, FZI Research Center for Information Technologies
 *	www.fzi.de
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

	
	class SitepageSelector extends DBO {
	
	  var $livepages;
	/**
 	 * standard constructor
 	 * @param string Text that is to be shown as description or label with your object.
 	 * @param string Table, you want to connect with the object.
 	 * @param string column, you want to connect with the object.
 	 * @param string $row_identifier Usually to be generated with form->setPK. Identifies the
 	 * row in db, you want to affect with operation. Keep empty for insert. (Example: stdEDForm)
 	 * @param string $params Allowed parameter is:
 	 * param:<Name of form> needed for js-reasons.
 	 * @param string $check Does checks on user input. Allowed are MANDATORY (=not null)|UNIQUE. Separate with &.
 	 * @param string $db_datatype Datatype of the database, you want to use. Allowed is DATE only.
 	 */
 	 function SitepageSelector($label, $table, $column, $row_identifier="1", $params="", $check="", $db_datatype="NUMBER", $livepages = false) {
 	 	DBO::DBO($label, $table, $column, $row_identifier, $params, $db_datatype, $check);		
 		$this->v_wuiobject = new Dropdown($this->name, null , $this->std_style, $this->value, 300, 1 );
 		if ($this->value=="" || $this->value==0) $this->value = "0";
 		$this->livepages = $livepages;
 	}
 	
 	/**
 	 * draw the object.
 	 */
 	 function draw() {
		/** Create the value array **/
 		global $db;
 		$folders = null;
 		$folders[0][0]="&gt;";
 		$folders[0][1]=0;
 		
 		if ($this->livepages) {
 		 $this->createFolders($folders,"&gt;", translateState(0,10,false));		
 		} else {
 		  $this->createFolders($folders,"&gt;", 0);		
 		}
 		
 	 	// set the values
 	 	$this->v_wuiobject->value = $folders;
 	 	// draw the object
 	 	DBO::draw();
 	 	return 2;
 	 }
 	
 	/**
 	 * Used to create a directory tree of the sitepages in a page
 	 * Recursive function. 
 	 * Create a global variable $isFolder if you are moving folders, because there are special rules then.
 	 * @param array array with name-value pairs of the folders
 	 * @param string prefix, which to write in front of all foldernames. Leave blank, is internally used.
 	 * @param integer node where to start indexing
 	 */
 	function createFolders(&$folder, $prefix, $node) {
	 	global $db, $oid, $c;
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
 			$this->createFolders($folder, $nprefix, $id);
  		}
 		$query->free();
 	
 	
 	}

}


/**
 * Selector without database access
 */
class SitepageSelector2 extends SitepageSelector {
	
  /**
   * Standard constructor
   */
  function SitepageSelector2() {  
    $this->name = "spid";
    $this->value = initValue("spid", "sspid", "0");	
  }

  /**
   * draw the object.
   */
  function draw() {
	global $db;
 	$folders = null;
 	$folders[0][0]="&gt;";
 	$folders[0][1]=0;
 	
 	$this->createFolders($folders,"&gt;", 0);		
 	$dd =  new Dropdown($this->name, $folders , "bcopy", $this->value, 500, 1 );	
 	$dd->draw();
  }
  
  /**
   * Process the data. Do nothing here
   */
  function process() {}
	
}

   