<?
  require_once "../../cms/config.inc.php";
  // Rate Form of Plugin pgnRate
?>
<html>
<head>
  <title>Rate this content</title>
</head>
<body style="font-face:Arial; font-size:12pt;">
<?php

	   /**
	    * Process the formfields, if set. Takes fields pgnratingcomment<id>, pgnratingvote<id>,
	    * pgnratingsend<id>
	    */
	    function processForm($sourceId) {
	      if (value("pgnratingsend".$sourceId) == "1") {
	        return saveData(value("pgnrating".$sourceId, "NUMERIC"), value("pgnratingcomment".$sourceId, "", ""), $sourceId);
	      }
	      return false;
	    }

	   /**
	    * Store a vote
	    * @param integer Vote of user (1-8)
	    * @param string comment of user
	    */
	   function saveData($vote, $comment="", $sourceId) {
	     if ($vote>0 && $vote <10) {
	       global $db;
	       $sql = "INSERT INTO pgn_rating (VOTE, COMMENT, SOURCEID) VALUES($vote, '".addslashes($comment)."', $sourceId)";
	       $query = new query($db, $sql);
	       $query->free();
	       return true;  
	     }
	     return false;
	   }

  			  $sourceId = value("source", "NUMERIC", "");
  				$out.= '<table style="font-face:arial; font-size:12px;">'."<tr>";
	        $out.= '<form name="rate" method="post">';
	        if (processForm($sourceId)) {
	            $out.= '<td class="rate_copy">Thank your for your vote and opinion.</td></tr><tr>';	           
	        } else {
	            $out.= '<td colspan="11" class="rate_label">Is this article helpful?</td></tr><tr>';
	            $out.= '<td colspan="11" class="rate_copy"><img src="sys/ptrans.gif" border="0" height="5" width="1">'."</td></tr><tr>";
	            $out.= '<td class="rate_label" width="10%">&nbsp;<input type="hidden" name="pgnratingsend'.$sourceId.'" value="1"></td>';
	            for ($i=1; $i < 10; $i++) {
    	            $out.= '<td class="rate_copy" align="center" width="10%">'.$i.'</td>';
	            }    
	            $out.= '<td class="rate_copy" width="10%">&nbsp;</td>'."</tr><tr>";
	            $out.= '<td class="rate_label" width="10%">Poor</td>';
	            for ($i=1; $i < 10; $i++) {
	                $out.= '<td class="rate_copy" align="center" width="10%"><input type="radio" name="pgnrating'.$sourceId.'" value="'.$i.'"></td>';
	            }
	            $out.= '<td class="rate_copy" width="10%">Great</td>'."</tr><tr>";
	            $out.= '<td colspan="11" class="rate_copy"><img src="sys/ptrans.gif" border="0" height="5" width="1">'."</td></tr><tr>";
	            $out.= '<td colspan="11" class="rate_label">Your Comment (optional):</td></tr><tr>';
	            $out.= '<td colspan="11" class="rate_copy"><textarea class="rate_textarea" name="pgnratingcomment'.$sourceId.'" size="4"></textarea>'."</td></tr><tr>";
	            $out.= '<td colspan="11" class="rate_copy"><input type="submit" name="submit" value="Send">'."</td></tr>";
	        }
	        $out.= '<input type="hidden" name="source" value="'.$sourceId.'"/>';
	        $out.= '</form>';
	        $out.= '</table>';    	        
	      
echo $out;


?>  
</body>
</html>