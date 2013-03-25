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
	 * use for creating n:n assosiasations of two tables in a third table.
	 * @package DatabaseConnectedInput
	 */
	class CombinationEditor extends WUIInterface {
		var $inlist1;

		var $outlist1;
		var $properties;
		var $values1;
		var $values2;
		var $headlines;
		var $table;
		var $keys;
		var $column1;
		var $column1_dt;
		var $column2;
		var $column2_dt;
		var $formname;
		var $colspan = "3";
		var $configdata;
		var $row_identifier;
		var $css;
		/**
		  * standard constructor
		 * @param array Name-Value Array for first table
		 * @param array Name-Value Array for second table
		 * @param array String-Array with all the Headlines ("head1, head1_selected, head2")
		 * @param string Name of the table the combinations are stored in.
		 * @param array Keys to select ("name", "value", "datatype") 
		 * @param string Name of column corresponding to values1
		 * @param string Datatype of column1
		 * @param string Name of column corresponding to values2
		 * @param string Datatype of column2
		  */
		function CombinationEditor($values1, $values2, $headlines, $table, $keys, $column1, $column1_datatype, $column2, $column2_datatype, $colspan = "3", $css = "standard") {
			global $page_action, $page_state, $formname, $page;
			$this->formname = $formname;

			if ($this->formname == "")
				$this->formname = "form1";

			$page->setJS("SELECT");

			$this->values1 = $values1;
			$this->values2 = $values2;
			$this->headlines = $headlines;
			$this->table = $table;
			$this->keys = $keys;
			$this->column1 = $column1;
			$this->column2 = $column2;
			$this->column1_dt = $column1_datatype;
			$this->column2_dt = $column2_datatype;
			$this->colspan = $colspan;
			$this->css = $css;

			$whereClause = "";

			for ($i = 0; $i < count($keys); $i++) {
				if ($i > 0)
					$whereClause .= " AND ";

				$whereClause .= $keys[$i][0] . " = ";

				if (strtoupper($keys[$i][2]) == "NUMBER") {
					$whereClause .= $keys[$i][1];
				} else {
					$whereClause .= "'" . $keys[$i][1] . "'";
				}
			} // end for

			$this->row_identifier = $whereClause;

			if ($page_state == "processing") {
				$this->configdata = value("configdata");

				$checklist = array ();

				for ($i = 1; $i < count($values1); $i++) {
					$checklist[$values1[$i][1]] = false;
				}

				if ($this->configdata != "" && $this->configdata != "0") {
					$accessors = explode(";", $this->configdata);

					for ($i = 0; $i < count($accessors); $i++) {
						$entry = explode(",", $accessors[$i]);

						$checklist[$entry[0]] = true;

						for ($j = 1; $j < count($entry); $j++) {
							$this->properties[$entry[0]][$j - 1] = $entry[$j];
						}
					}
				}

				// build the inlist and outlist now.
				$this->outlist1 = array ();

				for ($i = 1; $i < count($values1); $i++) {
					if (!$checklist[$values1[$i][1]])
						array_push($this->outlist1, $values1[$i]);
				}

				$this->inlist1 = array ();

				for ($i = 1; $i < count($values1); $i++) {
					if ($checklist[$values1[$i][1]])
						array_push($this->inlist1, $values1[$i]);
				}
			} else {
				//load from databae;    
				$in1 = createDBCArray($table, $column1, $whereClause);

				$checklist = array ();

				for ($j = 1; $j < count($values1); $j++)$checklist[$j] = false;

				for ($i = 0; $i < count($in1); $i++) {
					$this->inlist1[$i][1] = $in1[$i];

					for ($j = 1; $j < count($values1); $j++) {
						if ($in1[$i] == $values1[$j][1]) {
							$this->inlist1[$i][0] = $values1[$j][0];

							$checklist[$j] = true;
						}
					}
				}

				// filter outlist with help of checklist
				$this->outlist1 = array ();

				for ($i = 1; $i < count($values1); $i++) {
					if (!$checklist[$i])
						array_push($this->outlist1, $values1[$i]);
				}

				$this->configdata = "";

				// now get properties for the inlist....
				for ($i = 0; $i < count($this->inlist1); $i++) {
					$iid = $this->inlist1[$i][1];

					if ($this->configdata != "")
						$this->configdata .= ";";

					$this->configdata .= $iid;

					if ($column1_datatype == "NUMBER") {
						$whereCL = $whereClause . " AND $column1 = $iid";
					} else {
						$whereCL = $whereClause . " AND $column1 = '$iid'";
					}

					$props = createDBCArray($table, $column2, $whereCL);

					for ($j = 0; $j < count($props); $j++) {
						$this->properties[$iid][$j] = $props[$j];

						$this->configdata .= "," . $props[$j];
					}
				}
			// end of fetching data... 
			}
		}

		/**
		 * Draws the HTML for this Input.
		 */
		function draw() {
			global $lang;

			echo "<td colspan=\"" . $this->colspan . "\" class=\"" . $this->css . "\">";
			echo '<table width="100%" cellpadding="2" cellspacing="3" border="0">'; // Draw Selectbox with non-selected values
			echo '<tr><td class="bcopy">';
			echo $this->headlines["head1"] . "<br>";
			echo '<select name="notselvals" id="notselvals" style="width:250px;" size="8">';

			// populate the list
			for ($i = 0; $i < count($this->outlist1); $i++) {
				echo '<option value="' . $this->outlist1[$i][1] . '">' . $this->outlist1[$i][0] . '</option>';
			}

			echo '</select>';
			echo '</td><td valign="middle">';
			// Draw Add button...
			echo '&nbsp;<a href="#" class="navelement" onClick="moveSelectedOptions(document.' . $this->formname . '.notselvals, document.' . $this->formname . '.selvals); return false;">' . drawImage("right.gif"). '</a><br><br>';
			echo '&nbsp;<a href="#" class="navelement" onClick="moveSelectedOptions(document.' . $this->formname . '.selvals, document.' . $this->formname . '.notselvals); inlistProps(document.' . $this->formname . '); saveData(document.' . $this->formname . '); return false;">' . drawImage("left.gif"). '</a>';
			echo '</td>';
			// Draw Selectbox with selected values
			echo '<td width="50%" class="bcopy" valign="top">';
			echo $this->headlines["head1_selected"] . "<br>";
			echo '<select name="selvals" id="selvals" style="width:250px;" size="8" onChange="inlistProps(document.' . $this->formname . ');">';

			// populate the list
			for ($i = 0; $i < count($this->inlist1); $i++) {
				echo '<option value="' . $this->inlist1[$i][1] . '">' . $this->inlist1[$i][0] . '</option>';
			}

			echo '</select>';

			echo '</td></tr>';
			// Draw Roles
			echo '<tr><td colspan="2">&nbsp;</td>';
			echo '<td class="bcopy"><div id="aclprop">';
			echo $this->headlines["head2_selected"] . "<br>";
			echo '<select name="props" id="props" style="width:250px;" size="8" multiple disabled onChange="saveProps(document.' . $this->formname . ');">';

			// populate the list
			for ($i = 1; $i < count($this->values2); $i++) {
				echo '<option value="' . $this->values2[$i][1] . '">' . $this->values2[$i][0] . '</option>';
			}

			echo '</select>';
			echo '<br>' . $lang->get("selmultiple", "Hold down the CTRL-Key to select multiple items!");
			echo "<br><br>";
			echo buttonLink($lang->get("comb_all", "Select All"), "javascript:selectAllOptions(document." . $this->formname . ".props); javascript: saveProps(document." . $this->formname . "); javascript: saveData(document." . $this->formname . ");");
			echo "&nbsp;&nbsp;";
			echo buttonLink($lang->get("comb_none", "Clear All"), "javascript:unselectAllOptions(document." . $this->formname . ".props); javascript: saveProps(document." . $this->formname . "); javascript: saveData(document." . $this->formname . ");");
			echo '</div>';
			echo '</td></tr>';
			// Draw Remove button...
			echo '<tr><td>';
			retain("selval", "");
			retain("configdata", $this->configdata);
			echo '</td><td class="bcopy">&nbsp;</td></tr>';
			echo '</table>';
			echo "</td>";
			echo $this->draw_javascript();
			return $this->colspan;
		}

		/**
		  * draw the Javascript code..
		  */
		function draw_javascript() {
			echo '<script language="JavaScript">';

			echo "var val1 = new Array();";
			echo "var prop = new Array();";
			echo "var val2 = new Array();";

			// inlist
			for ($i = 0; $i < count($this->inlist1); $i++) {
				echo "val1[$i] = " . $this->inlist1[$i][1] . ";";

				echo "prop[$i] = new Array();";

				for ($j = 1; $j < count($this->values2); $j++) {
					if (is_array($this->properties[$this->inlist1[$i][1]]) && in_array($this->values2[$j][1], $this->properties[$this->inlist1[$i][1]])) {
						echo "prop[$i][" . $this->values2[$j][1] . "] = true;";
					} else {
						echo "prop[$i][" . $this->values2[$j][1] . "] = false;";
					}
				}
			}

			// outlist
			for ($k = 0; $k < count($this->outlist1); $k++) {
				echo "val1[" . ($i + $k) . "] = " . $this->outlist1[$k][1] . ";";

				echo "prop[" . ($i + $k) . "] = new Array();";

				for ($j = 1; $j < count($this->values2); $j++) {
					echo "prop[" . ($i + $k) . "][" . $this->values2[$j][1] . "] = false;";
				}
			}

			// values2
			for ($i = 1; $i < count($this->values2); $i++) {
				echo "val2[" . ($i - 1) . "] = " . $this->values2[$i][1] . ";";
			}

			echo '</script>';
		}

		/**
		 * Does nothing here. process must be done my developer.
		 */
		function process() {
			global $page_state;

			if ($page_state == "processing") {
				$nextIndex = count($this->keys);

				deleteRow($this->table, $this->row_identifier);

				if (is_array($this->properties)) {
					foreach ($this->properties as $key => $value) {
						foreach ($value as $role) {
							$is = $this->keys;

							$is[$nextIndex][0] = $this->column2;
							$is[$nextIndex][1] = $role;
							$is[$nextIndex][2] = $this->column2_dt;

							$is[$nextIndex + 1][0] = $this->column1;
							$is[$nextIndex + 1][1] = $key;
							$is[$nextIndex + 1][2] = $this->column1_dt;

							$cs = new CreateSet($this->table);

							for ($i = 0; $i < count($is); $i++) {
								$cs->add($is[$i][0], $is[$i][1], $is[$i][2]);
							}

							$cs->execute();
						}
					}
				}
			}
		}
		/** For Down-Grade-Compatibility only **/
		function proccess() { $this->process(); }

		/**
		 * empty at the moment.
		 */
		function check() { }
	}
?>