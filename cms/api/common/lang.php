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
	 * Container for all labels and descriptions in the
	 * backend of the cms. Used for Language abstraction
	 * @package Language
	 */
	class lang {
		var $container = null;
		var $smarttranslator = null;

		var $language;
		/**
		 * standard constructor.
		 */
		function lang($language = "en") { 
			$this->language = strtoupper($language); 
			$this->smarttranslator = array();
		}

		/**
		 * add a new label or description to the system.
		 * @param string Keyname of the label
		 * @param string Text for display of the label
		 * @param string Text for displaying a tooltip
		 */
		function add($title, $text, $tooltip="") {
			global $db;
		
			$title = strtoupper($title);

			if (countRows("internal_resources", "RESID", "RESID='$title' AND LANGID='" . $this->language . "'") == 0) {
				$sql = "INSERT INTO internal_resources (RESID, LANGID, VALUE, TOOLTIP) VALUES('$title', '" . $this->language . "', '$text', '$tooltip')";
			} else {
				$sql = "UPDATE internal_resources SET VALUE='$text' WHERE RESID='$title' AND LANGID='" . $this->language . "'";
			}

			$query = new query($db, $sql);
			$query->free();
		}

		/**
		 * get a text to a corrsponding keyname
		 * @param string keyname of the label.
		 * @param string Standard value if no entry for keyname exists
		 * @param stinng Text for tooltip.
		 */
		function get($title, $standard = "", $tooltip="") {
			global $auth;
			$this->smarttranslator[] = "'".strtoupper($title)."'";
			$title = strtoupper($title);
			$tmpreturn = getDBCell("internal_resources", "VALUE", "RESID='$title' AND LANGID='" . $this->language . "'");
			$tooltip =  getDBCell("internal_resources", "TOOLTIP", "RESID='$title' AND LANGID='" . $this->language . "'");
			if ($tmpreturn == "") {
				if ($standard != "") {
					$this->add($title, $standard, $tooltip);
				} else if ($this->language != "EN") {
					$tmpreturn = getDBCell("internal_resources", "VALUE", "RESID='$title' AND LANGID='EN'");
					$tooltip =  getDBCell("internal_resources", "TOOLTIP", "RESID='$title' AND LANGID='EN'");
				}

			}
			$tmpreturn = htmlentities($tmpreturn, ENT_COMPAT);
			if ($tooltip != "") {
				$agenttip = str_replace("\n", "", $tooltip);
				$agenttip = str_replace("\r", "", $agenttip);
				if ($auth->useAgent) {
					$result = "<div style='cursor:help;' class=\"agenttip\" oncontextmenu=\"javascript:showmenuie5();\" onClick=\"javascript:parent.AgentTooltip('".htmlentities($agenttip, ENT_COMPAT)."');\" onMouseOver=\"javascript:parent.AgentThinks();\">$tmpreturn</div>";
				} else {
			  		$result = "<div style='cursor:help;display:inline;' title='".htmlentities($tooltip, ENT_COMPAT)."'>$tmpreturn</div>";	
			  	}
			} else {
			  $result = $tmpreturn;
			}				
			if ($result == "") {
				$result = $standard;
			}
			$result = str_replace('&gt;', '>', $result);
			$result = str_replace('&lt;', '<', $result);
			$result = str_replace('&amp;', '&', $result);
			return $result;			
		}

		/**
		 * get a text to a corrsponding keyname. Whitespaces and - are replaced for buttons
		 * @param string keyname of the label.
		 * @param string Standard value if no entry for keyname exists
		 */
		function getc($title, $standard = "") { return clrSPC($this->get($title, $standard)); }

		/**
		 * Returns the currently set language.
		  */
		function getLang() { return $this->language; }

		/**
		 * Delete a set from the database
		 * @param string Name of the Text to delete
		 */
		function delete($key) {
		  global $db;
		  $sql = "DELETE FROM internal_resources WHERE RESID = '".strtoupper($key)."'";	
		  $query = new query($db, $sql);
		  $query->free();
		}
		
		/**
		 * Sets the current language...
		 *
		 * @param $language string Langauge, which is to be set
		 */
		function setLang($language) { $this->language = $language; }
		
		
		/**
		* returns the Version of the TTS-Module to use for the currently selected language.
		*/
		function getAgentVersion() {
			global $db;
			$sql = "SELECT * FROM internal_resources_languages WHERE LANGID = '".$this->language."' LIMIT 1";
			$query = new query($db, $sql);
			$query->getrow();
			return $query->field("AGENT_VERSION");
			$query->free();
		}
		
		/**
		* returns the ClassID of the TTS-Module to use for the currently selected language.
		*/
		function getAgentClassID() {
			global $db;
			$sql = "SELECT * FROM internal_resources_languages WHERE LANGID = '".$this->language."' LIMIT 1";
			$query = new query($db, $sql);
			$query->getrow();
			return $query->field("AGENT_CLASSID");	
			$query->free();
					
		}
		
		/**
		* returns the LanguageID of the TTS-Module to use for the currently selected language.
		*/
		function getAgentLanguageID() {
			global $db;
			$sql = "SELECT * FROM internal_resources_languages WHERE LANGID = '".$this->language."' LIMIT 1";
			$query = new query($db, $sql);
			$query->getrow();
			return $query->field("AGENT_LANGID");			
			$query->free();
		}
		
		/**
		 *  Create a form for smart translation and return the form reference.
		 *
		 */
		function createSmartTranslateForm() {
			global $c, $page_action, $specialID, $page_state;
			$page_action = 'UPDATE';
			if (value('tsaving', '', '') != 'yes')
			  $page_state = '';
			$oldvalues = value('oldfields', '', '0');
			if ($oldvalues != '0') {
			    $commas2 = $oldvalues;
			    $commas = "'" . str_replace(",", "','", $oldvalues) . "'";			    
			} else {				
				$commas = implode(',', $this->smarttranslator);
				$commas2 = str_replace("'", '', $commas);
			}
						
			$tform = new EditForm('N/X Smarttranslator', '', 'smarttranslator');	
			$oid = $c["smarttranslate"];
			
			
			
			$items = createDBCArray('internal_resources', 'RESID', "LANGID='".$oid."' AND UPPER(RESID) IN (".$commas.")", 'ORDER BY RESID ASC');			
			for ($i = 0; $i < count($items); $i++) {		
				$specialID = $items[$i];
				$tform->add(new Label("lbl", "<b>Translate: </b>" . getDBCell("internal_resources", "VALUE", "RESID='" . $items[$i] . "' AND LANGID='EN'"), "standardlight", 2));
				$tform->add(new TextInput($items[$i], "internal_resources", "VALUE", "RESID='" . $items[$i] . "' AND LANGID='$oid'", "type:textarea,size:2,width:400"));
				$tform->add(new TextInput("Tooltip".$items[$i], "internal_resources", "TOOLTIP", "RESID='" . $items[$i] . "' AND LANGID='$oid'", "type:textarea,size:2,width:400"));
				$tform->add(new Spacer(2));
			}
			$tform->add(new Hidden('oldfields', $commas2));
			$tform->add(new Hidden('tsaving', 'yes'));
			return $tform;		
		}
	}
?>