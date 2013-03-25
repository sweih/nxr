<?
	/**********************************************************************
	 *	N/X - Web Content Management System
	 *	Copyright 2002 Sven Weih & Fabian König
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
	 * Widget which allows selecting any data from the content-library 
	 * @package ContentManagement
	 */
	class LibrarySelect extends DBO{
		
		var $cells;
		var $filter;
		var $viewOnly= false;
		
		/**
		  * Standard constructor
		  * @param string Table where the FK is saved in
	 	  * @param string Column where the FK id saved in to reference the plugin.
	 	  * @param string Where-Clause to select a FK
		  * @param $filter specifies which plugins to display. Specify plugin name(s) e.g. IMAGE|TEXT
		  * @param $cells number of cells to use, standard=2
		  * @param $viewOnly Do not show buttons but view only the content
		  */
		function LibrarySelect($table, $column, $cond, $filter = "", $cells = 2, $viewOnly=false) {
			DBO::DBO("", $table, $column, $cond, "", "NUMBER");
			$this->filter = $filter;
			$this->columns = $cells;
			$this->viewOnly = $viewOnly;
			if ($viewOnly) $this->std_style="standardwhite";
			if ($this->value=="" || $this->value==0) $this->value="0";
		}

		function draw() {		  
		  global $lang, $c, $sid;
		  
		  $noObj = "<b>".htmlspecialchars("< ".$lang->get("no_obj", "No object selected.")." >")."</b>";
		  
		  echo '<td colspan="'.$this->columns.'">';
		  echo '<input type="hidden" name="'.$this->name.'" id="'.$this->name.'" value="'.$this->value.'">';
		  echo tableStart("100%", $this->std_style);
		  echo '<td id="disp_'.$this->name.'" valign="middle" align="center" width="50%">';
		  if ($this->value=="0") {
		    echo $noObj;
		  } else {
		  	$ref = createPGNRef2($this->value);
		  	if (is_object($ref)) {
		  		echo $ref->preview();
		  	} else {
		  	  echo $lang->get("was_deleted", "The referenced object was deleted from library!");	
		  	}
		  	unset($ref);	
		  }
		  echo '</td>';
		  echo '<td valign="middle" align="left" width="50%">';
		  if ($this->viewOnly) {
		  	echo "&nbsp;";
		  } else { 
		    	br();
		  	$clearButton = new LinkButton("cl_".$this->name, $lang->get("clear", "Clear"), "navelement", "button", 'document.form1.'.$this->name.'.value=\'\'; document.getElementById(\'disp_'.$this->name.'\').innerHTML=\''.$noObj.'\';');
		  	$clearButton->draw();
		  	echo drawSpacer(5,5);		  		
		  	$selectButton = new LinkButton("cl_".$this->name, $lang->get('find_obj',"Find Object"), "navelement", "button", "window.open('" . $c["docroot"] . "modules/content/objectbrowser.php?sid=$sid&filter=".$this->filter."&sname=".$this->name."&linkset=SELECT.LIB', 'library','Width=1000px,Height=700px,help=no,status=yes,scrollbars=yes,resizable=yes')");
		  	$selectButton->draw();
		  	br();br();
		  }
		  echo '</td>';
		  echo tableEnd();
		  echo '</td>';
		  
		  return $this->columns;	
		}					
	}
?>