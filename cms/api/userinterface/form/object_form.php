<?

	/**
	 * Form for editing objects in the cms.
	 * require_onces syncronize.php 
	 * @package ContentManagement
	 */
	class ObjectForm extends Form {
		var $variations = null;

		var $oid;
		var $externalAction = "";
		var $variation;
		var $editor; // stores if meta or content is to be edited.
		var $contentObject;
		var $title;

		/**
		 * standard constructor
		* @param string $headline Headline, to be displayed in the form
		* @param string $icon Icon, to be displayed in the form. Name of the file, stored in images folder.
		* @param string $name Name of the form. Will be used in HTML.
		* @param string $action Next script, that shall be called when submitting. Empty if self.
		* @param string $method POST or GET for sending the form. POST is default and strongly recommended
		* @param string $enctype Specify the enctype for the form. Use only when submitting files as multipart.
		*/
		function ObjectForm($headline, $icon = "", $name = "form1", $action = "", $method = "POST", $enctype = "") {
			Form::Form($headline, $icon, $name, $action, $method, $enctype);

			// initialize with values.
			global $oid, $action, $variation, $changevariation;
			$this->oid = $oid;
			$this->externalAction = $action;
			$this->title = getDBCell("content", "NAME", "CID = $oid");
			$this->headline .= ":&nbsp;" . $this->title;

			// get current variation!
			if (isset($variation) && isset($changevariation))
				pushVar("variation", $variation);

			$this->variation = getVar("variation");

			if ($this->variation == 0)
				$this->variation = 1; // set to standard.

			$this->populateVariations();

			// get current edit-scheme (META or CONTENT)
			global $changeeditor, $lang, $processing, $page_state;

			if (isset($changeeditor)) {
				pushVar("schemeEditor", $changeeditor);

				$processing = ""; // enable database loading if switched.
				$page_state = "START";
			}

			$this->editor = getVar("schemeEditor");

			if ($this->editor == "")
				$this->editor = $lang->get("ed_content");

			// prepare the form drawing.
			// check, if variation is defined or not.
			if (count($this->variations) > 0) {
				// define sniplets.
				$this->draw_jobButton();

				$this->draw_variationSelector();
				$this->draw_myBody();
				$this->draw_config();
			} else {
				global $lang;

				$this->addToTopText($lang->get("no_variations"));
				$this->setTopStyle($this->errorstyle);
			}
		}

		/**
		 * Gets all available variations for an object from the database.
		 */
		function populateVariations() {
			global $db;

			$isInVariations = false;

			$sql = "SELECT v.NAME AS NAM, v.VARIATION_ID AS VARI FROM variations v, content_variations c WHERE c.CID = " . $this->oid . " AND c.DELETED=0 AND c.VARIATION_ID = v.VARIATION_ID AND v.DELETED = 0 ORDER BY v.NAME";
			$query = new query($db, $sql);

			while ($query->getrow()) {
				$nextId = count($this->variations);

				$this->variations[$nextId][0] = $query->field("NAM");
				$this->variations[$nextId][1] = $query->field("VARI");

				if ($this->variation == $this->variations[$nextId][1])
					$isInVariations = true;
			}

			// set another variation, as standard variation is not available!
			if (!$isInVariations)
				$this->variation = $this->variations[0][1];
		}

		/**
		 * Form-wide Handler for going to the selected page and processing it.
		 */
		function process() {
			$this->check();

			// processing here
			global $savemeta, $errors, $lang, $page_action, $updatevariation;

			if ($errors == "" && (isset($savemeta) || isset($updatevariation))) {
								
				
				for ($i = 0; $i < count($this->container); $i++) {
					$this->container[$i]->process();
				}
				processSaveSets();

			  // after process
				for ($i = 0; $i < count($this->container); $i++) {
					$this->container[$i]->afterProcess();
				}
				processSaveSets();


				if ($errors != "") {
					$this->addToTopText($lang->get("saveerror"));
				} else {
					$this->addToTopText($lang->get("savesuccess"));
				}
			}
		}

		/**
		 * Draws the presentation or edit-fields in the body.
		 */
		function draw_myBody() {
			global $lang;

			$module = getDBCell("content", "MODULE_ID", "CID = " . $this->oid);

			if ($this->editor == $lang->get("ed_meta")) {
				// edit meta information
				syncMetas($this->oid, "OBJECT");

				$std = 0;
				$mod = getDBCell("modules", "MT_ID", "MODULE_ID = $module");
				$add = getDBCell("content", "MT_ID", "CID = " . $this->oid);

				$this->draw_metaInput($lang->get("mt_base"), $std);
				$this->draw_metaInput($lang->get("mt_module"), $mod);
				$this->draw_metaInput($lang->get("mt_additional"), $add);
			} else {
				// edit content
				includePGNSource ($module);

				$fkid = getDBCell("content_variations", "FK_ID", "CID = $this->oid AND VARIATION_ID = $this->variation");
				$this->contentObject = createPGNRef($module, $fkid);

				// dispatching between preview and editing mode.
				global $editvariation, $updatevariation;

				if (isset($editvariation) || isset($updatevariation)) {
					global $page_state, $specialID;

					if (isset($editvariation))
						$page_state = "start";

					$this->add(new FormSplitter($lang->get("o_edit"). "<i> " . $this->title . "</i>", "i_edit.gif"));
					$specialID = $fkid;
					$this->contentObject->edit($this);
					$specialID = "";
					$container = new HTMLContainer("container", "informationheader", 1);
					$container->add("<input type=\"SUBMIT\" name=\"updatevariation\" value=\"" . $lang->get("commit"). "\">");
					$container->add("<input type=\"SUBMIT\" name=\"preview\" value=\"" . $lang->get("preview"). "\">");
					$this->add($container);
					$this->add(new ButtonInCell("reset", $lang->get("reset"), "informationheader", "RESET"));
				} else {
					$this->add(new FormSplitter($lang->get("o_preview"). "<i>" . $this->title . "</i>", "i_edit.gif"));

					$this->add(new ButtonInCell("editvariation", $lang->get("o_edit"), "informationheader", "SUBMIT", "", 2));
					$this->add(new Label("preview", $this->contentObject->preview(), "standardlight", 2));
					$this->add(new ButtonInCell("editvariation", $lang->get("o_edit"), "informationheader", "SUBMIT", "", 2));
				}
			}
		}

		/**
		 * draw the change button between content and meta-editor
		 */
		function draw_jobButton() {
			global $lang;

			if ($this->editor == $lang->get("ed_content")) {
				$this->add(new ButtonInCell("changeeditor", $lang->get("ed_meta"), "errorheader", "SUBMIT"));
			} else {
				$this->add(new ButtonInCell("changeeditor", $lang->get("ed_content"), "errorheader", "SUBMIT"));
			}

			$this->add(new Label("lbl", $lang->get("ed_dispatcher"), "errorheader"));
		}

		/**
		 * draw an input field for meta-Data
		 * @param string Headline for this field.
		 * @param string Meta-Template, which is to be used.
		 */
		function draw_metaInput($headline, $mt_id) {
			global $db, $specialID;

			//checking, if there are any items in the template.
			$sql = "SELECT COUNT(MTI_ID) AS ANZ FROM meta_template_items WHERE MT_ID = $mt_id";

			$query = new query($db, $sql);
			$query->getrow();
			$amount = $query->field("ANZ");

			if ($amount > 0) {
				$this->add(new FormSplitter($headline, "i_meta.gif"));

				$sql = "SELECT m.MID AS D1, t.MTYPE_ID AS D2, t.NAME AS D3 FROM meta_template_items t, meta m WHERE m.MTI_ID = t.MTI_ID AND m.CID = " . $this->oid . " AND m.DELETED=0 AND t.MT_ID = $mt_id ORDER BY t.POSITION ASC";

				$query = new query($db, $sql);
				$mlist = null;
				$counter = 0;

				while ($query->getrow()) {
					// save the list, so that it will not go lost.
					$mlist[$counter][0] = $query->field("D1");

					$mlist[$counter][1] = $query->field("D2");
					$mlist[$counter][2] = $query->field("D3");
					$counter++;
				}

				// add the metainput fields.
				for ($i = 0; $i < $counter; $i++) {
					$specialID = $mlist[$i][0];

					// dispatch type.
					switch ($mlist[$i][1]) {
						case 1:
							$obj[$i] = new TextInput($mlist[$i][2], "meta", "VALUE", "MID = " . $mlist[$i][0], "type:text,size:64,width:300");

							break;

						case 2:
							$obj[$i] = new TextInput($mlist[$i][2], "meta", "VALUE", "MID = " . $mlist[$i][0], "type:textarea,size:3,width:300");

							break;

						case 3:
							$obj[$i] = new TextInput($mlist[$i][2], "meta", "VALUE", "MID = " . $mlist[$i][0], "type:color,param:form1");

							break;
					}

					if (isset($obj[$i]))
						$this->add($obj[$i]);
				}

				$specialID = "";
				$this->add(new ButtonInCell("savemeta", "Update", "informationheader", "SUBMIT"));
				$this->add(new Cell("clc", "informationheader", 1, 300));
			}
		}

		/**
		 * Draws the selectbox for variation-selection and the button for submitting.
		 */
		function draw_variationSelector() {
			global $lang, $c, $sid;

			$this->add(new Dropdown("variation", $this->variations, "errorheader", $this->variation, 150, 1));
			$this->add(new ButtonInCell("changevariation", $lang->get("select_variation"), "errorheader", "SUBMIT"));
			$this->add(new ButtonInCell("usage", "Usage", "errorheader", "button", "window.open('" . $c["docroot"] . "modules/common/object_usage.php?oid=" . $this->oid . "&sid=$sid', 'usage','Width=300px,Height=500px,help=no,status=yes,scrollbars=yes,resizable=yes');", 2));
		}

		/**
		 * Draws configuation information.
		 */
		function draw_config() {
			$this->add(new Hidden("action", $this->externalAction));

			$this->add(new Hidden("go", "UPDATE"));
		}
	}
?>