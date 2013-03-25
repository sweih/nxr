<?
  require_once "../../config.inc.php";
  $auth = new auth("CUSTOMERCARE|CUSTOMERCAREADMIN");
  $page = new Page("Email Ticketing");
  
  $menu = new StdMenu("Customer Care");
  include "menudef.inc.php";

  require_once("tickets.inc.php");
  GetEmails();
  $a = value("a", "NOSPACES", "");
  $id= value("id", "NUMERIC", "0");
  $close   = value("close");
  $reopen  = value("reopen");
  $delete  = value("delete");
  $t = value("t");
  if (isset($_POST["a"]))
    $a = $_POST["a"];
  
  if (isset($_POST["id"]))
    $id = $_POST["id"];
      
		$rep["username"] = $auth->userName;
				
		switch ($a) {
			case "vticket":
				ViewTicket($id);
				break;
			case "manage":
				if ($delete != "0") {
					if (isset($t)) {
						foreach ($t as $id => $val) {
							mysql_query("delete from tickets_answers where ticket = ".$id.";", $link);
							mysql_query("delete from tickets_messages where ticket = ".$id.";", $link);
							mysql_query("delete from tickets where ID = ".$id.";", $link);
						}
					}
				} else if ($close != "0") {
					if (isset($t)) {
						foreach ($t as $id => $val) {
							if ($id != "") CloseTicket($id);
						}
					}
				} else if ($reopen != "0") {
					if (isset($t)) {
						foreach ($t as $id => $val) {
							if ($id != "") OpenTicket($id);
						}
					}
				}						
				ShowMain();
				break;
			case "panswer":				
				//$reps_res = mysql_query("select ID from reps where username = '".$rep["username"]."';");
				//$reps_row = mysql_fetch_array($reps_res);
				$msg_res = mysql_query("select ticket from tickets_messages where ID = $id");
				$msg_row = mysql_fetch_array($msg_res);
				$message = value("message");
				if (PostAnswer($message, $auth->userId, $id))
					ViewTicket($msg_row["ticket"]);
				else
					$out.=  "Could not post your message";
				break;
			default:
				ShowMain();
				break;
		}
	 



function ViewTicket($id) {
	global $sid, $c, $c, $out;
	$divCounter=1;
	$firstId = 0;
	$hide=" style='display:none;'";
	$ticket_res = mysql_query("select * from tickets where ID = $id;");
	$ticket_row = mysql_fetch_array($ticket_res);
	$cat_res = mysql_query("select * from tickets_categories where id = ".$ticket_row["cat"].";");
	$cat_row = mysql_fetch_array($cat_res);
	if ($ticket_row["priority"] == 1)
		$pri = '<font color="green">Low</font>';
	else if ($ticket_row["priority"] == 2)
		$pri = '<font color="yellow">Normal</font>';
	else if ($ticket_row["priority"] == 3)
		$pri = '<font color="Red">High</font>';

	$out.= '
	<script language="JavaScript">
		function toggle(myid) {
			obj = document.getElementById(myid);
			d = obj.style.display;
			if (d=="none") {
				 d="block";
				
			}
			else if (d=="" || d=="block") d="none";
			obj.style.display =d;
	
		}
	</script>
	<table align="center" cellspacing="0" cellpadding="0" width="600" border=0>
	<tr><td colspan="2">'.getFormHeadline("Ticket Information").'</td></tr>
	<tr>
		<td width="140" class="standard"><b>Ticket ID:</b></tD>
		<td class="standardlight">'.$ticket_row["ID"].'</td>
	</tr>
	<tr>
		<td  class="standard"><b>Status:</b></tD>
		<Td class="standardlight">'.$ticket_row["status"].'</td>
	</tr>
	<tr>
		<td  class="standard"><b>Subject:</b></tD>
		<Td class="standardlight">'.stripslashes($ticket_row["subject"]).'</td>
	</tr>
	<tr>
		<td  class="standard"><b>Category:</b></tD>
		<Td class="standardlight">'. $cat_row["name"].'</td>
	</tr>
	<tr>
		<td  class="standard"><b>Name:</b></tD>
		<Td class="standardlight">'. htmlspecialchars($ticket_row["name"]).'</td>
	</tr>
	<tr>
		<td class="standard"><b>Email:</b></tD>
		<Td class="standardlight">'.htmlspecialchars($ticket_row["email"]).'</td>
	</tr>
	<tr>
		<td  class="standard"><b>Phone:</b></tD>
		<Td class="standardlight">'.$ticket_row["phone"].'</td>
	</tr>
	<tr><td colspan="2">'.getFormFooterline().'</td></tr>	
	</table>
	<br>';
	
	
	$msg_res = mysql_query("select * from tickets_messages where ticket = ".$ticket_row["ID"]." order by timestamp DESC, ID DESC;");
	while ($msg_row = mysql_fetch_array($msg_res)) {
		/* Zuerst werden die Antworten angezeigt, die wir bereits gesendet haben */
		$answers_res = mysql_query("select * from tickets_answers where reference = ".$msg_row["ID"]." order by timestamp DESC, ID DESC;");
		while ($answer_row = mysql_fetch_array($answers_res)) {
			$out.='<table align="center" cellspacing="0" cellpadding="0" width="600" border=0 class="headbox2">
			<tr>
				<td class="formtitle"><b>Reply: '.date("l, F j Y \a\t H:i", $answer_row["timestamp"]).'</b></td>
				<td class="formtitle" width="50"><a href="javascript:toggle(\'mes'.$divCounter.'\');" class="box">toggle</a></td>
			</tr>
			</table><div>			
			<table id="mes'.$divCounter; 
			$divCounter++;
			$out.='" align="center" cellspacing="0" cellpadding="0" width="600" border=0';
			if ($divCounter>3) $out.=$hide;
			$out.='>
			<tr>
			<td class="informationheader">';				 
				$buffer = nl2br($answer_row["message"]);
				$buffer = str_replace("\'", "'", $buffer);
				$buffer = str_replace('\"', '"', $buffer);
				$out.=  ($buffer); 
				
			$out.='</td>
		</tr>
		</table></div>';
		}
		/* Ende der Antwort-Anzeige */

		/* Jetzt wird die Anfrage angezeigt, auf die wir evtl. antworten sollten (wenn nicht bereits geschehen) */
		$out.='
		<table align="center" cellspacing="0" cellpadding="0" width="600" border=0  class="headbox2">
		<tr>
			<td  width="*"><b>Request: '.date("l, F j Y \a\t H:i", $msg_row["timestamp"]).'</b></td>
			<td  width="50"><a href="javascript:toggle(\'mes'. $divCounter.'\');" class="box">toggle</a></td>
		</tr>
		</table>

			
		<div><table id="mes'.$divCounter; 
		$divCounter++;
		$out.='" align="center" bgcolor="silver" cellspacing="0" cellpadding="0" width="600" border=0 ';
		if ($divCounter>3) $out.= $hide; 
		$out.='><tr>
			<td class="standardlight">';
				
				$buffer = nl2br($msg_row["message"]);
				$buffer = str_replace("\'", "'", $buffer);
				$buffer = str_replace('\"', '"', $buffer);
				$out.=  ($buffer); 
				if ($firstId==0) $firstId = $msg_row["ID"];
				$out.='
			</td>
		</tr>
		</table></div>';
		/* Ende Anzeige der Anfrage */		
	}
	
	
	$out.='<br><br>';
	$out.='<table align="center"  cellspacing="0" cellpadding="0" width="600" border=0>
	<tr>
		<td class="headbox">Post Reply</td>
	</tr>
	<tR bgcolor="standard">
		<td align="center" class="standardlight"><br>
		<font color="white">
		<form  name="replies" method="POST">
		<script language="Javascript">
			function addSnip() {
				snip = document.replies.block.options[document.replies.block.selectedIndex].name;
				content = eval("document.replies.tb" + String(snip) +".value");
				message = document.replies.message.value + content;
				document.replies.message.value = message;
			}
		</script>';
		
		$blocks = null;
		global $db;
		$sql = "SELECT NAME, CONTENT FROM tickets_textblocks ORDER BY NAME";
		$query = new query($db, $sql);
		$i=0;
		while ($query->getrow()) {
		   $blocks[$i]["name"] = $query->field("NAME");
		   $blocks[$i]["content"] = $query->field("CONTENT");
		   $i++;
		}
		$query->free();
		for ($i=0; $i<count($blocks); $i++) {
		  $out.= "<input type=\"hidden\" name=\"tb".$i."\" value=\"".$blocks[$i]["content"]."\">";
		}
		$out.= "<select name=\"block\" size=\"1\">";
		for ($i=0; $i<count($blocks);$i++) {
			$out.= "<option name=\"".$i."\">".$blocks[$i]["name"]."</option>";
		}
		$out.= "</select>&nbsp;&nbsp;";
		$out.= "<input type=\"button\" name=\"addtext\" value=\"Add Sniplet\" onClick=\"addSnip();\">";
		$out.='		
		<input type="hidden" name="a" value="panswer">
		<input type="hidden" name="id" value="'. $firstId.'">
		<input type="hidden" name="sid" value="'.$sid.'">
		<textarea name="message" rows="10" wrap="soft" style="width:500px;"></textarea><br><br>
		<input type="submit" value="Post">
		<input type="reset" value="Reset From">
		</form>
		</td>
	</tr>
	</table>';	
}

function ShowMain() {
	global $link, $pri_value, $sid, $out;
	$status = value("status");
	if ($status=="closed") {
		$headline = "Closed Requests";		
	} else {
		$headline = "Open Requests";
		$status = "open";
	}

	$sql = "select * from tickets where status = '$status' order by INSERTTIMESTAMP DESC";

	$tickets_res = mysql_query($sql, $link);
	$out.='
	<script language="JavaScript">
		function chkTicketDelete() {
			return confirm("Nur SPAM-Mails sollten gelöscht werden.\nAbgeschlossene Anfragen sollten mittels \'Close\' geschlossen werden.\n\nNachricht wirklich löschen ?");
		}
	</script>
	
	<form name="ticket" action="index.php" method="POST">
	<table align="center" bgcolor="silver" cellspacing="0" cellpadding="0" width="600" border=0>
		<tr bgcolor="black">
			<td>'.getFormHeadline($headline).'</td>
		</tr>
	</table>
	<br>
	<table width="600" border="0" cellspacing=0 cellpadding=0 align="centeR">
	<tr>
		<td class="gridtitle">&nbsp;</td>
		<td class="gridtitle">Subject</td>
		<td class="gridtitle">From</td>
		<td class="gridtitle">Category</td>
		<td class="gridtitle">Unanswered Msgs</td>
	</tr>';
	
	$tick_count = 0;
	if ($tickets_res) {
	  while ($tickets_row = mysql_fetch_array($tickets_res)) {
		  $tickets[$tickets_row["ID"]] = new Ticket($tickets_row);
		  $tick_count++;
	  }
	}
	
	
	$count = 0;
	while ($count < $tick_count) {
		$highestscore = 0;
		foreach($tickets as $ticket) {
			if (((isset($lastscore)) ? ($ticket->score <= $lastscore) : (1 == 1)) and ($ticket->score >= $highestscore) and ($ticket->printed == false)) {
				$highestscore = $ticket->score;
				$next = $ticket->id;
			}
		}
		$lastscore = $highestscore;
		
		$out.=  '<tr class="standardlight">';
		$out.=  '<td><input type="checkbox" name="t['.$tickets[$next]->id.']"></td>';
		$out.=  '<td><a href="index.php?a=vticket&id='.$tickets[$next]->id.'&sid='.$sid.'">'.$tickets[$next]->subject.'</a></td>';
		$out.=  '<td>'.$tickets[$next]->name.'</td>';
		$cat_res = mysql_query("select * from tickets_categories where id = ".$tickets[$next]->cat.";", $link);
		$cat_row = mysql_fetch_array($cat_res);
		$out.=  '<td>'.$cat_row["name"].'</td>';
		$out.=  '<td>'.$tickets[$next]->unanswered.'</tD>';
		$out.=  '</tr>';

		$tickets[$next]->printed = true;
		$count++;
	}
	$out.=  '<tr><td class="informationheader" colspan="5">';
	$out.=  '<table width="100%" border="0">';
	$out.=  '<tr><td align="left">';
	$out.=  '<input type="hidden" name="status" value="'.$status.'">';
	$out.=  '<input type="hidden" name="a" value="manage">';
	$out.=  '<input type="hidden" name="sid" value="'.$sid.'">';
	if ($status=='open') $out.=  '<input type="submit" name="close" value="Close Selected">';
	if ($status=='closed') $out.=  '<input type="submit" name="reopen" value="Reopen Selected">';
	$out.=  '<input type="submit" name="refresh" value="Refresh Page">';
	$out.=  '</td><td align="right"><br>';
	$out.=  '<input type="submit" name="delete" value="Delete Selected" onClick="return chkTicketDelete();">';
	$out.=  '</td></tr></table>';
	$out.=  '</td></tr>';
	$out.=  '<tr><td colspan="5">'.getFormFooterline().'</td></tr>';
	$out.=  "</table>";
	$out.=  '</form>';	
}

$container = new ExtContentContainer("Ticketing");
$container->extcontent = $out;
$page->addMenu($menu);
$page->add($container);
$page->draw();

$db->close();
?>


