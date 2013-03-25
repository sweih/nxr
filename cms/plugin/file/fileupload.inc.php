<?
	/**********************************************************************
	 *	N/X - Web Content Management System
	 *	Copyright 2002 Sven Weih, FZI Research Center for Information Technologies
	 *	www.fzi.de
	 *
	 *	This file is part of N/X.
	 *	The initial has been setup as a small diploma thesis (Studienarbeit) at the FZI.
	 *	It was be coached by Prof. Werner Zorn and Dipl.-Inform Thomas Gauweiler.
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
* @package DatabaseConnectedInput
*/

	/**
	 * draws a file-upload-field escecially for uploading images to the system and provides 
	 * all mechanisms for upload the image.
	 * @package DatabaseConnectedInput
	 */
	class FileUpload {
		var $v_label;

		var $v_wuiobject;
		var $fileId;
		var $label;
		var $suffix = "";
		var $name;
		var $fkid;
		var $width;
		var $height;
		var $filename;

		/**
		  * standard constructor
		  * @param string Text that is to be shown as description or label with your object.
		  * @param integer ID, that corresponds to the image file. the ending of the uploaded
		  * file will be detected and addes automatically. No manipulation to the database is 
		  * done.
		  */
		function FileUpload($label, $file_id) {
			global $specialID;

			$this->fileId = $file_id;
			$this->label = $label;
			$this->fkid = $file_id;
			$this->name = "file" . $specialID;
			$this->v_label = new Label("lbl", $label, "standard");
			$this->v_label->text = "<span class=\"standardlight\">$label</span>"; // set text light.
			$this->v_wuiobject = new Filebox($this->name, "standard", "*");
		}

		/**
		 * checks, whether the uploaded file is an image or not.
		 */
		function check() {
			global $HTTP_POST_FILES;

			if (isset($HTTP_POST_FILES[$this->name])) {
				$file_name = $HTTP_POST_FILES[$this->name]['name'];

				if ($file_name != "") {
					$fileparts = explode(".", $file_name);

					$suffix = strtolower($fileparts[(count($fileparts) - 1)]);
					$this->suffix = $suffix;
				}
			}
		}
		
		/**
		 * determine the size of the image. write size to the database.
		 * copy file to local folder
		 */
		function process() {
			global $c, $HTTP_POST_FILES, $HTTP_POST_VARS;

			// check, if upload.
			$delete = $HTTP_POST_VARS["REMOVE"];

			if ($delete) {
				addUpdate("pgn_file", "FILENAME", "", "FKID = " . $this->fileId, "TEXT");
				addUpdate("pgn_file", "FILETYPE", "", "FKID = " . $this->fileId, "TEXT");
			} else {
				if ($this->suffix != "") {
					$file = $HTTP_POST_FILES[$this->name]['tmp_name'];

					$file_name = $HTTP_POST_FILES[$this->name]['name'];

					$oldfilename = getDBCell("pgn_file", "FILENAME", "FKID = " . $this->fileId);
					$filename = $this->fileId . "." . $this->suffix;
					// updating the database
					addUpdate("pgn_file", "FILENAME", $filename, "FKID = " . $this->fileId, "TEXT");
					addUpdate("pgn_file", "FILETYPE", $this->suffix, "FKID = " . $this->fileId, "TEXT");

					// loading up new item.
					if ($oldfile != "")
						unlink ($c["devfilespath"] . $oldfilename);

					copy($file, $c["devfilespath"] . $filename);

					// setting variables for further filtering
					$this->filename = $filename;
				}
			}
		}
		
		/** For Down-Grade-Compatibility only **/
		function proccess() { $this->process(); }

		/**
		 * Add an errormessage to the global error-String.
		 * Changes also the colors of the DBO so that users can find the wrong input easier.
		 * @param string errortext, to be added to the global error-string. for debugging only.
		 * @param string text, to be displayed in the DBO for telling the user, what he did wrong.
		 */
		function addError($error, $text) {
			global $errors;

			$errors .= "-" . $error;
			$this->v_label = new Label("lbl_" . $this->column, $this->label . "<br><span class=\"standardlight\">" . $text . "</span>", "error");
			$this->v_wuiobject->style = "error";
			$this->std_style = "error";
		}

		/**
		 * Draw the upload-box
		 */
		function draw() {
			$this->v_label->draw();

			$this->v_wuiobject->draw();
			return 2;
		}
	}
?>