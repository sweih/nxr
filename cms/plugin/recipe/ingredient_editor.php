<?php

class IngredientEditor extends WUIInterface {
	
  var $table;
  var $cond;
  var $values;
  
  
  function IngredientEditor($table, $cond)	{
    $this->values = array();
    $this->cond = $cond;
    $this->table = $table;	   
    global $page_state, $page_action, $db;
    if ($page_state == "processing") {  	
		// Get Values form PoST
    	for($i=1; $i<18; $i++) {
    		$this->values[$i][0] = value("quantity".$i, '', '');
    		$this->values[$i][1] = value("ingredient".$i, '', '');
    	}    	 
    } else {
    	if (($page_action == "UPDATE" || $page_action == "DELETE") && $this->cond != "1") {
    		// Load Values from database		    		
    		for($i=1; $i<18; $i++) {
  
    		  $this->values[$i][0] = getDBCell($this->table, "QUANTITY".$i, $this->cond);
    		  $this->values[$i][1] = getDBCell($this->table, "INGREDIENT".$i, $this->cond);    		 
    		}    		
    	}
    }    
  }
	
	function draw() {

		echo '<td colspan="2">';
		echo '<table width="100%" cellpadding="2" cellspacing="0" border="0" class="standardlight">';
		echo '<tr><td  width="200"><b>Quantity</b></td><td  width="400"><b>Ingredient</b></td></tr>';
		for ($i=1; $i<18; $i++) {
			echo '<tr><td><input type="text" size="128" style="width:190px;" value="'.$this->values[$i][0].'" name="quantity'.$i.'"/></td>';
			echo '<td><input type="text" size="256" style="width:390px;" value="'.$this->values[$i][1].'" name="ingredient'.$i.'"/></td></tr>';
		}
		echo '</table>';
		echo '</td>';
		return 2;
	}
	
	
	function process() {
		global $page_action;			
		if ($page_action == "INSERT") {
			for ($i=1; $i<18;$i++) {
			  addInsert($this->table, "quantity".$i, $this->values[$i][0], "TEXT");
			  addInsert($this->table, "ingredient".$i, $this->values[$i][1], "TEXT");
			}
		}
		if ($page_action == "UPDATE") {			
			for ($i=1; $i<18;$i++) {
			  addUpdate($this->table, "quantity".$i, $this->values[$i][0], $this->cond, "TEXT");
			  addUpdate($this->table, "ingredient".$i, $this->values[$i][1], $this->cond, "TEXT");
			}			
		}
	}
}

?>