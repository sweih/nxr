<?
	/**
	 * HTML: Creates a Bar for displaying several buttons in a row.
	 * @package WebUserInterface
	 */
	class ButtonBar extends WUIObject {
		var $buttons = null;

		var $variation = 0;
		var $variations = null;
		var $variation_painted = false;
		var $translations = null;
		var $translations_painted = false;
		var $translation = 1;
		var $selectBoxDescr = false;
		var $actions;
		
		/**
		   * standard constructor
		   * @param string the name of the Label, used internally only.
		   * @param string $style sets the styles, which will be used for drawing
		   * @param integer $cells Cellspan of the element in Table.
		   */
		function ButtonBar($name, $style = "standard", $cells = 2) { 
			WUIObject::WUIObject($name, "", "", $style, $cells); 
			$this->actions = array();
		}

		
		
		/**
		 * Add a new action to the actiondropdown
		 *
		 * @param string $title
		 * @param string $js
		 */
		function addAction($title, $js="") {
			$i = count($this->actions);
			$this->actions[$i][0] = $title;	
			$this->actions[$i][1] = $js;
		}
		
		/**
		* Add a new button to the bar
		*
		   * @param string $name Name of the Button
		   * @param string $value Title of the button
		   * @param string $type Type of the button. Can be submit or button
		   * @param string $onclick Javascript action, that will be perfermode when clicking the button
		   * @param string $style Use this style for painting the button
		   * @param boolean show button active (true, default) or inactive (false)
		   * @param string tooltip for the button.
		   */
		function add($name, $value, $type = "submit", $onclick = "", $style = "", $active = true, $tooltip="") {
			$next = count($this->buttons);

			$this->buttons[$next]["name"] = $name;
			$this->buttons[$next]["value"] = $value;
			$this->buttons[$next]["style"] = $style;
			$this->buttons[$next]["type"] = $type;
			$this->buttons[$next]["onclick"] = $onclick;
			$this->buttons[$next]["active"] = $active;
			$this->buttons[$next]["tooltip"] = $tooltip;
		}
		
		/**
		* Insert a new button to the beginning of the button bar.
		*
		   * @param string $name Name of the Button
		   * @param string $value Title of the button
		   * @param string $type Type of the button. Can be submit or button
		   * @param string $onclick Javascript action, that will be perfermode when clicking the button
		   * @param string $style Use this style for painting the button
		   * @param boolean show button active (true, default) or inactive (false)
		   * @param string tooltip for the button.
		   */		
		function insert($name, $value, $type = "submit", $onclick = "", $style = "", $active = true, $tooltip="") {
			$newbutton[0]["name"] = $name;
			$newbutton[0]["value"] = $value;
			$newbutton[0]["style"] = $style;
			$newbutton[0]["type"] = $type;
			$newbutton[0]["onclick"] = $onclick;
			$newbutton[0]["active"] = $active;
			$newbutton[0]["tooltip"] = $tooltip;
			if ($this->buttons) {
				$this->buttons = array_merge($newbutton, $this->buttons);
			} else {
				$this->buttons = $newbutton; 
			}		
		}

           /**
           * Add a new button to the bar with JS-Confirmation
           *
           * @param string $name Name of the Button
           * @param string $value Title of the button
           * @param string $confirmation Text to display confirmation-message
           * @param string URI that will be called if confirmation was done.
           */
        function addConfirm($name, $value, $confirmmessage, $forwardAction) {
           $action =  "confirmAction('".$confirmmessage."', '$forwardAction');";
           $this->add($name, $value, "button", $action) ;
        }

		/**
		 * Add a variation selector or another dropdown to the buttonbar.
		 *
		 * @param array $variations NAme/Value-paired array with variations
		 * @param integer $variation Active variation
		 */
		function setVariationSelector($variations, $variation) {
			$this->variations = $variations;
			$this->variation = $variation;
		}
		
		/**
		 * Add a translation-source selector or another dropdown to the buttonbar.
		 *
		 * @param array $variations NAme/Value-paired array with variations
		 * @param integer $variation Active variation
		 */
		function setTranslationSelector($variations, $variation) {
			$this->translations = $variations;
			$this->translation = $variation;
		}

		/**
		 * Write HTML for the WUI-Object.
		 *
		 */
		function draw() {
			global $lang, $action, $variation;

			
			$output = '<td colspan="' . $this->columns . '" align="right" class="toolbar" cellpadding="0" cellspacing="0" border="0" valign="top">';

			for ($i = 0; $i < count($this->buttons); $i++) {
				if ($this->buttons[$i]["name"] == "separator") {
					$output .= '&nbsp;|&nbsp;';
				} else {
					$lb = new LinkButtonInline($this->buttons[$i]["name"], $this->buttons[$i]["value"], "navbar", $this->buttons[$i]["type"], $this->buttons[$i]["onclick"], "form1", 1, $this->buttons[$i]["active"], $this->buttons[$i]["tooltip"]);
					$output .= $lb->draw();
					$output .= "&nbsp;&nbsp;";
				}
			}

			// Draw action dropdown
			if (count($this->actions) > 0 ) {
			  $js = "if (this.value != '-') {";
			  $js.= " switch (this.value) {";
			  for ($i=0; $i<count($this->actions); $i++) {			  	
			  	if (strlen($this->actions[$i][1])>0)
			  	  $js.= " case '".$i."':".$this->actions[$i][1]."; break;";
			  }
			  $js.= "default: document.form1.action.value=this.value;". getWaitUpScreen()."; document.form1.submit();break;";		  
			  $js.= '}}';
			  $output.= '<select name="actionselector'.$this->name.'" size="1" onChange="'.$js.'">';
			  $output.= '<option value="-" style="color:#999999;">'.$lang->get('more_act', 'More actions...').'</option>';
			  for ($j=0; $j < count($this->actions); $j++) {			  				  	
			  	if (sameText($this->actions[$j][0], 'separator')) {
			  	  $output.= '<option value="-" style="color:#999999;">----------</option>';
			  	} else {
			  	  if (strlen($this->actions[$j][1])> 0) {
 					$output.= '<option value="'.$j.'">'.$this->actions[$j][0].'</option>';			  	  	
			  	  } else {
			  	    $output.= '<option value="'.$this->actions[$j][0].'">'.$this->actions[$j][0].'</option>';
			  	  }
			  	}
			  }
			  $output.= '</select>';	
				
			}
			
			
			// Draw variation selector.
			if ($this->variations != null && $this->variation_painted == false) {
				$overallSel = false;
				if ($i > 0)
					$output .= drawSpacer(20, 1);

				if ($this->selectBoxDescr)
 			   $output.= '<span class="bcopy" style="vertical-align:text-top;">'.$lang->get("sel_var", "Select Variation")."&nbsp;</span>";
				
				$output .= "<select name=\"variation\" size=\"1\" style=\"width:100 px;\">\n";

				// note: values are in form: array( array(name, value) )
				for ($i = 0; $i < count($this->variations); $i++) {
					$sel = "";

					if ($this->variations[$i][1] == $this->variation) {
						$sel = "selected";
						$overallSel = true;
					}

					$output .= "<option value=\"" . $this->variations[$i][1] . "\" " . $sel . ">" . $this->variations[$i][0] . "</option>\n";
				}

				if ($lang->get("change", 'Change') != "") {
					$label = $lang->get("change", "Change");
				} else {
					$label = "Go";
				}

				if (! $overallSel) {
				  $variation = $this->variations[0][1];	
				}
				$output .= "</select>".drawSpacer(3,1);			
				$output.=getButton("changevariation", $label, "", "submit");
				$this->variation_painted = true;
			}
			
			// Draw translations selector.
			if ($this->translations != null && $this->translations_painted == false) {
				if ($i > 0)
					$output .= drawSpacer(20, 1);

				if ($this->selectBoxDescr)
				 $output.= '<span class="bcopy" style="vertical-align:text-top;">'.$lang->get("trans_templ", "Translate from")."&nbsp;</span>";
				$output .= "<select name=\"translation\" size=\"1\" style=\"width:130 px;\">\n";

				if (! $this->selectBoxDescr) 
				  $output.="<option value=\"-1\">".$lang->get("trans_templ", "Translate from")."</option>\n";
				$output.="<option value=\"-2\">".$lang->get("disable", "Disable")."</option>\n";
				// note: values are in form: array( array(name, value) )
				for ($i = 0; $i < count($this->translations); $i++) {
					$sel = "";

					if ($this->translations[$i][1] == $this->translation) {
						$sel = "selected";
					}

					$output .= "<option value=\"" . $this->translations[$i][1] . "\" " . $sel . ">" . $this->translations[$i][0] . "</option>\n";
				}

				if ($lang->get("change") != "") {
					$label = $lang->get("change", 'Change');
				} else {
					$label = "Go";
				}

				$output .= "</select>".drawSpacer(3,1);
				$output .= getButton('changetranslation', $label, '', 'submit');
				$this->translations_painted = true;
			}

			if ($this->translations_painted || $this->variation_painted)
			  $output .= "<input type=\"hidden\" name=\"acstate\" value=\"$action\">";
			  
			$output .= "</td>";		
			echo $output;
			return $this->columns;
		}
	}
?>