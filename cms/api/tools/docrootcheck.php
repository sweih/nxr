<?php
/**********************************************************************
 *	N/X - Web Content Management System
 *	Copyright 2004 Sven Weih
 *
 *      $Id: docrootcheck.php,v 1.1 2004/10/13 15:00:05 sven_weih Exp $ *
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
  * this class checks, if a path/docroot is available on a webserver
  */
  class DocrootCheck {
  
    var $docroot;
    
  /**
   * Standard constructor
   * @param string Address of the webpage to check.
   */
    function DocrootCheck($docroot) {
  	  $this->docroot = $docroot;
  	  $this->execute();  	
    }	
    
    /**
     * perform the docroot check
     */
    function execute() {
    	echo '<br/>';
    	$fp = @fopen($this->docroot, "r");
		if ($fp != "") {
			echo '<font color="green">'."Docroot ".$this->docroot." found.</font></br>";
		} else {
			echo '<font color="red">'."Docroot ".$this->docroot." not found.</font></br>";
		}
		echo '<br/>';
    }
  	
  	
  }
  
 
 ?>