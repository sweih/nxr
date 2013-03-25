<?

	/**********************************************************************
	 *	N/X - Web Content Management System
	 *	Copyright 2001-2007 Sven Weih
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
	
class TagEditor extends WUIInterface  {
	
	var $title;
	var $store_table;
	var $pkKey;
	var $pkValue;
	var $fkKey;
	var $values;
	var $selectedValues;
	
	function TagEditor($title, $store_table, $pkKey, $pkValue, $fkKey, $values) {
	  $this->title = $title;
	  $this->store_table = $store_table;
	  $this->pkKey = $pkKey;
	  $this->pkValue = $pkValue;
	  $this->fkKey = $fkKey;
	  $this->values = $values;  			
	  
	global $page_state, $page_action, $db;
    if ($page_state == "processing") {  	
		// Get Values form PoST     	
		$this->selectedValues = explode(",", value($this->store_table.$this->pkKey.'selection', "NOSPACES"));     	     	
    } else {
    	if (($page_action == "UPDATE" || $page_action == "DELETE") && $this->cond != "1") {
    		// Load Values from database		    		
    		$this->selectedValues = createDBCArray($this->store_table, $this->fkKey, $this->pkKey.'='.$this->pkValue);   		 
    	  }    		
   	}
   	
   	// selected values jetzt fett machen....
   	if (is_array($this->selectedValues)) {
   	  $this->selectedValues = createNameValueArrayEx("pgn_recipes_tags", "TAG", "TAG_ID", "TAG_ID IN (".implode(",", $this->selectedValues).")", "ORDER BY TAG ASC");
   	} else {
   	  $this->selectedValues = array();	
   	}
   	
   	// extract selected Values from given values
   	$newValues = array();
   	for ($i=0; $i<count($this->values); $i++) {
   		$found = false;   		
   		for ($j=0; $j<count($this->selectedValues); $j++) {   			
   			if ($this->values[$i][1] == $this->selectedValues[$j][1]) 
   			  $found = true;
   		}   		
   		if (!$found)  $newValues[] = $this->values[$i];
   	}
   	$this->values = $newValues;
   	        
   }
	
	function draw() {
		$widget = new SelectMultiple($this->store_table.$this->pkKey, $this->values, $this->selectedValues, "Available", "Selected", "standardlight", 5);
		return $widget->draw();		
	}
	
	function process() {
		global $oid, $db;
		if (isset ($oid)) {
			$sql = "Delete From $this->store_table Where $this->pkKey = $this->pkValue";
			$query = new query($db, $sql);
			for ($i=0; $i<count($this->selectedValues); $i++) {
			  $sql = "INSERT INTO $this->store_table ($this->pkKey, $this->fkKey) VALUES ($oid, ".$this->selectedValues[$i][1].")";
			  $query = new query($db, $sql);
			}
			
		}
		
	}
	
	
	
	
}
	
	
?>