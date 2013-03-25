<?
	/**********************************************************************
	 *	N/X - Web Content Management System
	 *	Copyright 2002 Sven Weih and Fabian Koenig
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
	
	//general initialization

	require_once $c["path"]."api/database/lib.inc.php";
	require_once $c["path"]."api/common/help.php";
	require_once $c["path"]."api/common/layout.php";
	require_once $c["path"]."api/tools/copy.php";    	
	require_once $c["path"]."api/common/nximage.php";
	require_once $c["path"]."api/cms/cdsinformation.php";
	require_once $c["path"]."api/cms/plugin.php";

	//cds inizialitation
	require_once $c["path"] . "api/cds/abstract_cds_api.php";
	require_once $c["path"] . "api/cds/cds_api.php";
	require_once $c["path"] . "api/cds/cds_interface.php";
	require_once $c["path"] . "api/cds/cds_plugins.php";
   	require_once $c["path"] . "api/cds/cdssearchengine.php";
	require_once $c["path"] . "api/cds/clusters.php";
   	require_once $c["path"] . "api/cds/channel.php";
	require_once $c["path"] . "api/cds/content.php";
	require_once $c["path"] . "api/cds/management.php";
	require_once $c["path"] . "api/cds/messages.php";
	require_once $c["path"] . "api/cds/meta.php";
	require_once $c["path"] . "api/cds/layout.php";
	require_once $c["path"] . "api/cds/layout_menu.php";
	require_once $c["path"] . "api/cds/layout_media.php";
	require_once $c["path"] . "api/cds/layout_dhtml.php";
	require_once $c["path"] . "api/cds/menu.php";
	require_once $c["path"] . "api/cds/tools.php";
	require_once $c["path"] . "api/cds/sma_menu.php";
	require_once $c["path"] . "api/cds/sma_content.php";
	require_once $c["path"] . "api/cds/sma_cds_api.php";
	require_once $c["path"] . "api/cds/sma_layout.php";
	
	//authentification and authorization
	require_once $c["path"]."api/auth/auth_sma.php";
	require_once $c["path"]."api/auth/auth_community.php";
	require_once $c["path"]."api/cms/log.php";
	
	// parser
    require_once $c["path"]."api/parser/nxparser.php";
	require_once $c["path"]."api/parser/nx2html.php";
	require_once $c["path"]."api/parser/launch_text.php";
?>