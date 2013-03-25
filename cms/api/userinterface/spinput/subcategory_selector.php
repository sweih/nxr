<?php

	/**********************************************************************
	 *	N/X - Web Content Management System
	 *	Copyright 2002-2006 Sven Weih
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
 * Draw a selector which shows all subcategories for a category
 */
class SubCategorySelector extends Container{

  var $parentCat;
  var $headline;
  var $editAction;
  
  /**
   * Standard constructor
   * @param integer $parentCat  Parent - Category ID
   * @param string  $headline   Headline of the SubCategory Box
   */
  function SubCategorySelector($parentCat, $headline, $editAction) {
    $this->parentCat = $parentCat;
    $this->headline  = $headline;  	
    $this->editAction = $editAction;
    $this->columns   = 2;
    
    $this->add(new SubTitle("st", $headline));
  }
  
  
  /**
   * Draw the Category Overview
   */
  function draw() {
  	$categories = createNameValueArrayEx("categories", "CATEGORY_NAME", "CATEGORY_ID", "PARENT_CATEGORY_ID=".$this->parentCat, "order by CATEGORY_NAME");  	
  	if (count($categories) > 0 ) {
  	  Container::draw();
  	  echo '<tr><td colspan="'.$this->columns.'">';
 	    echo '<table width="100%" cellpadding="0" cellspacing="0" border="0">';
 	    $style=' style="border-bottom:1px solid #cccccc;cursor:pointer;padding-top:2px; padding-bottom:2px;" ';            
 	    for ($i=0; $i<count($categories); $i++) {
 	      echo "<tr class=\"grid\" onMouseOver='this.style.backgroundColor=\"#ffffcc\";' onMouseOut='this.style.backgroundColor=\"#e8eef7\";' onClick='document.location.href=\"".$this->editAction.$categories[$i][1]."\";'>";
        echo "<td $style>&nbsp;".$categories[$i][0].'</td>'; 
        echo "</tr>";	
 	    }
  	
  	  echo '</table>';
  	  echo '</td></tr>';
  	}
  	return $this->columns;
  }


}


?>