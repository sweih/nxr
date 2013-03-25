<?
	/**********************************************************************
	 *	N/X - Web Content Management System
	 *	Copyright 2002 Fabian Koenig
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
	$menu = new StdMenu($lang->get("mt_title", "Maintenance"));
	
	$menu->addMenuEntry($lang->get("mt_generate_dta", "Generate DataTypes"), "datatypes.php");
	$menu->addMenuEntry($lang->get("mt_sync_cl", "Synchronize Clusters"), "sync_clusters.php");
	
	if ($c["renderstatichtml"])
		$menu->addMenuEntry($lang->get("rb_cache", "Rebuild Cache"), "rebuild_cache.php");
		
	if ($JPCACHE_ON)
		$menu->addMenuEntry($lang->get("clear_jpcache", "Clear Cache"), "clear_jpcache.php");		
?>