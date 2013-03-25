<?PHP
  require_once "nxheader.inc.php";
  include $cds->path."inc/header.php";

  // Start of individual template
  echo '<h1>'.$cds->content->get("Headline").'</h1>';
  br();
  
  // Build the sitemap:
  
  //Check, if a commented sitemap should be used
  $commented = ($cds->content->get("Commented Sitemap")==1);
  
  // Determine the Startnode
  $node = $cds->menu->getMenuByPath("/");
  
  // Get all the root-Nodes
  $startNodes = $node->sameLevel();
  echo '<div style="margin:0px 0px 0px 20px;"><ul>';
  for ($i=0; $i<count($startNodes); $i++) {
    drawNode($startNodes[$i], $commented);
  }
  echo '</ul></div>';
 
  /**
   * Draw the ChildNOdes of the MenuNode which is passed and the menunode itself..
   * Calls drawNode for alle the ChildNOdes then.
   *
   * @param MenuObject $node
   * @param boolean		 $commented
   */  
  function drawNode($node, $commented) {
  	global $cds;

		echo "<li>";
		//Draw the link
		echo $cds->layout->menu->getLink($node);
		if ($commented) {
			$desc = $node->getDescription();
			if (strlen($desc) > 0) {
			  br();
			  echo $desc;
			}
		}  		
		// query for childnodes
		if ($node->hasLowerLevel()) {
		  echo '<ul>';
		  $childs = $node->lowerLevel();
		  for ($i=0; $i<count($childs); $i++)  			  
		    drawNode($childs[$i], $commented);			
		  echo '</ul>';
			}
		echo "</li>"; 	
  }  
    
  include $cds->path."inc/footer.php";
  require_once "nxfooter.inc.php";
?>