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
	* Draws a menubox an adds a filterinput to the system for direct
	* text input
	* @package WebUserInterface
	*/
	class StdMenu {
		var $title;

		var $menuentries = null;
		var $counter = 0;
		var $headline;
		var $width = 190;
		var $tipp = "";
        var $formname = "filter";
		/**
		* standard constructor
		 * @param string Text displayed in the title-bar of the filter box
		*/
		function StdMenu($title) {
			global $c_theme;

			$this->title = $title;
			$this->headline = $title;
			$this->width = $c_theme["stdmenu"]["width"];
		}

		/**
		* Adds a link to the menu. The link ist static here. Parameters oid, sid, go
		* will be automatically added for page processing. Use this function
		* to create links to page with same Filtermenu. Link is only displayed,
		* if oid is not null.
		*
		* @param string text, that will appear as link
		* @param string page, that will be called when link is active
		*/
		function addLink($label, $href) {
			global $sid, $oid, $go, $filter_rule, $filter_column, $filter_page;

			$this->menuentries[$this->counter][0] = "<nobr>".drawSpacer(10,1).clrSPC($label)."</nobr>";
			$this->menuentries[$this->counter][1] = $href . "?go=update&sid=$sid&filter_page=$filter_page&filter_column=$filter_column&filter_rule=$filter_rule";
			$this->menuentries[$this->counter][2] = 1; // 1 says, that link is only displayed when oid is not null.
			$this->counter++;
		}

		/**
		* Adds a link to the menu. The link ist static here. NO parameters
		* will be automatically added! Use this Function, to create Links with
		* new Filtermenues.
		*
		* @param string text, that will appear as link
		* @param string page, that will be called when link is active
		* @param string page, that will cause highlight on this menu also.
		*/
		function addMenuEntry($label, $href, $highlightmenu="xplqkjnxe", $function="") {
			global $sid, $auth;

            if (($function == "") || ( $auth->checkAccessToFunction($function))) {
                $this->menuentries[$this->counter][0] = "<nobr>".drawSpacer(10,1).clrSPC($label)."</nobr>";
                if (!stristr($href, "?"))
		  		    $href .= "?sid=$sid";

  			    $this->menuentries[$this->counter][1] = $href;
  			    $this->menuentries[$this->counter][2] = 0;
			    $this->menuentries[$this->counter][3] = $highlightmenu;
			    $this->counter++;
            }
		}

		/**
		 * Adds a link to the menu. The link is only displayed, if an oid is existent!
		 * @param string text, that will appear as link
		 * @param string page, that will be called when link is active
		 * @param string page, that will cause highlight on this menu also.
		 */
		function addContextSensitiveLink($label, $href, $highlightmenu="xplqkjnxe") {
			global $sid, $oid, $go;

			$this->menuentries[$this->counter][0] = $label;
			$this->menuentries[$this->counter][1] = $href . "?go=update&sid=$sid";
			$this->menuentries[$this->counter][2] = 2; //2 says only for oid not null and sets special style.
			$this->menuentries[$this->counter][3] = $highlightmenu;
			$this->counter++;
		}

		/**
		 * process the form-menu. empty.
		 */
		function process() {
			//empty, nothing to validate.
			}
		/** For Down-Grade-Compatibility only **/
		function proccess() { $this->process(); }

		/**
		 * writes HTML for Menu.
		 */
		function draw() {
			echo getFormHeadline($this->title);
			$this->draw_header();
			$this->draw_contents();
			$this->draw_footer();
		}

		/**
			* internal. draw contents of your form.
		 */
		function draw_contents() {			
            $this->draw_menu();
         }

		/**
		 * internal. write links.
		 */
		function draw_menu() {
			global $oid;

			if ($this->counter > 0) {
				echo "<table width='100%' cellpadding='0' cellspacing='0' border='0'>";
				for ($i = 0; $i < $this->counter; $i++) {
					echo "<tr><td class=\"toolbar\">";
					if ((($this->menuentries[$i][2] == 1 || $this->menuentries[$i][2] == 2) && $oid != "") || $this->menuentries[$i][2] == 0) {
						if ($this->menuentries[$i][2] > 0)
							$this->menuentries[$i][1] .= "&oid=$oid";
						if ((stristr($this->menuentries[$i][1], doc()) || stristr($this->menuentries[$i][3], doc())) &&  $this->menuentries[$i][3] != "NO") {
							echo "<li><a style=\"color:red;\" href=\"" . $this->menuentries[$i][1] . "\">" . $this->menuentries[$i][0] . "</a></li>";				
						} else {
							echo "<li><a href=\"" . $this->menuentries[$i][1] . "\">" . $this->menuentries[$i][0] . "</a></li>";				
						}
						
					}
					echo "</td></tr>";
				}							
				echo "</table>";
				echo "<br>";
			}
		}

		/**
		 * internal. write menu header.
		 */
		function draw_header() {
			global $sid, $oid, $go, $page_action, $c_theme, $lang;
			
			$sid = value("sid");
			if (!isset($oid))
			  $oid = value("oid");

			$temp = explode("?", $_SERVER["REQUEST_URI"]);
			$action = $temp[0];
			echo "<table width=\"" . ($this->width + 8) . "\" cellpadding=\"0\" cellspacing=\"0\" border=\"0\">\n";	
			echo "<form name=\"".$this->formname."\" action=\"$action\" method=\"POST\">";
            echo "<input type=\"hidden\" name=\"sid\" value=\"$sid\">\n";
            $this->drawOID();
		}


        /**
         * Draw the OID-Hidden-Field.
         */
        function drawOID() {
            global $go, $oid, $page_action;

            if ($go != "0") {
                if ($go != "DELETE" && $go != "Create")
                    echo "<input type=\"hidden\" name=\"go\" value=\"$go\">\n";
            }

            if (($page_action == "UPDATE" || $page_action == "DELETE") && $oid !="0") {
                 echo "<input type=\"hidden\" name=\"oid\" value=\"$oid\">";
            }
        }

		/**
		 * internal. write menu footer.
		 */
		function draw_footer() { echo "</form>";
			//echo "</td></tr></table>\n";
		}


	}
?>