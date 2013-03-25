<?
	require_once "../../../config.inc.php"
?>
	<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
	<html>
		<head>
			<title>BLANK PAGE</title>
			<META HTTP-EQUIV = "Pragma" content = "no-cache">
			<META HTTP-EQUIV = "Cache-Control" content = "no-cache, must-revalidate">
			<META HTTP-EQUIV = "Expires" content = "0">
			<link rel = stylesheet type = "text/css" href = "<? echo $c["docroot"]; ?>common/css/styles.css">
		</head>
		<body leftmargin = "0" topmargin = "0" marginheight = "0" marginwidth = "0">
		</body>
	</html>

	<?
		$db->close();
	?>