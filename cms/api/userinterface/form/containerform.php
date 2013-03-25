<?
	/**********************************************************************
	 *	N/X - Web Content Management System
	 *	Copyright 2002-2004 Sven Weih
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
	 * Container Form
	 * This form is used, to display plugins and provides therefore
	 * functions for creating, editing and changing order of items.
	 * You should not use this form, unless you know, what to do.
	 * This form cannot be used stand alone. You must create Subclasses
	 * with special properties.
	 * Plugins are of class Plugin.
	 * @package WebUserInterface
	 */
	class ContainerForm extends Form {
		var $value = "";

		var $containerName = "";
		var $containerDescription = "";
		var $drawPage = "";
		var $new_table = "";
		var $new_pkname = "";
		var $new_name = "";
		var $new_description = "";
		var $item_table = "";
		var $item_pkname = "";
		var $item_name = "";
		var $item_parentKeyName = "";
		var $item_fkname = "";
		var $state = "";
		var $objects = null;

		/**
		 * Standard constructor
		 * @param string Headline, to be displayed in the form
		 * @param string $icon Icon, to be displayed in the form. Name of the file, stored in images folder.
		 * @param string $name Name of the form. Will be used in HTML.
		 * @param string $action Next script, that shall be called when submitting. Empty if self.
		 */
		function ContainerForm($headline, $icon = "", $name = "form1", $action = "") { Form::Form($headline, $icon, $name, $action, "POST", ""); }

		/**
		 * Does operations move up, move down, delete, edit.
		 * @param integer PK-ID of the Object
		 * @param string Action that is to be performed
		 */
		function process_object_dispatcher($id, $action) {
			// developer needs to customize.
			}
		/** For Down-Grade-Compatibility only **/
		function proccess_object_dispatcher($id, $action) { $this->process_object_dispatcher($id, $action); }

		/**
		 * Use to synchronize changes of the model to the data-structures.
		 */
		function synchronize() { }

		/**
		 * Updates the changes towards the object with the given id.
		 * @param integer Id of the item, to be deleted.
		 */
		function process_object_update($id) { }
		/** For Down-Grade-Compatibility only **/
		function proccess_object_update($id) { $this->process_object_update($id); }

		/**
		 * Deletes one item in the context and does repositioning of the others.
		 * @param integer Id of the item, to be deleted.
		 */
		function process_object_delete($id) {
			global $oid, $db, $go;
			
			$oid = value("oid", "NUMERIC");
			$go = value("go", "NOSPACES");

			// get the position
			$position = getDBCell($this->item_table, "POSITION", $this->item_pkname . "= $id");
			$delete1 = "DELETE FROM " . $this->item_table . " WHERE " . $this->item_pkname . " = $id";
			$update = "UPDATE " . $this->item_table . " SET POSITION = (POSITION-1) WHERE POSITION> $position AND " . $this->item_parentKeyName . " = $oid";

			$query = new query($db, $delete1);
			$query = new query($db, $update);
			$query->free();

			$go = "UPDATE";
			$this->filterItems();
		}
		/** For Down-Grade-Compatibility only **/
		function proccess_object_delete($id) { $this->process_object_delete($id); }

		/**
		 * Draws the objects and their corresponding buttons. 
		 * makes use of draw_dispatcher.
		 */
		function draw_objects() {
			global $lang;
			$grid = new NXGrid("grid", 7);
			$grid->setRatio(array (	140,
									140,
									150,
									20,
									30,
									70,									
									70));
			
			$grid->addTitleRow(array( $lang->get("name"), $lang->get("type"), $lang->get("status"), "&nbsp;", "&nbsp;", "&nbsp;", "&nbsp;"));

			for ($i=1; $i <= count( $this->objects ); $i++) {
				$grid->addRow( 	array( 	new Label("lbl", "<b>".$i.". ".$this->objects[$i]["name"]."</b>", "cwhite_small" ),
			  							new Label("lbl", $this->getType($i), "cwhite_small"),
			  							new Label("lbl", $this->getInformation($i), "cwhite_small"),			  							
			  							new LinkButtonInCell("s".$this->objects[$i]["id"], $lang->get("up"), "navelement", "submit"),
			  					 		new LinkButtonInCell("s".$this->objects[$i]["id"], $lang->get("down"), "navelement", "submit"),
										new LinkButtonInCell("s".$this->objects[$i]["id"], $lang->get("config"), "navelement", "submit"),
			  					 		new LinkButtonInCell("s".$this->objects[$i]["id"], $lang->get("delete"), "navelement", "submit")));
			  retain("s".$this->objects[$i]["id"], "");
			}			
				
			echo '<tr><td colspan="2" class="informationheader">';
			$lbi = new ButtonInline("neu", $lang->get("new"), "navelement", "submit");
			echo $lbi->draw().'<br><br>';
			retain("neu", "");
			echo '<tr>';
			$cl = new Cell("clc", "standardwhite", 2, 600, 1);
			$cl->draw();
			echo '</tr>';
			echo '</td></tr>';
			echo '<tr>';
			$grid->draw();
			echo '</tr>';

		}


		/**
		 * draw the form for creating new items.
		 * May be overwritten if standard Display should not be used!
		 */
		function draw_new() {
			global $db, $lang, $go, $oid, $newitem, $errors, $page_action;
			
			$newitem = value("newitem");
			$oid = value("oid", "NUMERIC");
			$go = value("go", "NOSPACES");
			
			//needed for uniqueness checks.
			$page_action = "INSERT";
			$go = "UPDATE";

			$container = new Container(4);
			$container->add(new Label("lbl", $lang->get("selectobject"), "informationheader", 3));
			$container->add(new Cell("clc", "cwhite", 1, 20,10));
			$container->add(new Cell("clc", "cwhite", 1, 150,10));
			$container->add(new Cell("clc", "cwhite", 1, 350,10));
			// what data should be displayed

			// check if item is selected.
			$stdStyle = "standardlight";
			if ($this->state == creating && $newitem == "0") {
				$errors .= "-MANDATORY";
				$stdStyle = "error";				
				$container->add(new Label("lbl", $lang->get("selectone"), "error", 3));
			}
			$sql = "SELECT " . $this->new_pkname . ", " . $this->new_name . ", " . $this->new_description . " FROM " . $this->new_table . " ORDER BY " . $this->new_name;
			$query = new query($db, $sql);
			while ($query->getrow()) {
			    $pk = $query->field($this->new_pkname);
				$checked = false;
			    if (value("newitem", "NUMERIC") == $pk) $checked = true;
				$container->add(new Radio("newitem", $pk, $stdStyle, $checked));
				$container->add(new Label("name", $query->field($this->new_name), "standard", 2));
				$container->add(new Cell("clc", "standard", 1));
				$container->add(new Label("desc", $query->field($this->new_description), "description", 2));
			}
			
			$nameinput = new TextInput($lang->get("name"), $this->item_table, $this->item_name, "1", "type:text,size:32,width:200", "UNIQUE&MANDATORY");
			$nameinput->setFilter($this->item_parentKeyName . " = $oid");
			$positioninput = new TextInput($lang->get("position"), $this->item_table, "POSITION", "1", "type:text,size:3,width:30", "MANDATORY", "NUMBER");

			if ($this->state == "creating") {
				$nameinput->check();
				$positioninput->check();
			}
			$container->add(new Cell("cll", "standard", 3, 10, 10));
			$container->add(new Cell("cll", $nameinput->std_style, 1, 10, 1));
			$container->add($nameinput);
			$container->add(new Cell("cll", $positioninput->std_style, 1, 10, 1));
			$container->add($positioninput);
						
			
			// drawing.
			echo drawSpacer(1,1)."</td><td>".drawSpacer(1,1)."</td></tr>"; // cleanup the shit.
			echo "<tr><td colspan=\"2\"><br>";			
			echo '<table width="100%" cellpadding="0" cellspacing="0" border="0">';
			$container->draw();
			echo '</table>';
			echo '</td></tr>';
			echo '<tr><td colspan="2" align="right">';
			echo '<br/>';
			
			$resetbutton = new Button("reset", $lang->get("reset"), "navelement", "reset");
			$resetbutton->draw();
			echo drawSpacer(50,1);
			$backbutton = new Button("back", $lang->get("back"), "navelement", "submit");
			$backbutton->draw();
			echo '&nbsp;&nbsp;';
			$submitbutton = new Button("neu", $lang->get("create"), "navelement", "submit");
			$submitbutton->draw();
			retain("oid", $oid);
			retain("action", "editobject");
				
		
		}
		
		
		/**
		 * Draw the form
		 */
		function draw() {
		 $this->title = $this->title." <i>".$this->containerName."</i>";
		 Form::draw();
		}
		
		/**
		 * Draw the form
         */
		function draw_contents() {
			global $errors, $lang, $go, $back, $page_action, $oid;			
			$go = value("go", "NOSPCAES");
			$back = value("back");
			$oid = value("oid", "NUMERIC");			
			
			// deleting prompt returns whether commit or cancel!
			if ($this->drawPage == "deleting") {
				//$this->draw_header();
				$this->draw_innerbody();
				retain("oid", $oid);
				$this->draw_footer();
			} else if ($this->drawPage == "new") {
				$this->draw_body_head();
				$this->draw_new();
				if ($this->state == "creating")
					$this->process_new();				
				$this->draw_footer();
			} else if ($this->drawPage == "editing") {
				$page_action = "UPDATE";
				//$this->draw_header();
				$this->draw_innerbody();
				$this->draw_mybuttons();
				//			echo "</table>";
				//$this->draw_footer();
			} else if ($go != "" || $back != "") {				
				$page_action = "UPDATE";				
				$this->draw_body_head();
				$this->draw_innerbody();
				$this->draw_objects();
				$this->draw_footer();
			}	
		}
		
				/** 
		 * Draw the head of a forms body.
		 * May be overwritten for customization.
		 */
		function draw_body_head() {
			global $lang;
			$this->add(new Cell("cell", "cwhite", 2, 1, 10));
			$this->add(new Label("lbl", $lang->get("dosomething"), "informationheader", 2));
			$this->add(new Cell("cell", "cwhite", 2, 1, 10));
		}
		
		/**
		* Writes the HTML-Code for the body of your Form. Calls the draw functions of the
 		* WUIObjects you put in the form-container.
		*/
		function draw_innerbody() {			
			retain("oid", value("oid", "NUMERIC"));
			echo '<table width="100%" border="0" cellpadding="0" cellspacing="0">';
				echo '<tr><td>';
				$this->buttonbar->draw();			
				echo '</td></tr>';
				echo '</table>'			;
				
			echo "<table width=\"100%\" class=\"white\" cellpadding=\"0\" cellspacing=\"0\">";				
			echo "<tr>";
			$cl1 = new Cell("cl1", "border", 1, ceil($this->width / 3), 1);
			$cl2 = new Cell("cl2", "border", 1, ceil(($this->width / 3) * 2), 1);
			$cl1->draw();
			$cl2->draw();
			echo "</tr>";
			
			$this->draw_toptext();
			$col = 1;			
			
			for ($i = 0; $i < count($this->container); $i++) {
				if ($col == 1)
					echo "<tr valign=\"top\">\n";
				$col += $this->container[$i]->draw();
				if ($col > $this->col) {
					$col = 0;
					echo "</tr>";
				}
				$col++;
			}			
			if ($col != 1) echo "</tr>";
			echo "</table>";			
			//echo "</td></tr>"; // close tags opened by form.
		}

		/**
		 * Draw the buttons
		 */
		function draw_buttons() { }

		/**
		* internal. Draws the buttons of your form.
		*/
		function draw_mybuttons() {
			global $lang;

			if ($this->drawPage == "editing") {
				echo "<tr><td class=\"informationheader\" colspan=\"2\">";
				tableStart("100%", "informationheader");
				echo "<tr>";
				echo '<td align="right">';
				br();
				retain ("goon");
				$resetbutton = new  ButtonInline("reset", $lang->get("reset_form", "Reset Form"), "navelement", "reset", "", $this->name);
				echo $resetbutton->draw();			
				echo drawSpacer(50,1);
				$value = "Update";
				$backbutton = new ButtonInline("back", $lang->get("back"), "navelement", "submit", "",$this->name );
				echo $backbutton->draw();				
				retain("back", "");
				echo '&nbsp;&nbsp;';
				$submitbutton = new ButtonInline("goon", $value, "navelement", "submit", $this->submitButtonAction, $this->name);
				echo $submitbutton->draw();				
				br();
				br();
				tde();
				echo ('</tr>');
				tableEnd();
				echo "</td></tr>";
			}
		}

		/**
		 * Specify tables and columns, in which the items of a template
		 * are stored.
		 * @param string Name of the table
		 * @param string Name of the Primary-key-Column
		 * @param string Name of the identifier-column of an item.
		 * @param string Name of the parent key that selects the table.
		 * @param string Name of the foreign-key column of an item.
		 */
		function define_item($table, $pkname, $name, $parkname, $fkname) {
			$this->item_table = $table;
			$this->item_pkname = $pkname;
			$this->item_parentKeyName = $parkname;
			$this->item_name = $name;
			$this->item_fkname = $fkname;
		}

		/**
		 * Declare the tables and columns, that are to be used for
		 * creating the new item Catalogue window and for creating the
		 * new items in the end. Is used by standard draw_new() function.
		 * @param string table, where the plugin information is stored.
		 * @param string primary key of the table.
		 * @param string $name Column, where to take the Name of a Module from
		 * @param string $description Column, where to take the Description of a Module from
		 */
		function define_new($plugInTable, $pkname, $name = "NAME", $description = "DESCRIPTION") {
			$this->new_table = $plugInTable;
			$this->new_pkname = $pkname;
			$this->new_name = $name;
			$this->new_description = $description;
		}


		/**
		 * Processes the form.
		 */
		function process() {
			global $lang, $go, $back, $page_action, $errors;			
			$neu = value("neu");
			$commit = value("commit");
			$cancel = value("cancel");
			$did = value("did");
			$editing = value("editing", "NUMERIC");
			$back = value("back");
	
			$go = "UPDATE";
			$this->filterItems();

			if (($editing  != "0") && ($back == "0")) {
				$this->process_object_update($editing);
			}
				
			if ($neu != "0" && $neu != "") {				
				if ($neu == $lang->get("create")) {				
					$this->state = "creating";
				}

				$this->drawPage = "new";
			} else {
				for ($i = 1; $i <= count($this->objects); $i++) {
					if (value("s" . $this->objects[$i]["id"]) != "0")
						$this->process_object_dispatcher($this->objects[$i]["id"], value("s" . $this->objects[$i]["id"]));
				}
			}

			if ($cancel != "0")
				$go = "UPDATE";
			if (($commit  == $lang->get("yes")) && ($did  != "0"))
				$this->process_object_delete($did);			
		
		}
		/** For Down-Grade-Compatibility only **/
		function proccess() { $this->process(); }

		/**
		* Determines the Maximum Position of a item in a container.
		* @return integer Maximum Position
		*/
		function getMaxPos() {
			global $db, $oid;
			
			$oid = value("oid", "NUMERIC");

			//get maximum position at the moment.
			$maxpos = 1;
			$sql = "SELECT MAX(POSITION) AS MPOS FROM " . $this->item_table . " WHERE " . $this->item_parentKeyName . " = $oid";
			$query = new query($db, $sql);

			if ($query->getrow()) {
				$maxpos = $query->field("MPOS");
			}

			$query->free();

			return $maxpos;
		}

		/**
		 * creates a new object in the container.
		 */
		function process_new() {
			global $errors, $db, $go, $oid, $newitem, $lang;
			$newitem = value("newitem", "NUMERIC");
			$go = value("go");
			$oid = value("oid", "NUMERIC");

			// check, if any errors happened, while checking and drawing the form.
			if ($errors == "") {
				$maxpos = $this->getMaxPos();

				$position = value($this->item_table . "_POSITION", "NUMERIC");
				global $c_magic_quotes_gpc;
				$name = value($this->item_table . "_" . $this->item_name);

				if ($c_magic_quotes_gpc == 1)
					$name = str_replace("\\", "", $name);

				if ($position == 0)
					$position = 1;

				if ($position > $maxpos) {
					$position = $maxpos + 1;
				} else {
					// reorder the positions of the item.
					$sql = "UPDATE " . $this->item_table . " SET POSITION = (POSITION+1) WHERE " . $this->item_parentKeyName . " = $oid AND POSITION >= $position";
					$query = new query($db, $sql);
				}

				// create new record in db.
				$nextId = nextGUID();
				$pname = parseSQL($name);
				$sql = "INSERT INTO " . $this->item_table . " (" . $this->item_pkname . "," . $this->item_parentKeyName . "," . $this->item_name . ", POSITION," . $this->item_fkname . ") VALUES  (" . $nextId . "," . $oid . ",'" . $pname . "'," . $position . "," . $newitem . ")";
				$query = new query($db, $sql);
				$this->synchronize();

				if ($errors == "") {
					$text = "";
					
					$text .= "<br><b>" . $lang->get("name"). ":</b> " . $name . "<br>";
					$text .= "<b>" . $lang->get("cltposition", "Position"). ":</b> " . $position . "<br>";
					$text .= "<b>" . $lang->get("type"). ":</b> " . getDBCell($this->new_table, $this->new_name, $this->new_pkname . "=" . $newitem). "<br><br>";
					echo '<tr><td colspan="2"><br></td></tr>';
					echo '<tr><td colspan="2" class="headbox">'.$lang->get('created_item', 'The following Placeholder was created:').'</td></tr>';
					echo '<tr><td colspan="2">'.$text.'</td></tr>';
					
				}
			}
		}
		/** For Down-Grade-Compatibility only **/
		function proccess_new() { $this->process_new(); }

		/**
		   * Selects the elements of a container. Must be overwritten.
		   */
		function filterItems() {
			global $db, $oid;	
			$oid = value("oid", "NUMERIC");
			$this->objects = null;
			$sql = "SELECT " . $this->item_pkname . ", " . $this->item_name . ", " . $this->item_fkname . " FROM " . $this->item_table . " WHERE " . $this->item_parentKeyName . " = $oid ORDER BY POSITION ASC";
			$query = new query($db, $sql);
			$counter = 1;

			while ($query->getrow()) {
				$this->objects[$counter]["id"] = $query->field($this->item_pkname);
				$this->objects[$counter]["name"] = $query->field($this->item_name);
				$this->objects[$counter]["plugin"] = $query->field($this->item_fkname);
				$counter++;
			}
		}

		/**
	 * Move the item with pk ID one position down.
	 * @param integer ID of the Item to move.
	 */
		function move_down($id) {
			$oid = value("oid", "NUMERIC");
			moveRowDown($this->item_table, $this->item_pkname, $id, "POSITION", $this->item_parentKeyName . "=$oid");
		}

		/**
		 * Move the item with pk ID one position up.
		 * @param integer ID of the Item to move.
		 */
		function move_up($id) {
			$oid = value("oid", "NUMERIC");
			moveRowUp($this->item_table, $this->item_pkname, $id, "POSITION", $this->item_parentKeyName . "=$oid");
		}

		/**
		   * Sets the value of the PK that is to be used as Container and parent table.
		   * The method filterItems which must be overwritten will use that key!
		   * @param string Name of Parent Table
		   * @param string Name of PK in Parent Table.
		   * @param string Value of the Primary key.
		   */
		function define_home($table, $pkname, $value) {
			$this->parent_value = $value;
			$this->containerName = getDBCell($table, "NAME", $pkname . " = " . $value);
			$this->containerDescription = getDBCell($table, "DESCRIPTION", $pkname . " = " . $value);
		}
		
		
				/**
		* Writes the HTML-Code for the beginning of your form. May be overwritten by specialized forms.
		* stores also the variables sid and oid as hidden input fields.
		*/
		function draw_header() {
			global $sid, $page_action, $filter_rule, $filter_column, $filter_page;			
			if ($this->name=="") $this->name="form1";
			echo '<div id="foform1"></div><div id="waitbox"></div>';
			echo "<form name=\"$this->name\" action=\"$this->action\" method=\"$this->method\"";
			//if ($this->enctype !="") {
			echo " enctype=\"multipart/form-data\"";
			//}
			echo ">\n";
			echo "<input type=\"hidden\" name=\"processing\" value=\"yes\">";
			echo "<input type=\"hidden\" name=\"sid\" value=\"$sid\">";

			if ($page_action == "UPDATE" || $page_action == "DELETE") {
				global $oid;

				echo "<input type=\"hidden\" name=\"oid\" value=\"$oid\">";
			}
		}

		/**
		* Writes the HTML-Code for the end of your form. May be overwritten by custom classes
		*/
		function draw_footer() {
			global $lang;
			echo "</form>"; // echo "\n</table>\n</td></tr>\n</table>\n</form>\n";			
		}
		

	}
?>