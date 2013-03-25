<?php
/**
 * Wizard API Library
 * @package Userinterface
 * @subpackage Wizard
 */


/**********************************************************************
 *	N/X - Web Content Management System
 *	Copyright 2004 Sven Weih
 *
 *      $Id: step.php,v 1.2 2004/11/29 08:20:45 sven_weih Exp $ *
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
   * Steps are used within wizards. A wizard contains out of any number of steps
   * You need to derive classes form this abstract step class for each step.
   */
  class Step extends Container {
  
    var $parent=null;
    var $title;
    var $explanation; 
    var $addDrawString="";   
    var $forbidBack = false; 
   
    
    /**
     * Standard constructor
     */     
    function Step() {}
      
    /**
     * Set the title of the step
     */
     function setTitle($title) {
       $this->title = $title;	
     }
     
     /**
      * Set the explanation of the step
      */
     function setExplanation($explanation) {
       $this->explanation = $explanation;	
     }
     
    /**
     * Add an string to output. use for iframes and so on.
     */
    function addToDrawString($string) {
     	$this->addDrawString.=$string;
    } 

   
    /**
     * Start processing the page. Is called internally, so you do not need to call by yourself.
     * @param boolean Flag, if page has already been displayed or not.
     */
    function draw($firstRun=false) {
      global $errors;
      $do = value("pdo", "NUMERIC");     

      if (! $firstRun) {    
         if ($do=="Next" && $errors=="") {
           if ($this->parent->activeStep < count($this->parent->container)) $this->parent->activeStep++;
            $this->parent->container[$this->parent->activeStep]->draw(true);
            return 0;	
         }       
      }
      $this->_draw();
    }
    
    /**
     * Create the HTML for the step
     */
    function _draw() {
    	global $lang;
    	echo tableStart();
    	echo '<td colspan="">'.drawSpacer(5,5).'</td>';
    	tr();
    	echo '<td colspan="3" class="informationheader">'.$this->parent->titleText.'</td>';		
		tr();
  	   echo '<td colspan="3">'.drawSpacer(12,12).'</td>';
    	tr();

    	echo '<td colspan="3" class="headbox">';
    	echo '<table width="100%" border="0" cellpadding="0" cellspacing="0"><tr>';
    	echo '<td width="70%"><b>'.drawSpacer(10,10).$this->title.'</b></td>';
    	echo '<td width="30%" align="right"><b>'.$lang->get("step", "Step")." ".$this->parent->displayStep." ".$lang->get('of')." ".count($this->parent->container).drawSpacer(10,10).'</b></td>';
    	echo '</tr></table></td>';
    	tr();
  	    echo '<td colspan="3">'.drawSpacer(10,10).'</td>'; 	   
    	tr();
		echo '<td width="45%" valign="top" class="informationheader">'.$this->explanation.'</td>';
		echo '<td width="20">'.drawSpacer(20,250).'</td>';
		echo '<td width="50%" valign="top" class="standardlight">';
		echo tableStart();
		for ($i=0; $i<count($this->container); $i++) {
		  $this->container[$i]->draw();
		  tr();	
		}
		echo "<td>".drawSpacer(5)."</td>";
		tr();
  	   echo '<td>'.$this->addDrawString.'</td>'; 
		echo tableEnd();
		echo '</td>';	   
    	tr();
  	   echo '<td colspan="3">'.drawSpacer(10).drawLine(590).'</td>'; 	   
    	tr();
		
    	echo tableEnd();
    }
    
    /**
     * Execute something
     */
    function execute() {
      // abstract method	
    }
  
  }
 
 ?>