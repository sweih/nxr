<?php
	/**********************************************************************
	 *	N/X - Web Content Management System
	 *	Copyright 2003 Sven Weih, 
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
	 * This class displays a menu with several columns and rows
	 */
	class EmbeddedMenu extends WUIInterface {
	
		var $colTitles;
		var $rows;
		var $linkTo;
		var $colspan=2;
		
		
		/**
		 * Standard constructor
		 * @param array Titles of the columns
		 * @param array Data for the rows. The first column must be the id and is not displayed. All other columns must match the titles-array
		 * @param string Link to a page. The LInk must end with ....&id= The Menu will add the id there
		 * @param integer Colspan to use.
		 */
		function EmbeddedMenu($colTitles, $rows, $linkTo, $colspan=2) {
			$this->colTitles = $colTitles;
			$this->rows = $rows;
			$this->linkTo = $linkTo;		
			$this->colspan=$colspan;
		}
		
		/**
		 * Draw the menu...
		 */
		 function draw() {
		 	echo "<td colspan=\"".$this->colspan."\">\n";
		 	echo tableStart("100%", "cwhite");
		 	$this->drawColumnHeaders();
		 	$this->drawRows();
		 	echo tableEnd();
		 	echo "</td>\n";
		 	return $this->colspan;
		 }
		 
		 
	      /**
	       * Draw the Headlines of the column
       	       */
      		function drawColumnHeaders() {
         		for ($i=0; $i < count($this->colTitles); $i++) {
           			echo "<td class=\"gridtitle\">".$this->colTitles[$i]."</td>";
         		}
         		echo "</tr>\n";
      		}
      		
      	/**
	* draw the columns
       	*/
      	function drawRows() {
        	global $c, $sid;        	
        	$data = $this->rows;
        	for ($row=0; $row < count($data); $row++) {
          		echo "<tr class=\"grid\" onMouseOver='this.style.backgroundColor=\"#ffffcc\";' onMouseOut='this.style.backgroundColor=\"#e8eef7\";' onClick='document.location.href=\"".$this->linkTo.$data[$row][0]."\";'>";
          		for ($i=1; $i< count($data[$row]); $i++) {
            			$style=' style="border-bottom:1px solid #cccccc;" ';
            			echo "<td $style>".$data[$row][$i]."</td>";

          		}
          		echo "</tr>";
        	}
      }

		 
	
}

?>