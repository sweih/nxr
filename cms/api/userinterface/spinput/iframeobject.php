<?php
/**
 * Base class for IFrame Widgets
 * @package Userinterface
 * @subpackage Special Widgets
 */


/**********************************************************************
 *	N/X - Web Content Management System
 *	Copyright 2004 Sven Weih
 *
 *      $Id: iframeobject.php,v 1.3 2004/05/07 09:59:19 sven_weih Exp $ *
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
    * Abstract base class for creating Widgets who are configured in an IFRAME
    */
   class IFrameObject extends WUIInterface {
   
     var $name;
     var $callscript;
     var $frame;
     var $width;
     var $height;
     var $value;
     var $css;
     var $errortext;
     var $columns;
     
     /**
      * Standard constructor
      * @param string name to use in form for iframe
      * @param string name of the script to call for iframe
      * @parsm string CSS-Class to use
      * @param integer width of the iframe
      */
      function IFrameObject($name, $value, $callscript, $css="standard", $width="250", $height="250", $columns="1") {        
        $this->name = $name;
        $this->frame = "f".$this->name;
        $this->callscript=$callscript;
        $this->width = $width;
        $this->height = $height;
        $this->value = $value;
        $this->css = $css;
        $this->columns = $columns;
      }
      
      function check() {
        global $errors, $lang;        
        if ($this->value==-1) {
          $this->css="error";
          $errors.="-MANDATORY";
          $this->errortext = $lang->get("MANDATORY");
        }	
      }
      
      /**
       * Draw the iframe selector
       */
      function draw() {
        global $c, $sid;
        echo '<td class="'.$this->css.'" colspan="'.$this->columns.'">';
        echo '<input type="hidden" id="'.$this->name.'" name="'.$this->name.'" value="'.$this->value.'">';
        $add = "";
        if ($this->errortext !="") {
          echo $this->errortext;
          $add = "&error=on";
        }
        echo '<iframe name = "'.$this->frame.'" src="'.$c["docroot"]."api/userinterface/spinput/".$this->callscript."?sid=$sid&callback=".$this->name.'&value='.$this->value.$add.'&width='.$this->width.'" style = "width:'.$this->width.'px; height:'.$this->height.'px; border: 0px solid #cccccc;" frameborder = "0" scrolling="no"></iframe>';
        echo '</td>';
        return 1;
      }         
   }
   
   /**
    * Draw the header for an Iframe-Object
    */
   function drawIFOHeader($stdstyle = "standardlight", $noform=false) {
			global $c, $callback, $sid, $error, $style;
			$error = value("error");
			if ($error=="0") {
				$style=$stdstyle;
			} else {
			   $style = "error";	
			}
			echo "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.0 Transitional//EN\">\n";
			echo "<html>\n";
			echo "<head>\n";
			echo " <title>IFO</title>\n";
			echo "<META HTTP-EQUIV=\"Pragma\" content=\"no-cache\">\n";
			echo "<META HTTP-EQUIV=\"Cache-Control\" content=\"no-cache, must-revalidate\">\n";
			echo "<META HTTP-EQUIV=\"Expires\" content=\"0\">\n";

			echoCSS();

			echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"".$c["docroot"]."ext/jscalendar/cal.css\">\n";
			echo "<script type=\"text/javascript\" src=\"".$c["host"].$c["docroot"]."ext/jscalendar/calendar.js\"></script>\n";
			echo "<script type=\"text/javascript\" src=\"".$c["host"].$c["docroot"]."ext/jscalendar/lang/calendar-en.js\"></script>\n";
			echo "<script type=\"text/javascript\" src=\"".$c["host"].$c["docroot"]."ext/jscalendar/calendar-setup.js\"></script>\n";			
						
			echo "</head>\n";
			echo "<body leftmargin=\"0\" topmargin=\"0\" marginheight=\"0\" marginwidth=\"0\" class=\"$style\">\n";
			if (! $noform) {
			  echo "<form name=\"ifoform\" method=\"POST\" action=\"".doc()."\" enctype=\"multipart/form-data\">";
			  $sid = value("sid", "NOSPACES");
			  $callback = value("callback", "NOSPACES");
			  retain("sid", $sid);
			  retain("callback", $callback);
			  echo tableStart();
			}
   }
   
   
   /**
    * Draw the footer for an IFrame-Object
    */
   function drawIFOFooter($noform=false) {
   	if (! $noform) {   	
   	  echo tableEnd();
   	  echo "</form>";
   	}
   	echo "</body>";
   	echo "</html>";
   }
   
   /**
    * Returns javascript code for storing a value (localname) in the parent windows value-field(parentName)
    * @param string Name of on input in parent Window
    * @param string Name of an input in local iframe
    */
   function drawIFOSave($parentName, $localName) {
   	return "parent.document.getElementById('$parentName').value = document.getElementById('$localName').value;";
   }
   
?>