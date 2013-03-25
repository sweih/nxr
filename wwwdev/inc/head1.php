<table border="0" cellpadding="0" cellspacing="0">
<tr>
  <td valign="middle">
<?php
  echo '<a href="'.$cds->docroot.'">'.$cds->content->getByAccessKey('logo', array('class' => 'logo')).'</a>';
?>
</td><td valign="top" width="10">&nbsp;</td>
<td valign="middle">
<h3><span><?php echo $cds->content->getByAccessKey('title'); ?></span></h3>
<h4><?php echo $cds->content->getByAccessKey('subtitle'); ?></h4>
</td></tr></table>