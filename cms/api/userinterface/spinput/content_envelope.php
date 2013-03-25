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
	 * Container for displaying a Content-Object in a cluster. The trick is,
	 * that also Template-Items with more than one instances are supported.
	 * @package ContentManagement
	 */
	class ContentEnvelope extends AbstractEnvelope {

           		
		/**
		 * Draw the input boxes needed for editing the contents in the envelope.
		 * @param integer id of cluster_content.CLCID
		 */
		function getSingleEdit($id) {
            global $specialID, $translateSource;
			if (translation() > 0 && $this->editState) {
			  $this->drawTranslateTemplate($id, translation());	
			}
			$ref = createPGNRef($this->plugin, $id);
			$clti = getDBCEll("cluster_content", "CLTI_ID", "CLCID=$id");
			$ref->cltiid = $clti;
			if ($ref != null) {
				if ($this->editState && $this->editor) {
					$specialID = $id;
					$ref->edit($this);
					$specialID = "";
				} else {
					$this->add(new Label("lbl", $ref->preview(), "standardwhite", 3));
				}
			}		
		}
		
		/**
		 * Draw the text (if so) of a plugin.
		 * @param integer FKID of content_variations
		 * @param integer Variation of Source
		 */
		function drawTranslateTemplate($fkid, $variation) {	
           		 global $lang;
			$trans =  translateClusterContentItem($fkid, $variation);
			if ($trans != false && $trans != "") {
			  $module = getModuleFromCLC($trans);
			  $moduleName = getDBCell("modules", "MODULE_NAME", "MODULE_ID = $module");	
			  if (sameText($moduleName, 'Text')) {
			  	$content = getDBCell("pgn_text", "CONTENT", "FKID = $trans");			  	
			  } else if (sameText($moduleName, 'Label')) {
			  	$content = getDBCell("pgn_label", "CONTENT", "FKID = $trans");
			  }
			  
			  if (! sameText('', $content)) {			  	
			  	$out =  "<b>".$lang->get("trans_this", "Translate this:")."</b>";
			  	$parser = new NX2HTML($variation);
			  	$out.= $parser->parseText($content);			  	
			  	$this->add(new Label("lbl", $out, "standardwhite", 3));
			  	$this->add(new Cell("clc", "standard", 3, 600, 1));
			  }
			  
			}
		}
		
		/**
		 * Create Plugin references for new content.
		 * @param integer ID of the content to create.
		 */
		function createReferencedItem($id) {
			$PGNRef = createPGNRef(getModuleFromCLC($id), $id);
			$PGNRef->sync();
		}
		
		
		/**
		 * Delete the data in the plugins.		 
		 * @param integer ID of the content to delete
		 */
		function deleteReferencedItem($id) {
			$module = getModuleFromCLC($id);
			if ($module != "") 
			  $PGNRef = createPGNRef($module, $id);
			if (is_object($PGNRef)) 
			  $PGNRef->deleteRecord();
		}


}

?>