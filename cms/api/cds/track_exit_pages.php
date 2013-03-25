<?php
	if ($c["trackexitpages"]) {
		function encode_exit_urls($buffer) {  
			return preg_replace(    "#<a href=(\"|')http://([^\"']+)(\"|')#ime",    '"<a href=\"' .$c["hostlivedocroot"]. 'sys/exit.php?url=".base64_encode(\'\\2\')."\""',    $buffer  );
		}
		ob_start('encode_exit_urls');
	}
?>