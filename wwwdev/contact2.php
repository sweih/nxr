<?PHP
// disable the dynamic cache
define("_disableCache", true);

require_once "nxheader.inc.php";
require_once $cds->path."inc/header.php";

echo '<h1>'.$cds->content->get("Headline").'</h1>';
br();

$captchaAPI = $cds->plugins->getAPI("Captcha");
$captchaValid = $captchaAPI->validate();

$trouble_email = $cds->content->get("Trouble Email");
$ticket_category = $cds->content->get("Category");

$fname = value("name", "", "");
$fsurname = value("surname", "", "");
$fstreet = value("street", "", "");
$fzip = value("zip", "", "");
$fcity = value("city", "", "");
$ftel = value("tel", "", "");
$ffax = value("fax", "", "");
$femail = value("email", "","");
$fsubject = value("subject", "", "");
$fmessage = value("message", "", "");
$fcheckcode=value("checkcode", "", "");
if (value("submit") != "0" && $femail != "" && $captchaValid) {
	$mailstring = $headline ."\n";
	$mailstring.= $body."\n";
	$mailstring.= "\n";
	$mailstring.= "Name:     ".$fname."\n";
	$mailstring.= "Vorname:  ".$fsurname."\n";
	$mailstring.= "Strasse:  ".$fstreet."\n";
	$mailstring.= "PLZ:      ".$fzip."\n";
	$mailstring.= "Ort:      ".$fcity."\n";
	$mailstring.= "Telefon:  ".$ftel."\n";
	$mailstring.= "Fax:      ".$ffax."\n";
	$mailstring.= "E-Mail:   ".$femail."\n\n";
	$mailstring.= $fmessage."\n";
	$mailstring.= "\n\n";
	

	include($c["path"]."modules/customercare/tickets.inc.php");

	$ticket = CreateTicket($fsubject, $fname.', '.$fsurname, $femail, $ticket_category, $fphone, "1");
	if ($ticket == false)
		mail ($trouble_email, "{TROUBLE} $fsubject", "[ERROR: CreateTicket failed]\n".$mailstring, "From: Mailing System\nReply-To: $trouble_email");
	else {
		if (!PostMessage($ticket, $mailstring)) {
			mail ($trouble_email, "{TROUBLE} $ticket_subject", "[ERROR: PostMessage failed]\n".$mailstring, "From: Mailing System\nReply-To: $trouble_email");
		}	
	}
	echo $cds->content->get("success");
} else {

	$body = $cds->content->get("Body");

	if (strlen($body)>0) {
		echo $body;
		br();
	}


	if ($sma != 1) echo '<form name="contact" method="post">';
	echo '<table width="100%" cellpadding=5" cellspacing="0" border="0">';

	$label = $cds->content->get("name");
	if ($label != "") {
		echo '<tr>';
		echo   '<td width="30%" class="copy1" valign="middle">'.$label.'</td>';
		if ($sma != 1) echo   '<td width="326"><input type="text" style="width:400px;" name="name" value="'.$fname.'"></td>';
		echo '</tr>';
	}


	$label = $cds->content->get("Christian Name");
	if ($label != "") {
		echo '<tr>';
		echo   '<td width="30%" class="copy1" valign="middle">'.$label.'</td>';
		if ($sma != 1) echo   '<td width="326"><input type="text" style="width:400px;" name="surname" value="'.$fsurname.'"></td>';
		echo '</tr>';
	}

	$label = $cds->content->get("street");
	if ($label != "") {
		echo '<tr>';
		echo   '<td width="30%" class="copy1" valign="middle">'.$label.'</td>';
		if ($sma != 1) echo   '<td width="326"><input type="text" style="width:400px;" name="street" value="'.$fstreet.'"></td>';
		echo '</tr>';
	}

	$label = $cds->content->get("zip");
	if ($label != "") {
		echo '<tr>';
		echo   '<td width="30%" class="copy1" valign="middle">'.$label.'</td>';
		if ($sma != 1) echo   '<td width="326"><input type="text" style="width:40px" maxsize="7" name="zip" value="'.$fzip.'"></td>';
		echo '</tr>';
	}

	$label = $cds->content->get("city");
	if ($label != "") {
		echo '<tr>';
		echo   '<td width="30%" class="copy1" valign="middle">'.$label.'</td>';
		if ($sma != 1) echo   '<td width="326"><input type="text" style="width:400px;" name="city" value="'.$fcity.'"></td>';
		echo '</tr>';
	}

	$label = $cds->content->get("tel");
	if ($label != "") {
		echo '<tr>';
		echo   '<td width="30%" class="copy1" valign="middle">'.$label.'</td>';
		if ($sma != 1) echo   '<td width="326"><input type="text" style="width:400px;" name="tel" value="'.$ftel.'"></td>';
		echo '</tr>';
	}

	$label = $cds->content->get("fax");
	if ($label != "") {
		echo '<tr>';
		echo   '<td width="30%" class="copy1" valign="middle">'.$label.'</td>';
		if ($sma != 1) echo   '<td width="326"><input type="text" style="width:400px;" name="fax" value="'.$ffax.'"></td>';
		echo '</tr>';
	}


	$label = $cds->content->get("E-Mail");
	if ($label != "") {
		if ((value("submit") != "0") && $femail=="")
		$label='<span style="color:red;">'.$label."</span>";
		echo '<tr>';
		echo   '<td width="30%" class="copy1" valign="middle"><b>'.$label.'</b></td>';
		if ($sma != 1) echo   '<td width="326"><input type="text" style="width:400px;" name="email" value="'.$femail.'"></td>';
		echo '</tr>';
	}

	echo '<tr><td colspan="'.(2-$sma).'" height="8">'.$cds->tools->spacer(1,8).'</td></tr>';


	$label = $cds->content->get("subject");
	if ($label != "") {
		echo '<tr>';
		echo   '<td width="30%" class="copy1" valign="middle">'.$label.'</td>';
		if ($sma != 1) echo   '<td width="326"><input type="text" style="width:400px;" name="subject" value="'.$fsubject.'"></td>';
		echo '</tr>';
	}

	$label = $cds->content->get("mailbody");
	if ($label != "") {
		echo '<tr>';
		echo   '<td width="30%" class="copy1" valign="top">'.$label.'</td>';
		if ($sma != 1) echo   '<td width="326"><textarea style="width:400px;; height:100px;" name="message">'.$fmessage.'</textarea></td>';
		echo '</tr>';
	}

	echo '<tr><td colspan="'.(2-$sma).'" height="8">'.$cds->tools->spacer(1,8).'</td></tr>';
	echo '</table>';
	$label = '<b>'.$cds->content->get("Validation").'</b>';
	if ((value("submit") != "0") && ! $$captchaValid)
	  $label='<span style="color:red;">'.$label."</span>";
	echo $captchaAPI->get($label);
	br();
	echo '<table border="0" cellspacing="0" cellpadding="2" width="100%">';
	echo '<tr>';
	echo   '<td width="30%" class="copy1" valign="middle">&nbsp;</td>';
	if ($sma != 1) echo '<td width="400"><input type="reset" name="reset" value="'.$cds->content->get("reset").'">&nbsp;<input type="submit" name="submit" value="'.$cds->content->get("submit").'"></td>';
	echo '</tr>';
	echo '</table>';


	if ($sma != 1) echo '</form>';
}

include $cds->path."inc/footer.php";
require_once "nxfooter.inc.php";
?>