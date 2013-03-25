<?
	/**********************************************************************
	 *	N/X - Web Content Management System
	 *	Copyright 2002 Sven Weih and Fabian Koenig
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
	 * Form with different switchable views.
	 * @package WebUserInterface
	 */
	class PanelForm extends Form {
		var $panels = 1;
		var $panelcontainer = null;
		var $view = 0;
		var $quickpanel = false;
		var $id;
		
		/**
		  * standard constructor
		 * @param string $headline Headline, to be displayed in the form
		 * @param string $icon Icon, to be displayed in the form. Name of the file, stored in images folder.
		 * @param string $name Name of the form. Will be used in HTML.
		 * @param string $action Next script, that shall be called when submitting. Empty if self.
		 * @param string $method POST or GET for sending the form. POST is default and strongly recommended
		 * @param string $enctype Specify the enctype for the form. Use only when submitting files as multipart.
		 * @param boolean $quickpanel Specify whether to handle each panel separately (false = default) or draw all panels at one and switch between them using JavaScript (true)
		 */
		function PanelForm($headline, $icon = "", $id = "form1", $action = "", $method = "POST", $enctype = "", $quickpanel = false) {
			global $page;

			Form::Form($headline, $icon, "form1", $action, $method, $enctype);
			$this->quickpanel = $quickpanel;
			$this->headline = $headline;
			$this->id = $id;
			// panel processing
			$temp = explode("?", $_SERVER["REQUEST_URI"]);
			$temp2 = explode("/", $temp[0]);
			$prefix = $temp2[count($temp2) - 1];

			

			$this->view = initValue("view", doc()."view", 1);

			if ($this->view == "")
				$this->view = "1";

			if ($this->view == "0")
				$this->view = "1";

			// Include Java-Script
			$page->setJS("PANELFORM");
		}

		/**
		  * Add a new Panel to the form
		  * @param $panel Panel references a panel
		  */
		function addPanel(&$panel) {
			$panel->quickpanel = $this->quickpanel;
			$this->panelcontainer[$this->panels] = $panel;
			$this->panels++;			
		}

		/**
		 * Process the Panel-form!
		 */
		function process() {
			global $errors, $db, $lang;
			if ($errors == "") {
				if ($this->quickpanel) {
					for ($kti = 1; $kti < $this->panels; $kti++) {
						$this->panelcontainer[$kti]->process();
					}
					
					if ($errors == "" && (value("action") == $lang->get("back") || value("action") == $lang->get("save_back"))) {
						$db->close();
						header ("Location: " . $this->backto);
					exit;
					}
				} else {
					if (count($this->panelcontainer) >= $this->view)
				  	$this->panelcontainer[$this->view]->process();
				}								
			}
		}

		/**
		  * Draw the form
		  */
		function draw() {
			$this->draw_header();
			$this->draw_body();
			$this->draw_footer();
		}

		/**
		* Writes the HTML-Code for the body of your Form. Calls the draw functions of the
		* WUIObjects you put in the form-container.
		*/
		function draw_body() {
			global $c, $c_theme, $addLink;
			echo '<table width="'.$this->width.'" border="0" cellpadding="0" cellspacing="0"><tr><td>';
			$this->draw_topregion();
			echo getFormHeadline($this->headline,'100%', $this->headerlink);	
			$this->draw_contents();
			echo '</td></tr></table>';
		}

		/**
			* Writes the HTML-Code for the contents inside the form
		 */
		function draw_contents() {
			if ($this->quickpanel) {
				for ($ita = 1; $ita < $this->panels; $ita++) {
					if ($ita == 1) {
						echo "<div id=\"".$this->id."pmid" . $ita . "Body\" name=\"".$this->id."pmid" . $ita . "Body\" class=\"ListNuggetBody\">";
					} else {
						echo "<div id=\"".$this->id."pmid" . $ita . "Body\" name=\"".$this->id."pmid" . $ita . "Body\" class=\"ListNuggetBody\" style=\"display:none\">";
					}

					$this->panelcontainer[$ita]->draw();
					br();
					echo getFormFooterLine();
					echo "</div>";
				}
			} else {
				$this->panelcontainer[$this->view]->draw();
				br();
				echo getFormFooterLine();
			}
		}

		/**
			* Writes the HTML-Code for the panels above the form
		 */
		function draw_topregion() {
			global $sid, $oid, $c, $c_theme, $addLink;

			if (!$this->quickpanel) {
				$tabbar = new TabBar('panelmenu', $this->id.'pmid');
			} else  {				
				$tabbar = new TabBar('quickpanel', $this->id.'pmid');
				$tabbar->selectedMenu = 1;
			}

			if ($this->quickpanel) {
				for ($i = 1; $i < $this->panels; $i++) {
					$tabbar->addMainTab($this->panelcontainer[$i]->title, "javascript:PartWrapperSwitch('".$this->id."pmid', $i, $this->panels);");
					
				}
			} else {
				for ($i = 1; $i < $this->panels; $i++) {
					
					$tabbar->addMainTab($this->panelcontainer[$i]->title, $this->action . "?sid=$sid&oid=$oid&view=$i" . $addLink);				
				}
			}
			echo $tabbar->draw();
		}

		/**
		* Writes the HTML-Code for the beginning of the form
		* stores also the variables sid and oid as hidden input fields.
		*/
		function draw_header() {
			global $sid, $page_action, $filter_rule, $filter_column, $filter_page;
			echo '<div id="fo'.$this->name.'" style="display:block;">';
			echo "<form name=\"form1\" action=\"$this->action\" method=\"$this->method\"";
			echo " enctype=\"multipart/form-data\">";
			echo "<input type=\"hidden\" name=\"sid\" value=\"$sid\">";
			$oid = value("oid", "NUMERIC");
			echo "<input type=\"hidden\" name=\"oid\" value=\"$oid\">";
		}
		
	}

?>