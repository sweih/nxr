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

	// used for interaction with containers.
	$sel_object = 0;

	/**
	 * Displays a kind of search engine for Content-Items for inserting
	 * CIs into a Cluster.
	 * @package DatabaseConnectedInput
	 */
	class CISelector {
		var $table;

		var $column;
		var $cond;
		var $fk;
		var $container = null;
		var $results = null;
		var $idlabel;
		var $type = 0;

		/**
		 * Standard constructor
		 * @param string name of the table, the data is to be stored
		 * @param string name of the column, the data is to be stored
		 * @param string Where-Condition to select the record that is to be updated.
		 * @param integer ID of the content-type, i.e. plugin.
		 */
		function CISelector($table, $column, $row_identifier, $type = 0) {
			$this->table = $table;

			$this->column = $column;
			$this->cond = $row_identifier;
			$this->type = $type;
			$this->fk = getDBCell($table, $column, $row_identifier);

			global $lang, $folder, $plugin, $searchin, $pattern, $db, $specialID;
			
			// $plugin = value("plugin");
			$searchin = value("searchin");
			$pattern = value("pattern");
			// $specialID = value("specialID");
			// $folder = value("folder");
			
			$this->add(new Label("lbl1", $lang->get("sr_selectci"), "informationheader", 2));

			//$this->add(new Label("lbl", "<b>".$lang->get("selectedobject")."</b>", "informationheader"));
			//if ($this->fk !=0) {
			//	$this->idlabel = new Label("lbl0", getDBCell("content", "NAME", "CID = ".$this->fk), "informationheader");
			//} else {
			//	$this->idlabel = new Label("lbl0", $lang->get("empty"), "informationheader");	  	
			//}

			//$this->add(&$this->idlabel);
			$this->add(new Label("lbl2", $lang->get("folder"), "standard"));
			$this->add(new Label("lbl3", $lang->get("contenttype"), "standard"));
			// folder dropdown.
			$folders[0][0] = "&gt;";
			$folders[0][1] = 0;

			createFolders($folders, ">", 0);

			$plugins = createNameValueArray("modules", "MODULE_NAME", "MODULE_ID", "1");
			$this->add(new Dropdown("folder", $folders, "standard", $folder, 220, 1));

			if ($this->type == 0) {
				$this->add(new Dropdown("plugin", $plugins, "standard", $plugin, 250, 1));
			} else {
				$type_name = getDBCell("modules", "MODULE_NAME", "MODULE_ID = $type");

				$this->add(new Label("lbl", $type_name, "standardlight", 1));
			}

			$searchins[0][0] = $lang->get("searchin");
			$searchins[0][1] = 0;
			$searchins[1][0] = $lang->get("name");
			$searchins[1][1] = 1;
			$searchins[2][0] = $lang->get("keywords");
			$searchins[2][1] = 2;
			$searchins[3][0] = $lang->get("description");
			$searchins[3][1] = 3;

			$this->add(new Dropdown("searchin", $searchins, "standard", $searchin, 220, 1));
			$this->add(new Input("pattern", $pattern, "standard", 32, "", 250));
			$this->add(new Cell("clc", "standard", 1, 250));
			$this->add(new ButtonInCell("search", $lang->get("search"), "standard", "SUBMIT"));

			global $search;
			$search = value("search");

			if ($search != "0") {

				// prepare search-pattern.
				$ppattern = strtoupper($pattern);

				$ppattern = ereg_replace(" ", "%", $ppattern);
				$ppattern = ereg_replace("\*", "%", $ppattern);

				if (!isset($plugin))
					$plugin = $this->type;

				$this->search("/", $folder, $plugin, $searchin, $ppattern);

				$this->add(new Label("lbl4", $lang->get("searchresults"), "standard"));
				$this->add(new Dropdown("CIDRES_" . $specialID, $this->results, "standard", $CID, 250, 1));
			}
		}

		/**
		 * Performs the search in given parameters. Makes use of global var results.
		 * Therefore the db must be open.
		 * @param string Prefix with which the search results will start.
		 * @param integer ID of the Node where to start the search 
		 * @param integer ID of the Plugin to search for.
		 * @param integer ID of which column to search for (1=name, 2=keywords, 3=description) 
		 * @param string search Pattern for the search column.
		 */
		function search($prefix, $node, $plugin, $searchin, $pattern) {
			global $db;

			// create search string...
			$searchsql = "SELECT CID, NAME FROM content WHERE CATEGORY_ID = $node AND VERSION=0 AND DELETED = 0";

			if ($plugin != -1)
				$searchsql .= " AND MODULE_ID = $plugin";

			if ($searchin != 0 && $pattern != "") {
				switch ($searchin) {
					case 1:
						$searchsql .= " AND UPPER(NAME) LIKE '%" . $pattern . "%'";

						break;

					case 2:
						$searchsql .= " AND UPPER(KEYWORDS) LIKE '%" . $pattern . "%'";

						break;

					case 3:
						$searchsql .= " AND UPPER(DESCRIPTION) LIKE '%" . $pattern . "%'";

						break;
				}
			}

			$searchsql .= " ORDER BY NAME ASC";
			$searchquery = new query($db, $searchsql);

			while ($searchquery->getrow()) {
				$nextId = count($this->results);

				$this->results[$nextId][0] = $prefix . "&nbsp;" . $searchquery->field("NAME");
				$this->results[$nextId][1] = $searchquery->field("CID");
			}

			$searchquery->free();

			if (count($this->results) < 50) {
				$treesql = "SELECT CATEGORY_ID, CATEGORY_NAME from categories WHERE DELETED = 0 AND PARENT_CATEGORY_ID=$node ORDER BY CATEGORY_NAME ASC";

				$treequery = new query($db, $treesql);

				while ($treequery->getrow()) {
					$name = $treequery->field("CATEGORY_NAME");

					$id = $treequery->field("CATEGORY_ID");
					// traverse the search down the tree...
					$nprefix = $prefix . "&nbsp;" . $name . "&nbsp;/";
					$this->search($nprefix, $id, $plugin, $searchin, $pattern);
				}

				$treequery->free();
			} else {
				$this->results[50][0] = "Too many results... Stopping";

				$this->results[50][1] = "-1";
			}
		}

		/**
		 * Draws the Content-Selector
		 */
		function draw() {
			echo "<table width=\"" . $this->width . "\" class=\"white\" cellpadding=\"0\" cellspacing=\"0\">\n";

			echo "<tr>";
			$cl1 = new Cell("cl1", "border", 1, 250, 1);
			$cl2 = new Cell("cl2", "border", 1, 250, 1);
			$cl1->draw();
			$cl2->draw();
			echo "</tr>";

			$col = 1;

			for ($i = 0; $i < count($this->container); $i++) {
				if ($col == 1)
					echo "<tr>\n";

				$col += $this->container[$i]->draw();

				if ($col > 2) {
					$col = 0;

					echo "</tr>";
				}

				$col++;
			}

			echo "</tr>";
			echo "</table>";
			return 2;
		}

		/**
		 * Checks, if Content is selected and if sets sel_object to the Conten-ID.
		 */
		function check() {
			global $specialID, $sel_object;
			
			// $specialID = value("specialID");
			// $sel_object = value("sel_object");

			$CIDRES = value("CIDRES_" . $specialID);

			if ($CIDRES != "0") {
				if ($CIDRES != "" && $CIDRES != 0 && $CIDRES != -1)
					$sel_object = $CIDRES;
			}
		}

		/**
		 * empty. For interface reasons only.	
		 */
		function process() { }
		/** For Down-Grade-Compatibility only **/
		function proccess() { $this->process(); }

		/**
		 * Add a DBO or WUI Object to the Selector. The Objects will be diplayed in the order, 
		 * they are added. 
		 * Used internally only!
		 * @param integer &$item The object, you want to add.
		 */
		function add(&$item) {
			$next = count($this->container);

			$this->container[$next] = &$item;
		}
	}
?>