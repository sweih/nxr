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
	 * create the : june 10th 2003.
	 * @author      Matthieu MARY
	 * @version     %I%, %G%
	 * @since       1.0
	 */

	/**
	* Can be download at
	* http://www.phpclasses.org/browse.html/package/1220.html
	*/
	require_once "class_specif.php";

	class dir {
		/**
		 * array of errors
		 *
		 * @private
		 * @type array
		 **/
		var $aErrors;
		/**
		 * the directory
		 *
		 * @private
		 * @type string
		 **/
		var $dir;
		/**
		 * path delimiter
		 *
		 * @private
		 * @type char
		 **/
		var $cSep;

		/**
		 * builder
		 *
		 * @param string sDirectory, required. the directory use
		 * @public
		 * @type void
		 **/
		function dir($sDirectory) {
			$this->aErrors = array ();

			$this->dir = realpath($sDirectory);

			if (!is_dir($this->dir))
				$this->aErrors[] = "PARAM [" . $sDirectory . "] is not a valid directory";

			$pattern = '^(' . addslashes(realpath($_SERVER['DOCUMENT_ROOT'])). ')';
			$specif = new specif(preg_match("/^($pattern)/", $this->dir));
			$this->cSep = $specif->Path_delimiter();
			unset ($specif);
		} // builder

		/**
		 * Function that return the directory size
		 *
		 * @param bool bSubfolders, optional, default value TRUE. do you wants the size of the subfolders too?
		 * @public
		 * @type int
		 **/
		function SIZE($bSubfolders = TRUE) {
			// get the folder size
			$sum = $this->_SIZE_get($this->dir, $bSubfolders);

			if ($sum == -1) {
				$this->aErrors[] = "folder [" . $this->dir . "] is empty";

				return;
			}

			$size = $this->_SIZE_format($sum);
			return $size;
		} // size

		/**
		* Function that list all the directories in the current directory
		*
		* @public
		* @type array
		**/
		function LIST_directories() {
			$aDir = array ();

			$dir = openDir($this->dir);

			while ($oObj = readDir($dir)) {
				$sComplete_path = $this->dir . $this->cSep . $oObj;

				if (is_dir($sComplete_path) && ($oObj != '.') && ($oObj != '..'))
					$aDir[] = $sComplete_path;
			} //while

			closeDir ($dir);
			return $aDir;
		}     // LIST

		/**
		* Function that list all the files in the current directory
		*
		* @param bool bSubfolders, optional, default value TRUE; do you wants the list of the subfolders too?
		* @param array aExtensions, optional, default value array(); array of the extension files you wants list
		* @public
		* @type array
		**/
		function LIST_get($bSubfolders = TRUE, $aExtensions = array ()) { return $this->_LIST_files($this->dir, $aExtensions, $bSubfolders); } // LIST

		/**
		* Function that list all the files in the current directory
		*
		* @param string sPath, required. the path to list
		* @param bool bSubfolders, optional, default value TRUE; do you wants the list of the subfolders too?
		* @param array aExtensions, optional, default value array(); array of the extension files you wants list
		* @private
		* @type array
		**/
		function _LIST_files($sPath, $aExtensions = array (), $bSubfolders = TRUE) {
			$aFiles = array ();

			$dir = openDir($sPath);

			while ($oObj = readDir($dir)) {
				$sComplete_path = $sPath . $this->cSep . $oObj;

				if (is_file($sComplete_path)) {
					if (count($aExtensions) > 0) {
						$dPathinfos = pathinfo($sComplete_path);

						if (in_array(strtolower($dPathinfos['extension']), $aExtensions))
							$aFiles[] = $sComplete_path;
					} else
						$aFiles[] = $sComplete_path;
				}

				if ($bSubfolders && is_dir($sComplete_path) && ($oObj != '.') && ($oObj != '..')) {
					$aFiles = array_merge($aFiles, $this->_LIST_files($sComplete_path, $aExtensions, TRUE));
				}
			} //while

			closeDir ($dir);
			return $aFiles;
		}     // LIST_files

		/**
		* Function that will doest the sum of files in the directory
		*
		* @param string sFolder, required. the path to check
		* @param bool bSubfolders, optional, default value TRUE; do you wants the list of the subfolders too?
		* @private
		* @type int
		**/
		function _SIZE_get($sFolder, $bSubfolders) {
			$sum = -1;

			$dir = openDir(realpath($sFolder));

			while ($oObj = readDir($dir)) {
				if (is_file(realpath($sFolder). $this->cSep . $oObj)) {
					$sum += filesize(realpath($sFolder). $this->cSep . $oObj);
				}

				if ($bSubfolders && is_dir(realpath($sFolder). $this->cSep . $oObj) && ($oObj != '.') && ($oObj != '..')) {
					$sum += $this->_SIZE_get(realpath($sFolder). $this->cSep . $oObj, TRUE);
				}
			} //while

			closeDir ($dir);
			return $sum;
		}     //_get_size

		/**
		* Function that convert to Mo the size of a folder
		*
		* @param int size, required. the size to convert
		* @private
		* @type int
		**/
		function _SIZE_format($size) {
			// format a specified number into Mo
			// 1 ko = 1024 bits
			if ($size <= 0) {
				$this->aErrors[] = "specfied size [" . $size . "] is invalid";

				return;
			}

			return ($size / (1024 * 1000));
		} // size

		/**
		* Function that returns errors
		*
		* @public
		* @type array
		**/
		function DATA_errors() { return $this->aErrors; }

		/**
		* Function that returns number of errors
		*
		* @public
		* @type array
		**/
		function DATA_errors_size() { return count($this->aErrors); }
	} // class
?>