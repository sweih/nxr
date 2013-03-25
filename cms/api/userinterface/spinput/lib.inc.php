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
	require_once $c["path"] . "api/userinterface/spinput/abstract_envelope.php";
	require_once $c["path"] . "api/userinterface/spinput/cluster_envelope.php";
    require_once $c["path"] . "api/userinterface/spinput/channel_envelope.php";
	require_once $c["path"] . "api/userinterface/spinput/cluster_information.php";
	require_once $c["path"] . "api/userinterface/spinput/compound_cluster_envelope.php";
  	require_once $c["path"] . "api/userinterface/spinput/phpeditor.php";
	require_once $c["path"] . "api/userinterface/spinput/content_envelope.php";
	require_once $c["path"] . "api/userinterface/spinput/library_envelope.php";
	require_once $c["path"] . "api/userinterface/spinput/libraryviewer.php";
	require_once $c["path"] . "api/userinterface/spinput/library_select.php";
	require_once $c["path"] . "api/userinterface/spinput/metadataeditor.php";
	require_once $c["path"] . "api/userinterface/spinput/multipage_position.php";	
	require_once $c["path"] . "api/userinterface/spinput/embedded_menu.php";
	require_once $c["path"] . "api/userinterface/spinput/sitemap_position.php";
    require_once $c["path"] . "api/userinterface/spinput/select_channel_categories.php";  
    require_once $c["path"] . "api/userinterface/spinput/plugin_input.php";
    require_once $c["path"] . "api/userinterface/spinput/plugin_input_variation.php";
    require_once $c["path"] . "api/userinterface/spinput/iframeobject.php";  	
    require_once $c["path"] . "api/userinterface/spinput/subcategory_selector.php";  	
    require_once $c["path"] . "api/userinterface/spinput/cluster_editor.php";  	
    require_once $c["path"] . "api/userinterface/spinput/selectcltforcln.php";  	
    require_once $c["path"] . "api/userinterface/spinput/clusterinput.php";  	
?>