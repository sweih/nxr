<?
	/**********************************************************************
	 *	N/X - Web Content Management System
	 *	Copyright 2003 Sven Weih, 
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
	 * Container for displaying a Cluster-Link in a cluster. The trick is,
	 * that also Template-Items with more than one instances are supported.
	 * @package ContentManagement
	 */
	class LibraryEnvelope extends AbstractEnvelope{

        /**
		 * Draw the input boxes needed for editing the contents in the envelope.
		 * @param integer id of cluster_content.CLCID
		 */
		function getSingleEdit($id) {
			global $specialID;
			if ($this->editState && $this->editor) {
				$viewOnly = false;
			} else {
				$viewOnly = true;	
			}
			$specialID = $id;
			$filterId = getDBCell("cluster_template_items", "FKID", "CLTI_ID = ".$this->clti);
			$filter = getDBCell("modules", "MODULE_NAME", "MODULE_ID = ".$filterId);
			$this->add(new LibrarySelect("cluster_content", "FKID", "CLCID=$id", $filter, 2, $viewOnly));
			$specialID = "";
			
		}
	}
?>