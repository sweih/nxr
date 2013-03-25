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
	 * Form for prompting a text and an okay button and then forwarding.
	 *
	 * @package WebUserInterface
	 */
	class SettingsForm extends Form {
		
		var $settings;
		
		/**
		  * Standard constructor
		  * @param $title Title of the form
		  * @param $prompt Prompt-message of the form
		  */
		function SettingsForm($title) {
			global $go;
			Form::Form($title);
			$go="show";						
			$this->settings = array();
		}
		
		/**
		 * Adds a new setting to the form
		 *
		 * @param title of the setting $title
		 * @param string $regkey Path in registry, e.g. /DESIGNS/MENU
		 * @param array $nameValueArray
		 */
		function addRadioSetting($title, $regkey, $nameValueArray) {
			$ar[0] = $title;
			$ar[1] = $regkey;
			$ar[2] = $nameValueArray;	
			$ar[3] = reg_load($regkey);
			$ar[4] = "radio";
			$this->settings[] = $ar;
		}
		
		/**
		 * Adds a headline to the settingsform
		 *
		 * @param string $headline
		 */
		function addHeadline($headline) {
			$ar[0] = $headline;
			$ar[4] = "title";
			$this->settings[] = $ar;	
		}
		
		/**
		 * Add a Color setting widget
		 *
		 * @param string $title
		 * @param string $regkey		 
		 * @param string $color
		 */
		function addColorSetting($title, $regkey, $color) {
			$ar[0] = $title;
			$ar[1] = $regkey;
			$ar[3] = reg_load($regkey);
			if ($ar[3] == "") 
			  $ar[3] = $color;
			$ar[4] = "color";
			$this->settings[] = $ar;			
		}
		
				/**
		 * Add a Color setting widget
		 *
		 * @param string $title
		 * @param string $regkey		 
		 * @param string $text
		 */
		function addTextSetting($title, $regkey, $text) {
			$ar[0] = $title;
			$ar[1] = $regkey;
			$ar[3] = reg_load($regkey);
			if ($ar[3] == "") 
			  $ar[3] = $text;
			$ar[4] = "text";
			$this->settings[] = $ar;			
		}
		
				/**
		 * Add a Color setting widget
		 *
		 * @param string $title
		 * @param string $regkey		 
		 * @param string $color
		 */
		function addBooleanSetting($title, $regkey, $value) {
			$ar[0] = $title;
			$ar[1] = $regkey;
			$ar[3] = reg_load($regkey);
			if ($ar[3] == "") 
			  $ar[3] = $boolean;
			$ar[4] = "boolean";
			$this->settings[] = $ar;			
		}



		/**
		  * processes the form. Used internally only.
		  */
		function process() {
			global $page_state, $page_action;
			if ($page_state == "processing" && $page_action = "UPDATE") {
				for ($i=0; $i<count($this->settings); $i++) {
				  if ($this->settings[$i][1] != "") {
				    $value = value("set".$i, "", '');
				    reg_save($this->settings[$i][1], $value);
				    $this->settings[$i][3] = $value;
				  }	
				}								
			}
		}
		
		/**
		 * Draw the settingsform.
		 *
		 */
		function draw() {
			for ($i=0; $i< count($this->settings); $i++){
			  			  
			  if ($this->settings[$i][4] == "radio") {
			    $this->add(new Subtitle('st', $this->settings[$i][0], 2));
			  	$ar = $this->settings[$i][2];
			    for ($j=0; $j< count($ar); $j++) {
			      $this->add(new Radio("set".$i, $ar[$j][1], "standardlight", $this->settings[$i][3] == $ar[$j][1], 1));
			      $this->add(new Label('lbl', $ar[$j][0], 'standardlight'));
			    }
			  } else if($this->settings[$i][4] == "color") {
			  	$this->add(new Label('lbl', $this->settings[$i][0], 'standardlight'));
			  	$this->add(new ColorInput("set".$i, $this->settings[$i][3], 'standardlight', 'form1', 1));			  				  	
			  }	else if($this->settings[$i][4] == "text") {
			  	$this->add(new Label('lbl', $this->settings[$i][0], 'standardlight'));
			  	$this->add(new Input("set".$i, $this->settings[$i][3], 'standardlight', '255', 300));			  				  	
			  }	 else if($this->settings[$i][4] == "boolean") {
			  	$this->add(new Label('lbl', $this->settings[$i][0], 'standardlight'));
			  	$this->add(new Checkbox("set".$i, 1, 'standardlight', $this->settings[$i][3]));
			  } else if ($this->settings[$i][4] == "title") {
			  	$this->add(new Spacer(2));
			  	$this->add(new Subtitle('st', $this->settings[$i][0]));
			  }


			}
			Form::draw();
			
		}

		/**
		 * internal. Draws the buttons of your form.
		 */
		function draw_buttons() {
			global $lang;
			echo "<tr><td valign=\"middle\" align=\"right\" colspan=\"2\">";
			$value = "Update";
			$resetbutton = new Button("reset", $lang->get("reset_form", "Reset Form"), "", "reset");
			$resetbutton->draw();
			echo "&nbsp;&nbsp;";
			retain("goon");
			$submitbutton = new Button("goon", $value, "", "submit", $this->submitButtonAction.$this->getWaitupScreen());
			$submitbutton->draw();	
			echo "</td></tr>";
		}
	}
?>