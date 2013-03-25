<?php
/**********************************************************************
 *	N/X - Web Content Management System
 *	Copyright 2004 Sven Weih
 *
 *      $Id: stimportimages.php,v 1.1 2004/11/29 08:20:45 sven_weih Exp $ *
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
  class STImportImages extends Step {
  
  	var $counter;   	  
  
    /**
     * Standard constructor
     */
     function STImportImages() {              	
     }
  
     
     /**
      * check the archive
      */
     function execute() {
       global $lang, $errors;       
       $archfolder = $_SESSION["archivefolder"];
       if ($archfolder != "") {
		 $d = dir($archfolder);
		 $this->counter = 0;
		 while (false !== ($f = $d->read())) {
			if (is_file($d->path.'/'.$f)) {
			   $info = pathinfo($d->path.'/'.$f);
			   $ext2 = strtoupper($info["extension"]);
			   if ($ext2 == "JPG" || $ext2 == "JPEG" || $ext2=="PNG" || $ext2 == "GIF") {
			     createImageFromFile($d->path.'/'.$f, "", "", 1, value("folder", "NUMERIC", 1));
			     $this->counter++;
			   }
			}
		  }
		$d->close();
       }
       nxRMDir($d->path);
       $_SESSION["archivefolder"] = "";
       $this->add(new WZLabel($lang->get("nofi", "Number of files imported:") . $this->counter));       
       $this->forbidBack = true;
     }
  
  
  }
 
 ?>