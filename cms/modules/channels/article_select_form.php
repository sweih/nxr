<?php
	/**********************************************************************
	 *	N/X - Web Content Management System
	 *	Copyright 2002-2004 Sven Weih
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
	 * Specialized form for selecting articles of a channel.
	 */
	 
	 class ArticleSelectForm extends MenuForm {
	 
	 	var $channelId;
	 	var $categories;
	 	var $arows="";
	 	
	 	/**
	 	 * Standard constructor
	 	 * @param integer ID of the channel
	 	 */
	 	function ArticleSelectForm($chId) {
	 		global $lang, $db, $c, $sid, $page;
	 		
	 		// general configuration
        	$this->orderdir = strtoupper(initValue("dir", $table."_dir", "ASC"));
        	$this->order = initValue("order", $table."_order",$displayColumns[0]);         
        	if ($this->order =="") $this->order = "NAME";        	
        	$this->page = initValue("page", $table."_page", 1);
	 		$this->channelId = $chId;
	 		$this->width = 800;
	 		$this->title = $lang->get("sel_article", "Select Article")." ".$lang->get("in_channel", "in channel")." ".getDBCell("channels", "NAME", "CHID = ".$chId);
	 		
	 		// special configuration
	 		$this->displayColumns = array("", "POSITION","NAME", "CATEGORY", "EDITED", "");
	 			
	 		$this->colTitles = array("&nbsp;", "POS", $lang->get("name"), $lang->get("category"),  $lang->get("edited", "Edited"), "&nbsp;");
	 		$this->populateCategories();
	 		$this->init();
	 		
			$this->buttonbar = new Buttonbar("launch", "standard", count($this->colTitles));
				
	        	$this->showall = initValue("showall", $table."_showall", "no");
	        
	        	if ($this->showall == "yes") {
	        		$this->recordsPerPage = 100;
	        	}
	        
	        	$page->setJS("TOGGLETD");
	 	}
	 	
	       /**
       		* query the rows from the database
         	*/
      		function getRows () {
         		global $db, $c, $sid, $lang, $auth;
         		$result = array();
         		
         		$order = "ca.TITLE";
         		if ($this->order == "NAME") {
         			$order = "ca.TITLE";
         		} else if ($this->order == "CATEGORY") {
         			$order = "ca.CH_CAT_ID";
         		} else if ($this->order == "EDITED") {
         			$order = "cv.LAST_CHANGED";
				} else if ($this->order == "CREATED") {         			
						$order = "cv.CREATED_AT";
				} else if ($this->order == "POSITION") {
						$order = "ca.POSITION";	
				}
					
         		$order.=" ".$this->orderdir;
         		// $sql_articles = "Select ca.*, cv.LAST_USER, cv.CREATED_AT, cv.LAST_CHANGED FROM channel_articles ca, cluster_variations cv ".$this->getFilterJoin()." WHERE ca.CHID = ".$this->channelId." AND ca.ARTICLE_ID = cv.CLNID AND cv.VARIATION_ID = ".variation()." AND ca.VERSION=0 " . $this->getFilterSQL() . " ORDER BY $order LIMIT ".(($this->page - 1) * $this->recordsPerPage).",".$this->recordsPerPage;
         		$sql_articles = "SELECT DISTINCT ca.* FROM channel_articles ca, cluster_variations cv ".$this->getFilterJoin()." WHERE ca.ARTICLE_ID = cv.CLNID AND ca.CHID = ".$this->channelId." AND ca.VERSION = 0 ".$this->getFilterSQL()." ORDER BY $order LIMIT ".(($this->page - 1) * $this->recordsPerPage).",".$this->recordsPerPage;
         		// echo $sql_articles;
         		$query = new query($db, $sql_articles);
          		while ($query->getrow()) {
                 		$tmp = array();
                 		$varexists = true;
                 		$article_id = $query->field("ARTICLE_ID");
                 		$cvdatasql = "SELECT * FROM cluster_variations WHERE CLNID = ".$article_id." AND VARIATION_ID=".variation();
                 		$cvdata = new query($db, $cvdatasql);
                 		$cvdata->getrow();
                 		if ($cvdata->count() < 1) {
                 			$varexists = false;
                 		}
                 		array_push($tmp, $query->field("ARTICLE_ID"));
                 		$clid = getDBCell("cluster_variations", "CLID", "CLNID = ".$query->field("ARTICLE_ID")." AND VARIATION_ID = ".variation());                 		
                 		$live = isClusterLive($clid);
              			if ($varexists) {
	                 		if ($live) {
	                 			array_push($tmp, drawImage("green.gif", $lang->get("article_is_live", "Article is live")));
	                 		} else {
	                 			array_push($tmp, drawImage("red.gif", $lang->get("article_is_expired", "Article is expired")));
	                 		}
              			} else {
              				array_push($tmp, drawImage("gray.gif", $lang->get("article_variation_missing", "Variation of this article does not exist yet")));
              			}
                 		array_push($tmp, $query->field("POSITION"));
              			array_push($tmp, '<b>'.$query->field("TITLE").'</b>');
                 		array_push($tmp, $this->categories[$query->field("CH_CAT_ID")]);
                 		array_push($tmp, formatDBTimestamp($cvdata->field("LAST_CHANGED")));                 		
                        	$buttons = "&nbsp;".crLink(drawImage('up.gif'), $c["docroot"]."modules/channels/overview.php?sid=$sid&action=up&article=".$query->field("ARTICLE_ID") , "navelement");
                        	$buttons.= "&nbsp;".crLink(drawImage('down.gif'), $c["docroot"]."modules/channels/overview.php?sid=$sid&action=down&article=".$query->field("ARTICLE_ID") , "navelement");
                        	$buttons.= "&nbsp;";
                        	if ($auth->checkAccessToFunction("CHANNEL_DELETE")) {
                          		$buttons.= "&nbsp;".crLink($lang->get("delete"), "javascript:confirmAction('".$lang->get("del_article")."', '".$c["docroot"]."modules/channels/overview.php?sid=$sid&action=deletearticle&article=".$query->field("ARTICLE_ID")."');" , "navelement");
                        	}  
                        	if ($auth->checkAccessToFunction("CHANNEL_LAUNCH")) {
								$buttons.= "&nbsp;".crLink($lang->get("launch", "Launch"), $c["docroot"]."modules/channels/overview.php?sid=$sid&action=launcharticle&article=".$query->field("ARTICLE_ID") , "navelement");
								$buttons.= "&nbsp;".crLink($lang->get("expire", "Expire"), $c["docroot"]."modules/channels/overview.php?sid=$sid&action=expirearticle&article=".$query->field("ARTICLE_ID") , "navelement");
								
                        	}                        	
                          	array_push($tmp, $buttons);
                        	
                        	
                        	array_push($result, $tmp);
         		}
         		return $result;
      		}
	 	
		 /**
		  * count rows after filtering etc.
		  * @param boolean optional set to true if you want to override the cached value
		  * @return integer number of rows
		  */
		 function countRows($force=false) {
		 	global $db;
		 	
		 	if (($this->arows == "") || $force) {

		 		// $sql = "Select COUNT(ca.ARTICLE_ID) AS ANZ FROM channel_articles ca, cluster_variations cv ".$this->getFilterJoin()."WHERE ca.CHID = ".$this->channelId." AND ca.ARTICLE_ID = cv.CLNID AND cv.VARIATION_ID = ".variation()." AND ca.VERSION=0 ".$this->getFilterSQL();
		 		// we need a new count-sql due to displaying missing acticle-variations as well
		 		$sql = "SELECT COUNT(ca.ARTICLE_ID) AS ANZ FROM channel_articles ca ".$this->getFilterJoin()." WHERE ca.CHID = ".$this->channelId." AND ca.VERSION = 0 ".$this->getFilterSQL();
		 		$query = new query($db, $sql);
		 		if ($query->getrow()) {
		 			$this->arows = $query->field("ANZ");
		 			$query->free();
		 		}		 		
			 		
		 	}
		 	return $this->arows;
		 }      		
      		
      		
	 	/**
	 	 * Populate the categories-array
	 	 */
	 	function populateCategories() {
	 	  global $db;
	 	  
	 	  $this->categories = array();
	 	  $sql = "SELECT CH_CAT_ID, NAME FROM channel_categories WHERE CHID = ".$this->channelId;
	 	  $query = new query($db, $sql);
	 	  while ($query->getrow()) {
			$this->categories[$query->field("CH_CAT_ID")] = $query->field("NAME");	 	  		
	 	  }
	 	}
	 	
	 	 
	 
	 }
	 
?>