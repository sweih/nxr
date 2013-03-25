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
	 * Panel for use with PanelForms
	 * @package WebUserInterface
	 */
	class Panel {
		var $title;

		var $container = null;
		var $handlers = null;
		var $toptext = "";
		var $errorstyle = "headererror";
		var $backto = "";
		var $quickpanel;
		var $topicon;
		var $topstyle="headersuccess";

		/**
		  * Standard constructor
		  * @param $title string Title of the Panel
		  */
		function Panel($title) {
			$this->title = $title;
			$this->add(new Hidden("processing", "yes"));
			$this->quickpanel = false;
			
		}

		/**
		* Add a DBO or WUI Object to the Panel. The Objects will be diplayed in the order,
		* you add them.
		* @param integer &$item The object, you want to add.
		*/
		function add(&$item) {
			$next = count($this->container);
			$this->container[$next] = &$item;
		}

		/**
		* Add an ActionHandler to the form, which will be executed,
		* when the type of action specified in the handler is performed by the form. 
		*
		* @param integer Actions of type ActionHandler.
		*/
		function registerActionHandler(&$handler) { $this->handlers[count($this->handlers)] = &$handler; }

		/**
		* Executes all handlers of the specified type.
		* @param string  INSERT|DELETE|UPDATE
		*/
		function processHandlers($type) {
			for ($i = 0; $i < count($this->handlers); $i++) {
				$this->handlers[$i]->process($type);
			}
		}
		/** For Down-Grade-Compatibility only **/
		function proccessHandlers($type) { $this->processHandlers($type); }

		/**
		* Draw the Panel
		*/
		function draw() {
			global $errors, $lang, $go;
			if ($errors != "") {
				$this->addToTopText($lang->get("procerror"));
				$this->setTopStyle($this->errorstyle);
			}
			$this->draw_header();
			$this->draw_body();
			$this->draw_footer();
		}

		/**
		* Process the form. Autochecking and saving is to be done here.
		*/
		function process() {
			global $errors, $lang, $page_state, $page_action, $updatesets, $db;

			$this->check();
			if ($errors == "" && $page_state == "processing" && $page_action != "DELETE" && value("action") != $lang->get("back") && (value("changevariation") == "0" || value("changevariation") == "" ) && (value("changetranslation") == "0" || value("changetranslation") == "" )) {
			
			
				for ($i = 0; $i < count($this->container); $i++) {
					$this->container[$i]->process();
				}
				processSaveSets();
			
				$this->processHandlers($page_action);
			
			// after process
			  for ($i = 0; $i < count($this->container); $i++) {
					$this->container[$i]->afterProcess();			
				}
			  processSaveSets();
						

				if ($errors == "") {
					$this->addToTopText($lang->get("savesuccess"));								
				} else {
					$this->addToTopText($lang->get("saveerror"));					
				}
			}

			if ($errors == "" && (! $this->quickpanel) && (htmlentities(value("action")) == $lang->get("back") || htmlentities(value("action")) == $lang->get("save_back"))) {
				$db->close();
				header ("Location: " . $this->backto);
				exit;
			}
		}
		/** For Down-Grade-Compatibility only **/
		function proccess() { $this->process(); }

		/**
		 * Check all container-objects for correct values as specified.
		 */
		function check() {
			for ($i = 0; $i < count($this->container); $i++) {
				$this->container[$i]->check();
			}
		}

		
		/**
		 * Writes the HTML-Code for any Top-Text you added with the addToTopText-function.
		 */
		function draw_toptext() {
			global $formErrors;

			if ($formErrors != "")
				$this->toptext = $formErrors;

			if ($this->toptext != "") {
				echo '<tr><td colspan="2">'.drawSpacer(10,10).'</td></tr>';
				echo '<tr><td colspan="2" align="center">';				
				echo getBoxedText($this->toptext, $this->topstyle);
				echo "</td></tr>";
				echo '<tr><td colspan="2">'.drawSpacer(10,10).'</td></tr>';				
			}
		}

		/**
		* Sets the default style-sheet for the output-lines on top of the panel. You can add
		* text by using the addToTopText-function. Therefore the standard style is 'informationheader'.
		* You may change the style by using this function.
		* @param string The CSS-Style, you want to select.
		*/
		function setTopStyle($style) { $this->topstyle = $style; }

		/**
		* Writes the HTML-Code for the body of your Panel. Calls the draw functions of the
		* WUIObjects you put in the form-container.
		*/
		function draw_body() {
			$this->draw_toptext();

			$col = 1;
			$modified = true;			
			for ($i = 0; $i < count($this->container); $i++) {			
				if ($col == 1 && $modified) echo "<tr>\n";			
				$colBak = $col;
				$col += $this->container[$i]->draw();
				if ($col != $colBak) {
					$modified = true;
				} else {
					$modified = false;	
				}
				if ($col > 2) {
					$col = 1;
					echo "</tr>";
					
				}
					
			}
		}

		/**
		 * Every form has a region, where you can display text. Use this function
		 * to add text for display.
		 * @param string Text, you want to add for display.
		 */
		function addToTopText($text) { $this->toptext .= $text; }

		/**
		* Writes the HTML-Code for the beginning of your form. May be overwritten by specialized forms.
		* stores also the variables sid and oid as hidden input fields.
		*/
		function draw_header() { echo "<table width=\"100%\" class=\"design\" cellpadding=\"0\" cellspacing=\"0\">\n"; }

		/** 
		 * Writes the HTML-Code for the end of your form. May be overwritten by custom classes
		 */
		function draw_footer() { echo "</table>\n"; }
	}
?>