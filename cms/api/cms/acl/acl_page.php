<?
	/**
	 * @module ACL
	 * @package ContentManagement
	 */

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
	 * ACL-Object for pages.
	 */
	class ACL_PAGE extends ACLObject {

		/**
		   * Standard constructor
		   * @param integer GUID of the pages
		   */
		function ACL_PAGE($guid) { 
		  ACLObject::ACLObject($guid); 
		  $this->typeId = "1";
		}

		/**
		* Get the Parent ACL-GUID of the object. Used for inherited permissions
		* MUST BE overwritten!!
		*/
		function getParentACLGUID($guid) {
			$res = $this->getParentNode($guid);

			if ($res == "" || $res == 0)
				return 1;

			return $res;
		}


		/**
		 * Get the name of the parent object if ACL inheritance...
		 */
		function getParentName() {
			if ($this->parentACL == "" || $this->parentACL == "1" || $this->parentACL == "0")
				return "Website&gt;";
			
			return SPPathToRoot($this->parentACL);
		}

		/**
		 * help function
		 * gives parent page or root for a menu_id
		 * @param integer menuId
		 */
		function getParentNode($oid) {
			$parent = getDBCell("sitemap", "PARENT_ID", "MENU_ID = $oid");
			if ($parent == "0" || $parent=="")
				return 0;
			return $parent;
		}
	}
?>