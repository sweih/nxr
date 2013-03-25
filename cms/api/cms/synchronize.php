<?php

/**********************************************************************
 *	N/X - Web Content Management System
 *	Copyright 2004 Sven Weih
 *
 *      $Id: synchronize.php,v 1.14 2004/05/06 20:05:03 sven_weih Exp $ *
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
	 * syncronize variations with entered data to the database.
	 * The configuration for this function must be set manually.
	 * I.E. there must be the $oid-Variable set and there must(!)
	 * be also the global vars content_variations_VARIATION_ID_XX
	 * and content_MODULE_ID
	 * set which are automatically set by the SelectMultiple2Input.
	 */
	function syncVariations() {
		global $db, $oid, $content_MODULE_ID;

		$module = value("content_MODULE_ID", "NUMERIC");
		if ($module == "0") $module = $content_MODULE_ID;
		includePGNSource ($module);

		//delete all variations first.
		$del = "UPDATE content_variations SET DELETED=1 WHERE CID = $oid";

		$query = new query($db, $del);

		// get list of variations
		$variations = createNameValueArray("variations", "NAME", "VARIATION_ID", "DELETED=0");

		for ($i = 0; $i < count($variations); $i++) {
			$id = $variations[$i][1];

			if (value("content_variations_VARIATION_ID_" . $id) != "0") {
				// create or restore variation
				// check, if variations already exists and is set to deleted.
				$sql = "SELECT COUNT(CID) AS ANZ FROM content_variations WHERE CID = $oid AND VARIATION_ID = $id";

				$query = new query($db, $sql);
				$query->getrow();
				$amount = $query->field("ANZ");

				if ($amount > 0) {
					$sql = "UPDATE content_variations SET DELETED=0 WHERE CID = $oid AND VARIATION_ID = $id";
				} else {
					$fk = nextGUID();
					$sql = "INSERT INTO content_variations (CID, VARIATION_ID, FK_ID, DELETED) VALUES ( $oid, $id, $fk, 0)";
                                        $PGNRef = createPGNRef($module, $fk);
                                        $PGNRef->sync();
				}

				$query = new query($db, $sql);
			}
		}
	}




?>