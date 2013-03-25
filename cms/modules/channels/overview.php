<?php

  require_once "../../config.inc.php";
  $auth = new auth("CHANNEL_EDIT");
  $page = new Page("Edit Channels");
  require_once $c["path"]."modules/channels/article_select_form.php";

  if (value("action") == "deletearticle" && $auth->checkAccessToFunction("CHANNEL_DELETE")) {
      $article = value("article", "NUMERIC");
      deleteArticle($article);
  }

  if (value("action") == "launcharticle" && $auth->checkAccessToFunction("CHANNEL_LAUNCH")) {
      $article = value("article", "NUMERIC");
      launchArticle($article, 10, variation());
  }
  
  if (value("action") == "expirearticle" && $auth->checkAccessToFunction("CHANNEL_LAUNCH")) {
      $article = value("article", "NUMERIC");
      expireArticle($article, 10, variation());
  }
  
  $selch = new SelectMenu($lang->get("channel_select", "Select Channel"), "chsel", "channels", "NAME", "CHID", "1");
  $lang->delete("help_articles");
  $selch->tipp = $lang->get("help_articles", "The form displays articles.<br><br>The color codes are:<li>red: article not published<li>grey: article not translated<li>green: article published");
  
  $rowOrderFilter =  "VERSION=0 AND CHID=".$selch->selected;
  
  if (value("action") == "up") {
  	$article = value("article", "NUMERIC");
  	moveRowUp("channel_articles", "ARTICLE_ID", $article, "POSITION", $rowOrderFilter);
  } else if (value("action") == "down") {
  	$article = value("article", "NUMERIC");
  	moveRowDown("channel_articles", "ARTICLE_ID", $article, "POSITION", $rowOrderFilter);
  }
  
  $filtermenu = new StdMenu("");
  require_once $c["path"]."modules/channels/menu.inc.php";
  
  $oid = value("oid", "NUMERIC");
  if ($selch->selected != "0"  && $selch->selected != "-1" && $selch->selected != 0) {
    sortTableRows("channel_articles", "ARTICLE_ID", "POSITION", $rowOrderFilter);
  	$form = new ArticleSelectForm($selch->selected);
  	$form->newAction = "modules/channels/edit.php?sid=$sid&action=createarticle";
  	$form->buttonbar->setVariationSelector(getChannelVariations($selch->selected), variation());
  	$form->editAction = $c["docroot"]."modules/channels/edit.php";
  	$form->addFilterRule($lang->get("name"), "ca.TITLE");
    $form->addFilterRule($lang->get("category"), "cc.NAME", "LEFT JOIN channel_categories cc ON cc.CH_CAT_ID = ca.CH_CAT_ID");
  	$form->addFilterRule($lang->get("Author"), "cv.LAST_USER");
  	// preparation for adding a "grab from Multipage"-Button
  	$grabAction = "modules/channels/wz_import.php?sid=$sid";
  	$form->buttonbar->add("new", $lang->get("channel_importarticles", "Import articles"), "button", "document.location.href='".$c["docroot"].$grabAction."';", "navelement");
 	$page->add($form);
  }

 // SELECT `channel_articles`.* FROM channel_categories, channel_articles WHERE ((`channel_categories`.`NAME` = 'Erfolge') AND (`channel_articles`.`CH_CAT_ID` = channel_categories.CH_CAT_ID))

  
  $page->addMenu($selch);
  $page->addMenu($filtermenu);
  $page->draw();
?>