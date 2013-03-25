<?php

  require_once "../../config.inc.php";
  $auth = new auth("CHANNEL_ADMIN");
  $page = new Page("Channel Administration");

  $filter = new Filter("channels", "CHID");
  $filter->addRule($lang->get("name"), "NAME", "NAME");
  $filter->type_name = $lang->get("channels", "Channels");
  $filter->icon = "li_channel.gif";
  
  $filtermenu = new Filtermenu("", $filter);
  $filtermenu->tipp = $lang->get("help_channel", "Topic categories defining the contents of articles created by N/X. i.e. News, Persons ");

  require_once $c["path"]."modules/channels/menu.inc.php";
 
  if ((strtoupper(value("deletion")) ==	"DELETE") && (strtoupper(value("commit")) == strtoupper($lang->get("YES")))) {
  	deleteChannel(value("oid", "NUMERIC"));
  }

  if ($page_action == "INSERT") $oid=0;
  
    if ($oid == 0) {
        $addtext = "";
    } else {
        $addtext = ": " . getDBCell("channels", "NAME", "CHID = " . $oid);
    }

    $form = new stdEDForm($lang->get("channel", "Channel"). $addtext);      
    $cond = $form->setExPK("channels", "CHID");

    // Add a Toolbar for showing the launch button
    if ($auth->checkAccessToFunction("CHANNEL_LAUNCH")) {
    	$form->buttonbar->add("action", $lang->get("launch"));    	
    	
    	$form->add(new Hidden("action", ""));
    	if (value("action") == $lang->get("launch")) {    		
    		launchChannel($oid);
    		if ($errors == "") {
    			$form->addToTopText($lang->get("chnlaunched", "The channel was launched successfully."));	
    			$form->topicon = "ii_success.gif";
    		} else {
    			$form->addToTopText($lang->get("chnlaunchederr", "An error occured while launching the channel."));	
    			$form->topicon = "ii_error.gif";   			
    		}
    		$page_action="UPDATE";    	
    		$go = "update";   
    		$processing = "no";
    		$page_state = "start";
    	}
    	
    }

    
    $form->add(new TextInput($lang->get("name"), "channels", "NAME", $cond, "type:text,width:200,size:64", "MANDATORY&UNIQUE"));  	    
    if ($page_action != "INSERT") {
    	$updateHandler = new ActionHandler("UPDATE");
		$updateHandler->addFNCAction("syncSPMVariations");
    	$form->registerActionHandler($updateHandler);
		$members = createNameValueArrayEx("cluster_templates", "NAME", "CLT_ID", "CLT_TYPE_ID=1 AND DELETED=0 AND VERSION=0");
		$form->add(new SelectMultiple2Input($lang->get("spm_variations"), "sitepage_variations", "VARIATION_ID", "SPM_ID=".$oid, "variations", "NAME", "VARIATION_ID", "DELETED=0"));
    	$form->add(new SelectMultipleInputPos($lang->get("clt_select", "Select cluster templates"), $members, "channel_cluster_templates", "CLT_ID", "CHID", $cond, "cluster_templates", "CLT_ID", "NAME", "standardlight", 2));    	
      }

  $page->addMenu($filtermenu);
  $page->add($form);
  $page->drawAndForward("modules/channels/channels.php?sid=$sid&oid=<oid>&go=update");
  
  echo $errors;
?>