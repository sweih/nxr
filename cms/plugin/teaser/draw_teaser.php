<?php
/**
 * This file defines the layout of the teasers given by the teaser plugin
 * Please edit this file and copy it to www/sys to meet your needs.
 * Whenever a teaser is drawn, the system calls the function drawPGNTeaser($id).
 * the function returns the html-code of the teaser.
 */


  /**
   * Draw a teaser defined with the teaser plugin
   * @id internal id of the teaser. matched pgn_teaser.fkid.
   */
  function drawPGNTeaser($id) {
    global $cds, $db;
    $result = '';
    $query = new query($db, "Select * FROM pgn_teaser Where FKID=".$id);
    if ($query->getrow()) {
      $href = $query->field("HREF");
      if (substr($href,0,4) == "www.") $href = 'http://' . $href;
      $popup = ($href != "");
      $spid = $query->field('SPID');
      if (($spid != "0") && $href=="") {
        $menu = new Menu(null, $spid, $cds->variation, $cds->level);
        $href = $menu->getLink();    	
      }
      $imageid = $query->field("IMAGEID");      	
    
      $aTag = '<a href="'.$href.'"';
      if ($popup) $aTag.=' target="_blank"';
      $aTag.= '>';
      // image teaser
      if ($query->field("ISIMAGETEASER") == "1") {      	
      	$result = $aTag.$cds->content->getById($imageid).'</a>';      	
      } else {
        // usual teaser
        $headline = $query->field("HEADLINE");
        $body = $query->field("BODY");
        $linktext = $query->field("LINKTEXT");
        if ($linktext == "") $linktext = "read more";
        $result =  '<div class="teaser">';
        if ($headline != "") $result.=  '<b>'.$headline.'</b><br>';        
        if ($imageid != "0") $result.= $aTag.$cds->content->getById($imageid).'</a><br>';      	
        if ($body !="") $result.= $body;
        if (($query->field("RESOLVECHILDS") != "1") || (($spid == "0") && ($href==""))) {
        	$result.= '&nbsp;&nbsp;'.$aTag.$linktext.'</a>';
        } else {
          $childs = $menu->lowerLevel();
          for ($i=0; $i<count($childs); $i++) {
          	$result.='<br/>';
          $result.=$childs[$i]->getTag();
          }    	        	        	
        }          
        $result.= '</div>';      
      }
    }
    return $result; 
  }
?>