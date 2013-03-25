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
	 * Container, which is used for displaying the content of plugins in the
	 * new Library.
	 * @package ContentManagement
	 */
	class MetaDataEditor extends Container {
		var $oid;

		var $template_id;

		/**
		  * Standard constructor
		  * @param $oid ID of the content-item (content.CID)
		  */
		function MetaDataEditor($oid, $template_id, $cells = 2) {
			$this->oid = $oid;

			$this->template_id = $template_id;
			$this->cells = $cells;

			$this->prepare();
		}

		function prepare() {
			global $specialID, $lang, $db;

			syncMetas($this->oid, "OBJECT");

			//checking, if there are any items in the template.
			$sql = "SELECT COUNT(MTI_ID) AS ANZ FROM meta_template_items WHERE MT_ID =" . $this->template_id;

			$query = new query($db, $sql);
			$query->getrow();
			$amount = $query->field("ANZ");

			if ($amount > 0) {
				$this->add(new Subtitle("st", $lang->get("edit_meta", "Edit Meta-Data")));

				$sql = "SELECT m.MID AS D1, t.MTYPE_ID AS D2, t.NAME AS D3 FROM meta_template_items t, meta m WHERE m.MTI_ID = t.MTI_ID AND m.CID = " . $this->oid . " AND m.DELETED=0 AND t.MT_ID = " . $this->template_id . " ORDER BY t.POSITION ASC";

				$query = new query($db, $sql);
				$mlist = null;
				$counter = 0;

				while ($query->getrow()) {
					// save the list, so that it will not go lost.
					$mlist[$counter][0] = $query->field("D1");

					$mlist[$counter][1] = $query->field("D2");
					$mlist[$counter][2] = $query->field("D3");
					$counter++;
				}

				// add the metainput fields.
				for ($i = 0; $i < $counter; $i++) {
					$specialID = $mlist[$i][0];

					// dispatch type.
					switch ($mlist[$i][1]) {
						case 1:
							$obj[$i] = new TextInput($mlist[$i][2], "meta", "VALUE", "MID = " . $mlist[$i][0], "type:text,size:64,width:300");

							break;

						case 2:
							$obj[$i] = new TextInput($mlist[$i][2], "meta", "VALUE", "MID = " . $mlist[$i][0], "type:textarea,size:3,width:300");

							break;

						case 3:
							$obj[$i] = new TextInput($mlist[$i][2], "meta", "VALUE", "MID = " . $mlist[$i][0], "type:color,param:form1");

							break;
					}

					if (isset($obj[$i]))
						$this->add($obj[$i]);
				}

				$specialID = "";
			}

			return $this->cells;
		}
	}
?>