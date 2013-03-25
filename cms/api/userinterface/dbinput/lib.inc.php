<?php
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
	require_once $c["path"] . "api/userinterface/dbinput/dbo.php";

	require_once $c["path"] . "api/userinterface/dbinput/date_time_input.php";
	require_once $c["path"] . "api/userinterface/dbinput/ci_selector.php";
	require_once $c["path"] . "api/userinterface/dbinput/cl_selector.php";
	require_once $c["path"] . "api/userinterface/dbinput/cpcl_selector.php";
	require_once $c["path"] . "api/userinterface/dbinput/clt_selector.php";
	require_once $c["path"] . "api/userinterface/dbinput/clti_selector.php";
	require_once $c["path"] . "api/userinterface/dbinput/combinationeditor.php";
	require_once $c["path"] . "api/userinterface/dbinput/date_input.php";
	require_once $c["path"] . "api/userinterface/dbinput/dbo_folder.php";
	require_once $c["path"] . "api/userinterface/dbinput/dbo_menu.php";
	require_once $c["path"] . "api/userinterface/dbinput/displayed_value.php";
	require_once $c["path"] . "api/userinterface/dbinput/nondisplayed_value.php";
	require_once $c["path"] . "api/userinterface/dbinput/nondisplayed_value_oninsert.php";
	require_once $c["path"] . "api/userinterface/dbinput/password_input.php";
	require_once $c["path"] . "api/userinterface/dbinput/select_multiple2_input.php";
	require_once $c["path"] . "api/userinterface/dbinput/select_multiple_input.php";
	require_once $c["path"] . "api/userinterface/dbinput/select_one_input.php";
	require_once $c["path"] . "api/userinterface/dbinput/select_one_input_fixed.php";
	require_once $c["path"] . "api/userinterface/dbinput/text_input.php";
	require_once $c["path"] . "api/userinterface/dbinput/checkbox_input.php";
	require_once $c["path"] . "api/userinterface/dbinput/checkboxtxt_input.php";
	require_once $c["path"] . "api/userinterface/dbinput/iconselect_input.php";
	require_once $c["path"] . "api/userinterface/dbinput/duration_input.php";
	require_once $c["path"] . "api/userinterface/dbinput/time_input.php";
	require_once $c["path"] . "api/userinterface/dbinput/richedit_input.php";
	require_once $c["path"] . "api/userinterface/dbinput/sitepage_selector.php";
	require_once $c["path"] . "api/userinterface/dbinput/manual_dropdown_input.php";
	require_once $c["path"] . "api/userinterface/dbinput/position_input.php";
?>