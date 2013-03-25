<?php

  require_once "../../config.inc.php";  
  $auth = new auth("BULKIMAGE");
  $page = new Page("Bulkimage importer");
  require_once $c["path"]."api/userinterface/wizard/stcheckarchive.php";
  require_once $c["path"]."api/userinterface/wizard/stimportimages.php";
  
  
  $wizard = new Wizard($lang->get("imp_impages", "Import Images"));
  $wizard->setTitleText($lang->get("wz_import_im_title", "This wizard is used for importing importing images to N/X. Pack the images into a zip archive. The wizard will then create the data."));
	
  if ($wizard->firstRun) {
    $_SESSION["archivefolder"] = "";	
  }
  
  ////// STEP 1 //////	
  $step1 = new Step();
  $step1->setTitle($lang->get("wzt_archive_file", "Select Archive"));
  $step1->setExplanation($lang->get("wze_archive_file", "Please select the zip-archive you want to import. The images must be into that archive in a flat structure, having no folders.")); 		
  $step1->add(new WZUploadArchive("upload"));	
  
  $step2 = new STCheckArchive();
  $step2->setTitle($lang->get("wzt_ach_check","Check Archive")); 
  $step2->setExplanation($lang->get("wze_arch_check", "Please control the result of the archive checks and press next if you want to resume."));

  $step3 = new Step();
  $step3->setTitle($lang->get("wzt_dest_folder","Select destination folder"));
  $step3->setExplanation($lang->get("wze_dest_folder", "Please select the folder, where all the new pictures will be copied to."));
  $folders = array();
  createFolders($folders, ' / ', 0);
  $step3->add(new WZSelect('folder', $lang->get("dest_folder", "Destination folder"), $folders));
  
  $step4 = new STImportImages();
  $step4->setTitle($lang->get("wzt_imp_imag", "Importing images..."));
  
  $wizard->add($step1);  
  $wizard->add($step2);
  $wizard->add($step3);
  $wizard->add($step4);
  
  $page->add($wizard);
  $page->draw();
  $db->close();
  echo $errors;
?>