<?

	/**********************************************************************
	 *	N/X - Web Content Management System
	 *	Copyright 2003 Sven Weih
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
	 * Draw two selectboxes (left->right) for selecting. The right one is directly connected to db.
	 * Has Position sorting 
	 */
	class SelectMultipleInputPos extends WUIInterface {
	
	  var $headline;
	  var $selectValues;
	  var $selectedValues;
	  var $table;
	  var $idcolumn;
	  var $fkidcolumn;
	  var $cond;
	  var $lookupTable;
	  var $lookupID;
	  var $lookupName;
	  var $style;
	  var $columns;
	  
	  /**
	   * @param string Headline of the widget
	   * @param mixed Name-Value-Array
	   * @param string tablename where to save
	   * @param string Colum of PK where to save
	   * @param string FKID where to save
	   * @param string Where-Clause for saving
	   * @param string Table, where to lookup name for the PKID
	   * @param string PKIDColumn of the Lookup-Table
	   * @param string NameColumn of the Lookup-Table
	   * @param string css style
	   * @param integer Colspan
	   */
	  function SelectMultipleInputPos($label, $selectValues, $table, $idcolumn, $fkidcolumn, $cond, $lookupTable, $lookupID, $lookupName, $style="standardlight", $columns=2) {
	    $this->headline = $label;
	    $this->selectValues = $selectValues;
	    $this->table = $table;
	    $this->idcolumn = $idcolumn;
	    $this->fkidcolumn = $fkidcolumn;
	    $this->cond = $cond;
	    $this->lookupTable = $lookupTable;
	    $this->lookupID = $lookupID;
	    $this->lookupName = $lookupName;
	    $this->style = $style;
	    $this->columns = $columns;
	  }
	
	  /**
	   * Draw the widget
	   */
	  function draw() {
	    echo '<td colspan="'.$this->columns.'" class="'.$this->style.'">';
	    echo tableStart();
	    echo '<td colspan="'.$this->columns.'" class="'.$this->style.'" ><b>'.$this->headline.'</b></td></tr><tr>';
	    $this->drawSelector();
	    echo tableEnd();
	    echo '</td>';
	  }	
	  
	  /**
	   * Draws the Selector widget
	   */
	  function drawSelector() {
	  	global $lang;
	  	$this->querySelectedValues();
	  	$selector = new SelectMultipleWPos($this->table, $this->selectValues, $this->selectedValues, $lang->get("avail_items","Available items"), $lang->get("sel_mem","Selected members"), $this->style, 15);
	  	$selector->draw();
	  }
	  
	  /**
	   * Save all back to the database
	   */
	   function process() {
	       global $db, $oid;
	       deleteRow($this->table, $this->cond);	
	       $ids = explode(",", value($this->table."selection", "NOSPACES"));
	       for ($i=0; $i < count($ids); $i++) {
	         $sql = "INSERT INTO $this->table ($this->idcolumn, $this->fkidcolumn, POSITION) VALUES (".$ids[$i].", $oid, ".($i+1).")";
	         $query = new query($db, $sql);
	         $query->free();
	       }
	   }
	  
	  /**
	  * Query for the selected values
	  */
	  function querySelectedValues() {
	  	$this->selectedValues = array();		
		if ($this->cond != "1") {
	  		
			$ids = createDBCArray($this->table, $this->idcolumn, $this->cond, "ORDER BY POSITION");	
				for ($i=0; $i < count($ids); $i++) {		        	
	        		$sub[0] = getDBCell($this->lookupTable, $this->lookupName, $this->lookupID." = ".$ids[$i]);
	        		$sub[1] = $ids[$i];
            			$tmp = array();
            			foreach ($this->selectValues as $key) {
            			  if ($sub[1] != $key[1])
            			    $tmp[] = $key;           			  	
            			}
            			$this->selectValues = $tmp;
            			$this->selectedValues[] = $sub;
	      		}
		}  
	  }
		
	}

?>