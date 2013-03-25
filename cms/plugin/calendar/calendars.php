<?php


  require_once "../../config.inc.php";
  $auth = new auth("CALENDAR_CREATE");
  $page = new Page("Edit Calendars");

  $selcal = new SelectMenu($lang->get("calendar_select", "Select Calendar"), "calsel", "pgn_cal_calendars", "NAME", "CALID", "1");

  $filter = new Filter("pgn_cal_calendars", "CALID");
  $filter->addRule($lang->get("cal_name", "Calendar Name"), "NAME", "NAME");
  $filter->type_name = $lang->get("calendars", "Calendars");

  $filtermenu = new Filtermenu("", $filter);
  $filtermenu->addMenuEntry($lang->get("calendars_edit", "Edit Calendar"), "edit.php", "", "CALENDAR_EDIT");
  $filtermenu->addMenuEntry($lang->get("calendars_define", "Define Calendars"), "calendars.php", "", "CALENDAR_CREATE");
  $filtermenu->addMenuEntry($lang->get("calendars_cat_define", "Define Categories"), "categories.php","",  "CALENDAR_CREATE");
  $filtermenu->tipp = $lang->get("calendars_tipp", "You can create several calendars here. Each calendar will have its own events and dates.");

  $deleteHandler = new ActionHandler("DELETE");
  $deleteHandler->addDbAction("DELETE FROM pgn_cal_calendars WHERE CALID = $oid");
  $deleteHandler->addDbAction("DELETE FROM pgn_cal_appointment WHERE CALID = $oid");
  $deleteHandler->addDbAction("DELETE FROM pgn_cal_categories WHERE CALID = $oid");

    if ($oid == 0) {
        $addtext = "";
    } else {
        $addtext = ": " . getDBCell("pgn_cal_calendars", "NAME", "CALID = " . $oid);
    }

    $form = new stdEDForm($lang->get("calendar", "Calendar"). $addtext);
    $cond = $form->setExPK("pgn_cal_calendars", "CALID");

    $form->add(new TextInput($lang->get("name"), "pgn_cal_calendars", "NAME", $cond, "type:text,width:200,size:64", "MANDATORY&UNIQUE"));
    $form->registerActionHandler($deleteHandler);

  $page->addMenu($filtermenu);

  $page->add($form);
  $page->draw();
?>