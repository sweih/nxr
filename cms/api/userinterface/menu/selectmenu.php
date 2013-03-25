<?
	/**********************************************************************
	 *	N/X - Web Content Management System
	 *	Copyright 2002-2004 Sven Weih, FZI Research Center for Information Technologies
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
	* Draws a menubox for selecting a certain filtervalue (row in table)
	* The selected Value can be found in variable $selected
	* @package WebUserInterface
	*/
	class SelectMenu extends StdMenu {

        var $selected = 0;
        var $identifier="";
        var $table = "";
        var $namecol = "";
        var $valuecol = "";
        var $cond = "";

		/**
		* standard constructor
  	    * @param string Text displayed in the title-bar of the filter box
		* @param string Identifier for using several such boxes.
        * @param string table where the values are
        * @param string Name of the Column for the names to display
        * @param string Name of the Column for the values that correspond to the names
        * @param string Filter-Condition for filtering values
		*/
		function SelectMenu($title, $identifier, $table, $namecol, $valuecol, $cond) {
			StdMenu::StdMenu($title);
            		$this->identifier = $identifier;
            		$this->table = $table;
            		$this->namecol = $namecol;
            		$this->valuecol = $valuecol;
            		$this->cond = $cond;
            		$this->formname = "f".$identifier;
            		$this->selected = initValue($identifier, $identifier, "0");
		}



		/**
		 * writes HTML for the Selector
		 */
		 function draw_selector() {
		   	global $lang, $statsinfo;
           	$values = createNameValueArray($this->table, $this->namecol, $this->valuecol, $this->cond);		   
		   	echo "<tr><td>";
			echo "<table width=\"185\" cellpadding=\"3\" cellspacing=\"3\" class=\"sidebar_menubox\"><tr><td>";
			echo "<table width='100%' cellpadding='3' cellspacing='0' border='0'>";
			echo "<tr>";
			$dropdown = new Dropdown($this->identifier, $values, 'standardwhite', $this->selected, 100, 1);
			$dropdown->draw();
			echo "</td><td>";
			$lbi = new ButtonInline("changetop", $lang->get('change', 'Change'), 'navelement', 'submit', '', 'f'.$this->identifier);
			echo $lbi->draw();
			echo '<input type="hidden" name="changetop" value="">';
			echo "</td></tr>";
			echo "</tr></table>";
			echo "</td></tr></table>";
			echo "</td></tr>";
		 }

		/**
		 * writes HTML for Menu.
		 */
		function draw_contents() {
			$this->draw_selector();
		}

        /**
         * Do not draw OID
         */
        function drawOID() {}


	}
?>