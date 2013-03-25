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
	 * Provides all functions for drawing the cms menu. Does
	 * the menu, the user has priviledges for. Links to pages, a user is not
	 * allowed to access, because he does not have the necessary role
	 * are not displayed. 
	 * @package Layout
	 */
	class cmsmenu {

		var $tabbar;
		var $lastmenu;

		/**
		 * standard constructor.
		 */
		function cmsmenu() {
			global $sid;
			$this->tabbar = new TabBar();
			$this->tabbar->postfix = 'sid=' . $sid;
		}

		/**
		* Draw the menu.
		*/
		function draw() {
		  echo $this->tabbar->draw();	
		}

		/**
		 * Draws the mainmenubuttons. Do not call directly. Use the
		 * class draw() method.
		 */
	
		/**
		* Adds a new Mainmenu. after the addMenu all the addSubmenues
		* will be child of this Mainmenu until the next addMenu method is
		* called.
		* @param string filename of the tab-image
		* @param string roles, a user must have. One is enough.
		* @param boolean flag, if the user must be in system group or not
		*/
		function addMenu($graphic, $roles, $sys = true) {
			global $auth;
			if ($auth->checkPermission($roles, $sys)) {
				$this->lastmenu = $this->tabbar->addMainTab($graphic);
			} 			
		}

		/**
		 * Adds a new Submenu categorized by the previous Menu to the cmsmenu tree.
		 * @param string Text, that will be displayed in the browser.
		 * @param string Link to the page, you want to display when this menu gets active.
		 * @param string roles, a user must have. One is enough.
		 * @param boolean flag, if the user must be in system group or not
		 */
		function addSubmenu($title, $link, $roles, $icon = "none", $sys = true) {
			global $auth, $c;
			if ($auth->checkPermission($roles, $sys)) {
				$this->tabbar->addSubTab($this->lastmenu, $title, $c["docroot"] . $link);
			}			
		}
	}
?>