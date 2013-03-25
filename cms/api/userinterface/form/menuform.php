<?
    /**********************************************************************
     *    N/X - Web Content Management System
     *    Copyright 2002-2004 Sven Weih
     *
     *    This file is part of N/X.
     *    The initial has been setup as a small diploma thesis (Studienarbeit) at the FZI.
     *    It was be coached by Prof. Werner Zorn and Dipl.-Inform Thomas Gauweiler.
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
     * Draw a form that acts as big menu.
     * @package WebUserInterface
     */
    class MenuForm extends Form {

      	var $table = "";
      	var $pkcol = "";
      	var $displayColumns;
      	var $colTitles;
      	var $cond = "1";
      	var $rows = "";
      	var $newAction="";
      	var $order = "";
      	var $orderdir ="";
		var $page=1;
		var $recordsPerPage = 20;
		var $filterColumns;
		var $currentFilterColumn;
		var $searchphrase;
		var $searchdrawn;
		var $searchcolumn;
		var $buttonbar;
		var $showall;
      
      /**
       * Standard constuctor
       * @param string Headline of the form
       * @param array Headlines of the columns
       * @param string Name of table to query in
       * @param string Name of PK of Query-Table
       * @param array Array of Columns to display
       * @param string Filter - Condition for data
       * @param number Number of rows to display.
       */
      function MenuForm($title, $colTitles, $table, $pkcol, $displayColumns, $cond, $rows=20) {
      	global $lang, $c, $sid, $page;
        Form::Form($title);
        $this->colTitles = $colTitles;
        $this->table = $table;
        $this->pkcol = $pkcol;
        $this->displayColumns = $displayColumns;
        $this->cond = $cond;
        $this->rows = $rows;
        $this->name = "form1";
        $this->editAction = docRoot();
        $this->orderdir = strtoupper(initValue("dir", $table."_dir", "ASC"));
        $this->order = initValue("order", $table."_order",$displayColumns[0]);         
        $this->page = initValue("page", $table."_page", 1);
        
        $page->setJS("TOGGLETD");

        // $this->rows = countRows($this->table, $this->pkcol, $this->cond);
        
        $this->showall = initValue("showall", $table."_showall", "no");
        
        if ($this->showall == "yes") {
        	$this->recordsPerPage = 1000;
        }
          
			$this->init();		
			$this->buttonbar = new Buttonbar("launch", "standard", count($this->colTitles));
		
      }
      
    	/**
    	 * Initialize the search function
    	 */
        function init() {
    	     // check and initialize filter variables.
			$this->searchphrase = initValueEx("searchphrase", $table."searchphrase", "", "");
			$this->searchcolumn = initValue("searchcolumn", $table."searchcolumn", "", "");
    	}
      
      	/**
		 * Add a rule to the filter. Rules are columns, which one may filter for. Note that you may
		 * filter for column and present presentation_column on the output menu.
		 * @param string Text to be displayed in dropdown
		 * @param string Column, which you want to filter for		
		 * @param string optional statement to join tables with each other. 
		 */
		function addFilterRule($label, $column, $join="", $tables="") {
			if ($this->currentFilterColumn == "")
				$this->currentFilterColumn = $column;			
			$next = count($this->filterColumns);			
			$this->filterColumns[$next][0] = $label;
			$this->filterColumns[$next][1] = $column;	
			$this->filterColumns[$next][2] = $join;		
		}
		
		function getFilterJoin() {
			$join = "";
			for ($i=0; $i<count($this->filterColumns); $i++) {
				if (($this->filterColumns[$i][2] != "") && ($this->searchcolumn == $this->filterColumns[$i][1])) {
					$join .= " ".$this->filterColumns[$i][2]." ";
				}
			}
			if ($this->searchlive == "hide") {
		 		$join .= " LEFT JOIN state_translation st ON st.OUT_ID = ca.ARTICLE_ID WHERE st.EXPIRED = 1 ";
		 	}
			return $join;
		}
		
		/**
		 * Builds the SQL-String for filtering data
		 */
		 function getFilterSQL() {
			$result = "";		 	
		 	if ($this->searchphrase != "") {
		 	    $search = strtoupper($this->searchphrase);
		 		$result .= " AND ( UPPER($this->searchcolumn)  LIKE '%$search%' )";
		 	}
		 	return $result;
		 }
		 
		 /**
		  * count rows after filtering etc.
		  * @param boolean optional set to true if you want to override the cached value
		  * @returns integer number of rows
		  */
		 function countRows($force=false) {
		 	if (($this->rows == "") || $force) {
		 		$rows = countRows($this->table, $this->pkcol, $this->cond);
		 		$this->rows = $rows;
		 	}
		 	return $this->rows;
		 }


      /**
       * Draw Form Contents
       */
      function draw_contents() {
         global $lang, $sid, $c;
         echo tableStart();
         $this->buttonbar->insert("new", $lang->get("new"), "button", "document.location.href='".$c["docroot"].$this->newAction."';", "navelement");
         $this->buttonbar->draw();          
         echo "</tr>";
         $this->drawPaging();
         $this->drawColumnHeaders();
         $this->drawRows();
         $this->drawPaging();
         echo tableEnd();
      }

      /**
       * draw the columns
       */
      function drawRows() {
        global $c, $sid;
        $data = $this->getRows();
        for ($row=0; $row < count($data); $row++) {        
        echo "<tr class=\"grid\" onMouseOver='this.style.backgroundColor=\"#ffffcc\";' onMouseOut='this.style.backgroundColor=\"#e8eef7\";' onClick='document.location.href=\"".$this->editAction."?sid=".$sid."&go=update&oid=".$data[$row][0]."\";'>";
          for ($i=1; $i< count($data[$row]); $i++) {          
            $style=' style="border-bottom:1px solid #cccccc;cursor:pointer;padding-top:2px; padding-bottom:2px;" ';            
            if ($data[$row][$i] <> "") {
              echo "<td $style>".$data[$row][$i]."</td>";
             } else {
             	 echo "<td $style>&nbsp;</td>";
            }

          }
          echo "</tr>";
        }
      }

      /**
       * query the rows from the database
       */
      function getRows () {
         global $db;
         $result = array();
         $sql = "Select ".$this->pkcol.", ".implode(",", $this->displayColumns)." FROM ".$this->table.$this->getFilterJoin()." WHERE ".$this->cond.$this->getFilterSQL()." ORDER BY ".$this->order." ".$this->orderdir." LIMIT ".(($this->page - 1) * $this->recordsPerPage).",".$this->recordsPerPage;
         $query = new query($db, $sql);
          while ($query->getrow()) {
                 $tmp = array();
                 array_push($tmp, $query->field($this->pkcol));
                 for ($i=0; $i<count($this->displayColumns); $i++) {
                      array_push($tmp, $query->field($this->displayColumns[$i]));
                 }
                 array_push($result, $tmp);
         }
         return $result;
      }

      
      /**
       * Draw the search form
       */
      function drawSearchForm() {
      		global $sid, $lang;
      		$this->searchdrawn = true;
      		$useadvancedsesarch = false;
      		// echo $this->searchlive;
      		// echo $this->searchexpired;
      		// echo $this->searchmissing;
      		if (($this->searchlive != "") || ($this->searchexpired != "") || ($this->searchmissing != ""))
      			$useadvancedsesarch = true;
      		echo tableStart();
	      		echo "<tr><td class=\"standardwhite\" colspan=2>";
	        	echo $lang->get("filter_rule");
	      		echo ' <input type="text" size="32" style="width:120px;"  name="searchphrase" value="'.$this->searchphrase.'">&nbsp; ';
	      		echo $lang->get("filter_column")."&nbsp;&nbsp;";
	      		echo '<select name="searchcolumn" style="width:140px;">';
	      		for ($i=0; $i < count($this->filterColumns); $i++) {
					($this->searchcolumn == $this->filterColumns[$i][1])? $selected = " selected":$selected="";
	      			echo '<option value="'.$this->filterColumns[$i][1].'" '.$selected.'>'.$this->filterColumns[$i][0].'</option>';
	      		}
	      		echo '</select>&nbsp;';
	      		$submitbutton = new Button("filter", $lang->get("search2", 'Search'), "navelement", "submit", "", "form1");
				$submitbutton->draw();
				echo "&nbsp;&nbsp;";
				$clearbutton = new LinkButton("clearsearch", $lang->get("search_clear", "Reset Filter"), "navelement", "submit", "document.form1.searchphrase.value=''", "form1");
				$clearbutton->draw();
				retain("filter", "");
				retain("clearsearch", "");    	
				echo "</td></tr>";

			echo tableEnd();
      }
      
      /**
       * Draw the buttons for paging and the search form..
       */
      function drawPaging() {
      	global $sid, $lang;
      	$doc = doc();
      	echo "<tr>";
      	echo '<td valign="top" colspan="'.(count($this->displayColumns)-1).'"class="standardwhite">';
      	if (!$this->searchdrawn && count($this->filterColumns) >0) {      		
			    $this->drawSearchForm();
      		echo '</td>';
      		echo "<td valign='top'  style=\"padding-top:6px;\"  align=\"right\">";
      	} else {
      		echo "<td valign='top' colspan=\"".(count($this->displayColumns))."\" style=\"padding-top:6px;\" align=\"right\">";
      	}      	
      	$pageStart = ($this->page - 1) * $this->recordsPerPage;
      	$pageEnd = ($this->page) * $this->recordsPerPage;
      	$pages = ceil($this->countRows(true) / $this->recordsPerPage);      	      	
      	
      	// draw the buttons
      	if ($this->page != 1) {
      	  $out = '<a href="'.$doc."?sid=$sid&page="."1".'">&laquo;</a>';	
      	} else {
      	  $out = '&laquo;';	
         }

		 		$out.="&nbsp;&nbsp;";

         if ($this->page > 1) {
      	  $out.= '<a href="'.$doc."?sid=$sid&page=".($this->page-1).'">&lsaquo;</a>';	
      	} else {
      	  $out.= '&lsaquo;';
         }
         
         $out.="&nbsp;&nbsp;";
         
         // draw a button for displaying all pages
         if ($this->showall == "yes") {
         	$out .= '<a href="'.$doc."?sid=$sid&page=1&showall=no".'">'.$lang->get("show_pagewise", "pagewise")."</a>";
         } else {
         	$out .= '<a href="'.$doc."?sid=$sid&page=1&showall=yes".'">'.$lang->get("show_all", "all")."</a>";
         	$out.= '&nbsp;&nbsp;'.$this->page.'&nbsp;'.$lang->get('of', 'of').'&nbsp;'.$pages;
         }         
         
         $out.="&nbsp;&nbsp;";
         
         if ($this->page < $pages) {
      	  $out.= '<a href="'.$doc."?sid=$sid&page=".($this->page+1).'">&rsaquo;</a>';	
      	} else {
      	  $out.= '&rsaquo;';	
         }
          
         $out.="&nbsp;&nbsp;";        
         if ($this->page != $pages) {
      	  $out.= '<a href="'.$doc."?sid=$sid&page=".$pages.'">»</a>';	
      	} else {
      	  $out.= '&raquo;';	
         }
      		
      	echo $out.'<br><br>';        	
      	echo "</td></tr>";
      	
      }
      
           
      /**
       * Draw the Headlines of the column
       */
      function drawColumnHeaders() {
         global $sid;
         echo "<tr>";
         $sort = doc();
         for ($i=0; $i < count($this->colTitles); $i++) {
           $arrow="";
           $dir = "ASC";
           if ($this->order == $this->displayColumns[$i]) {
           		if ($this->orderdir == "ASC") {
           			$dir="DESC";
           			$arrow.=drawSpacer(4,1).drawImage("down.gif");	
           		} else {
          			$arrow.=drawSpacer(4,1).drawImage("up.gif");	
           		}
           }
           echo "<td class=\"gridtitle\" $style valign=\"middle\"><a href=\"$sort?sid=$sid&dir=$dir&order=".urlencode($this->displayColumns[$i])."\">".$this->colTitles[$i]."</a>$arrow</td>";
         }
         echo "</tr>\n";
      }

     /**
      * Draw the form
      */
      function draw() {          
          $this->draw_header();
          $this->draw_body();
          $this->draw_footer();         
      }
    }


?>