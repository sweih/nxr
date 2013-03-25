<?
	/**********************************************************************
	 * @module Application
	 **********************************************************************/
	
	//// ACL Check ////
	if (! $aclf->hasAccess($auth->userId))
	  header("Location: ". $c["docroot"]."modules/common/noaccess.php?sid=$sid&guid=$pnode");
	//// ACL Check ////
	
	$editing = value("editing");
	$action = value("action");

	// process object actions.
	if ($action != "0") {

		
		if ($action == $lang->get("createcopy")) {
			$page_state="start";
			if (value("prcstate") == "data") {
				$name = value("copyname");
				if ($name=="0") $name = "Cluster";
				$name = makeCopyName("cluster_templates", "NAME", $name, "CATEGORY_ID = ".$pnode);
				$oid = copyClusterTemplate($oid, $name);
				$action = "editobject";			
			} else {
				$page_action = "INSERT";
				$form = new StdEDForm($lang->get("copyclt", "Copy cluster template"));
				$form->headerlink = crHeaderLink($lang->get('ed_clt'), "modules/clustertemplate/clustertemplates.php?sid=$sid&oid=".value("oid", "NUMERIC")."&action=editobject");
				$form->add(new Label("lbl", $lang->get("source", "Source"), "standardlight"));
				$form->add(new Label("lbl", getDBCell("cluster_templates", "NAME", "CLT_ID = $oid"), "standardlight"));
				$form->add(new Label("lbl", $lang->get("newname", "New Name"), "standardlight"));
				$form->add(new Input("copyname", "Cluster Template", "standard"));
				$form->add(new Hidden("action", $lang->get("createcopy")));
				$form->add(new Hidden("oid", $oid));
				$form->add(new Hidden("prcstate", "data"));
				$form->forbidDelete(true);				
				$page->add($form);
			}
		}
			
		/** Create a new object **/
		if ($action == "newobject" && $aclf->checkAccessToFunction("NEW_CL_TEMP")) {
			if ($go == "insert")
				$page_action = "INSERT";

			$form = new stdEDForm($lang->get("clt_new"), "i_scheme.gif");
			$cond = $form->setPK("cluster_templates", "CLT_ID");
			$oname = new TextInput($lang->get("clt_name"), "cluster_templates", "NAME", $cond, "type:text,width:300,size:32", "MANDATORY&UNIQUE");
			$oname->setFilter("CATEGORY_ID = $pnode");
			$form->add($oname);
			$form->add(new SelectOneInput($lang->get("metatemplate"), "cluster_templates", "MT_ID", "meta_templates", "NAME", "MT_ID", "INTERNAL=0 AND VERSION=0", "1", "type:dropdown,width:300", "MANDATORY"));
			$form->add(new TextInput($lang->get("description"), "cluster_templates", "DESCRIPTION", $cond, "type:textarea,width:300,size:3", ""));
			$tempbox = new CheckboxTxtInput($lang->get("is_compound","Compound Cluster with own Layout" ), "cluster_templates", "CLT_TYPE_ID", $cond, "1", "0");
			$tempbox->setJSPayload("onClick='toggle(\"templ\");'");
			$form->add($tempbox);
			$show = ' style="display:none;" ';
			$form->add(new IDWrapper("templ", new TextInput($lang->get("template", "Template"), "cluster_templates", "TEMPLATE", $cond, "type:textarea,width:400,size:20") ,"embedded", $show,2));	
						
			$form->add(new Hidden("action", "newobject"));
			$form->add(new NonDisplayedValueOnInsert("cluster_templates", "CATEGORY_ID", $cond, $pnode, "NUMBER"));
			$form->add(new NonDisplayedValueOnInsert("cluster_templates", "DELETED", $cond, 0, "NUMBER"));
			$form->forbidDelete(true);
			$form->forbidUpdate(true);
			$page->add($form);

		// edit the properties of an object.
		} else if ($action == "objectprop" && $aclf->checkAccessToFunction("ED_CL_TEMP_PROPS")) {

			$form = new EditForm($lang->get("clt_properties"), "i_scheme.gif");
			$cond = $form->setPK("cluster_templates", "CLT_ID");
			$form->headerlink = crHeaderLink($lang->get('ed_clt'), "modules/clustertemplate/clustertemplates.php?sid=$sid&oid=".value("oid", "NUMERIC")."&action=editobject");
			$oname = new TextInput($lang->get("clt_name"), "cluster_templates", "NAME", $cond, "type:text,width:300,size:32", "MANDATORY&UNIQUE");
			$oname->setFilter("CATEGORY_ID = $pnode");
			$form->add($oname);
			$form->add(new SelectOneInput($lang->get("metatemplate"), "cluster_templates", "MT_ID", "meta_templates", "NAME", "MT_ID", "INTERNAL=0 AND VERSION=0", $cond, "type:dropdown,width:300", "MANDATORY"));
			$form->add(new FolderDropdown($lang->get("r_parent"), "cluster_templates", "CATEGORY_ID", $cond));
			$form->add(new TextInput($lang->get("description"), "cluster_templates", "DESCRIPTION", $cond, "type:textarea,width:300,size:3", ""));
			
			$tempbox = new CheckboxTxtInput($lang->get("is_compound","Compound Cluster with own Layout" ), "cluster_templates", "CLT_TYPE_ID", $cond, "1", "0");
			$tempbox->setJSPayload("onClick='toggle(\"templ\");'");
			$form->add($tempbox);
			$show = ' style="display:block;" ';
			if (getDBCell("cluster_templates", "CLT_TYPE_ID", $cond) == 0) {
			  $show = ' style="display:none;" ';
			} 			
			$form->add(new IDWrapper("templ", new TextInput($lang->get("template", "Template"), "cluster_templates", "TEMPLATE", $cond, "type:textarea,width:400,size:20") ,"embedded", $show,2));	

			$form->add(new CheckboxTxtInput($lang->get('is_shop_cat_class','Enable for Use as Shop Category Template'), 'cluster_templates', 'IS_SHOP_CATEGORY', $cond));
			$form->add(new CheckboxTxtInput($lang->get('is_shop_cat_product','Enable for Use as Shop Product Template'), 'cluster_templates', 'IS_SHOP_PRODUCT', $cond));
			
			$form->add(new Hidden("action", "objectprop"));
			$form->forbidDelete(true);
			$page->add($form);
		// delete the object
		} else if ($action == $lang->get("delete") && $aclf->checkAccessToFunction("DELETE_CL_TEMP") && value("decision") != $lang->get("no")) {
				if (!isset($go))
					$go = "start";				
				if (value("decision") == $lang->get("yes")) {			
					$delhandler = new ActionHandler("deleteobject");
					$delhandler->addDBAction("UPDATE cluster_templates SET DELETED = 1 WHERE CLT_ID = $oid");
					$todo = createDBCArray("cluster_template_items", "CLTI_ID", "FKID = $oid");
					for ($i = 0; $i < count($todo); $i++) {
						$delhandler->addDBAction("DELETE FROM cluster_content WHERE CLTI_ID = " . $todo[$i]);
					}
					$delhandler->addDBAction("UPDATE cluster_template_items SET FKID = 0 WHERE FKID = $oid");
					$delhandler->process("deleteobject");
				} else {
					$title = getDBCell("cluster_templates", "NAME", "CLT_ID = $oid");				
					$form = new YesNoForm($lang->get("clt_delete")." $title", $lang->get("clt_delmes"));
					$form->add(new Hidden("action", $lang->get("delete")));
					$form->add(new Hidden("oid", $oid));
					$page->add($form);
				}			
		} else if (($action == "editobject" || value("decision") == $lang->get("no") ) && $aclf->checkAccessToFunction("EDIT_CL_TEMP") ) {
			// edit the object. therefore use a special form which is highly customized.

			/**
			 * Containerform for Cluster-Templates.
			 */
			class ClusterTemplateForm extends ContainerForm {
				var $searchbox;
				var $ready = false; // used for indicating successful save.
				
				/** 
		 		* Draw the head of a forms body.
		 		* May be overwritten for customization.
		 		*/
				function draw_body_head() {
					global $lang, $sid, $db;
					// check, wether the cluster-template has still some instances. if so, do not delete!
					$amount = 0;
					$sql = "SELECT COUNT(CLT_ID) AS ANZ FROM cluster_node where CLT_ID = ".value("oid", "NUMERIC")." AND DELETED=0";
					$cquery = new query($db, $sql);
					$cquery->getrow();
					$amount += $cquery->field("ANZ");
					$sql = "SELECT COUNT(CLT_ID) AS ANZ FROM sitepage_master where CLT_ID = ".value("oid", "NUMERIC")." AND DELETED=0";
					$cquery = new query($db, $sql);
					$cquery->getrow();
					$amount += $cquery->field("ANZ");																
					$this->buttonbar->add("action", $lang->get("createcopy"));
					if ($amount == 0) {						
						$this->buttonbar->add("action", $lang->get("Delete"));											
					}
														
					$this->add(new Cell("cell", "cwhite", 2, 1, 10));
					$this->add(new Label("lbl", $lang->get("dosomething"), "informationheader", 2));
					$this->add(new Cell("cell", "cwhite", 2, 1, 10));
				}

				
				function draw_edit($id) {
					global $lang, $oid;
					$oid = value("oid", "NUMERIC");
					$cond = $this->item_pkname . " = $id";

					$myCLTIName = new TextInput($lang->get("name"), $this->item_table, $this->item_name, $cond, "type:text,size:32,width:200", "UNIQUE&MANDATORY");
					$myCLTIName->setFilter("CLT_ID = " . value("oid", "NUMERIC"));
					$this->add($myCLTIName);

					// dispatching, what type of  clti we are working with.
					$cltitypeId = getDBCell($this->item_table, "CLTITYPE_ID", $cond);
					$fkId = getDBCell($this->item_table, "FKID", $cond);

					switch ($cltitypeId) {
						case 1: // static content
						  $this->add(new Cell("clc", "standard", 2,1,5));
						  $this->add(new Label("lbl", $lang->get("ci_select", "Please select a content to link it into the cluster-template"), "standard", 2));												  
						  $this->add(new Cell("clc", "standard", 2,1,5));
						  $this->add(new LibrarySelect("cluster_template_items", "FKID", $cond));							
						  $this->add(new Cell("clc", "standard", 2,1,5));
  						  break;

						case 2: // dynamic content
							if ($fkId == 0)
								$this->add(new SelectOneInput($lang->get("type"), "cluster_template_items", "FKID", "modules", "MODULE_NAME", "MODULE_ID", "MODULE_TYPE_ID=1", $cond, "type:dropdown", "MANDATORY"));

							$this->add(new TextInput($lang->get("clt_mincard"), "cluster_template_items", "MINCARD", $cond, "type:text,size:3,width:40", "MANDATORY", "NUMBER"));
							$this->add(new TextInput($lang->get("clt_maxcard"), "cluster_template_items", "MAXCARD", $cond, "type:text,size:3,width:40", "MANDATORY", "NUMBER"));
							$this->add(new CheckboxInput($lang->get("clt_exclusive", "Developer Content", "If box is checked, this content can only be edited by developers."), "cluster_template_items", "EXCLUSIVE", $cond, 1, 0));
							if ($fkId != 0 || $this->ready) {
								if ($this->ready && $fkId =="0") $fkId = value($this->item_table . "_FKID", "NUMERIC");
								$classname = getDBCell("modules", "CLASS", "MODULE_ID = ".$fkId);					
								$ref = new $classname(0, $id);					
								$ref->_editConfig($this);
							}
							break;

						case 3: // static cluster
							
							// $this->searchbox = new CLTISelector($this->item_table, "FKID", $cond);
							$this->add(new Cell("clc", "standard", 2,1,5));
							$this->add(new Label("lbl", $lang->get("cl_select", "Please select a cluster to link it into the cluster-template"), "standard", 2));
							$this->add(new Cell("clc", "standard", 2,1,5));
							$container = new Container(2);
							$this->add(new WZSelectCluster("sel_object",600,2));
							
							//$this->add($container);
							break;

						case 4: //dynamic cluster
							if ($fkId == 0)
								$this->add(new CLTSelector($lang->get("type"), "cluster_template_items", "FKID", $cond, $oid, "", "MANDATORY"));

							$this->add(new TextInput($lang->get("clt_mincard"), "cluster_template_items", "MINCARD", $cond, "type:text,size:3,width:40", "MANDATORY", "NUMBER"));
							$this->add(new TextInput($lang->get("clt_maxcard"), "cluster_template_items", "MAXCARD", $cond, "type:text,size:3,width:40", "MANDATORY", "NUMBER"));
							$this->add(new CheckboxInput($lang->get("clt_exclusive", "Developer Content", "If box is checked, this content can only be edited by developers."), "cluster_template_items", "EXCLUSIVE", $cond, 1, 0));
							break;

						case 5: // library
							if ($fkId == 0)
								$this->add(new SelectOneInput($lang->get("type"), "cluster_template_items", "FKID", "modules", "MODULE_NAME", "MODULE_ID", "1", $cond, "type:dropdown", "MANDATORY"));
							$this->add(new TextInput($lang->get("clt_mincard"), "cluster_template_items", "MINCARD", $cond, "type:text,size:3,width:40", "MANDATORY", "NUMBER"));
							$this->add(new TextInput($lang->get("clt_maxcard"), "cluster_template_items", "MAXCARD", $cond, "type:text,size:3,width:40", "MANDATORY", "NUMBER"));
							$this->add(new CheckboxInput($lang->get("clt_exclusive", "Developer Content", "If box is checked, this content can only be edited by developers."), "cluster_template_items", "EXCLUSIVE", $cond, 1, 0));
							break;
						case 6: // compound
							if ($fkId == 0)
							$this->add(new CLTSelector($lang->get("type"), "cluster_template_items", "FKID", $cond, $oid, "", "", "NUMBER", true));
							$this->add(new TextInput($lang->get("clt_mincard"), "cluster_template_items", "MINCARD", $cond, "type:text,size:3,width:40", "MANDATORY", "NUMBER"));
							$this->add(new TextInput($lang->get("clt_maxcard"), "cluster_template_items", "MAXCARD", $cond, "type:text,size:3,width:40", "MANDATORY", "NUMBER"));
							$this->add(new CheckboxInput($lang->get("clt_exclusive", "Developer Content", "If box is checked, this content can only be edited by developers."), "cluster_template_items", "EXCLUSIVE", $cond, 1, 0));
							break;

						case 7: // compound group
							$this->add(new SelectOneInput($lang->get("cp_group", "Compound Group"), "cluster_template_items", "FKID", "compound_groups", "NAME", "CGID", "1", $cond, "type:dropdown", "MANDATORY"));							
							break;
                        			case 8: // channel                           				 
                           				 $this->add(new TextInput($lang->get("clt_mincard"), "cluster_template_items", "MINCARD", $cond, "type:text,size:3,width:40", "MANDATORY", "NUMBER"));
                           				 $this->add(new TextInput($lang->get("clt_maxcard"), "cluster_template_items", "MAXCARD", $cond, "type:text,size:3,width:40", "MANDATORY", "NUMBER"));
                           				 $this->add(new CheckboxInput($lang->get("clt_exclusive", "Developer Content", "If box is checked, this content can only be edited by developers."), "cluster_template_items", "EXCLUSIVE", $cond, 1, 0));
                           				 $this->add(new NonDisplayedValue("cluster_template_items", "FKID", $cond, "1", "NUMBER"));                           				 
                           				 break;

					}

					$this->add(new Hidden("editing", $id));
					
				}

				function process_object_dispatcher($id, $action) {
					global $lang, $go, $page_action, $page_state;
					
					// $go = value("go");
					if ($action == $lang->get("delete")) {
						$this->drawPage = "deleting";
						$text = '<br>'.$lang->get("delete"). ": <b>" . getDBCell($this->item_table, $this->item_name, $this->item_pkname . " = $id"). "</b><br>";
						$text .= "<input type=\"hidden\" name=\"did\" value=\"$id\">";
						$this->addTopYNPrompt($text . $lang->get("clti_delete"));
					} else if ($action == $lang->get("config")) {

						// configure DBO
						$page_action = "UPDATE";

						$page_state = "";
						$go = "UPDATE";
						$this->draw_edit($id);

						$this->drawPage = "editing";
					} else if ($action == $lang->get("up")) {
						$this->move_up($id);

						$go = "UPDATE";
					} else if ($action == $lang->get("down")) {
						$this->move_down($id);

						$go = "UPDATE";
					}

					// update output!
					$this->filterItems();
				}

				/**
				 * Updates the changes towards the object with the given id.
				 * @param integer Id of the item, to be deleted.
				 */
				function process_object_update($id) {
					global $lang, $errors, $db, $page_action, $page_state, $selectci;
					$selectci = value("selectci");
					$cond = $this->item_pkname . " = $id";
					// configure DBO
					$page_action = "UPDATE";
					$page_state = "processing";					
					$this->add(new Hidden("editing", $id));
					$this->check();
					$this->drawPage = "editing";
					if ($errors == "") {
						$name = value($this->item_table . "_" . $this->item_name);

						global $c_magic_quotes_gpc;

						if ($c_magic_quotes_gpc == 1)
							$name = str_replace("\\", "", $name);

						$pname = parseSQL($name);
						$type = value($this->item_table . "_FKID", "NUMERIC");
						$mincard = value($this->item_table . "_MINCARD", "NUMERIC");
						$maxcard = value($this->item_table . "_MAXCARD", "NUMERIC");
						$exclusive = value($this->item_table . "_EXCLUSIVE", "NUMERIC", "0");
						// SWAP
						if ($mincard > $maxcard) {
							$temp = $mincard;

							$mincard = $maxcard;
							$maxcard = $temp;
						}
						$cltitypeId = getDBCell($this->item_table, "CLTITYPE_ID", $cond);
						$fkId = getDBCell($this->item_table, "FKID", $cond);

						$uds = new UpdateSet("cluster_template_items", "CLTI_ID = $id");

						switch ($cltitypeId) {
							case 1: // static content
								$uds->add("NAME", $pname, "TEXT");

								global $sel_object;
								
								$sel_object = value("cluster_template_items_FKID");
								$uds->add("FKID", $sel_object, "NUMBER");																		
								break;

							case 2: // dynamic content
								if ($fkId == 0)
									$uds->add("FKID", $type, "NUMBER");

								$uds->add("MINCARD", $mincard, "NUMBER");
								$uds->add("MAXCARD", $maxcard, "NUMBER");
								$uds->add("NAME", $pname, "TEXT");
								$uds->add("EXCLUSIVE", $exclusive, "NUMBER");
								if ($fkId != 0) {
									$classname = getDBCell("modules", "CLASS", "MODULE_ID = ".$fkId);									
									$ref = new $classname(0, $id);								
									$ref->editConfig();
									$ref->processConfig();

								}	
								break;

							case 3: // static cluster
								$uds->add("NAME", $pname, "TEXT");

								global $sel_object;
								$sel_object = value("sel_object", "NUMERIC");

								if ($sel_object != 0) {
									$uds->add("FKID", $sel_object, "NUMBER");
									
								}

								break;

							case 4: //dynamic cluster
								if ($fkId == 0) {
									global $cluster_template_items_FKID;
									
									$cluster_template_items_FKID = value("cluster_template_items_FKID", "NUMERIC");
									$uds->add("FKID", $cluster_template_items_FKID, "NUMBER");
								}

								$uds->add("MINCARD", $mincard, "NUMBER");
								$uds->add("MAXCARD", $maxcard, "NUMBER");
								$uds->add("NAME", $pname, "TEXT");
								$uds->add("EXCLUSIVE", $exclusive, "NUMBER");
								break;

							case 5:
								if ($fkId == 0)
									$uds->add("FKID", $type, "NUMBER");

								$uds->add("MINCARD", $mincard, "NUMBER");
								$uds->add("MAXCARD", $maxcard, "NUMBER");
								$uds->add("NAME", $pname, "TEXT");
								$uds->add("EXCLUSIVE", $exclusive, "NUMBER");
								break;
							case 6: // compound
								if ($fkId == 0) {
									global $cluster_template_items_FKID;
									$cluster_template_items_FKID = value("cluster_template_items_FKID", "NUMERIC");
									$uds->add("FKID", $cluster_template_items_FKID, "NUMBER");
								}

								$uds->add("MINCARD", $mincard, "NUMBER");
								$uds->add("MAXCARD", $maxcard, "NUMBER");
								$uds->add("NAME", $pname, "TEXT");
								$uds->add("EXCLUSIVE", $exclusive, "NUMBER");
								break;
							case 7: // compound group

								global $cluster_template_items_FKID;
								$cluster_template_items_FKID = value("cluster_template_items_FKID", "NUMERIC");
								$uds->add("FKID", $cluster_template_items_FKID, "NUMBER");
								$uds->add("MINCARD", 1, "NUMBER");
								$uds->add("MAXCARD", 1, "NUMBER");
								$uds->add("NAME", $pname, "TEXT");								
								break;

                          		  case 8: //channel                               			
                               			 $uds->add("MINCARD", $mincard, "NUMBER");
                               			 $uds->add("MAXCARD", $maxcard, "NUMBER");
                               			 $uds->add("FKID", 1, "NUMBER");
                               			 $uds->add("NAME", $pname, "TEXT");
                               			 $uds->add("EXCLUSIVE", $exclusive, "NUMBER");
                              			  break;
						}

						global $search;											
						$search = value("search");
						if ($search == "0") {
							$uds->execute();							
							if ($errors == "") {
								$this->ready = true;	
								$this->addToTopText($lang->get("savesuccess"));
							} else {
								$this->addToTopText($lang->get("procerror"));
								$this->setTopStyle("errorheader");
							}
						}
					} else {
						// display errors.
					}
					$this->draw_edit($id);
				}

				
				/**
				 * Returns the type of the object on position 
				 * @param integer Position of the object information is asked for
				 */
				function getType($position) {
					return getDBCell($this->new_table, $this->new_name, $this->new_pkname . "=" . $this->objects[$position]["plugin"]);										
				}
				
				/**
				 * Returns the configuration of the object on position 
				 * @param integer Position of the object information is asked for
				 */
				function getInformation($position) {
					global $lang;
					$result = "";
					$fkid = getDBCell($this->item_table, "FKID", $this->item_pkname . " = " . $this->objects[$position]["id"]);
					if ($fkid == 0) {
						$result = "<span style=\"color:#990000;\">" . $lang->get("notconfigured"). "</span>";
					} else {
						$type = getDBCell("cluster_template_items", "CLTITYPE_ID", "CLTI_ID = " . $this->objects[$position]["id"]);
						switch ($type) {
							case 1:
								$result = $lang->get("linked"). " ".getDBCell("content", "NAME", "CID = $fkid");
								break;
							case 2:
								$result = $lang->get("type"). "&nbsp;" . getDBCell("modules", "MODULE_NAME", "MODULE_ID = $fkid");
								break;
							case 3:
								$result = $lang->get("linked").  " ".getDBCell("cluster_node", "NAME", "CLNID = $fkid");
								break;
							case 4:
								$result = $lang->get("type"). "&nbsp;" . getDBCell("cluster_templates", "NAME", "CLT_ID = $fkid");
								break;
							case 5:
								$result = $lang->get("type"). "&nbsp;" . getDBCell("modules", "MODULE_NAME", "MODULE_ID = $fkid");
								break;
							case 6:
								$result = $lang->get("cmptype", "Compound type:"). "&nbsp;";
								if ($fkid==-1) {
									$result.= $lang->get("not_specified", "Any type");	
								} else {
									$result.= getDBCell("cluster_templates", "NAME", "CLT_ID = $fkid");
								}
								break;
							case 7:
								$result = $lang->get("cp_group"). "&nbsp;" . getDBCell("compound_groups", "NAME", "CGID = $fkid");
                         		break;
                            case 8: //channel.
                                 $result = $lang->get("ready_to_use", "Ready to use.");
						}
					}
					return $result;
				}
								
			}

			// -- end of ClusterTemplatForm --
			$form = new ClusterTemplateForm($lang->get("clt_scheme"));
			$form->width = '100%';
			$form->headerlink = crHeaderLink($lang->get('ed_properties'), "modules/clustertemplate/clustertemplates.php?sid=$sid&oid=".value("oid", "NUMERIC")."&action=objectprop&go=update");				
			$form->define_home("cluster_templates", "CLT_ID", $oid);
			$form->define_item("cluster_template_items", "CLTI_ID", "NAME", "CLT_ID", "CLTITYPE_ID");
			$form->define_new("cluster_template_item_types", "CLTITYPE_ID");
			$form->add(new Hidden("action", "editobject"));
			$page->add($form);
			
		}
		
	} else {
	  $page_action = "update";
	  $form = new Form($lang->get("clt"));

	  
  	  include $c["path"] . "modules/common/buttonbar_folder.inc.php";

	 // Build breadcrumb
  	  if ($pnode == "")
	  $pnode = "0";
	
	  $basehref = '<a href="' . $c["docroot"] . 'modules/clustertemplate/clustertemplates.php?sid=' . $sid . '&pnode=';
  	  $str_base = $basehref . '0">Root &gt;</a> ';
	  $str = "";
	  $tmp = $pnode;
	
	  while ($tmp != "0") {
			$str = $basehref . "$tmp\">" . getDBCell("categories", "CATEGORY_NAME", "CATEGORY_ID = $tmp"). "	&gt;</a> " . $str;
			$tmp = getDBCell("categories", "PARENT_CATEGORY_ID", "CATEGORY_ID = $tmp");
	  }
	
	  $str = $str_base . $str;
	  
	  $form->add(new Spacer(2));
	  $form->add(new Label("lbl", getBoxedText($str, 'headergrey', '100%'), "", 2));
	  $form->add(new Spacer(2));
			  
	 	  
	  $titles = array($lang->get("name", "Name"));
	  $rows = array();
		
	  $sql = "SELECT NAME, CLT_ID FROM cluster_templates WHERE CATEGORY_ID = $pnode AND DELETED=0 AND VERSION=0 ORDER BY NAME";
  	  $query = new query($db, $sql);
	
	  while ($query->getrow()) {
			array_push($rows, array($query->field("CLT_ID"), $query->field("NAME")));
	  }
	  if (count($rows)>0) {
		$form->add(new EmbeddedMenu($titles, $rows, $c["docroot"]."modules/clustertemplate/clustertemplates.php?sid=$sid&action=editobject&go=update&oid="));
	  } else {
		$form->add(new Label("lbl", "<center>" . $lang->get("no_clustertemplates", "There are no cluster-templates."). "</center>", "standard", 2));	
	  }		
	  $form->add(new Hidden("pnode", $pnode));
	  $form->add(new Hidden("action", ""));
	  $page->add($form);
	}
	
?>