<?php

 /**
  * HTML Import functions
  * @package Tools
  * @subpackage Import
  */
  
/**********************************************************************
 *
 *	N/X - Web Content Management System
 *
 *	Copyright 2004 Sven Weih
 *
 *      $Id: importhtml.php,v 1.1 2004/02/03 16:44:27 sven_weih Exp $ *
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
  * Class for importing html files
  */
  class ImportHTML extends FileManipulation {
  	
  /**
   * standard constructor
   * @param string filename
   */
   function ImportHTML($filename) {
   	FileManipulation::FileManipulation($filename);
   }
  	

   /**
    * Returns the body of the html document
    */  
   function getParsedContent() {
    $fcupper = strtoupper($this->filecontent); 
   	$bodystart_start = strpos($fcupper, "<BODY");
   	$bodystart_end = strpos($fcupper, ">", $bodystart_start);
   	$bodyend = strpos($fcupper,"</BODY>", $bodystart_end);
   	return substr($this->filecontent, $bodystart_end+1, $bodyend - $bodystart_end-1);   		
   }
   
  }
 
 ?>