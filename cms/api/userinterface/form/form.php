<?
	/**********************************************************************
	 *	N/X - Web Content Management System
	 *	Copyright 2002-2006 Sven Weih, FZI Research Center for Information Technologies
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
	 * Base of all forms used in the cms. Stores data and gives support for drawing
	 * form headers, footers etc. Is also base container, for all Form-Objects (DBO, WUI)
	 * @package WebUserInterface
	 */
	class Form extends WUIInterface {
		var $name;

		var $title;
		var $action;
		var $method;
		var $enctype;
		var $icon;

		var $container = null;
		var $validatorContainer = null;
		var $cols = 2;
		var $toptext = "";
		var $topstyle = "headersuccess";
		var $errorstyle = "headererror";
		var $handlers = null;
		var $width = 600;
		var $topicon = "";
		var $headline = "";
		var $backto = "";
		var $avoidPKGeneration = false;
		var $buttonbar;
		var $question;
		var $headerlink;

		/**
		  * standard constructor
		 * @param string $title Title, to be displayed in the form
		 * @param string $icon Icon, to be displayed in the form. Name of the file, stored in images folder.
		 * @param string $name Name of the form. Will be used in HTML.
		 * @param string $action Next script, that shall be called when submitting. Empty if self.
		 * @param string $method POST or GET for sending the form. POST is default and strongly recommended
		 * @param string $enctype Specify the enctype for the form. Use only when submitting files as multipart.
		 */
		function Form($title, $icon = "", $name = "form1", $action = "", $method = "POST", $enctype = "") {
			global $c_theme;

			$this->icon = $icon;
			$this->title = $title;
			$this->name = $name;
			$this->method = $method;
			$this->enctype = $enctype;

			if ($action == "") {
				$temp = explode("?", $_SERVER["REQUEST_URI"]);

				$this->action = $temp[0];
			} else {
				$this->action = $action;
			}

			$this->buttonbar = new ButtonBar($name . "_buttonbar");
			$this->width = '600';
			$this->question="";
		}

		/**
		 * Add a DBO or WUI Object to the Form. The Objects will be diplayed in the order,
		 * you add them.
		 * @param integer &$item The object, you want to add.
		 */
		function add(&$item) {
			$next = count($this->container);
			$this->container[$next] = &$item;
			$item->setParent($this);
			$item->initialize();
		}

		
		/**
		 * Add the variation(language)-selector to the form
		 */
		function enableVariationSelector() {
			global $oid, $go, $page_action, $page_state;
			$this->buttonbar->setVariationSelector(createNameValueArrayEx("variations", "NAME", "VARIATION_ID", "DELETED=0", "ORDER BY NAME ASC"), variation());
			$this->add(new Hidden("goon", $page_action));
			if (isset($oid)) {
			  $this->add(new Hidden("oid", $oid));
			}
			if (value("changevariation") != "0") $page_state="";
		}
		
		/**
		 * Set the Primary Key and the table of one of the form objects.
		 * As result you will get a where-condition, you can use as row_identifier
		 * with your DBO objects.
		 * @param string Name of the table, you want to process
		 * @param string Name of the Primary Key.
		 * @return string where condition for row_identifier.
		 */
		function setPK($table, $key) {
			global $page_action, $oid, $temp_oid;
			
			if (($page_action == "INSERT") && ! $this->avoidPKGeneration) {
				addInsert($table, $key, "", "NUMBER");
				$oid = $temp_oid;
				return "1";
			} else {
				if (! is_numeric($oid)) {
					$where = "$key = '$oid'";					
				} else {
					$where = "$key = $oid AND $key > 999";
				}
				return $where;
			}
		}
		
		/**
		 * Adds a text to the right of the form header.
		 *
		 * @param unknown_type $payload
		 */
		function addHeaderLink($payload) {
			$this->headerlink.=$payload;
		}

		/**
		 * Set the Primary Key and the table of one of the form objects.
		 * As result you will get a where-condition, you can use as row_identifier
		 * with your DBO objects. Does not the system-vars check.
		 * @param string Name of the table, you want to process
		 * @param string Name of the Primary Key.
		 * @return string where condition for row_identifier.
		 */
		function setExPK($table, $key) {
			global $page_action, $oid, $temp_oid;
			
			if (($page_action == "INSERT") && ! $this->avoidPKGeneration) {
				addInsert($table, $key, "", "NUMBER");
				//2.12.2001: changed: saving temp_oid to oid for processing InsertHandlers.
				$oid = $temp_oid;
				return "1";
			} else {
				if (! is_numeric($oid)) {
					$where = "$key = '$oid'";					
				} else {
					$where = "$key = $oid";
				}
				return $where;
			}
		}

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
		 * Every form has a region, where you can display text. Use this function
		 * to add text for display.
		 * @param string Text, you want to add for display.
		 */
		function addToTopText($text) { $this->toptext .= $text; }

		/**
		* Add an ActionHandler to the form, which will be executed,
		* when the type of action specified in the handler is performed by the form.
		*
		* @param integer Actions of type ActionHandler.
		*/
		function registerActionHandler(&$handler) { $this->handlers[count($this->handlers)] = &$handler; }

		/**
		* Executes all handlers of the specified type.
		*
		* @param string  INSERT|DELETE|UPDATE
		*/
		function processHandlers($type) {
			for ($i = 0; $i < count($this->handlers); $i++) {
				$this->handlers[$i]->process($type);
			}
		}
		/** For Down-Grade-Compatibility only **/
		function proccessHandlers($type) { $this->processHandlery($type); }

		/**
		 * Add a prompt to the top of the form, in which the user may choose YES or NO
		 * the result will be existence of commit or cancel in next processing step.
		 * @param string Text, that will be displayed for prompt.
		 */
		function addTopYNPrompt($text, $title="") {
			global $lang;
			if ($title=="") 
			  $title = $lang->get('thereisquestion', 'Confirm action');
			
			$this->question = getFormHeadline($title, "100%");
			$this->question.= '<table width="100%" border="0" cellpadding="0" cellspacing="0"><tr><td><br>';
			$this->question.= $text;
			$this->question.= '<input type="hidden" name="commit" value=""><br><br>';
			$this->question.= '</td></tr><tr><td align="right">';		
			$this->question .= getButton("commit", $lang->get("no"), "navelement", "submit", $this->getWaitupScreen(), $this->name);
			$this->question .= "&nbsp;&nbsp;";
			$this->question .= getButton("commit", $lang->get("yes"), "navelement", "submit", $this->getWaitupScreen(), $this->name);
			$this->question .= "</td></tr></table>";
			$this->question.=getFormFooterline().'<br/><br/>';
		
		}

		/**
		 * Draw the form
		 */
		function draw() {
			global $errors, $lang, $go, $page_action, $c, $oid;   
			if ($go != "" || $page_action != "") {
				if ($errors != "") {
					$this->addToTopText($lang->get("procerror"));					
				}
				$this->draw_body();	  
			}
		}

		/**
		 * Process the form. Autochecking and saving is to be done here.
		 */
		function process() {
			//must overwrite by child-classes.
			$this->check(); 
		}
		

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
		 * Writes the HTML-Code for any Top-Text you added with the addToTopText-function.
		 */
		function draw_toptext() {
			global $formErrors;

			if ($formErrors != "")
				$this->toptext = $formErrors;

			if ($this->toptext != "") {
				echo '<tr><td colspan="2" align="center">';
				echo getBoxedText($this->toptext, $this->topstyle);
				br();			
				echo "</td></tr>";
			}
		}

		/**
		* Sets the default style-sheet for the output-lines on top of your form. You can add
		* text by using the addToTopText-function. Therefore the standard style is 'informationheader'.
		* You may change the style by using this function.
		* @param string The CSS-Style, you want to select.
		*/
		function setTopStyle($style) { $this->topstyle = $style; }

		/**
		* Draws the buttons of your form. Must be overwritten by child-classes to write the
		* HTML for button output.
		*/
		function draw_buttons() { }

		/**
		 * Creates Javascript code for showing the waitup screen
		 */
		function getWaitupScreen() {
		  return getWaitupScreen($this->name);
		}
		
		/**
		* Writes the HTML-Code for the body of your Form. Calls the draw functions of the
		* WUIObjects you put in the form-container.
		*/
		function draw_body() {
			 global $oid; 
			 $this->draw_header();			
			  echo '<table width="'.$this->width.'" border="0" cellpadding="0" cellspacing="0"><tr><td>';			  
			  if (strlen($this->question)>0)
			    echo $this->question;
			  $this->headerlink = str_ireplace('<oid>', $oid, $this->headerlink);
			  echo getFormHeadline($this->title,'100%', $this->headerlink);
			  $this->draw_contents();			  
			  echo getFormFooterline();					  	
			  $this->draw_footer();
			  echo '</td></tr></table>';
		}

		/**
			* Writes the HTML-Code for the contents inside the form
		 */
		function draw_contents() {
			// Contents
			echo '<table width="100%" cellpadding="0" cellspacing="0" border="0">';
			echo '<tr><td>';
			$this->buttonbar->draw();			
			echo '</td></tr>';
			echo '</table>';

			echo "<table width=\"100%\" cellpadding=\"0\" cellspacing=\"0\" align=\"center\" border=\"0\">";
			//echo "<tr>";
			//$cl1 = new Cell("cl1", "border", 1, ceil($this->width / 5)*2, 12);
			//$cl2 = new Cell("cl2", "border", $this->cols-1, ceil(($this->width / 5)*3 ), 12);
			//$cl1->draw();
			//$cl2->draw();
			//echo "</tr>";
			$this->draw_toptext();			
			$col = 1;

			for ($i = 0; $i < count($this->container); $i++) {
				if (get_class($this->container[$i]) == "formsplitter") {
					$this->container[$i]->draw();
				} else {
					if ($col == 1)
						echo "<tr valign=\"top\">\n";						
					  $coladd = $this->container[$i]->draw();					  
					  $col = $col + $coladd;				  
					if ($col > $this->cols) {
						$col = 1;
						echo "</tr>";
					}

					
				}
			}
			echo '<tr><td colspan="'.$this->cols.'">&nbsp;</td></tr>';
			$this->draw_buttons();
			echo '<tr><td colspan="'.$this->cols.'">&nbsp;</td></tr>';
			echo "</table> "; 			
		}

		/**
		* Writes the HTML-Code for the beginning of your form. May be overwritten by specialized forms.
		* stores also the variables sid and oid as hidden input fields.
		*/
		function draw_header() {
			global $sid, $page_action, $filter_rule, $filter_column, $filter_page, $oid;			
			if ($this->name=="") $this->name="form1";
			echo '<div id="fo'.$this->name.'" style="display:block;">';
			echo "<form name=\"$this->name\" action=\"$this->action\" method=\"$this->method\"";
			//if ($this->enctype !="") {
			echo " enctype=\"multipart/form-data\"";
			//}
			echo ">";
			echo "<input type=\"hidden\" name=\"processing\" value=\"yes\">";
			echo "<input type=\"hidden\" name=\"sid\" value=\"$sid\">";

			if ($page_action == "UPDATE" || $page_action == "DELETE") {				
				echo "<input type=\"hidden\" name=\"oid\" value=\"$oid\">";
			}
		}

		/**
		* Writes the HTML-Code for the end of your form. May be overwritten by custom classes
		*/
		function draw_footer() {
			global $lang, $c;
			echo "</form>";
			echo "</div>";
			echo '<div id="waitbox" align="center" class="standardwhite" style="width:' .$this->width. 'px;display:none;">';
			echo tableStart();
			echo '<td class="standardwhite" align="center">';
			br();
			br();
			br();
			br();
			br();
			br();
			br();
			br();
			br();
			echo drawImage('move.gif');
			br();
			echo $lang->get("proc_data", "Processing Data...");
			echo "</td>";
			echo tableEnd();
			echo '</div>';			
		}

		/**
		 * Draws the HTML-Code for the top region above the form, e.g. for panels etc.
		 */
		function draw_topregion() {	}

	}
?>