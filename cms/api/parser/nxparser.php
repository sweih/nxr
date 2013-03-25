<?PHP
/**********************************************************************
 *
 *	N/X - Web Content Management System
 *
 *	Copyright 2004 Sven Weih
 *
 *      $Id: nxparser.php,v 1.1 2004/04/26 22:14:14 sven_weih Exp $ *
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
   * Parse texts for tags and replace them
   */
  class NXParser {
 	
  	var $startStr='{NX:';
  	var $endStr='}';
  	
  	/**
  	 * Constuctor
  	 */
  	function NXParser() {
  	}
  	
  	/**
  	 * Run the paraser with a text
  	 * @param string Text to parse
  	 * @return string parsed text
  	 */
  	function parseText($text) {  		
  		if (preg_match_all('/'.$this->startStr.'(.+?)'.$this->endStr.'/is', $text, $matches)) {
  		  $tags = $matches[1];
  		  foreach($tags as $tag) {
 			$attributes = explode(" ", $tag);
 			if (is_array($attributes)) {
 			  $attributes[0] = strtolower($attributes[0]);
 			  if (method_exists($this, $attributes[0])) {
 			    $attrStack = array();
 			    for ($i=1; $i<count($attributes); $i++) {                 
                  $posEquals = strpos($attributes[$i], '=');
                  $attr = trim(substr($attributes[$i], $posEquals+1, strlen($attributes[$i])));
                  $attr = str_replace('"', '', $attr);
                  $attrName = strtoupper(trim(substr($attributes[$i], 0, $posEquals)));                                          
                  $attrStack[$attrName] = $attr;
 			    } 				
 			    $text = str_replace($this->startStr.$tag.$this->endStr, $this->$attributes[0]($attrStack), $text);	 	
 			  }
 			}
 	     } 		
  	  }
  	  return $text;
  	}	
  }
?>