<?php
/**********************************************************************
	 *	N/X - Web Content Management System
	 *	Copyright 2002-2005 Sven Weih, FZI Research Center for Information Technologies
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
	* Draws a to the database-connected positioninput field.
	* @package DatabaseConnectedInput
	*/
	class CoordinatesInput extends DBO {
	
	var $api;
	var $lng;
	var $lat;
	var $vlng;
	var $vlat;
	
	
		
	/**
		  * standard constructor
		  * @param string Text that is to be shown as description or label with your object.
		  * @param string Table, you want to connect with the object.
		  * @param string $longitude,column, which stores the longitude
		  * @param string $latitude, columnd, which stores the latitude
		  * @param string $row_identifier Usually to be generated with form->setPK. Identifies the
		  * row in db, you want to affect with operation. Keep empty for insert. (Example: stdEDForm)
		  */
		function CoordinatesInput($label, $table, $longitude, $latitude, $row_identifier = "1") {			
			global $page, $page_state, $forceLoadFromDB, $page_action;
			DBO::DBO($label, $table, $longitude, $row_identifier, "");									
			$this->lng = $longitude;
			$this->lat = $latitude;
		
		  $this->vlng = 0;
		  $this->vlat = 0;
		  	
			if ($page_state == "processing" && (value("changevariation") != "GO") && ! ($forceLoadFromDB=="yes")) {
				$this->vlng = value("coordY", "NUMERIC", "0");
		    $this->vlat = value("coordX", "NUMERIC", "0");
		  
			} else {
				if (($page_action == "UPDATE" || $page_action == "DELETE") && $this->row_identifier != "1") {
					$this->vlng = getDBCell($table, $longitude, $row_identifier);
					$this->vlat = getDBCell($table, $latitude, $row_identifier);
				}
			}
			
			include_once "nxgooglemapsapi.php";
			$this->api = new NXGoogleMapsAPI();
			$this->api->setWidth(590);
			$this->api->setHeight(350);
			$this->api->addControl(GLargeMapControl);
			$this->api->addControl(GOverviewMapControl);
			$this->api->addControl(GMapTypeControl);
			$this->api->setCenter(50,10);
			$this->api->setZoomFactor(4);
			if (($this->vlng != 0) || ($this->vlat) != 0) {
				$this->api->addDragMarker($this->vlng, $this->vlat);
			}
			$page->headerPayload = $this->api->getHeadCode();
			$page->onLoad.= $this->api->getOnLoadCode();
						
		}
		
		/**
		 * Checks, wheter a page is actually in INSERT or UPDATE mode an creates the corresponding
		 * Saveset.
		 */
		function process() {
		  global $page_action;		  
		  
		  if ($page_action == "INSERT") {
				addInsert($this->table, $this->lng, $this->vlng, "NUMBER");
				addInsert($this->table, $this->lat, $this->vlat, "NUMBER");
			} else if ($page_action == "UPDATE") {
				addUpdate($this->table, $this->lng, $this->vlng, $this->row_identifier, "NUMBER");
				addUpdate($this->table, $this->lat, $this->vlat,  $this->row_identifier, "NUMBER");
			}					
		}
		
		/** 
		 * Draw the input map
		 */
		function draw() {
		  global $lang;
		  echo '<td colspan="2" class="standardlight">';
		  echo '<table width="100%" cellpadding="2" cellspacing="0" border="0">';
		  echo '<tr>';
		  echo '<td colspan="2">'.$this->label.'</td>';
		  echo '</tr>';
		  echo '<tr>';
		  echo '<td colspan="2">';
		  echo $this->api->getBodyCode();
		  echo '</td>';
		  echo '</tr>';
		  echo '<tr>';
		  echo '<td colspan="2">';
		  echo '<input type="text" name="address7" id="address7" size="100" style="width:430px;">&nbsp;';
      echo '<input type="button" value="'.$lang->get("findaddr", "Find Address").'" style="width: 150px;" onClick="moveToAddressDMarker(document.getElementById(\'address7\').value);">';
      echo '</td>';
		  echo '</tr>';
		  echo '<tr>';
		  echo '<td width="50%">';
      echo 'Latitude: <input type="text" width="20" id="coordX" name="coordX" value="'.$this->vlat.'">';
		  echo '</td><td width="50%">';
		  echo 'Longitude: <input type="text" width="20" id="coordY" name="coordY" value="'.$this->vlng.'">';
		  echo '</td>';
		  echo '</tr>';		  
		  echo '</table>';
		  echo '</td>';
		  return "2";	
		}
		
		/**
		 * Do not checks on the coordinates
		 */
		function check() {	}
		
	}
?>