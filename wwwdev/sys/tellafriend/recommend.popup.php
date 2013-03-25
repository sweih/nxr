<?php
  define('ACCESS_CONTENT_ONLY', true);
  require_once  "../../../cms/config.inc.php";
  require_once  "../../nxheader.inc.php";
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
	<title>Site recommender</title>
	<link rel="stylesheet" href="recommend.css" type="text/css">
</head>

<body>

<table width="100%" height="100%">
	<tr>
		<td align="center" valign="middle">
			
			
			<?php

//******************************************************************* 
//  File:		inc.recommend.php © Big Lick Media - BigLickMedia.com
//  Author:		D Stewart
//  Date:		04-16-2006
//  Version:	2.4
//*******************************************************************


/* Config Section */


$sitename = $cds->content->getByAccessKey("title");				// Name of your site.
$url = $c["host"];			// Web address for your site.
$webmasterEmail = $c["webmastermail"];		// Your email address.
$receiveNotifications = 1;					// 0=no, 1=yes. If yes, you will be notified of the recipients and the message.
$errorstyleclass = 'error';					// The class that specifies the CSS error color.
$numberofrecipients = 3;					// Number of recipient email address fields to be displayed.
$emailsubject = $cds->content->getByAccessKey("taf_mailsubject");		//Email subject line.
$emailmessage = $cds->content->getByAccessKey("taf_mailmessage");		// Message in email body. 

/* End Config */


$mailsent = false; 
$errormessages = array(); 
$errorfields = array(); 

if(count($_POST) > 0) { 
    if(get_magic_quotes_gpc()) $_POST = strip_magic_quotes($_POST); 

    if(empty($_POST['name'])) { 
        $errormessages[] = $cds->content->getByAccessKey("taf_errname");
        $errorfields[] = 'name'; 
    } 
   
    if(empty($_POST['email'])) { 
        $errormessages[] = $cds->content->getByAccessKey("taf_entermail"); 
        $errorfields[] = 'email'; 
    } else { 
        if(!eregi("^[a-z0-9\._-]+@+[a-z0-9\._-]+\.+[a-z]{2,3}$", $_POST['email'])) { 
            $errormessages[] =  $cds->content->getByAccessKey("taf_entermail2"); 
            $errorfields[] = 'email'; 
        } 
    } 
   
    for($i=1, $count=count($_POST['to']); $i<=$count; $i++) { 
        if(empty($_POST['to'][$i])) unset($_POST['to'][$i]); 
    } 
   
    if(empty($_POST['to'])) { 
        $errormessages[] = $cds->content->getByAccessKey("taf_errorrec1");
        $errorfields[] = 'to[1]'; 
    } else { 
        foreach($_POST['to'] as $key=>$value) { 
            if(!empty($value)) { 
                if(!eregi("^[a-z0-9\._-]+@+[a-z0-9\._-]+\.+[a-z]{2,3}$", $value)) { 
                    $errormessages[] = $cds->content->getByAccessKey("taf_errorrec2").$key;
                    $errorfields[] = "to[$key]"; 
                } 
            } 
        } 
    } 

    // Now if there are no errors, send the message. 
    if(empty($errormessages)) { 
        $emailsubject = str_replace('[name]', $_POST['name'], $emailsubject); 
        $emailsubject = str_replace('[email]', $_POST['email'], $emailsubject); 
        $emailmessage = str_replace('[name]', $_POST['name'], $emailmessage);         
        $emailmessage .= "\r\n\n" . 
                          $_POST['message'] . 
                          "\n\n\n\n".$cds->content->getByAccessKey("taf_msgfooter");
        $emailmessage = str_replace('[url]', $url, $emailmessage); 
        
        $emailheader = "From: " . $_POST['email'] . "\r\n" . 
                       "Reply-To: " . $_POST['email'] . "\r\n" . 
                       "X-Mailer: Big Lick Media - php Site Recommender\r\n"; 
         
        $sent = array(); 
        foreach($_POST['to'] as $key=>$value) { 
            if(@mail($value, $emailsubject, $emailmessage, $emailheader)) { 
                $sent[] = $value; 
            } 
        } 
        $failed = array_diff($_POST['to'], $sent); 
        $mailsent = true; 
         
        if($receiveNotifications) { 
            $subject = 'Someone recommended your site'; 
            $message = 'This is a message to tell you that ' . $_POST['name'] . ' (' . $_POST['email'] .')' . 
                       ' sent a website recommendation to ' . implode(', ', $sent) . 
                       "\n\nMessage: " .  $_POST['message']; 
            $headers = 'From: ' . $webmasterEmail . "\r\n" . 
                       'X-Mailer: Big Lick Media - php Site Recommender'; 
            @mail($webmasterEmail, $subject, $message, $headers); 
        } 
    } 
} 

?> 

<?php 
if($mailsent) { 
	echo empty($sent) ? '' : '<p>'.$cds->content->getByAccessKey("taf_success").' ' . implode(', ', $sent) . '</p>'; 
	echo empty($failed) ? '' : '<p>'.$cds->content->getByAccessKey("taf_failed").' '. implode(', ', $failed);
} else { 
    if(count($_POST) > 0 && !empty($errormessages)) { 
        echo '<table><tr><td><span class="' , $errorstyleclass , '">'; 
        echo $cds->content->getByAccessKey('taf_errors').'<br />'; 
        foreach($errormessages as $value) { 
            echo ' &nbsp; &nbsp; &raquo; ' ,$value , '<br />'; 
        } 
        echo '</span><br /></td></tr></table>'; 
    } 
?> 

<table>
<form method="post" action="<?php echo $_SERVER['PHP_SELF'];?>">
	<tr>
		<td class="formtexttitle" colspan="2"><?php echo $cds->content->getByAccessKey("taf_title"); ?></td>
	</tr>
    <tr>
        <td class="formtext"><?php echo $cds->content->getByAccessKey("taf_yourname"); ?></td>
        <td><input type="text" name="name" value="<?php echo isset($_POST['name']) ? $_POST['name'] : '';?>" class="<?php echo in_array('name', $errorfields) ? $errorstyleclass : ''; ?>" onfocus="this.style.borderColor='#0072BC';" onblur="this.style.borderColor='silver';">
        </td>
    </tr>
    <tr>
        <td class="formtext"><?php echo $cds->content->getByAccessKey("taf_your_email"); ?></td>
        <td><input type="text" name="email" value="<?php echo isset($_POST['email']) ? $_POST['email'] : '';?>" class="<?php echo in_array('email', $errorfields) ? $errorstyleclass : ''; ?>" onfocus="this.style.borderColor='#0072BC';" onblur="this.style.borderColor='silver';">
        </td>
    </tr>
    <tr>
        <td colspan="2"><?php echo $cds->content->getByAccessKey("taf_recipients"); ?><br /><br /></td>
    </tr>
	<tr>
        <td class="formtext">1.</td>
        <td><input type="text" name="to[1]" value="" class="" onfocus="this.style.borderColor='#0072BC';" onblur="this.style.borderColor='silver';"></td>
    </tr>
    <?php 
    for($i=2; $i<=$numberofrecipients; $i++) { 
        $value = isset($_POST['to'][$i]) ? $_POST['to'][$i] : ''; 
        $class = in_array("to[$i]", $errorfields) ? $errorstyleclass : ''; 
        echo "    <tr>\n"; 
        echo '        <td class="formtext">' , $i , ".</td>\n"; 
        echo '        <td><input type="text" name="to[', $i ,']" value="', $value ,'" class="', $class ,"\" onfocus=\"this.style.borderColor='#0072BC';\" onblur=\"this.style.borderColor='silver';\"></td>\n"; 
        echo "    </tr>\n"; 
    } 
    ?> 
    <tr>
        <td colspan="2"><?php echo $cds->content->getByAccessKey("taf_message"); ?><br />
		<textarea name="message" rows="5" cols="31" class="<?php echo in_array('messsage', $errorfields) ? $errorstyleclass : ''; ?>" onfocus="this.style.borderColor='#0072BC';" onblur="this.style.borderColor='silver';"><?php echo isset($_POST['message']) ? $_POST['message'] : '';?></textarea>
        </td>
    </tr>
    <tr>
        <td colspan="2">
            <table>
                <tr>
                    <td><input class="send" type="submit" value="<?php echo $cds->content->getByAccessKey("taf_send"); ?>" /></td>
                    <td class="formtext">&nbsp;</td>
                    <td><input class="reset" type="reset" value="<?php echo $cds->content->getByAccessKey("taf_reset"); ?>" /></td>
                </tr>
            </table>
        </td>
    </tr>
</form>
</table>
<?php 
} 
?>

<?php 
function strip_magic_quotes($arr) { 
    foreach($arr as $k => $v) { 
        if(is_array($v)) { 
            $arr[$k] = strip_magic_quotes($v); 
        } else { 
            $arr[$k] = stripslashes($v); 
        } 
    } 
    return $arr; 
} 

?>
			
			
			
			<br><a href="javascript:window.close()"><?php echo $cds->content->getByAccessKey("taf_close"); ?></a>
		</td>
	</tr>
</table>

</body>
</html>
<?php
  require_once "../../nxfooter.inc.php";
?>