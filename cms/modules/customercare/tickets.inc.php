<?
/* phpTickets v0.2.1 by Cedric Veilleux */
require("config.inc.php");

$link = mysql_connect($db_host, $db_user, $db_pass);
mysql_select_db($db_name);

class Ticket {
	var $id;
	var $subject;
	var $name;
	var $email;
	var $phone;
	var $status;
	var $rep;
	var $cat;
	var $priority;
	var $in;
	var $out;
	var $unanswered;
	var $score;
	var $printed;
	var $age;
	var $lastanswer; // timestamp of last reply to this ticket added by FK

	function Ticket ($row) {
		global $pri_value;

		$this->id = $row["ID"];
		$this->subject = $row["subject"];
		$this->name = $row["name"];
		$this->email = $row["email"];
		$this->phone = $row["phone"];
		$this->status = $row["status"];
		$this->rep = $row["rep"];
		$this->cat = $row["cat"];
		$this->priority = $row["priority"];
		$this->in = 0;
		$this->out = 0;
		$this->printed = false;
		$this->lastanswer = 0; // to be updated in the next lines.

		$messages_res = mysql_query("select * from tickets_messages where ticket = ".$this->id." order by timestamp ASC;");
		/* added by FK (lastanswer) */
		$tanswers_res = mysql_query("select * from tickets_answers where ticket = ".$this->id." order by timestamp ASC;");

		while ($tanswers_row = mysql_fetch_array($tanswers_res)) {
			if ($tanswers_row["timestamp"] > $this->lastanswer) 
				$this->lastanswer = $tanswers_row["timestamp"];
		}
		/* End of added by FK (lastanswer) */

		while ($messages_row = mysql_fetch_array($messages_res)) {
			$this->in = 0;
			$answer_res = mysql_query("select * from tickets_answers where reference = ".$messages_row["ID"].";");
			$this->out = $this->out + mysql_num_rows($answer_res);
			if (mysql_num_rows($answer_res) == 0) {
				if (!isset($this->age))
					$this->age = time() - $messages_row["timestamp"];
				/* added by FK (lastanswer) */
				if ($this->lastanswer < $messages_row["timestamp"])
				/* End of added by FK (lastanswer) */
					$this->unanswered++;
			}
		}


		//The score determines the priority in the list. High score = first. Could need tweaking here...
		if ($this->unanswered == "") {
			$this->unanswered = 0;
			$this->score = $this->priority;
		} else {
			$this->score = (($this->priority * $pri_value) + $this->age);
		}
	}
}

function GetEmails() {
	global $trouble_email;

	/* Find out which pop accounts (categories) we have to check */
	$cat_res = mysql_query("select * from tickets_categories;");

	/* Check each pop accounts / categories */
	if (!!$cat_res) {	
		while ($cat_row = mysql_fetch_array($cat_res)) {
			$mbox = imap_open("{".$cat_row["pophost"].":110/pop3/notls}INBOX",$cat_row["popuser"],$cat_row["poppass"])
			or die("can't connect: ".imap_last_error());

			$curmsg = 1;
			while ($curmsg <= imap_num_msg($mbox)) {
				//print "Retrieving new message.<br>";
				$body = get_part($mbox, $curmsg, "TEXT/PLAIN");
				if ($body == "")
				$body = get_part($mbox, $curmsg, "TEXT/HTML");
				// if ($body == "") { /* We can't do anything with this email, leave it there */
				//	print "Error: Message could not be parsed. I left it in the mailbox.<br>";
				//	continue;
				// }
				$head = imap_headerinfo($mbox, $curmsg, 800, 800);
				// TODO:  Name and Email address should be properly parsed here.
				$email = $head->reply_toaddress;
				$name = $head->fromaddress;
				$subject = $head->fetchsubject;
				//print "Subject: $subject<br>";

				/* Check the subject for ticket number */
				if (!ereg ("[[][#][0-9]{6}[]]", $head->fetchsubject)) {
					/* Seems like a new ticket, create it first */
					//print "Creating a new ticket.<br>";
					$ticket_id = CreateTicket($subject, $name, $email, $cat_row["id"], "");
					if ($ticket_id == false) {
						/* We had troubles creating the ticket. Forward the problematic email to a real human  */
						print "Warning: CreateTicket failed! Message forwarded to $trouble_email <br>";
						@mail ($trouble_email, "{TROUBLE} $subject", "[ERROR: CreateTicket failed]\n".$body, "From: $name\nReply-To: $email");
						@imap_delete($mbox, $curmsg);
						$curmsg++;
						continue;
					}
				} else {
					/* Seems like a followup of an existing ticket, extract ticket_id from subject */
					$ticket_id = substr(strstr($head->fetchsubject, "[#"), 2, 6);
					//print "Follow up to ticket #$ticket_id<br>";
					SendNotification($name, $email, "", $subject, $cat_row["id"]);
				}
				$body = ereg_replace("\n\n", "\n", $body);
				if (!PostMessage($ticket_id, $body)) {
					/* Could not post the ticket, forward the problematic email to a real human */
					print "Warning: PostMessage failed! Message forwarded to $trouble_email <bR>";
					mail ($trouble_email, "{TROUBLE} $subject", "[ERROR: PostMessage failed]\n".$body, "From: $name\nReply-To: $email");
				}

				imap_delete($mbox, $curmsg);
				$curmsg++;
			}

			imap_expunge($mbox);
			imap_close($mbox);
		}
	}
}


/* Nice code from cleong@organic.com */
function get_mime_type(&$structure) { 
	$primary_mime_type = array("TEXT", "MULTIPART", "MESSAGE", "APPLICATION", "AUDIO", "IMAGE", "VIDEO", "OTHER"); 
	
	if($structure->subtype) { 
		return $primary_mime_type[(int) $structure->type] . '/' . $structure->subtype; 
	} 
	return "TEXT/PLAIN"; 
} 

function get_part($stream, $msg_number, $mime_type, $structure = false, $part_number = false) { 
	if(!$structure) { 
		$structure = imap_fetchstructure($stream, $msg_number); 
	} 
	if($structure) { 
		if($mime_type == get_mime_type($structure)) { 
			if(!$part_number) { 
				$part_number = "1"; 
			} 
			$text = imap_fetchbody($stream, $msg_number, $part_number); 
			if($structure->encoding == 3) { 
				return imap_base64($text); 
			} else if($structure->encoding == 4) { 
				return imap_qprint($text); 
			} else { 
				return $text; 
			} 
		} 
		if($structure->type == 1) /* multipart */ { 
			while(list($index, $sub_structure) = each($structure->parts)) { 
				if($part_number) { 
					$prefix = $part_number . '.'; 
				} 
				$data = get_part($stream, $msg_number, $mime_type, $sub_structure, $prefix . ($index + 1)); 
				if($data) { 
					return $data; 
				} 
			} 
		} 
	} 
	return false; 
} 

function CreateTicket($subject, $name, $email, $cat, $phone, $pri=2) {
	global $link, $db_name, $notify;

	if ($subject == "")
		$subject = "[No Subject]";

	/* Generate random ticket_id */
	do {
		mt_srand ((double) microtime() * 1000000);
		$ID =  mt_rand(0,9) . mt_rand(0,9) . mt_rand(0,9) . mt_rand(0,9) . mt_rand(0,9) . mt_rand(0,9);
	} while(ValidID($ID) == false);

	/* Insert the ticket */
	mysql_db_query($db_name, "insert into tickets (subject, name, email, cat, phone, status, ID, priority) VALUES ('".addslashes($subject)."', '$name', '$email', '$cat', '$phone', 'open', $ID, $pri);", $link);

	if (mysql_error($link))
		return false;

	/* Succeeded */

	SendNotification($name, $email, $phone, $subject, $cat);

	return $ID;
}

function PostMessage($ticket, $message) {
	global $link, $db_name;

	$now = time();
	
	$res = mysql_db_query($db_name, "select * from tickets where ID = $ticket;", $link);
	if (mysql_error($link)) {
		print mysql_error($link) . "<br>";
		return false;
	}

	if (mysql_num_rows($res) == 0) {
		print "unknown ticket!<br>";
		return false;
	}
	
	/* Make sure the ticket is open */
	mysql_db_query($db_name, "update tickets set status = 'open' where ID = $ticket;", $link);

	mysql_db_query($db_name, "insert into tickets_messages (ticket, message, timestamp) VALUES($ticket, '".addslashes($message)."', $now);", $link);
	if (mysql_error($link)) {
		print mysql_error($link) . "<br>";
		return false;
	}

	return mysql_insert_id($link);
}

function PostAnswer($message, $rep, $reference) {
	global $link, $message_footer, $organization;

	$now = time();
	$msg_res = mysql_query("select ticket from tickets_messages where ID = $reference;");
	$msg_row = mysql_fetch_array($msg_res);
	$ticket = $msg_row["ticket"];

	$res = mysql_query("select * from tickets where ID = $ticket;");
	echo "1";
	if (mysql_error($link)) {
		return false;
	}
	if (mysql_num_rows($res) == 0) {
		return false;
	}
	mysql_query("insert into tickets_answers (ticket, message, timestamp, rep, reference) VALUES($ticket, '$message', $now, $rep, $reference);");
	if (mysql_error($link)) {
		return false;
	}

	$ticket_row = mysql_fetch_array($res);
	$cat_res = mysql_query("select * from tickets_categories where id = ".$ticket_row["cat"].";");
	$cat_row = mysql_fetch_array($cat_res);
	//$rep_res = mysql_query("select * from reps where id = $rep;");
	//$rep_row = mysql_fetch_array($rep_res);

	$message = str_replace("\r", "\n", $message);
	$message = str_replace("\n\n", "\n", $message);
	mail ($ticket_row["email"], "[#".$ticket_row["ID"]."] ".$ticket_row["subject"], stripslashes($message)."\n\n--\n".$auth->userName."\n".$organization."\n\n".$message_footer."\n", "From: ".$cat_row["replyto"]);

	return mysql_insert_id($link);
}

function CloseTicket($ticket) {
	global $link;

	mysql_query("update tickets set status = 'closed' where ID = $ticket;", $link);

	return;	
}


function OpenTicket($ticket) {
	global $link;

	mysql_query("update tickets set status = 'open' where ID = $ticket;", $link);

	return;	
}

function ValidID($ID) {
	global $link, $db_name;
	$res = mysql_db_query($db_name, "SELECT ID FROM tickets where ID = $ID;", $link)
		or die (mysql_error($link));

	if (mysql_num_rows($res) != 0)
		return false;

	return true;
}

/* Added by Fabian Koenig fkoenig@fkhosting.de
 * Sending a notification-Mail to the corresponding official Address
 */
function SendNotification($name, $email, $phone, $subject, $cat) {
	 
		$notify_res = mysql_query("SELECT * FROM tickets_categories WHERE id = ".$cat.";");
		if ($notify_res) {
		  $notify = mysql_fetch_array($notify_res);
		} else {
			echo "Are you sure, the mailings category '$cat' is defined? I could not find it.<br><br>";			
		}
		
		$notification_subject = str_replace("[--subject--]", $subject, $notify["notify_subject"]);
		$notification_subject = str_replace("[--email--]", $email, $notification_subject);
		$notification_subject = str_replace("[--phone--]", $phone, $notification_subject);
		$notification_subject = str_replace("[--name--]", $name, $notification_subject);
	
		$notification_body = str_replace("[--subject--]", $subject, $notify["notify_body"]);
		$notification_body = str_replace("[--email--]", $email, $notification_body);
		$notification_body = str_replace("[--phone--]", $phone, $notification_body);
		$notification_body = str_replace("[--name--]", $name, $notification_body);
	 
		if ($notify["notify_to"] != "") {
			@mail($notify["notify_to"], $notification_subject, $notification_body, "From: ".$notify["notify_from"]."\r\nReply-To: ".$notify["notify_replyto"]."\r\n".$notify["notify_headers"]); 
		}
	

}
// End of Added by Fabian Koenig

?>
