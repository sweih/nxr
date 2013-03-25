<?php

  require_once "../../config.inc.php";
  $auth = new auth("CALENDAR_CREATE");
  $page = new Page("Edit Calendars");

  $selch = new SelectMenu($lang->get("channel_select", "Select Channel"), "chsel", "channels", "NAME", "CHID", "1");

  $filter = new Filter("channel_categories", "CH_CAT_ID");
  $filter->addRule($lang->get("cat_name", "Category"), "NAME", "NAME");
  $filter->setAdditionalCondition(" CHID=".$selch->selected." AND CHID <> 0");
  $filter->type_name = $lang->get("category", "Category");
  $filter->icon = "li_categories.gif";
  
  $filtermenu = new Filtermenu("", $filter);
  include $c["path"]."modules/channels/menu.inc.php";

  if ((strtoupper(value("deletion")) ==	"DELETE") && (strtoupper(value("commit")) == "YES")) {
  	deleteChannelCategory(value("oid", "NUMERIC"));
  }
  
  
    if ($oid == 0) {
        $addtext = "";
    } else {
        $addtext = ": " . getDBCell("channel_categories", "NAME", "CH_CAT_ID = " . $oid);
    }
    if ($selch->selected != "0" && $selch->selected != "-1" && $selch->selected != 0) {
      $form = new stdEDForm($lang->get("category"). $addtext);
      $cond = $form->setExPK("channel_categories", "CH_CAT_ID");
      $nameInput = new TextInput($lang->get("name"), "channel_categories", "NAME", $cond, "type:text,width:300,size:64", "MANDATORY&UNIQUE");
      $nameInput->setFilter("CHID = ".$selch->selected);
      $form->add($nameInput);
      $form->add(new SitepageSelector($lang->get("outputpage", "Output Page", 'Page where the articles will be rendered to.'),'channel_categories', 'PAGE_ID', $cond));
      $form->add(new NonDisplayedValueOnInsert("channel_categories", "CHID", $cond, $selch->selected, "NUMBER"));
      $page->add($form);
  }

  $page->addMenu($selch);
  $page->addMenu($filtermenu);
  $page->draw();
?>