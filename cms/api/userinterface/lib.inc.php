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

	// needed to split one file into more because of documentation.
	require_once $c["path"] . "api/userinterface/validation/lib.inc.php";

	require_once $c["path"] . "api/userinterface/htmlinput/lib.inc.php";
	require_once $c["path"] . "api/userinterface/dbinput/lib.inc.php";
	require_once $c["path"] . "api/userinterface/page/lib.inc.php";
	require_once $c["path"] . "api/userinterface/form/lib.inc.php";
	require_once $c["path"] . "api/userinterface/spinput/lib.inc.php";
	require_once $c["path"] . "api/userinterface/processing/lib.inc.php";
	require_once $c["path"] . "api/userinterface/menu/lib.inc.php";
	require_once $c["path"] . "api/userinterface/wizard/lib.inc.php";
	require_once $c["path"] . "api/userinterface/tree/treemenu2.php";	
?>