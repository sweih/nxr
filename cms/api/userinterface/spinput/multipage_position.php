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
	* @package DatabaseConnectedInput
	*/

	/**
	 * draws a to the database-connected dropdown or select field, which you can select only one value.
	 * @package DatabaseConnectedInput
	 */
	class MultipagePosition extends DBO {

		var $parentId;
		var $spid;
		
		/**
		* standard constructor
		* @param string Text that is to be shown as description or label with your object.
		* @param string Table, you want to connect with the object.
		* @param string column, you want to connect with the object.
		* @param string $row_identifier Usually to be generated with form->setPK. Identifies the
		* @param string PARENT_ID where all the menu-entries are located.
		* @param string allowed is type:DROPDOWN|SELECT as paramter for choosing a dropdown or selectbox. For select
		* you may also use size:XX for number of rows.
		* @param string $check Does checks on user input. Allowed are MANDATORY (=not null)|UNIQUE. Unique will not make sense in most cases!
		* @param string $datatype Datatype of the database, you want to use. Allowed are CHAR|NUMBER|DATE|DATETIME|TIMESTAMP|PASSWORD
		*/
		function MultipagePosition($label, $table, $column, $row_identifier, $spid, $parentId, $param = "type:dropdown", $check = "MANDATORY", $datatype = "NUMBER") {
			$chktmp = $check;
			$this->parentId = $parentId;
			$this->spid= $spid;
			
			DBO::DBO($label, $table, $column, $row_identifier, $param, $datatype, $chktmp);

		
			switch ($this->type):
				case "DROPDOWN":
					$this->v_wuiobject = new Dropdown($this->name, $values, $this->std_style, $this->value, $this->width);
					break;

				case "SELECT":
					$this->v_wuiobject = new Select($this->name, $values, "", $this->value, $this->size, $this->std_style, $this->width);
					break;
			endswitch;
		}
		
		function process() {
			global $errors;
			if ($errors == "") {
			  reorderSitepage($this->parentId, $this->spid, $this->value);	
			}	
		}
		
		function draw() {
			global $lang, $variation, $page_action;
			if ($this->value == "") $this->value = 999;
			$values = createNameValueArrayEx("sitepage", "SPID", "POSITION", "MENU_ID = ".$this->parentId, " AND DELETED=0 ORDER BY POSITION");					    
			for ($i=0; $i < count($values); $i++) {
				$menu = getMenu($values[$i][0], $variation);
				$values[$i][0] = $lang->get("after", "After:")." ".$menu[0];
				if ($this->value > $values[$i][1] || $page_action == "INSERT") {
					$values[$i][1]++;
				} else 	if ($this->value == $values[$i][1] && $page_action != "INSERT") {
					$values[$i][0] = $lang->get("remain_pos", "Do not change position"); 
				}
			}
			
			if (is_array($values)) {
				array_unshift($values, array($lang->get("begin", "At the beginnging"), "1"));
			} else {
			  	$values = array(array($lang->get("begin", "At the beginnging"), "1"));				
			}
			$this->v_wuiobject->value = $values;
			return DBO::draw();
		}
	}
?>