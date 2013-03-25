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
	 * Provides all functions for drawing the cms menu. Does
	 * the menu, the user has priviledges for. Links to pages, a user is not
	 * allowed to access, because he does not have the necessary role
	 * are not displayed. 
	 * @package Layout
	 */
	class cmsmenu {
		var $menucontainer = null;

		var $submenucontainer = null;
		var $globalcontainer = null;
		var $selectedMenu = 0;
		var $selectedSubmene = 0;
		var $menucounter = 0;
		var $submenucounter = 0;
		var $globalcounter = 0;

		/**
		 * standard constructor.
		 */
		function cmsmenu() {
			global $mid, $auth;

			$temp_menu = "";

			if (!isset($mid)) {
				$temp_menu = getVar("menu");
			} else {
				$temp_menu = $mid;
			}

			if ($temp_menu != "") {
				//process the menu id (mid) now.
				// it has Form: <menu>x<submenu>
				$msplit = explode("x", $temp_menu);

				$this->selectedMenu = $msplit[0];
				$this->selectedSubmenu = $msplit[1];
			}

			pushVar("menu", $temp_menu);
		}

		/**
		* Draw the menu.
		*/
		function draw() { $this->draw_mainmenu(); }

		/**
		 * Draws the mainmenubuttons. Do not call directly. Use the
		 * class draw() method.
		 */
		function draw_mainmenu() {
			global $sid, $auth, $c_languages, $c, $c_theme;

			echo '<table width="100%" cellpadding="0" cellspacing="0" border="0">';
			echo '<tr><td valign="middle" align="center" class="wcopy"><br><br>[Logo]<br><br><br></td></tr>';

			for ($i = 1; $i <= count($this->menucontainer); $i++) {
				if ($auth->checkPermission($this->menucontainer[$i][2], $this->menucontainer[$i][3])) {

					// draw submenu?
					if ($i == $this->selectedMenu) {
						//draw text
						echo '<tr><td valign="middle" align="left" class="embedded">';

						echo '<a href="menu.php?sid=' . $sid . '&mid=' . $i . 'x0" class="cmsmenu_menu">' . drawImage("down.gif"). "&nbsp;&nbsp;&nbsp;" . $this->menucontainer[$i][0] . '</a>';
						echo '</td></tr>';

						//spacer
						echo '<tr><td valign="middle" align="center">';
						echo drawSpacer(8, 8);
						echo '</td></tr>';

						echo '<tr><td valign="middle" align="center">';
						$this->draw_submenu();
						echo '</td></tr>';
					} else {
						//draw text
						echo '<tr><td valign="middle" align="left" class="embedded">';

						echo '<a href="menu.php?sid=' . $sid . '&mid=' . $i . 'x0" class="cmsmenu_menu">' . drawImage("right.gif"). "&nbsp;&nbsp;&nbsp;" . $this->menucontainer[$i][0] . '</a>';
						echo '</td></tr>';
					}

					//spacer
					echo '<tr><td valign="middle" align="center">';
					echo drawSpacer(2, 2);
					echo '</td></tr>';
				}
			}

			echo '</table>';
		}

		/**
		 * Draws the submenu. Do not call directly. Use the
		 * class draw() method.
		 */
		function draw_submenu() {
			global $sid, $auth;

			$sm = $this->selectedMenu;
			$submenues = count($this->submenucontainer[$sm]);
			//create container for menu-entires
			echo '<table border="0" cellpadding="2" cellspacing="0" width="100%">';

			for ($i = 1; $i <= $submenues; $i++) {
				if ($auth->checkPermission($this->submenucontainer[$sm][$i][2], $this->submenucontainer[$sm][$i][3])) {
					// draw icon
					echo '<tr><td valign="middle" align="center">';

					echo '<a href="' . $this->submenucontainer[$sm][$i][1] . '?sid=' . $sid . '" target="contentset" class="cmsmenu_submenu">';
					echo '<img src="' . $c["docroot"] . "img/icons/" . $this->submenucontainer[$sm][$i][4] . '" border="0">';
					echo '</a>';
					echo '</td></tr>';
					//draw text
					echo '<tr><td valign="middle" align="center">';
					echo '<a href="' . $this->submenucontainer[$sm][$i][1] . '?sid=' . $sid . '" target="contentset" class="cmsmenu_submenu">' . $this->submenucontainer[$sm][$i][0] . '</a>';
					echo '</td></tr>';
					//spacer
					echo '<tr><td valign="middle" align="center">';
					echo drawSpacer(10, 10);
					echo '</td></tr>';
				}
			}

			echo '</table>';
		}

		/**
		* Adds a new Mainmenu. after the addMenu all the addSubmenues
		* will be child of this Mainmenu until the next addMenu method is
		* called.
		* @param string filename of the tab-image
		* @param string roles, a user must have. One is enough.
		* @param boolean flag, if the user must be in system group or not
		*/
		function addMenu($graphic, $roles, $sys = true) {
			$this->menucounter++;

			$this->submenucounter = 0;
			$this->menucontainer[$this->menucounter][0] = $graphic;
			$this->menucontainer[$this->menucounter][2] = $roles;
			$this->menucontainer[$this->menucounter][3] = $sys;
		}

		/**
		 * Adds a new Submenu categorized by the previous Menu to the cmsmenu tree.
		 * @param string Text, that will be displayed in the browser.
		 * @param string Link to the page, you want to display when this menu gets active.
		 * @param string roles, a user must have. One is enough.
		 * @param boolean flag, if the user must be in system group or not
		 */
		function addSubmenu($title, $link, $roles, $icon = "none", $sys = true) {
			$this->submenucounter++;

			$this->submenucontainer[$this->menucounter][$this->submenucounter][0] = $title;
			$this->submenucontainer[$this->menucounter][$this->submenucounter][1] = $link;
			$this->submenucontainer[$this->menucounter][$this->submenucounter][2] = $roles;
			$this->submenucontainer[$this->menucounter][$this->submenucounter][3] = $sys;
			$this->submenucontainer[$this->menucounter][$this->submenucounter][4] = "mi_" . strtolower($icon). ".gif";
		}

		/**
		 * Adds a new global menu to the cmsmenu tree. Global menues are always visible.
		 * E.g. used for Logout-Link.
		 * @param string Text, that will be displayed in the browser.
		 * @param string Link to the page, you want to display when this menu gets active.
		 * @param string roles, a user must have. One is enough.
		 */
		function addGlobalmenu($title, $links, $roles) {
			$this->globalcounter++;

			$this->globalcontainer[$this->globalcounter][0] = $title;
			$this->globalcontainer[$this->globalcounter][1] = $links;
			$this->globalcontainer[$this->globalcounter][2] = $roles;
		}
	}
?>