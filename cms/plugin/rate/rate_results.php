<?php

  require_once "../../config.inc.php";
  $auth = new auth("ANALYSE_RATINGS");
  $page_action="update";
  $go="update";
  $page = new Page("Rating Results");
  $form = new EditForm($lang->get("rateres", "Rate Results"));
  
  $interval = array();
  $count = countRows("pgn_rating", "RATINGID", "COMMENT <>''");
  $steps = ($count /50);
  for ($i=0; $i < $steps; $i++) {
    $interval[$i][0] = ($i*50)." - ".((($i+1)*50)-1);
    $interval[$i][1] = $i;
  }
  $form->add(new Label("lbl", $lang->get("display", "Display"), "standard"));
  $form->add(new Select("display", $interval, "standardlight", value("display"), 1));
  
  $grid = new NXGrid("grid", 3);
  $grid->setRatio(array (
			150,
			350,
			100
		));

  $grid->addRow(array (
			new Label("lbl", "<b>" . $lang->get("page", "Page"). "</b>"),
			new Label("lbl", "<b>".$lang->get("comment", "Comment")."</b>"),
			new Label("lbl", "<b>".$lang->get("date", "Date")."</b>")
		));
  $thisInterval = value("interval");
  
  $sql= "SELECT SOURCEID, COMMENT, TIMESTAMP FROM pgn_rating WHERE COMMENT <> '' ORDER BY TIMESTAMP DESC";
  $query = new query($db, $sql);
  while ($query->getrow()) {
    $grid->addRow(array (
			new Label("lbl", resolvePageToLink($query->field("SOURCEID"))),
			new Label("lbl", str_replace('\\\\', '\\', $query->field("COMMENT"))),
			new Label("lbl", formatDBTimestamp($query->field('TIMESTAMP')))
		));	
  }
  $query->free();
  
  
  $form->add($grid);
  
  $page->add($form);
  $page->draw();
?>