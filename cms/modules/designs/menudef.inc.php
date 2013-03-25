<?php
  $menu = new StdMenu($lang->get("designs"));
  $menu->addMenuEntry($lang->get("website_designs", "Website Designs"), $c["docroot"].'modules/designs/overview.php');		
  $page->addMenu($menu);
?>