<?php
/**********************************************************************
 *	N/X - Web Content Management System
 *	Copyright 2004 Sven Weih
 *
 *      $Id: stcheckarchive.php,v 1.1 2004/11/29 08:20:45 sven_weih Exp $ *
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
   * Step module for checking the archive data
   */
  class STCheckArchive extends Step {
  
  	var $counter;
  	   	  
  
    /**
     * Standard constructor
     */
     function STCheckArchive() {       
        global $lang, $errors;
       
       $archfolder = $_SESSION["archivefolder"];
       if ($archfolder != "") {
		 $d = @dir($archfolder);
		 $this->counter = 0;
		 if (is_object($d)) {
		   while (false !== ($f = $d->read())) {
			  if (is_file($d->path.'/'.$f)) {
			    $this->counter++;
			  }
		    }
		  $d->close();
	    }
       	if ($this->counter > 0) {
       	  $this->add(new WZLabel($lang->get("num_files", "Number of files found in archive: ") . $this->counter));	
       	} 
       } 	
     }
  
     
     /**
      * check the archive
      */
     function check() {
       global $lang, $errors;
       
       $archfolder = $_SESSION["archivefolder"];
       if ($archfolder == "") {
       	 $this->add(new WZLabel($lang->get("archerr", "The archive could not be properly imported. Check for php_zip extension!")));
       	 $errors.="-ARCHIMPERROR";
       	 $this->css = "error";
       } else if ($this->counter==0) {
       	 $this->add(new WZLabel($lang->get("archempty", "The archive seems to empty or could not be unzipped successfully.")));
       	 $errors.="-ARCHEMPTY";
       	 $this->css = "error";      		
       	}	
     }
  }
 
 ?>