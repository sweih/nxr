<?php

  require_once "../../config.inc.php";

  $auth = new auth("CALENDAR_EDIT");
  $page = new Page("Edit Calendars");
  $selcal = new SelectMenu($lang->get("calendar_select", "Select Calendar"), "calsel", "pgn_cal_calendars", "NAME", "CALID", "1");
  $filtermenu = new StdMenu("");
  $filtermenu->addMenuEntry($lang->get("calendars_edit", "Edit Calendar"), "edit.php", "", "CALENDAR_EDIT");
  $filtermenu->addMenuEntry($lang->get("calendars_define", "Define Calendars"), "calendars.php", "", "CALENDAR_CREATE");
  $filtermenu->addMenuEntry($lang->get("calendars_cat_define", "Define Categories"), "categories.php","",  "CALENDAR_CREATE");

  $deleteHandler = new ActionHandler("DELETE");
  $deleteHandler->addDbAction("DELETE FROM pgn_cal_calendars WHERE CALID = $oid");
  $deleteHandler->addDbAction("DELETE FROM pgn_cal_appointments WHERE CALID = $oid");
  $deleteHandler->addDbAction("DELETE FROM pgn_cal_categories WHERE CALID = $oid");
  $oid = value("oid", "NUMERIC");
  if ($selcal->selected != "0"  && $selcal->selected != "-1") {
    if ($oid == "0" && $page_action != "INSERT") {
      $form = new MenuForm($lang->get("sel_event", "Select Event or Appointment"), array($lang->get("startdate", "Startdate"), $lang->get("starttime", "Starttime"), $lang->get("title", "Title")), "pgn_cal_appointment", "APID", array("STARTDATE", "STARTTIME", "TITLE"), "CALID=".$selcal->selected);
      $form->newAction = "plugin/calendar/edit.php?sid=$sid&go=create";
    } else {
      $deleteHandler = new ActionHandler("DELETE");
      $deleteHandler->addDbAction("DELETE FROM pgn_cal_appointment WHERE APID = $oid");


        $addtext = ": " . getDBCell("pgn_cal_appointment", "TITLE", "APID = " . $oid);
        $form = new stdEDForm($lang->get("cal_edit", "Edit Appointment"). $addtext);        
        $form->addHeaderLink(crHeaderLink('Back', "plugin/calendar/edit.php?sid=$sid"));
        $cond = $form->setExPK("pgn_cal_appointment", "APID");        
        $form->add(new TextInput($lang->get("title"), "pgn_cal_appointment", "TITLE", $cond, "type:text,width:300,size:64", "MANDATORY"));
        $form->add(new RichEditInput($lang->get("description"), "pgn_cal_appointment", "DESCRIPTION", $cond, "type:rich,width:350,size:6", ""));               
        $form->add(new DateInput($lang->get("startdate"), "pgn_cal_appointment", "STARTDATE", $cond, "param:form1", "MANDATORY"));
        $form->add(new TimeInput($lang->get("starttime"), "pgn_cal_appointment", "STARTTIME", $cond));
        $form->add(new DateInput($lang->get("endate"), "pgn_cal_appointment", "ENDDATE", $cond, "param:form1", "MANDATORY"));
        $form->add(new TimeInput($lang->get("endtime"), "pgn_cal_appointment", "ENDTIME", $cond));
        $form->add(new SelectOneInput($lang->get("category"), "pgn_cal_appointment", "CATID", "pgn_cal_categories", "NAME","CATID", "CALID = ".$selcal->selected,  $cond));
        $form->add(new NonDisplayedValueOnInsert("pgn_cal_appointment", "CALID", $cond, $selcal->selected, "NUMBER"));
        $form->add(new SubTitle("st", $lang->get("Report", "Veranstaltungsbericht"), 3));        
        $form->add(new RichEditInput($lang->get("report"), "pgn_cal_appointment", "REPORT", $cond, "type:rich,width:350,size:6", ""));           
        $form->add(new PluginInput("", "pgn_cal_appointment", "GALLERY", $cond, "GALLERY", $form));
        $form->registerActionHandler($deleteHandler);
    }
    $page->add($form);
  }


  $page->addMenu($selcal);
  $page->addMenu($filtermenu);
  $page->draw();
  echo $errors;
?>