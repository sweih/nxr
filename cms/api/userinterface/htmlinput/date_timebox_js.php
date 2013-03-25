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
	* Create dropdown-boxes, for entering a date and time with automatic checking in form-layout.
	* @version 1.00
	* @package WebUserInterface
	*/
	class DateTimebox extends WUIObject {

		/**
		  * String
		  */
		var $formName;

		/**
		   * standard constructor
		   * @param string the name of the Input, used in the html name property
		   * @param string sets a value that will be displayed
		   * @param string sets the styles, which will be used for drawing
		   * @param string You must manually enter the name of the form here for JS reasons.
		   * @param integer $cells Cellspan of the element in Table.
		   */
		function DateTimebox($name, $value, $style, $formname = "form1", $cells = 1) {
			WUIObject::WUIObject($name, "", $value, $style, $cells);
			$this->formName = $formname;
		}

		/**
		  * Draws the layout-element
		  */
		function draw() {
			global $c, $lang;
			$output = WUIObject::std_header();
			$date = substr($this->value, 0, 10);
			$time = substr($this->value, 11,5);
			$showDate = str_replace("-", "/", $date);
			if ($showDate=="") $showDate = '-------';
			$output.="<span style=\"width:90px;\" id=\"".$this->name."div\">".$showDate."</span>";
			$output.="<input type=\"hidden\" id=\"".$this->name."\" name=\"".$this->name."\" value=\"".$date."\"/>";
			$output.='<a href="#" onclick="document.getElementById(\''.$this->name.'\').value=\'\';document.getElementById(\''.$this->name.'div\').innerHTML=\'--------\';return false;">'.drawImage('delete.gif', 12, 12).'</a>';
			$output.='&nbsp;&nbsp;';
			$output.="<image src=\"".$c["docroot"]."ext/jscalendar/img.gif\" id=\"".$this->name."trigger\" style=\"cursor:hand;\">";
			$output.='<script type="text/javascript">';
			$output.='Calendar.setup({displayArea: "'.$this->name.'div",inputField : "'.$this->name.'",  button : "'.$this->name.'trigger"});';
			$output.='</script>';
			$output.=drawSpacer(10,1);
		    $output.='<select name="'.$this->name.'_time">';
			for ($i=0; $i < 24; $i++) {
			  	if ($i<10) {
			  		$hour = "0".$i;
			  	} else {
			  		$hour = $i;
			  	}
			  	for ($j=0; $j < 60; $j+=15) {
			  	  if ($j<10) {
			  	  		$minute = ":0".$j;
			  	  } else {
			  	  	 	$minute = ":".$j;
			  	  }
			  	  $selected = "";
			  	  if ($hour.$minute == $time) $selected=" selected";
			  	  $output.="<option value=\"".$hour.$minute."\"".$selected.">".$hour.$minute."</option>\n";
			  	}  
			}
			$output.="</select> h";			
			$output .= WUIObject::std_footer();
			echo $output;
			return $this->columns;
		}
	}
?>