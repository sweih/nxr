<?php

	/**
	 * @package CDS
	 */

	/**********************************************************************
     *    N/X - Web Content Management System
     *    Copyright 2003 Sven Weih
     *
     *    This file is part of N/X.
     *
     *    N/X is free software; you can redistribute it and/or modify
     *    it under the terms of the GNU General Public License as published by
     *    the Free Software Foundation; either version 2 of the License, or
     *    (at your option) any later version.
     *
     *    N/X is distributed in the hope that it will be useful,
     *    but WITHOUT ANY WARRANTY; without even the implied warranty of
     *    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
     *    GNU General Public License for more details.
     *
     *    You should have received a copy of the GNU General Public License
     *    along with N/X; if not, write to the Free Software
     *    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
     **********************************************************************/

     /**
      * Use this class to access articles in channels
      * Access this class with $cds->channel
      */
     class Channel extends CDSInterface {
        function Channel(&$parent) { CDSInterface::CDSInterface($parent); }

        /**
         * returns an field containing arrays containg the ids of clusters of this channel.
         * Use cluster->getById() to retrieve the content of the cluster.
         * @param string Name of the cluster
         * @param integer ID of the Variation to query. Leave Blank or set to zero for Page-Variation.
         * @param string name of the column to order the cluster-nodes.
         */
         function getField($name, $variation=0, $order = "POSITION ASC"){
             global $c;
             if ($variation == 0)
                    $variation = $this->variation;
             $name = strtolower($name);

              // get the clti..
              $clid = getDBCell("cluster_variations", "CLID", "CLNID = ".$this->parent->pageClusterNodeId." AND VARIATION_ID = $variation");
              $clt = getDBCell("cluster_node", "CLT_ID", "CLNID = ".$this->parent->pageClusterNodeId);
              $clti = getDBCell("cluster_template_items", "CLTI_ID", "CLT_ID = $clt AND UPPER(NAME) = '$name'");
              if ($clti == "") return "Field not defined!";
              $type = getDBCell("cluster_template_items", "CLTITYPE_ID", "CLTI_ID = $clti");

              if ($type == 8) {

                 // query parameters...
                 $stageIds = createDBCArray("cluster_content", "CLCID", "CLTI_ID = $clti AND CLID = $clid ORDER BY POSITION ASC");
                 $result = array();
                 for ($i=0; $i < count($stageIds); $i++) {
                   $result[] = $this->_getArticleClusters($stageIds[$i], $variation);
                 }
                 return $result;
              } else {
                echo "This field is not a channel-centerstage!";
              }
        }


        /**
         * Returns the Article-Date
         * @param integer id of article
         * @param string format of date-output
         */
         function getArticleDate($articleId="", $dateformat="%Y-%m-%d") {
         	global $db;
	  		if ($articleId == "") $articleId = $this->pageClusterNodeId;
         	if ($dateformat == "") {
			  $sql = "SELECT ARTICLE_DATE as d FROM channel_articles WHERE ARTICLE_ID = $articleId";         		
         	} else {
	  		  $sql = "SELECT DATE_FORMAT(ARTICLE_DATE, '$dateformat') as d FROM channel_articles WHERE ARTICLE_ID = $articleId";
         	}
	  		$query = new query($db, $sql);
	  		if ($query->getrow()) {
				$datum = $query->field("d");
	    		$query->free();
	    		return $datum;
	  		}
         }
        
         /**
          * Returns the link to the given article.
          *
          * @param integer $articleId
          * @param integer $variation
          */
         function getLink($articleId, $variation=0) {
         	global $c, $cds;
         	 if ($variation == 0)
                    $variation = $this->variation;
             
         	if ($cds->is_development) {
         		// dev url
         		$category = getDBCell("channel_articles", "CH_CAT_ID", "ARTICLE_ID=".$articleId);
         		$spid = getDBCell("channel_categories", "PAGE_ID", "CH_CAT_ID=".$category);         		         		
         		$spm = getDBCell("sitepage", "SPM_ID", "SPID=".$spid);
         		$template=getDBCell("sitepage_master", "TEMPLATE_PATH", "SPM_ID=".$spm);
         		$result = $c['devdocroot'].$template."?page=$spid&v=$variation&article=$articleId";
         	} else {
         		// live url.
         		$result = $c['livedocroot'].getArticleURL($articleId, $variation);         		
         	}
         	return $result;
         }
         
         /**
         * returns an array containg the ids of clusters of this channel.
         * Use cluster->getById() to retrieve the content of the cluster.
         * @param string Name of the cluster
         * @param integer ID of the Variation to query. Leave Blank or set to zero for Page-Variation.
         */
         function get($name, $variation=0) {
             global $c;
             if ($variation == 0)
                    $variation = $this->variation;
             $name = strtolower($name);
              // get the clti..
              $clid = getDBCell("cluster_variations", "CLID", "CLNID = ".$this->parent->pageClusterNodeId." AND VARIATION_ID = $variation");
              $clt = getDBCell("cluster_node", "CLT_ID", "CLNID = ".$this->parent->pageClusterNodeId);
              $clti = getDBCell("cluster_template_items", "CLTI_ID", "CLT_ID = $clt AND UPPER(NAME) = '$name'");
              if ($clti == "") return "Field not defined!";
              $type = getDBCell("cluster_template_items", "CLTITYPE_ID", "CLTI_ID = $clti");

              if ($type == 8) {

                 // query parameters...
                 $stageId = getDBCell("cluster_content", "CLCID", "CLTI_ID = $clti AND CLID = $clid");
                 return $this->_getArticleClusters($stageId, $variation);
              } else {
                echo "This field is not a channel-centerstage!";
              }
         }
         
         /**
          * returns the category of a Channel-Article
          * @param integer ID of the article
          * @returns string Name of the Channel-Category
          */
         function getCategory($articleId) {
         	$categoryId = getDBCell("channel_articles", "CH_CAT_ID", "ARTICLE_ID = $articleId");
         	$categoryName = getDBCell("channel_categories", "NAME", "CH_CAT_ID = $categoryId");
         	return $categoryName;
         }
         
        
        /**
          * returns the Title of a Channel-Article
          * @param integer ID of the article
          * @returns string Title of the Channel-Article
          */
         function getTitle($articleId) {
         	return getDBCell("channel_articles", "TITLE", "ARTICLE_ID = $articleId");
         }


         /**
          * Returns an Array with the articles of a centerstage
          * @param integer ID of the Stage-Item.
          * @param integer ID of the variation
          */
         function _getArticleClusters($stageId, $variation){
                 global $db;
                 $chid = getDBCell("centerstage", "CHID", "STAGE_ID = $stageId");
                 $chCatId = getDBCell("centerstage", "CH_CAT_ID", "STAGE_ID = $stageId");
                 $sortmode = getDBCell("centerstage", "SORT_ALGORITHM", "STAGE_ID = $stageId");
                 $limit = getDBCell("centerstage", "MAXCARD", "STAGE_ID = $stageId");

                 // build sql
                 $sLimit = "";
                 $aFilter = array();

                 if ($limit >0 && $limit < 999) {
                   $sLimit = " LIMIT 0, $limit ";
                 }

                 if ($chid != 0) array_push($aFilter, "ca.CHID = $chid");
                 if ($chCatId != 0) array_push($aFilter, "ca.CH_CAT_ID = $chCatId");
                 array_push($aFilter, "ca.ARTICLE_ID = cv.CLNID");
                 array_push($aFilter, "cv.VARIATION_ID = ".$variation);
                 if (! $this->parent->is_development) {
                 	array_push($aFilter, "cv.CLID = st.OUT_ID AND st.EXPIRED=0");
                  	array_push($aFilter, "(ca.LAUNCH_DATE <= NOW() OR ca.LAUNCH_DATE = 0 OR ca.LAUNCH_DATE IS NULL)");
                 	array_push($aFilter, "(ca.EXPIRE_DATE >= NOW() OR ca.EXPIRE_DATE = 0 OR ca.EXPIRE_DATE IS NULL)");                 	
                 	array_push($aFilter, "ca.VERSION=10");                 	
                 } else {
                 	array_push($aFilter, "ca.VERSION=0");                 	
                 }
                 
                 $sFilter = implode(" AND ", $aFilter);

                 /** sortmode
                     1 = Latest
                     2 = Oldest
                     3 = Random
                     4 = A-Z
                     5 = Z-A
                     6 = Latest created
                     7 = Oldest created
                  */
                 $sSort = "";
                // old style
                // if ($sortmode == 1) $sSort = " ORDER BY cv.LAST_CHANGED DESC";
                // if ($sortmode == 2) $sSort = " ORDER BY cv.LAST_CHANGED ASC";
                 
                 if ($sortmode == 1) $sSort = " ORDER BY cv.LAST_CHANGED DESC";
                 if ($sortmode == 2) $sSort = " ORDER BY cv.LAST_CHANGED ASC";               
                 if ($sortmode == 4) $sSort = " ORDER BY ca.TITLE ASC";
                 if ($sortmode == 5) $sSort = " ORDER BY ca.TITLE DESC";
                 if ($sortmode == 6) $sSort = " ORDER BY ca.ARTICLE_DATE DESC";
                 if ($sortmode == 7) $sSort = " ORDER BY ca.ARTICLE_DATE ASC";
                 if ($sortmode == 8) $sSort = " ORDER BY ca.POSITION ASC";
				 if ($sortmode == 9) $sSort = " ORDER BY ca.POSITION DESC";
				 
                 $clusters = array();

                 $sql = "SELECT DISTINCT ca.ARTICLE_ID FROM channel_articles ca, cluster_variations cv, state_translation st WHERE ".$sFilter.$sSort.$sLimit;                
                 $query = new query($db, $sql);
                 while ($query->getrow()) {
                        $clusters[] = $query->field("ARTICLE_ID");
                 }

                 if ($sortmode == 3) {
                     // shuffle
                     $i = count($clusters);
                     while ($i > 0) {
                        $i--;
                        $j = mt_rand(0, $i);
                        if ($i != $j) {
                            // swap
                            $tmp = $clusters[$i];
                            $clusters[$i] = $clusters[$j];
                            $clusters[$j] = $tmp;
                        }
                    }
                 }

                 return $clusters;
         }
       }
?>