<?php
  $nm = new StdMenu($lang->get("newsletter", "Newsletter"));
  $nm->addMenuEntry($lang->get("newsletters", "Newsletters"), "overview.php", "NEWSLETTERADM");
  $nm->addMenuEntry($lang->get("subscriptions", "Subscriptions"), "subscriptions.php", "SUBSCRIPTIONS");
  $page->addMenu($nm);
?>