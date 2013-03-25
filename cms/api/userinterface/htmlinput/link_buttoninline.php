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
	 * Button-Class for Form-Layout.
	 * The Button will not be placed within table's td-tags, as other layout elements do!
	 * @version 1.00
	 */
	class LinkButtonInline extends Button {
		var $type;

		var $action;
		var $formname;
		var $active;
		var $tooltip;

		/**
		  * standard constuctor
		  * @param string the name of the Input, used in the html name property
		  * @param string Text that will be placed on the button and will be submitted.
		  * @param string sets the styles, which will be used for drawing
		* @param string $type type of input. Allowed are submit|button|reset
		* @param string $action JS action, that is to be performed, when button is clicked
		* @param string Name of the form, the button is in.
		  * @param integer $cells Cellspan of the element in Table.
		  * @param boolean show button active (true, default) or inactive (false)
		  * @param string Tooltip-text to show.
		  */
		function LinkButtonInline($name, $value, $style, $type = "button", $action = "", $formname = "form1", $cells = 1, $active = true, $tooltip="") {
			WUIObject::WUIObject($name, "", $value, $style, $cells);

			$this->action = $action;
			$this->type = strtolower($type);
			$this->formname = $formname;
			$this->active = $active;
			$this->tooltip = $tooltip;
		}

		/**
		  * Draws the layout-element
		  */
		function draw() {
			if ($this->active) {
				$output = "<a href=\"#\" class=\"" . $this->style . "\" ";
			} else {
				$output = "<a class=\"" . $this->style . " " . $this->style . "_inactive\" ";
			}

			if ($this->tooltip != "") {
			 $output.='title="'.$this->tooltip.'" ';	
			}
			
			$myJS = "return false;";

			if ($this->type == "submit") {
				$myJS = "document." . $this->formname . "." . $this->name . ".value = '" . $this->value . "'; document." . $this->formname . ".submit(); return false;";
			} else if ($this->type == "reset") {
				$myJS = "document." . $this->formname . ".reset(); return false;";
			}
			
			if ($this->active) {
				if ($this->action != "") {
					$output .= " onClick=\"$this->action; $myJS\"";
				} else {
					$output .= " onClick=\"$myJS\"";
				}
			}

			$output .= ">";
			$output .= str_replace(" ", "&nbsp;", $this->value);
				$output .= "</a>";			
			return $output;
		}
	}
	
	/**
	 * Has the same functionality like LinkButtonInline but does not draw links but real buttons.
	 *
	 */
	class ButtonInline extends LinkButtonInline {
		
			/**
		  * Draws the layout-element
		  */
		function draw() {
			if ($this->active) {
				$output = '<input type="button" name="bi" ';
			} else {
				$output = '<input type="button" disabled name="bi" ';
			}

			if ($this->tooltip != "") {
			 $output.='title="'.$this->tooltip.'" ';	
			}
			
			$myJS = "return false;";

			if ($this->type == "submit") {
				$myJS = "document." . $this->formname . "." . $this->name . ".value = '" . $this->value . "'; document." . $this->formname . ".submit(); return false;";
			} else if ($this->type == "reset") {
				$myJS = "document." . $this->formname . ".reset(); return false;";
			}
			
			if ($this->active) {
				if ($this->action != "") {
					$output .= " onClick=\"$this->action; $myJS\"";
				} else {
					$output .= " onClick=\"$myJS\"";
				}
			}

			$output .= ' value= "'. str_replace(" ", "&nbsp;", $this->value) .'"/>';				
			return $output;
		}
		
	
	}
?>