<?
	/**********************************************************************
	 *	N/X - Web Content Management System
	 *	Copyright 2002 Sven Weih
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
	 * Draws a ACL-Info-Panel
	 * @package ACL
	 */
	class ACLInfo {
		var $acl;

		var $cols;

		/**
		 * standard constructor
		 * @param object ACLObject
		 * @param integer Number of columns to use.  	
		 */
		function ACLInfo($acl, $cols = 3) {
			$this->acl = $acl;

			$this->cols = $cols;
		}

		/**
		 * Draw the ACLInfoPanel
		 */
		function draw() {
			global $lang;

			// make sure to get the latest data.
			$this->acl->load();
			echo "<td class=\"standard\" colspan=\"" . $this->cols . "\">";
			echo '<table width="100%" cellpadding="2" cellspacing="2" border="0" class="standardlight">';
			// Parent Node
			echo '<tr><td class="standard" width="33%"><b>';
			echo $lang->get("acl_parent", "Permissions inherited from");
			echo '</b></td><td  width="66%">';
			echo $this->acl->getParentName();
			echo '</td></tr>';

			// Owner
			echo '<tr><td class="standard" width="33%"><b>';
			echo $lang->get("acl_owner");
			echo '</b></td><td  width="66%">';
			echo getAccessorName($this->acl->owner);
			echo '</td></tr>';

			// Groups & roles
			echo '<tr><td class="standard" valign="top"><b>';
			echo $lang->get("acl_groupsroles", "Foreign Access"). "<br></b>";
			echo '</td>';
			echo "<td>";

			if (!$this->acl->accessors == 0) {
				foreach ($this->acl->accessors as $key => $value) {
					echo "<b>" . getAccessorName($value["ACCESSOR"]). "</b>";

					echo " =&gt; ";
					echo getRoleName($value["ROLE"]);
					br();
				}
			}

			echo "</td></tr>";

			echo '</table>';
			echo "</td>";
			return $this->cols;
		}

		/**
		 * Check - Empty
		 */
		function check() { }

		/**
		 * Process - Empty
		  */
		function process() { }
		/** For Down-Grade-Compatibility only **/
		function proccess() { $this->process(); }
	}
?>