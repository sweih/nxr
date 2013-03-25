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
	require_once $c["path"] . "api/tools/benchmark.php";
	require_once $c["path"] . "api/tools/browser.php";
	require_once $c["path"] . "api/tools/class.img2thumb.php";
	require_once $c["path"] . "api/tools/class_dir.php";
	require_once $c["path"] . "api/tools/class_specif.php";
	require_once $c["path"] . "api/tools/class.ftp-client.php";
	require_once $c["path"] . "api/tools/copy.php";
	require_once $c["path"] . "api/tools/copy_tree.php";
	//require_once $c["path"] . "api/tools/datatypes.php"; moved to config.inc.php
	require_once $c["path"] . "api/tools/tableorder.php";
	require_once $c["path"] . "api/tools/dreamweaver.php";
	require_once $c["path"] . "api/tools/intrusion_detection.php";
	require_once $c["path"] . "api/tools/class.stats.php";
	require_once $c["path"] . "api/tools/statistics.php";	
	require_once $c["path"] . "api/tools/jpcache.php";
?>