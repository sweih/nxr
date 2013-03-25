<?php

	/**
	   * draw an input field for meta-Data
	   * @param string Headline for this field.
	   * @param string Meta-Template, which is to be used.
	   */
	function draw_metaInput($headline, $mt_id) {
		global $db, $specialID, $metaPanel, $clnid;

		//checking, if there are any items in the template.
		$sql = "SELECT COUNT(MTI_ID) AS ANZ FROM meta_template_items WHERE MT_ID = $mt_id";

		$query = new query($db, $sql);
		$query->getrow();
		$amount = $query->field("ANZ");

		if ($amount > 0) {
			$sql = "SELECT m.MID AS D1, t.MTYPE_ID AS D2, t.NAME AS D3 FROM meta_template_items t, meta m WHERE m.MTI_ID = t.MTI_ID AND m.CID = " . $clnid . " AND m.DELETED=0 AND t.MT_ID = $mt_id ORDER BY t.POSITION ASC";

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
					$metaPanel->add($obj[$i]);
			}

			$specialID = "";
		}
	}

	syncMetas($clnid, "CLUSTER");
	$std = 0;
	$add = getDBCell("cluster_templates", "MT_ID", "CLT_ID = " . $clt);
	draw_metaInput($lang->get("mt_base"), $std);
	draw_metaInput($lang->get("mt_additional"), $add);
	//$editbar = new ButtonBar("edtbar", "informationheader");
	//$editbar->add("action", $lang->get("save"));
	//$editbar->add("reset", $lang->get("reset"), "reset");
	if ($isArticle) {
			$editbar = new FormButtons();
	} else {
		$editbar = new FormButtons(true, true);
	}
	$metaPanel->add(new Cell("clc", "white", 617,1,2));
	$metaPanel->add(new Hidden("view", $view));
	$metaPanel->add(new Hidden("processing", "yes"));
	$metaPanel->add($editbar);
?>