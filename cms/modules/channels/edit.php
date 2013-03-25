<?php
  require_once "../../config.inc.php";

  $auth = new auth("CHANNEL_EDIT");
  $aclf = $auth; // no acls present yet.
  $page = new Page("Edit Channels");
  require_once $c["path"]."modules/channels/article_select_form.php";
  $oid = value("oid", "NUMERIC");
  $clnid = $oid;
  $action = value("action");
  if (value("setch", "NUMERIC") != "0") {
  	pushVar("chsel", value("setch", "NUMERIC"));
  }
  $chid = getVar("chsel");
  $variation = variation();
 
  
  if (value("laction") == $lang->get("ar_launch") && $auth->checkAccessToFunction("CHANNEL_LAUNCH")) {
     launchArticle($oid, 10, $variation);
  } else if (value("laction") == $lang->get("ar_expire") && $auth->checkAccessToFunction("CHANNEL_LAUNCH")) {
     expireArticle($oid, 10, $variation);
  }

  if ($action == "createarticle") {
  	$go = "CREATE";
  	$page_action = "INSERT";
  	$form = new stdEDForm($lang->get("create_article", "Create new Article in channel")." ".getDBCell("channels", "NAME", "CHID = ".$chid),"");
  	$cond = $form->setPK("channel_articles", "ARTICLE_ID");
  	$oname = new TextInput($lang->get("name"), "channel_articles", "TITLE", $cond, "type:text,width:300,size:64", "MANDATORY");
	$form->add($oname);
    	$form->add(new SelectOneInput($lang->get("template"), "channel_articles", "CLT_ID", "cluster_templates, channel_cluster_templates", "NAME", "channel_cluster_templates.CLT_ID AS CLT", "cluster_templates.CLT_ID = channel_cluster_templates.CLT_ID AND channel_cluster_templates.CHID = $chid ORDER BY POSITION ASC", $cond, "TYPE:DROPDOWN", "MANDATORY"));
	$form->add(new SelectOneInput($lang->get("category"), "channel_articles", "CH_CAT_ID", "channel_categories", "NAME", "CH_CAT_ID", "CHID = $chid", $cond, "type:dropdown", "MANDATORY"));
  	$form->add(new PositionInput($lang->get("position", "Position"), "channel_articles", "POSITION", $cond, "CHID=$chid AND VERSION=0", "size:4,width:50"));
	$form->add(new Hidden("action", "createarticle"));
	$form->add(new NonDisplayedValueOnInsert("channel_articles", "CHID", $cond, $chid, "NUMBER"));
	$form->add(new NonDisplayedValueOnInsert("channel_articles", "ARTICLE_DATE", $cond, "NOW()", "NUMBER"));
	$form->forbidDelete(true);

  	$handler = new ActionHandler("INSERT");
	$handler->addFncAction("createClusterNodeForArticle");
	$form->registerActionHandler($handler);

  	$page->add($form); 	
  	$page->drawAndForward("modules/channels/edit.php?sid=$sid&oid=<oid>&go=update");
  } else {
  	// Flag for configuration of cluster-panel...
    $clt = getDBCell("channel_articles", "CLT_ID", "ARTICLE_ID = $oid");
    $isArticle = true;
  	$page_action = "UPDATE";

  	$clid = syncArticleVariation($oid, $variation);
  	syncCluster($clid);
  	$view = initValue("view", doc()."view", 1);
  	$title = getDBCell("channel_articles", "TITLE", "ARTICLE_ID = $clnid");
  	
  	$form = new PanelForm($lang->get("edit_article", "Edit Article")." ".$title, "", 'articlesform');
  	$form->quickpanel = true;
  	$form->backto = $c["docroot"] . "modules/channels/overview.php?sid=$sid";
	if ($auth->checkAccessToFunction("CHANNEL_EDIT")) {
		$clusterPanel = new Panel($lang->get("ed_content"));
		require_once $c["path"] . "modules/common/panel_cluster.inc.php";		
		$metaPanel = new Panel($lang->get("ed_meta"));
		require_once $c["path"] . "modules/common/panel_meta.inc.php";
		$propPanel = new Panel($lang->get("properties", "Properties"));
		require_once $c["path"] . "modules/channels/panel_properties.inc.php";
	  	$form->addPanel($clusterPanel);
	  	$form->addPanel($metaPanel);
	  	$form->addPanel($propPanel);
	}  	
  	$page->add($form);
  	$page->draw();
  	
  }
  
  echo $errors;
?>