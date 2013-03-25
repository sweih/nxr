<?
	/**********************************************************************
	 *	N/X - Web Content Management System
	 *	Copyright 2004 Sven Weih (sven@nxsystems.org), Fabian Koenig (fabian@nxsystems.org)
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
	 * Container for displaying a CompoundCluster-Link in a cluster. The trick is,
	 * that also Template-Items with more than one instances are supported.
	 * @package ContentManagement
	 */
	class CompoundClusterEnvelope extends AbstractEnvelope {
				

	/**
	 * Draw the input boxes needed for editing the contents in the envelope.
	 * @param integer id of cluster_content.CLCID
	 */
	function getSingleEdit($id) {
		global $specialID, $lang, $c, $sid, $aclf, $db, $lang;
        global $forceLoadFromDB, $auth;

       $clt = getDBCell("cluster_template_items", "FKID", "CLTI_ID = ".$this->clti);

       if ($this->saction == "createCluster" && $id == value("id")) {
                $name = parseSQL(value("cluster_node_NAME".$id));
                if ($name=="0") $name = "Cluster";
                $name = makeCopyName("cluster_node", "NAME", $name, "1", "", false);
                $clnid = createClusterNode($name, $clt);
                $variations = createDBCArray("variations", "VARIATION_ID");
                for ($varX=0; $varX < count($variations); $varX++) {
                        $clid = createCluster($clnid, $variations[$varX], $auth->userName);
                }

                $sql = "UPDATE cluster_content SET FKID = $clnid WHERE CLCID = $id";
                $query = new query($db, $sql);
                $query->free();
                $forceLoadFromDB = "yes";
        }

        if ($this->editState && $this->editor) {
            $chclbox = new Container(4);
				$compoundEnvelope = new Container(3);
				$compoundContainer = new Container(3);

                $specialID = $id;
                $cpselector = new CPCLSelector($lang->get("select_cl"), "cluster_content", "FKID", "CLCID=$id", getModuleFromCLC($id), "", "");
                $cpselector->additionalAttribute = "onchange=\" if ( !confirm('".$lang->get("confirm_unsaved_changes_linkedcluster", "Note: When changing the linked cluster, any changes you apply on the currently linked cluster will be lost. If you want to save these canges, save your work first and change the linked cluster then. Proceed ?")."') ) { for(i=0; i<document.form1.cluster_content_FKID$specialID.length; i++)  if(document.form1.cluster_content_FKID$specialID.options[i].defaultSelected == true) document.form1.cluster_content_FKID$specialID.options[i].selected=true; }\"";
                $variation = variation();
                // force save in dbo.
                //$clnid = getDBCell("cluster_content", "FKID", "CLCID = ".$id);
                if (!isset($clnid)) {
                    $clnid = $cpselector->value;
                } else {
                    $cpselector->value = $clnid;
                }
                $clid = getClusterFromNode($clnid, $variation);
                $chclbox->add( $cpselector );
                $forceLoadFromDB = "no";


                if ($clnid != "" && $clnid != "0") {
				    $clid = getClusterFromNode($clnid, $variation);
                    $name = getDBCell("cluster_node", "NAME", "CLNID = $clnid");
                }
                
				$infoboxid = $id;
				$chclboxid = $id;

				$table = '<table width="100% border="0" cellpadding="0" cellspacing="0"><tr><td>';
				$table.= $lang->get("cllink", "This box is linked to ")."&nbsp; <b>".$name."</b>";
				$table.= '</td><td align="right">';
				// Add-Button
				$ShowInfoButton = new LinkButtonInline("toggle_info_".$infoboxid, "show info", "box", "button", "toggle('showinfo_".$infoboxid."')");
				$CHCLButton = new LinkButtonInline("toggle_chcl_".$chclboxid, "change cluster", "box", "button", "toggle('chcl_".$chclboxid."')");
				$table.= $ShowInfoButton->draw()."&nbsp;".$CHCLButton->draw();
				$table.= '</td></tr></table>';
				
				$this->add(new Label("lbl", $table, "headbox", 2));

				if (getDBCell("cluster_content", "FKID", "CLCID = ".$id) != $clnid)
					$forceLoadFromDB = "yes";
	

			    $chclbox->add(new Cell("spacer", "standardlight", 1));
			    $chclbox->add(new Label("lbl", "or create a new instance called", "standardlight"));
			    $chclbox->add(new Input("cluster_node_NAME$id", "", "standardlight", 32));
			    $chclbox->add(new SingleHidden("clt", ""));
			    $chclbox->add(new SingleHidden("id", ""));
			    $chclbox->add(new LinkButtonInCell("neueInstanz", "Create Instance", "standardlight navelement", "button", "javascript:if (confirm('".$lang->get("confirm_unsaved_changes")."')) { document.form1.saction.value='createCluster';document.form1.id.value='$id';document.form1.clt.value='$clt';document.form1.submit(); };", "form1", 1));

			    $sql = "SELECT * FROM variations WHERE 1";
			    $variations = new query($db, $sql);
			    while ($variations->getrow()) {
				    $chclbox->add(new Hidden("cluster_variations_VARIATION_ID_".$variations->field("VARIATION_ID"), "1"));
			    }

			    $specialID = "";
                $compoundContainer->add(new IDWrapper("chcl_".$chclboxid, $chclbox, "embedded", ' style="display:'.(($clid == 0) ? "" : "none" ).';" ', 3));

                if ($clid != "0" && $clid != "") {
        			// GET CONTENT OF THE CLUSTER
    				// set variables that will contain the content later to null.
    				$clusters = null;
    				$plugins = null;
    				$types = null;

    				// get the structure of the content.
    				$sql = "SELECT CLTI_ID, CLTITYPE_ID FROM cluster_template_items WHERE CLT_ID = $clt AND FKID!=0 ORDER BY POSITION";

    				$query = new query($db, $sql);

    				while ($query->getrow()) {
    					$cltitype = $query->field("CLTITYPE_ID");

    					$ni = count($plugins);
    					$plugins[$ni] = $query->field("CLTI_ID");
    					$types[$ni] = $cltitype;
    				}

    				$query->free();

    				// we don't want to draw an additional back link in clusters-editor
    				// if (! $sitepage && ! $isArticle) {
    				//	$compoundContainer->add(new LinkLabel("link1", $lang->get("back_to_cv", "Back to cluster overview"), "modules/cluster/clusterbrowser.php?sid=$sid&clt=$clt", "_self", "informationheader", 2));
    				// }

    				$infobox = new Container(3);

    				// draw some cluster-information.
    				$infobox->add(new Subtitle("", $lang->get("cluster_information", "Information about this record"), 3));
    				$infobox->add(new ClusterInformation($clid));

    				$compoundContainer->add(new IDWrapper("showinfo_".$infoboxid, $infobox, "embedded", ' style="display:none;" ', 3));
    				// draw plugin preview.

    				$len = count($plugins);
    				if ($clid) {
    					for ($i = 0; $i < $len; $i++) {

                            if ($types[$i] == 2)
    							$compoundContainer->add(new ContentEnvelope($plugins[$i], $clid, true));

    						if ($types[$i] == 4)
    							$compoundContainer->add(new ClusterEnvelope($plugins[$i], $clid, true));

    						if ($types[$i] == 5)
    							$compoundContainer->add(new LibraryEnvelope($plugins[$i], $clid, true));

    						if ($types[$i] == 6)
    							$compoundContainer->add(new CompoundClusterEnvelope($plugins[$i], $clid, true));

    				        if ($types[$i] == 8)
    				          $compoundContainer->add(new ChannelEnvelope($plugins[$i], $clid, true));
    					}
    					if ($isArticle || $action == $lang->get("edit_all") || ($action == $lang->get("save")) || $action == $lang->get("save_back")) {
    				       	$compoundContainer->add(new NonDisplayedValue("cluster_variations", "LAST_CHANGED", "CLID = ".$clid, "(NOW()+0)", "NUMBER"));
    						$compoundContainer->add(new NonDisplayedValue("cluster_variations", "LAST_USER", "CLID = ".$clid, $auth->userName, "TEXT"));
    					}
    				}
                }
                $compoundEnvelope->add(new IDWrapper("compoundEnvelope_$id", $compoundContainer, "embedded sub", '', 3));
                $this->add(new IDWrapper("compoundCluster_$id", $compoundEnvelope, "boxed", '', 3));

		} else {
			$name = "&lt;" . $lang->get("not_selected", "No item selected yet."). "&gt;";
			$myfk = getDBCell("cluster_content", "FKID", "CLCID = " . $id);
			if ($myfk != 0 && $myfk != "") {
				$clt = getDBCell("cluster_node", "CLT_ID", "CLNID = " . $myfk);
				$cltname = getDBCell("cluster_templates", "NAME", "CLT_ID = ".$clt);
				$cat = getDBCell("cluster_templates", "CATEGORY_ID", "CLT_ID = " . $clt);
				$name = "<br><b>" . $lang->get("cli"). "</b> " .$cltname."/".getDBCell("cluster_node", "NAME", "CLNID = " . $myfk);
				$buttons = "<br><a href=\"" . $c["docroot"] . "modules/cluster/clusterbrowser.php?sid=$sid&action=editobject&go=update&oid=$myfk&pnode=$cat&clt=$clt\" class=\"box\">".$lang->get("goto_cl", "Goto Cluster")."</a>";
				if ($aclf->checkAccessToFunction("B_PREVIEW_PAGE")) $buttons .= drawSpacer(10, 1). "<a href=\"#\" onClick=\"window.open('" . $c["docroot"] . "modules/cluster/previewcpcl.php?sid=$sid&oid=$myfk', 'cppreview', 'width=400,height=400,location=no,menubar=no,toolbar=no');return false;\"  class=\"box\">".$lang->get("preview")."</a>";
			}
			$this->add(new Label("lbl", $name, "", 1));
			$this->add(new AlignedLabel('lbl', $buttons, 'right', '', 1));
		}

	}
}
?>