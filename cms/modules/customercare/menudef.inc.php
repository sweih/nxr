<?php
$menu->addMenuEntry("Open Requests", "index.php?status=open&sid=$sid");
$menu->addMenuEntry("Closed Requests", "index.php?status=closed&sid=$sid");
$menu->addMenuEntry("Edit Textblocks", "texts.php");
if ($auth->checkPermission("ADMINISTRATOR")) $menu->addMenuEntry("Edit Categories", "category.php");
?>