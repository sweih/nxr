<?

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
	 
	 /**
	  * Paints two lists. You can shuffle values from one List to another 
	  * and remove the values from the other list again.
	  */
	 class SelectMultiple extends WUIObject {
		
		var $rows;
		var $params;
		var $selectedValues;
		var $iwidth;
		var $formname;
		var $headleft="";
		var $headright="";
		
		/**
		  * standard constructor
		  * @param string the name of the Input, used in the html name property
		  * @param string 2D-array with values and corresponding names. to be Created with db-funtions.
		  * @param string 2D-array with values and corresponding names. to be Created with db-funtions.
		  * @param string headline for left hand box
		  * @param string headline for right hand box
		  * @param string sets the styles, which will be used for drawing
		  * @param integer Height of the selectbox in lines
		  * @param string use to format the layout-item
		  * @param integer width in pixel
		  * @param integer Cellspan of the element in Table.
		  * @param string Name of the parent form.
		  */
		function SelectMultiple($name, $values, $selectedValues, $headleft, $headright, $style, $rows = 5, $params = "", $cells = 2, $formname="form1") {
			WUIObject::WUIObject($name, "", $values, $style, $cells);

			$this->selectedValues = $selectedValues;
			$this->rows = $rows;
			$this->params = $params;
			$this->iwidth = 220; 
			$this->formname = $formname;
			$this->headleft = $headleft;
			$this->headright = $headright;
		}

		/**
		  * Draws the layout-element
		  */
		function draw() {
			$output = WUIObject::std_header();
			$output.= tableStart();
			$output.= $this->drawValues();
			$output.= $this->drawLeftRightButtons();
			$output.= $this->drawSelectedValues();
			
			$output.= tableEnd();
			$output .= WUIObject::std_footer();
			echo $output;
			return $this->columns;
		}	 
	 
	 	/**
	 	 * Draws the left and right selector
	 	 */
	 	 function drawLeftRightButtons() {
	 	 	 $out = "    <td class=\"".$this->style."\" valign=\"middle\" align=\"center\">\n";
	 	 	 $out.= '<a href="#" class="navelement" onClick="moveSelectedOptions(document.' . $this->formname . '.'.$this->name.', document.' . $this->formname . '.'.$this->name.'sel);  saveSelectValues(document.'.$this->formname.'.'.$this->name.'sel, document.'.$this->formname.'.'.$this->name.'selection);">' . drawImage("right.gif"). '</a><br>';
			 $out.= '<a href="#" class="navelement" onClick="moveSelectedOptions(document.' . $this->formname . '.'.$this->name.'sel, document.' . $this->formname . '.'.$this->name.'); saveSelectValues(document.'.$this->formname.'.'.$this->name.'sel, document.'.$this->formname.'.'.$this->name.'selection);">' . drawImage("left.gif"). '</a>';
			 
	 	 	 $out.= "    </td>\n";
	 	 	 return $out;
	 	 }
	 	
	 	
	 	
	 	/**
	 	 * Draw the selectlist with all values in it.
	 	 */
	 	function drawValues() {
	 	  global $lang;
	 	  $out = "    <td class=\"".$this->style."\" valign=\"top\" width=\"".$this->iwidth."\">\n";
	 	  $out.= $this->headleft."<br>";
	 	  $out.= "      <select name=\"$this->name\" size=\"$this->rows\"".' onDblClick="moveSelectedOptions(document.' . $this->formname . '.'.$this->name.', document.' . $this->formname . '.'.$this->name.'sel);  saveSelectValues(document.'.$this->formname.'.'.$this->name.'sel, document.'.$this->formname.'.'.$this->name.'selection);"'." style=\"width:$this->iwidth px;\" $this->params multiple>\n";
		  foreach ($this->value as $value) {	 	  
		  	if ($value[1] != 0) $out.= "       <option value=\"" . $value[1] . "\">" . $value[0] . "</option>\n";
		  }
 		  $out.= "      </select><br><br>"; 	  	 	  
	 	  $out.=  buttonLink($lang->get("comb_all", "Select All"), 'javascript:moveAllOptions(document.' . $this->formname . '.'.$this->name.', document.' . $this->formname . '.'.$this->name.'sel); saveSelectValues(document.'.$this->formname.'.'.$this->name.'sel, document.'.$this->formname.'.'.$this->name.'selection);');
	 	  $out.= "    <br/><br/></td>\n";
	 	  return $out;
	 	}
	 
	 	/**
	 	 * Draw the selectlist with already selected values in it.
	 	 */
	 	function drawSelectedValues() {
	 	  global $lang;
	 	  $out = "    <td class=\"".$this->style."\" valign=\"top\" width=\"".$this->iwidth."\">\n";
	 	  $out.= $this->headright."<br>";
	 	  $out.= "      <select name=\"".$this->name."sel\" size=\"$this->rows\"".' onDblClick="moveSelectedOptions(document.' . $this->formname . '.'.$this->name.'sel, document.' . $this->formname . '.'.$this->name.'); saveSelectValues(document.'.$this->formname.'.'.$this->name.'sel, document.'.$this->formname.'.'.$this->name.'selection);"'." style=\"width:$this->iwidth px;\" $this->params multiple>\n";
		  $vals = array();
		  for ($i = 0; $i < count($this->selectedValues); $i++) {
			$out.= "       <option value=\"" . $this->selectedValues[$i][1] . "\">" . $this->selectedValues[$i][0] . "</option>\n";
			$vals[] = $this->selectedValues[$i][1];
		  }
 		  $out.= "      </select><br><br>\n"; 	  
	 	  $out.= buttonLink($lang->get("comb_none", "Clear All"), 'javascript:moveAllOptions(document.' . $this->formname . '.'.$this->name.'sel, document.' . $this->formname . '.'.$this->name.'); saveSelectValues(document.'.$this->formname.'.'.$this->name.'sel, document.'.$this->formname.'.'.$this->name.'selection);');
	 				
	 	  $out.= "    <br/><br/></td>\n";
	 	  $out.= ' <input type="hidden" name="'.$this->name.'selection" value="'.implode(',', $vals).'">';
	 	  
	 	  return $out;
	 	}	 	
	 }
	 
?>