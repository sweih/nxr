<?php
	/****************************************************************
	*****************************************************************
	
	class_log.php: create to work with log file (create and search).
	Copyright (C) 2003  Matthieu MARY marym@ifrance.com
	
	This program is free software; you can redistribute it and/or
	modify it under the terms of the GNU General Public License
	as published by the Free Software Foundation; either version 2
	of the License, or any later version.
	
	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.
	
	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
	
	You can found more information about GPL licence at:
	http://www.gnu.org/licenses/gpl.html
	
	for contact me: marym@ifrance.com
	****************************************************************
	****************************************************************/
	/**
	 * create the : july 6th 2003.
	 * @author      Matthieu MARY &lt;<a href="mailto:marym@ifrance.com">marym@ifrance.com</a>&gt;
	* @version     1.0.2
	 */
	class specif {
		/**
		 * path delimiter
		 * @private
		 * @type string
		 */
		var $cSep;
		/**
		 * endline char
		 * @private
		 * @type string
		 */
		var $sEndline;
		/**
		 * is it a Linux OS?
		 * @private
		 * @type bool
		 */
		var $bLinuxos;
		/**
		 * is the script is call from a browser agent?
		 * @private
		 * @type bool
		 */
		var $bUseragent;
		/**
		 * builder
		 *
		 * @public
		 * @param bool brelativepath optional, default value TRUE. do you wants to see relative path? (only need for windows users)
		 * @type void
		 */
		function specif($brelativepath = TRUE) {
			// linux os?
			$this->bLinuxos = ((preg_match("/WIN/", strtoupper(PHP_OS))) ? FALSE : TRUE);

			// call from command line?
			// this part of script was written with the help of Thiemo Mättig
			// thanks to him
			$amode = php_sapi_name();

			if ($amode == 'cli')
				$this->bUseragent = FALSE;
			else {
				if (!preg_match("/^cgi/", $amode))
					$this->bUseragent = TRUE;
				else
					$this->bUseragent = ((isset($_SERVER['HTTP_REFERER'])) ? TRUE : FALSE);
			}

			if ($this->bLinuxos)
				$this->cSep = '/';
			else {
				if ($this->bUseragent)
					$this->cSep = ((!$brelativepath) ? "\\" : "/"); // windows os with a call from server?
				else
					$this->cSep = "\\";                             //windows os with call with php.exe
			}

			if ($this->bUseragent)
				$this->sEndline = "<BR>";

			$this->sEndline .= (($this->bLinuxos) ? "\n" : "\r\n");
		}                                                           //specif

		/**
		 * return the path delimiter
		 *
		 * @public
		 * @type string
		 */
		function Path_delimiter() { return $this->cSep; } //Path_delimiter

		/**
		 * return the endline delimiter
		 *
		 * @public
		 * @type string
		 */
		function Endline() { return $this->sEndline; } //Endline
	}                                                  // class specif
?>