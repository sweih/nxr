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
	class LoginForm extends Form {

		/**
		 * standard constructor.
		 */
		function LoginForm() {
			global $c, $nx_version;

			Form::Form("N/X WCMS $nx_version Login", "i_key.gif", "loginform", $c["docroot"] . "api/auth/login.php");
			$this->width = 350;
			global $lang;
			if (reg_load("SYSTEM/MAINTENANCE/BB") == "1")
			  $this->add(new Label("lbl", $lang->get("bb_in_mm_mode", "The backend is in maintenance at the moment. Only the System Administrator can log in!"), "standardwhite", 2));
			  
			$submitTxt = 'onKeyPress="if (event.keyCode==13) {document.loginform.passwd.value=hex_md5(document.loginform.passwd0.value);document.loginform.passwd0.value=\'\';document.loginform.submit();}";';
			$login = new Input("login", "", "standard", 16, "", 150);
			$login->additionalParameters = $submitTxt;
			$pass  = new Input("passwd0", "", "standard", 64, "", 150, "PASSWORD");
			$pass->additionalParameters = $submitTxt;
			
			$this->add(new Label("lbl1", $lang->get("user_name"), "standard"));
			$this->add($login);
			$this->add(new Label("lbl2", $lang->get("password"), "standard"));
			$this->add($pass);
			$this->add(new Hidden("passwd", ""));
		}

		/**
		 * Draw the form
		 */
		function draw() {
			$this->draw_header();
			$this->draw_body();
			$this->draw_footer();			
		}

		/**
		 * draw the submit button.
		 */
		function draw_buttons() {
			global $lang;
			echo "<tr><td colspan=\"2\" align=\"center\">";			
			$submitbutton = new Button("goon", $lang->get('login', 'Sign In'), "", "button", "document.loginform.passwd.value=hex_md5(document.loginform.passwd0.value);document.loginform.passwd0.value='';document.loginform.submit();");
			$submitbutton->draw();			
			echo "</td></tr>";
		}

		/**
		 * does nothing but diplaying, that login was wrong.
		 * The auth class does a redirect, if login is okay.
		 * So process does only need to check, if the form was
		 * submitted.
		 */
		function process() {
			global $lang;

			$status = value("status");

			if ($status == "failed") {
				$this->addToTopText($lang->get("login_failed"));
				$this->topstyle = 'headererror';				
			}
		}
	}
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">

<html>
	<head>
		<title>N/X BACKOFFICE USER LOGIN</title>
		<META HTTP-EQUIV = "Pragma" content = "no-cache"/>
		<META HTTP-EQUIV = "Cache-Control" content = "no-cache, must-revalidate"/>
		<META HTTP-EQUIV = "Expires" content = "0">
		<script language = "Javascript" src = "<? echo $c["docroot"]; ?>api/js/md5.js"></script>

<?php
	echoCSS();
			$css = '
			div.active1 {
				height: 4px;
				background: url(' . $c["docroot"] . '/img/alb.gif) no-repeat top left;
			}

			div.active2 {
				height: 4px;
				background: url(' . $c["docroot"] . '/img/arb.gif) no-repeat top right;
				padding: 1px 6px;
			}
			
			div.active4 {
				height: 4px;
				background: url(' . $c["docroot"] . '/img/ulb.gif) no-repeat bottom left;
			}

			div.active5 {
				height: 4px;
				background: url(' . $c["docroot"] . '/img/urb.gif) no-repeat bottom right;
				padding: 1px 6px;
			}
			
			';
			echo '<style type="text/css">';
			echo $css;
			echo '</style>';
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
				<TD width="100%"  valign = "middle" align="center">
					<div style="width:350px;align:center">
					<?
						$loginform = new LoginForm();
						$loginform->process();
						$loginform->draw();
						$db->close();
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