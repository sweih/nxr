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
	  * and remove the values from the other list again. The right list can be sorted.
	  */
	 class SelectMultipleWPos extends SelectMultiple {
	 
	 
	 		/**
		  * Draws the layout-element
		  */
		function draw() {
			$output = WUIObject::std_header();
			$output.= tableStart();
			$output.= $this->drawValues();
			$output.= $this->drawLeftRightButtons();
			$output.= $this->drawSelectedValues();
			$output.= $this->drawUpDownButtons();	
			$output.= tableEnd();
			$output .= WUIObject::std_footer();
			echo $output;
			return $this->columns;
		} 
		
		/**
		 * Draw Buttons for moving selected items up and down
		 */
		function drawUpDownButtons() {
			$out = "<td class=\"".$this->style."\" valign=\"middle\">\n";	 	 	
			$out.= '&nbsp;<a href="#" class="navelement" onClick="moveOptionUp(document.' . $this->formname . '.'.$this->name.'sel);  saveSelectValues(document.'.$this->formname.'.'.$this->name.'sel, document.'.$this->formname.'.'.$this->name.'selection);">' . drawImage("up.gif"). '</a><br>';
			$out.= '&nbsp;<a href="#" class="navelement" onClick="moveOptionDown(document.' . $this->formname . '.'.$this->name.'sel); saveSelectValues(document.'.$this->formname.'.'.$this->name.'sel, document.'.$this->formname.'.'.$this->name.'selection);">' . drawImage("down.gif"). '</a>';		 
	 	 	$out.= "    </td>\n";
		  	return $out;	
		}
	   
	 }
?>