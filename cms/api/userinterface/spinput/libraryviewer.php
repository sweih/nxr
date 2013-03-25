<?
	/**********************************************************************
	 *	N/X - Web Content Management System
	 *	Copyright 2002 Sven Weih & Fabian König
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
	class LibraryViewer extends WUIInterface{
		var $oid;

		var $cells;
		var $name;
		var $module_id;
		var $module_name;
		var $fkid;
		var $accesskey;

		/**
		  * Standard constructor
		  * @param $oid ID of the content-item (content.CID)
		  * @param $cells number of cells to use, standard=2
		  * @param $linkset specifies which buttons to display. Possible values: LIB, SELECT, EDIT, USAGE, LAUNCH, DELETE, COPY, combination sparated by | e.g. "LIB|SELECT", Standard: LIB
		  * @param $filter specifies which plugins to display. Specify plugin name(s) e.g. IMAGE|TEXT
		  */
		function LibraryViewer($oid, $cells = 1, $linkset = "LIB", $filter = "") {
			$this->oid = $oid;

			$this->cells = $cells;
			$this->linkset = $linkset;

			//  initializing 
			global $variation;
			$variation_temp = $variation;
			$this->module_id = getDBCell("content", "MODULE_ID", "CID = $oid");
			$this->name = getDBCell("content", "NAME", "CID = $oid");
			$this->accesskey = getDBCell("content", "ACCESSKEY", "CID = $oid");
			$this->module_name = getDBCell("modules", "MODULE_NAME", "MODULE_ID = " . $this->module_id);			
			$check = getDBCell("content_variations", "VARIATION_ID", "VARIATION_ID = $variation AND CID = $oid");
			if ($check =="") {
				$variation_temp = getDBCell("content_variations", "VARIATION_ID", "CID = $oid");
			}
			$this->fkid = getDBCell("content_variations", "FK_ID", "VARIATION_ID = $variation_temp AND CID = $oid");
		}

		function draw() {
			global $lang, $c, $sid, $aclf;

			echo '<td colspan="' . $this->cells . '">';			
			echo '<table cellpadding="0" cellspacing="0" height="140" border="0" width="190">';
			echo '<tr>';
			echo '<td width="190" height="1">' . drawSpacer(190, 1). '</td>';			
			echo '</tr>';
			echo '<tr>';
			echo '<td class="headbox">';
			echo '<table width="100%" cellpadding="0" cellspacing="0" border="0"><tr><td>';
			echo '<b>'.$this->name.'</b>&nbsp;&nbsp;&nbsp;('.$this->module_name.')';						
			echo '</td><td align="right"><b>';
			// draw Links depending on $linkset
			
			$ref = createPGNRef($this->module_id, $this->fkid);

			$preview = "";
			if ($ref != null) {
				if ($ref->management_table == "pgn_image") $ref_array = $ref->draw("ALL");			
			}
			
			// draw information on the content
			echo '</b>';			
			echo '</td></tr></table>';
			echo '</td></tr>';
			echo '<tr><td class="standardlight">';
			if (stristr($this->linkset, "LIB") || stristr($this->linkset, "EDIT")) {
				if ($aclf->checkAccessToFunction("EDIT_OBJECT") || $aclf->checkAccessToFunction("OBJECT_PROPS"))
					echo buttonLink($lang->get("edit"), $c["docroot"] . "modules/content/object_edit.php?go=update&sid=$sid&oid=" . $this->oid). "&nbsp;";
			}

			
			if (stristr($this->linkset, "LIB") || stristr($this->linkset, "LAUNCH")) {				
				if ($aclf->checkAccessToFunction("OBJECT_LAUNCH"))
					echo buttonLink($lang->get("launch", "Launch"), "?action=launch&sid=$sid&oid=" . $this->oid). "&nbsp;&nbsp;";
			}

			if (stristr($this->linkset, "LIB") || stristr($this->linkset, "DELETE")) {
				if ($aclf->checkAccessToFunction("DELETE_OBJECT"))
					echo buttonLink($lang->get("Delete"), "?action=delobject&sid=$sid&oid=" . $this->oid). "&nbsp;&nbsp;";
			}


			if (stristr($this->linkset, "SELECT")) {
				echo drawSpacer("10", "1");
				if ($aclf->checkAccessToFunction("EDIT_CL_CONTENT")) {
					global $sname;
					if ($sname != "0" && $sname!="") {	
						$jsAction="javascript:window.opener.document.getElementById('".$sname."').value='".$this->oid."';window.opener.document.getElementById('disp_".$sname."').innerHTML='".$lang->get("prev_avail", "You selected an object. A preview will be available after saving.")."';window.close();";						
					} else {
						$jsAction = "javascript:getImage('" . $ref_array["path"] . "');ok();";
					}
					echo buttonLink($lang->get("select", "Select"), "$jsAction"). "&nbsp;&nbsp;";
				}
			}			
			echo '</td></tr>';
			echo '<tr>';
			echo '<td valign="top" align="center" class="standardlight">';
			echo  $ref->preview();
			echo '</td></tr>';
			
			echo '<tr><td valign="top" class="standardlight">';		
			echo '<b>'.$lang->get("ackey", 'Access Key'). ":</b> ";
			echo "[".$this->accesskey."]";
			echo "</td>";
			echo "</tr>";			
			
			echo '<tr><td class="line">'.drawSpacer(1,1).'</td></tr>';			
			echo "</table>";
			br();
			echo "</td>";
			unset($ref);
			return 1;
		}

		function process() {
			// empty	
			}
		/** For Down-Grade-Compatibility only **/
		function proccess() { $this->process(); }

		function check() {
			// empty	
			}
	}
?>