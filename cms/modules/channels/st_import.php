<?php
/**********************************************************************
 *	N/X - Web Content Management System
 *	Copyright 2005 Sven Weih
 *
 *      $Id: st_import.php,v 1.2 2005/05/08 10:19:00 sven_weih Exp $ *
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

class STImportToChannel extends Step {
	
	var $categoryId;
	var $channelId;
	var $menuId;

	
	/**
	 * Constructor
	 */
	function STImportToChannel() {
		$this->categoryId = $_SESSION["guid"];
		$this->menuId = $_SESSION["menuid"];
		$this->channelId = getDBCell("channel_categories", "CHID", "CH_CAT_ID = ".$this->categoryId);
	}
	
	
   /**
    * Perform the export.
    */
    function execute() {
      global $errors, $lang, $sid;
     
      $minus = 0;
      $pages = createDBCArray("sitepage", "SPID", "MENU_ID = ".$this->menuId);
      for ($i=0; $i < count($pages); $i++) {
        $cluster = getDBCell("sitepage", "CLNID", "SPID=".$pages[$i]);
        $articles = countRows("channel_articles", "ARTICLE_ID", "ARTICLE_ID=".$cluster);
      	if ($articles==0) {
          importClusterToArticle($pages[$i], $this->channelId, $this->categoryId); 	       	
      	} else {
      	  $minus++;
      		$error = $lang->get("already_imported", "At least one page has not been imported, because it already exists in an channel.");	
      	}
      }
      
      if ($errors == "") {
        $this->add(new WZLabel($lang->get("ch_imp_success", "The data was successfully imported to the channel.")));
        if (strlen($error) > 0 ) {
          $this->add(new WZLabel($error));	
        }
        $this->add(new WZLabel($lang->get("num_imp_pages", "Total number of imported pages:")." ".(count($pages)-$minus)));
      } else {
        $this->add(new WZLabel($lang->get("ch_imp_failed", "There was an error while importing the data to the channel.")));	
      }
      $this->parent->finished=true;
      $this->add(new WZLabel($lang->get("back_to_channels", "Back to Article Overview")." >>", "modules/channels/overview.php?sid=$sid"));
    }
	
}


?>