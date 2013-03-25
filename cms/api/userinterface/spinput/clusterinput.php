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
 * You define a table and a numeric column. In Insertmode, a template-selector is shown,
 * in update Mode the cluster can be edited.
 */
class ClusterInput extends WUIInterface {
	
	var $object = null;
	
	/**
	 * Standard constructor
	 *
	 * @param string $labelInsert Label which is displayed on Insert
	 * @param string $labelUpdate Label which is displayed on Update
	 * @param string $table  Name of the table
	 * @param string $column  Name of the column
	 * @param string $row_identifier  Row-identifier of the cell.
	 * @param string $clt_identifier  Filter for the cluster-templates
	 * @param integer $variation  Langauge-ID
	 * @param string $nameColumn  Name column 
	 */
	function ClusterInput($labelInsert, $table, $column, $row_identifier="1", $clt_identifier, $variation, $nameColumn) {
		global $page_action;
		if ($page_action == "INSERT") {
			$this->object = new SelectCLTForCLN($labelInsert, $table, $column, $row_identifier, $clt_identifier, $nameColumn);
		} else if ($page_action == "UPDATE") {			
			$this->object = new ClusterEditor($table, $column, $row_identifier, 0, $variation, '');
		}
	}	

	/**
	 * Initialize
	 *
	 */
	function initialize() {
		 if ($this->object != null) {		 	
		 	$this->object->parentForm = $this->parentForm;
		 	return $this->object->initialize(); 		
		 }
	}
	
	/**
	 * Check	 
	 */
	function check() { 
       if ($this->object != null) return $this->object->check(); 
	}
	
	/**
	 * Process	 
	 */
	function process() {
	  if ($this->object != null) return $this->object->process(); 	
	}
	
	/**
	 * Draw
	 */
	function draw() {		
	  if ($this->object != null) return $this->object->draw(); 
	}
	
}


?>