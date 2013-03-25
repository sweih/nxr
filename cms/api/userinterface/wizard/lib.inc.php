<?php
/**
 * Wizard API Library
 * @package Userinterface
 * @subpackage Wizard
 */


/**********************************************************************
 *	N/X - Web Content Management System
 *	Copyright 2004 Sven Weih
 *
 *      $Id: lib.inc.php,v 1.4 2004/11/29 08:20:44 sven_weih Exp $ *
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

	
	require_once $c["path"] . "api/userinterface/wizard/wizard.php";
	require_once $c["path"] . "api/userinterface/wizard/step.php";
   require_once $c["path"] . "api/userinterface/wizard/stexportresource.php";
   require_once $c["path"] . "api/userinterface/wizard/stselectresource.php";
	require_once $c["path"] . "api/userinterface/wizard/wzo.php";
	require_once $c["path"] . "api/userinterface/wizard/wzradio.php";
   require_once $c["path"] . "api/userinterface/wizard/wztext.php";
   require_once $c["path"] . "api/userinterface/wizard/wzlabel.php";
	require_once $c["path"] . "api/userinterface/wizard/wzselect.php";
	require_once $c["path"] . "api/userinterface/wizard/wzselectcluster.php";
	require_once $c["path"] . "api/userinterface/wizard/wzupload.php";
	require_once $c["path"] . "api/userinterface/wizard/wzuploadarchive.php";
	require_once $c["path"] . "api/userinterface/wizard/wzcheckbox.php";
	
?>