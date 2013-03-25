<?
	/**********************************************************************
	 *	N/X - Web Content Management System
	 *	Copyright 2004 Sven Weih
	 *
	 *	This file is part of N/X.
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
	$auth = new auth("ANY");
	$page = new page("Access Violation");
	$form = new MessageForm($lang->get("access_violation", "Access violation"), $lang->get("access_v_text", "You have not rights to access this object!"), $c["docroot"]."api/userinterface/page/blank_page.php");
	$log = new DBLog("Access Violation");
	$log->log("User ".$auth->userName." tried to access resource with guid ".value("guid", "NUMERIC"));
	$page->add($form);
	$page->draw();
	$db->close();
?>