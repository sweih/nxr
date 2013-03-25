<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">

<html>
<head>
    <title>N/X Help</title>
</head>
<body>
<link rel="stylesheet" href="help.css" media="screen">
<table width="600" border="0" cellpadding="0" cellspacing="1">
<tr>
 <td width="30%" class="sidebar" valign="top">

<? 

    include("menu.html");

    echo '&nbsp;<a href="help.php?topic=license_and_copyright"> License and copyright</a>';
    echo '</td><td width="70%" class="textarea" valign="top">';

    $topic = $HTTP_GET_VARS['topic'];

    if ($topic != "") {
        $htmlfile = $topic.".html";
    }
    
    // insert the default topic here
    else $htmlfile = "1_introduction_to_nx.html";

    include "$htmlfile";

    echo '</td></tr>';

?>

</body>
</html>
