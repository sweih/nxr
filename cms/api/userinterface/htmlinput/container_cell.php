<?
	/**********************************************************************
	 *	N/X - Web Content Management System
	 *	Copyright 2004 Fabian Koenig, fabian@nxsystems.org
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
	 * Container in form of a table cell that can take other objects.
	 * @package WebUserInterface
	 */
	class ContainerCell extends WUIInterface {
		var $columns;

		var $container = null;
		var $validatorContainer = null;

		/**
		  * standard constructor
		 * @param string $name Name of the form. Will be used in HTML.
		 */
		function ContainerCell($columns = 2) { $this->columns = $columns; }

		/**
		 * Add a DBO or WUI Object to the Container. The Objects will be diplayed in the order,
		 * you add them.
		 * @param integer &$item The object, you want to add.
		 */
		function add(&$item) {
			$next = count($this->container);

			$this->container[$next] = &$item;
		}

		/**
		   * Adds a number of empty cells.
		   * @param integer Number of empty Cells to add
		   */
		function addCells($number) { $this->add(new Cell("clc", $number, 1, 1)); }

		/**
		 * Add a Validator to the form.
		 * @param object reference to validator instance.
		 */
		function registerValidator(&$item) {
			$next = count($this->validatorContainer);

			$this->validatorContainer[$next] = &$item;
		}

		/**
		   * Process the Validators....
		   */
		function processValidators() {
			for ($i = 0; $i < count($this->validatorContainer); $i++) {
				$this->validatorContainer[$i]->validate();
			}
		}
		/** For Down-Grade-Compatibility only **/
		function proccessValidators() { $this->processValidators(); }

		/**
		 * Draw the form
		 */
		function draw() {
			global $errors, $lang, $go, $page_action;
			$this->draw_contents();
		}

		/**
		 * Process the form. Autochecking and saving is to be done here.
		 */
		function process() {
			//must overwrite by child-classes.
			global $errors, $page_state, $page_action;

			$this->check();
			if ($errors == "" && $page_state == "processing" && $page_action != "DELETE") {
				
				for ($i = 0; $i < count($this->container); $i++) {
					$this->container[$i]->process();					
				}
			}
		}
		/** For Down-Grade-Compatibility only **/
		function proccess() { $this->process(); }

		/**
		 * Check all container-objects for correct values as specified.
		 */
		function check() {
			$this->processValidators();

			for ($i = 0; $i < count($this->container); $i++) {
				$this->container[$i]->check();
			}
		}

		/**
		 * Writes the HTML-Code for the body of your Form. Calls the draw functions of the
		 * WUIObjects you put in the form-container.
		 */
		function draw_contents() {
			echo "<td colspan=\"".$this->columns."\">";
			echo "\n\n\n\n<!-- BEGIN DRAW CONTAINER CELL -->\n";

			$col = 1;

			for ($i = 0; $i < count($this->container); $i++) {
				if ($col == 1)
					echo "<tr>\n";
				$col += $this->container[$i]->draw();
				if ($col > $this->columns) {
					$col = 1;

					echo "</tr>";
				}
				
			}

			echo "\n<!-- END DRAW CONTAINER CELL -->\n\n\n\n";
			echo "</td>";
		}
	}
?>