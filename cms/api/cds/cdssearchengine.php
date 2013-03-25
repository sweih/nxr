<?php

 /**
  * @package CDS
  */

/**********************************************************************
 *	N/X - Web Content Management System
 *	Copyright 2003 Sven Weih
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
 
 /**
  * Base class for searching the site. Note. You must run the spider, before
  * you can use this engine.
  * Access this class with $cds->searchengine
  */
  class CDSSearchengine {
  	
    /**
     * Standard constructor
     */
    function CDSSearchengine() {}
  
    /**
     * Search for a phrase
     * 
     * note: you can draw your own search-form and add a path-variable. with the
     * path variable you can limit the search to url paths like www/cars/.. or www/bikes ...
     * 
     * @param string Search Phrase
     * @param integer Number of search results to diplay on one page.
     * @param String OPtion for search. Allowed are "any", "exact" and "start"
     */
    function search($searchPhrase, $limit=20, $options="any") {
    	global $c, $phpdig_words_chars;
	define('SEARCH_PAGE',$_SERVER['PHP_SELF']); 
	define('DISPLAY_DROPDOWN', 0);	
	$relative_script_path = $c["path"]."ext/phpdig";
	include_once $c["path"]."ext/phpdig/includes/config.php";
	include_once $c["path"]."ext/phpdig/libs/search_function.php";
   extract(phpdigHttpVars(
     array('query_string'=>'string',
           'refine'=>'integer',
           'refine_url'=>'string',
           'site'=>'string', // set to integer later
           'limite'=>'integer',
           'option'=>'string',
           'lim_start'=>'integer',
           'browse'=>'integer',
           'path'=>'string'
           )
     ),EXTR_SKIP);

	$results = phpdigSearch($id_connect, $searchPhrase, $options, $refine,
              $refine_url, $lim_start, $limite, $browse,
              $site, $path, $relative_script_path, 'array');
        return $results["results"];      
    }	
    
    /**
     * Draw search engine results 
     * @param mixed array created with the search function
     * @param string Text, which will be displayed, if no hits are found.
     */
    function drawResultList($resultArray, $nores = "Your search query returned no results.") {    	
    	if ($resultArray == "") {
    	  echo '<span class="search_text"><i>'.$nores.'</i></span>';	
    	}
    	for ($i=1; $i <= count($resultArray); $i++) {
    	  $result = $resultArray[$i];
    	  echo $result["page_link"];
    	  echo "<span class='searchinformation'> - [".$result["weight"]."%]</span> <br>";
    	  if ($result["text"] != " ") {
    	  	echo '<span class="searchtext">';
    	  	echo $result["text"]."<br>";
    	  	echo '</span>';
    	  }
    	  echo "<span class='searchinformation'>".$result["complete_path"]." - ".$result["filesize"]."k - ".$result["update_date"]."</span><br>";
    	  echo "<br>";
    	}
    }
    
    /**
     * Draws a simple search form
     * @param string Text on the submit button of search
     */
    function drawSearchForm($searchButton="Search") {
      $searchPhrase = value("searchphrase", "", "");
      $form = '<form name="searchform" method="post">'."\n";
      $form.= '  <input type="text" class="searchinput" length="32" name="searchphrase" value="'.$searchPhrase.'"> '."\n";
      $form.= '  <input type="submit" name="search" class="searchsubmit" value="'.$searchButton.'">'."\n";
      $form.= '</form>'."\n";
      echo $form;	
    }
    
  }
 
 
?>