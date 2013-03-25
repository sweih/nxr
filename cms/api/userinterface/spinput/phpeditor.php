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
	/**
	* Textareabox for form-layout.
	* @version 1.00
	* @package WebUserInterface
	*/
	class PHPEditor extends WUIObject {
		var $action;
		var $iwidth;
		var $params;
		var $rows;
		var $filename;

		/**
		   * standard constructor
		   * @param string the name of the Input, used in the html name property
		   * @param string sets a value that will be displayed
		   * @param string sets the styles, which will be used for drawing
		   * @param integer $rows Height of the textbox in lines
		   * @param string $params use to format the layout-item. the text will be entered into the textarea html-tag
		   * @param integer $width width in pixel
		   * @param string $action Javascript-action, that ist to be done when changing text.
		   * @param integer $cells Cellspan of the element in Table.
		   */
		function PHPEditor($name, $filename, $style, $rows = 25, $params = "", $width = 600, $action = "", $cells = 2) {
			global $c, $page_state;
			WUIObject::WUIObject($name, "", $value, $style, $cells);
            $this->filename = $filename;
			$this->action = $action;
			$this->iwidth = $width;
			$this->params = $params;
			$this->rows = $rows;
			
			// get information from file.
			$fp = @fopen($c["devpath"].$filename, "r");

			if ($page_state == "processing") {
				$this->value=value($name);
				global $c_magic_quotes_gpc;

				$this->value = str_replace("textarea", "_text_area_", $this->value);
			} else {
			  if ($fp != "") {
				  while (!feof($fp)) $this->value .= fgets($fp, 128);
				  @fclose ($fp);
			  } else {
			    $out = "<?PHP\n";
			    $out.= "  require_once \"nxheader.inc.php\";\n";
			    $out.= '  $cds->layout->htmlHeader(); '."\n\n";
			    $out.= '  //e.g. get content: echo $cds->content->get("content name"); refer to CDS-API-Doc'."\n\n";
			    $out.= "  require_once \"nxfooter.inc.php\";\n";
			    $out.= "?>";	
			    $fp = @fopen($c["devpath"].$filename, "w+");
			    if ($fp !="") {
   			  	  @fwrite($fp, $out);
			  	  @fclose($fp);
			    }
			    $this->value = $out;
			  }
			}			
		}
	
		
		/**
		 * Write the template back to file
		 */
		 function process() {
		   global $page_state, $errors, $c;
		   if ($page_state == "processing" && $errors=="") {
		     	$fp = @fopen($c["devpath"].$this->filename, "w+");
			    if ($fp !="") {
			      $this->value = stripslashes($this->value);
   			  	  @fwrite($fp, str_replace('_text_area_','textarea',$this->value));
			  	  @fclose($fp);
			    }
		   		
		   }
		 }
		 
	/**
	  * Draws the layout-element
	  */
		function draw() {
			$output = WUIObject::std_header();

			$output .= "<textarea name=\"$this->name\" id=\"$this->name\" wrap=\"off\" style=\"width:" . $this->iwidth . "px;font-family:courier;\" $this->params rows=\"$this->rows\"";

			if ($this->action != "") {
				$output .= " onChange=\"$this->action\"";
			}

			$output .= ">";
			$output .= htmlentities($this->value);
			$output .= "</textarea>";
			$output .= WUIObject::std_footer();
			echo $output;
			return $this->columns;
		}
	}
?>