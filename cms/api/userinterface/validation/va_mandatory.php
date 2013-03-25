<?
/**********************************************************************
 *	N/X - Web Content Management System
 *	Copyright 2002 Sven Weih, FZI Research Center for Information Technologies
 *	www.fzi.de
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

  class VA_MANDATORY extends Validator  {
  
  	
  	/**
  	 * Standard Constructor
  	 */ 	 
  	function VA_MANDATORY() {
  	  global $lang;
  	  Validator::Validator("MANDATORY", $lang->get("MANDATORY"), ""); 
  	}	
  	
  	/**
  	 * Function called for validating input fields
  	 * @param string Name of the table of the input field
  	 * @param string Name of the column of the input field
  	 * @param string Filter, which is to be used for selecting a set of rows for validating in.
  	 * @param string Value, which is currently entered by the user
  	 * @param string Value, which was in the db when starting editing.
  	 * @param string Datatype of the value
	 * @param array Assosiative Array with additional parameters, like array["info"] = "check this"
  	 */
  	function validate($table="", $column="", $filter="", $value="", $oldvalue="", $datatype="", $paramArray=null) {
  	  if (($value=="") || ($value=="-1" && $datatype=="NUMBER")) {
  	  	return $this->throwException();
  	  } else {
  	  	return "";
  	  }
  	}
  	  	  
  }