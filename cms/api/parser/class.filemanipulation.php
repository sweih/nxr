<?php

 /**
  * Office Import functions
  * @package Tools
  * @subpackage Import
  */
  
/**********************************************************************
 *	Parts of this file have been developed by
 * 	Robert Lemke <rl@robertlemke.com>
 * 	Kasper Skårhøj <kasper@typo3.com>
 *	for Typo3 (www.typo3.com)
 *	These parts of sourcecode are Copyright (C) 2003 by Robert Lemke or Kaspar Skårhøj
 *	and were released under GNU GPL.
 *
 *	N/X - Web Content Management System
 *
 *	Copyright 2004 Sven Weih
 *
 *      $Id: class.filemanipulation.php,v 1.2 2004/04/02 19:41:40 sven_weih Exp $ *
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

// Original copyright notice for typo3 script.
/***************************************************************
*  Copyright notice
*  
*  (c) 2003 Robert Lemke (rl@robertlemke.com)
*  All rights reserved
*
*  This script is part of the TYPO3 project. The TYPO3 project is 
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
*  (at your option) any later version.
* 
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
* 
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/

  /**
   * Class for importing, exporting and working on files on hdd
   */
   class FileManipulation {
   
     var $filename;
     var $filecontent;
     var $extension;	
     
    /**
     * Standard constructor
     * @param string path to the file on harddisk.
     */
     function FileManipulation($filename) {
       $this->filename = $filename;
       $this->loadFile();       	
       $this->extension = $this->getFileExtension();
     }
     
     
     /**
      * Saves the content of the file to class-variable filecontent.
      */
     function loadFile() {
     	$this->filecontent = null;
     	if (@file_exists($this->filename)) {
     	  $fp = @fopen($this->filename, "r");
  	  while (!feof($fp)) $this->filecontent .= fgets($fp, 128);
	  @fclose ($fp);   			    	
        }      	
     }
     
     /**
      * Returns the extension (type) of the file
      */
      function getFileExtension() {
        $result = pathinfo($this->filename);
        return strtolower($result["extension"]);	     	
      } 
      
      
      	/**
	 * Processes the XML structure for open tags and 'indents' them in the array
	 * 
	 * @param	[type]		$officeBody: ...
	 * @return	[type]		...
	 */
	function indentSubTags($officeBody)	{
		$newStruct=array();
		$subStruct=array();
		$currentTag='';
		$currentLevel=0;
		reset($officeBody);
		while(list($k,$v)=each($officeBody))	{
			if ($currentTag)	{
				if (!strcmp($v['tag'],$currentTag))	{	// match...
					if ($v['type']=='close')	$currentLevel--;
					if ($v['type']=='open')		$currentLevel++;
				}
				if ($currentLevel<=0)	{	// should never be LESS than 0, but for safety...
					$currentTag='';
					$subStruct['type']='complete';
					$newStruct[]=$subStruct;
				} else {
					$subStruct['subTags'][]=$v;
				}
			} else {	// On root level:				
				if (strstr(',complete,cdata,', ','.$v['type'].','))	{
					$newStruct[]=$v;
				}
				if ($v['type']=='open')	{
					$currentLevel=1;	
					$currentTag = $v['tag'];
					
					$subStruct=$v;
					$subStruct['subTags']=array();
				}
			}
		}
		return $newStruct;
	}
	
	/**
	 * Also indents open tags, but does so recursively to a certain number of levels
	 * 
	 * @param	[type]		$officeBody: ...
	 * @param	[type]		$depth: ...
	 * @return	[type]		...
	 */
	function indentSubTagsRec($officeBody,$depth=1)	{
		if ($depth<1)	return $officeBody;		
		$officeBody = $this->indentSubTags($officeBody);

		if ($depth>1)	{
			reset($officeBody);
			while(list($k,$v)=each($officeBody))	{
				if (is_array($officeBody[$k]['subTags']))	{
					$officeBody[$k]['subTags'] = $this->indentSubTagsRec($officeBody[$k]['subTags'],$depth-1);
				}
			}
		}
		return $officeBody;
	} 
	
		/**
	 * Returns the value of an element ready for output in HTML
	 * 
	 * @param	[type]		$v: ...
	 * @return	[type]		...
	 */
	function pValue($v)	{
		$v = str_replace(
			array("â€œ","â€?","â€™","â€“","â€¦"),
			array('"','"','´','–','...'),
		$v);
		$v = htmlentities(utf8_decode($v));
		return $v;
	}	

	/**
	 * Wrapping a string.
	 * Example: $content = "HELLO WORLD" and $wrap = "<b> | </b>", result: "<b>HELLO WORLD</b>"
	 * 
	 * @param	string		The content to wrap
	 * @param	string		The wrap value, eg. "<b> | </b>"
	 * @param	string		The char used to split the wrapping value, default is "|"
	 * @return	string		Wrapped input string
	 * @see noTrimWrap()
	 */
	function wrap($content,$wrap,$char='|')	{
		if ($wrap)	{
			$wrapArr = explode($char, $wrap);
			return trim($wrapArr[0]).$content.trim($wrapArr[1]);
		} else return $content;
	}

	/**
	 * Returns the left or right part of a wrap
	 * 
	 * @param	[string]		$wrap: the wrap to be exploded, must be separated by |
	 * @param	[boolean]		$part: 0=left, 1=right
	 * @return	[string]		Part of the wrap
	 */
	function getWrapPart ($wrap, $part) {
		if ($wrap) {
			$wrapArr = explode('|', $wrap);
		}
		return $part ? trim($wrapArr[1]) : trim($wrapArr[0]);		
	} 
	
		/**
	 * **********************************************************************
	 * Traverses into an array until it finds $tag and then returns its value
	 * 
	 * @param	[array]		$sourceArray: the subparts
	 * @param	[string]		$tag: the tag you're looking for (e.g. 'W:T')
	 * @param	[integer]		$level: Don't set this
	 * @return	[array]		...
	 */
	function traverseToTag ($sourceArray, $tag, $level=0) {
		if ($level < 1000 && is_array ($sourceArray)) {		// you never know ...
			foreach ($sourceArray as $value) {
				if ($value['tag'] == $tag) {
					return $value['value'];	
				}
			}
			return $this->traverseToTag ($value['subTags'], $tag, $level+1);
		}
	}  	
   }

?>