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
	* Draws a to the database-connected textinput field.
	* @package DatabaseConnectedInput
	*/
	class RicheditInput extends DBO {
		
		var $italic;
		var $underline;

		/**
		  * standard constructor
		  * @param string Text that is to be shown as description or label with your object. (will not be displayed !!)
		  * @param string Table, you want to connect with the object.
		  * @param string column, you want to connect with the object.
		  * @param string $row_identifier Usually to be generated with form->setPK. Identifies the
		  * row in db, you want to affect with operation. Keep empty for insert. (Example: stdEDForm)
		  * @param string $params Allowed parameters are:
		  * type:RICH;
		  * size:XX Size of Input in chars.
		  * width:XX Size of Input in pixel.
		  * @param string $check Does checks on user input. Allowed are MANDATORY (=not null)|UNIQUE. Separate with &.
		  * @param string $db_datatype Datatype of the database, you want to use. Allowed are CHAR|NUMBER|PASSWORD
		  */
		function RicheditInput($label, $table, $column, $row_identifier = "1", $params = "type:rich,size:10,width:100", $check = "", $db_datatype = "TEXT") {
			DBO::DBO($label, $table, $column, $row_identifier, $params, $db_datatype, $check);

			global $page_state, $page_action, $forceLoadFromDB;

			if ($page_state == "processing" && value("changevariation") != "GO" && value("changetranslation") != "GO" && ! ($forceLoadFromDB=="yes")) {
                //$fieldname = $this->table."_".$this->column;
				$fieldname = $this->name;

				if (value($fieldname) != "0") {
					$this->value = value($fieldname);
                } else {
                    $this->value = $_POST[$fieldname];
                }
				/** added 21.1.2002 */
				global $c_magic_quotes_gpc;

				if ($c_magic_quotes_gpc == 1)
					$this->value = str_replace("\\", "", $this->value);
				$this->value = str_replace('&amp;', '&', $this->value);
				/** got rid of magic quotes! */
				$this->oldvalue = getDBCell($table, $column, $row_identifier);
			} else {
				if (($page_action == "UPDATE" || $page_action == "DELETE") && $this->row_identifier != "1") {
                    $parser = new NX2HTML(variation());
					$this->value = $parser->parseText(getDBCell($table, $column, $row_identifier));

				}
			}
		
			switch ($this->type):
				case "RICH":
					$this->v_wuiobject = new Richedit($this->name, $this->value, $this->std_style, $this->size, $this->parameter, $this->width);
					break;
			endswitch;
		}
		
		/**
		  * parses a paramterstring for parameters. Scans for
		  * parameters TYPE, SIZE, WIDTH and PARAM.
		  * @param string parameter-string to parse.
		  */
		function parseParam($param) {
			DBO::parseParam($param);
			$params = explode(",", $param);

			for ($i = 0; $i < count($params); $i++) {
				$vals = explode(":", $params[$i]);

				switch (strtoupper($vals[0])):
					case "ITALIC":
						$this->italic = strtoupper($vals[1]);

						break;

					case "UNDERLINE":
						$this->underline = strtoupper($vals[1]);

						break;

				endswitch;
			}
		}
		
		/**
		 * Checks, wheter a page is actually in INSERT or UPDATE mode an creates the corresponding
		 * Saveset.
		 */
		function process() {
			global $page_action, $c;
			$parser = new HTML2NX();
			$this->cleanTags();
			$value = $parser->parseText($this->value);
			
			if ($page_action == "INSERT")
				addInsert($this->table, $this->column, $value, $this->datatype);

			if ($page_action == "UPDATE") {
				if (! isset($c[$this->name])) {
 			  		addUpdate($this->table, $this->column, $value, $this->row_identifier, $this->datatype);			
				} else {
			  		$this->v_wuiobject->value = $c[$this->name];
				}
			}			
		}			
		
		/**
		 * clean $this->value from certain tags specified in ClusterTemplate.
		 */
		function cleanTags() {
			if ($this->italic == "NO") {
				$this->value = str_replace("<I>", "", $this->value);
				$this->value = str_replace("</I>", "", $this->value);
			}
			
			if ($this->underline == "NO") {
				$this->value = str_replace("<U>", "", $this->value);
				$this->value = str_replace("</U>", "", $this->value);
			}
				
		}

		/**
		  * Writes the HTML for the Label and the input-element to the page.
		  * @return integer number of Columns, that were used by draw operation.
		  * Note: NX works with tables and the forms do automatic row-processing.
		  * So they need to know, how many columns the last draw-operation needed!
		  */
		function draw() {
			$cols = 1;
			
			$cols += $this->v_wuiobject->draw();
			return $cols;
		}
	}
?>