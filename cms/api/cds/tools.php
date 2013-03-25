<?
	/**
	 * @package CDS
	 */

	/**********************************************************************
	 *	N/X - Web Content Management System
	 *	Copyright 2002 Sven Weih, Fabian Koenig
	 *
	 *	This file is part of N/X.
	 *
	 *	N/X is free software; you can redistribute it and/or modify
	 *	it under the terms of the GNU General Public License as published by
	 *	the Free Software Foundation; either version 2 of the License, or
	 *	(at your option) any later version.
	 *
	 *	N/X is distributed in the hope that it will be useful,
	 *	but WITHOUT ANY WARRANTY; without even the implied warranty of
	 *	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	 *	GNU General Public License for more details.
	 *
	 *	You should have received a copy of the GNU General Public License
	 *	along with N/X; if not, write to the Free Software
	 *	Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
	 **********************************************************************/
	
	 /**
	  * Contains tools, whcih make working with N/X-CDS easier.
	  * Access this class with $cds->tools
	  */
	 class Tools extends CDSInterface {

		/**
		 * Standard constructor.
		 * @param object Reference to parent object
		 */
		function Tools(&$parent) { CDSInterface::CDSInterface($parent); }

		/**
		 * Trim a text to a special length. Searches for next Spaces after position.
		 * @param string Text to trim
		 * @param integer Length to trim for
		 * @param string Text to add after cut
		 */
		function shortenText($text, $length, $addAtEnd="") {
		  $result = $text;
		  if (strlen($text)>$length) {
		  	for ($i=$length; $i<strlen($text); $i++) {
		  	  if (substr($text, $i, 1) == " ") {
		  	  	$result = substr($text, 0, $i).$addAtEnd;		  	  	
		  	  	break;
		  	  }
		  	}
		  } 
		  return $result;
		}
		
		
		/**
		 * Get the URL of the last page to traverse back		 
		 */
		function getLastPageURL() {					
			$lastURL = $_SERVER['HTTP_REFERER'];
			return $lastURL;
		}
		
		/**
		 * Get a link back to the page who opened the actual one
		 *
		 * @param string $title
		 * @param string $css
		 * @return string
		 */
		function getBackLink($title, $css="") {
			$class='';
			if ($css != "") $class=' class="'.$css.'" ';
			$result='<a href="'.$this->getLastPageURL().'" '.$class.'>'.$title.'</a>';
			return $result;
		}

		/**
		 * Trim a text to a special length. Searches for last point.
		 * @param string Text to trim
		 * @param integer Length to trim for		 
		 */
		function shortenText2($text, $length ) {
		  $result = $text;
		  if (strlen($text)>$length) {
		  	for ($i=$length; $i>0; $i--) {
		  	  if (substr($text, $i, 1) == ".") {
		  	  	$result = substr($text, 0, $i+1);
		  	  	break;
		  	  }
		  	}
		  } 
		  return $result;
		}
		
		/**
		 * enclose text in a <nobr>...</nobr> tag to prevent line-breaks.
		 */
		function noBR($text) { return "<nobr>".$text."</nobr>"; }
		
		/**
		 * returns html for a spacer.
		 * @param integer x-space, you want to insert
		 * @param integer y-space, you want to insert
		 * @param string optional. If "ALL" an array is returned with values of width, height and path
		 */
		function spacer($width, $height = 1, $params = null) {
			global $c;

			if (stristr($params, "ALL")) {
				$output["widht"] = $width;

				$output["height"] = $height;
				$output["path"] = $c["livefilesdocroot"] . "ptrans.gif";
			} else {
				$output = "<img src=\"" . $c["livefilesdocroot"] . "ptrans.gif\" width=\"$width\" height=\"$height\" border=\"0\">";
			}

			return $output;
		}

		/**
		 * draws html for a spacer.
		 * @param integer x-space, you want to insert
		 * @param integer y-space, you want to insert
		 * @param string optional. If "ALL" an array is returned with values of width, height and path
		 */
		function drawSpacer($width, $height = 1, $params = null) { echo $this->spacer($width, $height, $params); }

		/**
	 	* Replace Whitespaces and - with &nbsp for
	 	* @param string Text to do the replace in.
	 	*/
		function clrSPC($title) { return str_replace(" ", "&nbsp;", str_replace("-", "&nbsp;", $title)); }

		
		/**
		 * Starts the Benchmark for measuring the script-execuction time.
		 */
		function startBenchmark() {
			global $benchmarkStartTime;

			$microtime = explode(" ", microtime());
			$benchmarkStartTime = $microtime[1] + $microtime[0];
		}

		/**
		 * returns the seconds the script executed.
		 * @param integer Precision of measurement
		 * @returns number time elapsed for execution
		 */
		function stopBenchmark($precision = 5) {
			global $benchmarkStartTime;

			$microtime = explode(" ", microtime());
			$stopTime = $microtime[1] + $microtime[0];
			return number_format(($stopTime - $benchmarkStartTime), $precision);
		}

		/**
		 * prints out the seconds the script has been running.
		 * @param integer Precision of measurement
		 * @returns number time elapsed for execution
		 */
		function printBenchmark($precision = 5) {
			$res = $this->stopBenchmark($precision);

			echo "Time elapsed: " . $res . " seconds";
		}
		
		/** 
		 * Encode an URL so that it lands on the exit page - for seo
		 * @param string URL to encode
		 * @returns string encoded URL
		 */
		 function encodeURL($url) {
		   global $cds;
		   $url = base64_encode($url);	
		   return $cds->docroot.'sys/go.php?u='.$url;
		 }
	}
?>