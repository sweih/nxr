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
	 * Standard-Edit-Delete-Form.
	 * Use this form, if you want to add, edit or delete from a table in the database.
	 * You may also customize your actions for working on several tables in a row.
	 * Therefore you should use ActionHandlers or NonDisplayedValues for joining tables.
	 * @package WebUserInterface
	 */
	class stdEDForm extends Form {
		var $forbidDelete = false;

		var $forbidUpdate = false;
		var $submitButtonAction = "";

		/**
		 * Standard constructor
		 * @param string Headline, to be displayed in the form
		 * @param string $icon Icon, to be displayed in the form. Name of the file, stored in images folder.
		 * @param string $name Name of the form. Will be used in HTML.
		 * @param string $action Next script, that shall be called when submitting. Empty if self.
		 * @param string $method POST or GET for sending the form. POST is default and strongly recommended
		 * @param string $enctype Specify the enctype for the form. Use only when submitting files as multipart.
		 */
		function stdEDForm($headline, $icon = "", $name = "form1", $action = "", $method = "POST", $enctype = "") { 
			global $page_action;
			Form::Form($headline, $icon, $name, $action, $method, $enctype); 
		}

		/**
		 * Prohibts deleting an item with this Dialog.
		 * @param boolean True, if you want to forbid deleting.
		 */
		function forbidDelete($boolean) { $this->forbidDelete = $boolean; }

		/**
		 * Prohibts updating an item with this Dialog.
		 * @param boolean True, if you want to forbid deleting.
		 */
		function forbidUpdate($boolean) { $this->forbidUpdate = $boolean; }

		
		function draw() {
			global $lang, $page_action;
			if ($page_action != "INSERT" && !$this->forbidDelete && $page_action != "DELETE")
			  $this->buttonbar->add("deletion", $lang->get("delete", "Delete"), "", "document.".$this->name.".deletion.value=&quot;" . $lang->get("delete") . "&quot;;document.".$this->name.".submit();", $this->getWaitupScreen());  
			$this->add(new Hidden('deletion', ''));
			Form::draw();	
		}
		
		/**
		 * Process the form. Autochecking and saving is to be done here.
		 * process will be automatically called by the page->draw() method!
		 */
		function process() {
			global $errors, $page_state, $lang, $page_action, $oids, $oid, $filter_column, $filter_rule, $filter_page, $db, $sid;

			$this->check();
			if ($errors == "" && $page_state == "processing" && $page_action != "DELETE" && (value("changevariation") == "0" || value("changevariation") == "" )) {
			  			  
				for ($i = 0; $i < count($this->container); $i++) {
					$this->container[$i]->process();
				}
				if ((!$this->forbidUpdate && $page_action == "UPDATE") || $page_action == "INSERT")
					processSaveSets();

				for ($i = 0; $i < count($this->container); $i++) {
					$this->container[$i]->afterProcess();			
				}
			  if ((!$this->forbidUpdate && $page_action == "UPDATE") || $page_action == "INSERT")
			    processSaveSets();
			
				
				if ($errors != "") {
					$this->addToTopText($lang->get("saveerror"));
					$this->topstyle = 'headererror';
					
				} else {				
					if ($page_action == "INSERT") {						
						$page_action = "UPDATE";
						$oid = $oids[0];												
						$this->processHandlers("INSERT");						
					} else {
						$this->processHandlers("UPDATE");						
					}

					if ($errors == "") {
						$this->addToTopText($lang->get("savesuccess"));
						$this->topstyle = 'headersuccess';
						
					}
				}
			}

			if ($page_action == "DELETE" && !$this->forbidDelete) {
				$prompt = value("commit");

				if ($prompt == $lang->get("No")) {					

					$db->close();
					header ("Location: " . $this->action . "?sid=" . $sid . "&oid=" . $oid . "&go=update");
					exit;
				} else if ($prompt == $lang->get("Yes")) {
					$this->processHandlers("DELETE");

					$this->addToTopText($lang->get("deletesuccess"));
					$this->topstyle = 'headersucess';					
					$oid = "";
				} else {
					global $lang;

					$this->addTopYNPrompt($lang->get("promptdelete"));
				}
			}
			
			global $errors;
			if ($errors == "" && (value("action") == $lang->get("back") || value("action") == $lang->get("save_back"))  && $this->backto != "") {
				$db->close();

				header ("Location: " . $this->backto);
				exit;
			}
			
			if ($errors != "")
			  $this->topstyle = 'headererror';
		}

		/**
		 * Check all container-objects for correct values as specified.
		 * Check will be automatically called by the form->process method.
		 */
		function check() {
			global $page_action;

			$this->processValidators();

			if ($page_action != "DELETE") {
				for ($i = 0; $i < count($this->container); $i++) {
					$this->container[$i]->check();
				}
			}
		}

		/**
		* internal. Draws the buttons of your form.
		*/
		function draw_buttons() {
			global $page_action, $lang;
			br();
			if ($page_action != "DELETE") {
				$value = "";

				echo "<tr><td valign=\"middle\" align=\"right\" colspan=\"2\">";				
				
				
				if ($page_action == "INSERT") {					
					$value = "Create";
					
					$resetbutton = new Button("reset", $lang->get("reset_form", "Reset Form"), "", "reset");
					$resetbutton->draw();
					echo "&nbsp;&nbsp;";

					$submitbutton = new Button("goon", $value, "", "submit", $this->getWaitupScreen());
					$submitbutton->draw();					
				} elseif ($page_action == "UPDATE" && !$this->forbidUpdate) {
					$value = "Update";
					$resetbutton = new Button("reset", $lang->get("reset_form", "Reset Form"), "", "reset");
					$resetbutton->draw();
					echo "&nbsp;&nbsp;";

					$submitbutton = new Button("goon", $value, "", "submit", $this->submitButtonAction.$this->getWaitupScreen());
					$submitbutton->draw();					
				
				}

				echo "</td></tr>";
			} else {
				echo "<input type=\"hidden\" name=\"deletion\" value=\"delete\">";
			}
		}
	}

	/**
	 * Standard-Update-Form
	 * Is the same as stdEDForm, except that one cannot delete records.
	 * @package WebUserInterface
	 */
	class EditForm extends stdEDForm {


				
		function draw() {		
			Form::draw();	
		}
		
		/**
		  * internal. Draws the buttons of your form.
		  */
		function draw_buttons() {
			global $lang;

			echo "<tr><td valign=\"middle\" align=\"right\" colspan=\"2\">";
			$value = "Update";
			$resetbutton = new Button("reset", $lang->get("reset_form", "Reset Form"), "", "reset");
			$resetbutton->draw();
			echo "&nbsp;&nbsp;";
			retain("goon");
			$submitbutton = new Button("goon", $value, "", "submit", $this->submitButtonAction.$this->getWaitupScreen());
			$submitbutton->draw();	
			echo "</td></tr>";
		}
	}
?>