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
	 * Menu for browsing the Sitemap.
	 * Highly customized!
	 * @package WebUserInterface
	 */
	class SitemapMenu extends Form {
		var $title;

		var $mid;
		var $action;
		var $obj = null;
		/**
		 * Standard constructor
		 * @param string Title of the Browser
		 */
		function SitemapMenu($title) {
			global $c_theme;

			$this->title = $title;
			$this->headline = $title;
			$this->obj = &$obj;

			// init other variables...
			if (isset($_GET["mid"]))
				pushVar("mid", value("mid", "NUMERIC"));

			$this->mid = getVar("mid");

			if ($this->mid == "")
				$this->mid = 0;

			$temp = explode("?", $_SERVER["REQUEST_URI"]);
			$this->action = $temp[0];

			$this->width = $c_theme["sitemap_menu"]["width"];
		}

		/**
		  * Draw the Browser-Window
		  */
		function draw() {
			$this->draw_header();

			$this->draw_body();
			// draw instance browser here
			$this->draw_footer();
		}

		function draw_contents() { $this->draw_menu(); }

		/**
		 * internal. draw the directory tree.
		 */
		function draw_menu() {
			global $sid, $lang, $db, $auth, $c_theme;

			$oid = value("oid", "NUMERIC");
			$developer = $auth->checkPermission("DEVELOPER|ADMINISTRATOR");
			$qualityman = $auth->checkPermission("QUALITY MANAGER|DEVELOPER|ADMINISTRATOR");
			$vario = variation();
			$vname = getDBCell("variations", "NAME", "VARIATION_ID = $vario");

			echo "<table width=\"" . $this->width . "\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" class=\"design\">\n";
			// draw quick-jump panel
			$sep = new Separator("form_separator", 1, $this->width, $c_theme["form"]["separator"]["height"]);
			echo "<tr><td class=\"current_settings_display\"><b>" . $lang->get("active_variation"). "</b> $vname</td></tr>";
			echo "<tr>";
			$sep->draw();
			echo "</tr>";
			echo "<tr><td class=\"informationheader\">";
			echo "<form method=\"GET\">";
			echo "PageID:<input type=\"text\" size=\"6\" name=\"oid\">";
			echo "<input type=\"hidden\" name=\"go\" value=\"update\">";
			echo "<input type=\"hidden\" name=\"sid\" value=\"$sid\">";
			echo "<input type=\"submit\" name=\"jump\" value=\"Go\">";
			echo "</form></td></tr>"; // 

			echo "<tr>\n";
			$sep->draw();
			echo "</tr>\n";

			// draw the header
			$drawTree = "";
			$searchNode = $this->mid;

			while ($searchNode != 0) {
				$title = getDBCell("sitepage_master m, sitemap s", "NAME", "s.VERSION=0 AND s.DELETED = 0 AND m.SPM_ID = s.SPM_ID AND s.MENU_ID = " . $searchNode);

				$drawTree = "&gt;&nbsp;<a href=\"" . $this->action . "?sid=$sid&mid=$searchNode\" class=\"menu\">$title</a>&nbsp;" . $drawTree;
				$searchNode = getDBCell("sitemap", "PARENT_ID", "DELETED = 0 AND MENU_ID = " . $searchNode);
			}

			$drawTree = "<b>:&nbsp;<a href=\"" . $this->action . "?sid=$sid&mid=0\" class=\"menu\">" . $lang->get("r_home"). "</a></b>&nbsp;" . $drawTree;
			echo "<tr><td class=\"current_tree_display\">$drawTree</td></tr>"; // draw the tree.
			echo "<tr>";
			$sep->draw();
			echo "</tr>";

			// draw instance list.
			$sql = "SELECT m.SPMTYPE_ID FROM sitemap s, sitepage_master m WHERE s.MENU_ID = $this->mid AND s.SPM_ID = m.SPM_ID AND s.VERSION=0";

			$query = new query($db, $sql);
			$query->getrow();
			$mytype = $query->field("SPMTYPE_ID");
			$query->free();

			//-- begin multipage handling --
			if ($mytype == 2) { //Multiinstance-page
				echo "<tr><td class=\"informationheader\">";

				echo "<table width=\"150px\" cellpadding=\"0\" cellspacing=\"0\" class=\"white\">";
				// drawing grid with 18px-80px-18px-18px-18px
				echo "<tr>";
				$cell18 = new Cell("spc1", "embedded", 1, 16);
				$cell18->draw();
				$cell80 = new Cell("spc2", "embedded", 1, 100);
				$cell80->draw();
				$cell18->draw();
				$cell18->draw();
				$cell18->draw();
				echo "</tr>";

				echo "<tr><td valign=\"top\" class=\"white\">&nbsp;</td>";
				echo "<td class=\"white\"><a href=\"" . $this->action . "?sid=$sid&go=insert&mid=" . $this->mid . "&action=newinstance\" valign=\"bottom\" class=\"menu\">:&nbsp;" . $lang->get("sp_newinstance"). "</a></td>";
				echo "<td valign=\"top\" class=\"white\">&nbsp;</td>";
				echo "<td valign=\"top\" class=\"white\">&nbsp;</td>";
				echo "<td valign=\"top\" class=\"white\">&nbsp;</td>";

				$spids = createDBCArray("sitepage", "SPID", "MENU_ID = " . $this->mid, "ORDER BY POSITION");

				for ($i = 0; $i < count($spids); $i++) {
					$oid = $spids[$i];

					$delIcon = "<a href=\"" . $this->action . "?sid=$sid&mid=" . $this->mid . "&action=delp&oid=$oid\" valign=\"bottom\" class=\"menu\">" . drawImage("si_delete.gif", 14, 12). "</a>";
					$editIcon = "<a href=\"" . $this->action . "?sid=$sid&mid=" . $this->mid . "&action=iproperties&oid=$oid\" valign=\"bottom\" class=\"menu\">" . drawImage("si_edit.gif", 10, 12). "</a>";

					if (!$developer || !$qualityman)
						$delIcon = "&nbsp;";

					$title = getMenu($spids[$i], $vario);

					if (isSPLive($spids[$i]))
						$delIcon = "&nbsp;";

					$edContent = "<a href=\"" . $this->action . "?sid=$sid&mid=" . $this->mid . "&oid=$oid&action=editobject&go=update\" valign=\"bottom\" class=\"menu\">" . drawImage("si_writer.gif", 10, 12). "</a>";

					if (isSPVarLive($spids[$i], $vario)) {
						if (SPVarExists($spids[$i], $vario)) {
							echo "<tr class=\"white\"><td class=\"white\" valign=\"top\"><a href=\"" . $this->action . "?sid=$sid&mid=" . $this->mid . "&oid=$oid&action=editobject&go=update\" valign=\"bottom\" class=\"menu\">" . drawImage("green.gif", 12, 12). "</a></td>";
						} else {
							echo "<tr class=\"white\"><td class=\"white\" valign=\"top\"><a href=\"" . $this->action . "?sid=$sid&mid=" . $this->mid . "&oid=$oid&action=editobject&go=update\" valign=\"bottom\" class=\"menu\">" . drawImage("yellow.gif", 12, 12). "</a></td>";
						}
					} else {
						echo "<tr class=\"white\"><td class=\"white\" valign=\"top\"><a href=\"" . $this->action . "?sid=$sid&mid=" . $this->mid . "&oid=$oid&action=editobject&go=update\" valign=\"bottom\" class=\"menu\">" . drawImage("red.gif", 12, 12). "</a></td>";
					}

					echo "<td class=\"white\"><a href=\"" . $this->action . "?sid=$sid&mid=" . $this->mid . "&oid=$oid&action=editobject&go=update\" valign=\"bottom\" class=\"menu\">" . $title[0] . "</a></td>";
					echo "<td valign=\"top\" class=\"white\">$edContent</td>";
					echo "<td valign=\"top\" class=\"white\">$editIcon</td>";
					echo "<td valign=\"top\" class=\"white\">$delIcon</td>";
				}

				echo "</tr></table></td></tr>";
			}

			// -- end multipage handling..		
			echo "<tr><td class=\"upperlevel_action\">";
			echo "<table width=\"150px\" cellpadding=\"0\" cellspacing=\"0\" class=\"embedded\">";
			// drawing grid with 18px-80px-18px-18px-18px
			echo "<tr>";
			$cell18 = new Cell("spc1", "embedded", 1, 16);
			$cell18->draw();
			$cell80 = new Cell("spc2", "embedded", 1, 100);
			$cell80->draw();
			$cell18->draw();
			$cell18->draw();
			$cell18->draw();
			echo "</tr>";

			// draw input for new category.
			if ($developer) {
				echo "<tr><td>";

				echo drawImage("si_newfolder.gif", 12, 12);
				echo "</td><td><a href=\"" . $this->action . "?sid=$sid&mid=" . $this->mid . "&action=newpage&go=insert\" valign=\"bottom\" class=\"menu\"><b>" . $lang->get("sp_newpage"). "</b></a></td><td colspan=\"2\">&nbsp;</td></tr>";
			}

			// drawing directory list
			$sql = "SELECT s.MENU_ID, s.NAME, m.SPMTYPE_ID  FROM sitemap s, sitepage_master m WHERE s.DELETED=0 AND s.SPM_ID = m.SPM_ID AND s.VERSION=0 AND s.PARENT_ID = " . $this->mid . " ORDER BY s.POSITION ASC";

			$query = new query($db, $sql);

			while ($query->getrow()) {
				$id = $query->field("MENU_ID");

				$type = $query->field("SPMTYPE_ID");
				$oid = getDBCell("sitepage", "SPID", "MENU_ID = $id");

				$delIcon = "<a href=\"" . $this->action . "?sid=$sid&mid=" . $this->mid . "&action=delsp&oid=$id\" valign=\"bottom\" class=\"menu\">" . drawImage("si_delete.gif", 14, 12). "</a>";
				$editIcon = "<a href=\"" . $this->action . "?sid=$sid&mid=" . $this->mid . "&action=pproperties&oid=$id\" valign=\"bottom\" class=\"menu\">" . drawImage("si_edit.gif", 10, 12). "</a>";

				if (!$developer || !$qualityman)
					$delIcon = "&nbsp;";

				// check for children, the delete is not allowed.
				$sql = "SELECT MENU_ID FROM sitemap WHERE PARENT_ID = $id AND DELETED=0";
				$cquery = new query($db, $sql);
				$check = $cquery->count();

				if ($check > 0) {
					$delIcon = "&nbsp;";
				} else if ($type == 2 || $type == 3) {
					$sql = "SELECT SPID FROM sitepage WHERE MENU_ID = $id AND DELETED = 0 AND VERSION=0";

					$cquery = new query($db, $sql);
					$check = $cquery->count();

					if ($check > 0)
						$delIcon = "&nbsp;";
				}

				$cquery->free();
				$oids = ""; // add oid string for type 1

				// singlepages start
				if ($type == 1) {
					$sql = "SELECT SPID FROM sitepage WHERE MENU_ID = $id AND DELETED=0 AND VERSION = 0";

					$equery = new query($db, $sql);
					$equery->getrow();
					$myoid = $equery->field("SPID");
					$equery->free();
					$oids = "&oid=$myoid&action=editobject&go=update";

					if (isSPLive($myoid))
						$delIcon = "&nbsp;"; // check if sitepage is live or not...					

					$edContent = "<a href=\"" . $this->action . "?sid=$sid&mid=" . $this->mid . "$oids\" valign=\"bottom\" class=\"menu\">" . drawImage("si_writer.gif", 10, 12). "</a>";

					if (isSPVarLive($myoid, $vario)) {
						if (SPVarExists($myoid, $vario)) {
							echo "<tr><td valign=\"top\"><a href=\"" . $this->action . "?sid=$sid&mid=" . $this->mid . "$oids\" valign=\"bottom\" class=\"menu\">" . drawImage("green.gif", 12, 12). "</a></td>";
						} else {
							echo "<tr><td valign=\"top\"><a href=\"" . $this->action . "?sid=$sid&mid=" . $this->mid . "$oids\" valign=\"bottom\" class=\"menu\">" . drawImage("yellow.gif", 12, 12). "</a></td>";
						}
					} else {
						echo "<tr><td valign=\"top\"><a href=\"" . $this->action . "?sid=$sid&mid=" . $this->mid . "$oids\" valign=\"bottom\" class=\"menu\">" . drawImage("red.gif", 12, 12). "</a></td>";
					}
				// singlepages end -- portal start
				} else if ($type == 3) {
					echo "<tr><td valign=\"top\">" . drawImage("portal.gif", 13, 12). "</td>";

					$edContent = "&nbsp;";
				} else {
					echo "<tr><td valign=\"top\">" . drawImage("si_folder.gif", 13, 12). "</td>";

					$edContent = "&nbsp;";
				}

				echo "<td><a href=\"" . $this->action . "?sid=$sid&mid=" . $id . "\" valign=\"bottom\" class=\"menu\">" . $query->field("NAME"). "</a></td>";
				echo "<td valign=\"top\">$edContent</td>";
				echo "<td valign=\"top\">$editIcon</td>";
				echo "<td valign=\"top\">$delIcon</td>";
			}

			echo "</table></td></tr></table>"; //end of embedded directory list
		}

		/**
		 * internal. write menu header.
		 */
		function draw_header() {
			global $sid, $oid, $go, $page_action;

			echo "<table width=\"" . $this->width . "\" border=\"0\" class=\"design\" cellpadding=\"0\" cellspacing=\"0\">\n";
			echo "<tr><td>\n";
			echo "<form name=\"filter\" action=\"" . $this->action . "\" method=\"POST\">";

			if (isset($oid))
				echo "<input type=\"hidden\" name=\"oid\" value=\"$oid\">\n";

			echo "<input type=\"hidden\" name=\"sid\" value=\"$sid\">\n";
			echo "\n";
		}

		/**
		 * internal. write menu footer.
		 */
		function draw_footer() {
			echo "</form>";

			echo "</td></tr>\n";
			echo "</table>\n";
		}
	}
?>