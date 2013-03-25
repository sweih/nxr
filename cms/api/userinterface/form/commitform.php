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
	 * Form for prompting a question to the user and awaiting the answer.
	 *
	 * @package WebUserInterface
	 */
	class CommitForm extends Form {
		var $actions = null;
		var $backpage = "";
		/**
		  * Add an action, that may be perfomed when is selected in form
		  * @param string Identifier, used internally only.
		  * @param string Text, that will be prompted next to the checkbox.
		  * @param string &$action ActionHandler for actions that will be performed, when checkbox is selected.
		  */
		function addCheck($identifier, $text, &$action) {
			$this->add(new Label("lbl", $text, "standard"));

			$this->add(new Checkbox($identifier, $identifier, "standard"));
			$insertAt = count($this->actions);
			$this->actions[$insertAt][0] = $identifier;
			$this->actions[$insertAt][1] = $action;
			$this->actions[$insertAt][2] = $text;
			$this->setTopStyle('headerinformation');
		}

		/**
		  * processes the form. Used internally only.
		  */
		function process() {
			global $goon, $lang, $c, $errors;

			if ($goon == $lang->get("commit")) {

				// process all the registered checks.	
				for ($i = 0; $i < count($this->actions); $i++) {
					if (value($this->actions[$i][0]) != "0") {
						$this->actions[$i][1]->process($this->actions[$i][0]);

						if ($errors == "") {
							$this->addToTopText("<br><b>" . $this->actions[$i][2] . " ". $lang->get("var_succeeded"). "</b>");
						} else {
							$this->addToTopText($lang->get("error"));
							$this->setTopStyle("headererror;");
						}
					}
				}
			} else if ($goon == $lang->get("cancel")) {
				global $db;
				$db->close();
				if ($this->backpage == "") {				
				   header ("Location: " . $c["docroot"] . "api/userinterface/page/blank_page.php");
				} else {
				   header ("Location: " . $c["docroot"] . $this->backpage);
				}
				exit;
			}
		}

		/**
		 * internal. Draws the buttons of your form.
		 */
		function draw_buttons() {
			global $lang;

			echo "<tr><td align=\"right\" colspan=\"2\"><br>";
			$resetbutton = new ButtonInline("goon", $lang->get("cancel"), "navelement", "submit");
			echo $resetbutton->draw();
			echo "&nbsp;&nbsp;&nbsp;&nbsp;";
			$submitbutton = new ButtonInline("goon", $lang->get("commit"), "navelement", "submit", $this->getWaitupScreen());
			echo $submitbutton->draw();
			retain("goon", "");
			echo "</td></tr>";
		}
	}
?>