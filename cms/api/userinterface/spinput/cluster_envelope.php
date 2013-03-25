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
	 * Container for displaying a Cluster-Link in a cluster. The trick is,
	 * that also Template-Items with more than one instances are supported.
	 * @package ContentManagement
	 */
	class ClusterEnvelope extends AbstractEnvelope {
				
           		
	/**
	 * Draw the input boxes needed for editing the contents in the envelope.
	 * @param integer id of cluster_content.CLCID
	 */
	function getSingleEdit($id) {
		global $specialID, $lang, $c, $sid;
		if ($this->editState && $this->editor) {
			$specialID = $id;
			$this->add(new CLSelector($lang->get("select_cl"), "cluster_content", "FKID", "CLCID=$id", getModuleFromCLC($id), "", ""));
			$specialID = "";		
		} else {
			$name = "&lt;" . $lang->get("not_selected", "No item selected yet."). "&gt;";
			$myfk = getDBCell("cluster_content", "FKID", "CLCID = " . $id);
			if ($myfk != 0 && $myfk != "") {				
				$clt = getDBCell("cluster_node", "CLT_ID", "CLNID = " . $myfk);
				$cat = getDBCell("cluster_templates", "CATEGORY_ID", "CLT_ID = " . $clt);
				$name = "<br><b>" . $lang->get("cli"). "</b> " . getDBCell("cluster_node", "NAME", "CLNID = " . $myfk);
				$buttons =  "<br><a href=\"" . $c["docroot"] . "modules/cluster/clusterbrowser.php?sid=$sid&action=editobject&go=update&oid=$myfk&pnode=$cat&clt=$clt\" class=\"box\">goto cluster</a>";
			}
			$this->add(new Label("lbl", $name, "standardwhite", 2));
			$this->add(new AlignedLabel('lbl', $buttons, 'right', '', 1));
		}

	}
}
?>