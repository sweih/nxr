<?

	/**
	* Displays a textinput-field and a dropdown-input field. With the dropdown you
	* specify, what column of the selected table you want to filter wherease you
	* define the filter with the textinput-field.
	* As a result the class gives you also an array with pk and label values.
	* @package WebUserInterface
	*/
	class Filter {
		var $table;

		var $pk;
		var $columns = null;
		var $counter = 0;
		var $filter_rule = "";
		var $filter_column = "";
		var $filter_presentation = "";
		var $recordset;
		var $listsize = 20;
		var $link_action = "";
		var $new_action = "";
		var $add_condition = "";
		var $prevent_sysvar_disp = true;
		var $icon;
		var $type_name;
		var $orderColumn="";
		var $newLabel = ""; // Label which is displayed for creating a record.

		/**
		 * @param string db-table, on which you want to filter
		 * @param string primary key of the table
		 */
		function Filter($table, $pk) {
			global $lang;
			$this->table = $table;

			$this->pk = $pk;
			$this->icon = "li_standard.gif"; // 14x11 pixels.
			$this->type_name = "Records";
			// get the action template to use.
			$temp = explode("?", $_SERVER["REQUEST_URI"]);
			$temp2 = explode("/", $temp[0]);
			$this->link_action = $temp2[count($temp2) - 1];
			$this->new_action = $this->link_action;
			$this->newLabel = $lang->get("new");

			// check and initialize filter variables.
			$filter_rule = parseSQL(value("filter_rule", "", ""));
			$filter_column = value("filter_column", "NOSPACES");
			$filter_page = value("filter_page");

			if ($filter_rule != "0") {
				pushVar($this->link_action . "filter_rule", value("filter_rule", "", ""));

				pushVar($this->link_action . "filter_page", 1);
			}

			if ($filter_column != "0") {
				pushVar($this->link_action . "filter_column", value("filter_column"));

				pushVar($this->link_action . "filter_page", 1);
			}

			if ($filter_page != "0")
				pushVar($this->link_action . "filter_page", value("filter_page"));

			$this->filter_rule = getVar($this->link_action . "filter_rule");
			$this->filter_column = getVar($this->link_action . "filter_column");
		}

		/**
		 * Add a rule to the filter. Rules are columns, which one may filter for. Note that you may
		 * filter for column and present presentation_column on the output menu.
		 * @param string Text to be displayed in dropdown
		 * @param string Column, which you want to filter for
		 * @param string column, which you want to use for output.
		 */
		function addRule($label, $column, $presentation_column) {
			if ($this->filter_column == "")
				$this->filter_column = $column;

			if ($this->filter_column == $column)
				$this->filter_presentation = $presentation_column;

			$this->columns[$this->counter][0] = $label;
			$this->columns[$this->counter][1] = $column;
			$this->columns[$this->counter][2] = $label;
			$this->counter++;
		}
		
		
		/**
		 * Sets a column which is used permanently for ordering the data
		 * @param string Order-Column
		 */
		function setOrderColumn($column) {
			$this->orderColumn = $column;
		}

		/**
		 * sets the template that is to be called when a new item is to be created.
		 * Does not need to be set, if calling template is to be used.
		 * @param string php-file that is to be called.
		 */
		function setNewAction($action) { $this->new_action = $action; }

		/**
		 * Set additional conditions in the where clause of the Filter
		 * sql query.
		 * @param string condition additional to Filter rules.
		 */
		function setAdditionalCondition($condition) { $this->add_condition = $this->add_condition . " AND " . $condition . " "; }

		/**
		 * Does the query to the database with the choosen filter settings and processes the
		 * resulting Recordset.
		 */
		function fillRecordSet() {
			global $db, $errors;
			
			if ($this->orderColumn == "") {
				$orderBy = $this->filter_presentation;
			} else {
				$orderBy = $this->orderColumn;	
			}

			if ($this->prevent_sysvar_disp) {
				$sql = "SELECT $this->pk, $this->filter_presentation FROM $this->table WHERE $this->filter_column LIKE '%" . $this->filter_rule . "%' AND $this->pk > 999 " . $this->add_condition . " ORDER BY $orderBy";
			} else {
				$sql = "SELECT $this->pk, $this->filter_presentation FROM $this->table WHERE $this->filter_column LIKE '%" . $this->filter_rule . "%' " . $this->add_condition . " ORDER BY $orderBy";
			}

			$this->recordset = new query($db, $sql);

			if (trim($db->error()) != "0:") {
				$errors .= "-DBSelect($sql)";
			}
		}

		/**
		 * draw the filter-input and the filter-results.
		 */
		function draw() {
			$this->draw_form();
			$this->draw_list();
		}

		/**
		 * internal. Draw filter input.
		 */
		function draw_form() {
			global $lang;

			echo '<table width="170"><tr>';
			echo "<td>" . $lang->get("filter_rule", "Search for..."). "</td>";
			echo "<td>" . $lang->get("filter_column", "...in"). "</td></tr><tr>";
			$txtb = new Input("filter_rule", $this->filter_rule, "embedded", "16", "", $width = "90", "TEXT", "", 1);
			$txtb->draw();

			$select = new Dropdown("filter_column", $this->columns, "embedded", $this->filter_column, 80);
			$select->draw();
			echo "</tr><tr>";
			echo '<td colspan="2">';
			echo '<table width="100%" cellpadding="0" cellspacing="0" border="0">';
			echo '<tr><td>';
			echo '<input type="hidden" name="filter" value="">';
			echo '<input type="hidden" name="clearsearch" value="">';

			$submitbutton = new Button("filter", $lang->get("search", "Search"), "navelement", "submit", "", "filter");
			$submitbutton->draw();
			echo "&nbsp;&nbsp;";
			$clearbutton = new LinkButton("clearsearch", $lang->get("search_clear", "Reset Filter"), "navelement", "submit", "document.filter.filter_rule.value='';", "filter");
			$clearbutton->draw();
			br();
			br();
			echo "</td><tr></table></td></tr></table>";			
		}

		/**
		 * internal. draw filter results.
		 */
		function draw_list() {
			global $db, $sid, $go, $page_action, $lang, $c;

			$this->fillRecordSet();
			$rssize = $this->recordset->count();
			$pages = ceil($rssize / $this->listsize);

			// modified to filter_page sessions.
			if ($pages > 1) {
				for ($i = 0; $i < (($this->presentation_page - 1) * $this->listsize); $i++) {
					$this->recordset->getrow();
				}
			}


			echo '<div id="explore">';
			echo '<table width="100%" cellpadding="0" cellspacing="0" border="0">';
			echo '<tr><td>' . drawSpacer(5, 5). '</td></tr>';
			echo "<tr><td>" . drawSpacer(14, 11). "<a href=\"" . $this->new_action . "?sid=$sid&go=create\" class=\"list\">";
			echo "<b>" . $this->newLabel. "</b></a></td></tr>";
			$counter = 0;

			while ($this->recordset->getrow()) {
				// tried working with urlencode. Did not work here!
				$curl = $this->link_action . "?sid=$sid&go=update&oid=" . $this->recordset->field($this->pk);

				echo "<tr><td class=\"embedded\">";
				echo "<a href=\"" . $curl . "\" class=\"searchresult\"><img src=\"" . $c["docroot"] . "img/icons/" . $this->icon . "\" border=\"0\">" . $this->recordset->field($this->filter_presentation). "</a>";
				echo "</td></tr>";

				$counter++;
			}

			echo '<tr><td><br/> <br/></td></tr>';
			echo '</table>';
			echo '</div>';
			

			if ($this->filter_rule != "") {			
				echo '<div class="headerinformation" style="width:165px;">';
				echo $lang->get("is_filtered", "Note: You may not display all records because you are using a filter!");
				echo "</div>";			
			}
			
		}
	}
?>