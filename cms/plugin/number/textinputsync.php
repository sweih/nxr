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

	require_once $c["path"]."api/userinterface/dbinput/dbo.php";
	require_once $c["path"]."api/userinterface/dbinput/text_input.php";
	
	 /**
	* Draws a to the database-connected textinput field for cluster-edititing.
	* Whenever a user changes a value, the changes are performed on all variations!
	* @package DatabaseConnectedInput
	*/
	class TextInputSyncPGNNumber extends TextInput {

				/**
		  * standard constructor
		  * @param string Text that is to be shown as description or label with your object.
		  * @param string Table, you want to connect with the object.
		  * @param string column, you want to connect with the object.
		  * @param string $row_identifier Usually to be generated with form->setPK. Identifies the
		  * row in db, you want to affect with operation. Keep empty for insert. (Example: stdEDForm)
		  * @param string $params Allowed parameters are:
		  * type:TEXT|TEXTAREA|COLOR|RICH;
		  * size:XX Size of Input in chars.
		  * width:XX Size of Input in pixel.
		  * @param string $check Does checks on user input. Allowed are MANDATORY (=not null)|UNIQUE. Separate with &.
		  * @param string $db_datatype Datatype of the database, you want to use. Allowed are CHAR|NUMBER|PASSWORD
		  */
		function TextInputSyncPGNNumber($label, $table, $column, $row_identifier = "1", $params = "type:text,size:10,width:100", $check = "", $db_datatype = "TEXT") {
		  global $c;
		  TextInput::TextInput($label, $table, $column, $row_identifier, $params, $check, $db_datatype);
		  if ($this->value == "0") {
		  	$temp = explode("=", $this->row_identifier);
			$pk = trim($temp[0]);
			$clcid = trim($temp[1]);
			$clti = getDBCell("cluster_content", "CLTI_ID", "CLCID=".$clcid);
			$posi = getDBCell("cluster_content", "POSITION", "CLCID=".$clcid);
			$clid = getDBCell("cluster_content", "CLID", "CLCID=".$clcid);
			$clnid = getDBCell("cluster_variations", "CLNID", "CLID = $clid");
			$clid = getDBCell("cluster_variations", "CLID", "CLNID=$clnid AND VARIATION_ID = ".$c["stdvariation"]);	
			$fkid = getDBCell("cluster_content", "CLCID", "CLID = $clid AND CLTI_ID = $clti AND POSITION = $posi");
			$this->value = getDBCell($this->table, $this->column, $pk."=".$fkid);	
			$this->v_wuiobject->value = $this->value;		
		  }
		}
		
		/**
		 * Save the value on all variations
		 * support update only, no create!
		 */
		function process() {
			global $page_action;			
			if ($page_action == "UPDATE") {
				$temp = explode("=", $this->row_identifier);
				$pk = trim($temp[0]);
				$clcid = trim($temp[1]);
				$clti = getDBCell("cluster_content", "CLTI_ID", "CLCID=".$clcid);
				$posi = getDBCell("cluster_content", "POSITION", "CLCID=".$clcid);
				$clid = getDBCell("cluster_content", "CLID", "CLCID=".$clcid);
				$clnid = getDBCell("cluster_variations", "CLNID", "CLID = $clid");
				
				$clids = implode(",", createDBCArray("cluster_variations", "CLID", "CLNID = $clnid AND DELETED=0"));
				$clcids = createDBCArray("cluster_content", "CLCID", "CLID IN ($clids) AND CLTI_ID = $clti AND POSITION=$posi");

				for ($i=0; $i < count($clcids); $i++) {
				  addUpdate($this->table, $this->column, $this->value, $pk."=".$clcids[$i], $this->datatype);
				}
			}	
		}
	}
?>