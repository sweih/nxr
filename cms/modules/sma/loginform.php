<?php

	/**
	 * Draws the LoginForm of the Systems. Calls the login-logic.
	 * @package Management
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
	require_once "../../config.inc.php";

	/**
	 * Special form for user authentification only.
	 * Used by Login-screen only!
	 */
	class LoginFormSMA extends Form {

		/**
		 * standard constructor.
		 */
		function LoginFormSMA() {
			global $c;

			Form::Form("N/X 2004 Login", "i_key.gif", "loginform", $c["docroot"] . "modules/sma/login.php");
			$this->width = 250;
			global $lang;
			$v = value("v");
			$page = value("page", "NUMERIC");
			$this->add(new Label("lbl1", $lang->get("user_name"), "standard"));
			$this->add(new Input("login", "", "standard", 16, "", 100));
			$this->add(new Label("lbl2", $lang->get("password"), "standard"));
			$this->add(new Input("passwd", "", "standard", 16, "", 100, "PASSWORD"));
			$this->add(new Hidden("page", $page));
			$this->add(new Hidden("v", $v));
		}

		/**
		 * Draw the form
		 */
		function draw() {
			echo "\n<!-- draw header -->\n";

			$this->draw_header();
			echo "\n<!-- draw body -->\n";
			$this->draw_body();
			echo "\n<!-- draw footer -->\n";
			$this->draw_footer();
			echo "\n<!-- end draw footer -->\n";
		}

		/**
		 * draw the submit button.
		 */
		function draw_buttons() {
			echo "<tr><td class=\"informationheader\" colspan=\"2\" align=\"center\">";

			echo drawSpacer(5, 5). "<br>";
			$submitbutton = new LinkButtonInline("goon", "Login", "navelement", "submit", "document.loginform.passwd.value=hex_md5(document.loginform.passwd.value);", "loginform");
			echo $submitbutton->draw();
			retain ("goon");
			echo "<br>" . drawSpacer(5, 5);
			echo "</td></tr>";
		}

		/**
		 * does nothing but diplaying, that login was wrong.
		 * The auth class does a redirect, if login is okay.
		 * So process does only need to check, if the form was
		 * submitted.
		 */
		function process() {
			global $status, $lang;

			if ($status == "failed") {
				$this->addToTopText($lang->get("login_failed"));

				$this->setTopStyle("errorheader");
			}
		}
		/** For Down-Grade-Compatibility only **/
		function proccess() { $this->process(); }
	}
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">

<html>
	<head>
		<title>N/X BACKOFFICE USER LOGIN</title>

		<META HTTP-EQUIV = "Pragma" content = "no-cache">

		<META HTTP-EQUIV = "Cache-Control" content = "no-cache, must-revalidate">

		<META HTTP-EQUIV = "Expires" content = "0">

		<script language = "Javascript" src = "<? echo $c["docroot"]; ?>api/js/md5.js"></script>

<?php
	echoCSS();
?>

	</head>

	<body leftmargin = "0" topmargin = "0" marginheight = "0" marginwidth = "0">
		<script type = "text/javascript">
			if (parent.frames.length > 0) {
				parent.location.href = self.document.location;
			}
		</script>

		<table width = "100%" height = "100%" border = "0" align = "center">
			<tr>
				<TD width = "100%" height = "10%" valign = "bottom" align = "center">
					&nbsp;
				</td>
			</tr>

			<tr>
				<TD widht = "100%" height = "*" valign = "middle" align = "center">
					<!-- Loginform -->
					<?
						$loginform = new LoginFormSMA();
						$loginform->process();
						$loginform->draw();
						$db->close();
					?>
				<!-- End Loginform -->
				</td>
			</tr>

			<tr>
				<td width = "100%" height = "20%" valign = "bottom" align = "right">
					<div align = "right" style = "font-size:8px">
						V

						<?PHP
							echo $nx_version;
						?>

					</div>
				</td>
			</tr>
		</table>

								<script language = "Javascript">
									document.loginform.login.focus();
								</script>
	</body>
</html>