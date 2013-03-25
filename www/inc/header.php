<?php
 
  // Add CSS and JS to the Header, Setup the Title of the homepage 
  $cds->layout->setStaticTitle( $cds->content->getByAccessKey("sitetitle") );
  $cds->layout->setStaticMetaKeywords( $cds->content->getByAccessKey("metakeywords") );
  $cds->layout->setStaticMetaDescription( $cds->content->getByAccessKey("metadescription") );
  
  // Check, if an rss-link is defined and set if.
 // $rss = $cds->content->getByAccessKey("rssfeed", "ALL");  
  //$rsslink = $rss["HREF"];
  //if (strlen($rsslink) > 0 ) {
  //	$cds->layout->addRSSFeed($cds->servername.$rsslink);
 // }
  
  // Draw the HTML Header with title tags, js, css.... 
  $cds->layout->htmlHeader(); 
  
  $cds->layout->drawMashups("HEAD1");
  // Draw the Menu. Some Menues consist of Header and Footer. The footer is drawn with
  // the call $cds->layout->drawMenuFooter() in footer.php.
  $cds->layout->drawMenuHeader();
    
  // Draw the table, which separates the content from the sidebar.
  $cds->layout->drawMashups("HEAD2");
?>