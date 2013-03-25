<?PHP
  require_once "nxheader.inc.php";
  include $cds->path."inc/header.php";

  // Start of individual template
  echo '<h1>User Login</h1>';
  echo '<center>';
  $menu = new Menu(null, $cds->pageId, $cds->variation, $cds->level);
  $action = $menu->getLink();  
  $le = value("le", "NOSPACES", "");
  $mail = value("mail", "", "");
  if ($le == 'unknown') {
  	echo '<span style="color:#cc0000;">Error: The username is unknown.</span>';  	
  } else if ($le=="pwd") {
  	echo '<span style="color:#cc0000;">Error: Incorrect password.</span>';  	
  } else if ($le=="confirm") {
  	echo '<span style="color:#cc0000;">Error: You did not confirm your account yet.</span><br>Request E-Mail-Confirmation';
  }
  
  echo '<form method="post" action="'.$action.'">';
  echo '<input type="hidden" name="page" value="'.$cds->pageId.'">';
  echo '<input type="hidden" name="v" value="'.$cds->variation.'">';  
  echo '<table width="400" border="0" cellpadding="0" cellspacing="0">';
  echo '<tr>';
    echo '<td width="180" align="left">E-Mail-Address:</td>';
    echo '<td width="220" align="right"><input type="text" name="email" style="width:200px;" value="'.$mail.'"></td>';
  echo '</tr>';
  echo '<tr>';
    echo '<td width="180" align="left">Password:</td>';
    echo '<td width="220" align="right"><input type="password" name="password" style="width:200px;" value=""></td>';
  echo '</tr>'; 
  echo '<tr>';
    echo '<td width="180"></td>';
    echo '<td width="220" align="right"><input type="submit" name="submit" value="Log In"></td>';
  echo '</tr>';  
  echo '</table>';
  echo '</form>';
  br();
  br();
  br();
  
  echo '<a href="'.$cds->docroot.'/auth_register.php?page='.$cds->page.'&v='.$cds->variation.'">Register new account</a>&nbsp;&nbsp;&nbsp;-&nbsp;&nbsp;&nbsp;Forgot your password?';
  echo '</center>';
  
 
  include $cds->path."inc/footer.php";
  require_once "nxfooter.inc.php";
?>