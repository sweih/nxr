<?PHP
  require_once "nxheader.inc.php";
  include $cds->path."inc/header.php";
  include_once $c["basepath"].'cms/plugin/captcha/pgn_captcha.php';
  $captcha = new NXCaptcha();
  
  // Start of individual template
  echo '<h1>Register a new account</h1>';
  
  if (value("submit","","") != "") {
  	$email = value("email", "", "");
  	$pass1 = value("password", "", "");
  	$pass2 = value("password2", "", "");
  	$accept = value("accept", "", "");
  	$code = $captcha->validate();
  	
  	
  }
  
  
  
  
  echo '<form method="post" action="'.$action.'">';
  echo '<input type="hidden" name="page" value="'.$cds->pageId.'">';
  echo '<input type="hidden" name="v" value="'.$cds->variation.'">';  
  echo '<table width="400" border="0" cellpadding="0" cellspacing="0">';
  echo '<tr>';
    echo '<td width="180" align="left">E-Mail-Address:</td>';
    echo '<td width="220" ><input type="text" name="email" style="width:200px;" value="'.$mail.'"></td>';
  echo '</tr>';
  echo '<tr>';
    echo '<td width="180" align="left">Password:</td>';
    echo '<td width="220" ><input type="password" name="password" style="width:200px;" value=""></td>';
  echo '</tr>'; 
  echo '<tr>';
    echo '<td width="180" align="left">Password confirmation:</td>';
    echo '<td width="220"><input type="password" name="password2" style="width:200px;" value=""></td>';
  echo '</tr>'; 
  echo '<tr>';
    
    
    echo '<td colspan="2">';
    br();
    echo $captcha->get("Security Code", 400,175,225);    
    echo '</td>';
  echo '</tr>'; 
  
  echo '<tr>';    
  echo '<td>&nbsp;</td>';  
  echo '<td><br><input type="checkbox" name="accept" value="1">&nbsp;&nbsp;Accept EULA</td>';
  echo '</tr>'; 
  
  echo '<tr>';
  echo '<td>&nbsp;</td>';    
    echo '<td ><br><input type="submit" name="submit" value="Register" style="width:200px;"><br><br><a href="'.$cds->docroot.'login.php?page='.$cds->pageId.'&v='.$cds->variation.'">Go to login page.</a></td>';
  echo '</tr>';  
  echo '</table>';
  echo '</form>';
  
 
  include $cds->path."inc/footer.php";
  require_once "nxfooter.inc.php";
?>