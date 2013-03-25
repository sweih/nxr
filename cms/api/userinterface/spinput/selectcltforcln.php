<?php
	/**********************************************************************
	 *	N/X - Web Content Management System
	 *	Copyright 2002-2006 Sven Weih <sven@nxsystems.org>
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
 * Draws a selector with cluster-templates and creates a cluster-node for the cluster-template
 * Works only in editState=Insert
 * and stores the node id in the specified cell. Perfect combination for use with ClusterEditor.
 */
class SelectCLTForCLN extends DBO {

  var $cltFilter;
  var $nameColumn;

  /**
   * Standard Constructor
   *
   * @param string $label Label of the widget
   * @param string $table
   * @param string $column
   * @param string $row_identifier
   * @param string $cltfilter Whereclause for selecting the clustertemplates
   * @param string $namecolumn Column in database, where the title of the record ist stored. Used for setting name of object.
   */   
  function SelectCLTForCLN($label, $table, $column, $row_identifier, $cltFilter="1", $nameColumn="") {    
    DBO::DBO($label, $table, $column, $row_identifier, "", "NUMBER");
    $this->cltFilter = $cltFilter;
    $this->nameColumn = $nameColumn;
    $this->v_wuiobject = new Select($this->name, $this->getNameValueArray(), $this->std_style, 0, 1, "", 300, 1);
  }
  
  
  /**
   * Check for valid input
   */
  function check() { }
  
  function process() {
    global $page_action, $specialID;
    if ($page_action == "INSERT") {      
      $this->value = createClusterNode(value($this->table . "_" . $this->nameColumn, "", "Object") . $specialID, $this->value);	     	
      DBO::process();
    }
  }
  
  /**
   * Get the values for the dropdownbox
   */
  function getNameValueArray() {
  	return createNameValueArrayEx('cluster_templates', 'NAME', 'CLT_ID', $this->cltFilter.' AND VERSION=0', 'ORDER BY NAME ASC');
  }
  
}

?>