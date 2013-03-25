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
 
 /**
  * Display information about a cluster
  */
 class ClusterInformation extends WUIInterface {
 
 	var $clid;
 	var $css;
 	var $cols;
 	
 	/**
 	 * Standard constructor
 	 * @param integer ID of the Cluster-Instance
 	 * @param string CSS-Class to use
 	 * @param integer Colspan to use
 	 */
 	function ClusterInformation($clid, $style="standardwhite", $columns=3) {
 		$this->clid = $clid;
 		$this->css = $style;
 		$this->columns = $columns;
 	}
 	
 	/**
 	 * Draw the information
 	 */
 	function draw() {
 	 	global $lang;
 	 	$createdAt = "<b>".formatDBTimeStamp(getDBCell("cluster_variations", "CREATED_AT", "CLID = ".$this->clid))."</b>";
 	 	$createdBy = "<b>".getDBCell("cluster_variations", "CREATE_USER", "CLID = ".$this->clid)."</b>";
 	 	$modifiedAt = "<b>".formatDBTimeStamp(getDBCell("cluster_variations", "LAST_CHANGED", "CLID = ".$this->clid))."</b>";
 	 	$modifiedBy = "<b>".getDBCell("cluster_variations", "LAST_USER", "CLID = ".$this->clid)."</b>";
 	 	$launchedAt = "<b>".formatDBTimeStamp(getDBCell("cluster_variations", "LAUNCHED_AT", "CLID = ".$this->clid))."</b>";
 	 	$launchedBy = "<b>".getDBCell("cluster_variations", "LAUNCH_USER", "CLID = ".$this->clid)."</b>";
 	 	
 	 	echo '<td colspan="'.$this->columns.'" class="'.$this->css.'">';
 		echo tableStart("100%", $this->css);
 		echo '<td width="200">'.$lang->get("created_at", "Created at")."</td><td width=\"200\">".$createdAt."</td><td width=\"50\">".$lang->get("by", "by")."</td><td>".$createdBy."</td></tr>";
 		echo '<tr><td>'.$lang->get("last_mod_at", "Last Modified at")."</td><td>".$modifiedAt."</td><td>".$lang->get("by", "by")."</td><td>".$modifiedBy."</td></tr>";
 		echo '<tr><td>'.$lang->get("last_launched_at", "Last Launched at")."</td><td>".$launchedAt."</td><td>".$lang->get("by", "by")."</td><td>".$launchedBy."</td></tr>";	
 		echo tableEnd();
 		echo '</td>';
 		return $this->columns; 	
 	}
 }
 
 ?>