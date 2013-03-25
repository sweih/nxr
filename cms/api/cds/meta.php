<?
	/**
	 * @package CDS
	 */

	/**********************************************************************
	 *	N/X - Web Content Management System
	 *	Copyright 2002 Sven Weih and Fabian Koenig
	 *
	 *	This file is part of N/X.
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
	  * Use this class to access the META-Data
	  * Access this class with $cds->meta
	  */
	 class Meta extends CDSInterface {
		function Meta(&$parent) { CDSInterface::CDSInterface($parent); }

		/**
		 * Retrieves the meta-data of an object.
		 * @param 		string	Name of the META-Field to retrieve.
		 * @param		string	optional. Name of a static content-item to get metas from.
		 * @returns	string 	Content of the META-Template.
		 */
		function get($name, $contentItem = "") {
			$result = ""; // save result here later.
	
			if ($contentItem == "") {
				// get mti-id.
				$clt = getDBCell("cluster_node", "CLT_ID", "CLNID = ".$this->pageClusterNodeId);
				$mt = getDBCell("cluster_templates", "MT_ID", "CLT_ID = $clt");
				$mti = getDBCell("meta_template_items", "MTI_ID", "MT_ID = $mt AND UPPER(NAME) = UPPER('$name')");
				if ($mti == "")
					return "";

				$result = getDBCell("meta", "VALUE", "MTI_ID = $mti AND CID = ".$this->pageClusterNodeId);
			} else {
				// get the mti-id
				$clt = getDBCell("cluster_node", "CLT_ID", "CLNID = ".$this->pageClusterNodeId);
				$cid = getDBCell("cluster_template_items", "FKID", "CLT_ID = $clt AND UPPER(NAME) = UPPER('$contentItem') AND CLTITYPE_ID = 1");

				if ($cid == "")
					return "";

				$mt = getDBCell("content", "MT_ID", "CID = $cid");
				$mti = getDBCell("meta_template_items", "MTI_ID", "MT_ID = $mt AND UPPER(NAME) = UPPER('$name')");

				if ($mti == "")
					return "";

				$result = getDBCell("meta", "VALUE", "MTI_ID = $mti AND CID = ".$this->pageClusterNodeId);
			}

			return $result;
		}
		
		/**
		 * Return an array containing all Meta-Field-Names of this cluster
		 */
		function getFieldnames() {
			$clt = getDBCell("cluster_node", "CLT_ID", "CLNID = ".$this->pageClusterNodeId);
			$mt = getDBCell("cluster_templates", "MT_ID", "CLT_ID = $clt");
			return createDBCArray("meta_template_items", "NAME", "MT_ID = $mt", "ORDER BY POSITION ASC");
		}
	}
?>