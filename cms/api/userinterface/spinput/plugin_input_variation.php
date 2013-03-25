<?
/**********************************************************************
 *	N/X - Web Content Management System
 *	Copyright 2002-2003 Sven Weih
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
 * Enables you to use the edit-panel of a plugin for a User Interface Control
 * Stores the data as a variation with help of content_variations table.
 * So the join is table.column = content_variations.cid and content_variations.fk_id=pgn.fk
 * Make sure that the global variable variation is set.
 * @package DatabaseConnectedInput
 */
 class PluginInputVariation  extends WUIInterface {

 	var $label;
 	var $table;
 	var $column;
 	var $cond;
 	var $pgnname;
 	var $pgntypeid;
 	var $style;
 	var $errortext;
 	var $v_label = null;
 	var $form;
 	var $variation;

 	/**
	 * Standard Constructor
	 *
	 * @param string Name to display on top.
	 * @param string Table where the FK is saved in
	 * @param string Column where the FK id saved in to reference the plugin.
	 * @param string Where-Clause to select a FK
	 * @param string Name of the plugin.
	 * @param string A reference to the form ýou are using.
	 * @param boolean show preview of the plugin.
	 * @param string style of the control.
	 */
 	function PluginInputVariation($label, $table, $column, $cond, $pgnname, & $form, $showpreview=false, $style="standard") {
 		global $page_state, $page_action, $variation, $db;
 		$this->label = $label;
 		$this->table = $table;
 		$this->column = $column;
 		$this->cond = $cond;
 		$this->pgnname = $pgnname;
 		$this->style = $style;
 		$this->form = &$form;
 		$this->variation = $variation;
 		if ($this->variation == "")
 		$this->variation = variation();

 		$this->v_label = new SubTitle("lbl", $label, 2);

 		//now retrieve the values....
 		$this->pgntypeid = getDBCell("modules", "MODULE_ID", "UPPER(MODULE_NAME) = '".strtoupper($pgnname)."'");
 		if ($this->pgntypeid =="") {
 			$this->errortext = "<center> The Plugin $pgnname is not installed. </center>";
 		} else {
 			includePGNSource($this->pgntypeid);
 			$form->add($this->v_label);
 			if ($page_action == "UPDATE") {
 				$cid = getDBCell($table, $column, $cond);
 				if (($cid != "") && ($cid != "0")) {
 				  $fkid = getDBCell("content_variations", "FK_ID", "VARIATION_ID = $this->variation AND CID=".$cid);
 				} else {
 					$fkid = ""; 					
 				}
 				if ($fkid !="") {
 					$ref = createPGNRef($this->pgntypeid, $fkid);
 					if ($showpreview) {
 						$preview = $ref->preview();
 						$this->form->add(new Label("lbl", $preview, "", 2));
 					}
 					$ref->edit($this->form);
 				} else {
 					// Create the other language version. 					
 					$fkid = value("PGFK".$this->table.$this->column, "NUMERIC");
 					if ($fkid == "0")
 					  $fkid = nextGUID();
 					$ref = createPGNRef($this->pgntypeid, $fkid);
 					if ($showpreview) {
 						$preview = $ref->preview();
 						$this->form->add(new Label("lbl", $preview, "", 2));
 					}
 					$ref->edit($this->form);
 					$this->form->add(new Hidden("PGFK".$this->table.$this->column, $fkid));
 					if ($page_state == "processing") {
 						$cid = nextGUID();
 						$ref->createRecord();
 						$sql = "INSERT INTO content_variations (CID, FK_ID, VARIATION_ID, DELETED) VALUES($cid, $fkid, $this->variation,0)";
 						$query = new query($db, $sql);
 						$query->free();
 						addUpdate($table, $column, $cid, $cond, "NUMBER"); 					
 					}
 				}
 			} else {
 				$fkid=value("PGFK".$this->table.$this->column, "NUMERIC");
 				if ($fkid=="0") {
 					$fkid = nextGUID();
 					$ref = createPGNRef($this->pgntypeid, $fkid);
 				} else {
 					$ref = createPGNRef($this->pgntypeid, $fkid);
 				}
 				$ref->edit($this->form);
 				$this->form->add(new Hidden("PGFK".$this->table.$this->column, $fkid));
 				global $page_state;
 				if ($page_state == "processing") { 			
 						$cid = nextGUID();
 						$sql = "INSERT INTO content_variations (CID, FK_ID, VARIATION_ID, DELETED) VALUES($cid, $fkid, $this->variation,0)";
 						$query = new query($db, $sql);
 						$query->free();
 						addUpdate($table, $column, $cid, $cond, "NUMBER"); 						 					
 				}
 			}
 		} // plugin does exist
 	}

 }

 ?>