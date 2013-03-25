<?php

  require_once "../../config.inc.php";
  $auth = new auth("CALENDAR_CREATE");
  $page = new Page("Edit Calendars");

  $selcal = new SelectMenu($lang->get("calendar_select", "Select Calendar"), "calsel", "pgn_cal_calendars", "NAME", "CALID", "1");

  $filter = new Filter("pgn_cal_categories", "CATID");
  $filter->addRule($lang->get("cat_name", "Category"), "NAME", "NAME");
  $filter->setAdditionalCondition(" CALID=".$selcal->selected." AND CALID <> 0");
  $filter->type_name = $lang->get("category", "Category");

  $filtermenu = new Filtermenu("", $filter);
  $filtermenu->addMenuEntry($lang->get("calendars_edit", "Edit Calendar"), "edit.php", "", "CALENDAR_EDIT");
  $filtermenu->addMenuEntry($lang->get("calendars_define", "Define Calendars"), "calendars.php", "", "CALENDAR_CREATE");
  $filtermenu->addMenuEntry($lang->get("calendars_cat_define", "Define Categories"), "categories.php","",  "CALENDAR_CREATE");

  $deleteHandler = new ActionHandler("DELETE");
  $deleteHandler->addDbAction("DELETE FROM pgn_cal_calendars WHERE CATID = $oid");
  $deleteHandler->addDbAction("DELETE FROM pgn_cal_categories WHERE CATID = $oid");

    if ($oid == 0) {
        $addtext = "";
    } else {
        $addtext = ": " . getDBCell("pgn_cal_categories", "NAME", "CATID = " . $oid);
    }

    if ($selcal->selected != "0" && $selcal->selected != "-1") {
      $form = new stdEDForm($lang->get("category"). $addtext);
      $cond = $form->setExPK("pgn_cal_categories", "CATID");
      $nameInput = new TextInput($lang->get("name"), "pgn_cal_categories", "NAME", $cond, "type:text,width:300,size:64", "MANDATORY&UNIQUE");
      $nameInput->setFilter("CALID = ".$selcal->selected);
      $form->add($nameInput);
      $form->add(new TextInput($lang->get("description"), "pgn_cal_categories", "DESCRIPTION", $cond, "type:textarea,width:300,size:3", ""));
      $form->add(new TextInput($lang->get("color", "Color"), "pgn_cal_categories", "COLOR", $cond, "type:color,param:form1", ""));
      $form->add(new NonDisplayedValueOnInsert("pgn_cal_categories", "CALID", $cond, $selcal->selected, "NUMBER"));
      $form->registerActionHandler($deleteHandler);
      $page->add($form);
  }

  $page->addMenu($selcal);
  $page->addMenu($filtermenu);
  $page->draw();
  echo $errors;
?>