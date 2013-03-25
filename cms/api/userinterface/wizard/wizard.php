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
 *      $Id: wizard.php,v 1.2 2004/11/29 08:20:45 sven_weih Exp $ *
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
  * Wizards are based on forms. They are thought to create easy to use
  * Workflows, step by step. Each wizard consists of steps. Each steps defines
  * its own widgets and actions
  */
  class Wizard extends Form {
      
    var $titleText;
    var $activeStep;
    var $displayStep;
    var $firstRun;
    var $finished = false;
    
    /**
     * Standard constructor
     * @param string Title of the wizard
     */
    function Wizard($title) {
      Form::Form($title, "", "wizard");	    	
    }	
    
    /**
     * Set the introductionText of the wizard
     */
   function setTitleText($titleText) {
     $this->titleText = $titleText;  	
   }

 	
 	/**
	 * Draw the wizard
	 */
	function draw() {
	   global $errors, $lang;
		if ($errors != "") {
			$this->addToTopText($lang->get("procerror"));
			$this->setTopStyle("headererror");			
		}
		$this->draw_header();
		$this->draw_body();
		$this->draw_footer();
	}
	
	 /**
     * Add a step to the wizard. The order of the steps is the order, in which the wizard
     * will go through the script. Each step automatically gets its own ID.
     * @param object Page, which is to be added to the script.		
     */
     function add(&$step) {
     	 $this->container[count($this->container)+1] = &$step;
     	 $step->parent = &$this; 
     }
     
    /**
     * Returns the step on a certain position
     * @param integer $pos Position of the page in the installer
     */
    function &getPage($pos) {
     	if (count($this->container)<$pos) {
     	  return  $this->container[$pos];
     	} else {
     	  return null;	
     	}
    }
    
    /**
     * Initialize the wizard
     */
    function check() {
    	 global $errors, $lang;
    	 $id = value("step");     	 
       $do = value("pdo");
        
       // determine page to display and page, which the data needs to processed from.      
       $this->firstRun = false;
       if ($do=="" || $do==$lang->get("back", "Back")) $this->firstRun=true;
     	 if ($do==$lang->get("back", "Back")) $id--;
     	 if ($id == "" || $id==0 || $id=="0") $id = 1;
       
       $this->activeStep = $id;
       $this->displayStep = $id;

       if ($do == $lang->get("next", "Next")) {
         $this->container[$this->activeStep]->check();
         if ($errors=="") { 
            $this->displayStep++;
            $this->container[$this->displayStep]->execute();
          }
        }
      	
     	  $amountPages = count($this->container);
     	  if ($this->displayStep > $amountPages) $this->displayStep = $amountPages;
     	  if ($this->activeStep > $amountPages) $this->activeStep = $amountPages;     	
    	}
    
    	/**
		* Writes the HTML-Code for the contents inside the form
		*/
		function draw_contents() {
			echo "<table width=\"100%\" class=\"white\" cellpadding=\"0\" cellspacing=\"0\" align=\"center\">\n";
			echo "<tr>";
			$cl1 = new Cell("cl1", "border", 1, ceil($this->width / 3), 1);
			$cl2 = new Cell("cl2", "border", 1, ceil(($this->width / 3) * 2), 1);
			$cl1->draw();
			$cl2->draw();
			echo "</tr>";
			$this->draw_toptext();			
			echo "<tr valign=\"top\"><td colspan=\"2\">";        
			$this->container[$this->activeStep]->draw($this->firstRun);  
			echo "</td></tr>";	
			echo '<tr><td colspan="2">&nbsp;</td></tr>';
			$this->draw_buttons();
			echo '<tr><td colspan="2">&nbsp;</td></tr>';
			echo "</table> "; 			
		}
		
		/**
		 * Draws the next and previous buttons.
		 */
		function draw_buttons() {
		  global $lang;
		  if (! $this->finished) {
  			 echo "<tr><td align=\"right\" colspan=\"2\">";
  			 echo '<input type="hidden" name="step" value="'.$this->activeStep.'">';
  			 retain ("pdo");
  			 echo drawSpacer(5, 5). "<br>";
  			 if (($this->displayStep >1) && (!$this->container[$this->activeStep]->forbidBack)) {
  			   $prevbutton = new ButtonInline("pdo", $lang->get("back", "Back"), "navelement", "submit", "", $this->name);
  			   echo $prevbutton->draw();
  			   echo "&nbsp;&nbsp;";			
  			 }
  			 if ($this->displayStep < count($this->container)) {
  			   $nextbutton = new ButtonInline("pdo", $lang->get("next", "Next"), "navelement", "submit", "", $this->name);
  			   echo $nextbutton->draw();			
  			 }
  			 echo "<br>" . drawSpacer(5, 5);
  			 echo "</td></tr>";
  		  }
		}		 		 				
  }
 
 ?>