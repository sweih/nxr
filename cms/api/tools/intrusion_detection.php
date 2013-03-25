<?php
	/** 
	********************************************************************** 
	* ASTALAVISTA Webapp Intrusion Detection/Response System 
	* 
	* @copyright ASTALAVISTA SECURE CMS - 2003 Astalavista Group GmbH 
	* @author    Ivan Schmid <ivan.schmid@astalavista.ch>               
	* @version   1.2 26.03.03                                             
	* @module    core 
	********************************************************************** /
	
	
	********************************************************************** 
	* Astalavista Web Intrusion Detection System (common filter) 
	* 
	* @param    array  untrusted array gpcs (GET/POST/SERVER/COOKIES/usw)   
	* @return   string attack type 
	* @return   string file 
	* @return   string line 
	* @version  1.2 26.03.03 
	* @module   core 
	********************************************************************** 
	*/
	function securityIDS($array = array ()) {
		global $excludeFromIDS;

		if (count($array)) {
			foreach ($array as $key => $untrustedValue)
				// faster than while (list($key, $untrustedValue) = each($array)) 
				{
				$exclude = false;

				for ($i = 0; $i < count($excludeFromIDS); $i++) {
					if (stristr($key, $excludeFromIDS[$i]))
						$exclude = true;
				//echo "$key = $untrustedValue <br>";
				}

				if (!$exclude) {
					if (is_array($untrustedValue)) {
						$trustedValue[$key] = securityIDS($array[$key]);
					} else {
						$attackType = "Unknown Web Attack";

						$attack = 0;

						if ((eregi(
							"<[^>]*script*\"?[^>]*>", $untrustedValue))
								|| (eregi("<[^>]*xml*\"?[^>]*>", $untrustedValue))
											  || (eregi("<[^>]*style*\"?[^>]*>", $untrustedValue))
															|| (eregi("<[^>]*form*\"?[^>]*>", $untrustedValue))
																		  || (eregi("<[^>]*window.*\"?[^>]*>", $untrustedValue))
																						|| (eregi("<[^>]*alert*\"?[^>]*>", $untrustedValue))
																									  || (eregi("<[^>]*img*\"?[^>]*>", $untrustedValue))
																													|| (eregi("<[^>]*document.*\"?[^>]*>", $untrustedValue))
																																  || (eregi("<[^>]*cookie*\"?[^>]*>", $untrustedValue))
																																				|| (eregi(".*[[:space:]](or|and)[[:space:]].*(=|like).*", $untrustedValue))
																																							  || (eregi("<[^>]*object*\"?[^>]*>", $untrustedValue))
																																											|| (eregi("<[^>]*iframe*\"?[^>]*>", $untrustedValue))
																																														  || (eregi("<[^>]*applet*\"?[^>]*>", $untrustedValue)) || (eregi("<[^>]*meta*\"?[^>]*>", $untrustedValue))) {
							$attack = 1;

							$attackType = "Potential Cross-Site-Scripting Attack";
						}

						if ((preg_match("/;/", $untrustedValue)) || (preg_match("/'/", $untrustedValue))) {
							$attack = 1;

							$attackType = "Potential SQL Injection Attack";
						}

						if ($attack == 1) {
							if (!get_magic_quotes_runtime())
								$trustedValue[$key] = htmlspecialchars($untrustedValue, ENT_QUOTES);
						} else {
							$trustedValue[$key] = $untrustedValue;
						}
					}
				} else {
					$trustedValue[$key] = $untrustedValue;
				}
			}
		}

		return $trustedValue;
	}
?>